<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'waktu'         => 'required',
            'kalibrasi'     => 'nullable|string',
            'qrcode'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kode_printing' => 'nullable',
            'kode_toples'   => 'required|string',
            'suhu'          => 'nullable|numeric',
            'speed'         => 'nullable|numeric',
            'kondisi_segel' => 'nullable|string',
            'jumlah_produk' => 'nullable|integer', 
            'berat_pcs'     => 'nullable|numeric', 
            'berat_pack'    => 'nullable|numeric', 
            
            // Validasi Input Dinamis Array Kemasan
            'data_kemasan'                  => 'nullable|array',
            'data_kemasan.*.jenis_kemasan'  => 'required|string',
            'data_kemasan.*.no_lot_kemasan' => 'required|string',
            'data_kemasan.*.tgl_kedatangan' => 'nullable|date',
            'data_kemasan.*.nama_supplier'  => 'nullable|string',
            'keterangan'                    => 'nullable|string',
        ]);

        $kodePrintingFinal = $request->hasFile('kode_printing') ? $this->compressAndStore($request->file('kode_printing'), 'printing') : null;
        $qrcodeFinal = $request->hasFile('qrcode') ? $this->compressAndStore($request->file('qrcode'), 'qrcode') : null;

        Packing::create([
            'date'                => $request->date,
            'shift'               => $request->shift,
            'nama_produk'         => $request->nama_produk,
            'waktu'               => $request->waktu,
            'kalibrasi'           => $request->has('kalibrasi') ? 'Ok' : 'Tidak Ok',
            'qrcode'              => $qrcodeFinal,
            'kode_printing'       => $kodePrintingFinal,
            'kode_toples'         => $request->kode_toples,
            'suhu'                => $request->suhu,
            'speed'               => $request->speed,
            'kondisi_segel'       => $request->kondisi_segel,
            'jumlah_produk'       => $request->jumlah_produk,
            'berat_pcs'           => $request->berat_pcs,
            'berat_pack'          => $request->berat_pack,
            
            // Encode menjadi JSON string sebelum masuk DB
            'data_kemasan'        => $request->has('data_kemasan') ? json_encode($request->data_kemasan) : null,
            'keterangan'          => $request->keterangan,

            'username'            => $username,
            'plant'               => $userPlant,
            'nama_produksi'       => $nama_produksi,
            'status_produksi'     => "1",
            'tgl_update_produksi' => now()->addHour(),
            'status_spv'          => "0",
        ]);

        return redirect()->route('packing.index')->with('success', 'Pemeriksaan Proses Packing berhasil disimpan');
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
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'waktu'         => 'required',
            'kalibrasi'     => 'nullable|string',
            'qrcode'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kode_toples'   => 'required|string',
            'data_kemasan'  => 'nullable|array',
            'keterangan'    => 'nullable|string',
        ]);

        // File Handler Code (Compressed)
        $kodePrintingFinal = $request->hasFile('kode_printing') ? $this->compressAndStore($request->file('kode_printing'), 'printing') : $packing->kode_printing;
        $qrcodeFinal = $request->hasFile('qrcode') ? $this->compressAndStore($request->file('qrcode'), 'qrcode') : $packing->qrcode;

        $packing->update([
            'date'                => $request->date,
            'shift'               => $request->shift,
            'nama_produk'         => $request->nama_produk,
            'waktu'               => $request->waktu,
            'kalibrasi'           => $request->has('kalibrasi') ? 'Ok' : 'Tidak Ok',
            'qrcode'              => $qrcodeFinal,
            'kode_printing'       => $kodePrintingFinal,
            'kode_toples'         => $request->kode_toples,
            'suhu'                => $request->suhu,
            'speed'               => $request->speed,
            'kondisi_segel'       => $request->kondisi_segel,
            'jumlah_produk'       => $request->jumlah_produk,
            'berat_pcs'           => $request->berat_pcs,
            'berat_pack'          => $request->berat_pack,
            'data_kemasan'        => $request->has('data_kemasan') ? json_encode($request->data_kemasan) : null,
            'keterangan'          => $request->keterangan,
            'username_updated'    => $username_updated,
        ]);

        return redirect()->route('packing.index')->with('success', 'Pemeriksaan Proses Packing berhasil diperbarui');
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
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'waktu'         => 'required',
            'kode_toples'   => 'required|string',
            'data_kemasan'  => 'nullable|array',
            'keterangan'    => 'nullable|string',
        ]);

        $packing->update([
            'date'                => $request->date,
            'shift'               => $request->shift,
            'nama_produk'         => $request->nama_produk,
            'waktu'               => $request->waktu,
            'kalibrasi'           => $request->has('kalibrasi') ? 'Ok' : 'Tidak Ok',
            'kode_toples'         => $request->kode_toples,
            'suhu'                => $request->suhu,
            'speed'               => $request->speed,
            'kondisi_segel'       => $request->kondisi_segel,
            'jumlah_produk'       => $request->jumlah_produk,
            'berat_pcs'           => $request->berat_pcs,
            'berat_pack'          => $request->berat_pack,
            'data_kemasan'        => $request->has('data_kemasan') ? json_encode($request->data_kemasan) : null,
            'keterangan'          => $request->keterangan,
        ]);

        return redirect()->route('packing.index')->with('success', 'Pemeriksaan Proses Packing berhasil diperbarui');
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

        return redirect()->route('packing.index')->with('success', 'Status Verifikasi Pemeriksaan Proses Packing berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $packing = Packing::where('uuid', $uuid)->firstOrFail();

        if (!empty($packing->kode_printing)) {
            $path = str_replace('storage/', 'public/', $packing->kode_printing);
            if (Storage::exists($path)) { Storage::delete($path); }
        }

        if (!empty($packing->qrcode)) {
            $pathQr = str_replace('storage/', 'public/', $packing->qrcode);
            if (Storage::exists($pathQr)) { Storage::delete($pathQr); }
        }

        $packing->delete();
        return redirect()->route('packing.index')->with('success', 'Pemeriksaan Proses Packing berhasil dihapus');
    }

    public function recyclebin()
    {
        $packing = Packing::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
        return view('form.packing.recyclebin', compact('packing'));
    }

    public function restore($uuid)
    {
        $packing = Packing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $packing->restore();
        return redirect()->route('packing.recyclebin')->with('success', 'Data berhasil direstore.');
    }

    public function deletePermanent($uuid)
    {
        $packing = Packing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $packing->forceDelete();
        return redirect()->route('packing.recyclebin')->with('success', 'Data berhasil dihapus permanen.');
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
        $pdf->SetTitle('Pemeriksaan Proses Packing');
        $pdf->SetSubject('Pemeriksaan Proses Packing');

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
    
    private function compressAndStore($file, $prefix)
    {
        $manager = new ImageManager(new Driver());
        $path = 'public/uploads/packing';
        $filename = $prefix . '_' . Str::uuid() . '.jpg';

        if (!Storage::exists($path)) { Storage::makeDirectory($path, 0755, true); }

        $image = $manager->read($file)->scale(width: 1280)->toJpeg(quality: 75);
        Storage::put("$path/$filename", (string) $image);

        return "storage/uploads/packing/$filename";
    }
}