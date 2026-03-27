<?php

namespace App\Http\Controllers;

use App\Models\Pemasakan_rte;
use App\Models\Produk;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class Pemasakan_rteController extends Controller
{
    public function index(Request $request)
    {
     $search    = $request->input('search');
     $date      = $request->input('date');
     $userPlant  = Auth::user()->plant;

     $data = Pemasakan_rte::query()
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

     return view('form.pemasakan_rte.index', compact('data', 'search', 'date'));
 }

 public function exportPdf(Request $request)
 {
    $search = $request->input('search');
    $date   = $request->input('date');
    $userPlant = Auth::user()->plant;

    $data = Pemasakan_rte::query()
    ->where('plant', $userPlant)
    ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        });
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'asc')
    ->get();

    if (ob_get_length()) {
        ob_end_clean();
    }

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Company');
    $pdf->SetTitle('Pemeriksaan Retain RTE');
    $pdf->SetSubject('Pemeriksaan Retain RTE');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    $pdf->SetFont('helvetica', '', 10);
    $pdf->AddPage('P', 'A4'); 

    $html = view('reports.pemasakan-rte', compact('data', 'request'))->render();
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    $pdf->Output('Pemeriksaan_Retain_RTE_' . date('Ymd_His') . '.pdf', 'I');

    exit();
}

public function create()
{
    $userPlant = Auth::user()->plant;
    $produks = Produk::where('plant', $userPlant)->get();
    $list_chambers = Mesin::where('plant', $userPlant)
    ->where('jenis_mesin', 'Chamber')
    ->orderBy('nama_mesin')
    ->get(['uuid', 'nama_mesin']);

    return view('form.pemasakan_rte.create', compact('produks', 'list_chambers'));
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
        'kode_produksi' => 'required|string',
        'no_chamber'    => 'required',
        'berat_produk'  => 'required|numeric',
        'suhu_produk'   => 'required|numeric',
        'jumlah_tray'   => 'required|string',
        'total_reject'  => 'nullable|numeric',
        'catatan'       => 'nullable|string',
        'cooking'       => 'nullable|array',
    ]);

    $data = $request->only([
        'date', 'shift', 'nama_produk', 'kode_produksi',
        'no_chamber', 'berat_produk', 'suhu_produk', 'jumlah_tray',
        'total_reject', 'catatan',
    ]);

    $data['username']            = $username;
    $data['plant']               = $userPlant;
    $data['nama_produksi']       = $nama_produksi;
    $data['status_produksi']     = "1";
    $data['tgl_update_produksi'] = now()->addHour();
    $data['status_spv']          = "0";
    $data['cooking']             = json_encode($request->input('cooking', []), JSON_UNESCAPED_UNICODE);

    Pemasakan_rte::create($data);

    return redirect()->route('pemasakan_rte.index')->with('success', 'Pengecekan Pemasakan RTE berhasil disimpan');
}

public function update(string $uuid)
{
    $pemasakan_rte = Pemasakan_rte::where('uuid', $uuid)->firstOrFail();
    $userPlant = Auth::user()->plant;
    $produks = Produk::where('plant', $userPlant)->get();
    $list_chambers = Mesin::where('plant', $userPlant)
    ->where('jenis_mesin', 'Chamber')
    ->orderBy('nama_mesin')
    ->get(['uuid', 'nama_mesin']);

    $pemasakanData = !empty($pemasakan_rte->cooking)
    ? json_decode($pemasakan_rte->cooking, true)
    : [];

    return view('form.pemasakan_rte.update', compact('pemasakan_rte', 'produks', 'pemasakanData', 'list_chambers'));
}

public function update_qc(Request $request, string $uuid)
{
    $pemasakan_rte = Pemasakan_rte::where('uuid', $uuid)->firstOrFail();
    $username_updated = Auth::user()->username ?? 'User QC';

    $request->validate([
        'date'          => 'required|date',
        'shift'         => 'required',
        'nama_produk'   => 'required',
        'kode_produksi' => 'required|string',
        'no_chamber'    => 'required',
        'berat_produk'  => 'required|numeric',
        'suhu_produk'   => 'required|numeric',
        'jumlah_tray'   => 'required|string',
        'total_reject'  => 'nullable|numeric',
        'catatan'       => 'nullable|string',
        'cooking'       => 'nullable|array',
    ]);

    $data = [
        'date'             => $request->date,
        'shift'            => $request->shift,
        'nama_produk'      => $request->nama_produk,
        'kode_produksi'    => $request->kode_produksi,
        'no_chamber'       => $request->no_chamber,
        'berat_produk'     => $request->berat_produk,
        'suhu_produk'      => $request->suhu_produk,
        'jumlah_tray'      => $request->jumlah_tray,
        'total_reject'     => $request->total_reject,
        'catatan'          => $request->catatan,
        'username_updated' => $username_updated,
        'cooking'          => json_encode($request->input('cooking', []), JSON_UNESCAPED_UNICODE),
    ];

    $pemasakan_rte->update($data);

    return redirect()->route('pemasakan_rte.index')->with('success', 'Pengecekan Pemasakan RTE berhasil diperbarui');
}

