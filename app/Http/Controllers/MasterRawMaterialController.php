<?php

namespace App\Http\Controllers;

use App\Models\Master_Raw_Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterRawMaterialController extends Controller
{
    public function index(Request $request) {
        $search = $request->input('search');
        
        // SESUAIKAN DISINI: Pakai ->plant bukan ->plant_uuid jika itu yang ada di tabel users
        $userPlantUuid = Auth::user()->plant; 

        $data = Master_Raw_Material::with(['creator', 'dataPlant'])
                ->where('plant_uuid', $userPlantUuid) 
                ->when($search, function($query, $search) {
                    $query->where('nama_bahan_baku', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();

        return view('form.master_raw_material.index', compact('data'));
    }


    public function create() {
        return view('form.master_raw_material.create');
    }

    public function store(Request $request) {
        $request->validate([
            'nama_bahan_baku' => 'required|string|max:255',
            'kode_internal'   => 'nullable|string|max:255', // Bisa diubah 'required' jika wajib diisi
            'satuan'          => 'required|in:kg,gr,liter,sak' // Validasi dropdown
        ]);

        Master_Raw_Material::create([
            'nama_bahan_baku' => $request->nama_bahan_baku,
            'kode_internal'   => $request->kode_internal,
            'satuan'          => $request->satuan,
            'plant_uuid'      => Auth::user()->plant, 
            'created_by'      => Auth::user()->uuid,
        ]);

        return redirect()->route('raw-material.index')->with('success', 'Data berhasil disimpan');
    }

    // Edit, Update, Destroy tetap sama namun pastikan filter plant di firstOrFail() untuk keamanan
    public function edit($uuid) {
        // Gunakan Auth::user()->plant agar sesuai dengan data di tabel users
        $raw_material = Master_Raw_Material::where('uuid', $uuid)
                        ->where('plant_uuid', Auth::user()->plant) // Sesuaikan di sini
                        ->firstOrFail();

        return view('form.master_raw_material.edit', compact('raw_material'));
    }
    
    public function update(Request $request, Master_Raw_Material $raw_material) {
        $request->validate([
            'nama_bahan_baku' => 'required|string|max:255',
            'kode_internal'   => 'nullable|string|max:255',
            'satuan'          => 'required|in:kg,gr,liter,sak'
        ]);

        if ($raw_material->plant_uuid !== Auth::user()->plant) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data ini.');
        }

        $raw_material->update([
            'nama_bahan_baku' => $request->nama_bahan_baku,
            'kode_internal'   => $request->kode_internal,
            'satuan'          => $request->satuan,
        ]);

        return redirect()->route('raw-material.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy(Master_Raw_Material $raw_material) {
        $raw_material->update(['deleted_by' => Auth::user()->uuid]);
        $raw_material->delete();
        return redirect()->route('raw-material.index')->with('success', 'Data berhasil dihapus');
    }
}