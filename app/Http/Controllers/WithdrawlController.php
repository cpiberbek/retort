<?php

namespace App\Http\Controllers;

use App\Models\Withdrawl;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WithdrawlController extends Controller
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

        $data = Withdrawl::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('no_withdrawl', 'like', "%{$search}%")
                ->orWhere('rincian', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.withdrawl.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $userPlant = Auth::user()->plant;

        $withdrawl = Withdrawl::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $withdrawl->status_spv = $request->status_spv; 
        $withdrawl->catatan_spv = $request->catatan_spv;
        $withdrawl->nama_spv = Auth::user()->username;
        $withdrawl->tgl_update_spv = now();
        $withdrawl->save();

        return redirect()->route('withdrawl.index')
        ->with('success', 'Status Verifikasi Laporan Withdrawl Berhasil diperbarui.');
    }

    public function updateApproval(Request $request, $uuid)
    {
        $request->validate([
            'status_manager' => 'required|in:1,2',
            'catatan_manager' => 'nullable|string|max:255',
        ]);

        $userPlant = Auth::user()->plant;

        $withdrawl = Withdrawl::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $withdrawl->status_manager = $request->status_manager; 
        $withdrawl->catatan_manager = $request->catatan_manager;
        $withdrawl->nama_manager = Auth::user()->username;
        $withdrawl->tgl_update_manager = now();
        $withdrawl->save();

        return redirect()->route('withdrawl.index')
        ->with('success', 'Status Persetujuan Laporan Withdrawl Berhasil diperbarui.');
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $suppliers = Supplier::where('plant', $userPlant)
        ->where('jenis_barang', 'Distributor')
        ->orderBy('nama_supplier')
        ->get();

        return view('form.withdrawl.create', compact('produks', 'suppliers'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTT';
        $userPlant  = Auth::user()->plant;

    // Validasi utama + rincian nested
        $request->validate([
            'date'             => 'required|date',
            'no_withdrawl'     => 'required|string',
            'nama_produk'      => 'required|string',
            'kode_produksi'    => 'required|string',
            'exp_date'         => 'required|date',
            'jumlah_produksi'  => 'nullable|numeric',
            'jumlah_edar'      => 'nullable|numeric',
            'tanggal_edar'     => 'nullable|date',
            'jumlah_tarik'     => 'nullable|numeric',
            'tanggal_tarik'    => 'nullable|date',
            'rincian'          => 'nullable|array',
            'rincian.*.nama_supplier' => 'required|string',
            'rincian.*.alamat'        => 'required|string',
            'rincian.*.jumlah'        => 'required|numeric|min:0',
        ]);

        $data = $request->only([
            'date', 'no_withdrawl', 'nama_produk', 'kode_produksi',
            'exp_date', 'jumlah_produksi', 'jumlah_edar', 'jumlah_tarik', 'tanggal_edar', 'tanggal_tarik'
        ]);

        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['status_manager']      = "0";
        // $data['tgl_update_produksi'] = now()->addHour();
        $data['status_spv']          = "0";
        $data['rincian']             = json_encode($request->input('rincian', []), JSON_UNESCAPED_UNICODE);

        Withdrawl::create($data);

        return redirect()->route('withdrawl.index')->with('success', 'Laporan Withdrawl berhasil dibuat');
    }

    public function edit(string $uuid)
    {
        $withdrawl = withdrawl::where('uuid', $uuid)
        ->where('plant', Auth::user()->plant)
        ->firstOrFail();

        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $suppliers = Supplier::where('plant', $userPlant)
        ->where('jenis_barang', 'Distributor')
        ->orderBy('nama_supplier')
        ->get();

        $withdrawlData = !empty($withdrawl->rincian)
        ? json_decode($withdrawl->rincian, true)
        : [];
        return view('form.withdrawl.edit', compact('withdrawl', 'produks', 'suppliers', 'withdrawlData'));
    }

    public function update(Request $request, string $uuid)
    {
        $userPlant = Auth::user()->plant;
        $userType  = Auth::user()->type_user ?? null;

        $withdrawl = Withdrawl::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

    // Validasi utama + rincian nested
        $request->validate([
            'date'             => 'required|date',
            'no_withdrawl'     => 'required|string',
            'nama_produk'      => 'required|string',
            'kode_produksi'    => 'required|string',
            'exp_date'         => 'required|date',
            'jumlah_produksi'  => 'nullable|numeric',
            'jumlah_edar'      => 'nullable|numeric',
            'tanggal_edar'     => 'nullable|date',
            'jumlah_tarik'     => 'nullable|numeric',
            'tanggal_tarik'    => 'nullable|date',
            'rincian'          => 'nullable|array',
            'rincian.*.nama_supplier' => 'required|string',
            'rincian.*.alamat'        => 'required|string',
            'rincian.*.jumlah'        => 'required|numeric|min:0',
        ]);

    // Hanya update username_updated untuk type_user 4 & 8
        $usernameUpdated = in_array($userType, [4,8])
        ? Auth::user()->username
        : $withdrawl->username_updated;

        $data = [
            'date'             => $request->date,
            'no_withdrawl'     => $request->no_withdrawl,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'exp_date'         => $request->exp_date,
            'jumlah_produksi'  => $request->jumlah_produksi,
            'jumlah_edar'      => $request->jumlah_edar,
            'tanggal_edar'     => $request->tanggal_edar,
            'jumlah_tarik'     => $request->jumlah_tarik,
            'tanggal_tarik'    => $request->tanggal_tarik,
            'rincian'          => json_encode($request->input('rincian', []), JSON_UNESCAPED_UNICODE),
            'username_updated' => $usernameUpdated,
        ];

        $withdrawl->update($data);

        return redirect()->route('withdrawl.index')->with('success', 'Data Laporan Withdrawl berhasil diperbarui.');
    }

    public function destroy(string $uuid)
    {
        $userPlant = Auth::user()->plant;

        $withdrawl = Withdrawl::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $withdrawl->delete();

        return redirect()->route('withdrawl.index')
        ->with('success', 'Data Laporan Withdrawl berhasil dihapus'); 
    }

    public function recyclebin()
    {
        $withdrawl = Withdrawl::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.withdrawl.recyclebin', compact('withdrawl'));
    }
    public function restore($uuid)
    {
        $withdrawl = Withdrawl::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $withdrawl->restore();

        return redirect()->route('withdrawl.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $withdrawl = Withdrawl::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $withdrawl->forceDelete();

        return redirect()->route('withdrawl.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

}
