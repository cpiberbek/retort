<?php

namespace App\Http\Controllers;

use App\Models\Mincing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LookupController extends Controller
{
    public function getBatchByProduk($nama_produk)
    {
        // Log request for debugging (temporary)
        Log::info('LookupController@getBatchByProduk called', [
            'nama_produk' => $nama_produk,
            'user' => optional(Auth::user())->username,
            'plant' => optional(Auth::user())->plant,
            'ip' => request()->ip(),
        ]);

        $userPlant  = Auth::user()->plant;
        $batches = Mincing::where('nama_produk', $nama_produk)
            ->where('plant', $userPlant)
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        return response()->json($batches);
    }

    public function getAllBatchByProduk(Request $request, $nama_produk)
    {
        $userPlant = Auth::user()->plant;
        $search    = $request->q; // Select2 keyword

        $batches = Mincing::query()
            ->select('uuid', 'kode_produksi')
            ->where('nama_produk', $nama_produk)
            ->where('plant', $userPlant)
            ->when($search, function ($q) use ($search) {
                $q->whereRaw('LOWER(kode_produksi) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->orderBy('created_at', 'desc')
            ->limit($search ? 20 : 6)
            ->get();

        return response()->json(
            $batches->map(fn ($b) => [
                'id'   => $b->uuid,
                'text' => $b->kode_produksi,
            ])
        );
    }

}
