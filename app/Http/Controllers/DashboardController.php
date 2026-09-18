<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Suhu;
use App\Models\Area_suhu;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $plant = Auth::user()->plant;

        $areas = Area_suhu::where('plant', $plant)
            ->orderBy('area')
            ->distinct()
            ->pluck('area');

        return view('dashboard', compact('plant', 'areas'));
    }

    public function suhu(Request $request)
    {
        $userUuid = $request->input('user');

        $user = \App\Models\User::where('uuid', $userUuid)->firstOrFail();

        $plant = $user->plant_active ?? $user->plant;
        $tanggal = now()->format('Y-m-d');

        return response()->json([
            'plant' => $plant,
            'tanggal' => $tanggal,
        ]);
    }

}
