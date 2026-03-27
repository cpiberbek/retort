<?php

namespace App\Http\Controllers;

use App\Models\Prepacking;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class PrepackingController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Prepacking::query()
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

        return view('form.prepacking.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.prepacking.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;

        $request->validate([
            'date'          => 'required|date',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'conveyor'      => 'nullable|string',
            'berat_produk'  => 'nullable|array',
            'suhu_produk'   => 'nullable|array',
            'kondisi_produk'=> 'nullable|array',
            'catatan'       => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'nama_produk', 'kode_produksi', 'catatan', 'conveyor',
        ]);

        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['status_spv']          = "0";
        $data['berat_produk']        = json_encode($request->input('berat_produk', []), JSON_UNESCAPED_UNICODE);
        $data['suhu_produk']         = json_encode($request->input('suhu_produk', []), JSON_UNESCAPED_UNICODE);
        $data['kondisi_produk']      = json_encode($request->input('kondisi_produk', []), JSON_UNESCAPED_UNICODE);

        Prepacking::create($data);

        return redirect()->route('prepacking.index')->with('success', 'Pengecekan Pre Packing berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $beratData = !empty($prepacking->berat_produk)
        ? json_decode($prepacking->berat_produk, true)
        : [];
        $suhuData = !empty($prepacking->suhu_produk)
        ? json_decode($prepacking->suhu_produk, true)
        : [];
        $kondisiData = !empty($prepacking->kondisi_produk)
        ? json_decode($prepacking->kondisi_produk, true)
        : [];

        return view('form.prepacking.update', compact(
            'prepacking', 'produks', 'beratData', 'suhuData', 'kondisiData'
        ));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User QC';

        $request->validate([
            'date'          => 'required|date',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'conveyor'      => 'nullable|string',
            'berat_produk'  => 'nullable|array',
            'suhu_produk'   => 'nullable|array',
            'kondisi_produk'=> 'nullable|array',
            'catatan'       => 'nullable|string',
        ]);

        $data = [
            'date'             => $request->date,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'conveyor'         => $request->conveyor,
            'berat_produk'     => json_encode($request->input('berat_produk', []), JSON_UNESCAPED_UNICODE),
            'suhu_produk'      => json_encode($request->input('suhu_produk', []), JSON_UNESCAPED_UNICODE),
            'kondisi_produk'   => json_encode($request->input('kondisi_produk', []), JSON_UNESCAPED_UNICODE),
        ];

        $prepacking->update($data);

        return redirect()->route('prepacking.index')->with('success', 'Data Pengecekan Prepacking berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
      $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();
      $userPlant = Auth::user()->plant;
      $produks = Produk::where('plant', $userPlant)->get();

      $beratData = !empty($prepacking->berat_produk)
      ? json_decode($prepacking->berat_produk, true)
      : [];
      $suhuData = !empty($prepacking->suhu_produk)
      ? json_decode($prepacking->suhu_produk, true)
      : [];
      $kondisiData = !empty($prepacking->kondisi_produk)
      ? json_decode($prepacking->kondisi_produk, true)
      : [];

      return view('form.prepacking.edit', compact('prepacking', 'produks', 'beratData', 'suhuData', 'kondisiData'));
  }

  public function edit_spv(Request $request, string $uuid)
  {
    $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();

    $request->validate([
        'date'          => 'required|date',
        'nama_produk'   => 'required',
        'kode_produksi' => 'required|string',
        'conveyor'      => 'nullable|string',
        'berat_produk'  => 'nullable|array',
        'suhu_produk'   => 'nullable|array',
        'kondisi_produk'=> 'nullable|array',
        'catatan'       => 'nullable|string',
    ]);

    $data = [
        'date'             => $request->date,
        'nama_produk'      => $request->nama_produk,
        'kode_produksi'    => $request->kode_produksi,
        'catatan'          => $request->catatan,
        'conveyor'         => $request->conveyor,
        'berat_produk'     => json_encode($request->input('berat_produk', []), JSON_UNESCAPED_UNICODE),
        'suhu_produk'      => json_encode($request->input('suhu_produk', []), JSON_UNESCAPED_UNICODE),
        'kondisi_produk'   => json_encode($request->input('kondisi_produk', []), JSON_UNESCAPED_UNICODE),
    ];
    
    $prepacking->update($data);

    return redirect()->route('prepacking.index')->with('success', 'Data Pengecekan Prepacking berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date       = $request->input('date');
    $userPlant  = Auth::user()->plant;

    $data = Prepacking::query()
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

    return view('form.prepacking.index', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    $request->validate([
        'status_spv'  => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();

    $prepacking->update([
        'status_spv'      => $request->status_spv,
        'catatan_spv'     => $request->catatan_spv,
        'nama_spv'        => Auth::user()->username,
        'tgl_update_spv'  => now(),
    ]);

    return redirect()->route('prepacking.index')
    ->with('success', 'Status Verifikasi Pengecekan Pre Packing berhasil diperbarui.');
}

public function destroy($uuid)
{
    $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();
    $prepacking->delete();
    return redirect()->route('prepacking.index')->with('success', 'Prepacking berhasil dihapus');
}

public function recyclebin()
{
    $prepacking = Prepacking::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.prepacking.recyclebin', compact('prepacking'));
}
public function restore($uuid)
{
    $prepacking = Prepacking::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $prepacking->restore();

    return redirect()->route('prepacking.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $prepacking = Prepacking::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $prepacking->forceDelete();

    return redirect()->route('prepacking.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    $date = $request->input('date');
    $userPlant = Auth::user()->plant;

    $prepackings = Prepacking::query()
    ->where('plant', $userPlant)
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
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
    $pdf->SetTitle('Pengecekan Pre Packing');
    $pdf->SetSubject('Pengecekan Pre Packing');

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
    $html = view('reports.pengecekan-pre-packing', compact('prepackings', 'request'))->render();

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // Close and output PDF document (Inline/Preview)
    $pdf->Output('Pengecekan_Pre_Packing_' . date('Ymd_His') . '.pdf', 'I');

    exit();
}
}
