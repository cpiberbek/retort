<?php

// app/HttpControllers/RawMaterialInspectionController.php

namespace App\Http\Controllers;

use App\Models\RawMaterialInspection;
use App\Models\Master_Raw_Material;
use App\Models\List_form;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RawMaterialInspectionController extends Controller
{
    // Daftar field boolean (dari tombol OK/Not OK)
    private const LAPORAN_BAHAN = 'Pemeriksaan Kedatangan Bahan Baku dan Bahan Kimia';

    private const AC_BOOL = [
        'mobil_check_warna', 'mobil_check_kotoran', 'mobil_check_aroma',
        'mobil_check_kemasan', 'analisa_logo_halal', 'dokumen_halal_berlaku',
    ];
    
    private $booleanFields = [
        'mobil_check_warna',
        'mobil_check_kotoran',
        'mobil_check_aroma',
        'mobil_check_kemasan',
        'analisa_logo_halal',
        'dokumen_halal_berlaku'
    ];

    public function index(Request $request)
    {
        if (Auth::user()->isAuditView()) {
            return redirect()->route('inspections.audit');
        }

        $query = RawMaterialInspection::with(['creator', 'updater'])
            ->where('is_audit', false);

        if (Auth::check() && !empty(Auth::user()->plant)) {
            $query->where('plant_uuid', Auth::user()->plant);
        }

        $query->when($request->filled('date'), function ($q) use ($request) {
            $q->whereDate('setup_kedatangan', $request->date);
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('bahan_baku', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhere('do_po', 'like', "%{$search}%");
            });
        });

        $inspections = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('raw_material.IndexRawMaterial', compact('inspections'));
    }

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('inspections.index');
        }

        $auditedSourceUuids = RawMaterialInspection::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = RawMaterialInspection::with(['creator', 'updater', 'auditVersion'])
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

        $query->when($request->filled('date'), function ($q) use ($request) {
            $q->whereDate('setup_kedatangan', $request->date);
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('bahan_baku', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhere('do_po', 'like', "%{$search}%");
            });
        });

        $inspections = $query
            ->orderByRaw('COALESCE((SELECT original.setup_kedatangan FROM raw_material_inspections AS original WHERE original.uuid = raw_material_inspections.source_uuid LIMIT 1), raw_material_inspections.setup_kedatangan) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM raw_material_inspections AS original WHERE original.uuid = raw_material_inspections.source_uuid LIMIT 1), raw_material_inspections.created_at) DESC')
            ->paginate(10)
            ->withQueryString();

        return view('raw_material.IndexAuditRawMaterial', compact('inspections'));
    }

    public function duplicateToAudit(string $uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $row = RawMaterialInspection::where('uuid', $uuid)
            ->where('plant_uuid', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $row->auditVersion) {
            return redirect()->route('inspections.edit', $existing->uuid);
        }

        $clone = $this->createCorrectedAudit($row);

        return redirect()->route('inspections.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    private function createCorrectedAudit(RawMaterialInspection $row): RawMaterialInspection
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
            ->where('laporan', self::LAPORAN_BAHAN)
            ->first();

        abort_if(empty($master?->standar_ref), 422, 'Master auto correct belum diterapkan untuk plant ini.');

        $base = RawMaterialInspection::where('plant_uuid', $plant)
            ->where('is_audit', false)
            ->whereDate('setup_kedatangan', '>=', $start)
            ->whereDate('setup_kedatangan', '<=', $end);

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
            return redirect()->route('inspections.audit')->with('warning', 'Tidak ada entry di tanggal ini.');
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
            return redirect()->route('inspections.audit')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi dan disesuaikan sesuai master. {$skipped} dilewati karena sudah ada.";
        if ($failed) $msg .= " {$failed} gagal diproses (cek log).";

        return redirect()->route('inspections.audit')->with($failed ? 'warning' : 'success', $msg);
    }

    private function autoCorrectFromMaster(RawMaterialInspection $model): void
    {
        $master = List_form::where('plant', $model->plant_uuid)
            ->where('laporan', self::LAPORAN_BAHAN)
            ->first();

        $std = $master?->standar_ref;
        if (is_string($std)) $std = json_decode($std, true);
        if (!is_array($std) || empty($std)) return;

        $range = function (string $key) use ($std) {
            $r = $std[$key] ?? null;
            if (!is_array($r)) return null;

            $min = $r['min'] ?? null;
            $max = $r['max'] ?? null;
            if ($min === null || $min === '' || $max === null || $max === '') return null;

            return [(float) $min, (float) $max];
        };

        $rand = fn (array $r) => round(
            mt_rand((int) round($r[0] * 100), (int) round($r[1] * 100)) / 100,
            2
        );

        foreach (['suhu_mobil', 'suhu_daging'] as $col) {
            if ($r = $range($col)) {
                $model->$col = $rand($r);
            }
        }

        if ($r = $range('analisa_ka_ffa')) {
            $model->analisa_ka_ffa = number_format($rand($r), 2, '.', '');
        }

        foreach (self::AC_BOOL as $col) {
            $rule = $std[$col] ?? null;
            if (in_array((string) $rule, ['0', '1'], true)) {
                $model->$col = (bool) (int) $rule;
            }
        }

        $kondisi = $std['kondisi_mobil'] ?? null;
        if (is_array($kondisi) && !empty($kondisi)) {
            $model->kondisi_mobil = implode(',', $kondisi);
        }

        $keterangan = $std['keterangan'] ?? null;
        if (is_string($keterangan) && trim($keterangan) !== '') {
            $model->keterangan = trim($keterangan);
        }

        $model->save();

        $rSampel = $range('jumlah_sampel');
        $rReject = $range('jumlah_reject');

        if ($rSampel || $rReject) {
            foreach ($model->productDetails()->get() as $detail) {
                if ($rSampel) $detail->jumlah_sampel = $rand($rSampel);
                if ($rReject) $detail->jumlah_reject = $rand($rReject);
                $detail->save();
            }
        }
    }

    public function create()
    {
        // Ambil data master bahan baku sesuai dengan plant user yang login
        $masterBahanBaku = Master_Raw_Material::where('plant_uuid', Auth::user()->plant)
            ->orderBy('nama_bahan_baku', 'asc')
            ->get();

        // Ambil data supplier berdasarkan plant user yang login
        $suppliers = Supplier::where('plant', Auth::user()->plant)
            ->orderBy('nama_supplier', 'asc')
            ->get();

        // Kirim data ($masterBahanBaku dan $suppliers) ke view
        return view('raw_material.CreateRawMaterial', compact('masterBahanBaku', 'suppliers'));
    }

    public function store(Request $request)
    {
        // PERUBAHAN: Menambahkan validasi untuk boolean fields
        $booleanFieldRule = 'required|in:0,1';

        $validationRules = [
            'setup_kedatangan' => 'required|date',
            'do_po' => 'required|string|max:255',
            'bahan_baku' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'nopol_mobil' => 'required|string|max:255',
            'suhu_mobil' => ['nullable', 'regex:/^-?\d+(\.\d+)?$/'],
            'kondisi_mobil' => 'required|array|min:1',
            'kondisi_mobil.*' => 'string',
            'analisa_ka_ffa' => ['nullable', 'regex:/^(?:-|\d+(?:\.\d+)?)$/'],
            'no_segel' => 'required|string|max:255',
            'suhu_daging' => ['nullable', 'regex:/^-?\d+(\.\d+)?$/'],
            'analisa_negara_asal' => 'required|string|max:255',
            'analisa_produsen' => 'required|string|max:255',
            'dokumen_halal_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'dokumen_coa_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',

            'details' => 'required|array|min:1',
            'details.*.kode_batch' => 'required|string',
            'details.*.exp' => 'required|date',
            'details.*.jumlah' => 'required|numeric|min:0',
            'details.*.jumlah_sampel' => 'required|numeric|min:0',
            'details.*.jumlah_reject' => 'required|numeric|min:0',
        ];

        // Tambahkan aturan validasi untuk semua boolean field
        foreach ($this->booleanFields as $field) {
            $validationRules[$field] = $booleanFieldRule;
        }

        // Ubah '-' menjadi null sebelum validasi agar lolos regex (yang butuh digit)
        $request->merge([
            'suhu_mobil' => $request->suhu_mobil === '-' ? null : $request->suhu_mobil,
            'suhu_daging' => $request->suhu_daging === '-' ? null : $request->suhu_daging,
        ]);

        $request->validate($validationRules);

        DB::beginTransaction();
        try {
            $data = $request->except(['_token', 'details', 'dokumen_halal_file', 'dokumen_coa_file']);
            $data['suhu_mobil'] = $request->suhu_mobil === '-'
                ? null
                : $request->suhu_mobil;

            $data['suhu_daging'] = $request->suhu_daging === '-'
                ? null
                : $request->suhu_daging;
            if ($request->has('kondisi_mobil')) {
                $data['kondisi_mobil'] = implode(',', $request->kondisi_mobil);
            }
            $user = Auth::user();
            $data['plant_uuid'] = $user->plant; // Ambil dari user yang login
            $data['updated_by'] = $user->uuid;  // Updated pertama = Creator
            // PERUBAHAN: Handle boolean (OK/Not OK) fields
            // Konversi '1' (OK) menjadi true, '0' (Not OK) menjadi false
            foreach ($this->booleanFields as $field) {
                $data[$field] = $request->input($field) == '1';
            }

            // Handle file upload
            if ($request->hasFile('dokumen_halal_file')) {
                $data['dokumen_halal_file'] = $request->file('dokumen_halal_file')->store('halal_docs', 'public');
            }
            if ($request->hasFile('dokumen_coa_file')) {
                $data['dokumen_coa_file'] = $request->file('dokumen_coa_file')->store('coa_docs', 'public');
            }

            $inspection = RawMaterialInspection::create($data);

            if ($request->has('details')) {
                $inspection->productDetails()->createMany($request->details);
            }

            DB::commit();
            return redirect()->route('inspections.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(RawMaterialInspection $inspection)
    {
        // Load relasi productDetails agar bisa di-loop di view
        // $inspection sudah otomatis di-fetch berdasarkan 'uuid'
        $inspection->load('productDetails', 'creator');

        // Arahkan ke view baru untuk 'show'
        return view('raw_material.ShowRawMaterial', compact('inspection'));
    }

    public function edit(RawMaterialInspection $inspection)
    {
        // Load relasi productDetails
        $inspection->load('productDetails', 'creator');

        // Ambil data master bahan baku sesuai dengan plant user yang login
        $masterBahanBaku = Master_Raw_Material::where('plant_uuid', Auth::user()->plant)
            ->orderBy('nama_bahan_baku', 'asc')
            ->get();

        // Ambil data supplier
        $suppliers = Supplier::where('plant', Auth::user()->plant)
            ->orderBy('nama_supplier', 'asc')
            ->get();
        // Pastikan variabel $masterBahanBaku dan $suppliers ikut dikirim ke dalam fungsi compact()
        return view('raw_material.EditRawMaterial', compact('inspection', 'masterBahanBaku', 'suppliers'));
    }

    /**
     * Mengupdate data inspeksi yang ada di database.
     */
    public function update(Request $request, RawMaterialInspection $inspection)
    {
        // Aturan validasi untuk tombol OK/Not OK
        $booleanFieldRule = 'required|in:0,1';

        // Validasi lengkap untuk semua field
        $validationRules = [
            'setup_kedatangan' => 'required|date',
            'do_po' => 'required|string|max:255',
            'bahan_baku' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'nopol_mobil' => 'required|string|max:255',
            'suhu_mobil' => ['nullable', 'regex:/^-?\d+(\.\d+)?$/'],
            'analisa_ka_ffa' => ['nullable', 'regex:/^(?:-|\d+(?:\.\d+)?)$/'],
            'kondisi_mobil' => 'required|array|min:1',
            'kondisi_mobil.*' => 'string',
            'no_segel' => 'required|string|max:255',
            'suhu_daging' => ['nullable', 'regex:/^-?\d+(\.\d+)?$/'],
            'analisa_negara_asal' => 'required|string|max:255',
            'analisa_produsen' => 'required|string|max:255',
            'keterangan' => 'nullable|string',

            // Validasi file: nullable agar tidak wajib diisi ulang
            'dokumen_halal_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'dokumen_coa_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',

            // Validasi untuk detail produk (repeatable items)
            'details' => 'required|array|min:1',
            'details.*.kode_batch' => 'required|string',
            'details.*.exp' => 'required|date',
            'details.*.jumlah' => 'required|numeric|min:0',
            'details.*.jumlah_sampel' => 'required|numeric|min:0',
            'details.*.jumlah_reject' => 'required|numeric|min:0',
        ];

        // Tambahkan aturan validasi untuk semua boolean field
        // $this->booleanFields harus didefinisikan di atas (di dalam class)
        foreach ($this->booleanFields as $field) {
            $validationRules[$field] = $booleanFieldRule;
        }

        // Ubah '-' menjadi null sebelum validasi agar lolos regex (yang butuh digit)
        $request->merge([
            'suhu_mobil' => $request->suhu_mobil === '-' ? null : $request->suhu_mobil,
            'suhu_daging' => $request->suhu_daging === '-' ? null : $request->suhu_daging,
        ]);

        $request->validate($validationRules);

        DB::beginTransaction();
        try {
            // Ambil semua data kecuali token, method, details, dan file
            $data = $request->except([
                '_token',
                '_method',
                'details',
                'dokumen_halal_file',
                'dokumen_coa_file'
            ]);

            $data['suhu_mobil'] = $request->suhu_mobil === '-'
                ? null
                : $request->suhu_mobil;

            $data['suhu_daging'] = $request->suhu_daging === '-'
                ? null
                : $request->suhu_daging;
            if ($request->has('kondisi_mobil')) {
                $data['kondisi_mobil'] = implode(',', $request->kondisi_mobil);
            }
            $data['updated_by'] = Auth::user()->uuid;
            // Handle boolean (OK/Not OK) fields
            // Konversi '1' (OK) menjadi true, '0' (Not OK) menjadi false
            foreach ($this->booleanFields as $field) {
                $data[$field] = $request->input($field) == '1';
            }

            // Handle file upload jika ada file baru
            if ($request->hasFile('dokumen_halal_file')) {
                // Hapus file lama jika ada
                if ($inspection->dokumen_halal_file) {
                    Storage::disk('public')->delete($inspection->dokumen_halal_file);
                }
                // Simpan file baru
                $data['dokumen_halal_file'] = $request->file('dokumen_halal_file')->store('halal_docs', 'public');
            }

            if ($request->hasFile('dokumen_coa_file')) {
                // Hapus file lama jika ada
                if ($inspection->dokumen_coa_file) {
                    Storage::disk('public')->delete($inspection->dokumen_coa_file);
                }
                // Simpan file baru
                $data['dokumen_coa_file'] = $request->file('dokumen_coa_file')->store('coa_docs', 'public');
            }

            // Update data utama di tabel 'raw_material_inspections'
            $inspection->update($data);

            // Hapus detail lama dan buat yang baru (strategi paling sederhana)
            $inspection->productDetails()->delete();

            if ($request->has('details')) {
                // Buat ulang product details
                // Eloquent akan otomatis mengisi 'raw_material_inspection_uuid'
                // berdasarkan relasi yang sudah kita definisikan di Model
                $inspection->productDetails()->createMany($request->details);
            }

            DB::commit();

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('inspections.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Jika terjadi error, kembali ke form edit dengan pesan error dan input lama
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(RawMaterialInspection $inspection)
    {
        try {
            DB::beginTransaction();

            if ($inspection->dokumen_halal_file) {
                Storage::disk('public')->delete($inspection->dokumen_halal_file);
            }
            if ($inspection->dokumen_coa_file) {
                Storage::disk('public')->delete($inspection->dokumen_coa_file);
            }

            $inspection->productDetails()->delete();
            $inspection->delete();

            DB::commit();
            return redirect()->route('inspections.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function recyclebin()
    {
        $inspection = RawMaterialInspection::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.raw_material.recyclebin', compact('inspection'));
    }
    public function restore($uuid)
    {
        $inspection = RawMaterialInspection::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $inspection->restore();

        return redirect()->route('raw_material.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $inspection = RawMaterialInspection::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $inspection->forceDelete();

        return redirect()->route('raw_material.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    // ===================================================================
    // TAMBAHKAN DUA FUNGSI BARU DI BAWAH INI
    // ===================================================================

    /**
     * Menampilkan halaman verifikasi untuk SPV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showVerificationPage(Request $request)
    {
        // 1. Mulai query builder
        $query = RawMaterialInspection::with('creator');
        if (Auth::check() && !empty(Auth::user()->plant)) {
            $query->where('plant_uuid', Auth::user()->plant);
        }

        // 2. Terapkan filter tanggal awal (start_date)
        if ($request->filled('start_date')) {
            // Menggunakan kolom tanggal 'setup_kedatangan'
            $query->whereDate('setup_kedatangan', '>=', $request->input('start_date'));
        }

        // 3. Terapkan filter tanggal akhir (end_date)
        if ($request->filled('end_date')) {
            // Menggunakan kolom tanggal 'setup_kedatangan'
            $query->whereDate('setup_kedatangan', '<=', $request->input('end_date'));
        }

        // 4. Terapkan filter pencarian jika ada
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            // Menggunakan kolom 'bahan_baku' dan 'supplier'
            $query->where(function ($q) use ($searchTerm) {
                $q->where('bahan_baku', 'like', '%' . $searchTerm . '%')
                    ->orWhere('supplier', 'like', '%' . $searchTerm . '%');
            });
        }

        // 5. Eksekusi query dengan urutan terbaru, paginasi, dan tambahkan parameter filter
        $data = $query->latest() // Mengurutkan dari yang terbaru
            ->paginate(10)
            ->withQueryString(); // <-- Penting agar filter tetap aktif saat pindah halaman

        // 6. Kirim data ke view verifikasi yang baru
        return view('raw_material.verification', compact('data'));
    }

    /**
     * Handle the SPV verification update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2', // 1 = Verified, 2 = Revision
            'catatan_spv' => 'nullable|string|max:1000',
        ]);

        // Cari record berdasarkan kolom 'uuid'
        $inspection = RawMaterialInspection::where('uuid', $uuid)->firstOrFail();

        $inspection->status_spv = $request->status_spv;

        // Hanya simpan catatan jika statusnya adalah 'Revision', jika tidak, kosongkan.
        $inspection->catatan_spv = ($request->status_spv == 2) ? $request->catatan_spv : null;

        $inspection->verified_by_spv_uuid = Auth::id(); // Asumsi user yang login adalah SPV
        $inspection->verified_at_spv = now();

        $inspection->save();

        return redirect()->route('inspections.index', [
            'page'       => $request->input('page', 1),
            'search'     => $request->input('search'),
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ])->with('success', 'Data berhasil diverifikasi.');
    }

    public function showUpdateForm(RawMaterialInspection $inspection)
    {
        // Load relasi productDetails agar bisa ditampilkan di JS
        $inspection->load('productDetails');

        // Tambahkan juga data master bahan baku di sini
        // agar dropdown bisa me-load data jika view ini menggunakannya
        $masterBahanBaku = Master_Raw_Material::where('plant_uuid', Auth::user()->plant)
            ->orderBy('nama_bahan_baku', 'asc')
            ->get();

        $suppliers = Supplier::where('plant', Auth::user()->plant)
            ->orderBy('nama_supplier', 'asc')
            ->get();

        // Arahkan ke blade baru: resources/views/raw_material/UpdateRawMaterial.blade.php
        return view('raw_material.UpdateRawMaterial', compact('inspection', 'masterBahanBaku', 'suppliers'));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;

        $query = RawMaterialInspection::with(['creator', 'productDetails']);
        if (Auth::check() && !empty($userPlant)) {
            $query->where('plant_uuid', $userPlant);
        }

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Kedatangan Bahan Baku dan Bahan Kimia')
            ->value('no_dokumen');

        // Filter tanggal (menggunakan setup_kedatangan)
        $query->when($date, function ($q) use ($date) {
            $q->whereDate('setup_kedatangan', $date);
        });

        $items = $query->orderBy('setup_kedatangan', 'asc')->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pemeriksaan Input Bahan Baku');

        // Hilangkan Header/Footer Bawaan
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set Margin
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);

        // Set Font Default
        $pdf->SetFont('helvetica', '', 6);

        $pdf->AddPage();

        // 3. Render
        $html = view('reports.pemeriksaan-input-bahan-baku', compact('items', 'request', 'noDokumen'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Pemeriksaan_Input_Bahan_Baku_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
