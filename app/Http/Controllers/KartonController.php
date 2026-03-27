<?php

namespace App\Http\Controllers;

use App\Models\Karton;
use App\Models\Operator;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use TCPDF;

class KartonController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $nama_produk = $request->input('nama_produk');
        $userPlant  = Auth::user()->plant;

        $data = Karton::with('mincing')  
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('kode_karton', 'like', "%{$search}%")
                ->orWhere('nama_supplier', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->when($nama_produk, function ($query) use ($nama_produk) {
            $query->where('nama_produk', $nama_produk);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.karton.index', compact('data', 'search', 'date', 'nama_produk'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();

        $suppliers = Supplier::where('plant', $userPlant)
        ->where('jenis_barang', 'Packaging')
        ->orderBy('nama_supplier')
        ->get();

        $operators = Operator::where('plant', $userPlant)
        ->where('bagian', 'Operator')
        ->orderBy('nama_karyawan')
        ->get();

        $koordinators  = Operator::where('plant', $userPlant)
        ->where('bagian', 'Koordinator')
        ->orderBy('nama_karyawan')
        ->get();

        return view('form.karton.create', compact('produks', 'operators', 'koordinators', 'suppliers'));
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $username  = Auth::user()->username ?? 'None';
        $userPlant = Auth::user()->plant;

        $nama_produksi = 'Produksi RTT';
        if (session()->has('selected_produksi')) {
            $userProduksi = \App\Models\User::where('uuid', session('selected_produksi'))->first();
            if ($userProduksi) {
                $nama_produksi = $userProduksi->name;
            }
        }

        $request->validate([
            'date'      => 'required|date',
            'nama_produk'     => 'required|string',
            'kode_produksi'     => 'required|string',
            'kode_karton' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'waktu_mulai'     => 'nullable',
            'waktu_selesai'     => 'nullable',
            'jumlah'     => 'nullable',
            'tgl_kedatangan'     => 'nullable|date',
            'nama_supplier'     => 'nullable|string',
            'no_lot'     => 'nullable|string',
            'nama_operator'     => 'nullable|string',
            'nama_koordinator'     => 'nullable|string',
            'keterangan'   => 'nullable|string',
        ]);

        $kartonPath = $request->hasFile('kode_karton')
        ? $this->compressAndStore($request->file('kode_karton'), 'kode_karton')
        : null;

        Karton::create([
            'date'                => $request->date,
            'nama_produk'               => $request->nama_produk,
            'kode_produksi'               => $request->kode_produksi,
            'kode_karton'         => $kartonPath,
            'waktu_mulai'             => $request->waktu_mulai,
            'waktu_selesai'             => $request->waktu_selesai,
            'jumlah'              => $request->jumlah,
            'tgl_kedatangan'             => $request->tgl_kedatangan,
            'nama_supplier'             => $request->nama_supplier,
            'no_lot'              => $request->no_lot,
            'keterangan'              => $request->keterangan,
            'nama_operator'             => $request->nama_operator,
            'nama_koordinator'             => $request->nama_koordinator,
            'nama_produksi'       => $nama_produksi,
            'status_produksi'     => "1",
            'status_operator'     => "1",
            'status_koordinator'  => "1",
            'tgl_update_produksi' => now(),
            'tgl_update_operator' => now(),
            'tgl_update_koordinator' => now(),
            'username'            => $username,
            'plant'               => $userPlant,
            'status_spv'          => "0",
        ]);

        return redirect()->route('karton.index')->with('success', 'Kontrol Labelisasi Karton berhasil disimpan.');
    }

    public function update($uuid)
    {
        $karton = Karton::where('uuid', $uuid)->firstOrFail(); 
        $userPlant = Auth::user()->plant;

        $suppliers = Supplier::where('plant', $userPlant)
        ->where('jenis_barang', 'Packaging')
        ->orderBy('nama_supplier')
        ->get();

        $produks    = Produk::where('plant', $userPlant)->get();
        $operators = Operator::where('plant', $userPlant)
        ->where('bagian', 'Operator')
        ->orderBy('nama_karyawan')
        ->get();

        $koordinators  = Operator::where('plant', $userPlant)
        ->where('bagian', 'Koordinator')
        ->orderBy('nama_karyawan')
        ->get();

        return view('form.karton.update', compact('karton', 'produks', 'koordinators', 'operators', 'suppliers'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $karton = Karton::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'           => 'required|date',
            'nama_produk'    => 'required|string',
            'kode_produksi'  => 'required|string',
            'kode_karton'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'waktu_mulai'    => 'required',
            'waktu_selesai'  => 'nullable',
            'jumlah'         => 'required',
            'tgl_kedatangan' => 'required|date',
            'nama_supplier'  => 'required|string',
            'no_lot'         => 'required|string',
            'nama_operator'  => 'nullable|string',
            'nama_koordinator' => 'nullable|string',
            'keterangan'     => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'              => $request->date,
            'nama_produk'       => $request->nama_produk,
            'kode_produksi'     => $request->kode_produksi,
            'waktu_mulai'       => $request->waktu_mulai,
            'waktu_selesai'     => $request->waktu_selesai,
            'jumlah'            => $request->jumlah,
            'tgl_kedatangan'    => $request->tgl_kedatangan,
            'nama_supplier'     => $request->nama_supplier,
            'no_lot'            => $request->no_lot,
            'keterangan'        => $request->keterangan,
            'nama_operator'     => $request->nama_operator,
            'nama_koordinator'  => $request->nama_koordinator,
            'username_updated'  => $username_updated,
        ];

        if ($request->hasFile('kode_karton')) {
            if ($karton->kode_karton && Storage::exists($karton->kode_karton)) {
                Storage::delete($karton->kode_karton);
            }
            $updateData['kode_karton'] = $this->compressAndStore($request->file('kode_karton'), 'kode_karton');
        }

        $karton->update($updateData);

        return redirect()->route('karton.index')->with('success', 'Kontrol Labelisasi Karton berhasil diperbarui.');
    }


    public function edit($uuid)
    {
        $karton = Karton::where('uuid', $uuid)->firstOrFail(); 
        $userPlant = Auth::user()->plant;

        $suppliers = Supplier::where('plant', $userPlant)
        ->where('jenis_barang', 'Packaging')
        ->orderBy('nama_supplier')
        ->get();

        $produks    = Produk::where('plant', $userPlant)->get();
        $operators = Operator::where('plant', $userPlant)
        ->where('bagian', 'Operator')
        ->orderBy('nama_karyawan')
        ->get();

        $koordinators  = Operator::where('plant', $userPlant)
        ->where('bagian', 'Koordinator')
        ->orderBy('nama_karyawan')
        ->get();

        return view('form.karton.edit', compact('karton', 'produks', 'koordinators', 'operators', 'suppliers'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $karton = Karton::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'           => 'required|date',
            'nama_produk'    => 'required|string',
            'kode_produksi'  => 'required|string',
            'kode_karton'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'waktu_mulai'    => 'required',
            'waktu_selesai'  => 'nullable',
            'jumlah'         => 'required',
            'tgl_kedatangan' => 'required|date',
            'nama_supplier'  => 'required|string',
            'no_lot'         => 'required|string',
            'nama_operator'  => 'nullable|string',
            'nama_koordinator' => 'nullable|string',
            'keterangan'     => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'              => $request->date,
            'nama_produk'       => $request->nama_produk,
            'kode_produksi'     => $request->kode_produksi,
            'waktu_mulai'       => $request->waktu_mulai,
            'waktu_selesai'     => $request->waktu_selesai,
            'jumlah'            => $request->jumlah,
            'tgl_kedatangan'    => $request->tgl_kedatangan,
            'nama_supplier'     => $request->nama_supplier,
            'no_lot'            => $request->no_lot,
            'keterangan'        => $request->keterangan,
            'nama_operator'     => $request->nama_operator,
            'nama_koordinator'  => $request->nama_koordinator,
            'username_updated'  => $username_updated,
        ];

        if ($request->hasFile('kode_karton')) {
            if ($karton->kode_karton && Storage::exists($karton->kode_karton)) {
                Storage::delete($karton->kode_karton);
            }
            $updateData['kode_karton'] = $this->compressAndStore($request->file('kode_karton'), 'kode_karton');
        }

        $karton->update($updateData);

        return redirect()->route('karton.index')->with('success', 'Kontrol Labelisasi Karton berhasil diperbarui.');
    }

    public function verification(Request $request)
    {
     $search     = $request->input('search');
     $date = $request->input('date');
     $userPlant  = Auth::user()->plant;

     $data = Karton::query()
     ->where('plant', $userPlant)
     ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%")
            ->orWhere('kode_karton', 'like', "%{$search}%")
            ->orWhere('nama_supplier', 'like', "%{$search}%");
        });
    })
     ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
     ->orderBy('date', 'desc')
     ->orderBy('created_at', 'desc')
     ->paginate(10)
     ->appends($request->all());

     return view('form.karton.index', compact('data', 'search', 'date'));
 }

 public function updateVerification(Request $request, $uuid)
 {
    $request->validate([
        'status_spv'  => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    $karton = Karton::where('uuid', $uuid)->firstOrFail();

    $karton->update([
        'status_spv'      => $request->status_spv,
        'catatan_spv'     => $request->catatan_spv,
        'nama_spv'        => Auth::user()->username,
        'tgl_update_spv'  => now(),
    ]);

    return redirect()->route('karton.index')->with('success', 'Status verifikasi Kontrol Labelisasi Karton berhasil diperbarui.');
}

public function destroy($uuid)
{
    $karton = Karton::where('uuid', $uuid)->firstOrFail();

    if ($karton->kode_karton && Storage::exists($karton->kode_karton)) {
        Storage::delete($karton->kode_karton);
    }
    if ($karton->handbasin && Storage::exists($karton->handbasin)) {
        Storage::delete($karton->handbasin);
    }

    $karton->delete();

    return redirect()->route('karton.index')->with('success', 'Data Kontrol Labelisasi Karton berhasil dihapus.');
}

public function recyclebin()
{
    $karton = Karton::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.karton.recyclebin', compact('karton'));
}
public function restore($uuid)
{
    $karton = Karton::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $karton->restore();

    return redirect()->route('karton.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $karton = Karton::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $karton->forceDelete();

    return redirect()->route('karton.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    $date = $request->input('date');
    $nama_produk = $request->input('nama_produk');
    $userPlant = Auth::user()->plant;

    $kartons = Karton::query()
    ->where('plant', $userPlant)
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->when($nama_produk, function ($query) use ($nama_produk) {
        $query->where('nama_produk', $nama_produk);
    })
    ->orderBy('date', 'asc')
    ->orderBy('created_at', 'asc')
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
    $pdf->SetTitle('Kontrol Labelisasi Karton');
    $pdf->SetSubject('Kontrol Labelisasi Karton');

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
    $pdf->SetFont('helvetica', '', 8);

    // Add a page
    $pdf->AddPage('L', 'A3'); // Landscape A3 for many columns

    // Convert the Blade view to HTML
    $html = view('reports.kontrol-labelisasi-karton', compact('kartons', 'request'))->render();

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // Close and output PDF document (Inline/Preview)
    $pdf->Output('Kontrol_Labelisasi_Karton_' . date('Ymd_His') . '.pdf', 'I');

    exit();
}

private function compressAndStore($file, $prefix)
{
    $manager = new ImageManager(new Driver());
    $path = 'public/karton';
    $filename = $prefix . '_' . Str::uuid() . '.jpg';

    $image = $manager->read($file)
    ->scale(width: 1280)
    ->toJpeg(quality: 75);

    Storage::put("$path/$filename", (string) $image);

    return "$path/$filename";
}
}