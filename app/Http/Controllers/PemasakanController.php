<?php

namespace App\Http\Controllers;

use App\Models\Pemasakan;
use App\Models\Mincing;
use App\Models\Stuffing;
use App\Models\Produk;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class PemasakanController extends Controller 
{

    public function index(Request $request)
    {
        // 1. INPUT (Gabungan: Ambil input Shift dari Anda)
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift'); // Punya Anda
        $userPlant = Auth::user()->plant;

        // 2. QUERY (Gabungan: Pakai logic query Anda yang ada filter shift-nya)
        $data = Pemasakan::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                      ->orWhere('kode_produksi', 'like', "%{$search}%"); // Punya Anda lebih lengkap
                  });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
            ->when($shift, function ($query) use ($shift) { // Punya Anda
                $query->where('shift', $shift);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        // 3. LOGIC BARU DARI SERVER (Wajib diambil agar tidak error)
            $allUUID = [];
            foreach ($data as $row) {
                if (is_array($row->kode_produksi)) {
                    $allUUID = array_merge($allUUID, $row->kode_produksi);
                }
            }
            $allUUID = array_unique($allUUID);
        // Mengambil data Mincing/Stuffing
            $stuffingData = Mincing::whereIn('uuid', $allUUID)->get()->keyBy('uuid');

        // 4. RETURN VIEW (Gabungan: Kirim 'shift' dan 'stuffingData')
            return view('form.pemasakan.index', compact('data', 'search', 'date', 'shift', 'stuffingData'));
        }


    /**
     * Export PDF dengan Filter Shift
     */
    public function exportPdf(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift'); // Tambah shift
        $userPlant = Auth::user()->plant;

        $items = Pemasakan::query()
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
            ->when($shift, function ($query) use ($shift) { // Logic Filter Shift
                $query->where('shift', $shift);
            })
            ->orderBy('date', 'asc')
            ->get();

            if (ob_get_length()) {
                ob_end_clean();
            }

        // Setup PDF (Portrait karena formulir vertikal)
            $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle('Pengecekan Pemasakan');
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(TRUE, 10);
            $pdf->SetFont('helvetica', '', 8);

            $pdf->AddPage();

            $html = view('reports.pemasakan', compact('items', 'request'))->render();

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output('Pengecekan_Pemasakan_' . date('YmdHis') . '.pdf', 'I');
            exit();
        }

        public function create()
        {
            $userPlant = Auth::user()->plant;
            $produks = Produk::where('plant', $userPlant)->get();
            $batches = Stuffing::latest()->take(3)->get();
            $list_chambers = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Chamber')
            ->orderBy('nama_mesin')
            ->get(['uuid', 'nama_mesin']);

            return view('form.pemasakan.create', compact('produks', 'list_chambers', 'batches'));
        }

        public function store(Request $request)
        {
    // dd($request->all());
            $username   = Auth::user()->username ?? 'User RTM';
            $userPlant  = Auth::user()->plant;
            $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTT';

            $request->validate([
                'date'          => 'required|date',
                'shift'         => 'required',
                'nama_produk'   => 'required',
                'kode_produksi' => 'required|array',
                'jumlah_tray'   => 'required|array',
                'no_chamber'    => 'required',
                'berat_produk'  => 'required|numeric', 
                'suhu_produk'   => 'required|numeric',
                'total_reject'  => 'nullable|numeric',
                'catatan'       => 'nullable|string',
                'cooking'       => 'nullable|array',
            ]);

            $data = $request->only([
                'date', 'shift', 'nama_produk', 'kode_produksi',
                'no_chamber', 'berat_produk', 'suhu_produk', 'jumlah_tray',
                'total_reject', 'catatan',
            ]);
            $data['cooking']             = json_encode($request->input('cooking', []), JSON_UNESCAPED_UNICODE);
            $data['username']            = $username;
            $data['plant']               = $userPlant;
            $data['nama_produksi']       = $nama_produksi;
            $data['status_produksi']     = "1";
            $data['tgl_update_produksi'] = now()->addHour();
            $data['status_spv']          = "0";

            Pemasakan::create($data);

            return redirect()->route('pemasakan.index')->with('success', 'Pengecekan Pemasakan berhasil disimpan');
        }

        /** ====================== QC ====================== **/
        public function update(string $uuid)
        {
            $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();
            $userPlant = Auth::user()->plant;
            $produks = Produk::where('plant', $userPlant)->get();
            $list_chambers = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Chamber')
            ->orderBy('nama_mesin')
            ->get(['uuid', 'nama_mesin']);

            $pemasakanData = !empty($pemasakan->cooking)
            ? json_decode($pemasakan->cooking, true)
            : [];

            return view('form.pemasakan.update', compact('pemasakan', 'produks', 'pemasakanData', 'list_chambers'));
        }

        public function update_qc(Request $request, string $uuid)
        {
            $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();
            $username_updated = Auth::user()->username ?? 'User QC';

            $request->validate([
                'date'          => 'required|date',
                'shift'         => 'required',
                'nama_produk'   => 'required',
                'kode_produksi' => 'required|array',
                'no_chamber'    => 'required',
                'berat_produk'  => 'required|numeric',
                'suhu_produk'   => 'required|numeric',
                'jumlah_tray'   => 'required|array',
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

            $pemasakan->update($data);

            return redirect()->route('pemasakan.index')->with('success', 'Data QC berhasil diperbarui');
        }

        /** ====================== SPV ====================== **/
        public function edit(string $uuid)
        {
            $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();
            $userPlant = Auth::user()->plant;
            $produks = Produk::where('plant', $userPlant)->get();
            $list_chambers = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Chamber')
            ->orderBy('nama_mesin')
            ->get(['uuid', 'nama_mesin']);

            $pemasakanData = !empty($pemasakan->cooking)
            ? json_decode($pemasakan->cooking, true)
            : [];

            return view('form.pemasakan.edit', compact('pemasakan', 'produks', 'pemasakanData', 'list_chambers'));
        }

        public function edit_spv(Request $request, string $uuid)
        {
            $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();

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

            $pemasakan->update([
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

            return redirect()->route('pemasakan.index')->with('success', 'Data SPV berhasil diperbarui');
        }

        public function verification(Request $request)
        {
            $search     = $request->input('search');
            $date       = $request->input('date');
            $userPlant  = Auth::user()->plant;

            $data = Pemasakan::query()
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

            return view('form.pemasakan.index', compact('data', 'search', 'date'));
        }

        public function updateVerification(Request $request, $uuid)
        {
            $request->validate([
                'status_spv'  => 'required|in:1,2',
                'catatan_spv' => 'nullable|string|max:255',
            ]);

            $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();

            $pemasakan->update([
                'status_spv'      => $request->status_spv,
                'catatan_spv'     => $request->catatan_spv,
                'nama_spv'        => Auth::user()->username,
                'tgl_update_spv'  => now(),
            ]);

            return redirect()->route('pemasakan.index')
            ->with('success', 'Status Verifikasi Pengecekan Pemasakan berhasil diperbarui.');
        }

        public function destroy($uuid)
        {
            $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();
            $pemasakan->delete();
            return redirect()->route('pemasakan.index')->with('success', 'Pemasakan berhasil dihapus');
        }

        public function recyclebin()
        {
            $pemasakan = Pemasakan::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

            return view('form.pemasakan.recyclebin', compact('pemasakan'));
        }
        public function restore($uuid)
        {
            $pemasakan = Pemasakan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            $pemasakan->restore();

            return redirect()->route('pemasakan.recyclebin')
            ->with('success', 'Data berhasil direstore.');
        }
        public function deletePermanent($uuid)
        {
            $pemasakan = Pemasakan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            $pemasakan->forceDelete();

            return redirect()->route('pemasakan.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
        }
    }