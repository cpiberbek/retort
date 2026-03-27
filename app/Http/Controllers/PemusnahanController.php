<?php

namespace App\Http\Controllers;

use App\Models\Pemusnahan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class PemusnahanController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $userPlant = Auth::user()->plant;

        $data = Pemusnahan::query()
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

        return view('form.pemusnahan.index', compact('data', 'search', 'date'));
    }

    /**
     * EXPORT PDF (Tanpa Shift)
     */
    public function exportPdf(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $userPlant = Auth::user()->plant;

        // Ambil Data
        $items = Pemusnahan::query()
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
            ->orderBy('date', 'asc') // Hapus orderBy shift
            ->get();

            if (ob_get_length()) {
                ob_end_clean();
            }

        // Setup PDF Landscape A4
            $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle('Laporan Pemusnahan Barang / Produk');

            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);

            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(TRUE, 10);
            $pdf->SetFont('helvetica', '', 9);

            $pdf->AddPage();

        // Render View
            $html = view('reports.pemusnahan', compact('items', 'request'))->render();

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output('Laporan_Pemusnahan_' . date('d-m-Y_His') . '.pdf', 'I');
            exit();
        }
        public function create()
        {
            $userPlant = Auth::user()->plant;
            $produks = Produk::where('plant', $userPlant)->get();

            return view('form.pemusnahan.create', compact('produks'));
        }

        public function store(Request $request)
        {
            $username   = Auth::user()->username ?? 'User RTM';
            $userPlant  = Auth::user()->plant;

            $request->validate([
                'date'                   => 'required|date',
                'nama_produk'            => 'required|string',
                'kode_produksi'          => 'required|string',
                'expired_date'           => 'required',
                'analisa'                => 'nullable|string',
                'keterangan'             => 'nullable|string',
            ]);

            $data = $request->only([
                'date', 'nama_produk', 'kode_produksi', 'expired_date',
                'analisa', 'keterangan'
            ]);

    // Tambahan default
            $data['username']            = $username;
            $data['plant']               = $userPlant;
            $data['status_spv']          = "0";

            Pemusnahan::create($data);

            return redirect()->route('pemusnahan.index')->with('success', 'Pemusnahan Barang / Produk disimpan');
        }

        public function update(string $uuid)
        {
            $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();
            $userPlant = Auth::user()->plant;
            $produks = Produk::where('plant', $userPlant)->get();

            return view('form.pemusnahan.update', compact('pemusnahan', 'produks'));
        }

        public function update_qc(Request $request, string $uuid)
        {
            $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();
            $username_updated = Auth::user()->username ?? 'User QC';

            $request->validate([
                'date'                   => 'required|date',
                'nama_produk'            => 'required|string',
                'kode_produksi'          => 'required|string',
                'expired_date'           => 'required',
                'analisa'                => 'nullable|string',
                'keterangan'             => 'nullable|string',
            ]);

            $data = $request->only([
                'date', 'nama_produk', 'kode_produksi', 'expired_date',
                'analisa', 'keterangan'
            ]);

            $data['username_updated'] = $username_updated;

            $pemusnahan->update($data);

            return redirect()->route('pemusnahan.index')->with('success', 'Data Pemusnahan Barang / Produk berhasil diperbarui');
        }

        public function edit(string $uuid)
        {
            $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();
            $userPlant = Auth::user()->plant;
            $produks = Produk::where('plant', $userPlant)->get();

            return view('form.pemusnahan.edit', compact('pemusnahan', 'produks'));
        }

        public function edit_spv(Request $request, string $uuid)
        {
            $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();

            $request->validate([
                'date'                   => 'required|date',
                'nama_produk'            => 'required|string',
                'kode_produksi'          => 'required|string',
                'expired_date'           => 'required',
                'analisa'                => 'nullable|string',
                'keterangan'             => 'nullable|string',
            ]);

            $data = $request->only([
                'date', 'nama_produk', 'kode_produksi', 'expired_date',
                'analisa', 'keterangan'
            ]);

            $pemusnahan->update($data);

            return redirect()->route('pemusnahan.index')->with('success', 'Data Pemusnahan Barang / Produk berhasil diperbarui');
        }

        public function verification(Request $request)
        {
            $search     = $request->input('search');
            $date       = $request->input('date');
            $userPlant  = Auth::user()->plant;

            $data = Pemusnahan::query()
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

            return view('form.pemusnahan.index', compact('data', 'search', 'date'));
        }

        public function updateVerification(Request $request, $uuid)
        {
            $request->validate([
                'status_spv'  => 'required|in:1,2',
                'catatan_spv' => 'nullable|string|max:255',
            ]);

            $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();

            $pemusnahan->update([
                'status_spv'      => $request->status_spv,
                'catatan_spv'     => $request->catatan_spv,
                'nama_spv'        => Auth::user()->username,
                'tgl_update_spv'  => now(),
            ]);

            return redirect()->route('pemusnahan.index')
            ->with('success', 'Status Verifikasi Pemusnahan Barang / Produk berhasil diperbarui.');
        }

        public function destroy($uuid)
        {
            $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();
            $pemusnahan->delete();
            return redirect()->route('pemusnahan.index')->with('success', 'Pemusnahan berhasil dihapus');
        }

        public function recyclebin()
        {
            $pemusnahan = Pemusnahan::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

            return view('form.pemusnahan.recyclebin', compact('pemusnahan'));
        }
        public function restore($uuid)
        {
            $pemusnahan = Pemusnahan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            $pemusnahan->restore();

            return redirect()->route('pemusnahan.recyclebin')
            ->with('success', 'Data berhasil direstore.');
        }
        public function deletePermanent($uuid)
        {
            $pemusnahan = Pemusnahan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            $pemusnahan->forceDelete();

            return redirect()->route('pemusnahan.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
        }
    }
