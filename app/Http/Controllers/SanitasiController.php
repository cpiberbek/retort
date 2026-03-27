<?php

namespace App\Http\Controllers;

use App\Models\Sanitasi;
use App\Models\Area_sanitasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class SanitasiController extends Controller 
{
public function index(Request $request)
{
    $search    = $request->input('search');
    $date      = $request->input('date');
    $shift     = $request->input('shift');
    $userPlant = Auth::user()->plant;

    $data = Sanitasi::select('sanitasis.*', 'area_table.area as area_name', 'area_table.sub_area', 'area_table.bagian')
        ->where('sanitasis.plant', $userPlant)
        ->leftJoin('area_sanitasis as area_table', 'sanitasis.area', '=', 'area_table.uuid')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sanitasis.username', 'like', "%{$search}%")
                  ->orWhere('area_table.area', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('sanitasis.date', $date);
        })
        ->when($shift, function ($query) use ($shift) {
            $query->where('sanitasis.shift', $shift);
        })
        ->orderBy('sanitasis.date', 'desc')
        ->orderBy('sanitasis.shift', 'desc')
        ->orderBy('sanitasis.created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    return view('form.sanitasi.index', compact('data', 'search', 'date', 'shift'));
}
 public function create()
 {
    $userPlant = Auth::user()->plant;
    $areas = Area_sanitasi::where('plant', $userPlant)->get();

    return view('form.sanitasi.create', compact('areas'));
}

public function store(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'date'        => 'required|date',
        'shift'       => 'required|string',
        'area'        => 'required|string',
        'sub_area'    => 'required|string',
        'pemeriksaan' => 'nullable|array',
    ]);

    $areaSanitasi = \App\Models\Area_sanitasi::where('area', $request->area)
        ->where('sub_area', $request->sub_area)
        ->firstOrFail();

    $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()?->name ?? 'Produksi RTT'
        : 'Produksi RTT';

    Sanitasi::create([
        'date'                 => $request->date,
        'shift'                => $request->shift,
        'area'                 => $areaSanitasi->uuid,
        'username'             => $user->username ?? 'User RTM',
        'plant'                => $user->plant,
        'nama_produksi'        => $nama_produksi,
        'status_produksi'      => "1",
        'tgl_update_produksi'  => now()->addHour(),
        'status_spv'           => "0",
        'pemeriksaan'          => json_encode($request->input('pemeriksaan', []), JSON_UNESCAPED_UNICODE),
    ]);

    return redirect()->route('sanitasi.index')
        ->with('success', 'Kontrol Sanitasi berhasil disimpan');
}

public function update(string $uuid)
{
    $userPlant = Auth::user()->plant;

    $sanitasi = Sanitasi::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

    $areas = Area_sanitasi::where('plant', $userPlant)->get();

    $areaRecord = $areas->firstWhere('uuid', $sanitasi->area);

    $sanitasiAreaName = $areaRecord ? $areaRecord->area : null;
    $sanitasiSubArea  = $areaRecord ? $areaRecord->sub_area : null;
    $sanitasiBagian   = $areaRecord ? json_decode($areaRecord->bagian, true) : [];

    $sanitasiData = !empty($sanitasi->pemeriksaan)
        ? json_decode($sanitasi->pemeriksaan, true)
        : [];

    return view('form.sanitasi.update', compact(
        'sanitasi', 
        'sanitasiData', 
        'areas', 
        'sanitasiAreaName', 
        'sanitasiSubArea', 
        'sanitasiBagian'
    ));
}

public function update_qc(Request $request, string $uuid)
{
    $sanitasi = Sanitasi::where('uuid', $uuid)->firstOrFail();
    
    $username_updated = Auth::user()->username ?? 'User QC';


    $request->validate([
        'date'        => 'required|date',
        'shift'       => 'required|string',
        'area'        => 'required|string',
        'pemeriksaan' => 'nullable|array',
    ]);

    $data = [
        'date'        => $request->date,
        'shift'       => $request->shift,
        'area'        => $request->area,
        'pemeriksaan' => json_encode($request->input('pemeriksaan', []), JSON_UNESCAPED_UNICODE),
        'username_updated' => $username_updated,
        'updated_at'  => now(),
    ];

    $sanitasi->update($data);

    return redirect()->route('sanitasi.index')
    ->with('success', 'Data QC berhasil diperbarui');
}

