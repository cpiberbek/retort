<?php

namespace App\Http\Controllers;

use App\Models\Retain_rte;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class Retain_rteController extends Controller
{

    public function index(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $userPlant = Auth::user()->plant;

        $data = Retain_rte::query()
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

        return view('form.retain_rte.index', compact('data', 'search', 'date'));
    }

    public function exportPdf(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');
        $userPlant = Auth::user()->plant;

        $data = Retain_rte::query()
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
        $pdf->AddPage('L', 'A4'); 

        $html = view('reports.retain-rte', compact('data', 'request'))->render();
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        $pdf->Output('Pemeriksaan_Retain_RTE_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.retain_rte.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;

        $request->validate([
            'date'          => 'required|date',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'analisa'       => 'nullable|array',
        ]);

        $data = $request->only([
            'date', 'nama_produk', 'kode_produksi'
        ]);

        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['status_spv']          = "0";
        $data['analisa']             = json_encode($request->input('analisa', []), JSON_UNESCAPED_UNICODE);

        Retain_rte::create($data);

        return redirect()->route('retain_rte.index')->with('success', 'Pemeriksaan Sampel Retain RTE berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $retain_rte = Retain_rte::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $retainData = !empty($retain_rte->analisa)
        ? json_decode($retain_rte->analisa, true)
        : [];

        return view('form.retain_rte.update', compact('retain_rte', 'produks', 'retainData'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $retain_rte = Retain_rte::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User QC';


        $request->validate([
            'date'          => 'required|date',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'analisa'       => 'nullable|array',
        ]);

        $data = [
            'date'             => $request->date,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'username_updated' => $username_updated,
            'analisa'          => json_encode($request->input('analisa', []), JSON_UNESCAPED_UNICODE),
        ];

        $retain_rte->update($data);

        return redirect()->route('retain_rte.index')->with('success', 'Data Pemeriksaan Sampel Retain RTE berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $retain_rte = Retain_rte::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $retainData = !empty($retain_rte->analisa)
        ? json_decode($retain_rte->analisa, true)
        : [];

        return view('form.retain_rte.edit', compact('retain_rte', 'produks', 'retainData'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $retain_rte = Retain_rte::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'          => 'required|date',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'analisa'       => 'nullable|array',
        ]);

        $data = [
            'date'             => $request->date,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'analisa'          => json_encode($request->input('analisa', []), JSON_UNESCAPED_UNICODE),
        ];

        $retain_rte->update($data);

        return redirect()->route('retain_rte.index')->with('success', 'Data Pemeriksaan Sampel Retain RTE berhasil diperbarui');
    }

    public function verification(Request $request)
    {
     $search    = $request->input('search');
     $date      = $request->input('date');
     $userPlant = Auth::user()->plant;

     $data = Retain_rte::query()
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

     return view('form.retain_rte.index', compact('data', 'search', 'date' ));
 }

 public function updateVerification(Request $request, $uuid)
 {
    $request->validate([
        'status_spv'  => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    $retain_rte = Retain_rte::where('uuid', $uuid)->firstOrFail();

    $retain_rte->update([
        'status_spv'      => $request->status_spv,
        'catatan_spv'     => $request->catatan_spv,
        'nama_spv'        => Auth::user()->username,
        'tgl_update_spv'  => now(),
    ]);

    return redirect()->route('retain_rte.index')
    ->with('success', 'Status Verifikasi Pemeriksaan Sampel Retain RTE berhasil diperbarui.');
}

public function destroy($uuid)
{
    $retain_rte = Retain_rte::where('uuid', $uuid)->firstOrFail();
    $retain_rte->delete();
    return redirect()->route('retain_rte.index')->with('success', 'Retain RTE berhasil dihapus');
}

public function recyclebin()
{
    $retain_rte = Retain_rte::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.retain_rte.recyclebin', compact('retain_rte'));
}
public function restore($uuid)
{
    $retain_rte = Retain_rte::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $retain_rte->restore();

    return redirect()->route('retain_rte.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $retain_rte = Retain_rte::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $retain_rte->forceDelete();

    return redirect()->route('retain_rte.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}
}
