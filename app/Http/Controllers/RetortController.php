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

        // 🔥 semua tabel
        $tables = [
            'area_hygienes',
            'area_sanitasis',
            'area_suhus',
            'berita_acaras',
            'chambers',
            'departemens',
            'dispositions',
            'engineers',
            'gmps',
            'inspection_product_details',
            'kartons',
            'klorins',
            'koordinators',
            'labelisasi_pvdcs',
            'list_chambers',
            'list_forms',
            'loading_checks',
            'loading_details',
            'magnet_traps',
            'master_raw_materials',
            'mesins',
            'metals',
            'mincings',
            'operators',
            'organoleptiks',
            'packaging_inspections',
            'packaging_inspection_items',
            'packings',
            'pemasakans',
            'pemasakan_rtes',
            'pemeriksaan_kekuatan_magnet_traps',
            'pemeriksaan_retains',
            'pemeriksaan_retain_items',
            'pemusnahans',
            'penyimpangan_kualitas',
            'plants',
            'prepackings',
            'produks',
            'produksis',
            'pvdcs',
            'raw_material_inspections',
            'recalls',
            'release_packings',
            'release_packing_rtes',
            'retain_rtes',
            'sampels',
            'samplings',
            'sampling_fgs',
            'sanitasis',
            'stuffings',
            'suhus',
            'suppliers',
            'supplier_rms',
            'thermometers',
            'timbangans',
            'traceabilities',
            'users',
            'washings',
            'wires',
            'withdrawls'
        ];

        // 🔥 kalau kosong
        if (!$txt_cari) {

            $emptyResults = [];

            foreach ($tables as $table) {
                $emptyResults[$table] = collect();
            }

            return view('retort.cari_data', $emptyResults);
        }

        $results = [];

        foreach ($tables as $table) {

        try {

            // Ambil semua kolom
            $columns = DB::getSchemaBuilder()->getColumnListing($table);

            if ($table === 'magnet_traps') {

                $query = \App\Models\MagnetTrapModel::query()
                    ->with(['updater', 'mincing', 'produksi', 'engineer'])
                    ->where(function ($q) use ($columns, $txt_cari) {

                        foreach ($columns as $col) {
                            $q->orWhere(
                                DB::raw("CAST($col AS CHAR)"),
                                'like',
                                "%{$txt_cari}%"
                            );
                        }
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'stuffings') {

                $query = \App\Models\Stuffing::with('mincing')
                    ->where(function ($q) use ($columns, $txt_cari) {

                        foreach ($columns as $col) {
                            $q->orWhere(
                                DB::raw("CAST($col AS CHAR)"),
                                'like',
                                "%{$txt_cari}%"
                            );
                        }

                        $q->orWhereHas('mincing', function ($m) use ($txt_cari) {
                            $m->where(
                                'kode_produksi',
                                'like',
                                "%{$txt_cari}%"
                            );
                        });
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'pvdcs') {

                $query = \App\Models\Pvdc::query()
                    ->where(function ($q) use ($columns, $txt_cari) {

                        foreach ($columns as $col) {
                            $q->orWhere(
                                DB::raw("CAST($col AS CHAR)"),
                                'like',
                                "%{$txt_cari}%"
                            );
                        }
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'wires') {

                $query = \App\Models\Wire::query()
                    ->where(function ($q) use ($columns, $txt_cari) {

                        foreach ($columns as $col) {
                            $q->orWhere(
                                DB::raw("CAST($col AS CHAR)"),
                                'like',
                                "%{$txt_cari}%"
                            );
                        }
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'washings') {

                $query = \App\Models\Washing::with('mincing')
                    ->where(function ($q) use ($columns, $txt_cari) {

                        foreach ($columns as $col) {
                            $q->orWhere(
                                DB::raw("CAST($col AS CHAR)"),
                                'like',
                                "%{$txt_cari}%"
                            );
                        }

                        $q->orWhereHas('mincing', function ($m) use ($txt_cari) {
                            $m->where(
                                'kode_produksi',
                                'like',
                                "%{$txt_cari}%"
                            );
                        });
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'pemasakans') {

                $query = \App\Models\Pemasakan::query()
                    ->where(function ($q) use ($columns, $txt_cari) {

                        foreach ($columns as $col) {
                            $q->orWhere(
                                DB::raw("CAST($col AS CHAR)"),
                                'like',
                                "%{$txt_cari}%"
                            );
                        }
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

                $allUUID = [];

                foreach ($query as $row) {

                    if (is_array($row->kode_produksi)) {
                        $allUUID = array_merge(
                            $allUUID,
                            $row->kode_produksi
                        );
                    }
                }

                $allUUID = array_unique($allUUID);

                $stuffingData = collect();

                if (!empty($allUUID)) {
                    $stuffingData = \App\Models\Mincing::whereIn('uuid', $allUUID)
                        ->orWhereIn('kode_produksi', $allUUID)
                        ->get()
                        ->keyBy('uuid');
                }

                foreach ($query as $row) {
                    $row->stuffingData = $stuffingData;
                }

            } elseif ($table === 'sampling_fgs') {

                $kodeProduksi = \App\Models\Mincing::where(
                    'kode_produksi',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Sampling_fg::query()
                    ->where(function ($q) use ($txt_cari, $kodeProduksi) {

                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produk', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('item_mutu', 'like', "%{$txt_cari}%")
                            ->orWhere('catatan', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_koordinator', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_spv', 'like', "%{$txt_cari}%");

                        if ($kodeProduksi->isNotEmpty()) {
                            $q->orWhereIn('kode_produksi', $kodeProduksi);
                        }
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'release_packings') {

                $query = \App\Models\Release_packing::query()
                    ->where(function ($q) use ($txt_cari) {

                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produk', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('jenis_kemasan', 'like', "%{$txt_cari}%")
                            ->orWhere('no_palet', 'like', "%{$txt_cari}%")
                            ->orWhere('release', 'like', "%{$txt_cari}%")
                            ->orWhere('keterangan', 'like', "%{$txt_cari}%")
                            ->orWhere('status_spv', 'like', "%{$txt_cari}%");
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'pemeriksaan_retains') {

                $retainIds = \App\Models\PemeriksaanRetainItem::where(
                    'kode_produksi',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('pemeriksaan_retain_id');

                $query = \App\Models\PemeriksaanRetain::with([
                        'items',
                        'creator',
                    ])
                    ->withCount('items')
                    ->where(function ($q) use ($txt_cari, $retainIds) {

                        $q->where('tanggal', 'like', "%{$txt_cari}%")
                            ->orWhere('hari', 'like', "%{$txt_cari}%")
                            ->orWhere('keterangan', 'like', "%{$txt_cari}%");

                        if ($retainIds->isNotEmpty()) {
                            $q->orWhereIn('id', $retainIds);
                        }
                    })
                    ->orderBy('tanggal', 'desc')
                    ->limit(50)
                    ->get();

            } else {

                $query = DB::table($table)
                    ->where(function ($q) use ($columns, $txt_cari) {

                        foreach ($columns as $col) {
                            $q->orWhere(
                                DB::raw("CAST($col AS CHAR)"),
                                'like',
                                "%{$txt_cari}%"
                            );
                        }
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();
            }

            $results[$table] = $query;

        } catch (\Exception $e) {

            $results[$table] = collect();
        }
    }

        return view('retort.cari_data', $results);
    }
}
