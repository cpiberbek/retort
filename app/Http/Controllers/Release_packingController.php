<?php

namespace App\Http\Controllers;

use App\Models\Release_packing;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class Release_packingController extends Controller
{
    public function index(Request $request)
    {
        $search       = $request->input('search');
        $date         = $request->input('date');
        $jenis_kemasan = $request->input('jenis_kemasan');
        $userPlant    = Auth::user()->plant;

        $data = Release_packing::query()
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
        ->when($jenis_kemasan, function ($query) use ($jenis_kemasan) {
            $query->where('jenis_kemasan', $jenis_kemasan);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.release_packing.index', compact('data', 'search', 'date', 'jenis_kemasan'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.release_packing.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;

        $request->validate([
            'date'                   => 'required|date',
            'jenis_kemasan'          => 'required|string',
            'nama_produk'            => 'required|string',
            'kode_produksi'          => 'required|string',
            'expired_date'           => 'required',
            'no_palet'               => 'required|string',
            'release'                => 'nullable|integer',
            'keterangan'             => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'jenis_kemasan', 'nama_produk', 'kode_produksi', 'expired_date', 'no_palet', 'release', 'keterangan'
        ]);

    // Tambahan default
        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['status_spv']          = "0";

        Release_packing::create($data);

        return redirect()->route('release_packing.index')->with('success', 'Data Release Packing disimpan');
    }

    public function update(string $uuid)
    {
        $release_packing = Release_packing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.release_packing.update', compact('release_packing', 'produks'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $release_packing = Release_packing::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User QC';

        $request->validate([
            'date'                   => 'required|date',
            'jenis_kemasan'          => 'required|string',
            'nama_produk'            => 'required|string',
            'kode_produksi'          => 'required|string',
            'expired_date'           => 'required',
            'no_palet'               => 'required|string',
            'release'                => 'nullable|integer',
            'keterangan'             => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'jenis_kemasan', 'nama_produk', 'kode_produksi', 'expired_date', 'no_palet', 'release', 'keterangan'
        ]);

        $data['username_updated'] = $username_updated;

        $release_packing->update($data);

        return redirect()->route('release_packing.index')->with('success', 'Data Release Packing berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $release_packing = Release_packing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.release_packing.edit', compact('release_packing', 'produks'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $release_packing = Release_packing::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'                   => 'required|date',
            'jenis_kemasan'          => 'required|string',
            'nama_produk'            => 'required|string',
            'kode_produksi'          => 'required|string',
            'expired_date'           => 'required',
            'no_palet'               => 'required|string',
            'release'                => 'nullable|integer',
            'keterangan'             => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'jenis_kemasan', 'nama_produk', 'kode_produksi', 'expired_date', 'no_palet', 'release', 'keterangan'
        ]);

        $release_packing->update($data);

        return redirect()->route('release_packing.index')->with('success', 'Data Release Packing berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Release_packing::query()
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

        return view('form.release_packing.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $release_packing = Release_packing::where('uuid', $uuid)->firstOrFail();

        $release_packing->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('release_packing.index')
        ->with('success', 'Status Verifikasi Data Release Packing diperbarui.');
    }

    public function destroy($uuid)
    {
        $release_packing = Release_packing::where('uuid', $uuid)->firstOrFail();
        $release_packing->delete();
        return redirect()->route('release_packing.index')->with('success', 'Release_packing berhasil dihapus');
    }

    public function recyclebin()
    {
        $release_packing = Release_packing::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.release_packing.recyclebin', compact('release_packing'));
    }
    public function restore($uuid)
    {
        $release_packing = Release_packing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $release_packing->restore();

        return redirect()->route('release_packing.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $release_packing = Release_packing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $release_packing->forceDelete();

        return redirect()->route('release_packing.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        $date         = $request->input('date');
        $jenis_kemasan = $request->input('jenis_kemasan');
        $userPlant    = Auth::user()->plant;

        $release_packings = Release_packing::query()
        ->where('plant', $userPlant)
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->when($jenis_kemasan, function ($query) use ($jenis_kemasan) {
            $query->where('jenis_kemasan', $jenis_kemasan);
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
        $pdf->SetTitle('Data Release Packing');
        $pdf->SetSubject('Data Release Packing');

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
        $html = view('reports.data-release-packing', compact('release_packings', 'request'))->render();

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document (Inline/Preview)
        $pdf->Output('Data_Release_Packing_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }
}
