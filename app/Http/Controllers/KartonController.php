<?php

namespace App\Http\Controllers;

use App\Models\Karton;
use App\Models\Operator;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\DB;
use TCPDF;

class KartonController extends Controller
{

    private const LAPORAN_KARTON = 'Kontrol Labelisasi Cartooning';

    public function index(Request $request)
    {

        if (Auth::user()->isAuditView()) {
            return redirect()->route('karton.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');
        $nama_produk = $request->input('nama_produk');
        $userPlant = Auth::user()->plant;

        $data = Karton::with('mincing')
            ->where('plant', $userPlant)
            ->where('is_audit', false)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_karton', 'like', "%{$search}%")
                        ->orWhere('nama_supplier', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', $search)
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhereExists(function ($sub) use ($search) {
                            $sub->selectRaw(1)
                                ->from('mincings')
                                ->whereColumn('mincings.uuid', 'kartons.kode_produksi')
                                ->where('mincings.kode_produksi', 'like', "%{$search}%");
                        });
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($nama_produk, function ($query) use ($nama_produk) {
                $query->where('nama_produk', $nama_produk);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.karton.index', compact('data', 'search', 'date', 'nama_produk'));
    }

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('karton.index');
        }

        $userPlant   = Auth::user()->plant;
        $search      = $request->input('search');
        $date        = $request->input('date');
        $nama_produk = $request->input('nama_produk');

        $auditedSourceUuids = Karton::where('plant', $userPlant)
            ->where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid');

        $data = Karton::with(['mincing', 'auditVersion'])
            ->where('plant', $userPlant)
            ->where(function ($query) use ($auditedSourceUuids) {
                $query->where('is_audit', true)
                    ->orWhere(function ($q) use ($auditedSourceUuids) {
                        $q->where('is_audit', false)
                            ->whereNotIn('uuid', $auditedSourceUuids);
                    });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_karton', 'like', "%{$search}%")
                        ->orWhere('nama_supplier', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhereExists(function ($sub) use ($search) {
                            $sub->selectRaw(1)
                                ->from('mincings')
                                ->whereColumn('mincings.uuid', 'kartons.kode_produksi')
                                ->where('mincings.kode_produksi', 'like', "%{$search}%");
                        });
                });
            })
            ->when($date, fn ($query) => $query->whereDate('date', $date))
            ->when($nama_produk, fn ($query) => $query->where('nama_produk', $nama_produk))
            ->orderByRaw('COALESCE((SELECT original.date FROM kartons AS original WHERE original.uuid = kartons.source_uuid LIMIT 1), kartons.date) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM kartons AS original WHERE original.uuid = kartons.source_uuid LIMIT 1), kartons.created_at) DESC')
            ->paginate(10)
            ->appends($request->all());

        $bulkMaster = List_form::where('plant', $userPlant)
            ->where('laporan', self::LAPORAN_KARTON)
            ->first();

        $masters = List_form::where('plant', $userPlant)->get();

        return view('form.karton.index_audit', compact(
            'data',
            'search',
            'date',
            'nama_produk',
            'masters',
            'bulkMaster'
        ));
    }

    public function duplicateToAudit($uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $karton = Karton::where('uuid', $uuid)
            ->where('plant', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $karton->auditVersion) {
            return redirect()
                ->route('karton.edit.form', $existing->uuid)
                ->with('info', 'Salinan data audit sudah tersedia.');
        }

        $clone = $this->createCorrectedAudit($karton);

        return redirect()
            ->route('karton.edit.form', $clone->uuid)
            ->with('success', 'Salinan data audit berhasil dibuat.');
    }

    private function createCorrectedAudit(Karton $karton): Karton
    {
        return DB::transaction(function () use ($karton) {
            $clone = $karton->copyToAudit();

            $this->autoCorrectFromMaster(
                $clone,
                self::LAPORAN_KARTON
            );

            return $clone;
        });
    }

    private function bulkBase(Request $request)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $userPlant = Auth::user()->plant;

        $master = List_form::where('plant', $userPlant)
            ->where('laporan', self::LAPORAN_KARTON)
            ->first();

        abort_if(
            !is_array($master?->standar_ref) || empty($master->standar_ref),
            422,
            'Master auto correct belum diterapkan untuk plant ini.'
        );

        $base = Karton::where('plant', $userPlant)
            ->where('is_audit', false)
            ->whereDate('date', '>=', $validated['start_date'])
            ->whereDate('date', '<=', $validated['end_date']);

        return [$base];
    }

