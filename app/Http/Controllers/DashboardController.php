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


}
