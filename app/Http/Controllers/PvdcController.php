<?php

namespace App\Http\Controllers;

use App\Models\Pvdc;
use App\Models\Mincing;
use App\Models\Produk;
use App\Models\Mesin;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use TCPDF;

class PvdcController extends Controller
{
    public function index(Request $request)
    {
        $search        = $request->input('search');
        $date          = $request->input('date');
        $shift         = $request->input('shift');
        $namaProduk    = $request->input('nama_produk');
        $userPlant     = Auth::user()->plant;

    // Ambil list produk untuk dropdown
        $produks = Pvdc::where('plant', $userPlant)
        ->select('nama_produk')
        ->distinct()
        ->orderBy('nama_produk')
        ->get();

    // Query utama PVDC
        $data = Pvdc::query()
        ->where('plant', $userPlant)

        // Filter pencarian bebas
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('nama_supplier', 'like', "%{$search}%")
                ->orWhere('catatan', 'like', "%{$search}%");
            });
        })

        // Filter berdasarkan tanggal
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })

        // Filter shift
        ->when($shift, function ($query) use ($shift) {
            $query->where('shift', $shift);
        })

        // Filter nama produk
        ->when($namaProduk, function ($query) use ($namaProduk) {
            $query->where('nama_produk', $namaProduk);
        })

        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        $pvdc = Pvdc::first();

        // dd([
        //     'raw_data_pvdc' => $pvdc->getRawOriginal('data_pvdc'),
        //     'casted'        => $pvdc->data_pvdc,
        //     'accessor'      => $pvdc->pvdc_detail,
        //     'first_mincing' => optional(
        //         $pvdc->pvdc_detail->first()['detail']->first()
        //     )['mincing'],
        // ]);

        return view('form.pvdc.index', compact('data', 'produks', 'search', 'date', 'shift', 'namaProduk'));
    }


    public function create()
    {
        $userPlant = Auth::user()->plant; 

        $produks = Produk::where('plant', $userPlant)->get();
        $mesins = Mesin::where('plant', $userPlant)
        ->where('jenis_mesin', 'Stuffing') 
        ->orderBy('nama_mesin')
        ->get();

        $suppliers = Supplier::where('plant', $userPlant)
        ->where('jenis_barang', 'Packaging')
        ->orderBy('nama_supplier')
        ->get();

        return view('form.pvdc.create', compact('produks', 'mesins', 'suppliers'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'nama_supplier' => 'required',
            'tgl_kedatangan'  => 'required|date',
            'tgl_expired'     => 'required|date',
            'catatan'         => 'nullable|string',
            'data_pvdc'       => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'nama_supplier', 'tgl_kedatangan', 'tgl_expired', 'catatan']);
        $data['username']        = $username;
        $data['plant']           = $userPlant;
        $data['status_spv']      = "0";
        $data['data_pvdc']       = json_encode($request->input('data_pvdc', []), JSON_UNESCAPED_UNICODE);

        Pvdc::create($data);

        return redirect()->route('pvdc.index')
        ->with('success', 'Data No. Lot PVDC berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $mesins = Mesin::where('plant', $userPlant)
        ->where('jenis_mesin', 'Stuffing')
        ->orderBy('nama_mesin')
        ->get();

        $suppliers = Supplier::where('plant', $userPlant)
        ->where('jenis_barang', 'Packaging')
        ->orderBy('nama_supplier')
        ->get();

        $pvdcData = !empty($pvdc->data_pvdc) ? json_decode($pvdc->data_pvdc, true) : [];

        foreach ($pvdcData as &$mesinRow) {
            if (empty($mesinRow['detail'])) {
                continue;
            }
            // Paksa detail jadi array numerik (buang key aneh 1.3333 dst)
            $details = is_array($mesinRow['detail'])
            ? array_values($mesinRow['detail'])
            : [];

            foreach ($details as &$detailRow) {
                if (empty($detailRow['batch'])) {
                    continue;
                }
                $mincing = Mincing::where('uuid', $detailRow['batch'])->first();
                if ($mincing) {
                    $detailRow['batch_display'] = $mincing->kode_produksi;
                }
            }
            $mesinRow['detail'] = $details;
        }

        return view('form.pvdc.update', compact('pvdc', 'produks', 'pvdcData', 'mesins', 'suppliers'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTT';

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'nama_produk'     => 'required',
            'nama_supplier'   => 'required',
            'tgl_kedatangan'  => 'required|date',
            'tgl_expired'     => 'required|date',
            'catatan'         => 'nullable|string',
            'data_pvdc'       => 'nullable|array',
            'data_pvdc_old'   => 'nullable|array', 
        ]);

        $data_pvdc_old = $request->input('data_pvdc_old', []);
        $data_pvdc_new = $request->input('data_pvdc', []);

        $combined_pvdc = array_merge($data_pvdc_old, $data_pvdc_new);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'nama_supplier'    => $request->nama_supplier,
            'tgl_kedatangan'   => $request->tgl_kedatangan,
            'tgl_expired'      => $request->tgl_expired,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'data_pvdc'        => json_encode($combined_pvdc, JSON_UNESCAPED_UNICODE),
        ];

        $pvdc->update($data);

        return redirect()->route('pvdc.index')->with('success', 'Data No. Lot PVDC berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $mesins = Mesin::where('plant', $userPlant)
        ->where('jenis_mesin', 'Stuffing')
        ->orderBy('nama_mesin')
        ->get();

        $suppliers = Supplier::where('plant', $userPlant)
        ->where('jenis_barang', 'Packaging')
        ->orderBy('nama_supplier')
        ->get();

        $pvdcData = !empty($pvdc->data_pvdc) ? json_decode($pvdc->data_pvdc, true) : [];

        return view('form.pvdc.edit', compact('pvdc', 'produks', 'pvdcData', 'mesins', 'suppliers'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'nama_produk'     => 'required',
            'nama_supplier'   => 'required',
            'tgl_kedatangan'  => 'required|date',
            'tgl_expired'     => 'required|date',
            'catatan'         => 'nullable|string',
            'data_pvdc'       => 'nullable|array',
            'data_pvdc_old'   => 'nullable|array', 
        ]);

        $data_pvdc_old = $request->input('data_pvdc_old', []);
        $data_pvdc_new = $request->input('data_pvdc', []);

        $combined_pvdc = array_merge($data_pvdc_old, $data_pvdc_new);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'nama_supplier'    => $request->nama_supplier,
            'tgl_kedatangan'   => $request->tgl_kedatangan,
            'tgl_expired'      => $request->tgl_expired,
            'catatan'          => $request->catatan,
            'data_pvdc'        => json_encode($combined_pvdc, JSON_UNESCAPED_UNICODE),
        ];

        $pvdc->update($data);

        return redirect()->route('pvdc.index')->with('success', 'Data No. Lot PVDC berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Pvdc::query()
        ->where('plant', $userPlant) 
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('nama_supplier', 'like', "%{$search}%")
                ->orWhere('no_lot', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.pvdc.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
            'tgl_update_spv'
        ]);

        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();

        $pvdc->update([
            'status_spv' => $request->status_spv,
            'catatan_spv' => $request->catatan_spv,
            'nama_spv' => Auth::user()->username,
            'tgl_update_spv' => now(),
        ]);

        return redirect()->route('pvdc.index')
        ->with('success', 'Status Verifikasi Data No. Lot PVDC berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();
        $pvdc->delete();
        return redirect()->route('pvdc.index')->with('success', 'Pvdc berhasil dihapus');
    }

    public function recyclebin()
    {
        $pvdc = Pvdc::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.pvdc.recyclebin', compact('pvdc'));
    }
    public function restore($uuid)
    {
        $pvdc = Pvdc::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pvdc->restore();

        return redirect()->route('pvdc.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pvdc = Pvdc::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pvdc->forceDelete();

        return redirect()->route('pvdc.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
    public function exportPdf(Request $request)
    {
        // 1. Ambil Parameter Filter
        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $userPlant  = Auth::user()->plant;

        // 2. Query Data Header
        $headers = Pvdc::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('nama_supplier', 'like', "%{$search}%")
                ->orWhere('catatan', 'like', "%{$search}%");
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

        // 3. TRANSFORMASI DATA (PENTING!)
        // Karena detail ada di dalam JSON, kita harus "memecah" (flatten) data 
        // agar menjadi baris-baris tabel yang siap cetak.
        
        $items = collect(); // Collection baru untuk menampung baris tabel

        foreach ($headers as $header) {
            // Decode JSON
            $pvdcData = !empty($header->data_pvdc) ? json_decode($header->data_pvdc, true) : [];

            // Cek apakah ada data detail
            if (empty($pvdcData)) {
                // Jika tidak ada detail mesin, tetap masukkan header saja (opsional)
                // $items->push((object) [ ... set null values ... ]);
                continue; 
            }

            foreach ($pvdcData as $mesinRow) {
                // Ambil Nama Mesin
                $namaMesin = $mesinRow['mesin'] ?? '-';
                $details   = $mesinRow['detail'] ?? [];

                // Normalisasi detail jika key-nya acak
                if (is_array($details)) {
                    $details = array_values($details);
                }

                foreach ($details as $row) {
                    // Cari Kode Batch (Mincing) jika perlu (mirip logic di update)
                    $kodeProduksi = $row['batch'] ?? '-';
                    if (!empty($row['batch'])) {
                       $mincing = Mincing::where('uuid', $row['batch'])->first();
                       if ($mincing) {
                           $kodeProduksi = $mincing->kode_produksi;
                       }
                   }

                    // Push ke collection items sebagai object
                   $items->push((object) [
                        // Data Header
                    'date' => $header->date,
                    'shift' => $header->shift,
                    'nama_produk' => $header->nama_produk,
                    'username' => $header->username,
                    'catatan' => $header->catatan,
                    'status_spv' => $header->status_spv,

                        // Data Detail (Hasil Decode JSON)
                    'kode_mesin' => $namaMesin,
                    'kode_produksi' => $kodeProduksi,
                    'no_lot' => $row['no_lot'] ?? '-',
                        'jam_mulai' => $row['waktu'] ?? '-', // Di blade pakai jam_mulai
                        
                        // Data Teknis (Sesuaikan dengan key di JSON Anda jika ada)
                        'suhu' => $row['suhu'] ?? null,
                        'kecepatan_stuffing' => $row['speed'] ?? null,
                        'panjang_pcs' => $row['pjg'] ?? null,
                        'berat_pcs' => $row['berat'] ?? null,
                        'cek_vakum' => $row['vakum'] ?? null,
                        'kekuatan_seal' => $row['seal'] ?? null,
                        'diameter_klip' => $row['klip'] ?? null,
                    ]);
               }
           }
       }

        // 4. Bersihkan Output Buffer
       if (ob_get_length()) {
        ob_end_clean();
    }

        // 5. Setup TCPDF
        $pdf = new TCPDF('L', PDF_UNIT, 'LEGAL', true, 'UTF-8', false); // Landscape agar muat banyak kolom
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Charoen Pokphand Indonesia');
        $pdf->SetTitle('Laporan Data No. Lot PVDC');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage();

        // 6. Render View
        // Kirim $items (data yang sudah di-flatten) ke view
        $html = view('reports.pvdc', compact('items', 'request'))->render();

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Laporan_PVDC_' . date('Ymd_His') . '.pdf', 'I');
        exit();
    }


}