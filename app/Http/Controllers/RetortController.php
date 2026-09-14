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

            } elseif ($table === 'prepackings') {

                $kodeProduksi = \App\Models\Mincing::where(
                    'kode_produksi',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Prepacking::query()
                    ->where(function ($q) use ($txt_cari, $kodeProduksi) {

                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produk', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%");

                        if ($kodeProduksi->isNotEmpty()) {
                            $q->orWhereIn('kode_produksi', $kodeProduksi);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'raw_material_inspections') {

                $query = \App\Models\RawMaterialInspection::with([
                    'creator',
                    'updater',
                ])
                    ->where(function ($q) use ($txt_cari) {

                        $q->where('bahan_baku', 'like', "%{$txt_cari}%")
                            ->orWhere('supplier', 'like', "%{$txt_cari}%")
                            ->orWhere('do_po', 'like', "%{$txt_cari}%")
                            ->orWhere('nopol_mobil', 'like', "%{$txt_cari}%")
                            ->orWhere('setup_kedatangan', 'like', "%{$txt_cari}%")
                            ->orWhereHas('creator', function ($creatorQuery) use ($txt_cari) {
                                $creatorQuery->where('name', 'like', "%{$txt_cari}%");
                            });
                    })
                    ->orderBy('setup_kedatangan', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'packaging_inspections') {

                $creatorUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\PackagingInspection::with('items')
                    ->where(function ($q) use ($txt_cari, $creatorUuids) {

                        $q->where('shift', 'like', "%{$txt_cari}%")
                            ->orWhere('uuid', 'like', "%{$txt_cari}%");

                        if ($creatorUuids->isNotEmpty()) {
                            $q->orWhereIn('created_by', $creatorUuids);
                        }

                        $q->orWhereHas('items', function ($itemQuery) use ($txt_cari) {
                            $itemQuery->where(
                                'packaging_type',
                                'like',
                                "%{$txt_cari}%"
                            );
                        });
                    })
                    ->orderBy('inspection_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'loading_checks') {

                $mincingUuids = \App\Models\Mincing::where(
                    'kode_produksi',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $creatorUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\LoadingProduk::with([
                    'creator',
                    'details',
                ])
                    ->where(function ($q) use ($txt_cari, $mincingUuids, $creatorUuids) {

                        $q->where('no_pol_mobil', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_supir', 'like', "%{$txt_cari}%")
                            ->orWhere('ekspedisi', 'like', "%{$txt_cari}%")
                            ->orWhere('shift', 'like', "%{$txt_cari}%")
                            ->orWhere('jenis_aktivitas', 'like', "%{$txt_cari}%");

                        if ($creatorUuids->isNotEmpty()) {
                            $q->orWhereIn('created_by', $creatorUuids);
                        }

                        $q->orWhereHas('details', function ($detailQuery) use ($txt_cari, $mincingUuids) {

                            $detailQuery->where(
                                'kode_produksi',
                                'like',
                                "%{$txt_cari}%"
                            );

                            if ($mincingUuids->isNotEmpty()) {
                                $detailQuery->orWhereIn(
                                    'kode_produksi',
                                    $mincingUuids
                                );
                            }
                        });
                    })
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'sampels') {

                $mincingUuids = \App\Models\Mincing::where(
                    'kode_produksi',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $creatorUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Sampel::query()
                    ->where(function ($q) use ($txt_cari, $mincingUuids, $creatorUuids) {

                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('username_updated', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produk', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('jenis_sampel', 'like', "%{$txt_cari}%")
                            ->orWhere('keterangan', 'like', "%{$txt_cari}%");

                        if ($mincingUuids->isNotEmpty()) {
                            $q->orWhereIn('kode_produksi', $mincingUuids);
                        }

                        if ($creatorUuids->isNotEmpty()) {
                            $q->orWhereIn('created_by', $creatorUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'suhus') {

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Suhu::query()
                    ->where(function ($q) use ($txt_cari, $userUuids) {

                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('shift', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('keterangan', 'like', "%{$txt_cari}%");

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

                foreach ($query as $row) {
                    $row->area_suhus = \App\Models\Area_suhu::where(
                        'plant',
                        $row->plant
                    )->get();
                }

            } elseif ($table === 'klorins') {

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Klorin::query()
                    ->where(function ($q) use ($txt_cari, $userUuids) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('lokasi', 'like', "%{$txt_cari}%")
                            ->orWhere('catatan', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('date', 'like', "%{$txt_cari}%");

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'berita_acaras') {

                $query = \App\Models\BeritaAcara::query()
                    ->where(function ($q) use ($txt_cari) {
                        $q->where('nomor', 'like', "%{$txt_cari}%")
                            ->orWhere('supplier', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_barang', 'like', "%{$txt_cari}%")
                            ->orWhere('tanggal_kedatangan', 'like', "%{$txt_cari}%")
                            ->orWhere('status_ppic', 'like', "%{$txt_cari}%")
                            ->orWhere('status_spv', 'like', "%{$txt_cari}%");
                    })
                    ->orderBy('tanggal_kedatangan', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'sanitasis') {

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $areaUuids = \App\Models\Area_sanitasi::where(function ($q) use ($txt_cari) {
                    $q->where('area', 'like', "%{$txt_cari}%")
                        ->orWhere('sub_area', 'like', "%{$txt_cari}%")
                        ->orWhere('bagian', 'like', "%{$txt_cari}%");
                })->pluck('uuid');

                $query = \App\Models\Sanitasi::query()
                    ->where(function ($q) use ($txt_cari, $userUuids, $areaUuids) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('date', 'like', "%{$txt_cari}%")
                            ->orWhere('shift', 'like', "%{$txt_cari}%")
                            ->orWhere('pemeriksaan', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produksi', 'like', "%{$txt_cari}%");

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }

                        if ($areaUuids->isNotEmpty()) {
                            $q->orWhereIn('area', $areaUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('shift', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'thermometers') {

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Thermometer::query()
                    ->where(function ($q) use ($txt_cari, $userUuids) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('peneraan', 'like', "%{$txt_cari}%")
                            ->orWhere('date', 'like', "%{$txt_cari}%")
                            ->orWhere('shift', 'like', "%{$txt_cari}%");

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'gmps') {

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $areaNames = \App\Models\Area_hygiene::where(
                    'area',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('area');

                $query = \App\Models\Gmp::query()
                    ->where(function ($q) use ($txt_cari, $userUuids, $areaNames) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('date', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('pemeriksaan', 'like', "%{$txt_cari}%");

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }

                        if ($areaNames->isNotEmpty()) {
                            foreach ($areaNames as $areaName) {
                                $q->orWhere('pemeriksaan', 'like', "%{$areaName}%");
                            }
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

                foreach ($query as $row) {
                    $pemeriksaan = $row->pemeriksaan;

                    if (is_string($pemeriksaan)) {
                        $pemeriksaan = json_decode($pemeriksaan, true);

                        if (is_string($pemeriksaan)) {
                            $pemeriksaan = json_decode($pemeriksaan, true);
                        }
                    }

                    $row->pemeriksaan = is_array($pemeriksaan) ? $pemeriksaan : [];

                    $row->areas = \App\Models\Area_hygiene::where(
                        'plant',
                        $row->plant
                    )
                        ->orderBy('area', 'asc')
                        ->pluck('area')
                        ->toArray();
                }

            } elseif ($table === 'recalls') {

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Recall::query()
                    ->where(function ($q) use ($txt_cari, $userUuids) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('penyebab', 'like', "%{$txt_cari}%")
                            ->orWhere('asal_informasi', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_dagang', 'like', "%{$txt_cari}%")
                            ->orWhere('jenis_pangan', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('no_pendaftaran', 'like', "%{$txt_cari}%")
                            ->orWhere('berat_bersih', 'like', "%{$txt_cari}%")
                            ->orWhere('jenis_kemasan', 'like', "%{$txt_cari}%")
                            ->orWhere('tanggal_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('tanggal_kadaluarsa', 'like', "%{$txt_cari}%")
                            ->orWhere('jumlah_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('tindak_lanjut', 'like', "%{$txt_cari}%");

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'traceabilities') {

                $query = \App\Models\Traceability::query()
                    ->where(function ($q) use ($txt_cari) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('penyebab', 'like', "%{$txt_cari}%")
                            ->orWhere('asal_informasi', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_dagang', 'like', "%{$txt_cari}%")
                            ->orWhere('jenis_pangan', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('no_pendaftaran', 'like', "%{$txt_cari}%")
                            ->orWhere('berat_bersih', 'like', "%{$txt_cari}%")
                            ->orWhere('jenis_kemasan', 'like', "%{$txt_cari}%")
                            ->orWhere('tanggal_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('tanggal_kadaluarsa', 'like', "%{$txt_cari}%")
                            ->orWhere('jumlah_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('tindak_lanjut', 'like', "%{$txt_cari}%")
                            ->orWhere('kesimpulan', 'like', "%{$txt_cari}%")
                            ->orWhere('total_waktu', 'like', "%{$txt_cari}%")
                            ->orWhere('kelengkapan_form', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_spv', 'like', "%{$txt_cari}%")
                            ->orWhere('catatan_spv', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_manager', 'like', "%{$txt_cari}%")
                            ->orWhere('catatan_manager', 'like', "%{$txt_cari}%");
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'withdrawls') {

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Withdrawl::query()
                    ->where(function ($q) use ($txt_cari, $userUuids) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produk', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('no_withdrawl', 'like', "%{$txt_cari}%")
                            ->orWhere('rincian', 'like', "%{$txt_cari}%")
                            ->orWhere('date', 'like', "%{$txt_cari}%")
                            ->orWhere('exp_date', 'like', "%{$txt_cari}%")
                            ->orWhere('tanggal_edar', 'like', "%{$txt_cari}%")
                            ->orWhere('tanggal_tarik', 'like', "%{$txt_cari}%")
                            ->orWhere('jumlah_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('jumlah_edar', 'like', "%{$txt_cari}%")
                            ->orWhere('jumlah_tarik', 'like', "%{$txt_cari}%");

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'penyimpangan_kualitas') {

                $creatorUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\PenyimpanganKualitas::with([
                        'creator',
                        'updater',
                        'verifierDiketahui',
                        'verifierDisetujui'
                    ])
                    ->where(function ($q) use ($txt_cari, $creatorUuids) {
                        $q->where('nomor', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produk', 'like', "%{$txt_cari}%")
                            ->orWhere('lot_kode', 'like', "%{$txt_cari}%")
                            ->orWhere('ditujukan_untuk', 'like', "%{$txt_cari}%")
                            ->orWhere('tanggal', 'like', "%{$txt_cari}%");

                        if ($creatorUuids->isNotEmpty()) {
                            $q->orWhereIn('created_by', $creatorUuids);
                        }
                    })
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'timbangans') {

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Timbangan::query()
                    ->where(function ($q) use ($txt_cari, $userUuids) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('peneraan', 'like', "%{$txt_cari}%")
                            ->orWhere('date', 'like', "%{$txt_cari}%")
                            ->orWhere('shift', 'like', "%{$txt_cari}%");

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'pemusnahans') {

                $kodeProduksiUuids = \App\Models\Mincing::where(
                    'kode_produksi',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Pemusnahan::query()
                    ->with('batch')
                    ->where(function ($q) use ($txt_cari, $kodeProduksiUuids) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produk', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('date', 'like', "%{$txt_cari}%")
                            ->orWhere('expired_date', 'like', "%{$txt_cari}%")
                            ->orWhere('analisa', 'like', "%{$txt_cari}%")
                            ->orWhere('keterangan', 'like', "%{$txt_cari}%");

                        if ($kodeProduksiUuids->isNotEmpty()) {
                            $q->orWhereIn('kode_produksi', $kodeProduksiUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

            } elseif ($table === 'pemasakan_rtes') {

                $kodeProduksiUuids = \App\Models\Mincing::where(
                    'kode_produksi',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Pemasakan_rte::query()
                    ->where(function ($q) use ($txt_cari, $kodeProduksiUuids, $userUuids) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produk', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('date', 'like', "%{$txt_cari}%")
                            ->orWhere('shift', 'like', "%{$txt_cari}%")
                            ->orWhere('no_chamber', 'like', "%{$txt_cari}%");

                        if ($kodeProduksiUuids->isNotEmpty()) {
                            $q->orWhereIn('kode_produksi', $kodeProduksiUuids);
                        }

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

                foreach ($query as $row) {
                    $row->kode_produksi = \App\Models\Mincing::where(
                        'uuid',
                        $row->kode_produksi
                    )->value('kode_produksi') ?? $row->kode_produksi;
                }

            } elseif ($table === 'release_packing_rtes') {

                $kodeProduksiUuids = \App\Models\Mincing::where(
                    'kode_produksi',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $userUuids = \App\Models\User::where(
                    'name',
                    'like',
                    "%{$txt_cari}%"
                )->pluck('uuid');

                $query = \App\Models\Release_packing_rte::query()
                    ->where(function ($q) use ($txt_cari, $kodeProduksiUuids, $userUuids) {
                        $q->where('username', 'like', "%{$txt_cari}%")
                            ->orWhere('nama_produk', 'like', "%{$txt_cari}%")
                            ->orWhere('kode_produksi', 'like', "%{$txt_cari}%")
                            ->orWhere('date', 'like', "%{$txt_cari}%")
                            ->orWhere('expired_date', 'like', "%{$txt_cari}%")
                            ->orWhere('keterangan', 'like', "%{$txt_cari}%")
                            ->orWhere('reject', 'like', "%{$txt_cari}%")
                            ->orWhere('release', 'like', "%{$txt_cari}%");

                        if ($kodeProduksiUuids->isNotEmpty()) {
                            $q->orWhereIn('kode_produksi', $kodeProduksiUuids);
                        }

                        if ($userUuids->isNotEmpty()) {
                            $q->orWhereIn('username', $userUuids);
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

                foreach ($query as $row) {
                    $row->kode_produksi = \App\Models\Mincing::where(
                        'uuid',
                        $row->kode_produksi
                    )->value('kode_produksi') ?? $row->kode_produksi;
                }

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
