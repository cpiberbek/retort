<?php

namespace App\Http\Controllers;

use App\Models\Gmp;
use App\Models\Produksi;
use App\Models\User;
use App\Models\Area_hygiene;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class GmpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $type_user = Auth::user()->type_user;
        $areas = Area_hygiene::where('plant', $userPlant)
        ->orderBy('area', 'asc')
        ->get();

        $data = Gmp::when($userPlant, fn($q) => $q->where('plant', $userPlant))
        ->when($search, fn($q) => $q->where('username', 'like', "%{$search}%"))
        ->when($date, fn($q) => $q->whereDate('date', $date))
        ->orderBy('date', 'desc')
        ->paginate(10)
        ->appends($request->all());

        $data->getCollection()->transform(function($item) {
            $decoded = json_decode($item->pemeriksaan, true) ?: [];
            $item->pemeriksaan = $decoded;

            $areasFromJson = array_unique(array_map(fn($row) => $row['area'] ?? 'Unknown', $decoded));
            $item->areas = $areasFromJson;

            return $item;
        });

        return view('form.gmp.index', compact('data', 'search', 'date', 'type_user', 'areas'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $userPlant = Auth::user()->plant;

        $gmp = Gmp::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $gmp->status_spv = $request->status_spv; 
        $gmp->catatan_spv = $request->catatan_spv;
        $gmp->nama_spv = Auth::user()->username;
        $gmp->tgl_update_spv = now();
        $gmp->save();

        return redirect()->route('gmp.index')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $areas = Area_hygiene::where('plant', $userPlant)
        ->orderBy('area', 'asc')
        ->get();

        $karyawanByArea = [];
        foreach ($areas as $area) {
            $karyawanByArea[$area->area] = Produksi::where('area', $area->area)
            ->where('plant', $userPlant)
            ->pluck('nama_karyawan')
            ->toArray();
        }

        return view('form.gmp.create', compact('areas', 'karyawanByArea'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
        ]);

        $user = Auth::user();

        $data = [
            'date' => $request->date,
            'username' => $user->username,
            'plant' => $user->plant,
            'nama_produksi' => session()->has('selected_produksi')
            ? User::where('uuid', session('selected_produksi'))->first()->name
            : null,
            'status_produksi' => "1",
            'status_spv' => "0",
            'catatan' => $request->input('catatan'),
        ];

        $pemeriksaanData = [];

    // 🔥 Ambil semua slug area yang valid
        $areaSlugs = Area_hygiene::orderBy('area', 'asc')
        ->get()
        ->mapWithKeys(function ($area) {
            return [ Str::slug($area->area, '_') => $area->area ];
        });

    // 🔥 Loop hanya area yang sesuai slug (tidak acak semua request)
        foreach ($areaSlugs as $slug => $namaAreaAsli) {

            if (!$request->has($slug)) {
            continue; // skip kalau area tidak dikirim
        }

        foreach ($request->$slug as $row) {
            $pemeriksaanData[] = [
                'area' => $namaAreaAsli,
                'nama_karyawan' => $row['nama_karyawan'] ?? '',
                'pukul' => now()->format('H:i'),

                'anting' => $row['anting'] ?? 0,
                'kalung' => $row['kalung'] ?? 0,
                'cincin' => $row['cincin'] ?? 0,
                'jam_tangan' => $row['jam_tangan'] ?? 0,
                'peniti' => $row['peniti'] ?? 0, 
                'bros' => $row['bros'] ?? 0,
                'payet' => $row['payet'] ?? 0,
                'softlens' => $row['softlens'] ?? 0,
                'eyelashes' => $row['eyelashes'] ?? 0,
                'seragam' => $row['seragam'] ?? 0,
                'boot' => $row['boot'] ?? 0,
                'masker' => $row['masker'] ?? 0,
                'ciput_hairnet' => $row['ciput_hairnet'] ?? 0,
                'kuku' => $row['kuku'] ?? 0,
                'parfum' => $row['parfum'] ?? 0,
                'make_up' => $row['make_up'] ?? 0,

                'diare' => $row['diare'] ?? 0,
                'demam' => $row['demam'] ?? 0,
                'luka_bakar' => $row['luka_bakar'] ?? 0,
                'batuk' => $row['batuk'] ?? 0,
                'radang' => $row['radang'] ?? 0,
                'influenza' => $row['influenza'] ?? 0,
                'sakit_mata' => $row['sakit_mata'] ?? 0,

                'keterangan' => $row['keterangan'] ?? null,
            ];
        }
    }

    // 🔥 Simpan JSON
    $data['pemeriksaan'] = json_encode($pemeriksaanData);

    $gmp = Gmp::create($data);
    $gmp->update(['tgl_update_produksi' => Carbon::parse($gmp->created_at)->addHour()]);

    return redirect()->route('gmp.index')
    ->with('success', 'Data GMP berhasil disimpan.');
}

public function edit(string $uuid)
{
    $gmp = Gmp::where('uuid', $uuid)
    ->where('plant', Auth::user()->plant)
    ->firstOrFail();

    $pemeriksaan = json_decode($gmp->pemeriksaan, true) ?? [];

    $karyawanByArea = [];
    $oldDataPerArea = [];

    foreach ($pemeriksaan as $row) {
        $areaName = $row['area'] ?? 'Unknown';
        $namaKaryawan = $row['nama_karyawan'] ?? 'Unknown';

        if (!isset($karyawanByArea[$areaName])) {
            $karyawanByArea[$areaName] = [];
            $oldDataPerArea[$areaName] = [];
        }

        $karyawanByArea[$areaName][] = $namaKaryawan;
        $oldDataPerArea[$areaName][$namaKaryawan] = $row;
    }

    $areas = array_map(function($areaName) {
        return (object)['area' => $areaName];
    }, array_keys($karyawanByArea));

    return view('form.gmp.edit', compact('gmp', 'areas', 'karyawanByArea', 'oldDataPerArea'));
}

public function update(Request $request, string $uuid)
{
    $userPlant = Auth::user()->plant;
    $userType  = Auth::user()->type_user ?? null;

    $gmp = Gmp::where('uuid', $uuid)
    ->where('plant', $userPlant)
    ->firstOrFail();

    $request->validate([
        'date' => 'required|date',
    ]);

    // Hanya update username_updated untuk type_user 4 & 8
    $usernameUpdated = in_array($userType, [4,8])
    ? Auth::user()->username
    : $gmp->username_updated; 

    $data = [
        'date' => $request->date,
        'username_updated' => $usernameUpdated,
        'plant' => $userPlant,
        'nama_produksi' => session()->has('selected_produksi')
        ? User::where('uuid', session('selected_produksi'))->first()->name
        : null,
        'catatan' => $request->input('catatan'),
    ];

    $pemeriksaanData = [];

    // 🔥 Ambil semua slug area yang valid
    $areaSlugs = Area_hygiene::orderBy('area', 'asc')
    ->get()
    ->mapWithKeys(function ($area) {
        return [ Str::slug($area->area, '_') => $area->area ];
    });

    // 🔥 Loop hanya area yang sesuai slug (tidak acak semua request)
    foreach ($areaSlugs as $slug => $namaAreaAsli) {

        if (!$request->has($slug)) {
            continue; // skip kalau area tidak dikirim
        }

        foreach ($request->$slug as $row) {
            $pemeriksaanData[] = [
                'area' => $namaAreaAsli,
                'nama_karyawan' => $row['nama_karyawan'] ?? '',
                'pukul' => now()->format('H:i'),

                'anting' => $row['anting'] ?? 0,
                'kalung' => $row['kalung'] ?? 0,
                'cincin' => $row['cincin'] ?? 0,
                'jam_tangan' => $row['jam_tangan'] ?? 0,
                'peniti' => $row['peniti'] ?? 0,
                'bros' => $row['bros'] ?? 0,
                'payet' => $row['payet'] ?? 0,
                'softlens' => $row['softlens'] ?? 0,
                'eyelashes' => $row['eyelashes'] ?? 0,
                'seragam' => $row['seragam'] ?? 0,
                'boot' => $row['boot'] ?? 0,
                'masker' => $row['masker'] ?? 0,
                'ciput_hairnet' => $row['ciput_hairnet'] ?? 0,
                'kuku' => $row['kuku'] ?? 0,
                'parfum' => $row['parfum'] ?? 0,
                'make_up' => $row['make_up'] ?? 0,

                'diare' => $row['diare'] ?? 0,
                'demam' => $row['demam'] ?? 0,
                'luka_bakar' => $row['luka_bakar'] ?? 0,
                'batuk' => $row['batuk'] ?? 0,
                'radang' => $row['radang'] ?? 0,
                'influenza' => $row['influenza'] ?? 0,
                'sakit_mata' => $row['sakit_mata'] ?? 0,

                'keterangan' => $row['keterangan'] ?? null,
            ];
        }
    }

    $data['pemeriksaan'] = json_encode($pemeriksaanData);

    $gmp->update($data);
    $gmp->update(['tgl_update_produksi' => Carbon::parse($gmp->updated_at)->addHour()]);

    return redirect()->route('gmp.index')->with('success', 'Data GMP berhasil diperbarui.');
}

public function destroy($uuid)
{
    $userPlant = Auth::user()->plant;

    $gmp = Gmp::where('uuid', $uuid)
    ->where('plant', $userPlant)
    ->firstOrFail();

    $gmp->delete();

    return redirect()->route('gmp.index')
    ->with('success', 'Data GMP Karyawan berhasil dihapus'); 
}

public function recyclebin()
{
    $gmp = Gmp::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

    // Transform data dari $gmp
    $gmp->getCollection()->transform(function($item) {

        $decoded = json_decode($item->pemeriksaan, true) ?: [];
        $item->pemeriksaan = $decoded;

        $areasFromJson = array_unique(
            array_map(fn($row) => $row['area'] ?? 'Unknown', $decoded)
        );

        $item->areas = $areasFromJson;

        return $item;
    });

    return view('form.gmp.recyclebin', compact('gmp'));
}

public function restore($uuid)
{
    $gmp = Gmp::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $gmp->restore();

    return redirect()->route('gmp.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $gmp = Gmp::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $gmp->forceDelete();

    return redirect()->route('gmp.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

    // EXPORT EXCEL
public function export(Request $request)
{
    $date = $request->input('date');
    $atribut = $request->input('atribut');

    if (!$date || !$atribut) {
        return redirect()->route('gmp.index')
        ->with('error', 'Pilih tanggal dan area terlebih dahulu.');
    }

    try {
        $userPlant = Auth::user()->plant;
        $username = Auth::user()->username ?? '-';
        $nama_produksi = session('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : '-';

        $areas = Area_hygiene::where('plant', $userPlant)->get();

        $selectedArea = $areas->firstWhere('area', $atribut);
        if (!$selectedArea) {
            return back()->with('error', 'Area tidak ditemukan di Area Hygiene.');
        }

        $selectedSlug = Str::slug($selectedArea->area, '_');
        $normalize = fn($str) => Str::slug(strtolower($str), '_');

        $gmpRows = Gmp::where('plant', $userPlant)
        ->where('date', $date)
        ->get();

        if ($gmpRows->isEmpty()) {
            return back()->with('error', "Tidak ada data GMP pada tanggal {$date}.");
        }

        $attributes = [
            'anting','kalung','cincin','jam_tangan','peniti','bros',
            'payet','softlens','eyelashes',
            'seragam','boot','masker','ciput_hairnet',
            'kuku','parfum','make_up',
            'diare','demam','luka_bakar','batuk','radang','influenza','sakit_mata'
        ];

        $rekap = [];

        foreach ($gmpRows as $row) {

            $json = json_decode($row->pemeriksaan, true);
            if (!$json) continue;

            foreach ($json as $entry) {

                if (!isset($entry['area'])) continue;
                if ($normalize($entry['area']) !== $selectedSlug) continue;

                $nama = $entry['nama_karyawan'] ?? 'Unknown';
                $pukul = $entry['pukul'] ?? '-';
                $keterangan = $entry['keterangan'] ?? '';

                if (!isset($rekap[$nama])) {
                    $rekap[$nama] = [
                        'pukul' => '',
                        'keterangan' => ''
                    ];
                    foreach ($attributes as $attr) {
                        $rekap[$nama][$attr] = 0;
                    }
                }

                $rekap[$nama]['pukul'] = $pukul;
                $rekap[$nama]['keterangan'] = $keterangan;

                foreach ($attributes as $attr) {
                    $rekap[$nama][$attr] = isset($entry[$attr]) ? (int)$entry[$attr] : 0;
                }
            }
        }

        if (empty($rekap)) {
            return back()->with('error', "Tidak ada data pada area {$atribut} untuk tanggal {$date}.");
        }

        $templatePath = storage_path('app/templates/personal_hygiene.xlsx');
        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        $sheet->getStyle($sheet->calculateWorksheetDimension())
        ->getFont()
        ->setName('Times')
        ->setSize(10);

        $sheet->setCellValue('K7', strtoupper($atribut));
        $sheet->setCellValue('E7', \Carbon\Carbon::parse($date)->format('d F Y'));

        $sheet->setCellValueByColumnAndRow(7, 10, \Carbon\Carbon::parse($date)->format('d-m-Y'));

        $rowNum = 12;
        $no = 1;

        $center = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];

        $left = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];

        foreach ($rekap as $nama => $data) {
            $sheet->setCellValue("B{$rowNum}", $no);
            $sheet->getStyle("B{$rowNum}")->applyFromArray($center);

            $sheet->setCellValue("C{$rowNum}", $nama);
            $sheet->getStyle("C{$rowNum}")->applyFromArray($left);

            $col = 6; 
            $sheet->setCellValueByColumnAndRow($col, $rowNum, $data['pukul']);
            $sheet->getStyleByColumnAndRow($col, $rowNum)->applyFromArray($center);
            $col++;

            foreach ($attributes as $attr) {
                $sheet->setCellValueByColumnAndRow($col, $rowNum, $data[$attr]);
                $sheet->getStyleByColumnAndRow($col, $rowNum)->applyFromArray($center);
                $col++;
            }

            $totalAtribut = array_sum(array_map(fn($a)=>$data[$a], $attributes));
            $sheet->setCellValueByColumnAndRow(30, $rowNum, $totalAtribut);
            $sheet->getStyleByColumnAndRow(30, $rowNum)->applyFromArray($center);
            $sheet->setCellValueByColumnAndRow(31, $rowNum, $data['keterangan']);
            $sheet->getStyleByColumnAndRow(31, $rowNum)->applyFromArray($left);
            $sheet->setCellValueByColumnAndRow(33, $rowNum, $username);
            $sheet->getStyleByColumnAndRow(33, $rowNum)->applyFromArray($center);
            $sheet->setCellValueByColumnAndRow(34, $rowNum, $nama_produksi);
            $sheet->getStyleByColumnAndRow(34, $rowNum)->applyFromArray($center);

            $rowNum++;
            $no++;
        }

        $filename = "Rekap_GMP_" . Str::slug($atribut,'_') . "_" . $date . ".xlsx";

        if (ob_get_contents()) ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        (new Xlsx($spreadsheet))->save('php://output');
        exit;

    } catch (\Throwable $e) {
        return back()->with('error', "Gagal export: " . $e->getMessage());
    }
}

}
