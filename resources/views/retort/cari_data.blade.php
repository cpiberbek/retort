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
                                                {{ $dep->username ?? '-' }}
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