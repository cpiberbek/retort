<?php

namespace App\Http\Controllers;

use App\Models\Sampling_fg;
use App\Models\Produk;
use App\Models\Operator;
use App\Models\Release_packing;
use App\Models\Mincing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCPDF;

class Sampling_fgController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant  = Auth::user()->plant;

        $data = Sampling_fg::query() 
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
        ->when($shift, function ($query) use ($shift) { 
            $query->where('shift', $shift);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.sampling_fg.index', compact('data', 'search', 'date','shift'));
    }

    public function exportPdf(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        // Ambil Data Tanpa Pagination
        $items = Sampling_fg::query() 
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
        ->when($shift, function ($query) use ($shift) {
            $query->where('shift', $shift);
        })
        ->orderBy('date', 'asc')
        ->orderBy('shift', 'asc')
        ->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // Setup PDF (Landscape A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Laporan Sampling Finish Good');
        
        // Remove Default Header/Footer
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        // Set Margins (Tipis 5mm)
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);
        $pdf->SetFont('helvetica', '', 7); // Font Kecil (7pt) karena kolom BANYAK

        $pdf->AddPage();

        // Render View
        // Pastikan Anda membuat file: resources/views/reports/sampling_fg.blade.php
        $html = view('reports.sampling_fg', compact('items', 'request'))->render();

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Laporan_Sampling_FG_' . date('d-m-Y_His') . '.pdf', 'I');
        exit();
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $koordinators  = Operator::where('plant', $userPlant)
        ->where('bagian', 'Koordinator')
        ->orderBy('nama_karyawan')
        ->get();

        return view('form.sampling_fg.create', compact('produks', 'koordinators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required|string',
            'palet'           => 'required|string',
            'nama_produk'     => 'required|string',
            'kode_produksi'   => 'required|string',
            'exp_date'        => 'required|date',
            'pukul'           => 'required',
            'kalibrasi'       => 'nullable|string',
            'berat_produk'    => 'nullable|integer',
            'keterangan'      => 'nullable|string',
            'isi_per_box'     => 'nullable|integer',
            'kemasan'         => 'nullable|string',
            'jumlah_box'      => 'nullable|integer',
            'release'         => 'nullable|integer',
            'reject'          => 'nullable|integer',
            'hold'            => 'nullable|integer',
            'item_mutu'       => 'nullable|string',
            'catatan'         => 'nullable|string', 
            'nama_koordinator'=> 'nullable|string',
        ]);

        $username  = Auth::user()->username ?? 'None';
        $userPlant = Auth::user()->plant;

        $data = [
            'date'            => $request->date,
            'shift'           => $request->shift,
            'palet'           => $request->palet,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'exp_date'        => $request->exp_date,
            'pukul'           => $request->pukul,
            'kalibrasi'       => $request->kalibrasi,
            'berat_produk'    => $request->berat_produk,
            'keterangan'      => $request->keterangan,
            'isi_per_box'     => $request->isi_per_box,
            'kemasan'         => $request->kemasan,
            'jumlah_box'      => $request->jumlah_box,
            'release'         => $request->release,
            'reject'          => $request->reject,
            'hold'            => $request->hold,
            'item_mutu'       => $request->item_mutu,
            'catatan'         => $request->catatan,
            'nama_koordinator'=> $request->nama_koordinator,
            'username'        => $username,
            'plant'           => $userPlant,
            'status_spv'      => "0",
            'status_koordinator'  => "1",
            'tgl_update_koordinator' => now(),
        ];

        Sampling_fg::create($data);

        return redirect()->route('sampling_fg.index')
        ->with('success', 'Data Pemeriksaan Proses Sampling Finish Good berhasil disimpan.');
    }

    public function update(string $uuid)
    {
        $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $koordinators  = Operator::where('plant', $userPlant)
        ->where('bagian', 'Koordinator')
        ->orderBy('nama_karyawan')
        ->get();

        return view('form.sampling_fg.update', compact('sampling_fg', 'produks', 'koordinators'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();

        $request->validate([
           'date'            => 'required|date',
           'shift'           => 'required|string',
           'palet'           => 'required|string',
           'nama_produk'     => 'required|string',
           'kode_produksi'   => 'required|string',
           'exp_date'        => 'required|date',
           'pukul'           => 'required',
           'kalibrasi'       => 'nullable|string',
           'berat_produk'    => 'nullable|integer',
           'keterangan'      => 'nullable|string',
           'isi_per_box'     => 'nullable|integer',
           'kemasan'         => 'nullable|string',
           'jumlah_box'      => 'nullable|integer',
           'release'         => 'nullable|integer',
           'reject'          => 'nullable|integer',
           'hold'            => 'nullable|integer',
           'item_mutu'       => 'nullable|string',
           'catatan'         => 'nullable|string', 
           'nama_koordinator'=> 'nullable|string',
       ]);

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
           'date'            => $request->date,
           'shift'           => $request->shift,
           'palet'           => $request->palet,
           'nama_produk'     => $request->nama_produk,
           'kode_produksi'   => $request->kode_produksi,
           'exp_date'        => $request->exp_date,
           'pukul'           => $request->pukul,
           'kalibrasi'       => $request->kalibrasi,
           'berat_produk'    => $request->berat_produk,
           'keterangan'      => $request->keterangan,
           'isi_per_box'     => $request->isi_per_box,
           'kemasan'         => $request->kemasan,
           'jumlah_box'      => $request->jumlah_box,
           'release'         => $request->release,
           'reject'          => $request->reject,
           'hold'            => $request->hold,
           'item_mutu'       => $request->item_mutu,
           'catatan'         => $request->catatan,
           'nama_koordinator'=> $request->nama_koordinator,
           'username_updated'=> $username_updated,
           'status_koordinator'  => "1",
           'tgl_update_koordinator' => now(),
       ];

       $sampling_fg->update($updateData);

       return redirect()->route('sampling_fg.index')
       ->with('success', 'Pemeriksaan Proses sampling_fg Finish Good berhasil diperbarui.');
   }

   public function edit(string $uuid)
   {
    $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();
    $userPlant = Auth::user()->plant;
    $produks = Produk::where('plant', $userPlant)->get();

    $koordinators  = Operator::where('plant', $userPlant)
    ->where('bagian', 'Koordinator')
    ->orderBy('nama_karyawan')
    ->get();

    return view('form.sampling_fg.edit', compact('sampling_fg', 'produks', 'koordinators'));
}

public function edit_spv(Request $request, string $uuid)
{
    $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();

    $request->validate([
       'date'            => 'required|date',
       'shift'           => 'required|string',
       'palet'           => 'required|string',
       'nama_produk'     => 'required|string',
       'kode_produksi'   => 'required|string',
       'exp_date'        => 'required|date',
       'pukul'           => 'required',
       'kalibrasi'       => 'nullable|string',
       'berat_produk'    => 'nullable|integer',
       'keterangan'      => 'nullable|string',
       'isi_per_box'     => 'nullable|integer',
       'kemasan'         => 'nullable|string',
       'jumlah_box'      => 'nullable|integer',
       'release'         => 'nullable|integer',
       'reject'          => 'nullable|integer',
       'hold'            => 'nullable|integer',
       'item_mutu'       => 'nullable|string',
       'catatan'         => 'nullable|string', 
       'nama_koordinator'=> 'nullable|string',
   ]);

    $username_updated = Auth::user()->username ?? 'None';

    $updateData = [
       'date'            => $request->date,
       'shift'           => $request->shift,
       'palet'           => $request->palet,
       'nama_produk'     => $request->nama_produk,
       'kode_produksi'   => $request->kode_produksi,
       'exp_date'        => $request->exp_date,
       'pukul'           => $request->pukul,
       'kalibrasi'       => $request->kalibrasi,
       'berat_produk'    => $request->berat_produk,
       'keterangan'      => $request->keterangan,
       'isi_per_box'     => $request->isi_per_box,
       'kemasan'         => $request->kemasan,
       'jumlah_box'      => $request->jumlah_box,
       'release'         => $request->release,
       'reject'          => $request->reject,
       'hold'            => $request->hold,
       'item_mutu'       => $request->item_mutu,
       'catatan'         => $request->catatan,
       'nama_koordinator'=> $request->nama_koordinator,
       'username_updated'=> $username_updated,
       'status_koordinator'  => "1",
       'tgl_update_koordinator' => now(),
   ];

   $sampling_fg->update($updateData);

   return redirect()->route('sampling_fg.index')
   ->with('success', 'Pemeriksaan Proses sampling_fg Finish Good berhasil diperbarui.');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date       = $request->input('date');
    $userPlant  = Auth::user()->plant;

    $data = Sampling_fg::query() 
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

    return view('form.sampling_fg.index', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    $request->validate([
        'status_spv'  => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();

    $sampling_fg->update([
        'status_spv'  => $request->status_spv,
        'catatan_spv' => $request->catatan_spv,
        'nama_spv'    => Auth::user()->username,
        'tgl_update_spv' => now(),
    ]);

    return redirect()->route('sampling_fg.index')
    ->with('success', 'Status Verifikasi Pemeriksaan Proses sampling_fg Finish Good berhasil diperbarui.');
}

public function destroy($uuid)
{
    $sampling_fg = Sampling_fg::where('uuid', $uuid)->firstOrFail();
    $sampling_fg->delete();
    return redirect()->route('sampling_fg.index')->with('success', 'Sampling FG berhasil dihapus');
}

public function recyclebin()
{
    $sampling_fg = Sampling_fg::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.sampling_fg.recyclebin', compact('sampling_fg'));
}
public function restore($uuid)
{
    $sampling_fg = Sampling_fg::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sampling_fg->restore();

    return redirect()->route('sampling_fg.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $sampling_fg = Sampling_fg::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sampling_fg->forceDelete();

    return redirect()->route('sampling_fg.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function getJumlahBox(Request $request)
{
    $nama_produk   = $request->input('nama_produk');
    $kode_produksi = $request->input('kode_produksi');

    if (!$nama_produk || !$kode_produksi) {
        return response()->json([
            'total_box' => 0
        ]);
    }

    // mencari data di tabel mincings soalnya uuid kode produksinya dari sini
    $mincing = Mincing::where('kode_produksi', $kode_produksi)
        ->where('nama_produk', $nama_produk)
        ->first();

    if (!$mincing) {
        return response()->json([
            'total_box' => 0
        ]);
    }

    // ambil uuid sebagai kode_produksi di tabel release_packings
    $uuidProduksi = $mincing->uuid;

    // hitung/count release
    $totalBox = Release_packing::where('kode_produksi', $uuidProduksi)
        ->sum('release');

    return response()->json([
        'nama_produk'   => $nama_produk,
        'kode_produksi' => $kode_produksi,
        'uuid'          => $uuidProduksi,
        'total_box'     => $totalBox
    ]);
}

}
