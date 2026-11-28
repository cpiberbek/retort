<?php

namespace App\Http\Controllers;

use App\Models\Thermometer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\List_form;
use Illuminate\Support\Str;

class ThermometerController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $userPlant  = Auth::user()->plant;

        $data = Thermometer::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('peneraan', 'like', "%{$search}%");
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

        return view('form.thermometer.index', compact('data', 'search', 'date', 'shift'));
    }

    public function create()
    {
        return view('form.thermometer.create');
    }

    public function store(Request $request)
    {
        $username  = Auth::user()->username ?? 'User RTM';
        $userPlant = Auth::user()->plant;

        $request->validate([
            'date'     => 'required|date',
            'shift'    => 'required',
            'peneraan' => 'nullable|array',
        ]);

        Thermometer::create([
            'uuid'       => Str::uuid(),
            'date'       => $request->date,
            'shift'      => $request->shift,
            'username'   => $username,
            'plant'      => $userPlant,
            'status_spv' => "0",
            'peneraan'   => $request->peneraan,
        ]);

        return redirect()->route('thermometer.index')
            ->with('success', 'Peneraan Thermometer berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $thermometer = Thermometer::where('uuid', $uuid)->firstOrFail();

        // Ambil data peneraan (sudah otomatis array dari cast)
        $peneraan = $thermometer->peneraan ?? [];

        // Jika kosong, isi dengan template default
        if (empty($peneraan)) {
            $peneraan = [
                (object)[
                    'kode_thermometer' => '',
                    'area' => '',
                    'standar' => '',
                    'pukul' => '',
                    'hasil_tera' => '',
                    'tindakan_perbaikan' => '',
                ]
            ];
        }

        return view('form.thermometer.update', compact('thermometer', 'peneraan'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $thermometer = Thermometer::where('uuid', $uuid)->firstOrFail();
        $usernameUpdated = Auth::user()->username ?? 'User RTM';

        $request->validate([
            'date'     => 'required|date',
            'shift'    => 'required',
            'peneraan' => 'nullable|array',
        ]);

        $thermometer->update([
            'date'             => $request->date,
            'shift'            => $request->shift,
            'username_updated' => $usernameUpdated,
            'peneraan'         => $request->peneraan,
        ]);

        return redirect()->route('thermometer.index')
            ->with('success', 'Peneraan Thermometer berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $thermometer = Thermometer::where('uuid', $uuid)->firstOrFail();

        // Ambil data peneraan (sudah otomatis array dari cast)
        $peneraan = $thermometer->peneraan ?? [];

        // Jika kosong, isi dengan template default
        if (empty($peneraan)) {
            $peneraan = [
                (object)[
                    'kode_thermometer' => '',
                    'area' => '',
                    'standar' => '',
                    'pukul' => '',
                    'hasil_tera' => '',
                    'tindakan_perbaikan' => '',
                ]
            ];
        }

        return view('form.thermometer.edit', compact('thermometer', 'peneraan'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $thermometer = Thermometer::where('uuid', $uuid)->firstOrFail();
        $usernameUpdated = Auth::user()->username ?? 'User RTM';

        $request->validate([
            'date'     => 'required|date',
            'shift'    => 'required',
            'peneraan' => 'nullable|array',
        ]);

        $thermometer->update([
            'date'             => $request->date,
            'shift'            => $request->shift,
            'username_updated' => $usernameUpdated,
            'peneraan'         => $request->peneraan,
        ]);

        return redirect()->route('thermometer.index')
            ->with('success', 'Peneraan Thermometer berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Thermometer::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('peneraan', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.thermometer.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $thermometer = Thermometer::where('uuid', $uuid)->firstOrFail();

        $thermometer->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('thermometer.index', [
            'page'   => $request->input('page', 1),
            'search' => $request->input('search'),
            'date'   => $request->input('date'),
        ])->with('success', 'Status verifikasi Peneraan Thermometer berhasil diperbarui.');
    }

    public function multiVerification(Request $request)
    {
        $request->validate([
            'startdate' => 'required|date',
            'enddate'   => 'required|date|after_or_equal:startdate',
        ]);

        $updated = Thermometer::where('plant', Auth::user()->plant_active ?? Auth::user()->plant)
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

        return redirect()->route('thermometer.index')
            ->with('success', "Multi Verification berhasil. {$updated} data diperbarui.");
    }

    public function destroy($uuid)
    {
        $thermometer = Thermometer::where('uuid', $uuid)->firstOrFail();
        $thermometer->delete();

        return redirect()->route('thermometer.index')
            ->with('success', '🗑️ Peneraan Thermometer berhasil dihapus.');
    }

    public function recyclebin()
    {
        $thermometer = Thermometer::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.thermometer.recyclebin', compact('thermometer'));
    }
    public function restore($uuid)
    {
        $thermometer = Thermometer::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $thermometer->restore();

        return redirect()->route('thermometer.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $thermometer = Thermometer::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $thermometer->forceDelete();

        return redirect()->route('thermometer.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        $items = Thermometer::query()
            ->where('plant', $userPlant)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->orderBy('date', 'asc')
            ->orderBy('shift', 'asc')
            ->get();

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Verifikasi Termometer')
            ->value('no_dokumen');

        $first = $items->first();

        $tanggal = $first
            ? \Carbon\Carbon::parse($first->date)->format('d-m-Y')
            : ($date ? \Carbon\Carbon::parse($date)->format('d-m-Y') : '');

        $usernames = $items
            ->flatMap(fn($i) => [$i->username, $i->nama_spv])
            ->filter()
            ->unique()
            ->values();

        $userMap  = \App\Models\User::whereIn('username', $usernames)->pluck('name', 'username');
        $namaUser = fn($username) => $username ? ($userMap[$username] ?? $username) : '';

        $toArray = function ($value) {
            if (is_array($value)) {
                return $value;
            }
            if (is_string($value) && $value !== '') {
                $decoded = json_decode($value, true);
                return is_array($decoded) ? $decoded : [];
            }
            return [];
        };

        $rows = [];

        foreach ($items as $item) {
            $qc  = $namaUser($item->username) ?: '-';
            $spv = $namaUser($item->nama_spv) ?: '-';

            foreach ($toArray($item->peneraan) as $p) {
                $p = (array) $p;

                $rows[] = [
                    'kode'     => $p['kode_thermometer'] ?? '',
                    'area'     => $p['area'] ?? '',
                    'standar'  => $p['standar'] ?? '',
                    'pukul'    => $p['pukul'] ?? '',
                    'hasil'    => $p['hasil_tera'] ?? '',
                    'tindakan' => $p['tindakan_perbaikan'] ?? '',
                    'qc'       => $qc,
                    'spv'      => $spv,
                ];
            }
        }

        while (count($rows) < 8) {
            $rows[] = [
                'kode' => '', 'area' => '', 'standar' => '', 'pukul' => '',
                'hasil' => '', 'tindakan' => '', 'qc' => '', 'spv' => '',
            ];
        }

        $pages = array_chunk($rows, 8);

        $pemeriksa    = $first ? $namaUser(trim((string) $first->username)) : '';
        $tglPemeriksa = ($first && $first->created_at)
            ? \Carbon\Carbon::parse($first->created_at)->format('d-m-Y')
            : '';

        $semuaDisetujui = $items->isNotEmpty()
            && $items->every(fn($i) => !empty($i->nama_spv));

        $spv    = '';
        $tglSpv = '';

        if ($semuaDisetujui) {
            $spv    = $namaUser(trim((string) $first->nama_spv));
            $spvRow = $items->first(fn($i) => !empty($i->tgl_update_spv));
            $tglSpv = $spvRow
                ? \Carbon\Carbon::parse($spvRow->tgl_update_spv)->format('d-m-Y')
                : '';
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Peneraan Thermometer');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(true, 5);

        $pdf->SetFont('helvetica', '', 6);
        $pdf->AddPage();

        $html = view('reports.peneraan-termometer', compact(
            'pages',
            'tanggal',
            'noDokumen',
            'pemeriksa',
            'tglPemeriksa',
            'spv',
            'tglSpv',
            'semuaDisetujui'
        ))->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('Peneraan_Thermometer_' . date('d-m-Y_His') . '.pdf', 'I');

        exit();
    }
}
