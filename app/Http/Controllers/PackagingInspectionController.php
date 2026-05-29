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

class PackagingInspectionController extends Controller
{
    /**
     * Menampilkan daftar semua inspeksi.
     */
    public function index(Request $request): View
    {
        // 1. Memulai query builder
        $query = PackagingInspection::query();

        // 2. Terapkan filter pencarian
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('shift', 'LIKE', "%{$searchTerm}%") // Pencarian shift
                  ->orWhere('uuid', 'LIKE', "%{$searchTerm}%"); // Opsional: Cari UUID juga
              });
        }

        // 3. Terapkan filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('inspection_date', '=', $request->input('start_date'));
        }

        // 4. Terapkan filter shift
        if ($request->filled('shift')) {
            $query->where('shift', $request->input('shift'));
        }

        // 5. Urutkan dan Paginate
        $inspections = $query->latest('inspection_date')
                             ->latest('created_at') // Urutan tambahan agar data baru selalu di atas
                             ->paginate(10)
                             ->withQueryString();

                             return view('packaging_inspections.index', compact('inspections'));
                         }

    /**
     * Menampilkan form untuk membuat inspeksi baru.
     */
    public function create(): View
    {
        // Opsi untuk dropdown KONDISI KENDARAAN (sekarang dipakai di level item)
        $vehicleConditions = ['Bersih', 'Kotor', 'Bau', 'Bocor', 'Basah', 'Kering', 'Bebas Hama'];
        $suppliers = Supplier::all();
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
            'items.*.condition_weight_pcs'=> 'nullable|string|max:255',
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

        return view('packaging_inspections.edit', compact('packagingInspection', 'vehicleConditions'));
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
                'verified_by'   => Auth::id(),
                'verified_at'   => now(),
            ]);

            return back()->with('success', 'Verifikasi packaging berhasil disimpan.');

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

        // Return ke view baru: packaging_inspections.update
        return view('packaging_inspections.update', compact('packagingInspection', 'vehicleConditions'));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date = $request->input('start_date');
        $shift = $request->input('shift');
        $userPlant = Auth::user()->plant;

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
        $html = view('reports.pemeriksaan-packaging', compact('inspections', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Pemeriksaan_Packaging_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
