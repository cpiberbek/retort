<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area_sanitasi; 
use Illuminate\Support\Facades\Auth;

class Area_sanitasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $userPlantUuid = Auth::user()->plant; 

        $area_sanitasi = Area_sanitasi::where('plant', $userPlantUuid)
        ->when($search, function($query, $search) {
            $query->where('area', 'subarea', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('area_sanitasi.index', compact('area_sanitasi'));
    }

    public function create()
    {
 
        return view('area_sanitasi.create'); 
    }
   
    public function store(Request $request)
    {
        $request->validate([
            'area' => 'required|string',
            'sub_area' => 'required|string',
            'bagian'  => 'nullable|array',
        ]);

        $user = Auth::user(); 

        Area_sanitasi::create([
            'username' => $user->username,  
            'plant'    => $user->plant,       
            'area'     => $request->area,
            'sub_area' => $request->sub_area,
            'bagian'   => json_encode($request->input('bagian', []), JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('area_sanitasi.index')
        ->with('success', 'Area Kontrol Sanitasi berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $area_sanitasi = Area_sanitasi::where('uuid', $uuid)
        ->where('plant', $userPlantUuid) 
        ->firstOrFail();

        return view('area_sanitasi.edit', compact('area_sanitasi'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'area' => 'required|string',
            'sub_area' => 'required|string',
            'bagian' => 'nullable|array',
        ]);

        $userPlant = Auth::user()->plant;

        $area_sanitasi = Area_sanitasi::where('uuid', $uuid)
        ->where('plant', $userPlant)
        ->firstOrFail();

        $area_sanitasi->update([
            'area' => $request->area,
            'sub_area' => $request->sub_area,
            'bagian' => json_encode($request->input('bagian', []), JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('area_sanitasi.index')
        ->with('success', 'Area Kontrol Sanitasi berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $area_sanitasi = Area_sanitasi::where('uuid', $uuid)
        ->where('plant', $userPlantUuid) 
        ->firstOrFail();

        $area_sanitasi->delete();

        return redirect()->route('area_sanitasi.index')->with('success', 'Area Hygiene berhasil dihapus');
    }
}