/** ====================== SPV ====================== **/
public function edit(string $uuid)
{
    $pemasakan_rte = Pemasakan_rte::where('uuid', $uuid)->firstOrFail();
    $userPlant = Auth::user()->plant;
    $produks = Produk::where('plant', $userPlant)->get();
    $list_chambers = Mesin::where('plant', $userPlant)
    ->where('jenis_mesin', 'Chamber')
    ->orderBy('nama_mesin')
    ->get(['uuid', 'nama_mesin']);

    $pemasakanData = !empty($pemasakan_rte->cooking)
    ? json_decode($pemasakan_rte->cooking, true)
    : [];

    return view('form.pemasakan_rte.edit', compact('pemasakan_rte', 'produks', 'pemasakanData', 'list_chambers'));
}

public function edit_spv(Request $request, string $uuid)
{
    $pemasakan_rte = Pemasakan_rte::where('uuid', $uuid)->firstOrFail();

    $request->validate([
        'date'          => 'required|date',
        'shift'         => 'required',
        'nama_produk'   => 'required',
        'kode_produksi' => 'required|string',
        'no_chamber'    => 'required',
        'berat_produk'  => 'required|numeric',
        'suhu_produk'   => 'required|numeric',
        'jumlah_tray'   => 'required|string',
        'total_reject'  => 'nullable|numeric',
        'catatan'       => 'nullable|string',
        'cooking'       => 'nullable|array',
    ]);

    $pemasakan_rte->update([
        'date'             => $request->date,
        'shift'            => $request->shift,
        'nama_produk'      => $request->nama_produk,
        'kode_produksi'    => $request->kode_produksi,
        'no_chamber'       => $request->no_chamber,
        'berat_produk'     => $request->berat_produk,
        'suhu_produk'      => $request->suhu_produk,
        'jumlah_tray'      => $request->jumlah_tray,
        'total_reject'     => $request->total_reject,
        'catatan'          => $request->catatan,
        'cooking'          => json_encode($request->input('cooking', []), JSON_UNESCAPED_UNICODE),
    ]);

    return redirect()->route('pemasakan_rte.index')->with('success', 'Pengecekan Pemasakan RTE berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date       = $request->input('date');
    $userPlant  = Auth::user()->plant;

    $data = Pemasakan_rte::query()
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

    return view('form.pemasakan_rte.index', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    $request->validate([
        'status_spv'  => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    $pemasakan_rte = Pemasakan_rte::where('uuid', $uuid)->firstOrFail();

    $pemasakan_rte->update([
        'status_spv'      => $request->status_spv,
        'catatan_spv'     => $request->catatan_spv,
        'nama_spv'        => Auth::user()->username,
        'tgl_update_spv'  => now(),
    ]);

    return redirect()->route('pemasakan_rte.index')
    ->with('success', 'Status Verifikasi Pengecekan Pemasakan RTE berhasil diperbarui.');
}

public function destroy($uuid)
{
    $pemasakan_rte = Pemasakan_rte::where('uuid', $uuid)->firstOrFail();
    $pemasakan_rte->delete();
    return redirect()->route('pemasakan_rte.index')->with('success', 'Pemasakan RTE berhasil dihapus');
}

public function recyclebin()
{
    $pemasakan_rte = Pemasakan_rte::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.pemasakan_rte.recyclebin', compact('pemasakan_rte'));
}
public function restore($uuid)
{
    $pemasakan_rte = Pemasakan_rte::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $pemasakan_rte->restore();

    return redirect()->route('pemasakan_rte.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $pemasakan_rte = Pemasakan_rte::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $pemasakan_rte->forceDelete();

    return redirect()->route('pemasakan_rte.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}
}
