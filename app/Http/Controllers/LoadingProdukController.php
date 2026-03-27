<?php

namespace App\Http\Controllers;

use App\Models\LoadingProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;


class LoadingProdukController extends Controller
{
    /**
     * Menampilkan daftar.
     */
    public function index(Request $request) // 1. Tambahkan (Request $request)
    {
        // 2. Mulai query builder
        $query = LoadingProduk::with('creator');

        // 3. Terapkan filter jika ada input
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pol_mobil', 'like', "%{$search}%")
                ->orWhere('nama_supir', 'like', "%{$search}%")
                ->orWhere('ekspedisi', 'like', "%{$search}%");
            });
        }

        // 4. Eksekusi query dengan urutan terbaru dan paginasi
        $produks = $query->latest()->paginate(10);

        // 5. Kirim data ke view
        return view('loading-produks.index', compact('produks'));
    }

    /**
     * Menampilkan form create.
     */
    public function create()
    {
        return view('loading-produks.create');
    }

    /**
     * Menyimpan data baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|in:Pagi,Malam',
            'jenis_aktivitas' => 'required|string|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
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
        ]);

        DB::beginTransaction();
        try {
            $loadingProdukData = Arr::except($validatedData, ['details']);
            $loadingProduk = LoadingProduk::create($loadingProdukData); // Model akan otomatis mengisi created_by dan uuid

            foreach ($validatedData['details'] as $detail) {
                $loadingProduk->details()->create($detail);
            }

            DB::commit();

            return redirect()->route('loading-produks.index')
            ->with('success', 'Data pemeriksaan loading berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving loading check: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }


    /**
     * Menampilkan detail.
     * $loadingProduk di-inject otomatis menggunakan 'uuid' berkat getRouteKeyName()
     */
    public function show(LoadingProduk $loadingProduk)
    {
        $loadingProduk->load('details', 'creator');
        return view('loading-produks.show', compact('loadingProduk'));
    }

    /**
     * Menampilkan form edit.
     * $loadingProduk di-inject otomatis menggunakan 'uuid'
     */
    public function edit(LoadingProduk $loadingProduk)
    {
        $loadingProduk->load('details');
        return view('loading-produks.edit', compact('loadingProduk'));
    }

    /**
     * Update data.
     */
    public function update(Request $request, LoadingProduk $loadingProduk)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|in:Pagi,Malam',
            'jenis_aktivitas' => 'required|string|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
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
        ]);

        DB::beginTransaction();
        try {
            $loadingProduk->update(Arr::except($validatedData, ['details']));

            $loadingProduk->details()->delete(); 
            foreach ($validatedData['details'] as $detail) {
                $loadingProduk->details()->create($detail);
            }

            DB::commit();

            // --- PERBAIKAN DI SINI ---
            // Arahkan redirect ke route 'show' menggunakan UUID, bukan ID.
            return redirect()->route('loading-produks.show', $loadingProduk->uuid)
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
            $query->where(function($q) use ($search) {
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

            return redirect()->back()->with('success', "Data (Nopol: {$loadingProduk->no_pol_mobil}) berhasil diverifikasi.");

        } catch (\Exception $e) {
            Log::error('Gagal verifikasi Loading Produk: ' . $e->getMessage());
            return back()->with('error', 'Gagal memverifikasi data. Error: ' . $e->getMessage());
        }
    }

    public function updateDetails(LoadingProduk $loadingProduk)
    {
        // Pastikan relasi details dimuat
        $loadingProduk->load('details');
        return view('loading-produks.update-details', compact('loadingProduk'));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date = $request->input('date');
        $shift = $request->input('shift');
        $userPlant = Auth::user()->plant;

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

        // 3. Render
        $html = view('reports.pemeriksaan-loading-unloading', compact('loadings', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Pemeriksaan_Loading_Unloading_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
