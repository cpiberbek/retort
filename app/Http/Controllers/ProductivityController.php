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
                ->first();

            if (!$productivity) {
                return redirect()
                    ->route('productivity.index')
                    ->with('error', 'Data tidak ditemukan di plant yang sedang aktif.');
            }
        } else {
            $productivity = null;
        }

        return view('productivity.create_or_update', compact('productivity'));
    }

    public function status()
    {
        $plant = auth()->user()->plant;
        $year = now()->year;
        $currentMonth = now()->month;

        $currentProductivity = Productivity::where('plant', $plant)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $year)
            ->first();

        $existing = Productivity::where('plant', $plant)
            ->whereYear('date', $year)
            ->get(['uuid', 'date'])
            ->map(fn ($item) => [
                'uuid'  => $item->uuid,
                'month' => $item->date->month,
            ])
            ->sortBy('month')
            ->values();

        $existingMonths   = $existing->pluck('month')->toArray();
        $availableMonths  = array_values(array_diff(range(1, 12), $existingMonths));

        return response()->json([
            'has_current_month' => (bool) $currentProductivity,
            'current_uuid'      => $currentProductivity?->uuid,
            'available_months'  => $availableMonths, // bulan yang masih kosong tahun ini
            'existing_months'   => $existing,         // buat dropdown edit kalau full
            'all_filled'        => count($availableMonths) === 0,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m',
            'hari_kerja' => 'required|integer|min:1|max:31',
            'tonase_bulanan' => 'required|numeric|min:0',
            'total_manpower' => 'required|integer|min:0',
        ]);

        $plant = auth()->user()->plant;
        $date = $request->date . '-01';

        $daysInMonth = \Carbon\Carbon::createFromFormat('Y-m-d', $date)->daysInMonth;

        if ($request->hari_kerja > $daysInMonth) {
            return back()
                ->withInput()
                ->withErrors([
                    'hari_kerja' => "Hari kerja tidak boleh lebih dari {$daysInMonth} hari untuk bulan ini.",
                ]);
        }

        $productivity = Productivity::where('plant', $plant)
            ->where('date', $date)
            ->first();

        if ($productivity) {
            $productivity->update([
                'hari_kerja' => $request->hari_kerja,
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
            'hari_kerja' => $request->hari_kerja,
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