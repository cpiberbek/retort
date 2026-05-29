<?php

namespace App\Http\Controllers;

use App\Models\Master_Premix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterPremixController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $userPlantUuid = Auth::user()->plant;

        $data = Master_Premix::with(['creator', 'dataPlant'])
            ->where('plant_uuid', $userPlantUuid)
            ->when($search, function ($query, $search) {
                $query->where('nama_premix', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('form.master_premix.index', compact('data'));
    }

    public function create()
    {
        return view('form.master_premix.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_premix'   => 'required|string|max:255',
            'kode_internal' => 'nullable|string|max:255',
            'satuan'        => 'required|in:kg,gr,liter,sak'
        ]);

        Master_Premix::create([
            'nama_premix'   => $request->nama_premix,
            'kode_internal' => $request->kode_internal,
            'satuan'        => $request->satuan,
            'plant_uuid'    => Auth::user()->plant,
            'created_by'    => Auth::user()->uuid,
        ]);

        return redirect()
            ->route('premix.index')
            ->with('success', 'Data premix berhasil disimpan');
    }

    public function edit($uuid)
    {
        $premix = Master_Premix::where('uuid', $uuid)
            ->where('plant_uuid', Auth::user()->plant)
            ->firstOrFail();

        return view('form.master_premix.edit', compact('premix'));
    }

    public function update(Request $request, Master_Premix $premix)
    {
        $request->validate([
            'nama_premix'   => 'required|string|max:255',
            'kode_internal' => 'nullable|string|max:255',
            'satuan'        => 'required|in:kg,gr,liter,sak'
        ]);

        if ($premix->plant_uuid !== Auth::user()->plant) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $premix->update([
            'nama_premix'   => $request->nama_premix,
            'kode_internal' => $request->kode_internal,
            'satuan'        => $request->satuan,
        ]);

        return redirect()
            ->route('premix.index')
            ->with('success', 'Data premix berhasil diupdate');
    }

    public function destroy(Master_Premix $premix)
    {
        $premix->update([
            'deleted_by' => Auth::user()->uuid
        ]);

        $premix->delete();

        return redirect()
            ->route('premix.index')
            ->with('success', 'Data premix berhasil dihapus');
    }
}