    public function auditBulkPreview(Request $request)
    {
        [$base] = $this->bulkBase($request);

        $total = (clone $base)->count();

        $sudahAda = (clone $base)
            ->whereHas('auditVersion')
            ->count();

        return response()->json([
            'total' => $total,
            'sudah_ada' => $sudahAda,
            'akan_dibuat' => $total - $sudahAda,
        ]);
    }

    public function auditBulkExecute(Request $request)
    {
        [$base] = $this->bulkBase($request);

        $total = (clone $base)->count();

        if ($total === 0) {
            return redirect()
                ->route('karton.audit')
                ->with('warning', 'Tidak ada data Karton pada rentang tanggal tersebut.');
        }

        $created = 0;
        $failed = 0;

        (clone $base)
            ->whereDoesntHave('auditVersion')
            ->chunkById(100, function ($kartons) use (&$created, &$failed) {
                foreach ($kartons as $karton) {
                    try {
                        $this->createCorrectedAudit($karton);
                        $created++;
                    } catch (\Throwable $e) {
                        report($e);
                        $failed++;
                    }
                }
            }, 'id');

        $skipped = $total - $created - $failed;

        $message = "Duplikasi selesai. Berhasil: {$created}, dilewati: {$skipped}, gagal: {$failed}.";

        return redirect()
            ->route('karton.audit')
            ->with($failed > 0 ? 'warning' : 'success', $message);
    }

    private function autoCorrectFromMaster(
        Karton $karton,
        string $laporan
    ): void {
        $master = List_form::where('plant', $karton->plant)
            ->where('laporan', $laporan)
            ->first();

        $standarRef = $master?->standar_ref;

        if (is_string($standarRef)) {
            $standarRef = json_decode($standarRef, true);
        }

        if (!is_array($standarRef) || empty($standarRef)) {
            return;
        }

        $jumlahRule = $standarRef['jumlah'] ?? null;

        if (is_array($jumlahRule)) {
            $min = $jumlahRule['min'] ?? null;
            $max = $jumlahRule['max'] ?? null;

            $minFilled = $min !== null && $min !== '';
            $maxFilled = $max !== null && $max !== '';

            if ($minFilled && $maxFilled && is_numeric($min) && is_numeric($max)) {
                if ((int) $min <= (int) $max) {
                    $karton->jumlah = random_int((int) $min, (int) $max);
                }
            }
        }

        $keterangan = $standarRef['keterangan'] ?? null;

        if (is_string($keterangan) && trim($keterangan) !== '') {
            $karton->keterangan = trim($keterangan);
        }

        $karton->save();
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();

        $suppliers = Supplier::where('plant', $userPlant)
            ->where('jenis_barang', 'Packaging')
            ->orderBy('nama_supplier')
            ->get();

        $operators = Operator::where('plant', $userPlant)
            ->where('bagian', 'Operator')
            ->orderBy('nama_karyawan')
            ->get();

        $koordinators  = Operator::where('plant', $userPlant)
            ->where('bagian', 'Koordinator')
            ->orderBy('nama_karyawan')
            ->get();

        return view('form.karton.create', compact('produks', 'operators', 'koordinators', 'suppliers'));
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $username  = Auth::user()->username ?? 'None';
        $userPlant = Auth::user()->plant;

        $nama_produksi = 'Produksi RTT';
        if (session()->has('selected_produksi')) {
            $userProduksi = \App\Models\User::where('uuid', session('selected_produksi'))->first();
            if ($userProduksi) {
                $nama_produksi = $userProduksi->name;
            }
        }

        $request->validate([
            'date'      => 'required|date',
            'nama_produk'     => 'required|string',
            'kode_produksi'     => 'required|string',
            'kode_karton' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'waktu_mulai'     => 'nullable',
            'waktu_selesai'     => 'nullable',
            'jumlah'     => 'nullable',
            'tgl_kedatangan'     => 'nullable|date',
            'nama_supplier'     => 'nullable|string',
            'no_lot'     => 'nullable|string',
            'nama_operator'     => 'nullable|string',
            'nama_koordinator'     => 'nullable|string',
            'keterangan'   => 'nullable|string',
        ]);

        $kartonPath = $request->hasFile('kode_karton')
            ? $this->compressAndStore($request->file('kode_karton'), 'kode_karton')
            : null;

        Karton::create([
            'date'                => $request->date,
            'nama_produk'               => $request->nama_produk,
            'kode_produksi'               => $request->kode_produksi,
            'kode_karton'         => $kartonPath,
            'waktu_mulai'             => $request->waktu_mulai,
            'waktu_selesai'             => $request->waktu_selesai,
            'jumlah'              => $request->jumlah,
            'tgl_kedatangan'             => $request->tgl_kedatangan,
            'nama_supplier'             => $request->nama_supplier,
            'no_lot'              => $request->no_lot,
            'keterangan'              => $request->keterangan,
            'nama_operator'             => $request->nama_operator,
            'nama_koordinator'             => $request->nama_koordinator,
            'nama_produksi'       => $nama_produksi,
            'status_produksi'     => "1",
            'status_operator'     => "1",
            'status_koordinator'  => "1",
            'tgl_update_produksi' => now(),
            'tgl_update_operator' => now(),
            'tgl_update_koordinator' => now(),
            'username'            => $username,
            'plant'               => $userPlant,
            'status_spv'          => "0",
        ]);

        return redirect()->route('karton.index')->with('success', 'Kontrol Labelisasi Karton berhasil disimpan.');
    }

