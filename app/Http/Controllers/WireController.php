<?php

namespace App\Http\Controllers;

use App\Models\Wire;
use App\Models\Produk;
use App\Models\Mesin;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use TCPDF;

class WireController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant  = Auth::user()->plant;

        $data = Wire::query()
        ->where('plant', $userPlant) 
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('nama_supplier', 'like', "%{$search}%")
                ->orWhere('data_wire', 'like', "%{$search}%");
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

        return view('form.wire.index', compact('data', 'search', 'date', 'shift'));
    }

    public function exportPdf(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift'); // Ambil input Shift
        $userPlant = Auth::user()->plant;

        $items = Wire::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                ->orWhere('nama_supplier', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->when($shift, function ($query) use ($shift) {
                // Filter Query berdasarkan Shift yang dipilih
            $query->where('shift', $shift);
        })
        ->orderBy('date', 'asc')
        ->orderBy('shift', 'asc')
        ->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // Setup PDF (Landscape)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Laporan Data No. Lot Wire');
        
        // Remove default header/footer
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        // Set Margins (Left, Top, Right) -> 5mm
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);
        $pdf->SetFont('helvetica', '', 8);

        $pdf->AddPage();

        // Render View
        $html = view('reports.wire', compact('items', 'request'))->render();

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Laporan_Wire_' . date('d-m-Y_His') . '.pdf', 'I');
        exit();
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

        return view('form.wire.create', compact('produks', 'mesins', 'suppliers'));
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
            'catatan'         => 'nullable|string',
            'data_wire'       => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'nama_supplier', 'catatan']);
        $data['username']        = $username;
        $data['plant']           = $userPlant;
        $data['status_spv']      = "0";
        $data['data_wire']       = json_encode($request->input('data_wire', []), JSON_UNESCAPED_UNICODE);

        Wire::create($data);

        return redirect()->route('wire.index')
        ->with('success', 'Data No. Lot Wire berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $wire = Wire::where('uuid', $uuid)->firstOrFail();
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

        $wireData = !empty($wire->data_wire) ? json_decode($wire->data_wire, true) : [];

        return view('form.wire.update', compact('wire', 'produks', 'wireData', 'mesins', 'suppliers'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $wire = Wire::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';

        $request->validate([
            'date'           => 'required|date',
            'shift'          => 'required',
            'nama_produk'    => 'required|string',
            'nama_supplier'  => 'required|string',
            'catatan'        => 'nullable|string',
            'data_wire'      => 'nullable|array',
        ]);

        $data_wire = $request->input('data_wire', []);

        $wire->update([
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'nama_supplier'    => $request->nama_supplier,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'data_wire'        => json_encode($data_wire, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('wire.index')->with('success', 'Data No. Lot Wire berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $wire = Wire::where('uuid', $uuid)->firstOrFail();
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

        $wireData = !empty($wire->data_wire) ? json_decode($wire->data_wire, true) : [];

        return view('form.wire.edit', compact('wire', 'produks', 'wireData', 'mesins', 'suppliers'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $wire = Wire::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';

        $request->validate([
            'date'           => 'required|date',
            'shift'          => 'required',
            'nama_produk'    => 'required|string',
            'nama_supplier'  => 'required|string',
            'catatan'        => 'nullable|string',
            'data_wire'      => 'nullable|array',
        ]);

        $data_wire = $request->input('data_wire', []);

        $wire->update([
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'nama_supplier'    => $request->nama_supplier,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'data_wire'        => json_encode($data_wire, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('wire.index')->with('success', 'Data No. Lot Wire berhasil diperbarui');
    }

    public function verification(Request $request)
    {
       $search     = $request->input('search');
       $date = $request->input('date');
       $userPlant  = Auth::user()->plant;

       $data = Wire::query()
       ->where('plant', $userPlant) 
       ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('nama_supplier', 'like', "%{$search}%")
            ->orWhere('data_wire', 'like', "%{$search}%");
        });
    })
       ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
       ->orderBy('date', 'desc')
       ->orderBy('created_at', 'desc')
       ->paginate(10)
       ->appends($request->all());

       return view('form.wire.index', compact('data', 'search', 'date'));
   }

   public function updateVerification(Request $request, $uuid)
   {
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
        'tgl_update_spv'
    ]);

    $wire = Wire::where('uuid', $uuid)->firstOrFail();

    $wire->update([
        'status_spv' => $request->status_spv,
        'catatan_spv' => $request->catatan_spv,
        'nama_spv' => Auth::user()->username,
        'tgl_update_spv' => now(),
    ]);

    return redirect()->route('wire.index')
    ->with('success', 'Status Verifikasi Data No. Lot Wire berhasil diperbarui.');
}

public function destroy($uuid)
{
    $wire = Wire::where('uuid', $uuid)->firstOrFail();
    $wire->delete();
    return redirect()->route('wire.index')->with('success', 'Wire berhasil dihapus');
}

public function recyclebin()
{
    $wire = Wire::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.wire.recyclebin', compact('wire'));
}
public function restore($uuid)
{
    $wire = Wire::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $wire->restore();

    return redirect()->route('wire.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $wire = Wire::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $wire->forceDelete();

    return redirect()->route('wire.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}
}
