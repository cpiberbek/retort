<?php

namespace App\Http\Controllers;

use App\Models\LoadingProduk;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class LoadingProdukController extends Controller
{
    private const LAPORAN_LOADING = 'Pemeriksaan Loading-Unloading Produk';

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $mincingUuids = \App\Models\Mincing::where('kode_produksi', 'like', "%{$search}%")
                ->pluck('uuid');

            $query->where(function ($q) use ($search, $mincingUuids) {
                $q->where('no_pol_mobil', 'like', "%{$search}%")
                    ->orWhere('nama_supir', 'like', "%{$search}%")
                    ->orWhere('ekspedisi', 'like', "%{$search}%")
                    ->orWhereHas('details', function ($detailQuery) use ($search, $mincingUuids) {
                        $detailQuery->where('kode_produksi', 'like', "%{$search}%");

                        if ($mincingUuids->isNotEmpty()) {
                            $detailQuery->orWhereIn('kode_produksi', $mincingUuids);
                        }
                    });
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        if (Auth::user()->isAuditView()) {
            return redirect()->route('loading-produks.audit');
        }

        $query = LoadingProduk::with('creator')
            ->where('plant_uuid', auth()->user()->plant)
            ->where('is_audit', false);

        $produks = $this->applyFilters($query, $request)->latest()->paginate(10);

        return view('loading-produks.index', compact('produks'));
    }

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('loading-produks.index');
        }

        $auditedSourceUuids = LoadingProduk::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = LoadingProduk::with(['creator', 'auditVersion'])
            ->where('plant_uuid', Auth::user()->plant)
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                    ->orWhere(function ($sub) use ($auditedSourceUuids) {
                        $sub->where('is_audit', false);
                        if (!empty($auditedSourceUuids)) {
                            $sub->whereNotIn('uuid', $auditedSourceUuids);
                        }
                    });
            });

        $produks = $this->applyFilters($query, $request)
            ->orderByRaw('COALESCE((SELECT original.tanggal FROM loading_checks AS original WHERE original.uuid = loading_checks.source_uuid LIMIT 1), loading_checks.tanggal) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM loading_checks AS original WHERE original.uuid = loading_checks.source_uuid LIMIT 1), loading_checks.created_at) DESC')
            ->paginate(10)
            ->withQueryString();

        return view('loading-produks.index_audit', compact('produks'));
    }

    public function duplicateToAudit(string $uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $row = LoadingProduk::where('uuid', $uuid)
            ->where('plant_uuid', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $row->auditVersion) {
            return redirect()->route('loading-produks.edit', $existing->uuid);
        }

        $clone = $this->createCorrectedAudit($row);

        return redirect()->route('loading-produks.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    private function createCorrectedAudit(LoadingProduk $row): LoadingProduk
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
            ->where('laporan', self::LAPORAN_LOADING)
            ->first();

        abort_if(empty($master?->standar_ref), 422, 'Master auto correct belum diterapkan untuk plant ini.');

        $base = LoadingProduk::where('plant_uuid', $plant)
            ->where('is_audit', false)
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end);

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
            return redirect()->route('loading-produks.audit')->with('warning', 'Tidak ada entry di tanggal ini.');
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
            return redirect()->route('loading-produks.audit')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi dan disesuaikan sesuai master. {$skipped} dilewati karena sudah ada.";
        if ($failed) $msg .= " {$failed} gagal diproses (cek log).";

        return redirect()->route('loading-produks.audit')->with($failed ? 'warning' : 'success', $msg);
    }

    private function autoCorrectFromMaster(LoadingProduk $model): void
    {
        $master = List_form::where('plant', $model->plant_uuid)
            ->where('laporan', self::LAPORAN_LOADING)
            ->first();

        $std = $master?->standar_ref;
        if (is_string($std)) $std = json_decode($std, true);
        if (!is_array($std) || empty($std)) return;

        // detail (jumlah baris bebas)
        $min = $std['jumlah']['min'] ?? null;
        $max = $std['jumlah']['max'] ?? null;
        $hasJumlah = $min !== null && $min !== '' && $max !== null && $max !== '';

        $itemKet = $std['item_keterangan'] ?? null;
        $hasItemKet = is_string($itemKet) && trim($itemKet) !== '';

        if ($hasJumlah || $hasItemKet) {
            foreach ($model->details()->get() as $detail) {
                if ($hasJumlah) {
                    $detail->jumlah = mt_rand((int) $min, (int) $max);
                }

                if ($hasItemKet) {
                    $detail->keterangan = trim($itemKet);
                }

                $detail->save();
            }
        }

        // induk
        $kondisi = $std['kondisi_mobil'] ?? null;
        if (is_array($kondisi) && !empty($kondisi)) {
            $model->kondisi_mobil = array_values($kondisi);
        }

        $umum = $std['keterangan_umum'] ?? null;
        if (is_string($umum) && trim($umum) !== '') {
            $model->keterangan_umum = trim($umum);
        }

        $total = $std['keterangan_total'] ?? null;
        if (is_string($total) && trim($total) !== '') {
            $model->keterangan_total = trim($total);
        }

        $model->save();
    }

    /**
     * Menampilkan form create.
     */
    public function create()
    {
        $user = Auth::user();
        $produks = \App\Models\Produk::where('plant', $user->plant)->get();
        return view('loading-produks.create', compact('user', 'produks'));
    }

    /**
     * Menyimpan data baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|in:Pagi,Malam,Shift 1,Shift 2,Shift 3',
            'jenis_aktivitas' => 'required|string|max:255',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'no_pol_mobil' => 'required|string|max:20',
            'nama_supir' => 'required|string|max:255',
            'ekspedisi' => 'required|string|max:255',
            'tujuan_asal' => 'required|string|max:255',
            'no_segel' => 'nullable|string|max:100',
            'jenis_kendaraan' => 'nullable|string|max:100',
            'kondisi_mobil' => 'nullable|array',
            'keterangan_total' => 'nullable|string',
            'keterangan_umum' => 'nullable|string',
            'pic_qc' => 'nullable|string|max:255',
            'pic_warehouse' => 'nullable|string|max:255',
            'pic_qc_spv' => 'nullable|string|max:255',
            'details' => 'required|array|min:1',
            'details.*.nama_produk' => 'required|string|max:255',
            'details.*.kode_produksi' => 'required|string|max:100',
            'details.*.kode_expired' => 'nullable|date',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.keterangan' => 'nullable|string|max:255',
            'details.*.satuan' => 'required|string|max:50',
        ]);

        // ✅ VALIDASI JAM (HANDLE LEWAT TENGAH MALAM)
        //$mulai = Carbon::createFromFormat('H:i', $request->jam_mulai);
        //  $selesai = Carbon::createFromFormat('H:i', $request->jam_selesai);

        // Jika shift malam dan jam selesai < jam mulai → berarti lewat tengah malam
        //  if ($request->shift === 'Malam' && $selesai->lt($mulai)) {
        //      $selesai->addDay();
        //  }

        // Validasi final
        //  if ($selesai->lte($mulai)) {
        //      return back()->withErrors([
        //           'jam_selesai' => 'Jam selesai harus setelah jam mulai.'
        //       ])->withInput();
        //  }

        DB::beginTransaction();

        try {
            $loadingProdukData = Arr::except($validatedData, ['details']);
            $loadingProduk = LoadingProduk::create($loadingProdukData);

            foreach ($validatedData['details'] as $detail) {
                $loadingProduk->details()->create($detail);
            }

            DB::commit();

            return redirect()->route('loading-produks.index')
                ->with('success', 'Data pemeriksaan loading berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving loading check: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.')
                ->withInput();
        }
    }


    /**
     * Menampilkan detail.
     * $loadingProduk di-inject otomatis menggunakan 'uuid' berkat getRouteKeyName()
     */
    public function show(LoadingProduk $loadingProduk)
    {
        $user = Auth::user();
        $loadingProduk->load('details', 'creator');
        return view('loading-produks.show', compact('loadingProduk', 'user'));
    }

    /**
     * Menampilkan form edit.
     * $loadingProduk di-inject otomatis menggunakan 'uuid'
     */
    public function edit(LoadingProduk $loadingProduk)
    {
        $user = Auth::user();
        $loadingProduk->load('details');
        $produks = \App\Models\Produk::where('plant', $user->plant)->get();
        return view('loading-produks.edit', compact('loadingProduk', 'user', 'produks'));
    }

    /**
     * Update data.
     */
    public function update(Request $request, LoadingProduk $loadingProduk)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|in:Pagi,Malam,Shift 1,Shift 2,Shift 3',
            'jenis_aktivitas' => 'required|string|max:255',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'no_pol_mobil' => 'required|string|max:20',
            'nama_supir' => 'required|string|max:255',
            'ekspedisi' => 'required|string|max:255',
            'tujuan_asal' => 'required|string|max:255',
            'no_segel' => 'nullable|string|max:100',
            'jenis_kendaraan' => 'nullable|string|max:100',
            'kondisi_mobil' => 'nullable|array',
            'keterangan_total' => 'nullable|string',
            'keterangan_umum' => 'nullable|string',
            'pic_qc' => 'nullable|string|max:255',
            'pic_warehouse' => 'nullable|string|max:255',
            'pic_qc_spv' => 'nullable|string|max:255',
            'details' => 'required|array|min:1',
            'details.*.nama_produk' => 'required|string|max:255',
            'details.*.kode_produksi' => 'required|string|max:100',
            'details.*.kode_expired' => 'nullable|date',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.keterangan' => 'nullable|string|max:255',
            'details.*.satuan' => 'required|string|max:50',
            'details.*.uuid' => 'nullable|uuid',
        ]);

        // ✅ VALIDASI JAM (HANDLE LEWAT TENGAH MALAM)
        // $mulai = Carbon::createFromFormat('H:i', $request->jam_mulai);
        // $selesai = Carbon::createFromFormat('H:i', $request->jam_selesai);

        // Jika shift malam dan jam selesai < jam mulai → berarti lewat tengah malam
        // if ($request->shift === 'Malam' && $selesai->lt($mulai)) {
        //     $selesai->addDay();
        // }

        // Validasi final
        // if ($selesai->lte($mulai)) {
        //     return back()->withErrors([
        //         'jam_selesai' => 'Jam selesai harus setelah jam mulai.'
        //     ])->withInput();
        // }

        DB::beginTransaction();
        try {
            $loadingProduk->update(Arr::except($validatedData, ['details']));

            $existingUuids = collect($validatedData['details'])
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $loadingProduk->details()
                ->whereNotIn('uuid', $existingUuids)
                ->delete();

            foreach ($validatedData['details'] as $detail) {
                if (!empty($detail['uuid'])) {
                    $loadingProduk->details()
                        ->where('uuid', $detail['uuid'])
                        ->firstOrFail()
                        ->update(Arr::except($detail, ['uuid']));
                } else {
                    $loadingProduk->details()->create($detail);
                }
            }

            DB::commit();

            // --- PERBAIKAN DI SINI ---
            // Arahkan redirect ke route 'show' menggunakan UUID, bukan ID.
            return redirect()->route('loading-produks.index', $loadingProduk->uuid)
                ->with('success', 'Data pemeriksaan loading berhasil diperbarui.');
            // --- BATAS PERBAIKAN ---

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating loading check: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.')->withInput();
        }
    }

    /**
     * Hapus data (Soft Delete).
     * $loadingProduk di-inject otomatis menggunakan 'uuid'
     */
    public function destroy(LoadingProduk $loadingProduk)
    {
        try {
            $loadingProduk->delete();
            return redirect()->route('loading-produks.index')
                ->with('success', 'Data pemeriksaan loading berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function recyclebin()
    {
        $loadingProduk = LoadingProduk::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('loading-produks.recyclebin', compact('loadingProduk'));
    }

    public function restore($uuid)
    {
        $loadingProduk = LoadingProduk::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $loadingProduk->restore();

        return redirect()->route('loading-produks.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }

    public function deletePermanent($uuid)
    {
        $loadingProduk = LoadingProduk::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $loadingProduk->forceDelete();

        return redirect()->route('loading-produks.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function showVerification(Request $request)
    {
        $query = LoadingProduk::with('creator');

        if ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pol_mobil', 'like', "%{$search}%")
                    ->orWhere('nama_supir', 'like', "%{$search}%")
                    ->orWhere('ekspedisi', 'like', "%{$search}%");
            });
        }

        $produks = $query->orderByRaw('status_spv = 0 DESC, status_spv = 2 DESC, tanggal DESC')
            ->paginate(15)
            ->withQueryString();

        return view('loading-produks.verification', [
            'data' => $produks
        ]);
    }

    public function verify(Request $request, $uuid)
    {
        $validated = $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:1000',
        ]);

        try {
            // 1. Cari data berdasarkan UUID
            $loadingProduk = LoadingProduk::where('uuid', $uuid)->firstOrFail();

            // 2. Set status
            $loadingProduk->status_spv = $validated['status_spv'];

            // 3. Set catatan (kosongkan jika 'Verified')
            $loadingProduk->catatan_spv = ($validated['status_spv'] == 2) ? $validated['catatan_spv'] : null;

            // 4. Menyimpan ke kolom yang benar (sesuai gambar Anda)
            // Asumsi Auth::id() mengembalikan UUID user (d63c...)
            $loadingProduk->verified_by = Auth::id();
            $loadingProduk->verified_at = now();

            // 5. Simpan ke database
            $loadingProduk->save();

            return redirect()->route('loading-produks.index', [
                'page'       => $request->input('page', 1),
                'search'     => $request->input('search'),
                'start_date' => $request->input('start_date'),
                'end_date'   => $request->input('end_date'),
            ])->with('success', "Data (Nopol: {$loadingProduk->no_pol_mobil}) berhasil diverifikasi.");
        } catch (\Exception $e) {
            Log::error('Gagal verifikasi Loading Produk: ' . $e->getMessage());
            return back()->with('error', 'Gagal memverifikasi data. Error: ' . $e->getMessage());
        }
    }

    public function updateDetails(LoadingProduk $loadingProduk)
    {
        // Pastikan relasi details dimuat
        $user = Auth::user();
        $loadingProduk->load('details');
        $produks = \App\Models\Produk::where('plant', $user->plant)->get();
        return view('loading-produks.update-details', compact('loadingProduk', 'produks'));
    }

    public function kodeProduksi(LoadingProduk $loadingProduk)
    {
        $loadingProduk->load('details');

        $kodeProduksi = $loadingProduk->details
            ->map(function ($detail) {
                if (\Illuminate\Support\Str::isUuid($detail->kode_produksi)) {
                    return \App\Models\Mincing::where('uuid', $detail->kode_produksi)
                        ->value('kode_produksi') ?? $detail->kode_produksi;
                }

                return $detail->kode_produksi;
            })
            ->filter()
            ->unique()
            ->values();

        return response()->json($kodeProduksi);
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date = $request->input('date');
        $shift = $request->input('shift');
        $userPlant = Auth::user()->plant;
        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Loading-Unloading Produk')
            ->value('no_dokumen');


        $query = LoadingProduk::with(['details', 'creator']);
        if (Auth::check() && !empty($userPlant)) {
            $query->where('plant_uuid', $userPlant);
        }

        // Filter tanggal dan shift
        $query->when($date, function ($q) use ($date) {
            $q->whereDate('tanggal', $date);
        });

        $query->when($shift, function ($q) use ($shift) {
            $q->where('shift', $shift);
        });

        $loadings = $query->orderBy('tanggal', 'asc')->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pemeriksaan Loading Unloading Produk');

        // Hilangkan Header/Footer Bawaan
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set Margin
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);

        // Set Font Default
        $pdf->SetFont('helvetica', '', 6);

        $pdf->AddPage();

        $header = $loadings->first();

        // 3. Render
        $html = view('reports.pemeriksaan-loading-unloading', compact('loadings', 'header', 'request', 'noDokumen'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Laporan_Stuffing_' .
            \Carbon\Carbon::parse($date)->format('d-m-Y') .
            '_Shift' . $shift .
            '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
