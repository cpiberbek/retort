<?php

namespace App\Http\Controllers;

use App\Models\IssueComplain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IssueComplainController extends Controller
{
    public function index(Request $request)
    {
        $plant = auth()->user()->plant;

        $query = IssueComplain::where('plant', $plant);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('judul_isu', 'like', '%' . $search . '%')
                    ->orWhere('jenis', 'like', '%' . $search . '%')
                    ->orWhere('detail', 'like', '%' . $search . '%');
            });
        }

        $issueComplains = $query
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('issue-complain.index', compact('issueComplains'));
    }

    public function createOrUpdate($uuid = null)
    {
        $plant = auth()->user()->plant;

        $issueComplain = null;

        if ($uuid) {
            $issueComplain = IssueComplain::where('uuid', $uuid)
                ->where('plant', $plant)
                ->firstOrFail();
        }

        return view('issue-complain.create_or_update', compact('issueComplain'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'judul_isu' => 'required|string|max:255',
            'jenis' => 'required|in:progress,penyelesaian,update',
            'detail' => 'required|string',
        ]);

        $plant = auth()->user()->plant;

        if ($request->filled('uuid')) {
            $issueComplain = IssueComplain::where('uuid', $request->uuid)
                ->where('plant', $plant)
                ->firstOrFail();

            $issueComplain->update([
                'date' => $request->date,
                'judul_isu' => $request->judul_isu,
                'jenis' => $request->jenis,
                'detail' => $request->detail,
                'username_updated' => auth()->user()->username,
            ]);

            return redirect()
                ->route('issue-complain.index')
                ->with('success', 'Data isu & komplain berhasil diperbarui.');
        }

        IssueComplain::create([
            'uuid' => Str::uuid(),
            'username' => auth()->user()->username,
            'username_updated' => auth()->user()->username,
            'date' => $request->date,
            'judul_isu' => $request->judul_isu,
            'jenis' => $request->jenis,
            'detail' => $request->detail,
            'plant' => $plant,
        ]);

        return redirect()
            ->route('issue-complain.index')
            ->with('success', 'Data isu & komplain berhasil ditambahkan.');
    }

    public function destroy($uuid)
    {
        $plant = auth()->user()->plant;

        $issueComplain = IssueComplain::where('uuid', $uuid)
            ->where('plant', $plant)
            ->firstOrFail();

        $issueComplain->delete();

        return redirect()
            ->route('issue-complain.index')
            ->with('success', 'Data isu & komplain berhasil dihapus.');
    }
}