<?php

namespace App\Http\Controllers;

use App\Models\Stuffing;
use App\Models\Mincing;
use App\Models\Produk;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use TCPDF;

class StuffingController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $userPlant  = Auth::user()->plant;
        
        $data = Stuffing::with('mincing')  
        ->where('plant', $userPlant) 
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('kode_mesin', 'like', "%{$search}%");
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

        return view('form.stuffing.index', compact('data', 'search', 'date', 'shift'));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        $items = Stuffing::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('kode_mesin', 'like', "%{$search}%");
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

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        
        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Laporan Stuffing');
        
        // Hilangkan Header/Footer Bawaan (Agar kita bisa custom full di Blade)
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // 3. SET MARGIN TIPIS (5mm)
        // Format: Left, Top, Right
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5); // Margin bawah juga tipis

        // 4. Set Font Default
        $pdf->SetFont('helvetica', '', 8);

        $pdf->AddPage();

        // 5. Render
        $html = view('reports.stuffing', compact('items', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Laporan_Stuffing_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $batches = Mincing::latest()->take(2)->get();
        $produks = Produk::where('plant', $userPlant)->get();
        $mesins = Mesin::where('plant', $userPlant)
        ->where('jenis_mesin', 'Stuffing')
        ->orderBy('nama_mesin')
        ->get(['uuid', 'nama_mesin']);

        return view('form.stuffing.create', compact('produks', 'mesins', 'batches'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;

        $nama_produksi = 'Produksi RTT';
        if (session()->has('selected_produksi')) {
            $user = \App\Models\User::where('uuid', session('selected_produksi'))->first();
            if ($user) $nama_produksi = $user->name;
        }

        // 1. Validasi Root & Validasi Array Multi-Dimensi (Dot Notation)
        $validated = $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|exists:mincings,uuid',
            'exp_date'      => 'required|date',
            
            // Validasi Array Form Dinamis
            'stuffing'                      => 'required|array|min:1',
            'stuffing.*.kode_mesin'         => 'required|string',
            'stuffing.*.jam_mulai'          => 'required',
            'stuffing.*.suhu'               => 'nullable|numeric',
            'stuffing.*.sensori'            => 'nullable|string',
            'stuffing.*.kecepatan_stuffing' => 'nullable|numeric',
            'stuffing.*.panjang_pcs'        => 'nullable|numeric',
            'stuffing.*.berat_pcs'          => 'nullable|numeric',
            'stuffing.*.kebersihan_seal'    => 'nullable|string',
            'stuffing.*.kekuatan_seal'      => 'nullable|string',
            'stuffing.*.diameter_klip'      => 'nullable|numeric',
            'stuffing.*.print_kode'         => 'nullable|string',
            'stuffing.*.lebar_cassing'      => 'nullable|numeric',
            'stuffing.*.catatan'            => 'nullable|string',
        ], [
            // Kustomisasi pesan error agar mudah dipahami user
            'stuffing.*.kode_mesin.required' => 'Nama Mesin pada salah satu form belum dipilih.',
            'stuffing.*.jam_mulai.required'  => 'Jam Mulai pada salah satu form belum diisi.',
        ]);

        // 2. Looping Simpan Data ke Database
        foreach ($request->stuffing as $item) {
            Stuffing::create([
                'date'               => $request->date,
                'shift'              => $request->shift,
                'nama_produk'        => $request->nama_produk,
                'kode_produksi'      => $request->kode_produksi,
                'exp_date'           => $request->exp_date,
                
                // Ambil dari iterasi form
                'kode_mesin'         => $item['kode_mesin'],
                'jam_mulai'          => $item['jam_mulai'],
                'suhu'               => $item['suhu'] ?? null,
                'sensori'            => $item['sensori'] ?? null,
                'kecepatan_stuffing' => $item['kecepatan_stuffing'] ?? null,
                'panjang_pcs'        => $item['panjang_pcs'] ?? null,
                'berat_pcs'          => $item['berat_pcs'] ?? null,
                'kebersihan_seal'    => $item['kebersihan_seal'] ?? null,
                'kekuatan_seal'      => $item['kekuatan_seal'] ?? null,
                'diameter_klip'      => $item['diameter_klip'] ?? null,
                'print_kode'         => $item['print_kode'] ?? null,
                'lebar_cassing'      => $item['lebar_cassing'] ?? null,
                'catatan'            => $item['catatan'] ?? null,

                // Sistem/User tracking
                'username'           => $username,
                'plant'              => $userPlant,
                'nama_produksi'      => $nama_produksi,
                'status_produksi'    => "1",
                'tgl_update_produksi'=> now()->addHour(),
                'status_spv'         => "0",
            ]);
        }

        return redirect()->route('stuffing.index')->with('success', 'Pemeriksaan Stuffing Sosis Retort berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $stuffing = Stuffing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $mesins = Mesin::where('plant', $userPlant)
        ->where('jenis_mesin', 'Stuffing')
        ->orderBy('nama_mesin')
        ->get();

        return view('form.stuffing.update', compact('stuffing', 'produks', 'mesins'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $stuffing = Stuffing::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi'  => 'required',
            'exp_date'       => 'required|date',
            'kode_mesin'  => 'required',
            'jam_mulai'   => 'required',
            'suhu'        => 'nullable|numeric',
            'sensori'            => 'nullable|string',
            'kecepatan_stuffing' => 'nullable|numeric',
            'panjang_pcs'     => 'nullable|numeric',
            'berat_pcs'       => 'nullable|numeric',
            'kebersihan_seal' => 'nullable|string',
            'kekuatan_seal'   => 'nullable|string',
            'diameter_klip'   => 'nullable|numeric',
            'print_kode'      => 'nullable|string',
            'lebar_cassing'   => 'nullable|numeric',
            'catatan'         => 'nullable|string',
        ]);

        $data = [
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'exp_date' => $request->exp_date,
            'kode_mesin' => $request->kode_mesin,
            'jam_mulai' => $request->jam_mulai,
            'suhu' => $request->suhu,
            'sensori' => $request->sensori,
            'kecepatan_stuffing' => $request->kecepatan_stuffing,
            'panjang_pcs' => $request->panjang_pcs,
            'berat_pcs' => $request->berat_pcs,
            'kebersihan_seal' => $request->kebersihan_seal,
            'kekuatan_seal' => $request->kekuatan_seal,
            'diameter_klip' => $request->diameter_klip,
            'print_kode' => $request->print_kode,
            'lebar_cassing' => $request->lebar_cassing,
            'catatan' => $request->catatan,
            'username_updated' => $username_updated,
        ];
        $stuffing->update($data);
        return redirect()->route('stuffing.index')->with('success', 'Pemeriksaan Stuffing Sosis Retort berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $stuffing = Stuffing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $mesins = Mesin::where('plant', $userPlant)
        ->where('jenis_mesin', 'Stuffing')
        ->orderBy('nama_mesin')
        ->get();

        return view('form.stuffing.edit', compact('stuffing', 'produks', 'mesins'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $stuffing = Stuffing::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi'  => 'required',
            'exp_date'       => 'required|date',
            'kode_mesin'  => 'required',
            'jam_mulai'   => 'required',
            'suhu'        => 'nullable|numeric',
            'sensori'            => 'nullable|string',
            'kecepatan_stuffing' => 'nullable|numeric',
            'panjang_pcs'     => 'nullable|numeric',
            'berat_pcs'       => 'nullable|numeric',
            'kebersihan_seal' => 'nullable|string',
            'kekuatan_seal'   => 'nullable|string',
            'diameter_klip'   => 'nullable|numeric',
            'print_kode'      => 'nullable|string',
            'lebar_cassing'   => 'nullable|numeric',
            'catatan'         => 'nullable|string',
        ]);

        $data = [
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'exp_date' => $request->exp_date,
            'kode_mesin' => $request->kode_mesin,
            'jam_mulai' => $request->jam_mulai,
            'suhu' => $request->suhu,
            'sensori' => $request->sensori,
            'kecepatan_stuffing' => $request->kecepatan_stuffing,
            'panjang_pcs' => $request->panjang_pcs,
            'berat_pcs' => $request->berat_pcs,
            'kebersihan_seal' => $request->kebersihan_seal,
            'kekuatan_seal' => $request->kekuatan_seal,
            'diameter_klip' => $request->diameter_klip,
            'print_kode' => $request->print_kode,
            'lebar_cassing' => $request->lebar_cassing,
            'catatan' => $request->catatan,
            'username_updated' => $username_updated,
        ];
        $stuffing->update($data);

        return redirect()->route('stuffing.index')->with('success', 'Pemeriksaan Stuffing Sosis Retort berhasil diperbarui');
    }

    public function verification(Request $request)
    {
     $search     = $request->input('search');
     $date       = $request->input('date');
     $userPlant  = Auth::user()->plant;

     $data = Stuffing::query()
     ->where('plant', $userPlant) 
     ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%")
            ->orWhere('kode_mesin', 'like', "%{$search}%");
        });
    })
     ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
     ->orderBy('date', 'desc')
     ->orderBy('created_at', 'desc')
     ->paginate(10)
     ->appends($request->all());

     return view('form.stuffing.index', compact('data', 'search', 'date'));
 }

 public function updateVerification(Request $request, $uuid)
 {
    $request->validate([
        'status_spv'   => 'required|in:1,2', 
        'catatan_spv'  => 'nullable|string|max:255',
    ]);

    $stuffing = Stuffing::where('uuid', $uuid)->firstOrFail();

    $username_spv = Auth::user()->username ?? 'SPV';

    // Mencegah status_spv double update
    if ($stuffing->status_spv != 0) {
        return back()->with('error', 'Data sudah diverifikasi sebelumnya!');
    }

    $stuffing->update([
        'status_spv'       => $request->status_spv,
        'catatan_spv'      => $request->catatan_spv,
        'username_spv'     => $username_spv,
        'tgl_update_spv'   => now(),
    ]);

    return redirect()
    ->route('stuffing.index')
    ->with('success', 'Verifikasi berhasil disimpan.');
}

public function destroy($uuid)
{
    $stuffing = Stuffing::where('uuid', $uuid)->firstOrFail();
    $stuffing->delete();
    return redirect()->route('stuffing.index')->with('success', 'Stuffing berhasil dihapus');
}

public function recyclebin()
{
    $stuffing = Stuffing::onlyTrashed()
    ->with('mincing')
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.stuffing.recyclebin', compact('stuffing'));
}

public function restore($uuid)
{
    $stuffing = Stuffing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $stuffing->restore();

    return redirect()->route('stuffing.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $stuffing = Stuffing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $stuffing->forceDelete();

    return redirect()->route('stuffing.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}
}