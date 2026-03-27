<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanRetain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;

class PemeriksaanRetainController extends Controller
{
    public function index(Request $request)
    {
        $query = PemeriksaanRetain::withCount('items')
        ->with('creator')
        ->latest();

        // Optional: Filter otomatis agar user hanya melihat data Plant-nya sendiri
        /*
        if (Auth::check() && Auth::user()->plant) {
             $query->where('plant_uuid', Auth::user()->plant);
        }
        */

        $query->when($request->start_date, fn($q) => $q->where('tanggal', '>=', $request->start_date));
        $query->when($request->end_date, fn($q) => $q->where('tanggal', '<=', $request->end_date));

        $pemeriksaanRetains = $query->paginate(15);

        return view('pemeriksaan_retain.index', compact('pemeriksaanRetains'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        return view('pemeriksaan_retain.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required|string|max:100',
            'tanggal' => 'required|date', 
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.kode_produksi' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Note: 'plant_uuid', 'created_by', 'updated_by' OTOMATIS DIISI OLEH MODEL (booted)
            // Jadi kita cukup kirim data form saja.
            $pemeriksaanRetain = PemeriksaanRetain::create($request->only('hari', 'tanggal', 'keterangan'));

            foreach ($request->items as $itemData) {
                // Mapping data item agar aman
                $itemPayload = [
                    'kode_produksi'   => $itemData['kode_produksi'] ?? null,
                    'exp_date'        => $itemData['exp_date'] ?? null,
                    'varian'          => $itemData['varian'] ?? null,
                    'panjang'         => $itemData['panjang'] ?? null,
                    'diameter'        => $itemData['diameter'] ?? null,
                    'sensori_rasa'    => $itemData['sensori_rasa'] ?? null,
                    'sensori_warna'   => $itemData['sensori_warna'] ?? null,
                    'sensori_aroma'   => $itemData['sensori_aroma'] ?? null,
                    'sensori_texture' => $itemData['sensori_texture'] ?? null,
                    'temuan_jamur'    => isset($itemData['temuan_jamur']),
                    'temuan_lendir'   => isset($itemData['temuan_lendir']),
                    'temuan_pinehole' => isset($itemData['temuan_pinehole']),
                    'temuan_kejepit'  => isset($itemData['temuan_kejepit']),
                    'temuan_seal'     => isset($itemData['temuan_seal']),
                    'lab_garam'       => $itemData['lab_garam'] ?? null,
                    'lab_air'         => $itemData['lab_air'] ?? null,
                    'lab_mikro'       => $itemData['lab_mikro'] ?? null,
                ];

                $pemeriksaanRetain->items()->create($itemPayload);
            }

            DB::commit();

            return redirect()->route('pemeriksaan_retain.index')
            ->with('success', 'Data pemeriksaan retain berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing pemeriksaan retain: ' . $e->getMessage());

            return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
        }
    }

    public function show(PemeriksaanRetain $pemeriksaanRetain)
    {
        $pemeriksaanRetain->load('items', 'creator', 'verifiedBy', 'updater');
        return view('pemeriksaan_retain.show', compact('pemeriksaanRetain'));
    }

    public function edit(PemeriksaanRetain $pemeriksaanRetain)
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $pemeriksaanRetain->load('items');
        return view('pemeriksaan_retain.edit', compact('pemeriksaanRetain', 'produks'));
    }

    public function update(Request $request, PemeriksaanRetain $pemeriksaanRetain)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update data header.
            // 'updated_by' akan otomatis terupdate oleh Model (booted -> updating)
            $pemeriksaanRetain->update($request->only('hari', 'tanggal', 'keterangan'));

            // Strategy: Hapus semua items lama, buat baru (Replacement)
            $pemeriksaanRetain->items()->delete();

            // Create ulang items
            foreach ($request->items as $itemData) {
                $itemPayload = [
                    'kode_produksi'   => $itemData['kode_produksi'] ?? null,
                    'exp_date'        => $itemData['exp_date'] ?? null,
                    'varian'          => $itemData['varian'] ?? null,
                    'panjang'         => $itemData['panjang'] ?? null,
                    'diameter'        => $itemData['diameter'] ?? null,
                    'sensori_rasa'    => $itemData['sensori_rasa'] ?? null,
                    'sensori_warna'   => $itemData['sensori_warna'] ?? null,
                    'sensori_aroma'   => $itemData['sensori_aroma'] ?? null,
                    'sensori_texture' => $itemData['sensori_texture'] ?? null,
                    'temuan_jamur'    => isset($itemData['temuan_jamur']),
                    'temuan_lendir'   => isset($itemData['temuan_lendir']),
                    'temuan_pinehole' => isset($itemData['temuan_pinehole']),
                    'temuan_kejepit'  => isset($itemData['temuan_kejepit']),
                    'temuan_seal'     => isset($itemData['temuan_seal']),
                    'lab_garam'       => $itemData['lab_garam'] ?? null,
                    'lab_air'         => $itemData['lab_air'] ?? null,
                    'lab_mikro'       => $itemData['lab_mikro'] ?? null,
                ];
                $pemeriksaanRetain->items()->create($itemPayload);
            }

            DB::commit();

            return redirect()->route('pemeriksaan_retain.index')
            ->with('success', 'Data pemeriksaan retain berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(PemeriksaanRetain $pemeriksaanRetain)
    {
        try {
            $pemeriksaanRetain->items()->delete();
            $pemeriksaanRetain->delete();
            return redirect()->route('pemeriksaan_retain.index')
            ->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function recyclebin()
    {
        $pemeriksaanRetain = PemeriksaanRetain::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('pemeriksaan_retain.recyclebin', compact('pemeriksaanRetain'));
    }
    public function restore($uuid)
    {
        $pemeriksaanRetain = PemeriksaanRetain::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemeriksaanRetain->restore();

        return redirect()->route('pemeriksaan_retain.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pemeriksaanRetain = PemeriksaanRetain::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemeriksaanRetain->forceDelete();

        return redirect()->route('pemeriksaan_retain.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
    // --- FITUR VERIFIKASI ---

    public function showVerificationPage(Request $request)
    {
        $baseQuery = PemeriksaanRetain::with('creator', 'items', 'verifiedBy')
        ->latest('tanggal');

        $baseQuery->when($request->start_date, fn($q, $d) => $q->where('tanggal', '>=', $d));
        $baseQuery->when($request->end_date, fn($q, $d) => $q->where('tanggal', '<=', $d));

        $baseQuery->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('hari', 'like', "%{$search}%")
                ->orWhereHas('creator', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                });
            });
        });

        $pemeriksaanRetains = $baseQuery->paginate(20)->withQueryString();
        return view('pemeriksaan_retain.verification', compact('pemeriksaanRetains'));
    }

    public function submitVerification(Request $request, PemeriksaanRetain $pemeriksaanRetain)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|required_if:status_spv,2',
        ]);

