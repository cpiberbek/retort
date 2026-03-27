<?php

namespace App\Http\Controllers;

use App\Models\Sampel;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SampelController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Sampel::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) { 
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('jenis_sampel', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.sampel.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.sampel.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'            => 'required|date',
            'nama_produk'     => 'required|string|max:255',
            'kode_produksi'   => 'required|string|max:20',
            'keterangan'      => 'nullable|string',
            'jenis_sampel'    => 'nullable|string|max:255',  
            'jenis_sampel_select' => 'nullable|string|max:255',
        ]);

        $jenisSampel = $request->jenis_sampel ?: $request->jenis_sampel_select;
        if (!$jenisSampel) {
            return back()->withErrors(['jenis_sampel' => 'Jenis sampel wajib diisi.'])->withInput();
        }

        $username  = Auth::user()->username ?? 'None';
        $userPlant = Auth::user()->plant;

        $data = [
            'date'            => $request->date,
            'jenis_sampel'    => $jenisSampel,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'keterangan'      => $request->keterangan,
            'username'        => $username,
            'plant'           => $userPlant,
            'status_spv'      => "0",
        ];

        Sampel::create($data);

        return redirect()->route('sampel.index')
        ->with('success', 'Pengambilan Sampel berhasil disimpan.');
    }

    public function update(string $uuid)
    {
        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.sampel.update', compact('sampel', 'produks'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'nama_produk'     => 'required|string|max:255',
            'kode_produksi'   => 'required|string|max:20',
            'keterangan'      => 'nullable|string',
            'jenis_sampel'    => 'nullable|string|max:255',
            'jenis_sampel_select' => 'nullable|string|max:255',
        ]);

        $jenisSampel = $request->jenis_sampel ?: $request->jenis_sampel_select;
        if (!$jenisSampel) {
            return back()->withErrors(['jenis_sampel' => 'Jenis sampel wajib diisi.'])->withInput();
        }

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'            => $request->date,
            'jenis_sampel'    => $jenisSampel,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'keterangan'      => $request->keterangan,
            'username_updated' => $username_updated,
        ];

        $sampel->update($updateData);

        return redirect()->route('sampel.index')
        ->with('success', 'Pengambilan Sampel berhasil diperbarui.');
    }

    public function edit(string $uuid)
    {
        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.sampel.edit', compact('sampel', 'produks'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'nama_produk'     => 'required|string|max:255',
            'kode_produksi'   => 'required|string|max:20',
            'keterangan'      => 'nullable|string',
            'jenis_sampel'    => 'nullable|string|max:255',
            'jenis_sampel_select' => 'nullable|string|max:255',
        ]);

        $jenisSampel = $request->jenis_sampel ?: $request->jenis_sampel_select;
        if (!$jenisSampel) {
            return back()->withErrors(['jenis_sampel' => 'Jenis sampel wajib diisi.'])->withInput();
        }

        $updateData = [
            'date'            => $request->date,
            'jenis_sampel'    => $jenisSampel,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'keterangan'      => $request->keterangan,
        ];

        $sampel->update($updateData);

        return redirect()->route('sampel.index')
        ->with('success', 'Pengambilan Sampel berhasil diperbarui.');
    }

    public function verification(Request $request)
    {
     $search     = $request->input('search');
     $date = $request->input('date');
     $userPlant  = Auth::user()->plant;

     $data = Sampel::query()
     ->where('plant', $userPlant)
     ->when($search, function ($query) use ($search) { 
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%")
            ->orWhere('jenis_sampel', 'like', "%{$search}%");
        });
    })
     ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
     ->orderBy('date', 'desc')
     ->orderBy('created_at', 'desc')
     ->paginate(10)
     ->appends($request->all());

     return view('form.sampel.index', compact('data', 'search', 'date'));
 }

 public function updateVerification(Request $request, $uuid)
 {
    $request->validate([
        'status_spv'  => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    $sampel = Sampel::where('uuid', $uuid)->firstOrFail();

    $sampel->update([
        'status_spv'  => $request->status_spv,
        'catatan_spv' => $request->catatan_spv,
        'nama_spv'    => Auth::user()->username,
        'tgl_update_spv' => now(),
    ]);

    return redirect()->route('sampel.index')
    ->with('success', 'Status Verifikasi Pengambilan Sampel berhasil diperbarui.');
}

public function destroy($uuid)
{
    $sampel = Sampel::where('uuid', $uuid)->firstOrFail();
    $sampel->delete();
    return redirect()->route('sampel.index')->with('success', 'Sampel berhasil dihapus');
}

public function recyclebin()
{
    $sampel = Sampel::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.sampel.recyclebin', compact('sampel'));
}
public function restore($uuid)
{
    $sampel = Sampel::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sampel->restore();

    return redirect()->route('sampel.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $sampel = Sampel::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $sampel->forceDelete();

    return redirect()->route('sampel.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

public function exportPdf(Request $request)
{
    // 1. Ambil Data
    $date      = $request->input('date');
    $userPlant = Auth::user()->plant;

    $items = Sampel::query()
    ->where('plant', $userPlant)
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'asc')
    ->get();

    if (ob_get_length()) {
        ob_end_clean();
    }

    // 2. Setup PDF (Portrait, A4)
    $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

    // Metadata
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Pengambilan Sampel');

    // Hilangkan Header/Footer Bawaan
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    // Set Margin
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(TRUE, 10);

    // Set Font Default
    $pdf->SetFont('helvetica', '', 9);

    $pdf->AddPage();

    // 3. Render
    $html = view('reports.pengambilan-sampel', compact('items', 'request'))->render();
    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = 'Pengambilan_Sampel_' . date('d-m-Y_His') . '.pdf';
    $pdf->Output($filename, 'I');
    exit();
}
}