    public function update($uuid)
    {
        $karton = Karton::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $suppliers = Supplier::where('plant', $userPlant)
            ->where('jenis_barang', 'Packaging')
            ->orderBy('nama_supplier')
            ->get();

        $produks    = Produk::where('plant', $userPlant)->get();
        $operators = Operator::where('plant', $userPlant)
            ->where('bagian', 'Operator')
            ->orderBy('nama_karyawan')
            ->get();

        $koordinators  = Operator::where('plant', $userPlant)
            ->where('bagian', 'Koordinator')
            ->orderBy('nama_karyawan')
            ->get();

        return view('form.karton.update', compact('karton', 'produks', 'koordinators', 'operators', 'suppliers'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $karton = Karton::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'           => 'required|date',
            'nama_produk'    => 'required|string',
            'kode_produksi'  => 'required|string',
            'kode_karton'    => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'waktu_mulai'    => 'required',
            'waktu_selesai'  => 'nullable',
            'jumlah'         => 'nullable',
            'tgl_kedatangan' => 'nullable|date',
            'nama_supplier'  => 'nullable|string',
            'no_lot'         => 'nullable|string',
            'nama_operator'  => 'nullable|string',
            'nama_koordinator' => 'nullable|string',
            'keterangan'     => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'              => $request->date,
            'nama_produk'       => $request->nama_produk,
            'kode_produksi'     => $request->kode_produksi,
            'waktu_mulai'       => $request->waktu_mulai,
            'waktu_selesai'     => $request->waktu_selesai,
            'jumlah'            => $request->jumlah,
            'tgl_kedatangan'    => $request->tgl_kedatangan,
            'nama_supplier'     => $request->nama_supplier,
            'no_lot'            => $request->no_lot,
            'keterangan'        => $request->keterangan,
            'nama_operator'     => $request->nama_operator,
            'nama_koordinator'  => $request->nama_koordinator,
            'username_updated'  => $username_updated,
        ];

        if ($request->hasFile('kode_karton')) {
            if ($karton->kode_karton && Storage::exists($karton->kode_karton)) {
                Storage::delete($karton->kode_karton);
            }
            $updateData['kode_karton'] = $this->compressAndStore($request->file('kode_karton'), 'kode_karton');
        }

        $karton->update($updateData);

        return redirect()->route('karton.index')->with('success', 'Kontrol Labelisasi Karton berhasil diperbarui.');
    }


    public function edit($uuid)
    {
        $karton = Karton::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $suppliers = Supplier::where('plant', $userPlant)
            ->where('jenis_barang', 'Packaging')
            ->orderBy('nama_supplier')
            ->get();

        $produks    = Produk::where('plant', $userPlant)->get();
        $operators = Operator::where('plant', $userPlant)
            ->where('bagian', 'Operator')
            ->orderBy('nama_karyawan')
            ->get();

        $koordinators  = Operator::where('plant', $userPlant)
            ->where('bagian', 'Koordinator')
            ->orderBy('nama_karyawan')
            ->get();

        return view('form.karton.edit', compact('karton', 'produks', 'koordinators', 'operators', 'suppliers'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $karton = Karton::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'           => 'required|date',
            'nama_produk'    => 'required|string',
            'kode_produksi'  => 'required|string',
            'kode_karton'    => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'waktu_mulai'    => 'required',
            'waktu_selesai'  => 'nullable',
            'jumlah'         => 'nullable',
            'tgl_kedatangan' => 'nullable|date',
            'nama_supplier'  => 'nullable|string',
            'no_lot'         => 'nullable|string',
            'nama_operator'  => 'nullable|string',
            'nama_koordinator' => 'nullable|string',
            'keterangan'     => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'              => $request->date,
            'nama_produk'       => $request->nama_produk,
            'kode_produksi'     => $request->kode_produksi,
            'waktu_mulai'       => $request->waktu_mulai,
            'waktu_selesai'     => $request->waktu_selesai,
            'jumlah'            => $request->jumlah,
            'tgl_kedatangan'    => $request->tgl_kedatangan,
            'nama_supplier'     => $request->nama_supplier,
            'no_lot'            => $request->no_lot,
            'keterangan'        => $request->keterangan,
            'nama_operator'     => $request->nama_operator,
            'nama_koordinator'  => $request->nama_koordinator,
            'username_updated'  => $username_updated,
        ];

        if ($request->hasFile('kode_karton')) {
            if ($karton->kode_karton && Storage::exists($karton->kode_karton)) {
                Storage::delete($karton->kode_karton);
            }
            $updateData['kode_karton'] = $this->compressAndStore($request->file('kode_karton'), 'kode_karton');
        }

        $karton->update($updateData);

        return redirect()->route('karton.index')->with('success', 'Kontrol Labelisasi Karton berhasil diperbarui.');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Karton::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('kode_karton', 'like', "%{$search}%")
                        ->orWhere('nama_supplier', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.karton.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $karton = Karton::where('uuid', $uuid)->firstOrFail();

        $karton->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('karton.index', [
            'page'   => $request->input('page', 1),
            'search' => $request->input('search'),
            'date'   => $request->input('date'),
        ])->with('success', 'Status verifikasi Kontrol Labelisasi Karton berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $karton = Karton::where('uuid', $uuid)->firstOrFail();

        if ($karton->kode_karton && Storage::exists($karton->kode_karton)) {
            Storage::delete($karton->kode_karton);
        }
        if ($karton->handbasin && Storage::exists($karton->handbasin)) {
            Storage::delete($karton->handbasin);
        }

        $karton->delete();

        return redirect()->route('karton.index')->with('success', 'Data Kontrol Labelisasi Karton berhasil dihapus.');
    }

    public function recyclebin()
    {
        $karton = Karton::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.karton.recyclebin', compact('karton'));
    }
    public function restore($uuid)
    {
        $karton = Karton::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $karton->restore();

        return redirect()->route('karton.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $karton = Karton::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $karton->forceDelete();

        return redirect()->route('karton.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        $date = $request->input('date');
        $nama_produk = $request->input('nama_produk');
        $search = $request->input('search');
        $userPlant = Auth::user()->plant;

        $kartons = Karton::query()
            ->where('plant', $userPlant)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($nama_produk, function ($query) use ($nama_produk) {
                $query->where('nama_produk', $nama_produk);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_produk', 'like', "%{$search}%")
                        ->orWhereHas('mincing', function ($m) use ($search) {
                            $m->where('kode_produksi', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->chunk(4);

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Kontrol Labelisasi Cartooning')
            ->value('no_dokumen');

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Company');
        $pdf->SetTitle('Kontrol Labelisasi Karton');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetFont('helvetica', '', 8);

        $totalPage = $kartons->count();

        foreach ($kartons as $index => $data) {
            $pdf->AddPage('L', 'F4');

            $html = view('reports.kontrol-labelisasi-karton', [
                'kartons' => $data,
                'request' => $request,
                'noDokumen' => $noDokumen,
                'pageIndex' => $index,
                'totalPage' => $totalPage
            ])->render();

            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        }

        $pdf->Output('Kontrol_Labelisasi_Karton_' . date('Ymd_His') . '.pdf', 'I');
        exit();
    }

    private function compressAndStore($file, $prefix)
    {
        $manager = new ImageManager(new Driver());
        $path = 'public/karton';
        $filename = $prefix . '_' . Str::uuid() . '.jpg';

        $image = $manager->read($file)
            ->scale(width: 1280)
            ->toJpeg(quality: 90);

        Storage::put("$path/$filename", (string) $image);

        return "$path/$filename";
    }
}
