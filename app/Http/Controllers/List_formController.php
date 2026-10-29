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

        $list_form = List_form::where('uuid', $uuid)
            ->where('plant', Auth::user()->plant)
            ->firstOrFail();

        $dataUpdate = [
            'laporan' => $request->laporan,
            'no_dokumen' => $request->no_dokumen,
        ];

        if ($request->has('standar_ref')) {
            $standarRef = $request->input('standar_ref', []);

            switch ($list_form->laporan) {
                case 'Pemeriksaan Mincing - Emulsifying - Aging':
                    if (isset($standarRef['non_premix']['sensori'])
                        && $standarRef['non_premix']['sensori'] === '') {
                        $standarRef['non_premix']['sensori'] = null;
                    }

                    if (isset($standarRef['premix']['sensori_premix'])
                        && $standarRef['premix']['sensori_premix'] === '') {
                        $standarRef['premix']['sensori_premix'] = null;
                    }
                break;

                case 'Pemeriksaan Metal Detector':
                    $metalRef = [];

                    foreach (['fe', 'nfe', 'sus'] as $field) {
                        if (isset($standarRef[$field])
                            && in_array($standarRef[$field], ['Terdeteksi', 'Tidak Terdeteksi'], true)) {
                            $metalRef[$field] = $standarRef[$field];
                        }
                    }

                    $standarRef = $metalRef ?: null;
                break;

                case 'Checklist Cleaning Magnet Trap':
                    $magnetRef = [];

                    if (isset($standarRef['status'])
                        && in_array($standarRef['status'], ['v', 'x'], true)) {
                        $magnetRef['status'] = $standarRef['status'];
                    }

                    $standarRef = $magnetRef ?: null;
                break;

                case 'Pemeriksaan Kekuatan Magnet Trap':
                    $magnetRef = [];

                    for ($i = 1; $i <= 6; $i++) {
                        $field = 'kekuatan_median_' . $i;

                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $magnetRef[$field] = [
                            'min' => $min !== '' ? $min : null,
                            'max' => $max !== '' ? $max : null,
                        ];
                    }

                    $parameterSesuai = $standarRef['parameter_sesuai'] ?? null;

                    $magnetRef['parameter_sesuai'] = in_array(
                        (string) $parameterSesuai,
                        ['0', '1'],
                        true
                    ) ? (int) $parameterSesuai : null;

                    $standarRef = $magnetRef;
                break;
               
                case 'Pemeriksaan Stuffing Sosis Retort':
                    $stuffingRef = [];

                    $rangeFields = [
                        'suhu',
                        'kecepatan_stuffing',
                        'panjang_pcs',
                        'berat_pcs',
                        'diameter_klip',
                        'lebar_cassing',
                    ];

                    foreach ($rangeFields as $field) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $stuffingRef[$field] = [
                            'min' => $min !== '' ? $min : null,
                            'max' => $max !== '' ? $max : null,
                        ];
                    }

                    foreach (['sensori', 'kebersihan_seal', 'kekuatan_seal', 'print_kode'] as $field) {
                        $value = $standarRef[$field] ?? null;

                        $stuffingRef[$field] = in_array($value, ['OK', 'Tidak OK'], true)
                            ? $value
                            : null;
                    }

                    $catatan = $standarRef['catatan'] ?? null;
                    $stuffingRef['catatan'] = $catatan !== '' ? $catatan : null;

                    $standarRef = $stuffingRef;
                break;
     
                case 'Pemeriksaan Washing - Drying':
                    $washingRef = [];

                    $rangeFields = [
                        'panjang_produk',
                        'diameter_produk',
                        'konsentrasi_pckleer',
                        'suhu_pckleer_1',
                        'suhu_pckleer_2',
                        'ph_pckleer',
                        'konsentrasi_pottasium',
                        'suhu_pottasium',
                        'ph_pottasium',
                        'suhu_heater',
                        'speed_1',
                        'speed_2',
                        'speed_3',
                        'speed_4',
                    ];

                    foreach ($rangeFields as $field) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $washingRef[$field] = [
                            'min' => $min !== '' ? $min : null,
                            'max' => $max !== '' ? $max : null,
                        ];
                    }

                    foreach ([
                        'airtrap',
                        'lengket',
                        'sisa_adonan',
                        'kebocoran',
                    ] as $field) {
                        $value = $standarRef[$field] ?? null;

                        $washingRef[$field] = in_array($value, ['Ada', 'Tidak Ada'], true)
                            ? $value
                            : null;
                    }

                    foreach ([
                        'kekuatan_seal',
                        'print_kode',
                        'kondisi_air_pckleer',
                        'kondisi_pottasium',
                    ] as $field) {
                        $value = $standarRef[$field] ?? null;

                        $washingRef[$field] = in_array($value, ['OK', 'Tidak OK'], true)
                            ? $value
                            : null;
                    }

                    $catatan = $standarRef['catatan'] ?? null;
                    $washingRef['catatan'] = is_string($catatan) && trim($catatan) !== ''
                        ? trim($catatan)
                        : null;

                    $standarRef = $washingRef;
                break;

                case 'Pemeriksaan Pemasakan':
                    $pemasakanRef = [];

                    $rangeFields = [
                        'berat_produk',
                        'suhu_produk',
                        'jumlah_tray',
                        'cooking.tekanan_angin',
                        'cooking.tekanan_steam',
                        'cooking.tekanan_air',
                        'cooking.suhu_air_awal',
                        'cooking.tekanan_awal',
                        'cooking.suhu_air_proses',
                        'cooking.tekanan_proses',
                        'cooking.suhu_air_sterilisasi',
                        'cooking.thermometer_retort',
                        'cooking.tekanan_sterilisasi',
                        'cooking.suhu_air_pendinginan_awal',
                        'cooking.tekanan_pendinginan_awal',
                        'cooking.suhu_air_pendinginan',
                        'cooking.tekanan_pendinginan',
                        'cooking.suhu_air_akhir',
                        'cooking.tekanan_akhir',
                        'cooking.suhu_produk_akhir',
                        'cooking.panjang',
                        'cooking.diameter',
                    ];

                    foreach ($rangeFields as $field) {
                        $value = data_get($standarRef, $field, []);
                        $min = $value['min'] ?? null;
                        $max = $value['max'] ?? null;

                        data_set($pemasakanRef, $field, [
                            'min' => $min !== '' ? $min : null,
                            'max' => $max !== '' ? $max : null,
                        ]);
                    }

                    foreach ([
                        'total_reject',
                        'cooking.texture',
                        'cooking.sobek_seal',
                    ] as $field) {
                        $value = data_get($standarRef, $field);
                        data_set($pemasakanRef, $field, $value !== '' ? $value : null);
                    }

                    foreach ([
                        'cooking.rasa',
                        'cooking.warna',
                        'cooking.aroma',
                    ] as $field) {
                        $value = data_get($standarRef, $field);

                        data_set(
                            $pemasakanRef,
                            $field,
                            in_array((string) $value, ['1', '2', '3'], true) ? (int) $value : null
                        );
                    }

                    $catatan = $standarRef['catatan'] ?? null;
                    $pemasakanRef['catatan'] = is_string($catatan) && trim($catatan) !== ''
                        ? trim($catatan)
                        : null;

                    $standarRef = $pemasakanRef;
                break;
                
                case 'Verifikasi Timer Chamber':
                    $timerRef = [];

                    foreach ([5, 10, 20, 30, 60] as $menit) {
                        foreach ([
                            'plc_menit',
                            'plc_detik',
                            'stopwatch_menit',
                            'stopwatch_detik',
                            'faktor_koreksi',
                        ] as $field) {
                            $key = $field . '_' . $menit;
                            $value = $standarRef[$key] ?? null;

                            if (in_array($field, [
                                'plc_menit',
                                'plc_detik',
                                'stopwatch_menit',
                                'stopwatch_detik',
                            ])) {
                                $min = $value['min'] ?? null;
                                $max = $value['max'] ?? null;

                                $timerRef[$key] = (
                                    $min !== null && $min !== '' &&
                                    $max !== null && $max !== ''
                                ) ? [
                                    'min' => (int) $min,
                                    'max' => (int) $max,
                                ] : null;
                            } else {
                                $timerRef[$key] = (
                                    $value !== null && $value !== ''
                                ) ? $value : null;
                            }
                        }
                    }

                    $catatan = $standarRef['catatan'] ?? null;
                    $timerRef['catatan'] = (
                        $catatan !== null && $catatan !== ''
                    ) ? $catatan : null;

                    $standarRef = collect($timerRef)->filter(
                        fn ($value) => $value !== null
                    )->isNotEmpty() ? $timerRef : null;

                break;

                case 'Pemeriksaan Proses Sampling Finish Good':
                    $samplingRef = [];

                    foreach (['berat_produk', 'release', 'reject', 'hold'] as $field) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $minFilled = $min !== null && $min !== '';
                        $maxFilled = $max !== null && $max !== '';

                        if ($minFilled xor $maxFilled) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => ucfirst(str_replace('_', ' ', $field))
                                    . ': Min dan Max harus diisi keduanya (atau kosongkan keduanya).',
                            ]);
                        }

                        if ($minFilled && (int) $min > (int) $max) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => ucfirst(str_replace('_', ' ', $field))
                                    . ': Min tidak boleh lebih besar dari Max.',
                            ]);
                        }

                        $samplingRef[$field] = [
                            'min' => $minFilled ? (int) $min : null,
                            'max' => $maxFilled ? (int) $max : null,
                        ];
                    }

                    $kalibrasi = $standarRef['kalibrasi'] ?? null;
                    $samplingRef['kalibrasi'] = in_array($kalibrasi, ['Sesuai', 'Tidak Sesuai'], true)
                        ? $kalibrasi
                        : null;

                    foreach (['item_mutu', 'keterangan', 'catatan'] as $field) {
                        $value = $standarRef[$field] ?? null;

                        $samplingRef[$field] = is_string($value) && trim($value) !== ''
                            ? trim($value)
                            : null;
                    }

                    $standarRef = $samplingRef;
                break;

                case 'Data Sampling Produk':
                    $samplingsRef = [];

                    foreach ([
                        'jamur', 'lendir', 'klip_tajam', 'pin_hole', 'air_trap_pvdc', 'air_trap_produk',
                        'keriput', 'bengkok', 'non_kode', 'over_lap', 'kecil', 'terjepit',
                        'double_klip', 'seal_halus', 'basah', 'dll',
                    ] as $field) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $minFilled = $min !== null && $min !== '';
                        $maxFilled = $max !== null && $max !== '';

                        if ($minFilled xor $maxFilled) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => ucfirst(str_replace('_', ' ', $field))
                                    . ': Min dan Max harus diisi keduanya (atau kosongkan keduanya).',
                            ]);
                        }

                        if ($minFilled && (int) $min > (int) $max) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => ucfirst(str_replace('_', ' ', $field))
                                    . ': Min tidak boleh lebih besar dari Max.',
                            ]);
                        }

                        $samplingsRef[$field] = [
                            'min' => $minFilled ? (int) $min : null,
                            'max' => $maxFilled ? (int) $max : null,
                        ];
                    }

                    $catatan = $standarRef['catatan'] ?? null;
                    $samplingsRef['catatan'] = is_string($catatan) && trim($catatan) !== ''
                        ? trim($catatan)
                        : null;

                    $standarRef = $samplingsRef;
                break;

                case 'Pemeriksaan Release Packing':
                    $releaseRef = [];

                    $min = $standarRef['release']['min'] ?? null;
                    $max = $standarRef['release']['max'] ?? null;

                    $minFilled = $min !== null && $min !== '';
                    $maxFilled = $max !== null && $max !== '';

                    if ($minFilled xor $maxFilled) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'standar_ref.release' => 'Release: Min dan Max harus diisi keduanya (atau kosongkan keduanya).',
                        ]);
                    }

                    if ($minFilled && (int) $min > (int) $max) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'standar_ref.release' => 'Release: Min tidak boleh lebih besar dari Max.',
                        ]);
                    }

                    $releaseRef['release'] = [
                        'min' => $minFilled ? (int) $min : null,
                        'max' => $maxFilled ? (int) $max : null,
                    ];

                    $keterangan = $standarRef['keterangan'] ?? null;
                    $releaseRef['keterangan'] = is_string($keterangan) && trim($keterangan) !== ''
                        ? trim($keterangan)
                        : null;

                    $standarRef = $releaseRef;
                break;

                case 'Pemeriksaan Proses Packing':
                    $packingRef = [];

                    foreach (['suhu', 'speed', 'berat_pcs', 'berat_pack'] as $field) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $minFilled = $min !== null && $min !== '';
                        $maxFilled = $max !== null && $max !== '';

                        if ($minFilled xor $maxFilled) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.{$field}" => ucfirst(str_replace('_', ' ', $field))
                                    . ': Min dan Max harus diisi keduanya (atau kosongkan keduanya).',
                            ]);
                        }

                        if ($minFilled && (float) $min > (float) $max) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.{$field}" => ucfirst(str_replace('_', ' ', $field))
                                    . ': Min tidak boleh lebih besar dari Max.',
                            ]);
                        }

                        $packingRef[$field] = [
                            'min' => $minFilled ? (float) $min : null,
                            'max' => $maxFilled ? (float) $max : null,
                        ];
                    }

                    foreach ([
                        'kalibrasi' => ['Oke', 'Tidak Oke'],
                        'kondisi_segel' => ['OKE', 'TIDAK OKE'],
                    ] as $field => $allowedValues) {
                        $value = $standarRef[$field] ?? null;

                        $packingRef[$field] = in_array($value, $allowedValues, true)
                            ? $value
                            : null;
                    }

                    $keterangan = $standarRef['keterangan'] ?? null;
                    $packingRef['keterangan'] = is_string($keterangan) && trim($keterangan) !== ''
                        ? trim($keterangan)
                        : null;

                    $standarRef = $packingRef;
                break;

                case 'Kontrol Labelisasi Cartooning':
                    $cartooningRef = [];

                    $min = $standarRef['jumlah']['min'] ?? null;
                    $max = $standarRef['jumlah']['max'] ?? null;

                    $minFilled = $min !== null && $min !== '';
                    $maxFilled = $max !== null && $max !== '';

                    if ($minFilled xor $maxFilled) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'standar_ref.jumlah' => 'Jumlah: Min dan Max harus diisi keduanya (atau kosongkan keduanya).',
                        ]);
                    }

                    if ($minFilled && (int) $min > (int) $max) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'standar_ref.jumlah' => 'Jumlah: Min tidak boleh lebih besar dari Max.',
                        ]);
                    }

                    $cartooningRef['jumlah'] = [
                        'min' => $minFilled ? (int) $min : null,
                        'max' => $maxFilled ? (int) $max : null,
                    ];

                    $keterangan = $standarRef['keterangan'] ?? null;
                    $cartooningRef['keterangan'] = is_string($keterangan) && trim($keterangan) !== ''
                        ? trim($keterangan)
                        : null;

                    $standarRef = $cartooningRef;
                break;

                case 'Pemeriksaan Organoleptik':
                    $organoRef = [];

                    foreach ([
                        'penampilan', 'aroma', 'kekenyalan', 'rasa_asin',
                        'rasa_gurih', 'rasa_manis', 'rasa_daging', 'rasa_keseluruhan',
                    ] as $field) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $minFilled = $min !== null && $min !== '';
                        $maxFilled = $max !== null && $max !== '';

                        $label = ucfirst(str_replace('_', ' ', $field));

                        if ($minFilled xor $maxFilled) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => "{$label}: Min dan Max harus diisi keduanya (atau kosongkan keduanya).",
                            ]);
                        }

                        if ($minFilled) {
                            if ((int) $min < 1 || (int) $max > 3 || (int) $min > (int) $max) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    "standar_ref.$field" => "{$label}: Skor harus 1 sampai 3 dan Min tidak boleh lebih besar dari Max.",
                                ]);
                            }
                        }

                        $organoRef[$field] = [
                            'min' => $minFilled ? (int) $min : null,
                            'max' => $maxFilled ? (int) $max : null,
                        ];
                    }

                    $standarRef = $organoRef;
                break;

                case 'Pemeriksaan Sampel Retain':
                    $retainRef = [];

                    $rangeRules = [
                        'panjang'         => false,
                        'diameter'        => false,
                        'lab_garam'       => false,
                        'lab_air'         => false,
                        'lab_mikro'       => false,
                        'sensori_rasa'    => true,
                        'sensori_warna'   => true,
                        'sensori_aroma'   => true,
                        'sensori_texture' => true,
                    ];

                    foreach ($rangeRules as $field => $isScore) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $minFilled = $min !== null && $min !== '';
                        $maxFilled = $max !== null && $max !== '';

                        $label = ucfirst(str_replace('_', ' ', $field));

                        if ($minFilled xor $maxFilled) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => "{$label}: Min dan Max harus diisi keduanya (atau kosongkan keduanya).",
                            ]);
                        }

                        if ($minFilled) {
                            if ((float) $min > (float) $max) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    "standar_ref.$field" => "{$label}: Min tidak boleh lebih besar dari Max.",
                                ]);
                            }

                            if ($isScore && ((int) $min < 1 || (int) $max > 3)) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    "standar_ref.$field" => "{$label}: Skor harus bernilai 1 sampai 3.",
                                ]);
                            }
                        }

                        $retainRef[$field] = [
                            'min' => $minFilled ? ($isScore ? (int) $min : (float) $min) : null,
                            'max' => $maxFilled ? ($isScore ? (int) $max : (float) $max) : null,
                        ];
                    }

                    foreach ([
                        'temuan_jamur', 'temuan_lendir', 'temuan_pinehole',
                        'temuan_kejepit', 'temuan_seal',
                    ] as $field) {
                        $value = $standarRef[$field] ?? null;

                        $retainRef[$field] = in_array((string) $value, ['0', '1'], true)
                            ? (int) $value
                            : null;
                    }

                    $keterangan = $standarRef['keterangan'] ?? null;
                    $retainRef['keterangan'] = is_string($keterangan) && trim($keterangan) !== ''
                        ? trim($keterangan)
                        : null;

                    $standarRef = $retainRef;
                break;

                case 'Pemeriksaan Pre Packing':
                    $prepackingRef = [];

                    foreach ([
                        'suhu_prepacking', 'pcs', 'toples',
                        'basah_air_ujung', 'basah_air_seal', 'kering_air_ujung', 'kering_air_seal',
                        'basah_minyak_ujung', 'basah_minyak_seal', 'kering_minyak_ujung', 'kering_minyak_seal',
                    ] as $field) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $minFilled = $min !== null && $min !== '';
                        $maxFilled = $max !== null && $max !== '';

                        $label = ucfirst(str_replace('_', ' ', $field));

                        if ($minFilled xor $maxFilled) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => "{$label}: Min dan Max harus diisi keduanya (atau kosongkan keduanya).",
                            ]);
                        }

                        if ($minFilled && (float) $min > (float) $max) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => "{$label}: Min tidak boleh lebih besar dari Max.",
                            ]);
                        }

                        $prepackingRef[$field] = [
                            'min' => $minFilled ? (float) $min : null,
                            'max' => $maxFilled ? (float) $max : null,
                        ];
                    }

                    $catatan = $standarRef['catatan'] ?? null;
                    $prepackingRef['catatan'] = is_string($catatan) && trim($catatan) !== ''
                        ? trim($catatan)
                        : null;

                    $standarRef = $prepackingRef;
                break;

                case 'Pemeriksaan Kedatangan Bahan Baku dan Bahan Kimia':
                    $rawRef = [];

                    foreach (['suhu_mobil', 'suhu_daging', 'analisa_ka_ffa', 'jumlah_sampel', 'jumlah_reject'] as $field) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $minFilled = $min !== null && $min !== '';
                        $maxFilled = $max !== null && $max !== '';

                        $label = ucfirst(str_replace('_', ' ', $field));

                        if ($minFilled xor $maxFilled) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => "{$label}: Min dan Max harus diisi keduanya (atau kosongkan keduanya).",
                            ]);
                        }

                        if ($minFilled && (float) $min > (float) $max) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => "{$label}: Min tidak boleh lebih besar dari Max.",
                            ]);
                        }

                        $rawRef[$field] = [
                            'min' => $minFilled ? (float) $min : null,
                            'max' => $maxFilled ? (float) $max : null,
                        ];
                    }

                    foreach ([
                        'mobil_check_warna', 'mobil_check_kotoran', 'mobil_check_aroma',
                        'mobil_check_kemasan', 'analisa_logo_halal', 'dokumen_halal_berlaku',
                    ] as $field) {
                        $value = $standarRef[$field] ?? null;

                        $rawRef[$field] = in_array((string) $value, ['0', '1'], true)
                            ? (int) $value
                            : null;
                    }

                    $kondisiOpsi = [
                        'Bersih',
                        'Kering',
                        'Tidak Bocor',
                        'Tidak Berdebu',
                        'Tidak Basah',
                        'Bebas Hama',
                        'Bebas Noda (Karat, cat, tinta)',
                        'Bebas Bekas oli di lantai/dinding',
                        'Tidak ada produk non halal',
                    ];

                    $picked = $standarRef['kondisi_mobil'] ?? [];
                    $picked = is_array($picked)
                        ? array_values(array_filter($kondisiOpsi, fn ($o) => in_array($o, $picked, true)))
                        : [];

                    $rawRef['kondisi_mobil'] = !empty($picked) ? $picked : null;

                    $keterangan = $standarRef['keterangan'] ?? null;
                    $rawRef['keterangan'] = is_string($keterangan) && trim($keterangan) !== ''
                        ? trim($keterangan)
                        : null;

                    $standarRef = $rawRef;
                break;

                case 'Pemeriksaan Kedatangan Bahan Kemas':
                    $packagingRef = [];

                    foreach ([
                        'condition_weight'  => false,
                        'quantity_goods'    => true,
                        'quantity_sample'   => true,
                        'quantity_reject'   => true,
                    ] as $field => $isInt) {
                        $min = $standarRef[$field]['min'] ?? null;
                        $max = $standarRef[$field]['max'] ?? null;

                        $minFilled = $min !== null && $min !== '';
                        $maxFilled = $max !== null && $max !== '';

                        $label = ucfirst(str_replace('_', ' ', $field));

                        if ($minFilled xor $maxFilled) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => "{$label}: Min dan Max harus diisi keduanya (atau kosongkan keduanya).",
                            ]);
                        }

                        if ($minFilled && (float) $min > (float) $max) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                "standar_ref.$field" => "{$label}: Min tidak boleh lebih besar dari Max.",
                            ]);
                        }

                        $packagingRef[$field] = [
                            'min' => $minFilled ? ($isInt ? (int) $min : (float) $min) : null,
                            'max' => $maxFilled ? ($isInt ? (int) $max : (float) $max) : null,
                        ];
                    }

                    foreach (['condition_design', 'condition_sealing', 'condition_color'] as $field) {
                        $value = $standarRef[$field] ?? null;

                        $packagingRef[$field] = in_array($value, ['OK', 'Not OK'], true)
                            ? $value
                            : null;
                    }

                    $accept = $standarRef['acceptance_status'] ?? null;
                    $packagingRef['acceptance_status'] = in_array($accept, ['OK', 'Tolak'], true)
                        ? $accept
                        : null;

                    foreach (['condition_dimension', 'notes'] as $field) {
                        $value = $standarRef[$field] ?? null;

                        $packagingRef[$field] = is_string($value) && trim($value) !== ''
                            ? trim($value)
                            : null;
                    }

                    $vehicleOrder = ['Bersih', 'Kotor', 'Bau', 'Bocor', 'Basah', 'Kering', 'Bebas Hama'];
                    $vehicleInput = array_filter((array) ($standarRef['vehicle_condition'] ?? []), fn ($v) => $v !== '' && $v !== null);

                    $vehicleSelected = array_values(array_intersect($vehicleOrder, $vehicleInput));
                    $packagingRef['vehicle_condition'] = !empty($vehicleSelected) ? $vehicleSelected : null;

                    $standarRef = $packagingRef;
                break;

                case 'Pemeriksaan Loading-Unloading Produk':
                    $loadingRef = [];

                    $min = $standarRef['jumlah']['min'] ?? null;
                    $max = $standarRef['jumlah']['max'] ?? null;

                    $minFilled = $min !== null && $min !== '';
                    $maxFilled = $max !== null && $max !== '';

                    if ($minFilled xor $maxFilled) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'standar_ref.jumlah' => 'Jumlah: Min dan Max harus diisi keduanya (atau kosongkan keduanya).',
                        ]);
                    }

                    if ($minFilled && (int) $min > (int) $max) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'standar_ref.jumlah' => 'Jumlah: Min tidak boleh lebih besar dari Max.',
                        ]);
                    }

                    $loadingRef['jumlah'] = [
                        'min' => $minFilled ? (int) $min : null,
                        'max' => $maxFilled ? (int) $max : null,
                    ];

                    $kondisiOrder = [
                        'bersih', 'kering', 'tidak_bocor', 'tidak_berdebu', 'tidak_basah',
                        'bebas_hama', 'bebas_noda', 'bebas_bekas_oli', 'tidak_ada_produk_non_halal',
                    ];
                    $kondisiInput = array_filter((array) ($standarRef['kondisi_mobil'] ?? []), fn ($v) => $v !== '' && $v !== null);
                    $kondisiSelected = array_values(array_intersect($kondisiOrder, $kondisiInput));
                    $loadingRef['kondisi_mobil'] = !empty($kondisiSelected) ? $kondisiSelected : null;

                    foreach (['item_keterangan', 'keterangan_total', 'keterangan_umum'] as $field) {
                        $value = $standarRef[$field] ?? null;

                        $loadingRef[$field] = is_string($value) && trim($value) !== ''
                            ? trim($value)
                            : null;
                    }

                    $standarRef = $loadingRef;
                break;

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
