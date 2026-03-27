<?php

namespace App\Http\Controllers;

use App\Models\Disposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rule;   

class DispositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Base Query dengan Eager Loading (Creator & Updater)
        // 'with' digunakan agar tidak terjadi N+1 Query problem saat menampilkan nama pembuat
        $query = Disposition::with(['creator', 'updater']);

        // 2. Filter Tanggal
        // Menggunakan 'date' sesuai name="date" di input HTML Anda
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // 3. Filter Search (Pencarian Global)
        if ($request->filled('search')) {
            $search = $request->search;
            
            // Kita bungkus dalam where(function...) agar logika OR tidak merusak logika AND tanggal
            $query->where(function($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                ->orWhere('kepada', 'like', "%{$search}%")

                  // Sesuai Model Anda: Gunakan 'uraian_disposisi' dan 'catatan'
                ->orWhere('uraian_disposisi', 'like', "%{$search}%")
                ->orWhere('catatan', 'like', "%{$search}%")

                  // Pencarian Relasi: Mencari berdasarkan nama User (creator)
                ->orWhereHas('creator', function($subQuery) use ($search) {
                  $subQuery->where('name', 'like', "%{$search}%");
              });
            });
        }

        // 4. Ambil data, urutkan dari yang terbaru, dan paginasi
        // withQueryString() wajib ada agar filter tidak hilang saat pindah halaman
        $dispositions = $query->latest()
        ->paginate(10)
        ->withQueryString();

        return view('dispositions.index', compact('dispositions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mengirim 'disposition' kosong agar form partial bisa dipakai di create dan edit
        $disposition = new Disposition();
        return view('dispositions.create', compact('disposition'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nomor' => 'required|string|max:255|unique:dispositions',
            'tanggal' => 'required|date',
            'kepada' => 'required|string|max:255',
            'dasar_disposisi' => 'required|string',
            'uraian_disposisi' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        // Logika untuk menangani checkbox
        // Jika checkbox tidak dicentang, $request->has() akan false.
        $validatedData['disposisi_produk'] = $request->has('disposisi_produk');
        $validatedData['disposisi_material'] = $request->has('disposisi_material');
        $validatedData['disposisi_prosedur'] = $request->has('disposisi_prosedur');

        // <-- TAMBAHAN: Mengisi ID user yang sedang login
        $validatedData['created_by'] = Auth::id();

        Disposition::create($validatedData);

        return redirect()->route('dispositions.index')
        ->with('success', 'Data disposisi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     * $disposition akan dicari otomatis menggunakan UUID.
     */
    public function show(Disposition $disposition)
    {
        // Anda bisa load relasinya di sini jika perlu
        // $disposition->load('creator'); 
        return view('dispositions.show', compact('disposition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Disposition $disposition)
    {
        return view('dispositions.edit', compact('disposition'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Disposition $disposition)
    {
        $validatedData = $request->validate([
            'nomor' => 'required|string|max:255|unique:dispositions,nomor,' . $disposition->id,
            'tanggal' => 'required|date',
            'kepada' => 'required|string|max:255',
            'dasar_disposisi' => 'required|string',
            'uraian_disposisi' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        // Logika untuk menangani checkbox
        $validatedData['disposisi_produk'] = $request->has('disposisi_produk');
        $validatedData['disposisi_material'] = $request->has('disposisi_material');
        $validatedData['disposisi_prosedur'] = $request->has('disposisi_prosedur');

        // Catatan: 'created_by' tidak di-set di sini, karena 'created_by' 
        // seharusnya tidak berubah saat update.
        // Jika Anda ingin melacak siapa yang meng-update, Anda perlu kolom 'updated_by'
        
        $disposition->update($validatedData);

        return redirect()->route('dispositions.index')
        ->with('success', 'Data disposisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * Ini akan melakukan Soft Delete.
     */
    public function destroy(Disposition $disposition)
    {
        $disposition->delete();

        return redirect()->route('dispositions.index')
        ->with('success', 'Data disposisi berhasil dihapus.');
    }

    public function recyclebin()
    {
        $disposition = Disposition::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('dispositions.recyclebin', compact('disposition'));
    }
    public function restore($uuid)
    {
        $disposition = Disposition::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $disposition->restore();

        return redirect()->route('dispositions.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $disposition = Disposition::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $disposition->forceDelete();

        return redirect()->route('dispositions.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function verification(Request $request)
    {
        $query = Disposition::query()->latest();

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        // Filter Pencarian (disesuaikan untuk disposisi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                ->orWhere('kepada', 'like', "%{$search}%");
            });
        }

        // Load relasi untuk efisiensi
        $dispositions = $query->with('verifiedBy', 'creator') 
        ->paginate(15)
        ->appends($request->query());

        return view('dispositions.verification', compact('dispositions'));
    }

    /**
     * Memproses verifikasi dari SPV.
     */
    public function verify(Request $request, Disposition $disposition)
    {
        // Validasi
        $validatedData = $request->validate([
            'status_spv' => [
                'required',
                Rule::in([1, 2]), // Harus 1 (Verified) atau 2 (Revision)
            ],
            'catatan_spv' => [
                'nullable',
                'required_if:status_spv,2', // Wajib jika status = 2 (Revision)
                'string',
            ],
        ]);

        try {
            // Update data disposisi
            $disposition->status_spv = $validatedData['status_spv'];
            $disposition->catatan_spv = $validatedData['catatan_spv'];
            $disposition->verified_by = Auth::id(); // User yang login
            $disposition->verified_at = now();      // Waktu sekarang
            $disposition->save();

            $message = $validatedData['status_spv'] == 1 ? 'Data berhasil diverifikasi.' : 'Data ditandai untuk revisi.';
            return redirect()->route('dispositions.index')->with('success', $message);

        } catch (\Exception $e) {
            // Handle jika ada error
            return redirect()->route('dispositions.index')->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function showUpdateForm(Disposition $disposition)
    {
        // Kita menggunakan view baru bernama 'dispositions.update_view'
        return view('dispositions.update_view', compact('disposition'));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;

        $query = Disposition::with(['creator']);
        if (Auth::check() && !empty($userPlant)) {
            $query->where('plant_uuid', $userPlant);
        }

        // Filter tanggal
        $query->when($date, function ($q) use ($date) {
            $q->whereDate('tanggal', $date);
        });

        $dispositions = $query->orderBy('tanggal', 'asc')->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Disposisi Produk dan Prosedur');

        // Hilangkan Header/Footer Bawaan
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set Margin
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);

        // Set Font Default
        $pdf->SetFont('helvetica', '', 8);

        $pdf->AddPage();

        // 3. Render
        $html = view('reports.disposisi-produk-prosedur', compact('dispositions', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Disposisi_Produk_dan_Prosedur_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
