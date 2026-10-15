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
