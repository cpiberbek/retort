<?php

namespace App\Http\Controllers;

use App\Models\MasterPackaging;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class PackagingMasterController extends Controller
{
    public function index()
    {
        $plantUuid = auth()->user()->plant_active;

        $packagings = MasterPackaging::where('plant_uuid', $plantUuid)
            ->latest()
            ->get();

        return view('packmaster.index', compact('packagings'));
    }

    public function store(Request $request)
    {
        $plantUuid = auth()->user()->plant_active;

        $validated = $request->validate([
            'nama_packaging' => [
                'required',
                'string',
                'max:255',
                Rule::unique('master_packaging', 'nama_packaging')
                    ->where('plant_uuid', $plantUuid)
                    ->whereNull('deleted_at'),
            ],
        ], [
            'nama_packaging.required' => 'Nama packaging wajib diisi.',
            'nama_packaging.unique' => 'Nama packaging sudah digunakan di plant ini.',
            'nama_packaging.max' => 'Nama packaging maksimal 255 karakter.',
        ]);

        try {
            MasterPackaging::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'plant_uuid' => $plantUuid,
                'nama_packaging' => $validated['nama_packaging'],
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('packmaster.index')
                ->with('success', 'Data packaging berhasil ditambahkan.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('packmaster.index')
                ->withInput()
                ->with('error', 'Gagal menambahkan packaging: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $uuid)
    {
        $plantUuid = auth()->user()->plant_active;

        $packaging = MasterPackaging::where('uuid', $uuid)
            ->where('plant_uuid', $plantUuid)
            ->firstOrFail();

        $validated = $request->validate([
            'nama_packaging' => [
                'required',
                'string',
                'max:255',
                Rule::unique('master_packaging', 'nama_packaging')
                    ->where('plant_uuid', $plantUuid)
                    ->whereNull('deleted_at')
                    ->ignore($packaging->id),
            ],
        ], [
            'nama_packaging.required' => 'Nama packaging wajib diisi.',
            'nama_packaging.unique' => 'Nama packaging sudah digunakan di plant ini.',
            'nama_packaging.max' => 'Nama packaging maksimal 255 karakter.',
        ]);

        try {
            $packaging->update([
                'nama_packaging' => $validated['nama_packaging'],
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('packmaster.index')
                ->with('success', 'Data packaging berhasil diperbarui.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('packmaster.index')
                ->with('error', 'Data packaging gagal diperbarui.');
        }
    }
}