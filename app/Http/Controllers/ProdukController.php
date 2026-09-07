<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Master_Raw_Material;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan semua data produk sesuai plant user login
    public function index(Request $request)
    {
        $search = $request->input('search');
        $userPlantUuid = Auth::user()->plant;

        $produk = Produk::with('dataPlant')
            ->where('plant', $userPlantUuid)
            ->when($search, function ($query, $search) {
                $query->where('nama_produk', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $rawMaterials = Master_Raw_Material::where('plant_uuid', $userPlantUuid)
            ->orderBy('nama_bahan_baku')
            ->get();

        return view('produk.index', compact('produk', 'rawMaterials'));
    }

    // Tampilkan form tambah produk
    public function create()
    {
        return view('produk.create');
    }

    // Simpan data baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255'
        ]);

        $user = Auth::user();

        Produk::create([
            'username'    => $user->username,
            'plant'       => $user->plant,
            'nama_produk' => $request->nama_produk,
            'bahan_baku'  => [],
        ]);

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    // Tampilkan form edit produk berdasarkan UUID
    public function edit($uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $produk = Produk::where('uuid', $uuid)
            ->where('plant', $userPlantUuid)
            ->firstOrFail();

        return view('produk.edit', compact('produk'));
    }

    // Update data produk
    public function update(Request $request, $uuid)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255'
        ]);

        $userPlantUuid = Auth::user()->plant;

        $produk = Produk::where('uuid', $uuid)
            ->where('plant', $userPlantUuid)
            ->firstOrFail();

        $produk->update([
            'nama_produk' => $request->nama_produk
        ]);

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    // Update bahan baku/formula produk
    public function updateBahanBaku(Request $request, $uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $produk = Produk::where('uuid', $uuid)
            ->where('plant', $userPlantUuid)
            ->firstOrFail();

        $request->validate([
            'bahan_baku'   => 'nullable|array',
            'bahan_baku.*' => 'required|string|max:255',
        ]);

        // Hapus duplikat dan rapikan index array
        $bahanBaku = collect($request->input('bahan_baku', []))
            ->filter(function ($item) {
                return !empty(trim($item));
            })
            ->map(function ($item) {
                return trim($item);
            })
            ->unique()
            ->values()
            ->toArray();

        $produk->update([
            'bahan_baku' => $bahanBaku
        ]);

        return redirect()
            ->route('produk.index')
            ->with('success', 'Bahan baku produk berhasil diperbarui');
    }

    // Hapus produk
    public function destroy($uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $produk = Produk::where('uuid', $uuid)
            ->where('plant', $userPlantUuid)
            ->firstOrFail();

        $produk->delete();

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
