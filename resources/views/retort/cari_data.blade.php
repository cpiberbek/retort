@php
    use Illuminate\Support\Str;

    $hiddenColumns = ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at'];
@endphp

<div class="container-fluid">

    @php
        $tables = [
            'AREA HYGIENE' => $area_hygienes,
            'AREA SANITASI' => $area_sanitasis,
            'AREA SUHU' => $area_suhus,
            'BERITA ACARA' => $berita_acaras,
            'CHAMBER' => $chambers,
            'DEPARTEMEN' => $departemens,
            'DISPOSITION' => $dispositions,
            'ENGINEER' => $engineers,
            'GMP' => $gmps,
            'INSPECTION PRODUCT DETAIL' => $inspection_product_details,
            'KARTON' => $kartons,
            'KLORIN' => $klorins,
            'KOORDINATOR' => $koordinators,
            'LABELISASI PVDC' => $labelisasi_pvdcs,
            'LIST CHAMBER' => $list_chambers,
            'LIST FORM' => $list_forms,
            'LOADING CHECK' => $loading_checks,
            'LOADING DETAIL' => $loading_details,
            'MAGNET TRAP' => $magnet_traps,
            'MASTER RAW MATERIAL' => $master_raw_materials,
            'MESIN' => $mesins,
            'METAL' => $metals,
            'MINCING' => $mincings,
            'OPERATOR' => $operators,
            'ORGANOLEPTIK' => $organoleptiks,
            'PACKAGING INSPECTION' => $packaging_inspections,
            'PACKAGING INSPECTION ITEM' => $packaging_inspection_items,
            'PACKING' => $packings,
            'PEMASAKAN' => $pemasakans,
            'PEMASAKAN RTE' => $pemasakan_rtes,
            'PEMERIKSAAN KEKUATAN MAGNET' => $pemeriksaan_kekuatan_magnet_traps,
            'PEMERIKSAAN RETAIN' => $pemeriksaan_retains,
            'PEMERIKSAAN RETAIN ITEM' => $pemeriksaan_retain_items,
            'PEMUSNAHAN' => $pemusnahans,
            'PENYIMPANGAN KUALITAS' => $penyimpangan_kualitas,
            'PLANT' => $plants,
            'PREPACKING' => $prepackings,
            'PRODUKS' => $produks,
            'PRODUKSI' => $produksis,
            'PVDC' => $pvdcs,
            'RAW MATERIAL INSPECTION' => $raw_material_inspections,
            'RECALL' => $recalls,
            'RELEASE PACKING' => $release_packings,
            'RELEASE PACKING RTE' => $release_packing_rtes,
            'RETAIN RTE' => $retain_rtes,
            'SAMPEL' => $sampels,
            'SAMPLING' => $samplings,
            'SAMPLING FG' => $sampling_fgs,
            'SANITASI' => $sanitasis,
            'STUFFING' => $stuffings,
            'SUHU' => $suhus,
            'SUPPLIER' => $suppliers,
            'SUPPLIER RM' => $supplier_rms,
            'THERMOMETER' => $thermometers,
            'TIMBANGAN' => $timbangans,
            'TRACEABILITY' => $traceabilities,
            'USER' => $users,
            'WASHING' => $washings,
            'WIRE' => $wires,
            'WITHDRAWL' => $withdrawls,
        ];
    @endphp

    @foreach ($tables as $title => $data)
        @continue($data->isEmpty())

        <div class="card shadow-sm border-0 mb-4">

            {{-- HEADER --}}
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">{{ $title }}</span>
                <span class="badge bg-light text-dark">{{ $data->count() }}</span>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table table-hover table-striped table-sm align-middle mb-0">

                    {{-- HEADER --}}
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width:50px;">No</th>

                            @foreach (array_keys((array) $data->first()) as $col)
                                @continue(in_array($col, $hiddenColumns))
                                <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    {{-- BODY --}}
                    <tbody>
                        @foreach ($data as $i => $row)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>

                                @foreach ((array) $row as $key => $value)
                                    @continue(in_array($key, $hiddenColumns))

                                    <td style="max-width: 250px; word-wrap: break-word;">

                                        {{-- ============================= --}}
                                        {{-- 🔥 PLANT UUID → NAMA --}}
                                        {{-- ============================= --}}
                                        @if ($key == 'plant' && isset($row->plant_nama))
                                            <span class="badge bg-info text-dark">
                                                {{ $row->plant_nama }}
                                            </span>

                                            {{-- ============================= --}}
                                            {{-- 🔥 FILE --}}
                                            {{-- ============================= --}}
                                        @elseif (Str::contains($key, ['file', 'foto', 'gambar']) && $value)
                                            <a href="{{ asset('storage/' . $value) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                Lihat File
                                            </a>

                                            {{-- ============================= --}}
                                            {{-- 🔥 JSON (OPTIMIZED) --}}
                                            {{-- ============================= --}}
                                        @elseif (is_string($value) && Str::startsWith($value, ['{', '[']))
                                            @php
                                                $decoded = json_decode($value, true);
                                            @endphp

                                            @if (json_last_error() === JSON_ERROR_NONE && is_array($decoded))

                                                <div class="bg-light border rounded p-2 small"
                                                    style="max-height:180px; overflow:auto;">

                                                    @foreach ($decoded as $item => $detail)
                                                        {{-- CASE 1: FORMAT OBJECT --}}
                                                        @if (is_array($detail))
                                                            <div class="mb-2">
                                                                <strong>{{ $item }}</strong>

                                                                <ul class="mb-1 ps-3">
                                                                    @foreach ($detail as $k => $v)
                                                                        @continue(empty($v))

                                                                        <li>
                                                                            @if ($v == '✔')
                                                                                <span class="badge bg-success">OK</span>
                                                                            @elseif ($k == 'keterangan')
                                                                                <span
                                                                                    class="text-danger">{{ $v }}</span>
                                                                            @else
                                                                                {{ $k }}: {{ $v }}
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>

                                                            {{-- CASE 2: FORMAT ARRAY (hasil_suhu) --}}
                                                        @elseif (is_array($decoded) && isset($decoded[0]['area']))
                                                            @foreach ($decoded as $rowJson)
                                                                <div>
                                                                    {{ $rowJson['area'] ?? '-' }} :
                                                                    <strong>{{ $rowJson['nilai'] ?? '-' }}</strong>
                                                                </div>
                                                            @endforeach
                                                            @break
                                                        @endif
                                                    @endforeach

                                                </div>
                                            @else
                                                {{ $value }}
                                            @endif

                                            {{-- ============================= --}}
                                            {{-- 🔥 NORMAL --}}
                                            {{-- ============================= --}}
                                        @else
                                            {{ $value ?? '-' }}
                                        @endif

                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>
    @endforeach

</div>
