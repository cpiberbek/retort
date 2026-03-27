<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use Illuminate\Http\Request; // <-- Pastikan ini di-import
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BeritaAcaraController extends Controller
{
    /**
     * Menampilkan daftar data.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Base Query dengan Eager Loading
        // Mengambil relasi creator & updater agar efisien (tidak N+1 query)
        $query = BeritaAcara::with(['creator', 'updater']);

        // 2. Filter Tanggal (Single Date)
        // Disesuaikan dengan UI baru yang menggunakan name="date"
        if ($request->filled('date')) {
            // Menggunakan whereDate untuk mencocokkan tanggal spesifik
            $query->whereDate('tanggal_kedatangan', $request->date);
        }

        // 3. Filter Search (Pencarian Global)
        if ($request->filled('search')) {
            $search = $request->search;

            // Grouping query (WHERE ...) AND ( ... OR ... OR ...)
            $query->where(function($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                ->orWhere('supplier', 'like', "%{$search}%")
                ->orWhere('nama_barang', 'like', "%{$search}%")
                
                // Opsional: Mencari berdasarkan nama Pembuat (Creator)
                ->orWhereHas('creator', function($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 4. Sorting & Pagination
        // Menggunakan withQueryString() agar filter tidak hilang saat pindah halaman
        $beritaAcaras = $query->latest()
        ->paginate(15)
        ->withQueryString();

        return view('berita-acara.index', compact('beritaAcaras'));
    }
    /**
     * Menampilkan form create.
     */
    public function create()
    {
        return view('berita-acara.create');
    }

    /**
     * Menyimpan data baru.
     */
    public function store(Request $request)
    {
        // Validasi bisa ditambahkan di sini
        // $validatedData = $request->all(); 
      $validatedData = $request->validate(
        [
            'nomor' => [
                'required',
                Rule::unique('berita_acaras', 'nomor')
            ],

            'nama_barang' => 'required|string',
            'jumlah_barang' => 'required|integer',
            'supplier' => 'required|string',

            'tanggal_kedatangan' => 'required|date',

            'no_surat_jalan' => 'nullable|string',
            'dd_po' => 'nullable|string',
            'tanggal_keputusan' => 'nullable|date',

            'uraian_masalah' => 'required|string',

            'analisa_penyebab' => 'nullable|string',
            'tindak_lanjut_perbaikan' => 'nullable|string',
            'lampiran' => 'nullable|string',
        ],
        [
            'nomor.required' => 'Nomor berita acara wajib diisi.',
            'nomor.unique'   => 'Nomor berita acara sudah pernah digunakan.',
        ]
    );

        // Menambahkan created_by (sesuai foreign key 'uuid' di tabel users)
      $validatedData['created_by'] = Auth::user()->uuid;

        // Handle checkbox (jika tidak dicentang, value-nya null)
      $checkboxes = [
        'keputusan_pengembalian', 'keputusan_potongan_harga', 'keputusan_sortir',
        'keputusan_penukaran_barang', 'keputusan_penggantian_biaya'
    ];
    foreach ($checkboxes as $cb) {
        $validatedData[$cb] = $request->has($cb);
    }

    BeritaAcara::create($validatedData);

    return redirect()->route('berita-acara.index')
    ->with('success', 'Berita Acara berhasil dibuat.');
}

    /**
     * Menampilkan detail data.
     */
    public function show(BeritaAcara $beritaAcara)
    {
        return view('berita-acara.show', compact('beritaAcara'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(BeritaAcara $beritaAcara)
    {
        return view('berita-acara.edit', compact('beritaAcara'));
    }

    /**
     * Update data.
     */

    public function update(Request $request, BeritaAcara $beritaAcara)
    {
        $validatedData = $request->validate(
            [
                'nomor' => [
                    'required',
                    Rule::unique('berita_acaras', 'nomor')
                    ->ignore($beritaAcara->id)
                ],

                'nama_barang' => 'required|string',
                'jumlah_barang' => 'required|integer',
                'supplier' => 'required|string',

                'tanggal_kedatangan' => 'required|date',

                'no_surat_jalan' => 'nullable|string',
                'dd_po' => 'nullable|string',
                'tanggal_keputusan' => 'nullable|date',

                'uraian_masalah' => 'required|string',

                'analisa_penyebab' => 'nullable|string',
                'tindak_lanjut_perbaikan' => 'nullable|string',
                'lampiran' => 'nullable|string',
            ],
            [
                'nomor.required' => 'Nomor berita acara wajib diisi.',
                'nomor.unique'   => 'Nomor berita acara sudah pernah digunakan.',
            ]
        );

    // Handle checkbox
        $checkboxes = [
            'keputusan_pengembalian',
            'keputusan_potongan_harga',
            'keputusan_sortir',
            'keputusan_penukaran_barang',
            'keputusan_penggantian_biaya',
        ];

        foreach ($checkboxes as $cb) {
            $validatedData[$cb] = $request->has($cb);
        }

        $beritaAcara->update($validatedData);

        return redirect()
        ->route('berita-acara.index')
        ->with('success', 'Berita Acara berhasil diperbarui.');
    }

    public function destroy(BeritaAcara $beritaAcara)
    {
        $beritaAcara->delete();
        return redirect()->route('berita-acara.index')
        ->with('success', 'Berita Acara berhasil dihapus.');
    }

    public function recyclebin()
    {
        $beritaAcara = BeritaAcara::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('berita-acara.recyclebin', compact('beritaAcara'));
    }
    public function restore($uuid)
    {
        $beritaAcara = BeritaAcara::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $beritaAcara->restore();

        return redirect()->route('berita-acara.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $beritaAcara = BeritaAcara::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $beritaAcara->forceDelete();

        return redirect()->route('berita-acara.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
    // --- METODE VERIFIKASI (SESUAI CONTOH ANDA) ---

    /**
     * Menampilkan halaman verifikasi untuk SPV.
     */
    public function verificationSpv(Request $request)
    {
        // Logika filter di sini SUDAH BENAR dan tidak perlu diubah.
        $query = BeritaAcara::query()->latest(); // Hanya tampilkan yg pending

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->where('tanggal_kedatangan', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal_kedatangan', '<=', $request->end_date);
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                ->orWhere('supplier', 'like', "%{$search}%")
                ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        $beritaAcaras = $query->with('creator')
        ->paginate(15)
        ->appends($request->query());

        return view('berita-acara.verification-spv', compact('beritaAcaras'));
    }

    /**
     * Memproses verifikasi dari SPV.
     */
    public function verifySpv(Request $request, BeritaAcara $beritaAcara)
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
            $beritaAcara->status_spv = $validatedData['status_spv'];
            $beritaAcara->catatan_spv = $validatedData['catatan_spv'];
            // Gunakan UUID user yang login, sesuai skema migration
            $beritaAcara->spv_verified_by = Auth::user()->uuid; 
            $beritaAcara->spv_verified_at = now();
            $beritaAcara->save();

            $message = $validatedData['status_spv'] == 1 ? 'Data berhasil diverifikasi.' : 'Data ditandai untuk revisi.';
            return redirect()->route('berita-acara.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error verifikasi SPV: ' . $e->getMessage());
            return redirect()->route('berita-acara.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function showUpdateForm(BeritaAcara $beritaAcara)
    {
        return view('berita-acara.update_view', compact('beritaAcara'));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;

        $query = BeritaAcara::query();
        if (Auth::check() && !empty($userPlant)) {
            $query->where('plant_uuid', $userPlant);
        }

        // Filter tanggal kedatangan
        $query->when($date, function ($q) use ($date) {
            $q->whereDate('tanggal_kedatangan', $date);
        });

        $beritaAcaras = $query->orderBy('tanggal_kedatangan', 'asc')->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Form Berita Acara');

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
        $html = view('reports.form-berita-acara', compact('beritaAcaras', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Form_Berita_Acara_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
