<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area_suhu;
use Illuminate\Support\Facades\Auth;

class Area_suhuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $userPlantUuid = Auth::user()->plant;

        $area_suhu = Area_suhu::where('plant', $userPlantUuid)
            ->when($search, function ($query, $search) {
                $query->where('area', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('area_suhu.index', compact('area_suhu'));
    }

    public function create()
    {
        return view('area_suhu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'area' => 'required|string',
            'standar_min' => 'required|numeric',
            'standar_max' => 'required|numeric|gte:standar_min',

            'rh_min' => 'nullable|numeric',
            'rh_max' => 'nullable|numeric|gte:rh_min',
        ]);

        $user = Auth::user();

        Area_suhu::create([
            'username'     => $user->username,
            'plant'        => $user->plant,
            'area'         => $request->area,
            'standar_min'  => $request->standar_min,
            'standar_max'  => $request->standar_max,
            'rh_min'       => $request->rh_min,
            'rh_max'       => $request->rh_max,
        ]);

        return redirect()
            ->route('area_suhu.index')
            ->with('success', 'Area Suhu berhasil ditambahkan');
    }


    public function edit($uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $area_suhu = Area_suhu::where('uuid', $uuid)
            ->where('plant', $userPlantUuid)
            ->firstOrFail();

        return view('area_suhu.edit', compact('area_suhu'));
    }

    public function update(Request $request, $uuid)
    {

        $request->validate([
            'area' => 'required|string|max:255',
            'standar_min' => 'required|numeric',
            'standar_max' => 'required|numeric|gte:standar_min',

            'rh_min' => 'nullable|numeric',
            'rh_max' => 'nullable|numeric|gte:rh_min',
        ]);

        $userPlantUuid = Auth::user()->plant;

        $area_suhu = Area_suhu::where('uuid', $uuid)
            ->where('plant', $userPlantUuid)
            ->firstOrFail();

        $area_suhu->update([
            'area' => $request->area,
            'standar_min' => $request->standar_min,
            'standar_max' => $request->standar_max,
            'rh_min' => $request->rh_min,
            'rh_max' => $request->rh_max,
        ]);

        return redirect()->route('area_suhu.index')
            ->with('success', 'Area Suhu berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $area_suhu = Area_suhu::where('uuid', $uuid)
            ->where('plant', $userPlantUuid)
            ->firstOrFail();

        $area_suhu->delete();

        return redirect()->route('area_suhu.index')->with('success', 'Area Hygiene berhasil dihapus');
    }
}
