<?php

namespace App\Http\Controllers;

use App\Models\Klorin;
use App\Models\List_form;
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
        $lokasi = $request->input('lokasi');
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
            'lokasi' => 'required|string',
            'footbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'handbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
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
            'lokasi'              => $request->lokasi,
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
            'lokasi'    => 'required|string',
            'footbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'handbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'catatan'   => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'             => $request->date,
            'pukul'            => $request->pukul,
            'lokasi'           => $request->lokasi,
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
            'lokasi'    => 'required|string',
            'footbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'handbasin' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'catatan'   => 'nullable|string',
        ]);

        $updateData = [
            'date'             => $request->date,
            'pukul'            => $request->pukul,
            'lokasi'           => $request->lokasi,
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

        return redirect()->route('klorin.index', [
            'page'   => $request->input('page', 1),
            'search' => $request->input('search'),
            'date'   => $request->input('date'),
        ])->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function multiVerification(Request $request)
    {
        $request->validate([
            'startdate' => 'required|date',
            'enddate'   => 'required|date|after_or_equal:startdate',
        ]);

       $updated = Klorin::where('plant', Auth::user()->plant_active ?? Auth::user()->plant)
            ->where('status_spv', 0)
            ->whereBetween('date', [
                $request->startdate,
                $request->enddate,
            ])
            ->update([
                'status_spv'     => 1,
                'catatan_spv'    => null,
                'nama_spv'       => Auth::user()->username,
                'tgl_update_spv' => now(),
            ]);

        return redirect()->route('klorin.index')
            ->with('success', "Multi Verification berhasil. {$updated} data diperbarui.");
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
    $date      = $request->input('date');
    $userPlant = Auth::user()->plant;

    $klorins = Klorin::query()
        ->when(!empty($userPlant), function ($q) use ($userPlant) {
            $q->where('plant', $userPlant);
        })
        ->when($date, function ($q) use ($date) {
            $q->whereDate('date', $date);
        })
        ->orderBy('date', 'asc')
        ->orderBy('pukul', 'asc')
        ->get();

    $noDokumen = List_form::where('plant', $userPlant)
        ->where('laporan', 'Pengecekan Klorin')
        ->value('no_dokumen') ?: 'QT 25 / 00';

    $tanggal = $date
        ? \Carbon\Carbon::parse($date)->format('d-m-Y')
        : '';

    $usernames = $klorins
        ->flatMap(fn($k) => [$k->username, $k->nama_produksi, $k->nama_spv])
        ->filter()
        ->unique()
        ->values();

    $userMap  = \App\Models\User::whereIn('username', $usernames)->pluck('name', 'username');
    $namaUser = fn($username) => $username ? ($userMap[$username] ?? $username) : '';

    $imagePath = function ($path) {
        if (empty($path)) {
            return '';
        }
        $full = storage_path('app/public/klorin/' . basename($path));
        return file_exists($full) ? $full : '';
    };

    $rows = [];

    foreach ($klorins as $k) {
        $rows[] = [
            'pukul'    => $k->pukul ? \Carbon\Carbon::parse($k->pukul)->format('H:i') : '-',
            'foot'     => $imagePath($k->footbasin),
            'hand'     => $imagePath($k->handbasin),
            'qc'       => $namaUser($k->username) ?: '-',
            'produksi' => $namaUser($k->nama_produksi) ?: '-',
        ];
    }

    $pages = empty($rows) ? [[]] : array_chunk($rows, 8);

    $catatan = $klorins->pluck('catatan')
        ->filter()
        ->unique()
        ->implode(', ');

    $first        = $klorins->first();
    $pemeriksa    = $first ? $namaUser(trim((string) $first->username)) : '';
    $tglPemeriksa = ($first && $first->created_at)
        ? \Carbon\Carbon::parse($first->created_at)->format('d-m-Y')
        : '';

    $semuaDisetujui = $klorins->isNotEmpty()
        && $klorins->every(fn($k) => (string) $k->status_spv === '1');

    $spv    = '';
    $tglSpv = '';

    if ($semuaDisetujui) {
        $spvRow = $klorins->first(fn($k) => !empty($k->nama_spv));

        if ($spvRow) {
            $spv    = $namaUser(trim((string) $spvRow->nama_spv));
            $tglSpv = $spvRow->tgl_update_spv
                ? \Carbon\Carbon::parse($spvRow->tgl_update_spv)->format('d-m-Y')
                : '';
        }
    }

    if (ob_get_length()) {
        ob_end_clean();
    }

    $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Pengecekan Klorin');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->SetMargins(5, 5, 5);
    $pdf->SetAutoPageBreak(true, 5);

    $pdf->SetFont('helvetica', '', 6);
    $pdf->AddPage();

    $html = view('reports.pengecekan-klorin', compact(
        'pages',
        'tanggal',
        'catatan',
        'noDokumen',
        'pemeriksa',
        'tglPemeriksa',
        'spv',
        'tglSpv',
        'semuaDisetujui'
    ))->render();

    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output('Pengecekan_Klorin_' . date('d-m-Y_His') . '.pdf', 'I');

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
            ->toJpeg(quality: 90); // kompres kualitas 90%

        Storage::put("$path/$filename", (string) $image);

        return "$path/$filename";
    }
}
