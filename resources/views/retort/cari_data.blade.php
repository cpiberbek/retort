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

        @switch($title)

            @case('MINCING')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA MINCING</span>
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

                                            <td class="text-center align-middle">
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

            @break

            @case('METAL')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA METAL DETECTOR</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Pukul</th>
                                        <th>FE 1.0 mm</th>
                                        <th>NFE 1.5 mm</th>
                                        <th>
                                            SUS
                                            @if(Auth::user()->plant == '2debd595-89c4-4a7e-bf94-e623cc220ca6')
                                                2.5 mm
                                            @elseif(Auth::user()->plant == 'fdaca613-7ab2-4997-8f33-686e886c867d')
                                                2.0 mm
                                            @else
                                                - mm
                                            @endif
                                        </th>
                                        <th>QC</th>
                                        <th>Produksi</th>
                                        <th>Engineer</th>
                                        <th>SPV</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                         $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                                |
                                                {{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}
                                            </td>

                                            <td class="text-center">
                                                {!! $dep->fe == 'Terdeteksi'
                                                    ? '<span class="text-success fw-bold">✓</span>'
                                                    : '<span class="text-danger fw-bold">x</span>' !!}
                                            </td>

                                            <td class="text-center">
                                                {!! $dep->nfe == 'Terdeteksi'
                                                    ? '<span class="text-success fw-bold">✓</span>'
                                                    : '<span class="text-danger fw-bold">x</span>' !!}
                                            </td>

                                            <td class="text-center">
                                                {!! $dep->sus == 'Terdeteksi'
                                                    ? '<span class="text-success fw-bold">✓</span>'
                                                    : '<span class="text-danger fw-bold">x</span>' !!}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                @if ($dep->status_produksi == 0)
                                                    <span class="fw-bold text-secondary">Created</span>

                                                @elseif ($dep->status_produksi == 1)
                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#checkedModalProduksi{{ $dep->uuid }}"
                                                        class="fw-bold text-success text-decoration-none">
                                                        Checked
                                                    </a>

                                                    <div class="modal fade"
                                                        id="checkedModalProduksi{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">

                                                                <div class="modal-header bg-success text-white">
                                                                    <h5 class="modal-title">
                                                                        Detail Checked
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal">
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <ul class="list-unstyled mb-0">
                                                                        <li>
                                                                            <strong>Status:</strong> Checked
                                                                        </li>
                                                                        <li>
                                                                            <strong>Nama Produksi:</strong>
                                                                            {{ $dep->nama_produksi ?? '-' }}
                                                                        </li>
                                                                    </ul>
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

                                                @elseif ($dep->status_produksi == 2)
                                                    <span class="fw-bold text-danger">Recheck</span>
                                                @endif
                                            </td>

                                            <td class="text-center align-middle">
                                                @if ($dep->status_engineer == 0)
                                                    <span class="fw-bold text-secondary">Created</span>

                                                @elseif ($dep->status_engineer == 1)
                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#checkedModalEngineer{{ $dep->uuid }}"
                                                        class="fw-bold text-success text-decoration-none">
                                                        Checked
                                                    </a>

                                                    <div class="modal fade"
                                                        id="checkedModalEngineer{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">

                                                                <div class="modal-header bg-success text-white">
                                                                    <h5 class="modal-title">
                                                                        Detail Checked
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal">
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <ul class="list-unstyled mb-0">
                                                                        <li>
                                                                            <strong>Status:</strong> Checked
                                                                        </li>
                                                                        <li>
                                                                            <strong>Nama Engineer:</strong>
                                                                            {{ $dep->nama_engineer ?? '-' }}
                                                                        </li>
                                                                    </ul>
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

                                                @elseif ($dep->status_engineer == 2)
                                                    <span class="fw-bold text-danger">Recheck</span>
                                                @endif
                                            </td>

                                            <td class="text-center align-middle">
                                                @if ($dep->status_spv == 0)
                                                    <span class="fw-bold text-secondary">Created</span>

                                                @elseif ($dep->status_spv == 1)
                                                    <span class="fw-bold text-success">Verified</span>

                                                @elseif ($dep->status_spv == 2)
                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                        class="text-danger fw-bold text-decoration-none">
                                                        Revision
                                                    </a>

                                                    <div class="modal fade"
                                                        id="revisionModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">

                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title">
                                                                        Detail Revisi
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal">
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <ul class="list-unstyled mb-0">
                                                                        <li>
                                                                            <strong>Status:</strong> Revision
                                                                        </li>
                                                                        <li>
                                                                            <strong>Catatan:</strong>
                                                                            {{ $dep->catatan_spv ?? '-' }}
                                                                        </li>
                                                                    </ul>
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
                                                @endif
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                Belum ada data metal detector.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @break

            @case('MAGNET TRAP')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA CLEANING MAGNET TRAP</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Nama Varian</th>
                                        <th>Kode Batch</th>
                                        <th>Tanggal | Pukul</th>
                                        <th>Jml Temuan</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Produksi</th>
                                        <th>Engineer</th>
                                        <th>Status SPV</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center">
                                                {{ $no++ }}
                                            </td>

                                            <td>
                                                {{ $item->nama_produk ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                {{ $item->mincing->kode_produksi ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tanggal ?? $item->created_at)->format('d-m-Y') }}
                                                <br>
                                                <span class="text-muted small">
                                                    {{ $item->pukul ? \Carbon\Carbon::parse($item->pukul)->format('H:i') : '-' }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                {{ $item->jumlah_temuan ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                @if ($item->status == 'v')
                                                    <span class="fw-bold text-success">
                                                        <i class="bi bi-check-circle-fill"></i> OK
                                                    </span>
                                                @else
                                                    <span class="fw-bold text-danger">
                                                        <i class="bi bi-x-circle-fill"></i> NOT OK
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                {{ Str::limit($item->keterangan ?? '-', 35) }}
                                            </td>

                                            <td class="text-center">
                                                {{ optional($item->produksi)->nama_karyawan ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                {{ optional($item->engineer)->nama_karyawan ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                @if ($item->status_spv == 1)
                                                    <span class="badge-status status-verified">
                                                        <i class="fas fa-check-circle me-1"></i>Verified
                                                    </span>
                                                @elseif ($item->status_spv == 2)
                                                    <span class="badge-status status-revision">
                                                        <i class="fas fa-exclamation-circle me-1"></i>Revision
                                                    </span>
                                                @else
                                                    <span class="badge-status status-pending">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                Belum ada data cleaning magnet trap.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @break

            @case('PEMERIKSAAN KEKUATAN MAGNET')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA PEMERIKSAAN KEKUATAN MAGNET TRAP</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Tanggal</th>
                                        <th>Kondisi Visual</th>
                                        <th>Petugas QC</th>
                                        <th>Parameter</th>
                                        <th>Status SPV</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $item)
                                        <tr>
                                            <td class="text-center">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center">
                                                {{ $item->tanggal
                                                    ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y')
                                                    : ($item->created_at
                                                        ? \Carbon\Carbon::parse($item->created_at)->format('d-m-Y')
                                                        : '-') }}
                                            </td>

                                            <td>
                                                {{ $item->kondisi_magnet_trap ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->petugas_qc ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                @if ($item->parameter_sesuai)
                                                    <span class="fw-bold text-success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                        Sesuai
                                                    </span>
                                                @else
                                                    <span class="fw-bold text-danger">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                        Tdk Sesuai
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if ($item->status_spv == 1)
                                                    <span class="fw-bold text-success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                        Verified
                                                    </span>
                                                @elseif ($item->status_spv == 2)
                                                    <span class="fw-bold text-danger">
                                                        <i class="bi bi-exclamation-circle-fill"></i>
                                                        Revisi
                                                    </span>
                                                @else
                                                    <span class="fw-bold text-secondary">
                                                        <i class="bi bi-hourglass-split"></i>
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                Belum ada data pemeriksaan kekuatan magnet trap.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @break

            @case('STUFFING')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA STUFFING</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Shift</th>
                                        <th>Nama Varian</th>
                                        <th>Kode Batch</th>
                                        <th>Exp. Date</th>
                                        <th>Kode Mesin</th>
                                        <th>Jam Mulai</th>
                                        <th>Pemeriksaan</th>
                                        <th>QC</th>
                                        <th>SPV</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        <tr>
                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->date
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                <br>
                                                <span class="text-muted small">
                                                    Shift: {{ $dep->shift ?? '-' }}
                                                </span>
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->mincing->kode_produksi ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->exp_date
                                                    ? \Carbon\Carbon::parse($dep->exp_date)->format('d-m-Y')
                                                    : '-' }}
                                            </td>

                                            {{-- Modal Kode Mesin --}}
                                            <td class="text-center align-middle">

                                                @if (is_array($dep->data_stuffing) && count($dep->data_stuffing) > 0)

                                                    <button
                                                        class="btn btn-secondary btn-sm mb-2 shadow-sm"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#mesinModal{{ $dep->uuid }}">
                                                        Mesin
                                                    </button>

                                                    <div class="modal fade"
                                                        id="mesinModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-sm modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-secondary text-white p-3">
                                                                    <h5 class="modal-title" style="font-size: 1rem;">
                                                                        Daftar Kode Mesin
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body p-0">

                                                                    <ul class="list-group list-group-flush text-start">

                                                                        @foreach ($dep->data_stuffing as $index => $m)

                                                                            <li class="list-group-item d-flex justify-content-between align-items-center p-3"
                                                                                style="font-size: 0.9rem;">

                                                                                <span class="text-muted fw-semibold">
                                                                                    Stuffing #{{ $index + 1 }}
                                                                                </span>

                                                                                <span class="badge bg-primary text-white px-3 py-2 rounded"
                                                                                    style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                                                                    {{ $m['kode_mesin'] ?? '-' }}
                                                                                </span>

                                                                            </li>

                                                                        @endforeach

                                                                    </ul>

                                                                </div>

                                                                <div class="modal-footer p-2">
                                                                    <button type="button"
                                                                        class="btn btn-light btn-sm border"
                                                                        data-bs-dismiss="modal">
                                                                        Tutup
                                                                    </button>
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>

                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif

                                            </td>

                                            {{-- Modal Jam Mulai --}}
                                            <td class="text-center align-middle">

                                                @if (is_array($dep->data_stuffing) && count($dep->data_stuffing) > 0)

                                                    <button
                                                        class="btn btn-light border btn-sm mb-2"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#jamModal{{ $dep->uuid }}">
                                                        Waktu
                                                    </button>

                                                    <div class="modal fade"
                                                        id="jamModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-sm modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" style="font-size: 1rem;">
                                                                        Daftar Jam Mulai
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body p-2">

                                                                    <ul class="list-group list-group-flush text-start">

                                                                        @foreach ($dep->data_stuffing as $index => $m)

                                                                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                                                                style="font-size: 0.85rem;">

                                                                                Stuffing #{{ $index + 1 }}

                                                                                <span class="badge bg-light text-dark border rounded-pill">
                                                                                    {{ $m['jam_mulai'] ?? '-' }}
                                                                                </span>

                                                                            </li>

                                                                        @endforeach

                                                                    </ul>

                                                                </div>

                                                                <div class="modal-footer p-1">
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

                                            {{-- Modal Detail Pemeriksaan --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->id)

                                                    <button
                                                        class="btn btn-info btn-sm mb-2"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#stuffingModal{{ $dep->uuid }}">
                                                        Details
                                                    </button>

                                                    <div class="modal fade"
                                                        id="stuffingModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">

                                                            <div class="modal-content">

                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">
                                                                        Detail Pemeriksaan
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body">

                                                                    <div class="table-responsive p-2">

                                                                        @if (is_array($dep->data_stuffing))

                                                                            @foreach ($dep->data_stuffing as $index => $item)

                                                                                <div class="border p-2 mb-2 bg-light rounded shadow-sm">

                                                                                    <strong
                                                                                        class="text-primary d-block mb-1 text-start"
                                                                                        style="font-size: 0.8rem;">

                                                                                        Stuffing #{{ $index + 1 }}
                                                                                        (Mesin: {{ $item['kode_mesin'] ?? '-' }})

                                                                                    </strong>

                                                                                    <table class="table table-bordered table-striped table-sm text-center align-middle mb-0">

                                                                                        <tbody>

                                                                                            @php
                                                                                                $fields = [
                                                                                                    ['type' => 'title', 'label' => 'Parameter Adonan'],

                                                                                                    ['type' => 'field', 'label' => 'Suhu (°C)', 'key' => 'suhu'],
                                                                                                    ['type' => 'field', 'label' => 'Sensori', 'key' => 'sensori'],

                                                                                                    ['type' => 'title', 'label' => 'Parameter Stuffing'],

                                                                                                    ['type' => 'field', 'label' => 'Kecepatan Stuffing', 'key' => 'kecepatan_stuffing'],
                                                                                                    ['type' => 'field', 'label' => 'Panjang/pcs (cm)', 'key' => 'panjang_pcs'],
                                                                                                    ['type' => 'field', 'label' => 'Berat/pcs (gr)', 'key' => 'berat_pcs'],
                                                                                                    ['type' => 'field', 'label' => 'Kebersihan Seal', 'key' => 'kebersihan_seal'],
                                                                                                    ['type' => 'field', 'label' => 'Kekuatan Seal', 'key' => 'kekuatan_seal'],
                                                                                                    ['type' => 'field', 'label' => 'Diameter Klip (mm)', 'key' => 'diameter_klip'],
                                                                                                    ['type' => 'field', 'label' => 'Print Kode', 'key' => 'print_kode'],
                                                                                                    ['type' => 'field', 'label' => 'Lebar Cassing (mm)', 'key' => 'lebar_cassing'],
                                                                                                    ['type' => 'field', 'label' => 'Catatan', 'key' => 'catatan'],
                                                                                                ];
                                                                                            @endphp

                                                                                            @foreach ($fields as $f)

                                                                                                @if ($f['type'] === 'title')

                                                                                                    <tr class="table-secondary">
                                                                                                        <td class="text-start fw-bold"
                                                                                                            colspan="2"
                                                                                                            style="font-size: 0.75rem;">
                                                                                                            {{ $f['label'] }}
                                                                                                        </td>
                                                                                                    </tr>

                                                                                                @else

                                                                                                    <tr>

                                                                                                        <td class="text-start"
                                                                                                            style="font-size: 0.75rem;">
                                                                                                            {{ $f['label'] }}
                                                                                                        </td>

                                                                                                        @php
                                                                                                            $value = $item[$f['key']] ?? null;

                                                                                                            $display = in_array($f['key'], [
                                                                                                                'sensori',
                                                                                                                'kebersihan_seal',
                                                                                                                'kekuatan_seal',
                                                                                                                'print_kode'
                                                                                                            ])
                                                                                                                ? (!empty($value) && $value === 'OK'
                                                                                                                    ? '✔'
                                                                                                                    : ($value === 'Tidak OK'
                                                                                                                        ? '❌'
                                                                                                                        : '-'))
                                                                                                                : ($value ?? '-');
                                                                                                        @endphp

                                                                                                        <td style="font-size: 0.75rem;">
                                                                                                            {{ $display }}
                                                                                                        </td>

                                                                                                    </tr>

                                                                                                @endif

                                                                                            @endforeach

                                                                                        </tbody>

                                                                                    </table>

                                                                                </div>

                                                                            @endforeach

                                                                        @else

                                                                            <span class="text-muted">-</span>

                                                                        @endif

                                                                    </div>

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

                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            {{-- Status SPV --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                        class="text-danger fw-bold text-decoration-none"
                                                        style="cursor: pointer;">
                                                        Revision
                                                    </a>

                                                    {{-- Modal Revision --}}
                                                    <div class="modal fade"
                                                        id="revisionModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-danger text-white">

                                                                    <h5 class="modal-title">
                                                                        Detail Revisi
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body text-start">

                                                                    <ul class="list-unstyled mb-0">

                                                                        <li>
                                                                            <strong>Status:</strong>
                                                                            Revision
                                                                        </li>

                                                                        <li>
                                                                            <strong>Catatan:</strong>
                                                                            {{ $dep->catatan_spv ?? '-' }}
                                                                        </li>

                                                                    </ul>

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

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="10" class="text-center align-middle">
                                                Belum ada data stuffing.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @break

            @case('LABELISASI PVDC')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA KONTROL LABELISASI PVDC</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Shift</th>
                                        <th>Nama Varian</th>
                                        <th>Hasil Pemeriksaan</th>
                                        <th>QC</th>
                                        <th>Operator</th>
                                        <th>SPV</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        <tr>
                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->date
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                <br>
                                                <span class="text-muted small">
                                                    Shift: {{ $dep->shift ?? '-' }}
                                                </span>
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            {{-- Modal Result --}}
                                            <td class="text-center align-middle">

                                                @if (!empty($dep->uuid))

                                                    <a href="#"
                                                        class="fw-bold text-decoration-underline btn-result-combined"
                                                        data-uuid="{{ $dep->uuid }}">
                                                        Result
                                                    </a>

                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_operator ?? '-' }}
                                            </td>

                                            {{-- Status SPV --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <a href="#"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#labelisasiRevisionModal{{ $dep->uuid }}"
                                                        class="text-danger fw-bold text-decoration-none">
                                                        Revision
                                                    </a>

                                                    {{-- Modal Revision --}}
                                                    <div class="modal fade"
                                                        id="labelisasiRevisionModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-danger text-white">

                                                                    <h5 class="modal-title">
                                                                        Detail Revisi
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body text-start">

                                                                    <ul class="list-unstyled mb-0">

                                                                        <li>
                                                                            <strong>Status:</strong>
                                                                            Revision
                                                                        </li>

                                                                        <li>
                                                                            <strong>Catatan:</strong>
                                                                            {{ $dep->catatan_spv ?? '-' }}
                                                                        </li>

                                                                    </ul>

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

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="7" class="text-center align-middle">
                                                Belum ada data labelisasi PVDC.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Result Modal --}}
                <script>
                    document.addEventListener('click', function (e) {

                        const button = e.target.closest('.btn-result-combined');

                        if (!button) {
                            return;
                        }

                        e.preventDefault();

                        const uuid = button.dataset.uuid;

                        fetch("{{ url('/labelisasi-pvdc') }}/" + uuid + "/result")
                            .then(response => response.text())
                            .then(html => {

                                document.body.insertAdjacentHTML('beforeend', html);

                                const modalElement =
                                    document.getElementById('resultModal' + uuid);

                                if (!modalElement) {
                                    return;
                                }

                                const modal = new bootstrap.Modal(modalElement);

                                modalElement.addEventListener(
                                    'hidden.bs.modal',
                                    function () {
                                        this.remove();
                                    }
                                );

                                modal.show();
                            })
                            .catch(error => {
                                console.error('Gagal mengambil result:', error);
                            });

                    });
                </script>

            @break

            @case('PVDC')

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA NO. LOT PVDC</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table">

                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Shift</th>
                                        <th>Nama Varian</th>
                                        <th>Nama Supplier</th>
                                        <th>Tanggal Kedatangan</th>
                                        <th>Tanggal Expired</th>
                                        <th>Data PVDC</th>
                                        <th>QC</th>
                                        <th>SPV</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="align-middle">
                                                {{ $dep->date
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                |
                                                Shift: {{ $dep->shift ?? '-' }}
                                            </td>

                                            <td class="align-middle">

                                                {{ $dep->nama_produk ?? '-' }}

                                            </td>

                                            <td class="align-middle">
                                                {{ $dep->nama_supplier ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ !empty($dep->tgl_kedatangan)
                                                    ? \Carbon\Carbon::parse($dep->tgl_kedatangan)->format('d-m-Y')
                                                    : '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ !empty($dep->tgl_expired)
                                                    ? \Carbon\Carbon::parse($dep->tgl_expired)->format('d-m-Y')
                                                    : '-' }}
                                            </td>

                                            {{-- Data PVDC --}}
                                            <td class="text-center align-middle">

                                                @php
                                                    $data_pvdc = is_string($dep->data_pvdc ?? null)
                                                        ? json_decode($dep->data_pvdc, true)
                                                        : ($dep->data_pvdc ?? null);
                                                @endphp

                                                @if (!empty($data_pvdc) && !empty($dep->pvdc_detail))

                                                    @php
                                                        $batches = collect($dep->pvdc_detail)
                                                            ->flatMap(function ($mesin) {
                                                                return collect($mesin['detail'] ?? [])
                                                                    ->map(function ($detail) {
                                                                        return data_get(
                                                                            $detail,
                                                                            'mincing.kode_produksi'
                                                                        );
                                                                    })
                                                                    ->filter();
                                                            })
                                                            ->unique()
                                                            ->values()
                                                            ->implode(', ');
                                                    @endphp

                                                    <a href="#"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#pvdcModal{{ $dep->uuid }}"
                                                        style="font-weight: bold; text-decoration: underline;">
                                                        Result
                                                    </a>

                                                    {{-- Modal Detail PVDC --}}
                                                    <div class="modal fade"
                                                        id="pvdcModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-labelledby="pvdcModalLabel{{ $dep->uuid }}"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-warning text-white">

                                                                    <h5 class="modal-title"
                                                                        id="pvdcModalLabel{{ $dep->uuid }}">
                                                                        Detail Pemeriksaan PVDC -
                                                                        Batch: {{ $batches ?: 'N/A' }}
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body table-responsive">

                                                                    @foreach ($dep->pvdc_detail as $mIndex => $mesin)

                                                                        <div class="mb-3 border p-3 rounded bg-light">

                                                                            <h6 class="fw-bold mb-2">
                                                                                Mesin:
                                                                                {{ $mesin['mesin'] ?? '-' }}
                                                                            </h6>

                                                                            <table
                                                                                class="table table-bordered table-striped table-sm text-center align-middle bg-white">

                                                                                <thead class="table-secondary">

                                                                                    <tr>
                                                                                        <th>No</th>
                                                                                        <th>Batch</th>
                                                                                        <th>No. Lot</th>
                                                                                        <th>Waktu</th>
                                                                                    </tr>

                                                                                </thead>

                                                                                <tbody>

                                                                                    @if (!empty($mesin['detail']))

                                                                                        @foreach ($mesin['detail'] as $index => $detail)

                                                                                            <tr>

                                                                                                <td>
                                                                                                    {{ $loop->iteration }}
                                                                                                </td>

                                                                                                <td>
                                                                                                    {{ data_get($detail, 'mincing.kode_produksi') ?? '-' }}
                                                                                                </td>

                                                                                                <td>
                                                                                                    {{ $detail['no_lot'] ?? '-' }}
                                                                                                </td>

                                                                                                <td>
                                                                                                    {{ $detail['waktu'] ?? '-' }}
                                                                                                </td>

                                                                                            </tr>

                                                                                        @endforeach

                                                                                    @else

                                                                                        <tr>
                                                                                            <td colspan="4">
                                                                                                Tidak ada data batch
                                                                                            </td>
                                                                                        </tr>

                                                                                    @endif

                                                                                </tbody>

                                                                            </table>

                                                                        </div>

                                                                    @endforeach

                                                                    <div class="mt-3 text-start">
                                                                        <strong>Catatan:</strong>
                                                                        {{ $dep->catatan ?? '-' }}
                                                                    </div>

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

                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            {{-- Status SPV --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                        class="text-danger fw-bold text-decoration-none">
                                                        Revision
                                                    </a>

                                                    {{-- Modal Revisi --}}
                                                    <div class="modal fade"
                                                        id="revisionModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-danger text-white">

                                                                    <h5 class="modal-title">
                                                                        Detail Revisi
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body text-start">

                                                                    <ul class="list-unstyled mb-0">

                                                                        <li>
                                                                            <strong>Status:</strong>
                                                                            Revision
                                                                        </li>

                                                                        <li>
                                                                            <strong>Catatan:</strong>
                                                                            {{ $dep->catatan_spv ?? '-' }}
                                                                        </li>

                                                                    </ul>

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

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="9" class="text-center">
                                                Belum ada data PVDC.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('WIRE')

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA NO. LOT WIRE</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table">

                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Shift</th>
                                        <th>Nama Varian</th>
                                        <th>Nama Supplier</th>
                                        <th>Data Wire</th>
                                        <th>QC</th>
                                        <th>SPV</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)
                                    
                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="align-middle">
                                                {{ !empty($dep->date)
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                |
                                                Shift: {{ $dep->shift ?? '-' }}
                                            </td>

                                            <td class="align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            <td class="align-middle">
                                                {{ $dep->nama_supplier ?? '-' }}
                                            </td>

                                            {{-- Data Wire --}}
                                            <td class="text-center align-middle">

                                                @php
                                                    $data_wire = $dep->data_wire ?? null;

                                                    if (is_string($data_wire)) {
                                                        $data_wire = json_decode($data_wire, true);
                                                    }

                                                    if (!is_array($data_wire)) {
                                                        $data_wire = [];
                                                    }
                                                @endphp

                                                @if (!empty($data_wire))

                                                    <a href="#"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#wireModal{{ $dep->uuid }}"
                                                        class="fw-bold text-decoration-underline">
                                                        Result
                                                    </a>

                                                    {{-- Modal Detail Wire --}}
                                                    <div class="modal fade"
                                                        id="wireModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog"
                                                            style="max-width: 70%;">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-warning text-white">

                                                                    <h5 class="modal-title">
                                                                        Detail Pemeriksaan Wire
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body text-start">

                                                                    @foreach ($data_wire as $mIndex => $mesin)

                                                                        <div class="mb-3 border-bottom pb-3">

                                                                            <h6 class="fw-bold text-primary">
                                                                                Mesin:
                                                                                {{ $mesin['mesin'] ?? '-' }}
                                                                            </h6>

                                                                            <table
                                                                                class="table table-bordered table-sm text-center">

                                                                                <thead class="table-light">

                                                                                    <tr>
                                                                                        <th>No</th>
                                                                                        <th>Start - End</th>
                                                                                        <th>No. Lot</th>
                                                                                    </tr>

                                                                                </thead>

                                                                                <tbody>

                                                                                    @if (!empty($mesin['detail']))

                                                                                        @foreach ($mesin['detail'] as $idx => $dtl)

                                                                                            <tr>

                                                                                                <td>
                                                                                                    {{ $idx + 1 }}
                                                                                                </td>

                                                                                                <td>
                                                                                                    {{ $dtl['start'] ?? '' }}
                                                                                                    -
                                                                                                    {{ $dtl['end'] ?? '' }}
                                                                                                </td>

                                                                                                <td>
                                                                                                    {{ $dtl['no_lot'] ?? '' }}
                                                                                                </td>

                                                                                            </tr>

                                                                                        @endforeach

                                                                                    @else

                                                                                        <tr>
                                                                                            <td colspan="3">
                                                                                                Tidak ada data
                                                                                            </td>
                                                                                        </tr>

                                                                                    @endif

                                                                                </tbody>

                                                                            </table>

                                                                        </div>

                                                                    @endforeach

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

                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            {{-- Status SPV --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <span class="fw-bold text-danger">
                                                        Revisi
                                                    </span>

                                                @else

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="7" class="text-center">
                                                Belum ada data Wire.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('WASHING')

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA PEMERIKSAAN WASHING DRYING</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table">

                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Shift</th>
                                        <th>Nama Varian</th>
                                        <th>Kode Batch</th>
                                        <th>Waktu</th>
                                        <th>Pemeriksaan</th>
                                        <th>QC</th>
                                        <th>Produksi</th>
                                        <th>SPV</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ !empty($dep->date)
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                |
                                                Shift: {{ $dep->shift ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->mincing->kode_produksi ?? $dep->kode_produksi ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ !empty($dep->pukul)
                                                    ? \Carbon\Carbon::parse($dep->pukul)->format('H:i')
                                                    : '-' }}
                                            </td>

                                            {{-- PEMERIKSAAN --}}
                                            <td class="text-center align-middle">

                                                <a href="javascript:void(0);"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailModal{{ $dep->uuid }}"
                                                    class="text-primary fw-bold text-decoration-none"
                                                    style="cursor: pointer;">
                                                    Result
                                                </a>

                                                {{-- Modal Detail --}}
                                                <div class="modal fade"
                                                    id="detailModal{{ $dep->uuid }}"
                                                    tabindex="-1"
                                                    aria-labelledby="detailModalLabel{{ $dep->uuid }}"
                                                    aria-hidden="true">

                                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">

                                                        <div class="modal-content">

                                                            <div class="modal-header bg-primary text-white">

                                                                <h5 class="modal-title"
                                                                    id="detailModalLabel{{ $dep->uuid }}">
                                                                    Detail Pemeriksaan Washing - Drying
                                                                </h5>

                                                                <button type="button"
                                                                    class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal"
                                                                    aria-label="Close">
                                                                </button>

                                                            </div>

                                                            <div class="modal-body text-start">

                                                                {{-- IDENTIFIKASI --}}
                                                                <h6 class="text-secondary fw-bold mt-2">
                                                                    Identifikasi
                                                                </h6>

                                                                <table class="table table-bordered table-sm mb-3">

                                                                    <tbody>

                                                                        <tr>
                                                                            <th style="width: 50%;">
                                                                                Nama Varian
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->nama_produk ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Kode Batch
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->mincing->kode_produksi ?? $dep->kode_produksi ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                    </tbody>

                                                                </table>

                                                                {{-- PENGECEKAN --}}
                                                                <h6 class="text-primary fw-bold mt-2">
                                                                    <i class="bi bi-check2-square me-1"></i>
                                                                    Pengecekan
                                                                </h6>

                                                                <table class="table table-bordered table-sm mb-3">

                                                                    <tbody>

                                                                        <tr>
                                                                            <th style="width: 50%;">
                                                                                Panjang Varian Akhir (Cm)
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->panjang_produk ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Diameter Varian Akhir (Mm)
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->diameter_produk ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Airtrap
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->airtrap ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Lengket
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->lengket ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Sisa Adonan
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->sisa_adonan ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Kekuatan Seal
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->kekuatan_seal ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Print Kode Batch
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->print_kode ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                    </tbody>

                                                                </table>

                                                                {{-- PC KLEER --}}
                                                                <h6 class="text-primary fw-bold mt-2">
                                                                    <i class="bi bi-droplet-half me-1"></i>
                                                                    PC Kleer
                                                                </h6>

                                                                <table class="table table-bordered table-sm mb-3">

                                                                    <tbody>

                                                                        <tr>
                                                                            <th style="width: 50%;">
                                                                                Konsentrasi PC Kleer 1 (%)
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->konsentrasi_pckleer ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Suhu PC Kleer 1 (°C)
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->suhu_pckleer_1 ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Suhu PC Kleer 2 (°C)
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->suhu_pckleer_2 ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                pH PC Kleer
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->ph_pckleer ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Kondisi Air PC Kleer
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->kondisi_air_pckleer ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                    </tbody>

                                                                </table>

                                                                {{-- POTTASIUM SORBATE --}}
                                                                <h6 class="text-primary fw-bold mt-2">
                                                                    <i class="bi bi-flask me-1"></i>
                                                                    Pottasium Sorbate
                                                                </h6>

                                                                <table class="table table-bordered table-sm mb-3">

                                                                    <tbody>

                                                                        <tr>
                                                                            <th style="width: 50%;">
                                                                                Konsentrasi Pottasium Sorbate (%)
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->konsentrasi_pottasium ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Suhu Pottasium Sorbate (°C)
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->suhu_pottasium ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                pH Pottasium Sorbate
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->ph_pottasium ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Kondisi Air Pottasium Sorbate
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->kondisi_pottasium ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                    </tbody>

                                                                </table>

                                                                {{-- SUHU & SPEED --}}
                                                                <h6 class="text-primary fw-bold mt-2">
                                                                    <i class="bi bi-speedometer2 me-1"></i>
                                                                    Suhu & Speed Conveyor
                                                                </h6>

                                                                <table class="table table-bordered table-sm mb-3">

                                                                    <tbody>

                                                                        <tr>
                                                                            <th style="width: 50%;">
                                                                                Suhu Heater (°C)
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->suhu_heater ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Speed Conv. Drying 1
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->speed_1 ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Speed Conv. Drying 2
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->speed_2 ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Speed Conv. Drying 3
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->speed_3 ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th>
                                                                                Speed Conv. Drying 4
                                                                            </th>

                                                                            <td>
                                                                                {{ $dep->speed_4 ?? '-' }}
                                                                            </td>
                                                                        </tr>

                                                                    </tbody>

                                                                </table>

                                                                {{-- CATATAN --}}
                                                                @if (!empty($dep->catatan))

                                                                    <h6 class="text-primary fw-bold mt-2">
                                                                        <i class="bi bi-journal-text me-1"></i>
                                                                        Catatan
                                                                    </h6>

                                                                    <p>
                                                                        {{ $dep->catatan }}
                                                                    </p>

                                                                @endif

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

                                            </td>

                                            {{-- QC --}}
                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            {{-- PRODUKSI --}}
                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produksi ?? '-' }}
                                            </td>

                                            {{-- STATUS SPV --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                        class="text-danger fw-bold text-decoration-none"
                                                        style="cursor: pointer;">
                                                        Revision
                                                    </a>

                                                    {{-- Modal Revisi --}}
                                                    <div class="modal fade"
                                                        id="revisionModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-danger text-white">

                                                                    <h5 class="modal-title">
                                                                        Detail Revisi
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body text-start">

                                                                    <ul class="list-unstyled mb-0">

                                                                        <li>
                                                                            <strong>Status:</strong>
                                                                            Revision
                                                                        </li>

                                                                        <li>
                                                                            <strong>Catatan:</strong>
                                                                            {{ $dep->catatan_spv ?? '-' }}
                                                                        </li>

                                                                    </ul>

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

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="9" class="text-center">
                                                Belum ada data pengecekan washing.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('PEMASAKAN')

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA PENGECEKAN PEMASAKAN</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table">

                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Shift</th>
                                        <th>Nama Varian</th>
                                        <th>Kode Batch</th>
                                        <th>Jumlah Tray</th>
                                        <th>No. Chamber</th>
                                        <th>Berat Varian (Gram)</th>
                                        <th>Suhu Varian (°C)</th>
                                        <th>Total Reject (Kg)</th>
                                        <th>Pengecekan</th>
                                        <th>QC</th>
                                        <th>SPV</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        @php
                                            $cooking = $dep->cooking ?? [];
                                        @endphp

                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ !empty($dep->date)
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                |
                                                Shift: {{ $dep->shift ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            {{-- KODE BATCH --}}
                                            <td class="text-center align-middle">

                                                @if (is_array($dep->kode_produksi))

                                                    @foreach ($dep->kode_produksi as $uuid)

                                                        {{ $dep->stuffingData[$uuid]->kode_produksi ?? 'Tidak ditemukan' }}

                                                        @if (!$loop->last)
                                                            <br>
                                                        @endif

                                                    @endforeach

                                                @else

                                                    -

                                                @endif

                                            </td>

                                            {{-- JUMLAH TRAY --}}
                                            <td class="text-center align-middle">

                                                @if (is_array($dep->jumlah_tray))

                                                    @foreach ($dep->jumlah_tray as $tray)
                                                        {{ $tray }}

                                                        @if (!$loop->last)
                                                            <br>
                                                        @endif
                                                    @endforeach

                                                @else

                                                    -

                                                @endif

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->no_chamber ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->berat_produk ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->suhu_produk ?? '-' }}
                                            </td>

                                            {{-- TOTAL REJECT --}}
                                            <td class="text-center align-middle">

                                                @php
                                                    $rejects = is_array($dep->total_reject)
                                                        ? array_filter(
                                                            $dep->total_reject,
                                                            fn($v) => !is_null($v) && $v !== ''
                                                        )
                                                        : [];
                                                @endphp

                                                {{ !empty($rejects) ? implode(' / ', $rejects) : '-' }}

                                            </td>

                                            {{-- PEMERIKSAAN --}}
                                            <td class="text-center align-middle">

                                                @if (!empty($cooking))

                                                    <a href="#"
                                                        class="fw-bold text-decoration-underline text-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cookingModal{{ $dep->uuid }}">
                                                        Result
                                                    </a>

                                                    {{-- MODAL DETAIL PEMASAKAN --}}
                                                    <div class="modal fade"
                                                        id="cookingModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">

                                                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

                                                                <div class="modal-header bg-primary bg-gradient text-white">

                                                                    <h5 class="modal-title fw-semibold">
                                                                        <i class="bi bi-fire me-2"></i>
                                                                        Detail Proses Pemasakan
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body p-4 bg-light-subtle">

                                                                    @php
                                                                        $sections = [
                                                                            '2. Tekanan & Suhu Awal' => [
                                                                                'Tekanan Angin' => 'tekanan_angin',
                                                                                'Tekanan Steam' => 'tekanan_steam',
                                                                                'Tekanan Air' => 'tekanan_air',
                                                                            ],

                                                                            '3. Pemanasan Awal' => [
                                                                                'Suhu Air Awal' => 'suhu_air_awal',
                                                                                'Tekanan Awal' => 'tekanan_awal',
                                                                                'Waktu Mulai' => 'waktu_mulai_awal',
                                                                                'Waktu Selesai' => 'waktu_selesai_awal',
                                                                            ],

                                                                            '4. Proses Pemanasan' => [
                                                                                'Suhu Air Proses' => 'suhu_air_proses',
                                                                                'Tekanan Proses' => 'tekanan_proses',
                                                                                'Waktu Mulai' => 'waktu_mulai_proses',
                                                                                'Waktu Selesai' => 'waktu_selesai_proses',
                                                                            ],

                                                                            '5. Sterilisasi' => [
                                                                                'Suhu Air Sterilisasi' => 'suhu_air_sterilisasi',
                                                                                'Thermometer Retort' => 'thermometer_retort',
                                                                                'Tekanan Sterilisasi' => 'tekanan_sterilisasi',
                                                                            ],

                                                                            '10. Hasil Pemasakan' => [
                                                                                'Suhu Varian Akhir' => 'suhu_produk_akhir',
                                                                                'Panjang' => 'panjang',
                                                                                'Diameter' => 'diameter',
                                                                                'Rasa' => 'rasa',
                                                                                'Warna' => 'warna',
                                                                                'Texture' => 'texture',
                                                                            ],
                                                                        ];
                                                                    @endphp

                                                                    @foreach ($sections as $sectionTitle => $rows)

                                                                        <div class="mb-3">

                                                                            <h6 class="fw-bold text-primary">
                                                                                {{ $sectionTitle }}
                                                                            </h6>

                                                                            <div class="table-responsive shadow-sm rounded">

                                                                                <table class="table table-bordered table-sm align-middle text-center mb-0 bg-white">

                                                                                    <tbody>

                                                                                        @foreach ($rows as $label => $key)

                                                                                            <tr>

                                                                                                <td class="fw-semibold text-start ps-3 w-50">
                                                                                                    {{ $label }}
                                                                                                </td>

                                                                                                <td class="text-start ps-3">

                                                                                                    @php
                                                                                                        $value = $cooking[$key] ?? '-';
                                                                                                    @endphp

                                                                                                    @if (is_array($value))

                                                                                                        @foreach ($value as $val)
                                                                                                            <span class="badge bg-gradient bg-light text-dark border border-secondary me-1 mb-1">
                                                                                                                {{ $val }}
                                                                                                            </span>
                                                                                                        @endforeach

                                                                                                    @else

                                                                                                        {{ $value }}

                                                                                                    @endif

                                                                                                </td>

                                                                                            </tr>

                                                                                        @endforeach

                                                                                    </tbody>

                                                                                </table>

                                                                            </div>

                                                                        </div>

                                                                    @endforeach

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

                                            {{-- QC --}}
                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            {{-- SPV --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <span class="fw-bold text-danger">
                                                        Revisi
                                                    </span>

                                                @else

                                                    <span class="fw-bold text-secondary">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="12" class="text-center">
                                                Belum ada data pemasakan.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('CHAMBER')

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA VERIFIKASI TIMER CHAMBER</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table">

                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Shift</th>
                                        <th>Pemeriksaan</th>
                                        <th>QC (User)</th>
                                        <th>Operator</th>
                                        <th>Status SPV</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ !empty($dep->date)
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                |
                                                Shift: {{ $dep->shift ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @php
                                                    $chambers = is_string($dep->verifikasi ?? null)
                                                        ? json_decode($dep->verifikasi, true)
                                                        : ($dep->verifikasi ?? null);

                                                    $rentang_menit = [5, 10, 20, 30, 60];
                                                @endphp

                                                @if (!empty($chambers) && is_array($chambers))

                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#chamberModal{{ $dep->uuid }}"
                                                        class="text-primary fw-bold text-decoration-none"
                                                        style="cursor: pointer;">
                                                        Result
                                                    </a>

                                                    <div class="modal fade"
                                                        id="chamberModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-labelledby="chamberModalLabel{{ $dep->uuid }}"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-primary text-white">

                                                                    <h5 class="modal-title"
                                                                        id="chamberModalLabel{{ $dep->uuid }}">
                                                                        <i class="bi bi-list-task me-2"></i>
                                                                        Detail Verifikasi Timer Chamber
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body text-start">

                                                                    <div class="table-responsive">

                                                                        <table class="table table-bordered table-striped table-sm text-center align-middle mb-3"
                                                                            style="font-size: 0.8rem;">

                                                                            <thead class="table-light">

                                                                                <tr class="table-secondary">

                                                                                    <th rowspan="2"
                                                                                        colspan="2"
                                                                                        class="align-middle">
                                                                                        RENTANG UKUR
                                                                                    </th>

                                                                                    @foreach ($chambers as $index => $row)

                                                                                        <th colspan="6"
                                                                                            class="fw-bold">
                                                                                            No. Chamber {{ $index + 1 }}
                                                                                        </th>

                                                                                    @endforeach

                                                                                </tr>

                                                                                <tr>

                                                                                    @foreach ($chambers as $index => $row)

                                                                                        <th colspan="2">
                                                                                            PLC
                                                                                        </th>

                                                                                        <th colspan="2">
                                                                                            STOPWATCH
                                                                                        </th>

                                                                                        <th colspan="2">
                                                                                            KOREKSI
                                                                                        </th>

                                                                                    @endforeach

                                                                                </tr>

                                                                                <tr>

                                                                                    <th>MNT</th>
                                                                                    <th>DTK</th>

                                                                                    @foreach ($chambers as $index => $row)

                                                                                        <th>MNT</th>
                                                                                        <th>DTK</th>

                                                                                        <th>MNT</th>
                                                                                        <th>DTK</th>

                                                                                        <th colspan="2">
                                                                                            Factor
                                                                                        </th>

                                                                                    @endforeach

                                                                                </tr>

                                                                            </thead>

                                                                            <tbody>

                                                                                @foreach ($rentang_menit as $rentang)

                                                                                    <tr>

                                                                                        <td class="fw-bold">
                                                                                            {{ $rentang }}
                                                                                        </td>

                                                                                        <td>
                                                                                            00
                                                                                        </td>

                                                                                        @foreach ($chambers as $index => $row)

                                                                                            <td>
                                                                                                {{ $row['plc_menit_' . $rentang] ?? '-' }}
                                                                                            </td>

                                                                                            <td>
                                                                                                {{ $row['plc_detik_' . $rentang] ?? '-' }}
                                                                                            </td>

                                                                                            <td>
                                                                                                {{ $row['stopwatch_menit_' . $rentang] ?? '-' }}
                                                                                            </td>

                                                                                            <td>
                                                                                                {{ $row['stopwatch_detik_' . $rentang] ?? '-' }}
                                                                                            </td>

                                                                                            <td colspan="2"
                                                                                                class="fw-bold text-danger">
                                                                                                {{ $row['faktor_koreksi_' . $rentang] ?? '-' }}
                                                                                            </td>

                                                                                        @endforeach

                                                                                    </tr>

                                                                                @endforeach

                                                                            </tbody>

                                                                        </table>

                                                                    </div>

                                                                    @if (!empty($dep->catatan))

                                                                        <h6 class="text-primary fw-bold mt-2">
                                                                            <i class="bi bi-journal-text me-1"></i>
                                                                            Catatan
                                                                        </h6>

                                                                        <p>
                                                                            {{ $dep->catatan }}
                                                                        </p>

                                                                    @endif

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

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_operator ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                        class="text-danger fw-bold text-decoration-none"
                                                        style="cursor: pointer;">
                                                        Revision
                                                    </a>

                                                    <div class="modal fade"
                                                        id="revisionModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-danger text-white">

                                                                    <h5 class="modal-title">
                                                                        Detail Revisi
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body text-start">

                                                                    <ul class="list-unstyled mb-0">

                                                                        <li>
                                                                            <strong>Status:</strong>
                                                                            Revision
                                                                        </li>

                                                                        <li>
                                                                            <strong>Catatan:</strong>
                                                                            {{ $dep->catatan_spv ?? '-' }}
                                                                        </li>

                                                                    </ul>

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

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="6" class="text-center">
                                                Belum ada data verifikasi timer chamber.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('SAMPLING FG')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA PEMERIKSAAN SAMPLING FINISH GOOD</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead class="table-secondary text-center">

                                    <tr>
                                        <th rowspan="2" style="width: 3%;">NO.</th>
                                        <th rowspan="2" style="width: 8%;">Tanggal | Shift</th>
                                        <th rowspan="2" style="width: 4%;">Palet</th>
                                        <th rowspan="2" style="width: 12%;">Nama Varian</th>
                                        <th rowspan="2" style="width: 6%;">Kode Batch</th>
                                        <th rowspan="2" style="width: 6%;">Exp. Date</th>

                                        <th colspan="4">
                                            Pemeriksaan Proses Cartoning
                                        </th>

                                        <th rowspan="2" style="width: 5%;">
                                            Isi<br>/Box
                                        </th>

                                        <th rowspan="2" style="width: 4%;">
                                            Jml<br>Box
                                        </th>

                                        <th colspan="3">
                                            Status Varian
                                        </th>

                                        <th rowspan="2" style="width: 5%;">
                                            Item<br>Mutu
                                        </th>

                                        <th rowspan="2" style="width: 8%;">
                                            Catatan
                                        </th>

                                        <th rowspan="2" style="width: 4%;">
                                            QC
                                        </th>

                                        <th rowspan="2" style="width: 4%;">
                                            Koord
                                        </th>

                                        <th rowspan="2" style="width: 4%;">
                                            SPV
                                        </th>
                                    </tr>

                                    <tr>

                                        <th style="width: 4%;">
                                            Jam
                                        </th>

                                        <th style="width: 4%;">
                                            Kalib
                                        </th>

                                        <th style="width: 4%;">
                                            Berat
                                        </th>

                                        <th style="width: 5%;">
                                            Ket
                                        </th>

                                        <th style="width: 3%;">
                                            Rls
                                        </th>

                                        <th style="width: 3%;">
                                            Rjc
                                        </th>

                                        <th style="width: 3%;">
                                            Hld
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        <tr>

                                            <td class="text-center">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center">
                                                {{ $dep->date
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                |
                                                {{ $dep->shift ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                {{ $dep->palet ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            <td class="text-center">

                                                @php
                                                    $kodeBatch = $dep->kode_produksi ?? '-';

                                                    if (
                                                        $kodeBatch &&
                                                        \Illuminate\Support\Str::isUuid($kodeBatch)
                                                    ) {
                                                        $kodeBatch = \App\Models\Mincing::where(
                                                            'uuid',
                                                            $kodeBatch
                                                        )->value('kode_produksi') ?? $kodeBatch;
                                                    }
                                                @endphp

                                                {{ $kodeBatch }}

                                            </td>

                                            <td class="text-center">
                                                {{ $dep->exp_date
                                                    ? \Carbon\Carbon::parse($dep->exp_date)->format('d-m-Y')
                                                    : '-' }}
                                            </td>

                                            {{-- JAM --}}
                                            <td class="text-center">
                                                {{ $dep->pukul
                                                    ? \Carbon\Carbon::parse($dep->pukul)->format('H:i')
                                                    : '-' }}
                                            </td>

                                            {{-- KALIBRASI --}}
                                            <td class="text-center">

                                                @if ($dep->kalibrasi == 'Sesuai')

                                                    <span class="text-success fw-bold">
                                                        ✔
                                                    </span>

                                                @else

                                                    <span class="text-danger fw-bold">
                                                        ✘
                                                    </span>

                                                @endif

                                            </td>

                                            {{-- BERAT --}}
                                            <td class="text-center">
                                                {{ $dep->berat_produk ?? '-' }}
                                            </td>

                                            {{-- KETERANGAN --}}
                                            <td class="text-center small">
                                                {{ $dep->keterangan ?? '-' }}
                                            </td>

                                            {{-- ISI / BOX --}}
                                            <td class="text-center">
                                                {{ $dep->isi_per_box ?? '-' }}
                                            </td>

                                            {{-- JUMLAH BOX --}}
                                            <td class="text-center">
                                                {{ $dep->jumlah_box ?? '-' }}
                                            </td>

                                            {{-- RELEASE --}}
                                            <td class="text-center">
                                                {{ $dep->release ?? '-' }}
                                            </td>

                                            {{-- REJECT --}}
                                            <td class="text-center">
                                                {{ $dep->reject ?? '-' }}
                                            </td>

                                            {{-- HOLD --}}
                                            <td class="text-center">
                                                {{ $dep->hold ?? '-' }}
                                            </td>

                                            {{-- ITEM MUTU --}}
                                            <td class="text-center small">
                                                {{ $dep->item_mutu ?? '-' }}
                                            </td>

                                            {{-- CATATAN --}}
                                            <td class="text-start small">
                                                {{ $dep->catatan ?? '-' }}
                                            </td>

                                            {{-- QC --}}
                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            {{-- KOORD --}}
                                            <td class="text-center">
                                                {{ $dep->nama_koordinator ?? '-' }}
                                            </td>

                                            {{-- STATUS SPV --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <span class="fw-bold text-danger">
                                                        Revisi
                                                    </span>

                                                @else

                                                    <span class="fw-bold text-secondary">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="20" class="text-center py-3">
                                                Belum ada data.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('SAMPLING')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA PEMERIKSAAN SAMPLING PRODUK</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table">

                                <thead class="table-secondary text-center">

                                    <tr>
                                        <th>NO.</th>
                                        <th>Tanggal | Shift</th>
                                        <th>Jenis Sampling</th>
                                        <th>Nama Varian</th>
                                        <th>Kode Batch</th>
                                        <th>Jumlah</th>
                                        <th>Jamur</th>
                                        <th>Lendir</th>
                                        <th>Klip Tajam</th>
                                        <th>Pin Hole</th>
                                        <th>Air Trap PVDC</th>
                                        <th>Air Trap Produk</th>
                                        <th>Keriput</th>
                                        <th>Bengkok</th>
                                        <th>Non Kode</th>
                                        <th>Over Lap</th>
                                        <th>Kecil</th>
                                        <th>Terjepit</th>
                                        <th>Double Klip</th>
                                        <th>Seal Halus</th>
                                        <th>Basah</th>
                                        <th>Dll</th>
                                        <th>Catatan</th>
                                        <th>QC</th>
                                        <th>SPV</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">

                                                {{ $dep->date
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}

                                                |

                                                Shift:
                                                {{ $dep->shift ?? '-' }}

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->jenis_sampel ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @php
                                                    $kodeBatch = $dep->kode_produksi ?? '-';

                                                    if (
                                                        $kodeBatch &&
                                                        \Illuminate\Support\Str::isUuid($kodeBatch)
                                                    ) {
                                                        $kodeBatch = \App\Models\Mincing::where(
                                                            'uuid',
                                                            $kodeBatch
                                                        )->value('kode_produksi') ?? $kodeBatch;
                                                    }
                                                @endphp

                                                {{ $kodeBatch }}

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->jumlah ?? '-' }}
                                                {{ $dep->jenis_kemasan ?? '' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->jamur ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->jamur, 2, ',', '.'), '0'), ',')
                                                    : ($dep->jamur ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->lendir ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->lendir, 2, ',', '.'), '0'), ',')
                                                    : ($dep->lendir ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->klip_tajam ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->klip_tajam, 2, ',', '.'), '0'), ',')
                                                    : ($dep->klip_tajam ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->pin_hole ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->pin_hole, 2, ',', '.'), '0'), ',')
                                                    : ($dep->pin_hole ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->air_trap_pvdc ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->air_trap_pvdc, 2, ',', '.'), '0'), ',')
                                                    : ($dep->air_trap_pvdc ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->air_trap_produk ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->air_trap_produk, 2, ',', '.'), '0'), ',')
                                                    : ($dep->air_trap_produk ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->keriput ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->keriput, 2, ',', '.'), '0'), ',')
                                                    : ($dep->keriput ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->bengkok ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->bengkok, 2, ',', '.'), '0'), ',')
                                                    : ($dep->bengkok ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->non_kode ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->non_kode, 2, ',', '.'), '0'), ',')
                                                    : ($dep->non_kode ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->over_lap ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->over_lap, 2, ',', '.'), '0'), ',')
                                                    : ($dep->over_lap ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->kecil ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->kecil, 2, ',', '.'), '0'), ',')
                                                    : ($dep->kecil ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->terjepit ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->terjepit, 2, ',', '.'), '0'), ',')
                                                    : ($dep->terjepit ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->double_klip ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->double_klip, 2, ',', '.'), '0'), ',')
                                                    : ($dep->double_klip ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->seal_halus ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->seal_halus, 2, ',', '.'), '0'), ',')
                                                    : ($dep->seal_halus ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->basah ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->basah, 2, ',', '.'), '0'), ',')
                                                    : ($dep->basah ?? '-') }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ is_numeric($dep->dll ?? null)
                                                    ? rtrim(rtrim(number_format((float) $dep->dll, 2, ',', '.'), '0'), ',')
                                                    : ($dep->dll ?? '-') }}
                                            </td>

                                            {{-- CATATAN --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->catatan)

                                                    <a href="#"
                                                        class="text-primary text-decoration-underline"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#catatanModalCombined{{ $dep->uuid ?? $dep->id }}">

                                                        Lihat Catatan

                                                    </a>

                                                    <div class="modal fade"
                                                        id="catatanModalCombined{{ $dep->uuid ?? $dep->id }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-primary text-white">

                                                                    <h5 class="modal-title">
                                                                        Catatan
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body text-start">
                                                                    {{ $dep->catatan }}
                                                                </div>

                                                                <div class="modal-footer">

                                                                    <button type="button"
                                                                        class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        Tutup
                                                                    </button>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                @else

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                            {{-- QC --}}
                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? '-' }}
                                            </td>

                                            {{-- SPV --}}
                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#revisionModalCombined{{ $dep->uuid ?? $dep->id }}"
                                                        class="text-danger fw-bold text-decoration-none"
                                                        style="cursor: pointer;">

                                                        Revision

                                                    </a>

                                                    <div class="modal fade"
                                                        id="revisionModalCombined{{ $dep->uuid ?? $dep->id }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-danger text-white">

                                                                    <h5 class="modal-title">
                                                                        Detail Revisi
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body">

                                                                    <ul class="list-unstyled mb-0">

                                                                        <li>
                                                                            <strong>Status:</strong>
                                                                            Revision
                                                                        </li>

                                                                        <li>
                                                                            <strong>Catatan:</strong>
                                                                            {{ $dep->catatan_spv ?? '-' }}
                                                                        </li>

                                                                    </ul>

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

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="25" class="text-center">
                                                Belum ada data sampling.
                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('RELEASE PACKING')

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA RELEASE PACKING</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table">

                                <thead class="table-secondary text-center">

                                    <tr>
                                        <th>NO.</th>
                                        <th>Date</th>
                                        <th>Jenis Kemasan</th>
                                        <th>Nama Varian</th>
                                        <th>Kode Batch</th>
                                        <th>Expired</th>
                                        <th>No. Palet</th>
                                        <th>Jumlah Release</th>
                                        <th>Keterangan</th>
                                        <th>QC</th>
                                        <th>SPV</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->date
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->jenis_kemasan ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @php
                                                    $kodeBatch = $dep->kode_produksi ?? '-';

                                                    if (
                                                        $kodeBatch &&
                                                        \Illuminate\Support\Str::isUuid($kodeBatch)
                                                    ) {
                                                        $kodeBatch = $dep->mincing->kode_produksi ?? '-';
                                                    }
                                                @endphp

                                                {{ $kodeBatch }}

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->expired_date
                                                    ? \Carbon\Carbon::parse($dep->expired_date)->format('d-m-Y')
                                                    : '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->no_palet ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->release ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->keterangan ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? $dep->username ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <span class="fw-bold text-danger">
                                                        Revision
                                                    </span>

                                                @else

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="11" class="text-center">
                                                Belum ada data release packing.
                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('PACKING')

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA PEMERIKSAAN PROSES PACKING</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table">

                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Shift</th>
                                        <th>Nama Varian</th>
                                        <th>Waktu</th>
                                        <th>Pemeriksaan Packing</th>
                                        <th>QC</th>
                                        <th>Produksi</th>
                                        <th>SPV</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        @php
                                            $suhuData = [];

                                            if (is_array($dep->suhu)) {
                                                $suhuData = $dep->suhu;
                                            } elseif (is_string($dep->suhu) && !empty($dep->suhu)) {
                                                $decodedSuhu = json_decode($dep->suhu, true);
                                                $suhuData = is_array($decodedSuhu) ? $decodedSuhu : [];
                                            }

                                            $kemasans = [];

                                            if (is_array($dep->data_kemasan)) {
                                                $kemasans = $dep->data_kemasan;
                                            } elseif (is_object($dep->data_kemasan)) {
                                                $kemasans = (array) $dep->data_kemasan;
                                            } elseif (is_string($dep->data_kemasan) && !empty($dep->data_kemasan)) {
                                                $decodedKemasan = json_decode($dep->data_kemasan, true);
                                                $kemasans = is_array($decodedKemasan) ? $decodedKemasan : [];
                                            }

                                            if (
                                                !empty($kemasans) &&
                                                !array_is_list($kemasans) &&
                                                (
                                                    isset($kemasans['jenis_kemasan']) ||
                                                    isset($kemasans['no_lot_kemasan']) ||
                                                    isset($kemasans['tgl_kedatangan']) ||
                                                    isset($kemasans['nama_supplier'])
                                                )
                                            ) {
                                                $kemasans = [$kemasans];
                                            }

                                            $kodeToples = $dep->kode_toples ?? null;

                                            $kodeProduksi = $kodeToples
                                                ? \App\Models\Mincing::where('uuid', $kodeToples)->value('kode_produksi')
                                                : null;

                                            $qcName = \App\Models\User::where('username', $dep->username)->value('name');
                                        @endphp

                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->date
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                |
                                                Shift: {{ $dep->shift ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->waktu
                                                    ? \Carbon\Carbon::parse($dep->waktu)->format('H:i')
                                                    : '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                <a href="javascript:void(0);"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#packingSearchModal{{ $dep->uuid }}"
                                                    class="fw-bold text-decoration-underline text-primary">
                                                    Result
                                                </a>

                                                <div class="modal fade"
                                                    id="packingSearchModal{{ $dep->uuid }}"
                                                    tabindex="-1"
                                                    aria-labelledby="packingSearchModalLabel{{ $dep->uuid }}"
                                                    aria-hidden="true">

                                                    <div class="modal-dialog modal-xl">

                                                        <div class="modal-content text-start">

                                                            <div class="modal-header bg-info text-white">

                                                                <h5 class="modal-title"
                                                                    id="packingSearchModalLabel{{ $dep->uuid }}">
                                                                    Detail Pemeriksaan Proses Packing:
                                                                    {{ $dep->nama_produk ?? '-' }}
                                                                </h5>

                                                                <button type="button"
                                                                    class="btn-close"
                                                                    data-bs-dismiss="modal"
                                                                    aria-label="Close">
                                                                </button>

                                                            </div>

                                                            <div class="modal-body">

                                                                <div class="row">

                                                                    <div class="col-md-6">

                                                                        <table class="table table-sm table-bordered">

                                                                            <tr>
                                                                                <th>Waktu</th>
                                                                                <td>
                                                                                    {{ $dep->waktu
                                                                                        ? \Carbon\Carbon::parse($dep->waktu)->format('H:i')
                                                                                        : '-' }}
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <th>Kode Toples (Batch)</th>
                                                                                <td>
                                                                                    {{ $kodeProduksi ?? $kodeToples ?? '-' }}
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <th>Suhu</th>
                                                                                <td>

                                                                                    @if (!empty($suhuData))

                                                                                        @foreach ($suhuData as $suhu)

                                                                                            @php
                                                                                                $suhuValue = is_array($suhu)
                                                                                                    ? ($suhu['suhu'] ?? '-')
                                                                                                    : (is_object($suhu)
                                                                                                        ? ($suhu->suhu ?? '-')
                                                                                                        : $suhu);
                                                                                            @endphp

                                                                                            {{ $suhuValue }}°C{{ !$loop->last ? ', ' : '' }}

                                                                                        @endforeach

                                                                                    @else

                                                                                        -

                                                                                    @endif

                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <th>Jml Produk</th>
                                                                                <td>
                                                                                    {{ $dep->jumlah_produk ?? '-' }}
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <th>QR Code</th>
                                                                                <td>

                                                                                    @if (!empty($dep->qrcode) && !in_array($dep->qrcode, ['Ok', 'Tidak Ok']))

                                                                                        <a href="{{ asset($dep->qrcode) }}"
                                                                                            target="_blank">

                                                                                            <img src="{{ asset($dep->qrcode) }}"
                                                                                                width="60"
                                                                                                class="img-thumbnail"
                                                                                                alt="QR Code">

                                                                                        </a>

                                                                                    @else

                                                                                        {{ $dep->qrcode ?? '-' }}

                                                                                    @endif

                                                                                </td>
                                                                            </tr>

                                                                        </table>

                                                                    </div>

                                                                    <div class="col-md-6">

                                                                        <table class="table table-sm table-bordered">

                                                                            <tr>
                                                                                <th>Kalibrasi</th>
                                                                                <td>
                                                                                    {{ $dep->kalibrasi ?? '-' }}
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <th>Berat Pcs</th>
                                                                                <td>
                                                                                    {{ $dep->berat_pcs ?? '-' }} gr
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <th>Berat Pack</th>
                                                                                <td>
                                                                                    {{ $dep->berat_pack ?? '-' }} gr
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <th>Kondisi Segel</th>
                                                                                <td>
                                                                                    {{ $dep->kondisi_segel ?? '-' }}
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <th>Kode Printing</th>
                                                                                <td>

                                                                                    @if (!empty($dep->kode_printing))

                                                                                        <a href="{{ asset($dep->kode_printing) }}"
                                                                                            target="_blank">

                                                                                            <img src="{{ asset($dep->kode_printing) }}"
                                                                                                width="60"
                                                                                                class="img-thumbnail"
                                                                                                alt="Printing">

                                                                                        </a>

                                                                                    @else

                                                                                        -

                                                                                    @endif

                                                                                </td>
                                                                            </tr>

                                                                        </table>

                                                                    </div>

                                                                </div>

                                                                <h6 class="mt-4 fw-bold text-primary">
                                                                    <i class="bi bi-box-seam"></i>
                                                                    Data Kemasan:
                                                                </h6>

                                                                <div class="table-responsive">

                                                                    <table class="table table-bordered table-sm text-center align-middle">

                                                                        <thead class="table-light">

                                                                            <tr>
                                                                                <th>Jenis Kemasan</th>
                                                                                <th>No. Lot Kemasan</th>
                                                                                <th>Tanggal Kedatangan</th>
                                                                                <th>Supplier</th>
                                                                            </tr>

                                                                        </thead>

                                                                        @php
                                                                            $kemasans = $dep->data_kemasan;

                                                                            if (is_string($kemasans)) {
                                                                                $kemasans = json_decode($kemasans, true);

                                                                                if (is_string($kemasans)) {
                                                                                    $kemasans = json_decode($kemasans, true);
                                                                                }
                                                                            }

                                                                            $kemasans = is_array($kemasans) ? $kemasans : [];
                                                                        @endphp

                                                                        <tbody>
                                                                            @foreach ($kemasans as $item)
                                                                                <tr>
                                                                                    <td>{{ $item['jenis_kemasan'] ?? '-' }}</td>
                                                                                    <td>{{ $item['no_lot_kemasan'] ?? '-' }}</td>
                                                                                    <td>
                                                                                        {{ $item['tgl_kedatangan']
                                                                                            ? \Carbon\Carbon::parse($item['tgl_kedatangan'])->format('d-m-Y')
                                                                                            : '-' }}
                                                                                    </td>
                                                                                    <td>{{ $item['nama_supplier'] ?? '-' }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>

                                                                    </table>

                                                                </div>

                                                                <div class="mt-3">
                                                                    <strong>Keterangan:</strong>
                                                                    {{ $dep->keterangan ?? '-' }}
                                                                </div>

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

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $qcName ?? $dep->username ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produksi ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <span class="fw-bold text-danger">
                                                        Revision
                                                    </span>

                                                @else

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="8" class="text-center">
                                                Belum ada data packing.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('KARTON')

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA KONTROL LABELISASI KARTON</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped table-hover mb-0">

                                <thead class="table-secondary text-center">

                                    <tr>
                                        <th>NO.</th>
                                        <th>Tanggal</th>
                                        <th>Start - Finish</th>
                                        <th>Nama Varian</th>
                                        <th>Kode Batch</th>
                                        <th>Bukti Kode</th>
                                        <th>Tgl Kedatangan</th>
                                        <th>Jumlah</th>
                                        <th>Nama Supplier</th>
                                        <th>No. Lot Karton</th>
                                        <th>Keterangan</th>
                                        <th>Operator</th>
                                        <th>KR</th>
                                        <th>QC</th>
                                        <th>SPV</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->date
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                {{ $dep->waktu_mulai
                                                    ? \Carbon\Carbon::parse($dep->waktu_mulai)->format('H:i')
                                                    : '-' }}

                                                -

                                                {{ $dep->waktu_selesai
                                                    ? \Carbon\Carbon::parse($dep->waktu_selesai)->format('H:i')
                                                    : '-' }}

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->mincing->kode_produksi ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @if ($dep->kode_karton)

                                                    <a href="{{ asset('storage/' . str_replace('public/', '', $dep->kode_karton)) }}"
                                                        target="_blank">

                                                        <img src="{{ asset('storage/' . str_replace('public/', '', $dep->kode_karton)) }}"
                                                            alt="Karton"
                                                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">

                                                    </a>

                                                @else

                                                    <span class="text-muted">
                                                        Tidak ada gambar
                                                    </span>

                                                @endif

                                            </td>

                                            <td class="text-center align-middle">

                                                {{ $dep->tgl_kedatangan
                                                    ? \Carbon\Carbon::parse($dep->tgl_kedatangan)->format('d-m-Y')
                                                    : '-' }}

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->jumlah ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_supplier ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->no_lot ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->keterangan ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_operator ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_koordinator ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? $dep->username ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#kartonRevisionModal{{ $dep->uuid }}"
                                                        class="text-danger fw-bold text-decoration-none">

                                                        Revision

                                                    </a>

                                                    <div class="modal fade"
                                                        id="kartonRevisionModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-labelledby="kartonRevisionModalLabel{{ $dep->uuid }}"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-dialog-centered">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-danger text-white">

                                                                    <h5 class="modal-title"
                                                                        id="kartonRevisionModalLabel{{ $dep->uuid }}">

                                                                        Detail Revisi

                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body text-start">

                                                                    <ul class="list-unstyled mb-0">

                                                                        <li>
                                                                            <strong>Status:</strong>
                                                                            Revision
                                                                        </li>

                                                                        <li>
                                                                            <strong>Catatan:</strong>
                                                                            {{ $dep->catatan_spv ?? '-' }}
                                                                        </li>

                                                                    </ul>

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

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="15" class="text-center">
                                                Belum ada data karton.
                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

            @case('ORGANOLEPTIK')

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">DATA PEMERIKSAAN ORGANOLEPTIK</span>
                        <span class="badge bg-light text-dark">{{ $data->count() }}</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table">

                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>NO.</th>
                                        <th>Date | Shift</th>
                                        <th>Nama Varian</th>
                                        <th>Hasil Sensori</th>
                                        <th>QC</th>
                                        <th>SPV</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php
                                        $no = 1;
                                    @endphp

                                    @forelse ($data as $dep)

                                        @php
                                            $sensori = $dep->sensori;

                                            if (is_string($sensori)) {
                                                $sensori = json_decode($sensori, true);

                                                if (is_string($sensori)) {
                                                    $sensori = json_decode($sensori, true);
                                                }
                                            }

                                            $sensori = is_array($sensori) ? $sensori : [];
                                        @endphp

                                        <tr>

                                            <td class="text-center align-middle">
                                                {{ $no++ }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->date
                                                    ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                    : '-' }}
                                                |
                                                Shift: {{ $dep->shift ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">
                                                {{ $dep->nama_produk ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @if (!empty($sensori))

                                                    <a href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#organoleptikSearchModal{{ $dep->uuid }}"
                                                        style="font-weight: bold; text-decoration: underline;">
                                                        Result
                                                    </a>

                                                    <div class="modal fade"
                                                        id="organoleptikSearchModal{{ $dep->uuid }}"
                                                        tabindex="-1"
                                                        aria-labelledby="organoleptikSearchModalLabel{{ $dep->uuid }}"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog" style="max-width: 70%;">

                                                            <div class="modal-content">

                                                                <div class="modal-header bg-info text-white">

                                                                    <h5 class="modal-title text-start"
                                                                        id="organoleptikSearchModalLabel{{ $dep->uuid }}">
                                                                        Detail Pemeriksaan Organoleptik
                                                                    </h5>

                                                                    <button type="button"
                                                                        class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close">
                                                                    </button>

                                                                </div>

                                                                <div class="modal-body">

                                                                    <div class="table-responsive">

                                                                        <table class="table table-bordered table-striped table-sm text-center align-middle">

                                                                            <thead class="table-light">

                                                                                <tr>
                                                                                    <th>No</th>
                                                                                    <th>Kode Batch</th>
                                                                                    <th>Penampilan</th>
                                                                                    <th>Aroma</th>
                                                                                    <th>Kekenyalan</th>
                                                                                    <th>Rasa Asin</th>
                                                                                    <th>Rasa Gurih</th>
                                                                                    <th>Rasa Manis</th>
                                                                                    <th>Rasa Ayam/BBQ/Ikan</th>
                                                                                    <th>Rasa Keseluruhan</th>
                                                                                    <th>Hasil Score</th>
                                                                                    <th>Keterangan</th>
                                                                                </tr>

                                                                            </thead>

                                                                            <tbody>

                                                                                @foreach ($sensori as $index => $item)

                                                                                    @php
                                                                                        $kodeProduksi = $item['kode_produksi'] ?? null;

                                                                                        $kodeBatch = $kodeProduksi
                                                                                            ? (\App\Models\Mincing::where('uuid', $kodeProduksi)->value('kode_produksi')
                                                                                                ?? \App\Models\Mincing::where('id', $kodeProduksi)->value('kode_produksi')
                                                                                                ?? $kodeProduksi)
                                                                                            : '-';

                                                                                        $release = $item['release'] ?? '-';
                                                                                    @endphp

                                                                                    <tr>

                                                                                        <td>
                                                                                            {{ $index + 1 }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $kodeBatch }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $item['penampilan'] ?? '-' }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $item['aroma'] ?? '-' }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $item['kekenyalan'] ?? '-' }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $item['rasa_asin'] ?? '-' }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $item['rasa_gurih'] ?? '-' }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $item['rasa_manis'] ?? '-' }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $item['rasa_daging'] ?? '-' }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $item['rasa_keseluruhan'] ?? '-' }}
                                                                                        </td>

                                                                                        <td>
                                                                                            {{ $item['rata_score'] ?? '-' }}
                                                                                        </td>

                                                                                        <td>

                                                                                            @if ($release === 'Release')

                                                                                                <span class="fw-bold text-success">
                                                                                                    {{ $release }}
                                                                                                </span>

                                                                                            @elseif ($release === 'Tidak Release')

                                                                                                <span class="fw-bold text-danger">
                                                                                                    {{ $release }}
                                                                                                </span>

                                                                                            @else

                                                                                                {{ $release }}

                                                                                            @endif

                                                                                        </td>

                                                                                    </tr>

                                                                                @endforeach

                                                                            </tbody>

                                                                        </table>

                                                                    </div>

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

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                            <td class="text-center align-middle">
                                                {{ \App\Models\User::where('username', $dep->username)->value('name') ?? $dep->username ?? '-' }}
                                            </td>

                                            <td class="text-center align-middle">

                                                @if ($dep->status_spv == 0)

                                                    <span class="fw-bold text-secondary">
                                                        Created
                                                    </span>

                                                @elseif ($dep->status_spv == 1)

                                                    <span class="fw-bold text-success">
                                                        Verified
                                                    </span>

                                                @elseif ($dep->status_spv == 2)

                                                    <span class="fw-bold text-danger">
                                                        Revision
                                                    </span>

                                                @else

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="6" class="text-center">
                                                Belum ada data organoleptik.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @break

                @default

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

                                                    @elseif (is_array($value))

                                                        <div class="bg-light border rounded p-2 small"
                                                            style="max-height:180px; overflow:auto;">

                                                            @foreach ($value as $arrayKey => $arrayValue)

                                                                @if (is_array($arrayValue))

                                                                    <div class="mb-2">

                                                                        <strong>
                                                                            {{ is_string($arrayKey)
                                                                                ? ucwords(str_replace('_', ' ', $arrayKey))
                                                                                : 'Data' }}
                                                                        </strong>

                                                                        <ul class="mb-1 ps-3">

                                                                            @foreach ($arrayValue as $subKey => $subValue)

                                                                                @if (is_array($subValue))

                                                                                    <li>
                                                                                        {{ $subKey }}:
                                                                                        {{ json_encode($subValue, JSON_UNESCAPED_UNICODE) }}
                                                                                    </li>

                                                                                @else

                                                                                    <li>
                                                                                        {{ $subKey }}:
                                                                                        {{ $subValue ?? '-' }}
                                                                                    </li>

                                                                                @endif

                                                                            @endforeach

                                                                        </ul>

                                                                    </div>

                                                                @else

                                                                    <div>
                                                                        <strong>
                                                                            {{ is_string($arrayKey)
                                                                                ? ucwords(str_replace('_', ' ', $arrayKey)) . ':'
                                                                                : '' }}
                                                                        </strong>

                                                                        {{ $arrayValue ?? '-' }}
                                                                    </div>

                                                                @endif

                                                            @endforeach

                                                        </div>

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

                @break

        @endswitch

    @endforeach

</div>