        try {
            // Gunakan UUID user yang login untuk verified_by
            // Kolom 'updated_by' juga akan otomatis terupdate oleh Model
            $pemeriksaanRetain->update([
                'status_spv'  => $request->status_spv,
                'catatan_spv' => $request->catatan_spv,
                'verified_by' => Auth::user()->uuid,
                'verified_at' => now(),
            ]);

            return redirect()->route('pemeriksaan_retain.index')
            ->with('success', 'Data berhasil diverifikasi.');
        } catch (\Exception $e) {
            Log::error('Verifikasi Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal verifikasi.');
        }
    }

    public function editForUpdate(PemeriksaanRetain $pemeriksaanRetain)
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        // Load items agar muncul di form
        $pemeriksaanRetain->load('items');

        // Return ke view baru: pemeriksaan_retain.update
        return view('pemeriksaan_retain.update', compact('pemeriksaanRetain', 'produks'));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;

        $query = PemeriksaanRetain::with(['items', 'creator']);
        if (Auth::check() && !empty($userPlant)) {
            $query->where('plant_uuid', $userPlant);
        }

        // Filter tanggal
        $query->when($date, function ($q) use ($date) {
            $q->whereDate('tanggal', $date);
        });

        $retains = $query->orderBy('tanggal', 'asc')->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pengecekan Retain Sampel');

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
        $html = view('reports.pengecekan-retain-sampel', compact('retains', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Pengecekan_Retain_Sampel_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
