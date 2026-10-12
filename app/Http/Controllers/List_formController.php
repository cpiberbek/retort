<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\List_form; 
use Illuminate\Support\Facades\Auth;

class List_formController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $userPlantUuid = Auth::user()->plant; 

        $list_form = List_form::where('plant', $userPlantUuid)
        ->when($search, function($query, $search) {
            $query->where('laporan', 'like', "%{$search}%")
            ->orWhere('no_dokumen', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('list_form.index', compact('list_form'));
    }

    public function create()
    {
        return view('list_form.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'laporan' => 'required|string|max:255',
            'no_dokumen' => 'required|string|max:255'
        ]);

        $user = Auth::user(); 

        List_form::create([
            'username' => $user->username,  
            'plant' => $user->plant,     
            'laporan' => $request->laporan,
            'no_dokumen' => $request->no_dokumen
        ]);

        return redirect()->route('list_form.index')->with('success', 'Laporan berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $list_form = List_form::where('uuid', $uuid)
        ->where('plant', $userPlantUuid) 
        ->firstOrFail();

        return view('list_form.edit', compact('list_form'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'laporan' => 'required|string|max:255',
            'no_dokumen' => 'required|string|max:255',
            'standar_ref' => 'nullable|array',
        ]);

        $userPlantUuid = Auth::user()->plant;

        $list_form = List_form::where('uuid', $uuid)
            ->where('plant', $userPlantUuid)
            ->firstOrFail();

        $dataUpdate = [
            'laporan' => $request->laporan,
            'no_dokumen' => $request->no_dokumen,
        ];

        if ($request->has('standar_ref')) {
            $standarRef = $request->input('standar_ref', []);

            // Checkbox tidak dicentang menjadi null.
            if (isset($standarRef['non_premix']['sensori'])
                && $standarRef['non_premix']['sensori'] === '') {
                $standarRef['non_premix']['sensori'] = null;
            }

            if (isset($standarRef['premix']['sensori_premix'])
                && $standarRef['premix']['sensori_premix'] === '') {
                $standarRef['premix']['sensori_premix'] = null;
            }

            $dataUpdate['standar_ref'] = $standarRef;
        }

        $list_form->update($dataUpdate);

        return redirect()
            ->route('list_form.index')
            ->with('success', 'Laporan berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $userPlantUuid = Auth::user()->plant;

        $list_form = List_form::where('uuid', $uuid)
        ->where('plant', $userPlantUuid) 
        ->firstOrFail();

        $list_form->delete();

        return redirect()->route('list_form.index')->with('success', 'Laporan berhasil dihapus');
    }
}
