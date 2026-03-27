<?php

namespace App\Http\Controllers;

use App\Models\Sampling;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class SamplingController extends Controller
{
    public function index(Request $request)
    {
     $search     = $request->input('search');
     $date = $request->input('date');
     $shift = $request->input('shift');
     $nama_produk = $request->input('nama_produk');
     $userPlant  = Auth::user()->plant;

     $data = Sampling::query()
     ->where('plant', $userPlant)
     ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%")
            ->orWhere('jenis_kemasan', 'like', "%{$search}%");
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

     return view('form.sampling.index', compact('data', 'search', 'date', 'shift', 'nama_produk'));
 }

 public function create()
 {
    $userPlant = Auth::user()->plant;
    $produks = Produk::where('plant', $userPlant)->get();

    return view('form.sampling.create', compact('produks'));
}

public function store(Request $request)
{
    $request->validate([
        'date'            => 'required|date',
        'shift'    => 'required|string',
        'jenis_sampel'    => 'required|string',
        'jenis_kemasan'   => 'required|string',
        'nama_produk'     => 'required|string',
        'kode_produksi'   => 'required|string',
        'jumlah'     => 'nullable|numeric',
        'jamur'      => 'nullable|numeric',
        'lendir'      => 'nullable|numeric',
        'klip_tajam'      => 'nullable|numeric',
        'pin_hole'      => 'nullable|numeric',
        'air_trap_pvdc'      => 'nullable|numeric',
        'air_trap_produk'      => 'nullable|numeric',
        'keriput'      => 'nullable|numeric',
        'bengkok'      => 'nullable|numeric',
        'non_kode'      => 'nullable|numeric',
        'over_lap'      => 'nullable|numeric',
        'kecil'      => 'nullable|numeric',
        'terjepit'      => 'nullable|numeric',
        'double_klip'      => 'nullable|numeric',
        'seal_halus'      => 'nullable|numeric',
        'basah'      => 'nullable|numeric',
        'dll'      => 'nullable|numeric',
        'catatan'    => 'nullable|string', 
    ]);

    $username  = Auth::user()->username ?? 'None';
    $userPlant = Auth::user()->plant;

    $data = [
        'date'            => $request->date,
        'shift'            => $request->shift,
        'jenis_sampel'    => $request->jenis_sampel,
        'jenis_kemasan'    => $request->jenis_kemasan,
        'nama_produk'     => $request->nama_produk,
        'kode_produksi'   => $request->kode_produksi,
        'jumlah'            => $request->jumlah,
        'jamur'   => $request->jamur,
        'lendir'   => $request->lendir,
        'klip_tajam'   => $request->klip_tajam,
        'pin_hole'   => $request->pin_hole,
        'air_trap_pvdc'   => $request->air_trap_pvdc,
        'air_trap_produk'   => $request->air_trap_produk,
        'keriput'   => $request->keriput,
        'bengkok'   => $request->bengkok,
        'non_kode'   => $request->non_kode,
        'over_lap'   => $request->over_lap,
        'kecil'   => $request->kecil,
        'terjepit'   => $request->terjepit,
        'double_klip'   => $request->double_klip,
        'seal_halus'   => $request->seal_halus,
        'basah'   => $request->basah,
        'dll'   => $request->dll,
        'catatan'         => $request->catatan,
        'username'        => $username,
        'plant'           => $userPlant,
        'status_spv'      => "0",
    ];

    Sampling::create($data);

    return redirect()->route('sampling.index')
    ->with('success', 'Data Sampling Produk berhasil disimpan.');
}

public function update(string $uuid)
{
    $sampling = Sampling::where('uuid', $uuid)->firstOrFail();
    $userPlant = Auth::user()->plant;
    $produks = Produk::where('plant', $userPlant)->get();

    return view('form.sampling.update', compact('sampling', 'produks'));
}

public function update_qc(Request $request, string $uuid)
{
    $sampling = Sampling::where('uuid', $uuid)->firstOrFail();

    $request->validate([
       'date'            => 'required|date',
       'shift'    => 'required|string',
       'jenis_sampel'    => 'required|string',
       'jenis_kemasan'   => 'required|string',
       'nama_produk'     => 'required|string',
       'kode_produksi'   => 'required|string',
       'jumlah'     => 'nullable|numeric',
       'jamur'      => 'nullable|numeric',
       'lendir'      => 'nullable|numeric',
       'klip_tajam'      => 'nullable|numeric',
       'pin_hole'      => 'nullable|numeric',
       'air_trap_pvdc'      => 'nullable|numeric',
       'air_trap_produk'      => 'nullable|numeric',
       'keriput'      => 'nullable|numeric',
       'bengkok'      => 'nullable|numeric',
       'non_kode'      => 'nullable|numeric',
       'over_lap'      => 'nullable|numeric',
       'kecil'      => 'nullable|numeric',
       'terjepit'      => 'nullable|numeric',
       'double_klip'      => 'nullable|numeric',
       'seal_halus'      => 'nullable|numeric',
       'basah'      => 'nullable|numeric',
       'dll'      => 'nullable|numeric',
       'catatan'    => 'nullable|string', 
   ]);

    $username_updated = Auth::user()->username ?? 'None';

    $updateData = [
        'date'            => $request->date,
        'shift'            => $request->shift,
        'jenis_sampel'    => $request->jenis_sampel,
        'jenis_kemasan'    => $request->jenis_kemasan,
        'nama_produk'     => $request->nama_produk,
        'kode_produksi'   => $request->kode_produksi,
        'jumlah'            => $request->jumlah,
        'jamur'   => $request->jamur,
        'lendir'   => $request->lendir,
        'klip_tajam'   => $request->klip_tajam,
        'pin_hole'   => $request->pin_hole,
        'air_trap_pvdc'   => $request->air_trap_pvdc,
        'air_trap_produk'   => $request->air_trap_produk,
        'keriput'   => $request->keriput,
        'bengkok'   => $request->bengkok,
        'non_kode'   => $request->non_kode,
        'over_lap'   => $request->over_lap,
        'kecil'   => $request->kecil,
        'terjepit'   => $request->terjepit,
        'double_klip'   => $request->double_klip,
        'seal_halus'   => $request->seal_halus,
        'basah'   => $request->basah,
        'dll'   => $request->dll,
        'catatan'      => $request->catatan,
        'username_updated' => $username_updated,
    ];

    $sampling->update($updateData);

    return redirect()->route('sampling.index')
    ->with('success', 'Data Sampling Produk berhasil diperbarui.');
}

public function edit(string $uuid)
{
    $sampling = Sampling::where('uuid', $uuid)->firstOrFail();
    $userPlant = Auth::user()->plant;
    $produks = Produk::where('plant', $userPlant)->get();

    return view('form.sampling.edit', compact('sampling', 'produks'));
}

public function edit_spv(Request $request, string $uuid)
{
    $sampling = Sampling::where('uuid', $uuid)->firstOrFail();

    $request->validate([
       'date'            => 'required|date',
       'shift'    => 'required|string',
       'jenis_sampel'    => 'required|string',
       'jenis_kemasan'   => 'required|string',
       'nama_produk'     => 'required|string',
       'kode_produksi'   => 'required|string',
       'jumlah'     => 'nullable|numeric',
       'jamur'      => 'nullable|numeric',
       'lendir'      => 'nullable|numeric',
       'klip_tajam'      => 'nullable|numeric',
       'pin_hole'      => 'nullable|numeric',
       'air_trap_pvdc'      => 'nullable|numeric',
       'air_trap_produk'      => 'nullable|numeric',
       'keriput'      => 'nullable|numeric',
       'bengkok'      => 'nullable|numeric',
       'non_kode'      => 'nullable|numeric',
       'over_lap'      => 'nullable|numeric',
       'kecil'      => 'nullable|numeric',
       'terjepit'      => 'nullable|numeric',
       'double_klip'      => 'nullable|numeric',
       'seal_halus'      => 'nullable|numeric',
       'basah'      => 'nullable|numeric',
       'dll'      => 'nullable|numeric',
       'catatan'    => 'nullable|string', 
   ]);

    $username_updated = Auth::user()->username ?? 'None';

    $updateData = [
        'date'            => $request->date,
        'shift'            => $request->shift,
        'jenis_sampel'    => $request->jenis_sampel,
        'jenis_kemasan'    => $request->jenis_kemasan,
        'nama_produk'     => $request->nama_produk,
        'kode_produksi'   => $request->kode_produksi,
        'jumlah'            => $request->jumlah,
        'jamur'   => $request->jamur,
        'lendir'   => $request->lendir,
        'klip_tajam'   => $request->klip_tajam,
        'pin_hole'   => $request->pin_hole,
        'air_trap_pvdc'   => $request->air_trap_pvdc,
        'air_trap_produk'   => $request->air_trap_produk,
        'keriput'   => $request->keriput,
        'bengkok'   => $request->bengkok,
        'non_kode'   => $request->non_kode,
        'over_lap'   => $request->over_lap,
        'kecil'   => $request->kecil,
        'terjepit'   => $request->terjepit,
        'double_klip'   => $request->double_klip,
        'seal_halus'   => $request->seal_halus,
        'basah'   => $request->basah,
        'dll'   => $request->dll,
        'catatan'      => $request->catatan,
        'username_updated' => $username_updated,
    ];

    $sampling->update($updateData);

    return redirect()->route('sampling.index')
    ->with('success', 'Data Sampling Produk berhasil diperbarui.');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');
    $userPlant  = Auth::user()->plant;

    $data = Sampling::query()
    ->where('plant', $userPlant)
    ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%")
            ->orWhere('jenis_kemasan', 'like', "%{$search}%");
        });
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    return view('form.sampling.index', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    $request->validate([
        'status_spv'  => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    $sampling = Sampling::where('uuid', $uuid)->firstOrFail();

    $sampling->update([
        'status_spv'  => $request->status_spv,
        'catatan_spv' => $request->catatan_spv,
        'nama_spv'    => Auth::user()->username,
        'tgl_update_spv' => now(),
    ]);

    return redirect()->route('sampling.index')
    ->with('success', 'Status Verifikasi Data Sampling Produk berhasil diperbarui.');
}

public function destroy($uuid)
{
    $sampling = Sampling::where('uuid', $uuid)->firstOrFail();
    $sampling->delete();
    return redirect()->route('sampling.index')->with('success', 'Sampling berhasil dihapus');
}

public function recyclebin()
{
    $sampling = Sampling::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.sampling.recyclebin', compact('sampling'));
}
public function restore($uuid)
{
    $sampling = Sampling::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sampling->restore();

    return redirect()->route('sampling.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $sampling = Sampling::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sampling->forceDelete();

    return redirect()->route('sampling.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    $date = $request->input('date');
    $shift = $request->input('shift');
    $nama_produk = $request->input('nama_produk');
    $userPlant = Auth::user()->plant;

    $samplings = Sampling::query()
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
    $pdf->SetTitle('Data Sampling Produk');
    $pdf->SetSubject('Data Sampling Produk');

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
    $html = view('reports.data-sampling-produk', compact('samplings', 'request'))->render();

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // Close and output PDF document (Inline/Preview)
    $pdf->Output('Data_Sampling_Produk_' . date('Ymd_His') . '.pdf', 'I');

    exit();
}
}
