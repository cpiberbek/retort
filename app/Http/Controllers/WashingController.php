<?php

namespace App\Http\Controllers;

use App\Models\Washing;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class WashingController extends Controller
{

    public function index(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift'); // Pastikan variabel ini ada
        $userPlant = Auth::user()->plant;

        $data = Washing::with('mincing')  
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

        return view('form.washing.index', compact('data', 'search', 'date', 'shift'));
    }
    
    public function exportPdf(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        // Ambil data tanpa pagination
        $items = Washing::query()
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
        ->orderBy('pukul', 'asc')
        ->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // Setup PDF Landscape A4
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Laporan Pemeriksaan Washing - Drying');
        
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        // Margin Kiri/Kanan sangat tipis (3mm) agar tabel muat
        $pdf->SetMargins(3, 5, 3);
        $pdf->SetAutoPageBreak(TRUE, 5);
        $pdf->SetFont('helvetica', '', 6); // Font sangat kecil

        $pdf->AddPage();

        $html = view('reports.washing', compact('items', 'request'))->render();

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Laporan_Washing_' . date('d-m-Y_His') . '.pdf', 'I');
        exit();
    }
    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.washing.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTT';

    // Validasi semua field sesuai tipe data
        $request->validate([
            'date'                   => 'required|date',
            'shift'                  => 'required|string',
            'nama_produk'            => 'required|string',
            'kode_produksi'          => 'required|string',
            'pukul'                  => 'required',
            'panjang_produk'         => 'nullable|numeric',
            'diameter_produk'        => 'nullable|numeric',
            'airtrap'                => 'nullable|string',
            'lengket'                => 'nullable|string',
            'sisa_adonan'            => 'nullable|string',
            'kebocoran'              => 'nullable|string',
            'kekuatan_seal'          => 'nullable|string',
            'print_kode'             => 'nullable|string',
            'konsentrasi_pckleer'    => 'nullable|numeric',
            'suhu_pckleer_1'         => 'nullable|numeric',
            'suhu_pckleer_2'         => 'nullable|numeric',
            'ph_pckleer'             => 'nullable|numeric',
            'kondisi_air_pckleer'    => 'nullable|string',
            'konsentrasi_pottasium'  => 'nullable|numeric',
            'suhu_pottasium'         => 'nullable|numeric',
            'ph_pottasium'           => 'nullable|numeric',
            'kondisi_pottasium'      => 'nullable|string',
            'suhu_heater'            => 'nullable|numeric',
            'speed_1'                => 'nullable|numeric',
            'speed_2'                => 'nullable|numeric',
            'speed_3'                => 'nullable|numeric',
            'speed_4'                => 'nullable|numeric',
            'catatan'                => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'pukul',
            'panjang_produk', 'diameter_produk', 'airtrap', 'lengket',
            'sisa_adonan', 'kebocoran', 'kekuatan_seal', 'print_kode',
            'konsentrasi_pckleer', 'suhu_pckleer_1', 'suhu_pckleer_2',
            'ph_pckleer', 'kondisi_air_pckleer', 'konsentrasi_pottasium',
            'suhu_pottasium', 'ph_pottasium', 'kondisi_pottasium',
            'suhu_heater', 'speed_1', 'speed_2', 'speed_3', 'speed_4',
            'catatan'
        ]);

    // Tambahan default
        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['nama_produksi']       = $nama_produksi;
        $data['status_produksi']     = "1";
        $data['tgl_update_produksi'] = now()->addHour();
        $data['status_spv']          = "0";

        Washing::create($data);

        return redirect()->route('washing.index')->with('success', 'Pemeriksaan Washing - Drying berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $washing = Washing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.washing.update', compact('washing', 'produks'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $washing = Washing::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User QC';

        $request->validate([
            'date'                   => 'required|date',
            'shift'                  => 'required|string',
            'nama_produk'            => 'required|string',
            'kode_produksi'          => 'required|string',
            'pukul'                  => 'required',
            'panjang_produk'         => 'nullable|numeric',
            'diameter_produk'        => 'nullable|numeric',
            'airtrap'                => 'nullable|string',
            'lengket'                => 'nullable|string',
            'sisa_adonan'            => 'nullable|string',
            'kebocoran'              => 'nullable|string',
            'kekuatan_seal'          => 'nullable|string',
            'print_kode'             => 'nullable|string',
            'konsentrasi_pckleer'    => 'nullable|numeric',
            'suhu_pckleer_1'         => 'nullable|numeric',
            'suhu_pckleer_2'         => 'nullable|numeric',
            'ph_pckleer'             => 'nullable|numeric',
            'kondisi_air_pckleer'    => 'nullable|string',
            'konsentrasi_pottasium'  => 'nullable|numeric',
            'suhu_pottasium'         => 'nullable|numeric',
            'ph_pottasium'           => 'nullable|numeric',
            'kondisi_pottasium'      => 'nullable|string',
            'suhu_heater'            => 'nullable|numeric',
            'speed_1'                => 'nullable|numeric',
            'speed_2'                => 'nullable|numeric',
            'speed_3'                => 'nullable|numeric',
            'speed_4'                => 'nullable|numeric',
            'berat_produk'           => 'nullable|numeric',
            'suhu_produk'            => 'nullable|numeric',
            'jumlah_tray'            => 'nullable|string',
            'total_reject'           => 'nullable|numeric',
            'catatan'                => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'pukul',
            'panjang_produk', 'diameter_produk', 'airtrap', 'lengket',
            'sisa_adonan', 'kebocoran', 'kekuatan_seal', 'print_kode',
            'konsentrasi_pckleer', 'suhu_pckleer_1', 'suhu_pckleer_2',
            'ph_pckleer', 'kondisi_air_pckleer', 'konsentrasi_pottasium',
            'suhu_pottasium', 'ph_pottasium', 'kondisi_pottasium',
            'suhu_heater', 'speed_1', 'speed_2', 'speed_3', 'speed_4',
            'berat_produk', 'suhu_produk', 'jumlah_tray', 'total_reject',
            'catatan'
        ]);

        $data['username_updated'] = $username_updated;

        $washing->update($data);

        return redirect()->route('washing.index')->with('success', 'Data Pemeriksaan Washing - Drying berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $washing = Washing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.washing.edit', compact('washing', 'produks'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $washing = Washing::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'                   => 'required|date',
            'shift'                  => 'required|string',
            'nama_produk'            => 'required|string',
            'kode_produksi'          => 'required|string',
            'pukul'                  => 'required',
            'panjang_produk'         => 'nullable|numeric',
            'diameter_produk'        => 'nullable|numeric',
            'airtrap'                => 'nullable|string',
            'lengket'                => 'nullable|string',
            'sisa_adonan'            => 'nullable|string',
            'kebocoran'              => 'nullable|string',
            'kekuatan_seal'          => 'nullable|string',
            'print_kode'             => 'nullable|string',
            'konsentrasi_pckleer'    => 'nullable|numeric',
            'suhu_pckleer_1'         => 'nullable|numeric',
            'suhu_pckleer_2'         => 'nullable|numeric',
            'ph_pckleer'             => 'nullable|numeric',
            'kondisi_air_pckleer'    => 'nullable|string',
            'konsentrasi_pottasium'  => 'nullable|numeric',
            'suhu_pottasium'         => 'nullable|numeric',
            'ph_pottasium'           => 'nullable|numeric',
            'kondisi_pottasium'      => 'nullable|string',
            'suhu_heater'            => 'nullable|numeric',
            'speed_1'                => 'nullable|numeric',
            'speed_2'                => 'nullable|numeric',
            'speed_3'                => 'nullable|numeric',
            'speed_4'                => 'nullable|numeric',
            'berat_produk'           => 'nullable|numeric',
            'suhu_produk'            => 'nullable|numeric',
            'jumlah_tray'            => 'nullable|string',
            'total_reject'           => 'nullable|numeric',
            'catatan'                => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'pukul',
            'panjang_produk', 'diameter_produk', 'airtrap', 'lengket',
            'sisa_adonan', 'kebocoran', 'kekuatan_seal', 'print_kode',
            'konsentrasi_pckleer', 'suhu_pckleer_1', 'suhu_pckleer_2',
            'ph_pckleer', 'kondisi_air_pckleer', 'konsentrasi_pottasium',
            'suhu_pottasium', 'ph_pottasium', 'kondisi_pottasium',
            'suhu_heater', 'speed_1', 'speed_2', 'speed_3', 'speed_4',
            'berat_produk', 'suhu_produk', 'jumlah_tray', 'total_reject',
            'catatan'
        ]);

        $washing->update($data);

        return redirect()->route('washing.index')->with('success', 'Data Pemeriksaan Washing - Drying berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date   = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Washing::query()
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

        return view('form.washing.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $washing = Washing::where('uuid', $uuid)->firstOrFail();

        $washing->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('washing.index')
        ->with('success', 'Status Verifikasi Pemeriksaan Washing - Drying berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $washing = Washing::where('uuid', $uuid)->firstOrFail();
        $washing->delete();
        return redirect()->route('washing.index')->with('success', 'Washing berhasil dihapus');
    }

    public function recyclebin()
    {
        $washing = Washing::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.washing.recyclebin', compact('washing'));
    }
    public function restore($uuid)
    {
        $washing = Washing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $washing->restore();

        return redirect()->route('washing.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $washing = Washing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $washing->forceDelete();

        return redirect()->route('washing.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
}