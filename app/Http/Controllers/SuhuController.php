<?php

namespace App\Http\Controllers;

use App\Models\Suhu;
use App\Models\Produk;
use App\Models\Area_suhu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuhuController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant  = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)->get();

        $data = Suhu::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
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

        return view('form.suhu.index', compact('data', 'search', 'date', 'shift', 'area_suhus'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)
            ->orderBy('area')
            ->get(['area', 'standar_min', 'standar_max']);

        $today = now()->toDateString();
        $existing = Suhu::where('date', $today)
            ->where('plant', $userPlant)
            ->get();

        $suhuData = [];

        foreach ($existing as $row) {
            $raw = json_decode($row->hasil_suhu, true) ?? [];

            foreach ($raw as $item) {
                $suhuData[$item['area']] = [
                    'suhu' => $item['suhu'] ?? $item['nilai'] ?? null,
                    'rh'   => $item['rh'] ?? null,
                ];
            }
        }

        return view('form.suhu.create', compact('area_suhus', 'suhuData'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;

        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTT';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'pukul'       => 'required',
            'keterangan'  => 'nullable|string',
            'catatan'     => 'nullable|string',
            'hasil_suhu'  => 'nullable|array',
            'hasil_rh'    => 'nullable|array',
        ]);

        $hasil_suhu = $request->input('hasil_suhu', []);
        $hasil_rh   = $request->input('hasil_rh', []);

        $hasil_terstruktur = [];

        foreach ($hasil_suhu as $id => $item) {

            $nilai_suhu_raw = $item['nilai'] ?? '';
            $nilai_rh_raw   = $hasil_rh[$id]['nilai'] ?? '';

            // FORMAT SUHU
            if ($nilai_suhu_raw === '') {
                $nilai_suhu = null;
            } elseif ($nilai_suhu_raw === '-') {
                $nilai_suhu = '-';
            } else {
                $nilai_suhu = (float) str_replace(',', '.', $nilai_suhu_raw);
            }

            // FORMAT RH
            if ($nilai_rh_raw === '') {
                $nilai_rh = null;
            } elseif ($nilai_rh_raw === '-') {
                $nilai_rh = '-';
            } else {
                $nilai_rh = (float) str_replace(',', '.', $nilai_rh_raw);
            }

            $hasil_terstruktur[] = [
                'area' => $item['area'] ?? '',
                'suhu' => $nilai_suhu,
                'rh'   => $nilai_rh,
            ];
        }

        // SORT AREA
        $area_order = Area_suhu::where('plant', $userPlant)
            ->orderBy('area')
            ->pluck('area')
            ->toArray();

        $hasil_terstruktur = collect($hasil_terstruktur)
            ->sortBy(fn($item) => array_search($item['area'], $area_order))
            ->values()
            ->toArray();

        $data = $request->only(['date', 'shift', 'pukul', 'keterangan', 'catatan']);
        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['nama_produksi']       = $nama_produksi;
        $data['status_produksi']     = "1";
        $data['tgl_update_produksi'] = now()->addHour();
        $data['status_spv']          = "0";
        $data['hasil_suhu']          = json_encode($hasil_terstruktur, JSON_UNESCAPED_UNICODE);

        Suhu::create($data);

        return redirect()->route('suhu.index')
            ->with('success', 'Pemeriksaan Suhu dan RH berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)
            ->orderBy('area')
            ->get();

        $raw = !empty($suhu->hasil_suhu) ? json_decode($suhu->hasil_suhu, true) : [];
        $suhuData = collect($raw)->mapWithKeys(function ($item) {
            return [
                $item['area'] => [
                    'suhu' => $item['suhu'] ?? $item['nilai'] ?? null,
                    'rh'   => $item['rh'] ?? null,
                ]
            ];
        })->toArray();

        return view('form.suhu.update', compact('suhu', 'suhuData', 'area_suhus'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User QC';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'pukul'       => 'required',
            'keterangan'  => 'nullable|string',
            'catatan'     => 'nullable|string',
            'hasil_suhu'  => 'nullable|array',
            'hasil_rh'    => 'nullable|array',
        ]);

        $hasil_suhu = $request->input('hasil_suhu', []);
        $hasil_rh   = $request->input('hasil_rh', []);

        $hasil_terstruktur = [];

        foreach ($hasil_suhu as $id => $item) {

            $nilai_suhu_raw = $item['nilai'] ?? '';
            $nilai_rh_raw   = $hasil_rh[$id]['nilai'] ?? '';

            // FORMAT SUHU
            if ($nilai_suhu_raw === '') {
                $nilai_suhu = null;
            } elseif ($nilai_suhu_raw === '-') {
                $nilai_suhu = '-';
            } else {
                $nilai_suhu = (float) str_replace(',', '.', $nilai_suhu_raw);
            }

            // FORMAT RH
            if ($nilai_rh_raw === '') {
                $nilai_rh = null;
            } elseif ($nilai_rh_raw === '-') {
                $nilai_rh = '-';
            } else {
                $nilai_rh = (float) str_replace(',', '.', $nilai_rh_raw);
            }

            $hasil_terstruktur[] = [
                'area' => $item['area'] ?? '',
                'suhu' => $nilai_suhu,
                'rh'   => $nilai_rh,
            ];
        }

        // SORT AREA
        $area_order = Area_suhu::where('plant', $suhu->plant)
            ->orderBy('area')
            ->pluck('area')
            ->toArray();

        $hasil_terstruktur = collect($hasil_terstruktur)
            ->sortBy(fn($item) => array_search($item['area'], $area_order))
            ->values()
            ->toArray();

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'pukul'            => $request->pukul,
            'keterangan'       => $request->keterangan,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'hasil_suhu'       => json_encode($hasil_terstruktur, JSON_UNESCAPED_UNICODE),
        ];

        $suhu->update($data);

        return redirect()->route('suhu.index')
            ->with('success', 'Data Pemeriksaan Suhu dan RH berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)
            ->orderBy('area')
            ->get();

        $raw = !empty($suhu->hasil_suhu) ? json_decode($suhu->hasil_suhu, true) : [];
        $suhuData = collect($raw)->mapWithKeys(function ($item) {
            return [
                $item['area'] => [
                    'suhu' => $item['suhu'] ?? $item['nilai'] ?? null,
                    'rh'   => $item['rh'] ?? null,
                ]
            ];
        })->toArray();;

        return view('form.suhu.edit', compact('suhu', 'suhuData', 'area_suhus'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'pukul'       => 'required',
            'keterangan'  => 'nullable|string',
            'catatan'     => 'nullable|string',
            'hasil_suhu'  => 'nullable|array',
            'hasil_rh'    => 'nullable|array',
        ]);

        $hasil_suhu = $request->input('hasil_suhu', []);
        $hasil_rh   = $request->input('hasil_rh', []);

        $hasil_terstruktur = [];

        foreach ($hasil_suhu as $id => $item) {

            $nilai_suhu_raw = $item['nilai'] ?? '';
            $nilai_rh_raw   = $hasil_rh[$id]['nilai'] ?? '';

            // FORMAT SUHU
            if ($nilai_suhu_raw === '') {
                $nilai_suhu = null;
            } elseif ($nilai_suhu_raw === '-') {
                $nilai_suhu = '-';
            } else {
                $nilai_suhu = (float) str_replace(',', '.', $nilai_suhu_raw);
            }

            // FORMAT RH
            if ($nilai_rh_raw === '') {
                $nilai_rh = null;
            } elseif ($nilai_rh_raw === '-') {
                $nilai_rh = '-';
            } else {
                $nilai_rh = (float) str_replace(',', '.', $nilai_rh_raw);
            }

            $hasil_terstruktur[] = [
                'area' => $item['area'] ?? '',
                'suhu' => $nilai_suhu,
                'rh'   => $nilai_rh,
            ];
        }

        // SORT AREA
        $area_order = Area_suhu::where('plant', $suhu->plant)
            ->orderBy('area')
            ->pluck('area')
            ->toArray();

        $hasil_terstruktur = collect($hasil_terstruktur)
            ->sortBy(fn($item) => array_search($item['area'], $area_order))
            ->values()
            ->toArray();

        $data = [
            'date'       => $request->date,
            'shift'      => $request->shift,
            'pukul'      => $request->pukul,
            'keterangan' => $request->keterangan,
            'catatan'    => $request->catatan,
            'hasil_suhu' => json_encode($hasil_terstruktur, JSON_UNESCAPED_UNICODE),
        ];

        $suhu->update($data);

        return redirect()->route('suhu.index')
            ->with('success', 'Data Pemeriksaan Suhu dan RH berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $userPlant  = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)->get();

        $data = Suhu::query()
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

        return view('form.suhu.index', compact('data', 'search', 'date', 'area_suhus'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();

        $suhu->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('suhu.index')
            ->with('success', 'Status Verifikasi Pemeriksaan Suhu dan RH berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();
        $suhu->delete();
        return redirect()->route('suhu.index')->with('success', 'Suhu berhasil dihapus');
    }

    public function recyclebin()
    {
        $suhu = Suhu::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.suhu.recyclebin', compact('suhu'));
    }
    public function restore($uuid)
    {
        $suhu = Suhu::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $suhu->restore();

        return redirect()->route('suhu.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $suhu = Suhu::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $suhu->forceDelete();

        return redirect()->route('suhu.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        $items = Suhu::query()
            ->where('plant', $userPlant)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->orderBy('pukul', 'asc')
            ->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pemeriksaan Suhu dan RH');

        // Hilangkan Header/Footer Bawaan
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set Margin
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);

        // Set Font Default
        $pdf->SetFont('helvetica', '', 7);

        $pdf->AddPage();

        // 3. Render
        $html = view('reports.pemeriksaan-suhu-rh', compact('items', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Pemeriksaan_Suhu_RH_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
