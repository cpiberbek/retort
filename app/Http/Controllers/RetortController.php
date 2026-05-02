<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetortController extends Controller
{
    public function index()
    {
        return view('retort.index');
    }

    public function cari(Request $request)
    {
        $txt_cari = $request->txt_cari;

        if (!$txt_cari) {
            return view('retort.cari_data', [
                'area_hygienes' => collect(),
                'area_suhus' => collect(),
                'sanitasis' => collect(),
                'suhus' => collect(),
                'produksis' => collect(),
                'packings' => collect(),
                'samplings' => collect(),
                'sampels' => collect(),
            ]);
        }

        // 🔥 daftar tabel yang mau dicari
        $tables = [
            'area_hygienes',
            'area_suhus',
            'sanitasis',
            'suhus',
            'produksis',
            'packings',
            'samplings',
            'sampels'
        ];

        $results = [];

        foreach ($tables as $table) {

            // 🔥 ambil semua kolom dari tabel
            $columns = DB::getSchemaBuilder()->getColumnListing($table);

            $query = DB::table($table)
                ->where(function ($q) use ($columns, $txt_cari) {

                    foreach ($columns as $col) {
                        $q->orWhere($col, 'like', "%$txt_cari%");
                    }
                })
                ->limit(50) // biar ga jebol server
                ->get();

            $results[$table] = $query;
        }

        return view('retort.cari_data', [
            'area_hygienes' => $results['area_hygienes'],
            'area_suhus'    => $results['area_suhus'],
            'sanitasis'     => $results['sanitasis'],
            'suhus'         => $results['suhus'],
            'produksis'     => $results['produksis'],
            'packings'      => $results['packings'],
            'samplings'     => $results['samplings'],
            'sampels'       => $results['sampels'],
        ]);
    }
}
