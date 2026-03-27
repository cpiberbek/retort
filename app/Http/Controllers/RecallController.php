<?php

namespace App\Http\Controllers;

use App\Models\Recall;
use App\Models\User;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecallController extends Controller
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

        $data = Recall::query()
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

        return view('form.recall.index', compact('data', 'search', 'date', 'type_user'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $forms = List_form::where('plant', $userPlant)->get();

        return view('form.recall.create', compact('forms'));
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
            'tanggal_kadaluarsa'  => 'nullable|date',
            'no_pendaftaran'      => 'nullable|string',
            'diproduksi_oleh'     => 'nullable|string',
            'jumlah_produksi'     => 'nullable|numeric',
            'jumlah_terkirim'     => 'nullable|numeric',
            'jumlah_tersisa'      => 'nullable|numeric',
            'tindak_lanjut'       => 'nullable|string',
            'distribusi'              => 'nullable|array',
            'neraca_penarikan'        => 'nullable|array',
            'simulasi'                => 'nullable|array',
            'total_waktu'             => 'nullable|string',
            'evaluasi'                => 'nullable|array',
        ]);

        $data = $request->only([
            'date', 'penyebab', 'asal_informasi', 'jenis_pangan', 'nama_dagang', 'berat_bersih', 'jenis_kemasan', 'kode_produksi', 'tanggal_produksi', 'tanggal_kadaluarsa', 'no_pendaftaran', 'diproduksi_oleh', 'jumlah_produksi', 'jumlah_terkirim', 'jumlah_tersisa','tindak_lanjut', 'total_waktu'
        ]);

        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['status_manager']      = "0";
        // $data['tgl_update_produksi'] = now()->addHour();
        $data['status_spv']          = "0";
        $data['distribusi']          = json_encode($request->input('distribusi', []), JSON_UNESCAPED_UNICODE);
        $data['neraca_penarikan']    = json_encode($request->input('neraca_penarikan', []), JSON_UNESCAPED_UNICODE);
        $data['simulasi']            = json_encode($request->input('simulasi', []), JSON_UNESCAPED_UNICODE);
        $data['evaluasi']            = json_encode($request->input('evaluasi', []), JSON_UNESCAPED_UNICODE);

        recall::create($data);

        return redirect()->route('recall.index')->with('success', 'Laporan Recall berhasil dibuat');
    }

    public function edit(string $uuid)
    {
        $userPlant = Auth::user()->plant;

        $recall = Recall::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $forms = List_form::where('plant', $userPlant)->get();

    // decode JSON fields supaya bisa diloop di blade
        $recall->distribusi = !empty($recall->distribusi)
        ? json_decode($recall->distribusi, true)
        : [];

        $recall->neraca_penarikan = !empty($recall->neraca_penarikan)
        ? json_decode($recall->neraca_penarikan, true)
        : [];

        $recall->simulasi = !empty($recall->simulasi)
        ? json_decode($recall->simulasi, true)
        : [];

        $recall->evaluasi = !empty($recall->evaluasi)
        ? json_decode($recall->evaluasi, true)
        : [];

    // kelengkapan form
        $recallData = !empty($recall->kelengkapan_form)
        ? json_decode($recall->kelengkapan_form, true)
        : [];

        return view('form.recall.edit', compact(
            'recall',
            'recallData',
            'forms'
        ));
    }

    public function update(Request $request, string $uuid)
    {
        $userPlant = Auth::user()->plant;
        $userType  = Auth::user()->type_user ?? null;
        $username_updated = Auth::user()->username ?? 'User RTT';

        $recall = recall::where('uuid', $uuid)
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
            'tanggal_kadaluarsa'  => 'nullable|date',
            'no_pendaftaran'      => 'nullable|string',
            'diproduksi_oleh'     => 'nullable|string',
            'jumlah_produksi'     => 'nullable|numeric',
            'jumlah_terkirim'     => 'nullable|numeric',
            'jumlah_tersisa'      => 'nullable|numeric',
            'tindak_lanjut'       => 'nullable|string',
            'distribusi'              => 'nullable|array',
            'neraca_penarikan'        => 'nullable|array',
            'simulasi'                => 'nullable|array',
            'total_waktu'             => 'nullable|string',
            'evaluasi'                => 'nullable|array',
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
            'diproduksi_oleh'     => $request->diproduksi_oleh,
            'jumlah_produksi'     => $request->jumlah_produksi,
            'jumlah_terkirim'     => $request->jumlah_terkirim,
            'jumlah_tersisa'      => $request->jumlah_tersisa,
            'tindak_lanjut'       => $request->tindak_lanjut,
            'total_waktu'         => $request->total_waktu,
            'distribusi'    => json_encode($request->input('distribusi', []), JSON_UNESCAPED_UNICODE),
            'neraca_penarikan'    => json_encode($request->input('neraca_penarikan', []), JSON_UNESCAPED_UNICODE),
            'simulasi'    => json_encode($request->input('simulasi', []), JSON_UNESCAPED_UNICODE),
            'evaluasi'    => json_encode($request->input('evaluasi', []), JSON_UNESCAPED_UNICODE),
            'username_updated'    => $username_updated,
        ];

        $recall->update($data);

        return redirect()->route('recall.index')->with('success', 'Data Laporan Recall berhasil diperbarui.');
    }

    public function destroy(string $uuid)
    {
        $userPlant = Auth::user()->plant;

        $recall = recall::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $recall->delete();

        return redirect()->route('recall.index')
        ->with('success', 'Data Laporan Recall berhasil dihapus'); 
    }

    public function recyclebin()
    {
        $recall = Recall::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.recall.recyclebin', compact('recall'));
    }
    public function restore($uuid)
    {
        $recall = Recall::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $recall->restore();

        return redirect()->route('recall.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $recall = Recall::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $recall->forceDelete();

        return redirect()->route('recall.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
    // public function updateVerification(Request $request, $uuid)
    // {
    //     $request->validate([
    //         'status_spv' => 'required|in:1,2',
    //         'catatan_spv' => 'nullable|string|max:255',
    //     ]);

    //     $userPlant = Auth::user()->plant;

    //     $recall = recall::where('uuid', $uuid)
    //     ->where('plant', $userPlant)
    //     ->firstOrFail();

    //     $recall->status_spv = $request->status_spv; 
    //     $recall->catatan_spv = $request->catatan_spv;
    //     $recall->nama_spv = Auth::user()->username;
    //     $recall->tgl_update_spv = now();
    //     $recall->save();

    //     return redirect()->route('recall.index')
    //     ->with('success', 'Status Verifikasi Laporan recall Berhasil diperbarui.');
    // }

    // public function updateApproval(Request $request, $uuid)
    // {
    //     $request->validate([
    //         'status_manager' => 'required|in:1,2',
    //         'catatan_manager' => 'nullable|string|max:255',
    //     ]);
    //     $userPlant = Auth::user()->plant;

    //     $recall = recall::where('uuid', $uuid)
    //     ->where('plant', $userPlant)
    //     ->firstOrFail();

    //     $recall->persetujuan_trace = "Setuju"; 
    //     $recall->status_manager = $request->status_manager; 
    //     $recall->catatan_manager = $request->catatan_manager;
    //     $recall->nama_manager = Auth::user()->username;
    //     $recall->tgl_update_manager = now();
    //     $recall->save();

    //     return redirect()->route('recall.index')
    //     ->with('success', 'Status Persetujuan Laporan recall Berhasil diperbarui.');
    // }

}