public function edit(string $uuid)
{
    $userPlant = Auth::user()->plant;

    $sanitasi = Sanitasi::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

    $areas = Area_sanitasi::where('plant', $userPlant)->get();

    $areaRecord = $areas->firstWhere('uuid', $sanitasi->area);

    $sanitasiAreaName = $areaRecord ? $areaRecord->area : null;
    $sanitasiSubArea  = $areaRecord ? $areaRecord->sub_area : null;
    $sanitasiBagian   = $areaRecord ? json_decode($areaRecord->bagian, true) : [];

    $sanitasiData = !empty($sanitasi->pemeriksaan)
        ? json_decode($sanitasi->pemeriksaan, true)
        : [];

    return view('form.sanitasi.edit', compact(
        'sanitasi', 
        'sanitasiData', 
        'areas', 
        'sanitasiAreaName', 
        'sanitasiSubArea', 
        'sanitasiBagian'
    ));
}

public function edit_spv(Request $request, string $uuid)
{
    $sanitasi = Sanitasi::where('uuid', $uuid)->firstOrFail();

    $request->validate([
        'date'        => 'required|date',
        'shift'       => 'required|string',
        'area'        => 'required|string',
        'pemeriksaan' => 'nullable|array',
    ]);

    $data = [
        'date'        => $request->date,
        'shift'       => $request->shift,
        'area'        => $request->area,
        'pemeriksaan' => json_encode($request->input('pemeriksaan', []), JSON_UNESCAPED_UNICODE),
        'updated_at'  => now(),
    ];

    $sanitasi->update($data);

    return redirect()->route('sanitasi.index')
    ->with('success', 'Data QC berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date       = $request->input('date');
    $userPlant  = Auth::user()->plant;

    $data = Sanitasi::query()
    ->where('plant', $userPlant)
    ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        });
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    return view('form.sanitasi.index', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    $request->validate([
        'status_spv'  => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    $sanitasi = Sanitasi::where('uuid', $uuid)->firstOrFail();

    $sanitasi->update([
        'status_spv'      => $request->status_spv,
        'catatan_spv'     => $request->catatan_spv,
        'nama_spv'        => Auth::user()->username,
        'tgl_update_spv'  => now(),
    ]);

    return redirect()->route('sanitasi.index')
    ->with('success', 'Status Verifikasi Pengecekan sanitasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $sanitasi = Sanitasi::where('uuid', $uuid)->firstOrFail();
    $sanitasi->delete();
    return redirect()->route('sanitasi.index')->with('success', 'Kontrol Sanitasi berhasil dihapus');
}

public function recyclebin()
{
    $sanitasi = Sanitasi::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.sanitasi.recyclebin', compact('sanitasi'));
}
public function restore($uuid)
{
    $sanitasi = Sanitasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sanitasi->restore();

    return redirect()->route('sanitasi.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $sanitasi = Sanitasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sanitasi->forceDelete();

    return redirect()->route('sanitasi.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    $date = $request->input('date');
    $shift = $request->input('shift');
    $userPlant = Auth::user()->plant;

    $sanitasies = Sanitasi::query()
    ->where('plant', $userPlant)
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->when($shift, function ($query) use ($shift) {
        $query->where('shift', $shift);
    })
    ->orderBy('date', 'asc')
    ->orderBy('shift', 'asc')
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
    $pdf->SetTitle('Kontrol Sanitasi');
    $pdf->SetSubject('Kontrol Sanitasi');

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
    $pdf->AddPage('P', 'A3'); // Landscape A3 for many columns

    // Convert the Blade view to HTML
    $html = view('reports.kontrol-sanitasi', compact('sanitasies', 'request'))->render();

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // Generate filename with filter info
    $filename = 'Kontrol_Sanitasi';
    if ($date) {
        $filename .= '_' . date('Ymd', strtotime($date));
    }
    if ($shift) {
        $filename .= '_Shift_' . $shift;
    }
    $filename .= '_' . date('His') . '.pdf';

    // Close and output PDF document (Inline/Preview)
    $pdf->Output($filename, 'I');

    exit();
}
}
