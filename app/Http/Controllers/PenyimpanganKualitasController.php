<?php

namespace App\Http\Controllers;

use App\Models\PenyimpanganKualitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PenyimpanganKualitasController extends Controller
{
    /**
     * Menampilkan daftar data dengan filter.
     */
    public function index(Request $request)
    {
        // 1. Base Query dengan Eager Loading
        // Memuat semua relasi yang diperlukan untuk efisiensi
        $query = PenyimpanganKualitas::with([
            'creator', 
            'updater', 
            'verifierDiketahui', 
            'verifierDisetujui'
        ]);

        // 2. Filter Tanggal (Single Date)
        // Menggunakan 'date' sesuai name="date" di View baru
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // 3. Filter Search (Pencarian Global)
        if ($request->filled('search')) {
            $search = $request->search;

            // Grouping query agar logika OR tidak merusak filter tanggal (AND)
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('lot_kode', 'like', "%{$search}%")
                ->orWhere('ditujukan_untuk', 'like', "%{$search}%")
                
                // Tambahan: Cari juga berdasarkan nama User pembuat
                ->orWhereHas('creator', function($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 4. Sorting & Pagination
        // Menggunakan latest() untuk urutan terbaru
        // Menggunakan withQueryString() agar filter tetap ada saat ganti halaman
        $penyimpanganKualitasItems = $query->latest()
        ->paginate(15)
        ->withQueryString();

        return view('penyimpangan-kualitas.index', compact('penyimpanganKualitasItems'));
    }

    /**
     * Menampilkan form create.
     */
    public function create()
    {
        return view('penyimpangan-kualitas.create');
    }

    /**
     * Menyimpan data baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nomor' => [
                'required',
                'string',
                'max:50',
                'unique:penyimpangan_kualitas,nomor'
            ],
            'tanggal' => ['required', 'date'],
            'ditujukan_untuk' => ['required', 'string', 'max:100'],

            'nama_produk' => ['required', 'string', 'max:150'],
            'lot_kode' => ['nullable', 'string', 'max:50'],
            'jumlah' => ['nullable', 'integer', 'min:1'],

            'keterangan' => ['nullable', 'string'],
            'penyelesaian' => ['nullable', 'string'],
        ]);

        $validatedData['created_by'] = Auth::user()->uuid;

        PenyimpanganKualitas::create($validatedData);

        return redirect()
        ->route('penyimpangan-kualitas.index')
        ->with('success', 'Laporan Penyimpangan Kualitas berhasil dibuat.');
    }


    /**
     * Menampilkan detail data.
     */
    public function show(PenyimpanganKualitas $penyimpanganKualitas)
    {
        return view('penyimpangan-kualitas.show', compact('penyimpanganKualitas'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(PenyimpanganKualitas $penyimpanganKualitas)
    {
        return view('penyimpangan-kualitas.edit', compact('penyimpanganKualitas'));
    }

    /**
     * Update data.
     */
    public function update(Request $request, PenyimpanganKualitas $penyimpanganKualitas)
    {
        $validatedData = $request->validate([
            'nomor' => [
                'required',
                'string',
                'max:50',
                Rule::unique('penyimpangan_kualitas', 'nomor')
                ->ignore($penyimpanganKualitas->id)
            ],
            'tanggal' => ['required', 'date'],
            'ditujukan_untuk' => ['required', 'string', 'max:100'],

            'nama_produk' => ['required', 'string', 'max:150'],
            'lot_kode' => ['nullable', 'string', 'max:50'],
            'jumlah' => ['nullable', 'integer', 'min:1'],

            'keterangan' => ['nullable', 'string'],
            'penyelesaian' => ['nullable', 'string'],
        ]);

        $penyimpanganKualitas->update($validatedData);

        return redirect()
        ->route('penyimpangan-kualitas.index')
        ->with('success', 'Laporan Penyimpangan Kualitas berhasil diperbarui.');
    }

    public function destroy(PenyimpanganKualitas $penyimpanganKualitas)
    {
        $penyimpanganKualitas->delete();
        return redirect()->route('penyimpangan-kualitas.index')
        ->with('success', 'Laporan Penyimpangan Kualitas berhasil dihapus.');
    }

    public function recyclebin()
    {
        $penyimpanganKualitas = PenyimpanganKualitas::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('penyimpangan-kualitas.recyclebin', compact('penyimpanganKualitas'));
    }
    public function restore($uuid)
    {
        $penyimpanganKualitas = PenyimpanganKualitas::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $penyimpanganKualitas->restore();

        return redirect()->route('penyimpangan-kualitas.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $penyimpanganKualitas = PenyimpanganKualitas::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $penyimpanganKualitas->forceDelete();

        return redirect()->route('penyimpangan-kualitas.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    // --- METODE VERIFIKASI (TAHAP 1: DIKETAHUI) ---

    public function verificationDiketahui(Request $request)
    {
        $query = PenyimpanganKualitas::query()->latest(); // Menampilkan semua data

        if ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('lot_kode', 'like', "%{$search}%");
            });
        }

        $penyimpanganKualitasItems = $query->with('creator', 'verifierDiketahui')
        ->paginate(15)
        ->appends($request->query());

        return view('penyimpangan-kualitas.verification-diketahui', compact('penyimpanganKualitasItems'));
    }

    public function verifyDiketahui(Request $request, PenyimpanganKualitas $penyimpanganKualitas)
    {
        $validatedData = $request->validate([
            'status_diketahui' => ['required', Rule::in([1, 2])],
            'catatan_diketahui' => ['nullable', 'required_if:status_diketahui,2', 'string'],
        ]);

        try {
            $penyimpanganKualitas->status_diketahui = $validatedData['status_diketahui'];
            $penyimpanganKualitas->catatan_diketahui = $validatedData['catatan_diketahui'];
            $penyimpanganKualitas->diketahui_by = Auth::user()->uuid;
            $penyimpanganKualitas->diketahui_at = now();
            $penyimpanganKualitas->save();

            $message = $validatedData['status_diketahui'] == 1 ? 'Data berhasil diverifikasi.' : 'Data ditandai untuk revisi.';
            return redirect()->route('penyimpangan-kualitas.verification.diketahui')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error verifikasi (Diketahui): ' . $e->getMessage());
            return redirect()->route('penyimpangan-kualitas.verification.diketahui')->with('error', 'Terjadi kesalahan.');
        }
    }

    // --- METODE VERIFIKASI (TAHAP 2: DISETUJUI) ---

    public function verificationDisetujui(Request $request)
    {
        // Menampilkan data yang sudah "Diketahui" (status 1) atau "Disetujui" (status 1)
        $query = PenyimpanganKualitas::query()
        ->whereIn('status_diketahui', [1, 2])
        ->latest(); 

        if ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('lot_kode', 'like', "%{$search}%");
            });
        }

        $penyimpanganKualitasItems = $query->with('creator', 'verifierDiketahui', 'verifierDisetujui')
        ->paginate(15)
        ->appends($request->query());

        return view('penyimpangan-kualitas.verification-disetujui', compact('penyimpanganKualitasItems'));
    }

    public function verifyDisetujui(Request $request, PenyimpanganKualitas $penyimpanganKualitas)
    {
        // Pastikan sudah lolos tahap 1
        if ($penyimpanganKualitas->status_diketahui != 1) {
           return redirect()->route('penyimpangan-kualitas.verification.disetujui')->with('error', 'Data ini belum lolos verifikasi tahap 1 (Diketahui).');
       }

       $validatedData = $request->validate([
        'status_disetujui' => ['required', Rule::in([1, 2])],
        'catatan_disetujui' => ['nullable', 'required_if:status_disetujui,2', 'string'],
    ]);

       try {
        $penyimpanganKualitas->status_disetujui = $validatedData['status_disetujui'];
        $penyimpanganKualitas->catatan_disetujui = $validatedData['catatan_disetujui'];
        $penyimpanganKualitas->disetujui_by = Auth::user()->uuid;
        $penyimpanganKualitas->disetujui_at = now();
        $penyimpanganKualitas->save();

        $message = $validatedData['status_disetujui'] == 1 ? 'Data berhasil disetujui.' : 'Data ditandai untuk revisi.';
        return redirect()->route('penyimpangan-kualitas.verification.disetujui')->with('success', $message);
    } catch (\Exception $e) {
        Log::error('Error verifikasi (Disetujui): ' . $e->getMessage());
        return redirect()->route('penyimpangan-kualitas.verification.disetujui')->with('error', 'Terjadi kesalahan.');
    }
}

public function showUpdateForm(PenyimpanganKualitas $penyimpanganKualitas)
{
        // Menggunakan view baru khusus update restricted
    return view('penyimpangan-kualitas.update_view', compact('penyimpanganKualitas'));
}

public function exportPdf(Request $request)
{
        // 1. Ambil Data
    $date = $request->input('date');
    $userPlant = Auth::user()->plant;

    $query = PenyimpanganKualitas::query();
    if (Auth::check() && !empty($userPlant)) {
        $query->where('plant_uuid', $userPlant);
    }

        // Filter tanggal
    $query->when($date, function ($q) use ($date) {
        $q->whereDate('tanggal', $date);
    });

    $penyimpanganKualitas = $query->orderBy('tanggal', 'asc')->get();

    if (ob_get_length()) {
        ob_end_clean();
    }

        // 2. Setup PDF (Landscape, A4)
    $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Berita Acara Internal Penyimpangan Kualitas');

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
    $html = view('reports.berita-acara-internal-penyimpangan-kualitas', compact('penyimpanganKualitas', 'request'))->render();
    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = 'Berita_Acara_Internal_Penyimpangan_Kualitas_' . date('d-m-Y_His') . '.pdf';
    $pdf->Output($filename, 'I');
    exit();
}
}
