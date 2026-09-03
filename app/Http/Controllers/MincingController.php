<?php

namespace App\Http\Controllers;

use App\Models\InspectionProductDetail;
use App\Models\Mincing;
use App\Models\Produk;
use App\Models\Mesin;
use App\Models\Master_Raw_Material;
use App\Models\RawMaterialInspection;
use App\Models\Master_Premix;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCPDF; // Import TCPDF
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\MincingExport;

class MincingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');
        $shift = $request->input('shift');
        $kode_batch = $request->input('kode_batch');
        $userPlant = Auth::user()->plant;

        $data = Mincing::query()
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
            ->when($kode_batch, function ($query) use ($kode_batch) {
                $query->where('kode_produksi', 'like', "%{$kode_batch}%");
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.mincing.index', compact(
            'data',
            'search',
            'date',
            'shift',
            'kode_batch'
        ));
    }

    public function exportPdf(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');
        $shift = $request->input('shift');
        $kode_batch = $request->input('kode_batch');
        $userPlant = Auth::user()->plant;

        $produks = Mincing::query()
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
            ->when($kode_batch, function ($query) use ($kode_batch) {
                $query->where('kode_produksi', 'like', "%{$kode_batch}%");
            })
            ->orderBy('date', 'desc')
            ->get();

        if ($produks->isEmpty()) {
            abort(404, 'Data tidak ditemukan.');
        }

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Mincing - Emulsifying - Aging')
            ->value('no_dokumen');

        $html = view('reports.mincing-emulsifying-aging', compact('produks', 'request', 'noDokumen'))->render();

        $pdf = new \TCPDF('P', 'mm', [210, 330], true, 'UTF-8', false);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetCreator('CPI');
        $pdf->SetAuthor('CPI');
        $pdf->SetTitle('Pemeriksaan Mincing - Emulsifying - Aging');

        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        $pdf->SetFont('times', '', 10);

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Pemeriksaan_Mincing_' . $kode_batch . '_' . date('d-m-Y') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            ob_clean();
            $pdf->Output('php://output', 'I');
        }, $filename);
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();

        $rawMaterials = Master_Raw_Material::where('plant_uuid', $userPlant)
            ->orderBy('nama_bahan_baku')
            ->get();

        $premixes = Master_Premix::where('plant_uuid', Auth::user()->plant)
            ->orderBy('nama_premix')
            ->get();

        // ambil detail batch + data inspeksi bahan baku
        $inspections = InspectionProductDetail::with('inspection')
            ->whereHas('inspection', function ($q) use ($userPlant) {
                $q->where('plant_uuid', $userPlant);
            })
            ->get();

        return view('form.mincing.create', compact(
            'produks',
            'rawMaterials',
            'inspections',
            'premixes'
        ));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTT';
        // Ubah '-' menjadi null sebelum validasi agar lolos rule 'numeric'
        $request->merge([
            'suhu_akhir_emulsi_gel' => $request->suhu_akhir_emulsi_gel === '-' ? null : $request->suhu_akhir_emulsi_gel,
            'suhu_akhir_mixing'     => $request->suhu_akhir_mixing === '-' ? null : $request->suhu_akhir_mixing,
            'suhu_akhir_emulsi'     => $request->suhu_akhir_emulsi === '-' ? null : $request->suhu_akhir_emulsi,
        ]);

        $nonPremix = $request->input('non_premix', []);
        $expandedNonPremix = [];

        foreach ($nonPremix as $np) {
            foreach ((array) ($np['inspection_uuid'] ?? []) as $uuid) {
                $item = $np;
                $item['inspection_uuid'] = $uuid;
                $expandedNonPremix[] = $item;
            }
        }

        $request->merge([
            'non_premix' => $expandedNonPremix
        ]);

        $expandedNonPremix = array_map('unserialize', array_unique(array_map('serialize', $expandedNonPremix)));

        $request->merge([
            'non_premix' => $expandedNonPremix
        ]);

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'waktu_mulai'   => 'nullable',
            'waktu_selesai' => 'nullable',
            'premix'        => 'nullable|array',
            'non_premix'    => 'nullable|array',
            'daging'        => 'nullable',
            'suhu_grinding_input' => 'nullable|array',
            'waktu_mixing_premix'        => 'nullable|integer',
            'waktu_mixing_premix_start'  => 'nullable|string',
            'waktu_mixing_premix_end'    => 'nullable|string',
            'waktu_bowl_cutter'          => 'nullable|integer',
            'waktu_bowl_cutter_start'    => 'nullable|string',
            'waktu_bowl_cutter_end'      => 'nullable|string',
            'waktu_aging_emulsi_awal'    => 'nullable',
            'waktu_aging_emulsi_akhir'   => 'nullable',
            'suhu_akhir_emulsi_gel'      => 'nullable|numeric',
            'waktu_mixing'               => 'nullable|integer',
            'waktu_mixing_start'         => 'nullable|string',
            'waktu_mixing_end'           => 'nullable|string',
            'suhu_akhir_mixing'          => 'nullable|numeric',
            'suhu_akhir_emulsi'          => 'nullable|numeric',
            'catatan'                    => 'nullable|string',
        ]);

        // Sanitasi field suhu: jika hanya '-' atau kosong, simpan null
        $sanitasiSuhu = function ($val) {
            return ($val === '' || $val === null) ? null : $val;
        };

        // Sanitasi suhu_bahan di dalam non_premix
        $nonPremix = $request->input('non_premix', []);
        foreach ($nonPremix as &$np) {
            if (isset($np['suhu_bahan'])) {
                $np['suhu_bahan'] = $sanitasiSuhu($np['suhu_bahan']);
            }
        }
        unset($np);

        // Sanitasi suhu di dalam suhu_grinding_input
        $suhuGrinding = $request->input('suhu_grinding_input', []);
        foreach ($suhuGrinding as &$sg) {
            if (isset($sg['suhu'])) {
                $sg['suhu'] = $sanitasiSuhu($sg['suhu']);
            }
        }
        unset($sg);

        $data = $request->only([
            'date',
            'shift',
            'nama_produk',
            'kode_produksi',
            'waktu_mulai',
            'waktu_selesai',
            'daging',
            'waktu_mixing_premix',
            'waktu_mixing_premix_start',
            'waktu_mixing_premix_end',
            'waktu_bowl_cutter',
            'waktu_bowl_cutter_start',
            'waktu_bowl_cutter_end',
            'waktu_aging_emulsi_awal',
            'waktu_aging_emulsi_akhir',
            'waktu_mixing',
            'waktu_mixing_start',
            'waktu_mixing_end',
            'catatan',
        ]);

        $data['suhu_akhir_emulsi_gel'] = $sanitasiSuhu($request->suhu_akhir_emulsi_gel);
        $data['suhu_akhir_mixing']     = $sanitasiSuhu($request->suhu_akhir_mixing);
        $data['suhu_akhir_emulsi']     = $sanitasiSuhu($request->suhu_akhir_emulsi);

        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['nama_produksi']       = $nama_produksi;
        $data['status_produksi']     = "1";
        $data['tgl_update_produksi'] = now()->addHour();
        $data['status_spv']          = "0";
        $data['premix']             = json_encode($request->input('premix', []), JSON_UNESCAPED_UNICODE);
        $data['non_premix']             = json_encode($nonPremix, JSON_UNESCAPED_UNICODE);
        $data['suhu_sebelum_grinding'] = json_encode($suhuGrinding, JSON_UNESCAPED_UNICODE);
        Mincing::create($data);

        return redirect()->route('mincing.index')->with('success', 'Pengecekan mincing berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();

        // 1. Definisikan $userPlant terlebih dahulu
        $userPlant = Auth::user()->plant;

        // 2. Baru gunakan $userPlant untuk query tabel lain
        $produks = Produk::where('plant', $userPlant)->get();
        $rawMaterials = Master_Raw_Material::where('plant_uuid', $userPlant)->get();

        $premixes = Master_Premix::where('plant_uuid', $userPlant)->get();


        $inspections = InspectionProductDetail::with('inspection')
            ->whereHas('inspection', function ($q) use ($userPlant) {
                $q->where('plant_uuid', $userPlant);
            })
            ->get();

        $premixData = !empty($mincing->premix)
            ? json_decode($mincing->premix, true)
            : [];

        $nonPremixData = !empty($mincing->non_premix)
            ? json_decode($mincing->non_premix, true)
            : [];

        return view('form.mincing.update', compact('mincing', 'produks', 'premixData', 'nonPremixData', 'rawMaterials', 'inspections', 'premixes'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User QC';

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'waktu_mulai'   => 'nullable',
            'waktu_selesai' => 'nullable',
            'premix'        => 'nullable|array',
            'non_premix'    => 'nullable|array',
            'daging'        => 'nullable',
            'suhu_grinding_input'   => 'nullable|array',
            'waktu_mixing_premix'        => 'nullable|integer',
            'waktu_mixing_premix_start'  => 'nullable|string',
            'waktu_mixing_premix_end'    => 'nullable|string',
            'waktu_bowl_cutter'          => 'nullable|integer',
            'waktu_bowl_cutter_start'    => 'nullable|string',
            'waktu_bowl_cutter_end'      => 'nullable|string',
            'waktu_aging_emulsi_awal'    => 'nullable',
            'waktu_aging_emulsi_akhir'   => 'nullable',
            'suhu_akhir_emulsi_gel'      => 'nullable|numeric',
            'waktu_mixing'               => 'nullable|integer',
            'waktu_mixing_start'         => 'nullable|string',
            'waktu_mixing_end'           => 'nullable|string',
            'suhu_akhir_mixing'          => 'nullable|numeric',
            'suhu_akhir_emulsi'          => 'nullable|numeric',
            'catatan'                    => 'nullable|string',
        ]);

        // Sanitasi field suhu: jika hanya '-' atau kosong, simpan null
        $sanitasiSuhu = function ($val) {
            return ($val === '' || $val === null) ? null : $val;
        };

        // Sanitasi suhu_bahan di dalam non_premix
        $nonPremix = $request->input('non_premix', []);
        $expandedNonPremix = [];

        foreach ($nonPremix as $np) {
            $inspectionUuids = (array) ($np['inspection_uuid'] ?? []);

            if (empty($inspectionUuids)) {
                $np['inspection_uuid'] = null;
                $expandedNonPremix[] = $np;
                continue;
            }

            foreach ($inspectionUuids as $uuid) {
                $item = $np;
                $item['inspection_uuid'] = $uuid;
                $expandedNonPremix[] = $item;
            }
        }

        $expandedNonPremix = array_map(
            'unserialize',
            array_unique(
                array_map('serialize', $expandedNonPremix)
            )
        );

        $nonPremix = $expandedNonPremix;

        foreach ($nonPremix as &$np) {
            if (isset($np['suhu_bahan'])) {
                $np['suhu_bahan'] = $sanitasiSuhu($np['suhu_bahan']);
            }
        }
        unset($np);

        // Sanitasi suhu di dalam suhu_grinding_input
        $suhuGrinding = $request->input('suhu_grinding_input', []);
        foreach ($suhuGrinding as &$sg) {
            if (isset($sg['suhu'])) {
                $sg['suhu'] = $sanitasiSuhu($sg['suhu']);
            }
        }
        unset($sg);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'waktu_mulai'      => $request->waktu_mulai,
            'waktu_selesai'    => $request->waktu_selesai,
            'daging'           => $request->daging,
            'suhu_sebelum_grinding'    => json_encode($suhuGrinding, JSON_UNESCAPED_UNICODE),
            'waktu_mixing_premix'        => $request->waktu_mixing_premix,
            'waktu_mixing_premix_start'  => $request->waktu_mixing_premix_start,
            'waktu_mixing_premix_end'    => $request->waktu_mixing_premix_end,
            'waktu_bowl_cutter'          => $request->waktu_bowl_cutter,
            'waktu_bowl_cutter_start' => $request->waktu_bowl_cutter_start ? date('H:i:s', strtotime($request->waktu_bowl_cutter_start)) : null,
            'waktu_bowl_cutter_end'   => $request->waktu_bowl_cutter_end ? date('H:i:s', strtotime($request->waktu_bowl_cutter_end)) : null,
            'waktu_aging_emulsi_awal'    => $request->waktu_aging_emulsi_awal,
            'waktu_aging_emulsi_akhir'   => $request->waktu_aging_emulsi_akhir,
            'suhu_akhir_emulsi_gel'      => $sanitasiSuhu($request->suhu_akhir_emulsi_gel),
            'waktu_mixing'               => $request->waktu_mixing,
            'waktu_mixing_start'         => $request->waktu_mixing_start,
            'waktu_mixing_end'           => $request->waktu_mixing_end,
            'suhu_akhir_mixing'          => $sanitasiSuhu($request->suhu_akhir_mixing),
            'suhu_akhir_emulsi'          => $sanitasiSuhu($request->suhu_akhir_emulsi),
            'catatan'                    => $request->catatan,
            'username_updated'           => $username_updated,
            'premix'                     => json_encode($request->input('premix', []), JSON_UNESCAPED_UNICODE),
            'non_premix'                 => json_encode($nonPremix, JSON_UNESCAPED_UNICODE),
        ];

        $mincing->update($data);

        return redirect()->route('mincing.index')->with('success', 'Data QC berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $rawMaterials = Master_Raw_Material::where('plant_uuid', $userPlant)->get();
        $inspections = InspectionProductDetail::with('inspection')
            ->whereHas('inspection', function ($q) use ($userPlant) {
                $q->where('plant_uuid', $userPlant);
            })
            ->get();

        $premixes = Master_Premix::where('plant_uuid', $userPlant)->get();

        $premixData = !empty($mincing->premix)
            ? json_decode($mincing->premix, true)
            : [];

        $nonPremixData = !empty($mincing->non_premix)
            ? json_decode($mincing->non_premix, true)
            : [];

        return view('form.mincing.edit', compact(
            'mincing',
            'produks',
            'premixData',
            'nonPremixData',
            'rawMaterials',
            'inspections',
            'premixes'
        ));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'                  => 'required|date',
            'shift'                 => 'required',
            'nama_produk'           => 'required',
            'kode_produksi'         => 'required|string',
            'waktu_mulai'           => 'nullable',
            'waktu_selesai'         => 'nullable',
            'premix'                => 'nullable|array',
            'non_premix'            => 'nullable|array',
            'daging'                => 'nullable',
            'suhu_grinding_input'   => 'nullable|array',
            'waktu_mixing_premix'        => 'nullable|integer',
            'waktu_mixing_premix_start'  => 'nullable|string',
            'waktu_mixing_premix_end'    => 'nullable|string',
            'waktu_bowl_cutter'          => 'nullable|integer',
            'waktu_bowl_cutter_start'    => 'nullable|string',
            'waktu_bowl_cutter_end'      => 'nullable|string',
            'waktu_aging_emulsi_awal'    => 'nullable',
            'waktu_aging_emulsi_akhir'   => 'nullable',
            'suhu_akhir_emulsi_gel'      => 'nullable|numeric',
            'waktu_mixing'               => 'nullable|integer',
            'waktu_mixing_start'         => 'nullable|string',
            'waktu_mixing_end'           => 'nullable|string',
            'suhu_akhir_mixing'          => 'nullable|numeric',
            'suhu_akhir_emulsi'          => 'nullable|numeric',
            'catatan'                    => 'nullable|string',
        ]);


        // Sanitasi field suhu: jika hanya '-' atau kosong, simpan null
        $sanitasiSuhu = function ($val) {
            return ($val === '' || $val === null) ? null : $val;
        };

        // Sanitasi suhu_bahan di dalam non_premix
        $nonPremix = $request->input('non_premix', []);
        $expandedNonPremix = [];

        foreach ($nonPremix as $np) {
            $inspectionUuids = (array) ($np['inspection_uuid'] ?? []);

            if (empty($inspectionUuids)) {
                $np['inspection_uuid'] = null;
                $expandedNonPremix[] = $np;
                continue;
            }

            foreach ($inspectionUuids as $uuid) {
                $item = $np;
                $item['inspection_uuid'] = $uuid;
                $expandedNonPremix[] = $item;
            }
        }

        $expandedNonPremix = array_map(
            'unserialize',
            array_unique(
                array_map('serialize', $expandedNonPremix)
            )
        );

        $nonPremix = $expandedNonPremix;

        foreach ($nonPremix as &$np) {
            if (isset($np['suhu_bahan'])) {
                $np['suhu_bahan'] = $sanitasiSuhu($np['suhu_bahan']);
            }
        }
        unset($np);

        // Sanitasi suhu di dalam suhu_grinding_input
        $suhuGrinding = $request->input('suhu_grinding_input', []);
        foreach ($suhuGrinding as &$sg) {
            if (isset($sg['suhu'])) {
                $sg['suhu'] = $sanitasiSuhu($sg['suhu']);
            }
        }
        unset($sg);

        $data = [
            'date'                       => $request->date,
            'shift'                      => $request->shift,
            'nama_produk'                => $request->nama_produk,
            'kode_produksi'              => $request->kode_produksi,
            'waktu_mulai'                => $request->waktu_mulai,
            'waktu_selesai'              => $request->waktu_selesai,
            'daging'                     => $request->daging,
            'suhu_sebelum_grinding'      => json_encode($suhuGrinding, JSON_UNESCAPED_UNICODE),
            'waktu_mixing_premix'        => $request->waktu_mixing_premix,
            'waktu_mixing_premix_start'  => $request->waktu_mixing_premix_start,
            'waktu_mixing_premix_end'    => $request->waktu_mixing_premix_end,
            'waktu_bowl_cutter'          => $request->waktu_bowl_cutter,
            'waktu_bowl_cutter_start' => $request->waktu_bowl_cutter_start ? date('H:i:s', strtotime($request->waktu_bowl_cutter_start)) : null,
            'waktu_bowl_cutter_end'   => $request->waktu_bowl_cutter_end ? date('H:i:s', strtotime($request->waktu_bowl_cutter_end)) : null,
            'waktu_aging_emulsi_awal'    => $request->waktu_aging_emulsi_awal,
            'waktu_aging_emulsi_akhir'   => $request->waktu_aging_emulsi_akhir,
            'suhu_akhir_emulsi_gel'      => $sanitasiSuhu($request->suhu_akhir_emulsi_gel),
            'waktu_mixing'               => $request->waktu_mixing,
            'waktu_mixing_start'         => $request->waktu_mixing_start,
            'waktu_mixing_end'           => $request->waktu_mixing_end,
            'suhu_akhir_mixing'          => $sanitasiSuhu($request->suhu_akhir_mixing),
            'suhu_akhir_emulsi'          => $sanitasiSuhu($request->suhu_akhir_emulsi),
            'catatan'                    => $request->catatan,
            'premix'                     => json_encode($request->input('premix', []), JSON_UNESCAPED_UNICODE),
            'non_premix'                 => json_encode($nonPremix, JSON_UNESCAPED_UNICODE),
        ];

        $mincing->update($data);

        return redirect()->route('mincing.index')->with('success', 'Data SPV berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Mincing::query()
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

        return view('form.mincing.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();

        $mincing->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('mincing.index')
            ->with('success', 'Status Verifikasi Pengecekan mincing berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();
        $mincing->delete();
        return redirect()->route('mincing.index')->with('success', 'Mincing berhasil dihapus');
    }

    public function recyclebin()
    {
        $mincing = Mincing::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.mincing.recyclebin', compact('mincing'));
    }
    public function restore($uuid)
    {
        $mincing = Mincing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $mincing->restore();

        return redirect()->route('mincing.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $mincing = Mincing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $mincing->forceDelete();

        return redirect()->route('mincing.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }
    public function getInspections(Request $request)
    {
        $bahan = $request->bahan;

        $data = DB::table('raw_material_inspections as rmi')
            ->join('raw_material_inspection_details as d', 'rmi.uuid', '=', 'd.raw_material_inspection_uuid')
            ->where('rmi.bahan_baku', $bahan)
            ->select(
                'd.uuid',
                'd.kode_batch',
                'd.tanggal_produksi',
                'd.exp',
                'd.jumlah',
                'rmi.supplier'
            )
            ->get();

        return response()->json($data);
    }

    public function getBatch($nama_produk)
    {
        $data = DB::table('mincings')
            ->where('nama_produk', $nama_produk)
            ->select('uuid', 'kode_produksi')
            ->get();

        return response()->json($data);
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');
        $shift = $request->input('shift');
        $kode_batch = $request->input('kode_batch');
        $userPlant = Auth::user()->plant;

        $data = Mincing::query()
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
            ->when($kode_batch, function ($query) use ($kode_batch) {
                $query->where('kode_produksi', 'like', "%{$kode_batch}%");
            })
            ->orderBy('date', 'desc')
            ->get();

        $row = $data->first();

        if (!$row) {
            abort(404, 'Data tidak ditemukan.');
        }

        $template = app_path('templates/pemeriksaan_mincing.xlsx');

        $spreadsheet = IOFactory::load($template);
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheet->getStyle($sheet->calculateWorksheetDimension())
                ->getFont()
                ->setName('Times New Roman');
        }

        $sheet->getStyle('B8:F43')
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText->createText('Hari/Tanggal: ');
        $dateText = $richText->createTextRun(\Carbon\Carbon::parse($row->date)->format('d-m-Y'));
        $dateText->getFont()->setUnderline(true);
        $sheet->setCellValue('A6', $richText);

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText->createText('Shift: ');
        $shiftText = $richText->createTextRun($row->shift ?? '-');
        $shiftText->getFont()->setUnderline(true);
        $sheet->setCellValue('C6', $richText);

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText->createText('Nama Produk: ');
        $produkText = $richText->createTextRun($row->nama_produk ?? '-');
        $produkText->getFont()->setUnderline(true);
        $sheet->setCellValue('E6', $richText);

        $sheet->setCellValue('B8', $kode_batch);
        $sheet->setCellValue('B10', $row->waktu_mulai ?? '-');
        $sheet->setCellValue('F10', $row->waktu_selesai ?? '-');

        $nonPremix = json_decode($row->non_premix, true) ?? [];

        $startRow = 12;

        $nonPremixGrouped = collect($nonPremix)->groupBy(function ($item) {
            return $item['nama_bahan'] ?? '-';
        });

        $currentRow = $startRow;

        foreach ($nonPremixGrouped as $namaBahan => $items) {

            $groupStartRow = $currentRow;
            $groupEndRow = $currentRow + count($items) - 1;

            foreach ($items as $item) {

                $excelRow = $currentRow;

                $kodeBatch = '-';

                if (!empty($item['inspection_uuid'])) {
                    $kodeBatch = \App\Models\InspectionProductDetail::where(
                        'uuid',
                        $item['inspection_uuid']
                    )->value('kode_batch') ?? '-';
                }

                // Nama bahan
                $sheet->setCellValue(
                    "A{$excelRow}",
                    $namaBahan
                );

                // Kode
                $sheet->setCellValue(
                    "B{$excelRow}",
                    $kodeBatch
                );

                // Suhu
                $sheet->setCellValue(
                    "C{$excelRow}",
                    $item['suhu_bahan'] ?? '-'
                );

                // pH
                $sheet->setCellValue(
                    "D{$excelRow}",
                    $item['ph_bahan'] ?? '-'
                );

                // Berat
                $sheet->setCellValue(
                    "E{$excelRow}",
                    $item['berat_bahan'] ?? '-'
                );

                // Sens
                $sheet->setCellValue(
                    "F{$excelRow}",
                    $item['sensori'] ?? '-'
                );

                $currentRow++;
            }

            // Gabungkan cell Nama Bahan jika lebih dari 1 baris
            if (count($items) > 1) {
                $sheet->mergeCells("A{$groupStartRow}:A{$groupEndRow}");
            }

            // Posisi Nama Bahan tengah horizontal & vertikal
            $sheet->getStyle("A{$groupStartRow}:A{$groupEndRow}")
                ->getAlignment()
                ->setHorizontal(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                )
                ->setVertical(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                );
        }

        $premix = json_decode($row->premix, true) ?? [];

        $startRow = 30;

        foreach ($premix as $i => $item) {
            $excelRow = $startRow + $i;

            $sheet->setCellValue(
                "A{$excelRow}",
                $item['nama_premix'] ?? '-'
            );

            $sheet->setCellValue("B{$excelRow}", $item['kode_premix'] ?? '-');
            $sheet->setCellValue("E{$excelRow}", $item['berat_premix'] ?? '-');
            $sheet->setCellValue("F{$excelRow}", $item['sensori_premix'] ?? '-');
        }
        $daging = json_decode($row->suhu_sebelum_grinding, true) ?? [];

        $text = [];

        foreach ($daging as $item) {
            $text[] = ($item['daging'] ?? '-') . ': ' . ($item['suhu'] ?? '-') . ' °C';
        }

        $sheet->setCellValue('B33', implode(', ', $text));

        // =====================================================
        // WAKTU MIXING PREMIX
        // =====================================================
        $mixingPremixStart = $row->waktu_mixing_premix_start
            ? \Carbon\Carbon::parse($row->waktu_mixing_premix_start)->format('H:i')
            : '-';

        $mixingPremixEnd = $row->waktu_mixing_premix_end
            ? \Carbon\Carbon::parse($row->waktu_mixing_premix_end)->format('H:i')
            : '-';

        $mixingPremixMenit = $row->waktu_mixing_premix ?? 0;

        $sheet->setCellValue(
            'B35',
            $mixingPremixStart . ' - ' . $mixingPremixEnd .
                ' (' . $mixingPremixMenit . ' menit)'
        );


        // =====================================================
        // WAKTU BOWL CUTTER
        // =====================================================
        $bowlStart = $row->waktu_bowl_cutter_start
            ? \Carbon\Carbon::parse($row->waktu_bowl_cutter_start)->format('H:i')
            : '-';

        $bowlEnd = $row->waktu_bowl_cutter_end
            ? \Carbon\Carbon::parse($row->waktu_bowl_cutter_end)->format('H:i')
            : '-';

        $bowlMenit = $row->waktu_bowl_cutter ?? 0;

        $sheet->setCellValue(
            'B36',
            $bowlStart . ' - ' . $bowlEnd .
                ' (' . $bowlMenit . ' menit)'
        );


        // =====================================================
        // WAKTU AGING EMULSI
        // =====================================================
        $awalAging = $row->waktu_aging_emulsi_awal;
        $akhirAging = $row->waktu_aging_emulsi_akhir;

        $agingMenit = 0;

        if ($awalAging && $akhirAging) {
            $agingMenit = \Carbon\Carbon::parse($awalAging)
                ->diffInMinutes(\Carbon\Carbon::parse($akhirAging));
        }

        $agingStart = $awalAging
            ? \Carbon\Carbon::parse($awalAging)->format('H:i')
            : '-';

        $agingEnd = $akhirAging
            ? \Carbon\Carbon::parse($akhirAging)->format('H:i')
            : '-';

        $sheet->setCellValue(
            'B37',
            $agingStart . ' - ' . $agingEnd .
                ' (' . $agingMenit . ' menit)'
        );


        // =====================================================
        // SUHU AKHIR EMULSI GEL
        // =====================================================
        $sheet->setCellValue(
            'B38',
            ($row->suhu_akhir_emulsi_gel ?? '-') . ' °C'
        );


        // =====================================================
        // WAKTU MIXING
        // =====================================================
        $mixingStart = $row->waktu_mixing_start
            ? \Carbon\Carbon::parse($row->waktu_mixing_start)->format('H:i')
            : '-';

        $mixingEnd = $row->waktu_mixing_end
            ? \Carbon\Carbon::parse($row->waktu_mixing_end)->format('H:i')
            : '-';

        $mixingMenit = $row->waktu_mixing ?? 0;

        $sheet->setCellValue(
            'B39',
            $mixingStart . ' - ' . $mixingEnd .
                ' (' . $mixingMenit . ' menit)'
        );
        $sheet->setCellValue('B40', ($row->suhu_akhir_mixing ?? '-') . ' °C');
        $sheet->setCellValue('B41', ($row->suhu_akhir_emulsi ?? '-') . ' °C');
        $sheet->setCellValue('B42', $row->username_updated ?? $row->username ?? '-');
        $sheet->setCellValue('B43', $row->nama_produksi ?? '-');

        $sheet->setCellValue('F44', $noDokumen ?? '-');

        $sheet->getStyle('F44')
            ->getFont()
            ->setItalic(true);

        $sheet->setCellValue('A48', $row->catatan);

        $sheet->getStyle('A48')
            ->getFont()
            ->setUnderline(true);

        $sheet->getStyle('A48')
            ->getAlignment()
            ->setWrapText(true);

        $sheet->setCellValue('F50', '(' . ($row->nama_spv ?? '-') . ')');

        $sheet->getStyle('F50')
            ->getFont()
            ->setUnderline(true);

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Mincing - Emulsifying - Aging')
            ->value('no_dokumen');

        $sheet->setCellValue('F44', $noDokumen ?? '-');

        $sheet->getStyle('F44')
            ->getFont()
            ->setItalic(true);

        $filename = 'Pemeriksaan_Mincing_' . $kode_batch . '_' . date('d-m-Y') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            ob_clean();
            $writer->save('php://output');
        }, $filename);
    }
}
