<?php

namespace App\Http\Controllers;

use App\Models\PackagingInspection;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Untuk error logging
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use TCPDF;
use Carbon\Carbon;
use App\Models\List_form;

class PackagingInspectionController extends Controller
{
    private const LAPORAN_PACKAGING = 'Pemeriksaan Kedatangan Bahan Kemas';

    public function index(Request $request): View|RedirectResponse
    {
        if (Auth::user()->isAuditView()) {
            return redirect()->route('packaging-inspections.audit');
        }

        $query = PackagingInspection::with('items')
            ->where('plant_uuid', auth()->user()->plant)
            ->where('is_audit', false);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('shift', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('uuid', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('items', function ($q) use ($searchTerm) {
                        $q->where('packaging_type', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('inspection_date', $request->input('start_date'));
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->input('shift'));
        }

        $inspections = $query->latest('inspection_date')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('packaging_inspections.index', compact('inspections'));
    }

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('packaging-inspections.index');
        }

        $auditedSourceUuids = PackagingInspection::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = PackagingInspection::with(['items', 'auditVersion'])
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

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('shift', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('uuid', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('items', function ($q) use ($searchTerm) {
                        $q->where('packaging_type', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('inspection_date', $request->input('start_date'));
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->input('shift'));
        }

        $inspections = $query
            ->orderByRaw('COALESCE((SELECT original.inspection_date FROM packaging_inspections AS original WHERE original.uuid = packaging_inspections.source_uuid LIMIT 1), packaging_inspections.inspection_date) DESC')
            ->orderByRaw('CAST(COALESCE((SELECT original.shift FROM packaging_inspections AS original WHERE original.uuid = packaging_inspections.source_uuid LIMIT 1), packaging_inspections.shift) AS UNSIGNED) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM packaging_inspections AS original WHERE original.uuid = packaging_inspections.source_uuid LIMIT 1), packaging_inspections.created_at) DESC')
            ->paginate(10)
            ->withQueryString();

        return view('packaging_inspections.index_audit', compact('inspections'));
    }

    public function duplicateToAudit(string $uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $row = PackagingInspection::where('uuid', $uuid)
            ->where('plant_uuid', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $row->auditVersion) {
            return redirect()->route('packaging-inspections.edit', $existing->uuid);
        }

        $clone = $this->createCorrectedAudit($row);

        return redirect()->route('packaging-inspections.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    private function createCorrectedAudit(PackagingInspection $row): PackagingInspection
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
            ->where('laporan', self::LAPORAN_PACKAGING)
            ->first();

        abort_if(empty($master?->standar_ref), 422, 'Master auto correct belum diterapkan untuk plant ini.');

        $base = PackagingInspection::where('plant_uuid', $plant)
            ->where('is_audit', false)
            ->whereDate('inspection_date', '>=', $start)
            ->whereDate('inspection_date', '<=', $end);

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
            return redirect()->route('packaging-inspections.audit')->with('warning', 'Tidak ada entry di tanggal ini.');
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
            return redirect()->route('packaging-inspections.audit')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi dan disesuaikan sesuai master. {$skipped} dilewati karena sudah ada.";
        if ($failed) $msg .= " {$failed} gagal diproses (cek log).";

        return redirect()->route('packaging-inspections.audit')->with($failed ? 'warning' : 'success', $msg);
    }

    private function autoCorrectFromMaster(PackagingInspection $model): void
    {
        $master = List_form::where('plant', $model->plant_uuid)
            ->where('laporan', self::LAPORAN_PACKAGING)
            ->first();

        $std = $master?->standar_ref;
        if (is_string($std)) $std = json_decode($std, true);
        if (!is_array($std) || empty($std)) return;

        $range = function ($rule) {
            if (!is_array($rule)) return null;
            $min = $rule['min'] ?? null;
            $max = $rule['max'] ?? null;
            if ($min === null || $min === '' || $max === null || $max === '') return null;
            return [(float) $min, (float) $max];
        };

        foreach ($model->items()->get() as $item) {
            if ($r = $range($std['condition_weight'] ?? null)) {
                $item->condition_weight = round(mt_rand((int) round($r[0] * 100), (int) round($r[1] * 100)) / 100, 2);
            }

            foreach (['quantity_goods', 'quantity_sample', 'quantity_reject'] as $col) {
                if ($r = $range($std[$col] ?? null)) {
                    $item->$col = mt_rand((int) $r[0], (int) $r[1]);
                }
            }

            foreach (['condition_design', 'condition_sealing', 'condition_color', 'acceptance_status', 'condition_dimension', 'notes'] as $col) {
                $rule = $std[$col] ?? null;
                if (is_string($rule) && trim($rule) !== '') {
                    $item->$col = $rule;
                }
            }

            $vehicle = $std['vehicle_condition'] ?? null;
            if (is_array($vehicle) && !empty($vehicle)) {
                $item->vehicle_condition = implode(',', $vehicle);
            }

            $item->save();
        }
    }

    /**
     * Menampilkan form untuk membuat inspeksi baru.
     */
    public function create(): View
    {
        // Opsi untuk dropdown KONDISI KENDARAAN (sekarang dipakai di level item)
        $vehicleConditions = ['Bersih', 'Kotor', 'Bau', 'Bocor', 'Basah', 'Kering', 'Bebas Hama'];
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        return view('packaging_inspections.create', compact('vehicleConditions', 'suppliers'));
    }
    /**
     * Menyimpan inspeksi baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedHeader = $request->validate([
            'inspection_date' => 'required|date',
            'shift'           => 'required|string|max:255',

            // Validasi Items
            'items'                       => 'required|array|min:1',
            'items.*.no_pol'              => 'required|string|max:255',
            'items.*.vehicle_condition'   => 'required|array|min:1',
            'items.*.pbb_op'              => 'nullable|string|max:255',
            'items.*.packaging_type'      => 'required|string|max:255',
            'items.*.supplier'            => 'required|string|max:255',
            'items.*.lot_batch'           => 'required|string|max:255',
            'items.*.condition_design'    => 'required|string|max:10',
            'items.*.condition_sealing'   => 'required|string|max:10',
            'items.*.condition_color'     => 'required|string|max:10',
            'items.*.condition_dimension' => 'nullable|string|max:255',
            'items.*.condition_weight'    => 'nullable|numeric|min:0',
            'items.*.condition_weight_pcs' => 'nullable|string|max:255',
            'items.*.quantity_goods'      => 'required|integer|min:0',
            'items.*.quantity_sample'     => 'required|integer|min:0',
            'items.*.quantity_reject'     => 'required|integer|min:0',
            'items.*.acceptance_status'   => 'required|in:OK,Tolak',
            'items.*.notes'               => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // --- LOGIKA UTAMA: DATA USER ---
            $user = Auth::user();

            $headerData = [
                'inspection_date' => $validatedHeader['inspection_date'],
                'shift'           => $validatedHeader['shift'],
                // Ambil 'plant' dari kolom tabel users
                'plant_uuid'      => $user->plant ?? null,
                // Simpan ID user (integer) ke kolom created_by/updated_by
                'created_by'      => $user->uuid,
                'updated_by'      => $user->uuid,
            ];

            // 1. Simpan Header
            $inspection = PackagingInspection::create($headerData);

            // 2. Simpan Items
            $itemsData = collect($validatedHeader['items'])->map(function ($item) use ($inspection) {
                if (isset($item['vehicle_condition']) && is_array($item['vehicle_condition'])) {
                    $item['vehicle_condition'] = implode(',', $item['vehicle_condition']);
                }

                $item['packaging_inspection_id'] = $inspection->id;
                return $item;
            });

            $inspection->items()->createMany($itemsData);

            DB::commit();

            return redirect()->route('packaging-inspections.index')
                ->with('success', 'Inspeksi packaging berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing packaging inspection: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail satu inspeksi.
     */
    public function show(PackagingInspection $packagingInspection): View
    {
        // Eager load items untuk efisiensi
        $packagingInspection->load('items');
        return view('packaging_inspections.show', compact('packagingInspection'));
    }

    /**
     * Menampilkan form untuk mengedit inspeksi.
     */
    public function edit(PackagingInspection $packagingInspection): View
    {
        $packagingInspection->load('items');
        $vehicleConditions = ['Bersih', 'Kotor', 'Bau', 'Bocor', 'Basah', 'Kering', 'Bebas Hama'];
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        return view('packaging_inspections.edit', compact('packagingInspection', 'vehicleConditions', 'suppliers'));
    }

    /**
     * Update data inspeksi di database.
     */
    public function update(Request $request, PackagingInspection $packagingInspection): RedirectResponse
    {
        $validatedData = $request->validate([
            'inspection_date' => 'required|date',
            'shift'           => 'required|string|max:255',
            'items'           => 'required|array|min:1',
            'items.*.id'      => 'nullable|integer|exists:packaging_inspection_items,id',
            // ... (copy validasi items lengkap dari store) ...
            'items.*.no_pol'              => 'required|string|max:255',
            'items.*.vehicle_condition'   => 'required|array|min:1',
            'items.*.pbb_op'              => 'nullable|string|max:255',
            'items.*.packaging_type'      => 'required|string|max:255',
            'items.*.supplier'            => 'required|string|max:255',
            'items.*.lot_batch'           => 'required|string|max:255',
            'items.*.condition_design'    => 'required|string|max:10',
            'items.*.condition_sealing'   => 'required|string|max:10',
            'items.*.condition_color'     => 'required|string|max:10',
            'items.*.condition_dimension' => 'nullable|string|max:255',
            'items.*.quantity_goods'      => 'required|integer|min:0',
            'items.*.quantity_sample'     => 'required|integer|min:0',
            'items.*.quantity_reject'     => 'required|integer|min:0',
            'items.*.acceptance_status'   => 'required|in:OK,Tolak',
            'items.*.notes'               => 'nullable|string',
            'items.*.condition_weight'    => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // --- LOGIKA UTAMA: UPDATE ---
            // Update updated_by dengan user yang sedang login
            $packagingInspection->update([
                'inspection_date' => $validatedData['inspection_date'],
                'shift'           => $validatedData['shift'],
                'updated_by'      => Auth::user()->uuid,
            ]);

            // Logic Update Items (Sama seperti sebelumnya)
            $incomingItemIds = [];
            foreach ($validatedData['items'] as $itemData) {

                if (isset($itemData['vehicle_condition']) && is_array($itemData['vehicle_condition'])) {
                    $itemData['vehicle_condition'] = implode(',', $itemData['vehicle_condition']);
                }

                if (isset($itemData['id']) && $itemData['id']) {
                    $item = $packagingInspection->items()->find($itemData['id']);
                    if ($item) {
                        $item->update($itemData);
                        $incomingItemIds[] = $item->id;
                    }
                } else {
                    $newItem = $packagingInspection->items()->create($itemData);
                    $incomingItemIds[] = $newItem->id;
                }
            }
            $packagingInspection->items()->whereNotIn('id', $incomingItemIds)->delete();

            DB::commit();

            return redirect()->route('packaging-inspections.index')
                ->with('success', 'Inspeksi packaging berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating packaging inspection: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus (Soft Delete) data inspeksi.
     */
    public function destroy(PackagingInspection $packagingInspection): RedirectResponse
    {
        try {
            // Soft delete akan otomatis men-trigger cascade soft delete (jika di-setting di model)
            // atau kita bisa hapus manual item-itemnya
            $packagingInspection->items()->delete(); // Soft delete semua items
            $packagingInspection->delete(); // Soft delete induk

            return redirect()->route('packaging-inspections.index')
                ->with('success', 'Inspeksi packaging berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting packaging inspection: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function recyclebin()
    {
        $packagingInspection = PackagingInspection::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('packaging_inspections.recyclebin', compact('packagingInspection'));
    }
    public function restore($uuid)
    {
        $packagingInspection = PackagingInspection::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $packagingInspection->restore();

        return redirect()->route('packaging_inspections.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $packagingInspection = PackagingInspection::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $packagingInspection->forceDelete();

        return redirect()->route('packaging_inspections.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function showVerificationList(Request $request): View
    {
        $query = PackagingInspection::query()->with('items'); // Eager load items

        // Terapkan filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('inspection_date', [$request->start_date, $request->end_date]);
        }

        // Terapkan filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Mencari di tabel header (packaging_inspections)
                $q->where('shift', 'like', "%{$search}%")
                    // Mencari di tabel relasi (packaging_inspection_items)
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where('no_pol', 'like', "%{$search}%")
                            ->orWhere('supplier', 'like', "%{$search}%")
                            ->orWhere('packaging_type', 'like', "%{$search}%");
                    });
            });
        }

        // Ambil data, urutkan dari terbaru, dan paginasi
        $inspections = $query->latest('inspection_date')->paginate(15)->withQueryString();

        // Mengirim data ke view 'packaging.verification'
        return view('packaging_inspections.verification', ['data' => $inspections]);
    }

    /**
     * Menyimpan hasil verifikasi dari modal.
     * (Menangani route 'packaging-inspections.verify')
     * @param string $uuid UUID dari inspeksi yang akan diverifikasi
     */
    public function verify(Request $request, PackagingInspection $inspection): RedirectResponse
    {
        // Validasi input dari modal
        $validated = $request->validate([
            'status_spv' => ['required', Rule::in([1, 2])],
            'catatan_spv' => [
                'nullable',
                'string',
                'required_if:status_spv,2',
            ],
        ]);

        // TIDAK PERLU LAGI MENCARI MANUAL:
        // $inspection = PackagingInspection::where('uuid', $uuid)->firstOrFail();
        // Variabel $inspection sudah berisi data yang benar dari URL.

        try {
            // Gunakan update() untuk mass assignment (lebih bersih)
            $inspection->update([
                'status_spv'    => $validated['status_spv'],
                'catatan_spv'   => $validated['catatan_spv'] ?? null,
                'verified_by' => Auth::user()->id,
                'verified_at'   => now(),
            ]);

            return redirect()->route('packaging-inspections.index', [
                'page'       => $request->input('page', 1),
                'search'     => $request->input('search'),
                'start_date' => $request->input('start_date'),
                'end_date'   => $request->input('end_date'),
            ])->with('success', 'Verifikasi packaging berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan verifikasi packaging: ' . $e->getMessage());
            // Tampilkan pesan error yang sebenarnya ke user
            return back()->with('error', 'GAGAL: ' . $e->getMessage());
        }
    }

    public function editForUpdate(PackagingInspection $packagingInspection): View
    {
        $packagingInspection->load('items');
        $vehicleConditions = ['Bersih', 'Kotor', 'Bau', 'Bocor', 'Basah', 'Kering', 'Bebas Hama'];
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        // Return ke view baru: packaging_inspections.update
        return view('packaging_inspections.update', compact('packagingInspection', 'vehicleConditions', 'suppliers'));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date = $request->input('start_date');
        $shift = $request->input('shift');
        $userPlant = Auth::user()->plant;

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Kedatangan Bahan Kemas')
            ->value('no_dokumen');

        $query = PackagingInspection::with(['items']);
        if (Auth::check() && !empty($userPlant)) {
            $query->where('plant_uuid', $userPlant);
        }

        // Filter tanggal dan shift
        $query->when($date, function ($q) use ($date) {
            $q->whereDate('inspection_date', $date);
        });

        $query->when($shift, function ($q) use ($shift) {
            $q->where('shift', $shift);
        });

        $inspections = $query->orderBy('inspection_date', 'asc')->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pemeriksaan Packaging');

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
        $html = view('reports.pemeriksaan-packaging', compact('inspections', 'request', 'noDokumen'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Pemeriksaan_Packaging_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
