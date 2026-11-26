<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TCPDF;

class PackingController extends Controller
{

    private const LAPORAN_PACKING = 'Pemeriksaan Proses Packing';   // <-- sesuaikan dengan List_form

    private const AC_RANGE = ['suhu', 'speed', 'berat_pcs', 'berat_pack'];
    private const AC_TEXT  = ['kalibrasi', 'kondisi_segel', 'keterangan'];

    public function index(Request $request)
    {
        if (Auth::user()->isAuditView()) {
            return redirect()->route('packing.audit');
        }

        $search      = $request->input('search');
        $date        = $request->input('date');
        $shift       = $request->input('shift');
        $nama_produk = $request->input('nama_produk');
        $userPlant   = Auth::user()->plant;

        $data = Packing::query()
            ->where('plant', $userPlant)
            ->where('is_audit', false)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->when($nama_produk, function ($query) use ($nama_produk) {
                $query->where('nama_produk', $nama_produk);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.packing.index', compact('data', 'search', 'date', 'shift', 'nama_produk'));
    }

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('packing.index');
        }

        $search      = $request->input('search');
        $date        = $request->input('date');
        $shift       = $request->input('shift');
        $nama_produk = $request->input('nama_produk');

        $auditedSourceUuids = Packing::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $data = Packing::query()
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
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%");
                });
            })
            ->when($date, fn ($q) => $q->whereDate('date', $date))
            ->when($shift, fn ($q) => $q->where('shift', $shift))
            ->when($nama_produk, fn ($q) => $q->where('nama_produk', $nama_produk))
            ->orderByRaw('COALESCE((SELECT original.date FROM packings AS original WHERE original.uuid = packings.source_uuid LIMIT 1), packings.date) DESC')
            ->orderByRaw('CAST(COALESCE((SELECT original.shift FROM packings AS original WHERE original.uuid = packings.source_uuid LIMIT 1), packings.shift) AS UNSIGNED) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM packings AS original WHERE original.uuid = packings.source_uuid LIMIT 1), packings.created_at) DESC')
            ->paginate(10)
            ->appends($request->all());

        return view('form.packing.index_audit', compact('data', 'search', 'date', 'shift', 'nama_produk'));
    }

    public function multiVerification(Request $request)
    {
        $request->validate([
            'startdate' => 'required|date',
            'enddate'   => 'required|date|after_or_equal:startdate',
        ]);

        $updated = Packing::where('is_audit', 0)
            ->where('plant', Auth::user()->plant_active ?? Auth::user()->plant)
            ->where('status_spv', 0)
            ->whereBetween('date', [
                $request->startdate,
                $request->enddate,
            ])
            ->update([
                'status_spv'     => 1,
                'catatan_spv'    => null,
                'nama_spv'       => Auth::user()->username,
                'tgl_update_spv' => now(),
            ]);

        return redirect()->route('packing.index')
            ->with('success', "Multi Verification Packing berhasil. {$updated} data diperbarui.");
    }

    public function duplicateToAudit(string $uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $row = Packing::where('uuid', $uuid)
            ->where('plant', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $row->auditVersion) {
            return redirect()->route('packing.edit.form', $existing->uuid);
        }

        $clone = $this->createCorrectedAudit($row);

        return redirect()->route('packing.edit.form', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    private function createCorrectedAudit(Packing $row): Packing
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
            ->where('laporan', self::LAPORAN_PACKING)
            ->first();

        abort_if(empty($master?->standar_ref), 422, 'Master auto correct belum diterapkan untuk plant ini.');

        $base = Packing::where('plant', $plant)
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
            return redirect()->route('packing.audit')->with('warning', 'Tidak ada entry di tanggal ini.');
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
            return redirect()->route('packing.audit')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi dan disesuaikan sesuai master. {$skipped} dilewati karena sudah ada.";
        if ($failed) $msg .= " {$failed} gagal diproses (cek log).";

        return redirect()->route('packing.audit')->with($failed ? 'warning' : 'success', $msg);
    }

    private function autoCorrectFromMaster(Packing $model): void
    {
        $master = List_form::where('plant', $model->plant)
            ->where('laporan', self::LAPORAN_PACKING)
            ->first();

        $std = $master?->standar_ref;

        if (is_string($std)) {
            $std = json_decode($std, true);
        }

        if (!is_array($std) || empty($std)) {
            return;
        }

        foreach (self::AC_RANGE as $col) {
            $rule = $std[$col] ?? null;

            if (!is_array($rule)) {
                continue;
            }

            $min = $rule['min'] ?? null;
            $max = $rule['max'] ?? null;

            if (
                $min === null || $min === '' ||
                $max === null || $max === ''
            ) {
                continue;
            }

            $min = (float) $min;
            $max = (float) $max;

            if ($min > $max) {
                continue;
            }

            $rand = fn () => round(
                mt_rand(
                    (int) round($min * 100),
                    (int) round($max * 100)
                ) / 100,
                2
            );

            if ($col === 'suhu') {
                $current = $model->suhu;

                if (is_string($current)) {
                    $current = json_decode($current, true);
                }

                if (is_array($current) && count($current) > 0) {
                    $model->suhu = array_map(
                        fn () => (string) $rand(),
                        $current
                    );
                }
            } else {
                $model->$col = $rand();
            }
        }

        foreach (self::AC_TEXT as $col) {
            $rule = $std[$col] ?? null;

            if (is_string($rule) && trim($rule) !== '') {
                $model->$col = $rule;
            }
        }

        $model->save();
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $suppliers = Supplier::where('plant', $userPlant)
            ->where('jenis_barang', 'Packaging')
            ->orderBy('nama_supplier')
            ->get();

        return view('form.packing.create', compact('produks', 'suppliers'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTT';

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'waktu'         => 'required',
            'kalibrasi'     => 'nullable|string',
            'qrcode'        => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'kode_printing' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'kode_toples'   => 'required|string',
            'suhu'          => 'nullable|array',
            'suhu.*'        => 'nullable|numeric',
            'speed'         => 'nullable|numeric',
            'kondisi_segel' => 'nullable|string',
            'jumlah_produk' => 'nullable|integer',
            'berat_pcs'     => 'nullable|numeric',
            'berat_pack'    => 'nullable|numeric',

            // Validasi Input Dinamis Array Kemasan
            'data_kemasan'                  => 'nullable|array',
            'data_kemasan.*.jenis_kemasan'  => 'required|string',
            'data_kemasan.*.no_lot_kemasan' => 'required|string',
            'data_kemasan.*.tgl_kedatangan' => 'nullable|date',
            'data_kemasan.*.nama_supplier'  => 'nullable|string',
            'keterangan'                    => 'nullable|string',
        ]);

        $kodePrintingFinal = $request->hasFile('kode_printing') ? $this->compressAndStore($request->file('kode_printing'), 'printing') : null;
        $qrcodeFinal = $request->hasFile('qrcode') ? $this->compressAndStore($request->file('qrcode'), 'qrcode') : null;

        Packing::create([
            'date'                => $request->date,
            'shift'               => $request->shift,
            'nama_produk'         => $request->nama_produk,
            'waktu'               => $request->waktu,
            'kalibrasi'           => $request->has('kalibrasi') ? 'Ok' : 'Tidak Ok',
            'qrcode'              => $qrcodeFinal,
            'kode_printing'       => $kodePrintingFinal,
            'kode_toples'         => $request->kode_toples,
            'suhu'                => $request->suhu ? array_values(array_filter($request->suhu, fn($value) => $value !== null && $value !== '')) : null,
            'speed'               => $request->speed,
            'kondisi_segel'       => $request->kondisi_segel,
            'jumlah_produk'       => $request->jumlah_produk,
            'berat_pcs'           => $request->berat_pcs,
            'berat_pack'          => $request->berat_pack,

            // Encode menjadi JSON string sebelum masuk DB
            'data_kemasan'        => $request->has('data_kemasan') ? json_encode($request->data_kemasan) : null,
            'keterangan'          => $request->keterangan,

            'username'            => $username,
            'plant'               => $userPlant,
            'nama_produksi'       => $nama_produksi,
            'status_produksi'     => "1",
            'tgl_update_produksi' => now()->addHour(),
            'status_spv'          => "0",
        ]);

        return redirect()->route('packing.index')->with('success', 'Pemeriksaan Proses Packing berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $packing = Packing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $suppliers = Supplier::where('plant', $userPlant)
            ->where('jenis_barang', 'Packaging')
            ->orderBy('nama_supplier')
            ->get();

        return view('form.packing.update', compact('packing', 'produks', 'suppliers'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $packing = Packing::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'waktu'         => 'required',
            'kalibrasi'     => 'nullable|string',
            'qrcode'        => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'kode_printing' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'kode_toples'   => 'required|string',
            'data_kemasan'  => 'nullable|array',
            'keterangan'    => 'nullable|string',
        ]);

        // File Handler Code (Compressed)
        $kodePrintingFinal = $request->hasFile('kode_printing') ? $this->compressAndStore($request->file('kode_printing'), 'printing') : $packing->kode_printing;
        $qrcodeFinal = $request->hasFile('qrcode') ? $this->compressAndStore($request->file('qrcode'), 'qrcode') : $packing->qrcode;

        $packing->update([
            'date'                => $request->date,
            'shift'               => $request->shift,
            'nama_produk'         => $request->nama_produk,
            'waktu'               => $request->waktu,
            'kalibrasi'           => $request->has('kalibrasi') ? 'Ok' : 'Tidak Ok',
            'qrcode'              => $qrcodeFinal,
            'kode_printing'       => $kodePrintingFinal,
            'kode_toples'         => $request->kode_toples,
            'suhu' => $request->suhu
                ? array_values(array_filter($request->suhu, fn($value) => $value !== null && $value !== ''))
                : null,
            'speed'               => $request->speed,
            'kondisi_segel'       => $request->kondisi_segel,
            'jumlah_produk'       => $request->jumlah_produk,
            'berat_pcs'           => $request->berat_pcs,
            'berat_pack'          => $request->berat_pack,
            'data_kemasan'        => $request->has('data_kemasan') ? json_encode($request->data_kemasan) : null,
            'keterangan'          => $request->keterangan,
            'username_updated'    => $username_updated,
        ]);

        return redirect()->route('packing.index')->with('success', 'Pemeriksaan Proses Packing berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $packing = Packing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $suppliers = Supplier::where('plant', $userPlant)
            ->where('jenis_barang', 'Packaging')
            ->orderBy('nama_supplier')
            ->get();

        return view('form.packing.edit', compact('packing', 'produks', 'suppliers'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $packing = Packing::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'waktu'         => 'required',
            'kode_toples'   => 'required|string',
            'data_kemasan'  => 'nullable|array',
            'keterangan'    => 'nullable|string',
            'suhu'          => 'nullable|array',
            'suhu.*'        => 'nullable|numeric',
        ]);

        $qrcodeFinal = $request->hasFile('qrcode')
            ? $this->compressAndStore($request->file('qrcode'), 'qrcode')
            : $packing->qrcode;

        $kodePrintingFinal = $request->hasFile('kode_printing')
            ? $this->compressAndStore($request->file('kode_printing'), 'printing')
            : $packing->kode_printing;

        $packing->update([
            'date'                => $request->date,
            'shift'               => $request->shift,
            'nama_produk'         => $request->nama_produk,
            'waktu'               => $request->waktu,
            'kalibrasi'           => $request->has('kalibrasi') ? 'Ok' : 'Tidak Ok',
            'kode_toples'         => $request->kode_toples,
            'suhu' => $request->suhu
                ? array_values(array_filter($request->suhu, fn($value) => $value !== null && $value !== ''))
                : null,
            'speed'               => $request->speed,
            'kondisi_segel'       => $request->kondisi_segel,
            'jumlah_produk'       => $request->jumlah_produk,
            'berat_pcs'           => $request->berat_pcs,
            'berat_pack'          => $request->berat_pack,
            'data_kemasan'        => $request->has('data_kemasan') ? json_encode($request->data_kemasan) : null,
            'keterangan'          => $request->keterangan,
            'qrcode'              => $qrcodeFinal,
            'kode_printing'       => $kodePrintingFinal,
        ]);

        return redirect()->route('packing.index')->with('success', 'Pemeriksaan Proses Packing berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Packing::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.packing.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $packing = Packing::where('uuid', $uuid)->firstOrFail();

        $packing->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('packing.index', [
            'page'   => $request->input('page', 1),
            'search' => $request->input('search'),
            'date'   => $request->input('date'),
        ])->with('success', 'Status Verifikasi Pemeriksaan Proses Packing berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $packing = Packing::where('uuid', $uuid)->firstOrFail();

        if (!empty($packing->kode_printing)) {
            $path = str_replace('storage/', 'public/', $packing->kode_printing);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        if (!empty($packing->qrcode)) {
            $pathQr = str_replace('storage/', 'public/', $packing->qrcode);
            if (Storage::exists($pathQr)) {
                Storage::delete($pathQr);
            }
        }

        $packing->delete();
        return redirect()->route('packing.index')->with('success', 'Pemeriksaan Proses Packing berhasil dihapus');
    }

    public function recyclebin()
    {
        $packing = Packing::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
        return view('form.packing.recyclebin', compact('packing'));
    }

    public function restore($uuid)
    {
        $packing = Packing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $packing->restore();
        return redirect()->route('packing.recyclebin')->with('success', 'Data berhasil direstore.');
    }

    public function deletePermanent($uuid)
    {
        $packing = Packing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $packing->forceDelete();
        return redirect()->route('packing.recyclebin')->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        $date        = $request->input('date');
        $shift       = $request->input('shift');
        $nama_produk = $request->input('nama_produk');
        $userPlant   = Auth::user()->plant;

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Proses Packing')
            ->value('no_dokumen');

        $packings = Packing::query()
            ->where('plant', $userPlant)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->when($nama_produk, function ($query) use ($nama_produk) {
                $query->where('nama_produk', $nama_produk);
            })
            ->orderBy('date', 'asc')
            ->orderBy('shift', 'asc')
            ->get();

        $usernames = $packings
            ->flatMap(fn($p) => [$p->username, $p->nama_produksi, $p->nama_spv])
            ->filter()
            ->unique()
            ->values();

        $userMap  = \App\Models\User::whereIn('username', $usernames)->pluck('name', 'username');
        $namaUser = fn($username) => $username ? ($userMap[$username] ?? $username) : '';

        $kodeTopleMap = \App\Models\Mincing::whereIn(
            'uuid',
            $packings->pluck('kode_toples')->filter()->unique()
        )->pluck('kode_produksi', 'uuid');

        $decode = function ($value) {
            for ($i = 0; $i < 2; $i++) {
                if (is_string($value) && $value !== '') {
                    $decoded = json_decode($value, true);
                    if ($decoded !== null) {
                        $value = $decoded;
                    }
                }
            }
            return is_array($value) ? $value : [];
        };

        $imagePath = function ($path) {
            if (empty($path)) {
                return '';
            }
            $full = storage_path('app/public/' . ltrim($path, '/'));
            return file_exists($full) ? $full : '';
        };

        $dash = fn($v) => ($v === null || $v === '') ? '-' : $v;

        $rows = [];

        foreach ($packings as $p) {
            $suhuList = $decode($p->suhu);
            if (empty($suhuList) && $p->suhu !== null && $p->suhu !== '') {
                $suhuList = [$p->suhu];
            }

            $suhu = [];
            foreach (array_values($suhuList) as $i => $v) {
                $suhu[] = '#' . ($i + 1) . ': ' . $v;
            }

            $kemasan = [];
            foreach (array_values($decode($p->data_kemasan)) as $k) {
                $kemasan[] = [
                    'jenis'    => $dash($k['jenis_kemasan'] ?? null),
                    'lot'      => $dash($k['no_lot_kemasan'] ?? null),
                    'tgl'      => !empty($k['tgl_kedatangan'])
                        ? \Carbon\Carbon::parse($k['tgl_kedatangan'])->format('d-m-Y')
                        : '-',
                    'supplier' => $dash($k['nama_supplier'] ?? null),
                ];
            }

            if (empty($kemasan)) {
                $kemasan[] = ['jenis' => '-', 'lot' => '-', 'tgl' => '-', 'supplier' => '-'];
            }

            $rows[] = [
                'waktu'      => $p->waktu ? \Carbon\Carbon::parse($p->waktu)->format('H:i') : '-',
                'kalibrasi'  => $dash($p->kalibrasi),
                'qrcode'     => $imagePath($p->qrcode),
                'printing'   => $imagePath($p->kode_printing),
                'toples'     => $dash($kodeTopleMap[$p->kode_toples] ?? null),
                'suhu'       => $suhu,
                'speed'      => $dash($p->speed),
                'segel'      => $dash($p->kondisi_segel),
                'jumlah'     => $dash($p->jumlah_produk),
                'berat_pcs'  => $dash($p->berat_pcs),
                'berat_pack' => $dash($p->berat_pack),
                'kemasan'    => $kemasan,
                'qc'         => $dash($namaUser($p->username)),
                'produksi'   => $dash($namaUser($p->nama_produksi)),
                'ket'        => $dash($p->keterangan),
            ];
        }

        $pages       = [];
        $currentPage = [];
        $currentCost = 0;

        foreach ($rows as $row) {
            $cost = count($row['kemasan']);

            if (!empty($currentPage) && $currentCost + $cost > 20) {
                $pages[]     = $currentPage;
                $currentPage = [];
                $currentCost = 0;
            }

            $currentPage[] = $row;
            $currentCost  += $cost;
        }

        $pages[] = $currentPage;

        $tanggal = $date
            ? \Carbon\Carbon::parse($date)->format('d-m-Y')
            : $packings->pluck('date')
                ->filter()
                ->unique()
                ->map(fn($d) => \Carbon\Carbon::parse($d)->format('d-m-Y'))
                ->implode(', ');

        $shiftText  = $shift ?: $packings->pluck('shift')->filter()->unique()->implode(', ');
        $namaVarian = $packings->pluck('nama_produk')->filter()->unique()->implode(', ');

        $first        = $packings->first();
        $pemeriksa    = $first ? $namaUser(trim((string) $first->username)) : '';
        $tglPemeriksa = ($first && $first->created_at)
            ? \Carbon\Carbon::parse($first->created_at)->format('d-m-Y')
            : '';

        $semuaDisetujui = $packings->isNotEmpty()
            && $packings->every(fn($p) => (string) $p->status_spv === '1');

        $spv    = '';
        $tglSpv = '';

        if ($semuaDisetujui) {
            $spv    = $namaUser(trim((string) ($first->nama_spv ?? '')));
            $tglSpv = $first->tgl_update_spv
                ? \Carbon\Carbon::parse($first->tgl_update_spv)->format('d-m-Y')
                : '';
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pemeriksaan Proses Packing');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(true, 5);

        $pdf->SetFont('helvetica', '', 6);
        $pdf->AddPage();

        $html = view('reports.proses-packing', compact(
            'pages',
            'tanggal',
            'shiftText',
            'namaVarian',
            'noDokumen',
            'pemeriksa',
            'tglPemeriksa',
            'spv',
            'tglSpv',
            'semuaDisetujui'
        ))->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('Pemeriksaan_Proses_Packing_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }

    private function compressAndStore($file, $prefix)
    {
        $manager = new ImageManager(new Driver());
        $path = 'public/uploads/packing';
        $filename = $prefix . '_' . Str::uuid() . '.jpg';

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path, 0755, true);
        }

        $image = $manager->read($file)->scale(width: 1280)->toJpeg(quality: 90);
        Storage::put("$path/$filename", (string) $image);

        return "storage/uploads/packing/$filename";
    }
}
