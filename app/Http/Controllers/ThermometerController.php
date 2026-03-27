<?php

namespace App\Http\Controllers;

use App\Models\Thermometer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return redirect()->route('thermometer.index')
        ->with('success', 'Status verifikasi Peneraan Thermometer berhasil diperbarui.');
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
        // 1. Ambil Data
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

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Portrait, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Peneraan Thermometer');

        // Hilangkan Header/Footer Bawaan
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set Margin
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);

        // Set Font Default
        $pdf->SetFont('helvetica', '', 9);

        $pdf->AddPage();

        // 3. Render
        $html = view('reports.peneraan-termometer', compact('items', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Peneraan_Thermometer_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
