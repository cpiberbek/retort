<?php

namespace App\Http\Controllers;

use App\Models\Klorin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class KlorinController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Klorin::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.klorin.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        return view('form.klorin.create');
    }

    public function store(Request $request)
    {
        $username  = Auth::user()->username ?? 'None';
        $userPlant = Auth::user()->plant;

        $nama_produksi = 'Produksi RTT';
        if (session()->has('selected_produksi')) {
            $userProduksi = \App\Models\User::where('uuid', session('selected_produksi'))->first();
            if ($userProduksi) {
                $nama_produksi = $userProduksi->name;
            }
        }

        $request->validate([
            'date'      => 'required|date',
            'pukul'     => 'required|string|max:255',
            'footbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'handbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan'   => 'nullable|string',
        ]);

        // Kompres dan simpan gambar
        $footbasinPath = $request->hasFile('footbasin')
            ? $this->compressAndStore($request->file('footbasin'), 'footbasin')
            : null;

        $handbasinPath = $request->hasFile('handbasin')
            ? $this->compressAndStore($request->file('handbasin'), 'handbasin')
            : null;

        Klorin::create([
            'date'                => $request->date,
            'pukul'               => $request->pukul,
            'footbasin'           => $footbasinPath,
            'handbasin'           => $handbasinPath,
            'catatan'             => $request->catatan,
            'nama_produksi'       => $nama_produksi,
            'status_produksi'     => "1",
            'tgl_update_produksi' => now(),
            'username'            => $username,
            'plant'               => $userPlant,
            'status_spv'          => "0",
        ]);

        return redirect()->route('klorin.index')->with('success', 'Pengecekan Klorin berhasil disimpan.');
    }

    public function update(string $uuid)
    {
        $klorin = Klorin::where('uuid', $uuid)->firstOrFail();
        return view('form.klorin.update', compact('klorin'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $klorin = Klorin::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'      => 'required|date',
            'pukul'     => 'required|string|max:255',
            'footbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'handbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan'   => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'             => $request->date,
            'pukul'            => $request->pukul,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
        ];

        // Hapus dan update gambar baru jika diupload
        if ($request->hasFile('footbasin')) {
            if ($klorin->footbasin && Storage::exists($klorin->footbasin)) {
                Storage::delete($klorin->footbasin);
            }
            $updateData['footbasin'] = $this->compressAndStore($request->file('footbasin'), 'footbasin');
        }

        if ($request->hasFile('handbasin')) {
            if ($klorin->handbasin && Storage::exists($klorin->handbasin)) {
                Storage::delete($klorin->handbasin);
            }
            $updateData['handbasin'] = $this->compressAndStore($request->file('handbasin'), 'handbasin');
        }

        $klorin->update($updateData);

        return redirect()->route('klorin.index')->with('success', 'Pengecekan Klorin berhasil diperbarui.');
    }


    public function edit(string $uuid)
    {
        $klorin = Klorin::where('uuid', $uuid)->firstOrFail();
        return view('form.klorin.edit', compact('klorin'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $klorin = Klorin::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'      => 'required|date',
            'pukul'     => 'required|string|max:255',
            'footbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'handbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan'   => 'nullable|string',
        ]);

        $updateData = [
            'date'             => $request->date,
            'pukul'            => $request->pukul,
            'catatan'          => $request->catatan,
        ];

        // Hapus dan update gambar baru jika diupload
        if ($request->hasFile('footbasin')) {
            if ($klorin->footbasin && Storage::exists($klorin->footbasin)) {
                Storage::delete($klorin->footbasin);
            }
            $updateData['footbasin'] = $this->compressAndStore($request->file('footbasin'), 'footbasin');
        }

        if ($request->hasFile('handbasin')) {
            if ($klorin->handbasin && Storage::exists($klorin->handbasin)) {
                Storage::delete($klorin->handbasin);
            }
            $updateData['handbasin'] = $this->compressAndStore($request->file('handbasin'), 'handbasin');
        }

        $klorin->update($updateData);

        return redirect()->route('klorin.index')->with('success', 'Pengecekan Klorin berhasil diperbarui.');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Klorin::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.klorin.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $klorin = Klorin::where('uuid', $uuid)->firstOrFail();

        $klorin->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('klorin.index')->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $klorin = Klorin::where('uuid', $uuid)->firstOrFail();

        if ($klorin->footbasin && Storage::exists($klorin->footbasin)) {
            Storage::delete($klorin->footbasin);
        }
        if ($klorin->handbasin && Storage::exists($klorin->handbasin)) {
            Storage::delete($klorin->handbasin);
        }

        $klorin->delete();

        return redirect()->route('klorin.index')->with('success', 'Data Pengecekan Klorin berhasil dihapus.');
    }

    public function recyclebin()
    {
        $klorin = Klorin::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.klorin.recyclebin', compact('klorin'));
    }

    public function restore($uuid)
    {
        $klorin = Klorin::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $klorin->restore();

        return redirect()->route('klorin.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    
    public function deletePermanent($uuid)
    {
        $klorin = Klorin::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $klorin->forceDelete();

        return redirect()->route('klorin.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;

        $query = Klorin::query();
        if (Auth::check() && !empty($userPlant)) {
            $query->where('plant', $userPlant);
        }

        // Filter tanggal
        $query->when($date, function ($q) use ($date) {
            $q->whereDate('date', $date);
        });

        $klorins = $query->orderBy('date', 'asc')->orderBy('pukul', 'asc')->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pengecekan Klorin');

        // Hilangkan Header/Footer Bawaan
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set Margin
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);

        // Set Font Default
        $pdf->SetFont('helvetica', '', 6);

        $pdf->AddPage();

        // 3. Render
        $html = view('reports.pengecekan-klorin', compact('klorins', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Pengecekan_Klorin_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }

    /**
     * 🔧 Helper untuk kompres dan simpan gambar
     */
    private function compressAndStore($file, $prefix)
    {
        $manager = new ImageManager(new Driver());
        $path = 'public/klorin';
        $filename = $prefix . '_' . Str::uuid() . '.jpg';

        $image = $manager->read($file)
            ->scale(width: 1280) // ubah resolusi ke max 1280px
            ->toJpeg(quality: 75); // kompres kualitas 75%

        Storage::put("$path/$filename", (string) $image);

        return "$path/$filename";
    }
}
