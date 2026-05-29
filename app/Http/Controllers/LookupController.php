<?php

namespace App\Http\Controllers;

use App\Models\Mincing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LookupController extends Controller
{
    public function getBatchByProduk($nama_produk)
    {
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
                $q->where('kode_produksi', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json(
            $batches->map(fn ($b) => [
                'id'   => $b->uuid,
                'text' => $b->kode_produksi,
            ])
        );
    }

}
