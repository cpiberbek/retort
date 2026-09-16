<?php

namespace App\Http\Controllers;

use App\Models\Productivity;
use Illuminate\Http\Request;

class ProductivityController extends Controller
{
    public function index(Request $request)
    {
        $plant = auth()->user()->plant;

        $query = Productivity::where('plant', $plant);

        if ($request->filled('month_year')) {
            [$year, $month] = explode('-', $request->month_year);

            $query->whereYear('date', $year)
                ->whereMonth('date', $month);
        }

        $productivities = $query
            ->orderBy('date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('productivity.index', compact('productivities'));
    }

    public function createOrUpdate($uuid = null)
    {
        $plant = auth()->user()->plant;

        if ($uuid) {
            $productivity = Productivity::where('uuid', $uuid)
                ->where('plant', $plant)
                ->firstOrFail();
        } else {
            $productivity = Productivity::where('plant', $plant)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->first();
        }

        return view('productivity.create_or_update', compact('productivity'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m',
            'tonase_bulanan' => 'required|numeric|min:0',
            'total_manpower' => 'required|integer|min:0',
        ]);

        $plant = auth()->user()->plant;
        $date = $request->date . '-01';

        $productivity = Productivity::where('plant', $plant)
            ->where('date', $date)
            ->first();

        if ($productivity) {
            $productivity->update([
                'tonase_bulanan' => $request->tonase_bulanan,
                'total_manpower' => $request->total_manpower,
                'username_updated' => auth()->user()->username,
            ]);

            return redirect()
                ->route('productivity.index')
                ->with('success', 'Data productivity berhasil diperbarui.');
        }

        Productivity::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'username' => auth()->user()->username,
            'username_updated' => auth()->user()->username,
            'date' => $date,
            'plant' => $plant,
            'tonase_bulanan' => $request->tonase_bulanan,
            'total_manpower' => $request->total_manpower,
        ]);

        return redirect()
            ->route('productivity.index')
            ->with('success', 'Data productivity berhasil ditambahkan.');
    }

    public function destroy($uuid)
    {
        $plant = auth()->user()->plant;

        $productivity = Productivity::where('uuid', $uuid)
            ->where('plant', $plant)
            ->firstOrFail();

        $productivity->delete();

        return redirect()
            ->route('productivity.index')
            ->with('success', 'Data productivity berhasil dihapus.');
    }
}