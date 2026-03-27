<?php

namespace App\Http\Controllers;

use App\Models\Traceability;
use App\Models\List_form;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TraceabilityController extends Controller
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

        // $type_user = Auth::user()->type_user;

        $data = Traceability::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('penyebab', 'like', "%{$search}%")
                ->orWhere('asal_informasi', 'like', "%{$search}%")
                ->orWhere('nama_dagang', 'like', "%{$search}%")
                ->orWhere('jenis_pangan', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('no_pendaftaran', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.traceability.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $userPlant = Auth::user()->plant;

        $traceability = Traceability::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $traceability->status_spv = $request->status_spv; 
        $traceability->catatan_spv = $request->catatan_spv;
        $traceability->nama_spv = Auth::user()->username;
        $traceability->tgl_update_spv = now();
        $traceability->save();

        return redirect()->route('traceability.index')
        ->with('success', 'Status Verifikasi Laporan Traceability Berhasil diperbarui.');
    }

    public function updateApproval(Request $request, $uuid)
    {
        $request->validate([
            'status_manager' => 'required|in:1,2',
            'catatan_manager' => 'nullable|string|max:255',
        ]);
        $userPlant = Auth::user()->plant;

        $traceability = Traceability::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $traceability->persetujuan_trace = "Setuju"; 
        $traceability->status_manager = $request->status_manager; 
        $traceability->catatan_manager = $request->catatan_manager;
        $traceability->nama_manager = Auth::user()->username;
        $traceability->tgl_update_manager = now();
        $traceability->save();

        return redirect()->route('traceability.index')
        ->with('success', 'Status Persetujuan Laporan Traceability Berhasil diperbarui.');
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $forms = List_form::where('plant', $userPlant)->get();

        return view('form.traceability.create', compact('forms'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTT';
        $userPlant  = Auth::user()->plant;

        $request->validate([
            'date'             => 'required|date',
            'penyebab'         => 'required|string',
            'asal_informasi'   => 'required|string',
            'jenis_pangan'     => 'nullable|string',
            'nama_dagang'      => 'nullable|string',
            'berat_bersih'     => 'nullable|numeric',
            'jenis_kemasan'    => 'nullable|string',
            'kode_produksi'    => 'nullable|string',
            'tanggal_produksi' => 'nullable|date',
            'tanggal_kadaluarsa'  => 'nullable|string',
            'no_pendaftaran'      => 'nullable|string',
            'jumlah_produksi'     => 'nullable|numeric',
            'tindak_lanjut'       => 'nullable|string',
            'kelengkapan_form'              => 'nullable|array',
            'kelengkapan_form.*.laporan'    => 'nullable|string',
            'kelengkapan_form.*.no_dokumen'     => 'nullable|string',
            'kelengkapan_form.*.kelengkapan'    => 'nullable|string',
            'kelengkapan_form.*.waktu_telusur'  => 'nullable|string',
            'total_waktu'   => 'nullable|string',
            'kesimpulan'    => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'penyebab', 'asal_informasi', 'jenis_pangan', 'nama_dagang', 'berat_bersih', 'jenis_kemasan', 'kode_produksi', 'tanggal_produksi', 'tanggal_kadaluarsa', 'no_pendaftaran', 'jumlah_produksi', 'tindak_lanjut', 'total_waktu', 'kesimpulan'
        ]);

        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['status_manager']      = "0";
        // $data['tgl_update_produksi'] = now()->addHour();
        $data['status_spv']          = "0";
        $data['kelengkapan_form']    = json_encode($request->input('kelengkapan_form', []), JSON_UNESCAPED_UNICODE);

        Traceability::create($data);

        return redirect()->route('traceability.index')->with('success', 'Laporan traceability berhasil dibuat');
    }

    public function edit(string $uuid)
    {
        $traceability = Traceability::where('uuid', $uuid)
        ->where('plant', Auth::user()->plant)
        ->firstOrFail();

        $userPlant = Auth::user()->plant;
        $forms = List_form::where('plant', $userPlant)->get();

        $traceabilityData = !empty($traceability->kelengkapan_form)
        ? json_decode($traceability->kelengkapan_form, true)
        : [];
        return view('form.traceability.edit', compact('traceability', 'traceabilityData', 'forms'));
    }

    public function update(Request $request, string $uuid)
    {
        $userPlant = Auth::user()->plant;
        $userType  = Auth::user()->type_user ?? null;
        $username_updated = Auth::user()->username ?? 'User RTM';

        $traceability = Traceability::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $request->validate([
            'date'             => 'required|date',
            'penyebab'         => 'required|string',
            'asal_informasi'   => 'required|string',
            'jenis_pangan'     => 'nullable|string',
            'nama_dagang'      => 'nullable|string',
            'berat_bersih'     => 'nullable|numeric',
            'jenis_kemasan'    => 'nullable|string',
            'kode_produksi'    => 'nullable|string',
            'tanggal_produksi' => 'nullable|date',
            'tanggal_kadaluarsa'  => 'nullable|string',
            'no_pendaftaran'      => 'nullable|string',
            'jumlah_produksi'     => 'nullable|numeric',
            'tindak_lanjut'       => 'nullable|string',
            'kelengkapan_form'              => 'nullable|array',
            'kelengkapan_form.*.laporan'    => 'nullable|string',
            'kelengkapan_form.*.no_dokumen'     => 'nullable|string',
            'kelengkapan_form.*.kelengkapan'    => 'nullable|string',
            'kelengkapan_form.*.waktu_telusur'  => 'nullable|string',
            'total_waktu'   => 'nullable|string',
            'kesimpulan'    => 'nullable|string',
        ]);

        $data = [
            'date'             => $request->date,
            'penyebab'         => $request->penyebab,
            'asal_informasi'   => $request->asal_informasi,
            'jenis_pangan'     => $request->jenis_pangan,
            'nama_dagang'      => $request->nama_dagang,
            'berat_bersih'     => $request->berat_bersih,
            'jenis_kemasan'    => $request->jenis_kemasan,
            'kode_produksi'    => $request->kode_produksi,
            'tanggal_produksi'    => $request->tanggal_produksi,
            'tanggal_kadaluarsa'  => $request->tanggal_kadaluarsa,
            'no_pendaftaran'      => $request->no_pendaftaran,
            'jumlah_produksi'     => $request->jumlah_produksi,
            'tindak_lanjut'       => $request->tindak_lanjut,
            'total_waktu'         => $request->total_waktu,
            'kesimpulan'          => $request->kesimpulan,
            'kelengkapan_form'    => json_encode($request->input('kelengkapan_form', []), JSON_UNESCAPED_UNICODE),
            'username_updated'    => $username_updated,
        ];

        $traceability->update($data);

        return redirect()->route('traceability.index')->with('success', 'Data Laporan traceability berhasil diperbarui.');
    }

    public function destroy(string $uuid)
    {
        $userPlant = Auth::user()->plant;

        $traceability = Traceability::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $traceability->delete();

        return redirect()->route('traceability.index')
        ->with('success', 'Data Laporan Traceability berhasil dihapus'); 
    }

    public function recyclebin()
    {
        $traceability = Traceability::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.traceability.recyclebin', compact('traceability'));
    }
    public function restore($uuid)
    {
        $traceability = Traceability::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $traceability->restore();

        return redirect()->route('traceability.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $traceability = Traceability::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $traceability->forceDelete();

        return redirect()->route('traceability.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

}
