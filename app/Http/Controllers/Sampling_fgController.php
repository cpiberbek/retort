<?php

namespace App\Http\Controllers;

use App\Models\Sampling_fg;
use App\Models\Produk;
use App\Models\Operator;
use App\Models\Release_packing;
use App\Models\Mincing;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use TCPDF;

class Sampling_fgController extends Controller
{

    private const LAPORAN_SAMPLING = 'Pemeriksaan Proses Sampling Finish Good';

    private const AC_COLUMNS = [
        'kalibrasi', 'berat_produk', 'release', 'reject', 'hold',
        'item_mutu', 'keterangan', 'catatan',
    ];

    public function index(Request $request)
    {
        if (Auth::user()->isAuditView()) {
            return redirect()->route('sampling_fg.audit');
        }
        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant  = Auth::user()->plant;

        $data = Sampling_fg::query()
            ->where('plant', $userPlant)
            ->where('is_audit', false)
            ->when($search, function ($query) use ($search) {
                $kodeProduksi = \App\Models\Mincing::where('kode_produksi', 'like', "%{$search}%")
                    ->pluck('uuid')
                    ->toArray();

                $query->where(function ($q) use ($search, $kodeProduksi) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%");

                    if (!empty($kodeProduksi)) {
                        $q->orWhereIn('kode_produksi', $kodeProduksi);
                    }
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.sampling_fg.index', compact('data', 'search', 'date', 'shift'));
    }

    private function applySearch($query, ?string $search)
    {
        return $query->when($search, function ($query) use ($search) {
            $uuids = Mincing::where('kode_produksi', 'like', "%{$search}%")->pluck('uuid')->toArray();

            $query->where(function ($q) use ($search, $uuids) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produksi', 'like', "%{$search}%");

                if (!empty($uuids)) {
                    $q->orWhereIn('kode_produksi', $uuids);
                }
            });
        });
    }

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('sampling_fg.index');
        }

        $search = $request->input('search');
        $date   = $request->input('date');
        $shift  = $request->input('shift');

        $auditedSourceUuids = Sampling_fg::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = Sampling_fg::query()
            ->where('plant', Auth::user()->plant)
            ->with('auditVersion')
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                    ->orWhere(function ($sub) use ($auditedSourceUuids) {
                        $sub->where('is_audit', false);
                        if (!empty($auditedSourceUuids)) {
                            $sub->whereNotIn('uuid', $auditedSourceUuids);
                        }
                    });
            });

        $data = $this->applySearch($query, $search)
            ->when($date, fn ($q) => $q->whereDate('date', $date))
            ->when($shift, fn ($q) => $q->where('shift', $shift))
            ->orderByRaw('COALESCE((SELECT original.date FROM sampling_fgs AS original WHERE original.uuid = sampling_fgs.source_uuid LIMIT 1), sampling_fgs.date) DESC')
            ->orderByRaw('CAST(COALESCE((SELECT original.shift FROM sampling_fgs AS original WHERE original.uuid = sampling_fgs.source_uuid LIMIT 1), sampling_fgs.shift) AS UNSIGNED) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM sampling_fgs AS original WHERE original.uuid = sampling_fgs.source_uuid LIMIT 1), sampling_fgs.created_at) DESC')
            ->paginate(10)
            ->appends($request->all());

        return view('form.sampling_fg.index_audit', compact('data', 'search', 'date', 'shift'));
    }

    public function duplicateToAudit(string $uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $row = Sampling_fg::where('uuid', $uuid)
            ->where('plant', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $row->auditVersion) {
            return redirect()->route('sampling_fg.edit.form', $existing->uuid);
        }

        $clone = $this->createCorrectedAudit($row);

        return redirect()->route('sampling_fg.edit.form', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    private function createCorrectedAudit(Sampling_fg $row): Sampling_fg
    {
        return DB::transaction(function () use ($row) {
            $clone = $row->copyToAudit();
            $this->autoCorrectFromMaster($clone);
            return $clone;
        });
    }

    private function bulkBase(Request $request): array
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($request->start_date)->toDateString();
        $end   = Carbon::parse($request->end_date)->toDateString();
        $plant = Auth::user()->plant;

        $master = List_form::where('plant', $plant)
            ->where('laporan', self::LAPORAN_SAMPLING)
            ->first();

        abort_if(empty($master?->standar_ref), 422, 'Master auto correct belum diterapkan untuk plant ini.');

        $base = Sampling_fg::where('plant', $plant)
            ->where('is_audit', false)
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end);

        return [$base];
    }

    public function auditBulkPreview(Request $request)
    {
        [$base] = $this->bulkBase($request);

        $total = (clone $base)->count();
        $sudah = (clone $base)->whereHas('auditVersion')->count();

        return response()->json([
            'total'       => $total,
            'sudah_ada'   => $sudah,
            'akan_dibuat' => $total - $sudah,
        ]);
    }

    public function auditBulkExecute(Request $request)
    {
        [$base] = $this->bulkBase($request);

        $total = (clone $base)->count();
        if ($total === 0) {
            return redirect()->route('sampling_fg.audit')->with('warning', 'Tidak ada entry di tanggal ini.');
        }

        $targets = (clone $base)->whereDoesntHave('auditVersion')->get();
        $skipped = $total - $targets->count();
        $created = 0;
        $failed  = 0;

        foreach ($targets as $row) {
            try {
                $this->createCorrectedAudit($row);
                $created++;
            } catch (\Throwable $e) {
                $failed++;
                report($e);
            }
        }

        if ($created === 0 && $failed === 0) {
            return redirect()->route('sampling_fg.audit')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi dan disesuaikan sesuai master. {$skipped} dilewati karena sudah ada.";
        if ($failed) $msg .= " {$failed} gagal diproses (cek log).";

        return redirect()->route('sampling_fg.audit')->with($failed ? 'warning' : 'success', $msg);
    }

    private function autoCorrectFromMaster(Sampling_fg $model): void
    {
        $master = List_form::where('plant', $model->plant)
            ->where('laporan', self::LAPORAN_SAMPLING)
            ->first();

        $std = $master?->standar_ref;
        if (is_string($std)) $std = json_decode($std, true);
        if (!is_array($std) || empty($std)) return;

        foreach (self::AC_COLUMNS as $col) {
            $rule = $std[$col] ?? null;

            if (is_array($rule)) {
                $min = $rule['min'] ?? null;
                $max = $rule['max'] ?? null;
                if ($min === null || $min === '' || $max === null || $max === '') continue;

                $model->$col = mt_rand((int) $min, (int) $max);
            } elseif (is_string($rule) && trim($rule) !== '') {
                $model->$col = $rule;
            }
        }

        $model->save();
    }

    public function exportPdf(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        $items = Sampling_fg::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $kodeProduksi = Mincing::where('kode_produksi', 'like', "%{$search}%")
                    ->pluck('uuid')
                    ->toArray();

                $query->where(function ($q) use ($search, $kodeProduksi) {
                    $q->where('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%");

                    if (!empty($kodeProduksi)) {
                        $q->orWhereIn('kode_produksi', $kodeProduksi);
                    }
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->orderBy('date', 'asc')
            ->orderBy('shift', 'asc')
            ->get();

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Proses Sampling Finish Good')
            ->value('no_dokumen');


        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = new \TCPDF('L', PDF_UNIT, 'F4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Laporan Sampling Finish Good');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(true, 5);
        $pdf->SetFont('helvetica', '', 7);

        $pdf->AddPage();

        $html = view('reports.sampling_fg', compact('items', 'request', 'noDokumen'))->render();

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Laporan_Sampling_FG_' . date('d-m-Y_His') . '.pdf', 'I');
        exit();
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $koordinators  = Operator::where('plant', $userPlant)
            ->where('bagian', 'Koordinator')
            ->orderBy('nama_karyawan')
            ->get();

        return view('form.sampling_fg.create', compact('produks', 'koordinators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required|string',
            'palet'           => 'required|string',
            'nama_produk'     => 'required|string',
            'kode_produksi'   => 'required|string',
            'exp_date'        => 'required|date',
            'pukul'           => 'required',
            'kalibrasi'       => 'nullable|string',
            'berat_produk'    => 'nullable|integer',
            'keterangan'      => 'nullable|string',
            'isi_per_box'     => 'nullable|integer',
            'kemasan'         => 'nullable|string',
            'jumlah_box'      => 'nullable|integer',
            'release'         => 'nullable|integer',
            'reject'          => 'nullable|integer',
            'hold'            => 'nullable|integer',
            'item_mutu'       => 'nullable|string',
            'catatan'         => 'nullable|string',
            'nama_koordinator' => 'nullable|string',
        ]);

        $username  = Auth::user()->username ?? 'None';
        $userPlant = Auth::user()->plant;

        $data = [
            'date'            => $request->date,
            'shift'           => $request->shift,
            'palet'           => $request->palet,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'exp_date'        => $request->exp_date,
            'pukul'           => $request->pukul,
            'kalibrasi'       => $request->kalibrasi,
            'berat_produk'    => $request->berat_produk,
            'keterangan'      => $request->keterangan,
            'isi_per_box'     => $request->isi_per_box,
            'kemasan'         => $request->kemasan,
            'jumlah_box'      => $request->jumlah_box,
            'release'         => $request->release,
            'reject'          => $request->reject,
            'hold'            => $request->hold,
            'item_mutu'       => $request->item_mutu,
            'catatan'         => $request->catatan,
            'nama_koordinator' => $request->nama_koordinator,
            'username'        => $username,
            'plant'           => $userPlant,
            'status_spv'      => "0",
            'status_koordinator'  => "1",
            'tgl_update_koordinator' => now(),
        ];

        Sampling_fg::create($data);

        return redirect()->route('sampling_fg.index')
            ->with('success', 'Data Pemeriksaan Proses Sampling Finish Good berhasil disimpan.');
    }

    public function update(string $uuid)
    {
        $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $koordinators  = Operator::where('plant', $userPlant)
            ->where('bagian', 'Koordinator')
            ->orderBy('nama_karyawan')
            ->get();

        return view('form.sampling_fg.update', compact('sampling_fg', 'produks', 'koordinators'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required|string',
            'palet'           => 'required|string',
            'nama_produk'     => 'required|string',
            'kode_produksi'   => 'required|string',
            'exp_date'        => 'required|date',
            'pukul'           => 'required',
            'kalibrasi'       => 'nullable|string',
            'berat_produk'    => 'nullable|integer',
            'keterangan'      => 'nullable|string',
            'isi_per_box'     => 'nullable|integer',
            'kemasan'         => 'nullable|string',
            'jumlah_box'      => 'nullable|integer',
            'release'         => 'nullable|integer',
            'reject'          => 'nullable|integer',
            'hold'            => 'nullable|integer',
            'item_mutu'       => 'nullable|string',
            'catatan'         => 'nullable|string',
            'nama_koordinator' => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'            => $request->date,
            'shift'           => $request->shift,
            'palet'           => $request->palet,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'exp_date'        => $request->exp_date,
            'pukul'           => $request->pukul,
            'kalibrasi'       => $request->kalibrasi,
            'berat_produk'    => $request->berat_produk,
            'keterangan'      => $request->keterangan,
            'isi_per_box'     => $request->isi_per_box,
            'kemasan'         => $request->kemasan,
            'jumlah_box'      => $request->jumlah_box,
            'release'         => $request->release,
            'reject'          => $request->reject,
            'hold'            => $request->hold,
            'item_mutu'       => $request->item_mutu,
            'catatan'         => $request->catatan,
            'nama_koordinator' => $request->nama_koordinator,
            'username_updated' => $username_updated,
            'status_koordinator'  => "1",
            'tgl_update_koordinator' => now(),
        ];

        $sampling_fg->update($updateData);

        return redirect()->route('sampling_fg.index')
            ->with('success', 'Pemeriksaan Proses sampling_fg Finish Good berhasil diperbarui.');
    }

    public function edit(string $uuid)
    {
        $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $koordinators  = Operator::where('plant', $userPlant)
            ->where('bagian', 'Koordinator')
            ->orderBy('nama_karyawan')
            ->get();

        return view('form.sampling_fg.edit', compact('sampling_fg', 'produks', 'koordinators'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required|string',
            'palet'           => 'required|string',
            'nama_produk'     => 'required|string',
            'kode_produksi'   => 'required|string',
            'exp_date'        => 'required|date',
            'pukul'           => 'required',
            'kalibrasi'       => 'nullable|string',
            'berat_produk'    => 'nullable|integer',
            'keterangan'      => 'nullable|string',
            'isi_per_box'     => 'nullable|integer',
            'kemasan'         => 'nullable|string',
            'jumlah_box'      => 'nullable|integer',
            'release'         => 'nullable|integer',
            'reject'          => 'nullable|integer',
            'hold'            => 'nullable|integer',
            'item_mutu'       => 'nullable|string',
            'catatan'         => 'nullable|string',
            'nama_koordinator' => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'            => $request->date,
            'shift'           => $request->shift,
            'palet'           => $request->palet,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'exp_date'        => $request->exp_date,
            'pukul'           => $request->pukul,
            'kalibrasi'       => $request->kalibrasi,
            'berat_produk'    => $request->berat_produk,
            'keterangan'      => $request->keterangan,
            'isi_per_box'     => $request->isi_per_box,
            'kemasan'         => $request->kemasan,
            'jumlah_box'      => $request->jumlah_box,
            'release'         => $request->release,
            'reject'          => $request->reject,
            'hold'            => $request->hold,
            'item_mutu'       => $request->item_mutu,
            'catatan'         => $request->catatan,
            'nama_koordinator' => $request->nama_koordinator,
            'username_updated' => $username_updated,
            'status_koordinator'  => "1",
            'tgl_update_koordinator' => now(),
        ];

        $sampling_fg->update($updateData);

        return redirect()->route('sampling_fg.index')
            ->with('success', 'Pemeriksaan Proses sampling_fg Finish Good berhasil diperbarui.');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Sampling_fg::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.sampling_fg.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();

        $sampling_fg->update([
            'status_spv'  => $request->status_spv,
            'catatan_spv' => $request->catatan_spv,
            'nama_spv'    => Auth::user()->username,
            'tgl_update_spv' => now(),
        ]);

        return redirect()->route('sampling_fg.index', [
            'page' => $request->input('page', 1),
            'search' => $request->input('search'),
            'date' => $request->input('date'),
        ])->with('success', 'Status Verifikasi Pemeriksaan Proses sampling_fg Finish Good berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();
        $sampling_fg->delete();
        return redirect()->route('sampling_fg.index')->with('success', 'Sampling FG berhasil dihapus');
    }

    public function recyclebin()
    {
        $sampling_fg = Sampling_fg::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.sampling_fg.recyclebin', compact('sampling_fg'));
    }
    public function restore($uuid)
    {
        $sampling_fg = Sampling_fg::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $sampling_fg->restore();

        return redirect()->route('sampling_fg.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $sampling_fg = Sampling_fg::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $sampling_fg->forceDelete();

        return redirect()->route('sampling_fg.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function getJumlahBox(Request $request)
    {
        $nama_produk   = $request->input('nama_produk');
        $kode_produksi = $request->input('kode_produksi');
        $no_palet      = $request->input('no_palet');

        if (!$kode_produksi) {
            return response()->json([
                'jumlah_box' => 0,
            ]);
        }

        $releaseQuery = Release_packing::query();

        if (Str::isUuid($kode_produksi)) {
            $mincing = Mincing::where('uuid', $kode_produksi);

            if ($nama_produk) {
                $mincing->where('nama_produk', $nama_produk);
            }

            $mincing = $mincing->first();

            if (!$mincing) {
                return response()->json([
                    'jumlah_box' => 0,
                ]);
            }

            $kode_produksi = $mincing->kode_produksi;
        }

        $releaseQuery->where('kode_produksi', $kode_produksi);

        if ($nama_produk) {
            $releaseQuery->where('nama_produk', $nama_produk);
        }

        if ($no_palet) {
            $releaseQuery->where('no_palet', $no_palet);
        }

        $jumlahBox = $releaseQuery->sum('release');

        return response()->json([
            'nama_produk'   => $nama_produk,
            'kode_produksi' => $kode_produksi,
            'no_palet'      => $no_palet,
            'jumlah_box'    => $jumlahBox,
            'total_box'     => $jumlahBox,
        ]);
    }

    public function getBatch($nama_produk)
    {
        $data = DB::table('mincings')
            ->where('nama_produk', $nama_produk)
            ->select('uuid', 'kode_produksi')
            ->get();

        return response()->json($data);
    }

    public function getPalet(Request $request)
    {
        $kode_produksi = $request->input('kode_produksi');
        $nama_produk = $request->input('nama_produk');

        if (!$kode_produksi) {
            return response()->json([]);
        }

        if (Str::isUuid($kode_produksi)) {
            $query = Mincing::where('uuid', $kode_produksi);

            if ($nama_produk) {
                $query->where('nama_produk', $nama_produk);
            }

            $mincing = $query->first();

            if (!$mincing) {
                return response()->json([]);
            }

            $kode_produksi = $mincing->kode_produksi;
        }

        $palets = Release_packing::where('kode_produksi', $kode_produksi)
            ->when($nama_produk, function ($query) use ($nama_produk) {
                $query->where('nama_produk', $nama_produk);
            })
            ->select('no_palet')
            ->distinct()
            ->orderBy('no_palet')
            ->get();

        return response()->json($palets);
    }
}
