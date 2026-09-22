<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Suhu;
use App\Models\Area_suhu;
use App\Models\IssueComplain;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $plant = Auth::user()->plant;

        $areas = Area_suhu::where('plant', $plant)
            ->orderBy('area')
            ->distinct()
            ->pluck('area');

        $issueComplains = $this->issueComplainQuery($request, $plant)->paginate(5);

        return view('dashboard', compact('plant', 'areas', 'issueComplains'));
    }

    public function issueComplainFilter(Request $request)
    {
        $plant = Auth::user()->plant;

        $issueComplains = $this->issueComplainQuery($request, $plant)->paginate(5);

        return view('partials.dashboard.card-6-list', compact('issueComplains'));
    }

    private function issueComplainQuery(Request $request, $plant)
    {
        return IssueComplain::query()
            ->where('plant', $plant)
            ->when($request->filled('date'), fn ($q) =>
                $q->whereDate('date', $request->date)
            )
            ->when($request->filled('jenis'), fn ($q) =>
                $q->where('jenis', $request->jenis)
            )
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q2) use ($search) {
                    $q2->where('judul_isu', 'like', "%{$search}%")
                        ->orWhere('jenis', 'like', "%{$search}%")
                        ->orWhere('detail', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('date');
    }

}
