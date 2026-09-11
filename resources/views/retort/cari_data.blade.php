@php
    use Illuminate\Support\Str;
@endphp

<div class="container-fluid">

    @php
        $hiddenColumns = ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at'];

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

        @if ($title === 'MINCING')

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">FORM MINCING</span>
                    <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                </div>
                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>NO.</th>
                                    <th>Date | Shift</th>
                                    <th>Nama Produk</th>
                                    <th>Kode Produksi</th>
                                    <th>Hasil Pemeriksaan</th>
                                    <th>QC</th>
                                    <th>Produksi</th>
                                    <th>SPV</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($data as $i => $dep)

                                    @php
                                        $hasHasilPemeriksaan =
                                            !empty($dep->non_premix) ||
                                            !empty($dep->premix) ||
                                            !empty($dep->suhu_sebelum_grinding) ||
                                            !empty($dep->daging) ||
                                            $dep->waktu_mulai !== null ||
                                            $dep->waktu_selesai !== null ||
                                            $dep->waktu_aging_emulsi_awal !== null ||
                                            $dep->waktu_aging_emulsi_akhir !== null ||
                                            $dep->suhu_akhir_emulsi_gel !== null ||
                                            $dep->waktu_mixing !== null ||
                                            $dep->waktu_mixing_start !== null ||
                                            $dep->waktu_mixing_end !== null ||
                                            $dep->suhu_akhir_mixing !== null ||
                                            $dep->suhu_akhir_emulsi !== null ||
                                            $dep->waktu_mixing_premix !== null ||
                                            $dep->waktu_mixing_premix_start !== null ||
                                            $dep->waktu_mixing_premix_end !== null ||
                                            $dep->waktu_bowl_cutter !== null ||
                                            $dep->waktu_bowl_cutter_start !== null ||
                                            $dep->waktu_bowl_cutter_end !== null ||
                                            $dep->catatan !== null;

                                        $nonPremixItems = $dep->non_premix ?? [];
                                        $premixItems = $dep->premix ?? [];

                                        if (is_string($nonPremixItems)) {
                                            $nonPremixItems = json_decode($nonPremixItems, true);
                                        }

                                        if (is_string($premixItems)) {
                                            $premixItems = json_decode($premixItems, true);
                                        }

                                        $rawSuhu = $dep->suhu_sebelum_grinding ?? [];

                                        if (is_string($rawSuhu)) {
                                            $rawSuhu = json_decode($rawSuhu, true);
                                        }
                                    @endphp

                                    <tr>

                                        <td class="text-center">
                                            {{ $i + 1 }}
                                        </td>

                                        <td class="text-center">
                                            {{ $dep->date ?? '-' }}
                                            @if ($dep->shift !== null)
                                                | Shift: {{ $dep->shift }}
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            {{ $dep->nama_produk ?? '-' }}
                                        </td>

                                        <td class="text-center">
                                            {{ $dep->kode_produksi ?? '-' }}
                                        </td>

                                        <td class="text-center">

    @if ($hasHasilPemeriksaan)

        @php
            $nonPremixItems = $dep->non_premix ?? [];
            $premixItems = $dep->premix ?? [];

            if (is_string($nonPremixItems)) {
                $nonPremixItems = json_decode($nonPremixItems, true);
            }

            if (is_string($nonPremixItems)) {
                $nonPremixItems = json_decode($nonPremixItems, true);
            }

            if (is_string($premixItems)) {
                $premixItems = json_decode($premixItems, true);
            }

            if (is_string($premixItems)) {
                $premixItems = json_decode($premixItems, true);
            }
        @endphp

        <a href="#"
            data-bs-toggle="modal"
            data-bs-target="#mincingModal{{ $dep->uuid }}"
            style="font-weight: bold; text-decoration: underline;">
            Result
        </a>

        <div class="modal fade text-start"
            id="mincingModal{{ $dep->uuid }}"
            tabindex="-1"
            aria-labelledby="mincingModalLabel{{ $dep->uuid }}"
            aria-hidden="true">

            <div class="modal-dialog modal-xl modal-dialog-scrollable">

                <div class="modal-content">

                    <div class="modal-header bg-warning text-dark">

                        <h5 class="modal-title fw-bold"
                            id="mincingModalLabel{{ $dep->uuid }}">
                            Detail Pemeriksaan Mincing
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body table-responsive">

                        <table class="table table-bordered table-striped table-sm text-center align-middle">

                            <tbody>

                                <tr>
                                    <td class="text-start fw-bold w-25">
                                        Kode Batch
                                    </td>

                                    <td colspan="5" class="text-start">
                                        {{ $dep->kode_produksi ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-start fw-bold">
                                        Preparation
                                    </td>

                                    <td colspan="2">
                                        {{ $dep->waktu_mulai ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        s/d
                                    </td>

                                    <td colspan="2">
                                        {{ $dep->waktu_selesai ?? '-' }}
                                    </td>
                                </tr>

                                <tr class="section-header bg-light fw-bold text-center">

                                    <td class="text-start">
                                        Bahan Baku & Tambahan (Non-Premix)
                                    </td>

                                    <td>Kode</td>
                                    <td>(°C)</td>
                                    <td>*pH</td>
                                    <td>Kg</td>
                                    <td>Sens</td>

                                </tr>

                                @if (!empty($nonPremixItems) && is_array($nonPremixItems))

                                    @foreach ($nonPremixItems as $index => $bahan)

                                        @php
                                            $namaBahan = $bahan['nama_bahan'] ?? '-';

                                            $rowspan = 0;

                                            foreach ($nonPremixItems as $item) {
                                                if (($item['nama_bahan'] ?? '-') === $namaBahan) {
                                                    $rowspan++;
                                                }
                                            }

                                            $isFirst = true;

                                            for ($j = 0; $j < $index; $j++) {
                                                if (($nonPremixItems[$j]['nama_bahan'] ?? '-') === $namaBahan) {
                                                    $isFirst = false;
                                                    break;
                                                }
                                            }
                                        @endphp

                                        <tr>

                                            @if ($isFirst)

                                                <td class="text-start"
                                                    rowspan="{{ $rowspan }}"
                                                    style="text-align: center; vertical-align: middle;">
                                                    {{ $namaBahan }}
                                                </td>

                                            @endif

                                            <td>
                                                {{ \App\Models\InspectionProductDetail::where('uuid', $bahan['inspection_uuid'] ?? null)->value('kode_batch') ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $bahan['suhu_bahan'] ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $bahan['ph_bahan'] ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $bahan['berat_bahan'] ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $bahan['sensori'] ?? '-' }}
                                            </td>

                                        </tr>

                                    @endforeach

                                @else

                                    <tr>
                                        <td colspan="6"
                                            class="text-center text-muted">
                                            Belum ada data Non-Premix
                                        </td>
                                    </tr>

                                @endif

                                <tr class="section-header bg-light fw-bold text-center">

                                    <td class="text-start">
                                        Premix
                                    </td>

                                    <td colspan="2">
                                        Kode
                                    </td>

                                    <td colspan="2">
                                        Kg
                                    </td>

                                    <td>
                                        Sens
                                    </td>

                                </tr>

                                @if (!empty($premixItems) && is_array($premixItems))

                                    @foreach ($premixItems as $p)

                                        <tr>

                                            <td class="text-start">
                                                {{ $p['nama_premix'] ?? '-' }}
                                            </td>

                                            <td colspan="2">
                                                {{ $p['kode_premix'] ?? '-' }}
                                            </td>

                                            <td colspan="2">
                                                {{ $p['berat_premix'] ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $p['sensori_premix'] ?? '-' }}
                                            </td>

                                        </tr>

                                    @endforeach

                                @else

                                    <tr>
                                        <td colspan="6"
                                            class="text-center text-muted">
                                            Belum ada data Premix
                                        </td>
                                    </tr>

                                @endif

                                <tr>

                                    <td class="text-start fw-bold">
                                        Suhu (Sebelum Grinding)
                                    </td>

                                    <td colspan="5" class="text-center">

                                        @php
                                            $rawSuhu = $dep->suhu_sebelum_grinding;

                                            if (is_string($rawSuhu)) {
                                                $rawSuhu = json_decode($rawSuhu, true);
                                            }

                                            if (is_string($rawSuhu)) {
                                                $rawSuhu = json_decode($rawSuhu, true);
                                            }
                                        @endphp

                                        @if (!empty($rawSuhu) && is_array($rawSuhu))

                                            <div class="d-flex flex-wrap gap-2 justify-content-center">

                                                @foreach ($rawSuhu as $s)

                                                    <span class="badge bg-secondary text-white border">
                                                        {{ $s['daging'] ?? '?' }}:
                                                        {{ $s['suhu'] ?? '-' }}°C
                                                    </span>

                                                @endforeach

                                            </div>

                                        @elseif (!empty($dep->daging))

                                            {{ $dep->daging }}:
                                            {{ $dep->suhu_sebelum_grinding }}°C

                                        @else

                                            -

                                        @endif

                                    </td>

                                </tr>

                                <tr>

                                    <td class="text-start fw-bold">
                                        Waktu Mixing Premix
                                    </td>

                                    <td colspan="5" class="text-start">

                                        {{ $dep->waktu_mixing_premix_start
                                            ? \Carbon\Carbon::parse($dep->waktu_mixing_premix_start)->format('H:i')
                                            : '' }}

                                        -

                                        {{ $dep->waktu_mixing_premix_end
                                            ? \Carbon\Carbon::parse($dep->waktu_mixing_premix_end)->format('H:i')
                                            : '' }}

                                        <span class="text-muted ms-2">
                                            ({{ $dep->waktu_mixing_premix ?? 0 }} menit)
                                        </span>

                                    </td>

                                </tr>

                                <tr>

                                    <td class="text-start fw-bold">
                                        Waktu Bowl Cutter
                                    </td>

                                    <td colspan="5" class="text-start">

                                        {{ $dep->waktu_bowl_cutter_start
                                            ? \Carbon\Carbon::parse($dep->waktu_bowl_cutter_start)->format('H:i')
                                            : '' }}

                                        -

                                        {{ $dep->waktu_bowl_cutter_end
                                            ? \Carbon\Carbon::parse($dep->waktu_bowl_cutter_end)->format('H:i')
                                            : '' }}

                                        <span class="text-muted ms-2">
                                            ({{ $dep->waktu_bowl_cutter ?? 0 }} menit)
                                        </span>

                                    </td>

                                </tr>

                                <tr>

                                    <td class="text-start fw-bold">
                                        Waktu Aging Emulsi
                                    </td>

                                    <td colspan="2">
                                        {{ $dep->waktu_aging_emulsi_awal ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        s/d
                                    </td>

                                    <td colspan="2">
                                        {{ $dep->waktu_aging_emulsi_akhir ?? '-' }}
                                    </td>

                                </tr>

                                <tr>

                                    <td class="text-start fw-bold">
                                        Suhu Akhir Emulsi Gel
                                    </td>

                                    <td colspan="5" class="text-start">

                                        {{ $dep->suhu_akhir_emulsi_gel !== null
                                            ? rtrim(rtrim($dep->suhu_akhir_emulsi_gel, '0'), '.')
                                            : '-' }}

                                    </td>

                                </tr>

                                <tr>

                                    <td class="text-start fw-bold">
                                        Waktu Mixing
                                    </td>

                                    <td colspan="5" class="text-start">

                                        {{ $dep->waktu_mixing_start
                                            ? \Carbon\Carbon::parse($dep->waktu_mixing_start)->format('H:i')
                                            : '' }}

                                        -

                                        {{ $dep->waktu_mixing_end
                                            ? \Carbon\Carbon::parse($dep->waktu_mixing_end)->format('H:i')
                                            : '' }}

                                        <span class="text-muted ms-2">
                                            ({{ $dep->waktu_mixing ?? 0 }} menit)
                                        </span>

                                    </td>

                                </tr>

                                <tr>

                                    <td class="text-start fw-bold">
                                        Suhu Akhir Mixing
                                    </td>

                                    <td colspan="5" class="text-start">

                                        {{ $dep->suhu_akhir_mixing !== null
                                            ? rtrim(rtrim($dep->suhu_akhir_mixing, '0'), '.')
                                            : '-' }}

                                    </td>

                                </tr>

                                <tr>

                                    <td class="text-start fw-bold">
                                        Suhu Akhir Emulsifying
                                    </td>

                                    <td colspan="5" class="text-start">

                                        {{ $dep->suhu_akhir_emulsi !== null
                                            ? rtrim(rtrim($dep->suhu_akhir_emulsi, '0'), '.')
                                            : '-' }}

                                    </td>

                                </tr>

                                <tr>

                                    <td class="text-start fw-bold">
                                        Catatan
                                    </td>

                                    <td colspan="5" class="text-start">
                                        {{ $dep->catatan ?? '-' }}
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <div class="modal-footer">

                        <button type="button"
                            class="btn btn-secondary btn-sm"
                            data-bs-dismiss="modal">
                            Tutup
                        </button>

                    </div>

                </div>

            </div>

        </div>

    @else

        <span>-</span>

    @endif

</td>

                                        <td class="text-center">
                                            {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                        </td>

                                        <td class="text-center">

                                            @if (($dep->status_produksi ?? null) == 0)

                                                <span class="fw-bold text-secondary">
                                                    Created
                                                </span>

                                            @elseif (($dep->status_produksi ?? null) == 1)

                                                <span class="fw-bold text-success">
                                                    Checked
                                                </span>

                                            @elseif (($dep->status_produksi ?? null) == 2)

                                                <span class="fw-bold text-danger">
                                                    Recheck
                                                </span>

                                            @else

                                                -

                                            @endif

                                        </td>

                                        <td class="text-center">

                                            @if (($dep->status_spv ?? null) == 0)

                                                <span class="fw-bold text-secondary">
                                                    Created
                                                </span>

                                            @elseif (($dep->status_spv ?? null) == 1)

                                                <span class="fw-bold text-success">
                                                    Verified
                                                </span>

                                            @elseif (($dep->status_spv ?? null) == 2)

                                                <span class="fw-bold text-danger">
                                                    Revision
                                                </span>

                                            @else

                                                -

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="8"
                                            class="text-center text-muted">
                                            Belum ada data mincing.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        @else

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">

                    <span class="fw-bold">
                        {{ $title }}
                    </span>

                    <span class="badge bg-light text-dark">
                        {{ $data->count() }}
                    </span>

                </div>

                <div class="table-responsive">

                    <table class="table table-hover table-striped table-sm align-middle mb-0">

                        <thead class="table-light text-center">

                            <tr>

                                <th style="width:50px;">
                                    No
                                </th>

                                @foreach (array_keys((array) $data->first()) as $col)

                                    @continue(in_array($col, $hiddenColumns))

                                    <th>
                                        {{ ucwords(str_replace('_', ' ', $col)) }}
                                    </th>

                                @endforeach

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($data as $i => $row)

                                <tr>

                                    <td class="text-center">
                                        {{ $i + 1 }}
                                    </td>

                                    @foreach ((array) $row as $key => $value)

                                        @continue(in_array($key, $hiddenColumns))

                                        <td style="max-width: 250px; word-wrap: break-word;">

                                            @if ($key == 'plant' && isset($row->plant_nama))

                                                <span class="badge bg-info text-dark">
                                                    {{ $row->plant_nama }}
                                                </span>

                                            @elseif (
                                                Str::contains($key, ['file', 'foto', 'gambar']) &&
                                                $value
                                            )

                                                <a href="{{ asset('storage/' . $value) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    Lihat File
                                                </a>

                                            @elseif (
                                                is_string($value) &&
                                                Str::startsWith($value, ['{', '['])
                                            )

                                                @php
                                                    $decoded = json_decode($value, true);
                                                @endphp

                                                @if (
                                                    json_last_error() === JSON_ERROR_NONE &&
                                                    is_array($decoded)
                                                )

                                                    <div class="bg-light border rounded p-2 small"
                                                        style="max-height:180px; overflow:auto;">

                                                        @if (
                                                            isset($decoded[0]) &&
                                                            is_array($decoded[0]) &&
                                                            isset($decoded[0]['area'])
                                                        )

                                                            @foreach ($decoded as $rowJson)

                                                                <div>
                                                                    {{ $rowJson['area'] ?? '-' }} :
                                                                    <strong>
                                                                        {{ $rowJson['nilai'] ?? '-' }}
                                                                    </strong>
                                                                </div>

                                                            @endforeach

                                                        @else

                                                            @foreach ($decoded as $item => $detail)

                                                                @if (is_array($detail))

                                                                    <div class="mb-2">

                                                                        <strong>
                                                                            {{ $item }}
                                                                        </strong>

                                                                        <ul class="mb-1 ps-3">

                                                                            @foreach ($detail as $k => $v)

                                                                                @continue(empty($v))

                                                                                <li>

                                                                                    @if ($v == '✔')

                                                                                        <span class="badge bg-success">
                                                                                            OK
                                                                                        </span>

                                                                                    @elseif ($k == 'keterangan')

                                                                                        <span class="text-danger">
                                                                                            {{ $v }}
                                                                                        </span>

                                                                                    @else

                                                                                        {{ $k }}:
                                                                                        {{ $v }}

                                                                                    @endif

                                                                                </li>

                                                                            @endforeach

                                                                        </ul>

                                                                    </div>

                                                                @else

                                                                    <div>
                                                                        <strong>
                                                                            {{ $item }}:
                                                                        </strong>

                                                                        {{ $detail }}
                                                                    </div>

                                                                @endif

                                                            @endforeach

                                                        @endif

                                                    </div>

                                                @else

                                                    {{ $value }}

                                                @endif

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

        @endif

    @endforeach

</div>