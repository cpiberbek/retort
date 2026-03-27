<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use TCPDF;

class PackingController extends Controller 
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $nama_produk = $request->input('nama_produk');
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
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'waktu'       => 'required',
            'kalibrasi'   => 'required',
            'qrcode'      => 'required',

            'kode_printing' => 'nullable',

            'kode_toples'   => 'nullable|string',
            'kode_karton'   => 'nullable|string',
            'suhu'          => 'nullable|numeric',
            'speed'         => 'nullable|numeric',
            'kondisi_segel' => 'nullable|string',
            'berat_toples'  => 'nullable|numeric',
            'berat_pouch'   => 'nullable|numeric',
            'no_lot'        => 'nullable|string',
            'tgl_kedatangan'=> 'nullable|date',
            'nama_supplier' => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        $kodePrintingFinal = null;
        if ($request->hasFile('kode_printing')) {

            $file = $request->file('kode_printing');

            if ($file->getSize() > 2 * 1024 * 1024) {
                return back()
                ->withErrors(['kode_printing' => 'Ukuran file maksimal 2 MB.'])
                ->withInput();
            }

            $kodePrintingFinal = $this->compressAndStore($file, 'printing');

        } else {
            $kodePrintingFinal = $request->kode_printing;
        }

        Packing::create([
            'date'                => $request->date,
            'shift'               => $request->shift,
            'nama_produk'         => $request->nama_produk,
            'waktu'               => $request->waktu,
            'kalibrasi'           => $request->kalibrasi,
            'qrcode'              => $request->qrcode,
            'kode_printing'       => $kodePrintingFinal,

            'kode_toples'         => $request->kode_toples,
            'kode_karton'         => $request->kode_karton,
            'suhu'                => $request->suhu,
            'speed'               => $request->speed,
            'kondisi_segel'       => $request->kondisi_segel,
            'berat_toples'        => $request->berat_toples,
            'berat_pouch'         => $request->berat_pouch,
            'no_lot'              => $request->no_lot,
            'tgl_kedatangan'      => $request->tgl_kedatangan,
            'nama_supplier'       => $request->nama_supplier,
            'keterangan'          => $request->keterangan,

            'username'            => $username,
            'plant'               => $userPlant,
            'nama_produksi'       => $nama_produksi,
            'status_produksi'     => "1",
            'tgl_update_produksi' => now()->addHour(),
            'status_spv'          => "0",
        ]);

        return redirect()->route('packing.index')
        ->with('success', 'Pemeriksaan Proses Cartoning berhasil disimpan');
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
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'waktu'       => 'required',
            'kalibrasi'   => 'required',
            'qrcode'      => 'required',
            'kode_printing' => 'nullable',

            'kode_toples'   => 'nullable|string',
            'kode_karton'   => 'nullable|string',
            'suhu'          => 'nullable|numeric',
            'speed'         => 'nullable|numeric',
            'kondisi_segel' => 'nullable|string',
            'berat_toples'  => 'nullable|numeric',
            'berat_pouch'   => 'nullable|numeric',
            'no_lot'        => 'nullable|string',
            'tgl_kedatangan'=> 'nullable|date',
            'nama_supplier' => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        $kodePrintingFinal = $packing->kode_printing;

        if ($request->hasFile('kode_printing')) {

            $file = $request->file('kode_printing');

            if ($file->getSize() > 2 * 1024 * 1024) {
                return back()
                ->withErrors(['kode_printing' => 'Ukuran file maksimal 2 MB.'])
                ->withInput();
            }
            if (!empty($packing->kode_printing) && str_contains($packing->kode_printing, 'storage/')) {
                $oldPath = str_replace('storage/', 'public/', $packing->kode_printing);

                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }

            $kodePrintingFinal = $this->compressAndStore($file, 'printing');

        } else {
            if (is_string($request->kode_printing)) {
                $kodePrintingFinal = $request->kode_printing;
            }
        }

        $packing->update([
            'date'                => $request->date,
            'shift'               => $request->shift,
            'nama_produk'         => $request->nama_produk,
            'waktu'               => $request->waktu,
            'kalibrasi'           => $request->kalibrasi,
            'qrcode'              => $request->qrcode,
            'kode_printing'       => $kodePrintingFinal,

            'kode_toples'         => $request->kode_toples,
            'kode_karton'         => $request->kode_karton,
            'suhu'                => $request->suhu,
            'speed'               => $request->speed,
            'kondisi_segel'       => $request->kondisi_segel,
            'berat_toples'        => $request->berat_toples,
            'berat_pouch'         => $request->berat_pouch,
            'no_lot'              => $request->no_lot,
            'tgl_kedatangan'      => $request->tgl_kedatangan,
            'nama_supplier'       => $request->nama_supplier,
            'keterangan'          => $request->keterangan,

            'username_updated'    => $username_updated,
        ]);

        return redirect()->route('packing.index')
        ->with('success', 'Pemeriksaan Proses Cartoning berhasil diperbarui');
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
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'waktu'       => 'required',
            'kalibrasi'   => 'required',
            'qrcode'      => 'required',
            'kode_printing' => 'nullable',

            'kode_toples'   => 'nullable|string',
            'kode_karton'   => 'nullable|string',
            'suhu'          => 'nullable|numeric',
            'speed'         => 'nullable|numeric',
            'kondisi_segel' => 'nullable|string',
            'berat_toples'  => 'nullable|numeric',
            'berat_pouch'   => 'nullable|numeric',
            'no_lot'        => 'nullable|string',
            'tgl_kedatangan'=> 'nullable|date',
            'nama_supplier' => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        $kodePrintingFinal = $packing->kode_printing;

        if ($request->hasFile('kode_printing')) {

            $file = $request->file('kode_printing');

            if ($file->getSize() > 2 * 1024 * 1024) {
                return back()
                ->withErrors(['kode_printing' => 'Ukuran file maksimal 2 MB.'])
                ->withInput();
            }
            if (!empty($packing->kode_printing) && str_contains($packing->kode_printing, 'storage/')) {
                $oldPath = str_replace('storage/', 'public/', $packing->kode_printing);

                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }

            $kodePrintingFinal = $this->compressAndStore($file, 'printing');

        } else {
            if (is_string($request->kode_printing)) {
                $kodePrintingFinal = $request->kode_printing;
            }
        }

        $packing->update([
            'date'                => $request->date,
            'shift'               => $request->shift,
            'nama_produk'         => $request->nama_produk,
            'waktu'               => $request->waktu,
            'kalibrasi'           => $request->kalibrasi,
            'qrcode'              => $request->qrcode,
            'kode_printing'       => $kodePrintingFinal,

            'kode_toples'         => $request->kode_toples,
            'kode_karton'         => $request->kode_karton,
            'suhu'                => $request->suhu,
            'speed'               => $request->speed,
            'kondisi_segel'       => $request->kondisi_segel,
            'berat_toples'        => $request->berat_toples,
            'berat_pouch'         => $request->berat_pouch,
            'no_lot'              => $request->no_lot,
            'tgl_kedatangan'      => $request->tgl_kedatangan,
            'nama_supplier'       => $request->nama_supplier,
            'keterangan'          => $request->keterangan,
        ]);

        return redirect()->route('packing.index')
        ->with('success', 'Pemeriksaan Proses Cartoning berhasil diperbarui');
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

        return redirect()->route('packing.index')
        ->with('success', 'Status Verifikasi Pemeriksaan Proses Cartoning berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $packing = Packing::where('uuid', $uuid)->firstOrFail();

        // Hapus file gambar jika ada
        $proses = json_decode($packing->proses, true) ?? [];
        foreach ($proses as $row) {
            if (!empty($row['kode_printing'])) {
                $path = str_replace('storage/', 'public/', $row['kode_printing']);
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
        }

        $packing->delete();

        return redirect()->route('packing.index')
        ->with('success', 'Pemeriksaan Proses Packing berhasil dihapus');
    }

    public function recyclebin()
    {
        $packing = Packing::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.packing.recyclebin', compact('packing'));
    }
    public function restore($uuid)
    {
        $packing = Packing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $packing->restore();

        return redirect()->route('packing.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $packing = Packing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $packing->forceDelete();

        return redirect()->route('packing.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        $date = $request->input('date');
        $shift = $request->input('shift');
        $nama_produk = $request->input('nama_produk');
        $userPlant = Auth::user()->plant;

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

        // Clear any previous output buffers to prevent "TCPDF ERROR: Some data has already been output"
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Create new TCPDF object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name/Company');
        $pdf->SetTitle('Pemeriksaan Proses Cartoning');
        $pdf->SetSubject('Pemeriksaan Proses Cartoning');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Add a page
        $pdf->AddPage('L', 'A4'); // Landscape A4

        // Convert the Blade view to HTML
        $html = view('reports.proses-packing', compact('packings', 'request'))->render();

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document (Inline/Preview)
        $pdf->Output('Pemeriksaan_Proses_Packing_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }

    /**
     * 🧰 Helper untuk kompres dan simpan gambar
     */
    private function compressAndStore($file, $prefix)
    {
        $manager = new ImageManager(new Driver());
        $path = 'public/uploads/packing'; // ✅ pindah ke folder aman
        $filename = $prefix . '_' . Str::uuid() . '.jpg';

        // Pastikan folder ada
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path, 0755, true);
        }

        // Resize dan kompres gambar
        $image = $manager->read($file)->scale(width: 1280)->toJpeg(quality: 75);
        Storage::put("$path/$filename", (string) $image);

        // Return path yang bisa dipakai untuk view
        return "storage/uploads/packing/$filename";
    }
}
