<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area_hygiene; 
use Illuminate\Support\Facades\Auth;

class Area_hygieneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $userPlantUuid = Auth::user()->plant; 

        $area_hygiene = Area_hygiene::where('plant', $userPlantUuid)
        ->when($search, function($query, $search) {
            $query->where('area', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('area_hygiene.index', compact('area_hygiene'));
    }

    public function create()
    {
        return view('area_hygiene.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'area' => 'required|string|max:255'
        ]);

        $user = Auth::user(); 

        Area_hygiene::create([
            'username' => $user->username,  
            'plant' => $user->plant,       
            'area' => $request->area
        ]);

        return redirect()->route('area_hygiene.index')->with('success', 'Area Hygiene berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $area_hygiene = Area_hygiene::where('uuid', $uuid)
        ->where('plant', $userPlantUuid) 
        ->firstOrFail();

        return view('area_hygiene.edit', compact('area_hygiene'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'area' => 'required|string|max:255'
        ]);

        $userPlantUuid = Auth::user()->plant;

        $area_hygiene = Area_hygiene::where('uuid', $uuid)
        ->where('plant', $userPlantUuid)
        ->firstOrFail();

        $area_hygiene->update([
            'area' => $request->area
        ]);

        return redirect()->route('area_hygiene.index')->with('success', 'Area Hygiene berhasil diupdate');
    }

    // public function destroy($uuid)
    // {
    //     $userPlantUuid = Auth::user()->plant;

    //     $area_hygiene = Area_hygiene::where('uuid', $uuid)
    //     ->where('plant', $userPlantUuid) 
    //     ->firstOrFail();

    //     $area_hygiene->delete();

    //     return redirect()->route('area_hygiene.index')->with('success', 'Area Hygiene berhasil dihapus');
    // }

    public function destroy($uuid)
    {
        $area_hygiene = Area_hygiene::where('uuid', $uuid)->firstOrFail();
        $area_hygiene->delete();
        return redirect()->route('area_hygiene.index')->with('success', 'Area Hygiene berhasil dihapus');
    }

    public function recyclebin()
    {
        $area_hygiene = Area_hygiene::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('area_hygiene.recyclebin', compact('area_hygiene'));
    }
    public function restore($uuid)
    {
        $area_hygiene = Area_hygiene::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $area_hygiene->restore();

        return redirect()->route('area_hygiene.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $area_hygiene = Area_hygiene::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $area_hygiene->forceDelete();

        return redirect()->route('area_hygiene.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
}
