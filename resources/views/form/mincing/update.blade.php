@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">

        <div class="card shadow-lg border-0">
            <div class="card-body">
                <h4 class="mb-4 fw-bold text-primary">
                    <i class="bi bi-pencil-square me-2"></i>
                    Update Pemeriksaan Mincing - Emulsifying - Aging
                </h4>

                <form id="mincingForm" action="{{ route('mincing.update_qc', $mincing->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- ===================== IDENTIFIKASI ===================== --}}
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-primary text-white fw-bold">IDENTIFIKASI</div>
                        <div class="card-body bg-light">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal</label>
                                    <input type="date" name="date" id="dateInput"
                                        value="{{ old('date', $mincing->date) }}" class="form-control" required
                                        {{ $mincing->date ? 'readonly' : '' }}>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Shift</label>
                                    {{-- Catatan: attribute readonly pada select HTML butuh tambahan style pointer-events:
                                none --}}
                                    <select name="shift" id="shiftInput" class="form-control" required
                                        {{ $mincing->shift ? 'readonly tabindex=-1 style=pointer-events:none;' : '' }}>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="1" {{ old('shift', $mincing->shift) == '1' ? 'selected' : '' }}>
                                            Shift
                                            1</option>
                                        <option value="2" {{ old('shift', $mincing->shift) == '2' ? 'selected' : '' }}>
                                            Shift
                                            2</option>
                                        <option value="3" {{ old('shift', $mincing->shift) == '3' ? 'selected' : '' }}>
                                            Shift
                                            3</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Varian</label>
                                    @if ($mincing->nama_produk)
                                        <input type="text" class="form-control" value="{{ $mincing->nama_produk }}"
                                            readonly>
                                        <input type="hidden" name="nama_produk" value="{{ $mincing->nama_produk }}">
                                    @else
                                        <select name="nama_produk" class="form-control selectpicker" data-live-search="true"
                                            required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach ($produks as $produk)
                                                <option value="{{ $produk->nama_produk }}"
                                                    {{ old('nama_produk', $mincing->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                                    {{ $produk->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kode Batch</label>
                                    <input type="text" name="kode_produksi" id="kode_produksi"
                                        class="form-control text-uppercase" maxlength="10"
                                        value="{{ old('kode_produksi', $mincing->kode_produksi) }}" required
                                        {{ $mincing->kode_produksi ? 'readonly' : '' }}>
                                    <small id="kodeError" class="text-danger d-none"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== PEMERIKSAAN / PERSIAPAN ===================== --}}
                    <div class="card mb-4 border-0 shadow-sm">
                        <div
                            class="card-header bg-info text-white fw-bold d-flex justify-content-between align-items-center">
                            <span>PEMERIKSAAN</span>
                        </div>

                        <div class="card-body bg-light">
                            {{-- Preparation --}}
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="4" class="text-start">Preparation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start fw-semibold">Waktu Mulai</td>
                                            <td><input type="time" name="waktu_mulai"
                                                class="form-control form-control-sm text-center"
                                                value="{{ old('waktu_mulai', $mincing->waktu_mulai) }}">
                                            <td class="fw-bold">s/d</td>
                                            <td><input type="time" name="waktu_selesai"
                                                class="form-control form-control-sm text-center"
                                                value="{{ old('waktu_selesai', $mincing->waktu_selesai) }}">
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- ===================== NON-PREMIX ===================== --}}
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered text-center align-middle" id="tabelNonPremix">
                                    <thead class="table-primary">
                                        <tr>
                                            <th colspan="7" class="text-start">Bahan Baku dan Bahan Tambahan (Non-Premix)
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Bahan</th>
                                            <th>Kode</th>
                                            <th>(°C)</th>
                                            <th>*pH</th>
                                            <th>Kg</th>
                                            <th>Sens</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyNonPremix">
                                        @php
                                            $nonPremix =
                                                $nonPremixData ??
                                                (is_array($mincing->non_premix)
                                                    ? $mincing->non_premix
                                                    : json_decode($mincing->non_premix ?? '[]', true));
                                        @endphp

                                        @if (!empty($nonPremix) && is_array($nonPremix))
                                            @foreach ($nonPremix as $i => $np)
                                                <tr>
                                                    <td>
                                                        <select name="non_premix[{{ $i }}][nama_bahan]"
                                                            class="form-control form-select-sm text-center" required
                                                            {{ !empty($np['nama_bahan']) ? 'readonly tabindex=-1 style=pointer-events:none;' : '' }}>
                                                            <option value="" disabled>-- Pilih Bahan --</option>
                                                            @foreach ($rawMaterials as $rm)
                                                                <option value="{{ $rm->nama_bahan_baku }}"
                                                                    {{ old("non_premix.$i.nama_bahan", $np['nama_bahan'] ?? '') == $rm->nama_bahan_baku ? 'selected' : '' }}>
                                                                    {{ $rm->nama_bahan_baku }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="non_premix[{{ $i }}][inspection_uuid]"
                                                            class="form-control form-select-sm text-center kode-batch-select"
                                                            {{ !empty($np['inspection_uuid']) ? 'disabled' : '' }}>

                                                            <option value="">-- Pilih Batch --</option>

                                                            @foreach ($inspections as $insp)
                                                                @if ($insp->inspection)
                                                                    <option value="{{ $insp->uuid }}"
                                                                        data-bahan="{{ $insp->inspection->bahan_baku }}"
                                                                        {{ old("non_premix.$i.inspection_uuid", $np['inspection_uuid'] ?? '') == $insp->uuid ? 'selected' : '' }}>
                                                                        {{ $insp->kode_batch }}
                                                                    </option>
                                                                @endif
                                                            @endforeach

                                                        </select>

                                                        @if (!empty($np['inspection_uuid']))
                                                            <input type="hidden"
                                                                name="non_premix[{{ $i }}][inspection_uuid]"
                                                                value="{{ $np['inspection_uuid'] }}">
                                                        @endif
                                                    </td>

                                                    <td><div class="input-group input-group-sm">
                                                        <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1"
                                                            {{ !empty($np['suhu_bahan']) ? 'disabled' : '' }}>±</button>
                                                        <input type="text" inputmode="decimal"
                                                            name="non_premix[{{ $i }}][suhu_bahan]"
                                                            value="{{ old(" non_premix.$i.suhu_bahan", $np['suhu_bahan'] ?? '') }}"
                                                            class="form-control form-control-sm text-center suhu-number-input"
                                                           
                                                            {{ !empty($np['suhu_bahan']) ? 'readonly' : '' }}>
                                                    </div></td>
                                                    <td><input type="number"
                                                            name="non_premix[{{ $i }}][ph_bahan]" step="0.01"
                                                            value="{{ old(" non_premix.$i.ph_bahan", $np['ph_bahan'] ?? '') }}"
                                                            class="form-control form-control-sm text-center"
                                                            {{ !empty($np['ph_bahan']) ? 'readonly' : '' }}></td>
                                                    <td><input type="number"
                                                            name="non_premix[{{ $i }}][berat_bahan]"
                                                            step="0.01"
                                                            value="{{ old(" non_premix.$i.berat_bahan", $np['berat_bahan'] ?? '') }}"
                                                            class="form-control form-control-sm text-center"
                                                            {{ !empty($np['berat_bahan']) ? 'readonly' : '' }}></td>
                                                    <td class="text-center">
                                                        <input type="checkbox"
                                                            name="non_premix[{{ $i }}][sensori]"
                                                            value="Oke"
                                                            {{ old("non_premix.$i.sensori", $np['sensori'] ?? '') == 'Oke' ? 'checked' : '' }}
                                                            {{ !empty($np['sensori']) ? 'onclick=return false;' : '' }}
                                                            class="form-check-input">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm hapusBaris"
                                                            {{ !empty($np['nama_bahan']) ? 'disabled' : '' }}><i
                                                                class="bi bi-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            {{-- Default row --}}
                                            <tr>
                                                <td>
                                                    <select name="non_premix[0][nama_bahan]"
                                                        class="form-control form-select-sm text-center" required>
                                                        <option value="" selected disabled>-- Pilih Bahan --</option>
                                                        @foreach ($rawMaterials as $rm)
                                                            <option value="{{ $rm->nama_bahan_baku }}">
                                                                {{ $rm->nama_bahan_baku }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="non_premix[0][inspection_uuid]"
                                                        class="form-control form-select-sm text-center kode-batch-select">

                                                        <option value="" selected disabled>
                                                            -- Pilih Batch --
                                                        </option>

                                                        @foreach ($inspections as $insp)
                                                            @if ($insp->inspection)
                                                                <option value="{{ $insp->uuid }}"
                                                                    data-bahan="{{ $insp->inspection->bahan_baku }}">
                                                                    {{ $insp->kode_batch }}
                                                                </option>
                                                            @endif
                                                        @endforeach

                                                    </select>
                                                </td>
                                                <td><div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                                                    <input type="text" inputmode="decimal" name="non_premix[0][suhu_bahan]"
                                                        class="form-control form-control-sm text-center suhu-number-input">
                                                </div></td>
                                                <td><input type="number" name="non_premix[0][ph_bahan]" step="0.01"
                                                        class="form-control form-control-sm text-center"></td>
                                                <td><input type="number" name="non_premix[0][berat_bahan]"
                                                        step="0.01" class="form-control form-control-sm text-center">
                                                </td>
                                                <td class="text-center"><input type="checkbox"
                                                        name="non_premix[0][sensori]" value="Oke"
                                                        class="form-check-input"></td>
                                                <td><button type="button" class="btn btn-danger btn-sm hapusBaris"><i
                                                            class="bi bi-trash"></i></button></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>

                                <button type="button" class="btn btn-success btn-sm" id="tambahBarisNonPremix">
                                    <i class="bi bi-plus-circle"></i> Tambah Bahan
                                </button>
                            </div>

                            {{-- ===================== PREMIX ===================== --}}
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered text-center align-middle" id="tabelPremix">
                                    <thead class="table-primary">
                                        <tr>
                                            <th colspan="5" class="text-start">Premix</th>
                                        </tr>
                                        <tr>
                                            <th>Premix</th>
                                            <th>Kode</th>
                                            <th>Kg</th>
                                            <th>Sens</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyPremix">
                                        @php
                                            $premix =
                                                $premixData ??
                                                (is_array($mincing->premix)
                                                    ? $mincing->premix
                                                    : json_decode($mincing->premix ?? '[]', true));
                                        @endphp

                                        @if (!empty($premix) && is_array($premix))
                                            @foreach ($premix as $i => $px)
                                                <tr>
                                                    <td><input type="text"
                                                            name="premix[{{ $i }}][nama_premix]"
                                                            value="{{ old(
                                                                "
                                                                                                                                                                                                                                                                                                premix.$i.nama_premix",
                                                                $px['nama_premix'] ?? '',
                                                            ) }}"
                                                            class="form-control form-control-sm text-center"
                                                            {{ !empty($px['nama_premix']) ? 'readonly' : '' }}></td>
                                                    <td><input type="text"
                                                            name="premix[{{ $i }}][kode_premix]"
                                                            value="{{ old(
                                                                "
                                                                                                                                                                                                                                                                                                premix.$i.kode_premix",
                                                                $px['kode_premix'] ?? '',
                                                            ) }}"
                                                            class="form-control form-control-sm text-center"
                                                            {{ !empty($px['kode_premix']) ? 'readonly' : '' }}></td>
                                                    <td><input type="number"
                                                            name="premix[{{ $i }}][berat_premix]"
                                                            step="0.01"
                                                            value="{{ old(" premix.$i.berat_premix", $px['berat_premix'] ?? '') }}"
                                                            class="form-control form-control-sm text-center"
                                                            {{ !empty($px['berat_premix']) ? 'readonly' : '' }}></td>
                                                    <td class="text-center"><input type="checkbox"
                                                            name="premix[{{ $i }}][sensori_premix]"
                                                            value="Oke"
                                                            {{ old("premix.$i.sensori_premix", $px['sensori_premix'] ?? '') == 'Oke' ? 'checked' : '' }}
                                                            {{ !empty($px['sensori_premix']) ? 'onclick=return false;' : '' }}
                                                            class="form-check-input"></td>
                                                    <td><button type="button"
                                                            class="btn btn-danger btn-sm hapusBarisPremix"
                                                            {{ !empty($px['nama_premix']) ? 'disabled' : '' }}><i
                                                                class="bi bi-trash"></i></button></td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td><input type="text" name="premix[0][nama_premix]"
                                                        class="form-control form-control-sm text-center"></td>
                                                <td><input type="text" name="premix[0][kode_premix]"
                                                        class="form-control form-control-sm text-center"></td>
                                                <td><input type="number" name="premix[0][berat_premix]" step="0.01"
                                                        class="form-control form-control-sm text-center"></td>
                                                <td class="text-center"><input type="checkbox"
                                                        name="premix[0][sensori_premix]" value="Oke"
                                                        class="form-check-input"></td>
                                                <td><button type="button"
                                                        class="btn btn-danger btn-sm hapusBarisPremix"><i
                                                            class="bi bi-trash"></i></button></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>

                                <button type="button" class="btn btn-success btn-sm" id="tambahBarisPremix">
                                    <i class="bi bi-plus-circle"></i> Tambah Premix
                                </button>
                            </div>

                            {{-- ===================== PROSES MIXING, GEL, EMULSI ===================== --}}
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-middle mb-0">
                                    <tbody>
                                        {{-- BARIS SUHU SEBELUM GRINDING --}}
                                        <tr>
                                            <td class="text-start fw-semibold bg-light" style="width: 25%;">Suhu (Sebelum
                                                Grinding)</td>
                                            <td colspan="3" class="p-0">
                                                <table class="table table-borderless mb-0">
                                                    <tbody id="tbodySuhuGrinding">
                                                        @php
                                                            $rawSuhu = $mincing->suhu_sebelum_grinding ?? '[]';
                                                            $suhuDataLocal = is_string($rawSuhu)
                                                                ? json_decode($rawSuhu, true)
                                                                : $rawSuhu;
                                                            if (!is_array($suhuDataLocal)) {
                                                                $suhuDataLocal = [];
                                                            }
                                                        @endphp

                                                        @forelse($suhuDataLocal as $key => $item)
                                                            <tr>
                                                                <td style="width: 45%;">
                                                                    <select
                                                                        name="suhu_grinding_input[{{ $key }}][daging]"
                                                                        class="form-control form-select-sm"
                                                                        {{ !empty($item['daging']) ? 'readonly tabindex=-1 style=pointer-events:none;' : '' }}>
                                                                        <option value="" selected disabled>Pilih
                                                                            Daging</option>
                                                                        <option value="BEEF"
                                                                            {{ ($item['daging'] ?? '') == 'BEEF' ? 'selected' : '' }}>
                                                                            BEEF</option>
                                                                        <option value="SBB"
                                                                            {{ ($item['daging'] ?? '') == 'SBB' ? 'selected' : '' }}>
                                                                            SBB</option>
                                                                        <option value="SBL"
                                                                            {{ ($item['daging'] ?? '') == 'SBL' ? 'selected' : '' }}>
                                                                            SBL</option>
                                                                        <option value="MDM"
                                                                            {{ ($item['daging'] ?? '') == 'MDM' ? 'selected' : '' }}>
                                                                            MDM</option>
                                                                        <option value="CCM"
                                                                            {{ ($item['daging'] ?? '') == 'CCM' ? 'selected' : '' }}>
                                                                            CCM</option>
                                                                        <option value="SURIMI"
                                                                            {{ ($item['daging'] ?? '') == 'SURIMI' ? 'selected' : '' }}>
                                                                            SURIMI</option>
                                                                    </select>
                                                                </td>
                                                                <td style="width: 45%;">
                                                                    <div class="input-group input-group-sm">
                                                                        <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1"
                                                                            {{ !empty($item['suhu']) ? 'disabled' : '' }}>±</button>
                                                                        <input type="text" inputmode="decimal"
                                                                            name="suhu_grinding_input[{{ $key }}][suhu]"
                                                                            value="{{ $item['suhu'] ?? '' }}"
                                                                            class="form-control form-control-sm text-center suhu-number-input"
                                                                           
                                                                            {{ !empty($item['suhu']) ? 'readonly' : '' }}>
                                                                    </div>
                                                                </td>
                                                                <td style="width: 10%;">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger hapusBarisSuhu"
                                                                        {{ !empty($item['daging']) ? 'disabled' : '' }}><i
                                                                            class="bi bi-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td style="width: 45%;">
                                                                    <select name="suhu_grinding_input[0][daging]"
                                                                        class="form-control form-select-sm">
                                                                        <option value="" selected disabled>Pilih
                                                                            Daging</option>
                                                                        <option value="BEEF">BEEF</option>
                                                                        <option value="SBB">SBB</option>
                                                                        <option value="SBL">SBL</option>
                                                                        <option value="MDM">MDM</option>
                                                                        <option value="CCM">CCM</option>
                                                                        <option value="SURIMI">SURIMI</option>
                                                                    </select>
                                                                </td>
                                                                <td style="width: 45%;">
                                                                    <div class="input-group input-group-sm">
                                                                        <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                                                                        <input type="text" inputmode="decimal"
                                                                            name="suhu_grinding_input[0][suhu]"
                                                                            class="form-control form-control-sm text-center suhu-number-input"
                                                                           >
                                                                    </div>
                                                                </td>
                                                                <td style="width: 10%;">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger hapusBarisSuhu"><i
                                                                            class="bi bi-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                                <div class="p-2 border-top bg-white">
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        id="tambahBarisSuhu">
                                                        <i class="bi bi-plus-circle"></i> Tambah Daging
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- BARIS WAKTU MIXING PREMIX --}}
                                        <tr>
                                            <td class="text-start fw-semibold bg-light" style="width: 25%;">Waktu Mixing Premix</td>
                                            <td colspan="3">
                                                <div class="d-flex align-items-center gap-2">

                                                    <input type="time"
                                                        id="premix_start"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('waktu_mixing_premix_start', $mincing->waktu_mixing_premix_start) }}"
                                                        disabled>

                                                    <span>-</span>

                                                    <input type="time"
                                                        id="premix_end"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('waktu_mixing_premix_end', $mincing->waktu_mixing_premix_end) }}"
                                                        disabled>

                                                    <span id="premix_result" class="badge bg-light text-dark">
                                                        ({{ old('waktu_mixing_premix', $mincing->waktu_mixing_premix ?? 0) }}) Menit
                                                    </span>

                                                </div>

                                                <input type="hidden"
                                                    name="waktu_mixing_premix"
                                                    id="premix_menit"
                                                    value="{{ old('waktu_mixing_premix', $mincing->waktu_mixing_premix) }}">

                                                <input type="hidden"
                                                    name="waktu_mixing_premix_start"
                                                    id="premix_start_hidden"
                                                    value="{{ old('waktu_mixing_premix_start', $mincing->waktu_mixing_premix_start) }}">

                                                <input type="hidden"
                                                    name="waktu_mixing_premix_end"
                                                    id="premix_end_hidden"
                                                    value="{{ old('waktu_mixing_premix_end', $mincing->waktu_mixing_premix_end) }}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- GEL --}}
                                <table class="table table-bordered text-center align-middle mb-4">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="4" class="text-start">GEL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start fw-semibold" style="width: 25%;">Waktu Bowl Cutter</td>
                                            <td colspan="3">
                                                <div class="d-flex align-items-center gap-2">

                                                    <input type="time"
                                                        id="bowl_start"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('waktu_bowl_cutter_start', $mincing->waktu_bowl_cutter_start) }}"
                                                        disabled>

                                                    <span>-</span>

                                                    <input type="time"
                                                        id="bowl_end"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('waktu_bowl_cutter_end', $mincing->waktu_bowl_cutter_end) }}"
                                                        disabled>

                                                    <span id="bowl_result" class="badge bg-light text-dark">
                                                        ({{ old('waktu_bowl_cutter', $mincing->waktu_bowl_cutter ?? 0) }}) Menit
                                                    </span>

                                                </div>

                                                <input type="hidden"
                                                    name="waktu_bowl_cutter"
                                                    id="bowl_menit"
                                                    value="{{ old('waktu_bowl_cutter', $mincing->waktu_bowl_cutter) }}">

                                                <input type="hidden"
                                                    name="waktu_bowl_cutter_start"
                                                    id="bowl_start_hidden"
                                                    value="{{ old('waktu_bowl_cutter_start', $mincing->waktu_bowl_cutter_start) }}">

                                                <input type="hidden"
                                                    name="waktu_bowl_cutter_end"
                                                    id="bowl_end_hidden"
                                                    value="{{ old('waktu_bowl_cutter_end', $mincing->waktu_bowl_cutter_end) }}">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-start fw-semibold">Waktu Aging Emulsi (Menit)</td>
                                            <td><input type="time" name="waktu_aging_emulsi_awal"
                                                    class="form-control form-control-sm text-center"
                                                    value="{{ old('waktu_aging_emulsi_awal', $mincing->waktu_aging_emulsi_awal) }}"
                                                    {{ $mincing->waktu_aging_emulsi_awal ? 'readonly' : '' }}></td>
                                            <td class="fw-bold" style="width: 5%;">s/d</td>
                                            <td><input type="time" name="waktu_aging_emulsi_akhir"
                                                    class="form-control form-control-sm text-center"
                                                    value="{{ old('waktu_aging_emulsi_akhir', $mincing->waktu_aging_emulsi_akhir) }}"
                                                    {{ $mincing->waktu_aging_emulsi_akhir ? 'readonly' : '' }}></td>
                                        </tr>

                                        <tr>
                                            <td class="text-start fw-semibold">Suhu Akhir Emulsi Gel (Std &lt;5°C)</td>
                                            <td colspan="3">
                                                <div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1"
                                                        {{ $mincing->suhu_akhir_emulsi_gel ? 'disabled' : '' }}>±</button>
                                                    <input type="text" inputmode="decimal" name="suhu_akhir_emulsi_gel"
                                                        class="form-control form-control-sm text-center suhu-number-input"
                                                        value="{{ rtrim(rtrim(old('suhu_akhir_emulsi_gel', $mincing->suhu_akhir_emulsi_gel), '0'), '.') }}"
                                                        {{ $mincing->suhu_akhir_emulsi_gel ? 'readonly' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- Waktu Mixing & Emulsifying --}}
                                <table class="table table-bordered text-center align-middle">
                                    <tbody>
                                        <tr>
                                            <td class="text-start fw-semibold" style="width: 25%;">Waktu Mixing</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="time" id="mixing_start"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('waktu_mixing_start', $mincing->waktu_mixing_start) }}"
                                                        disabled>

                                                    <span>-</span>

                                                    <input type="time" id="mixing_end"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('waktu_mixing_end', $mincing->waktu_mixing_end) }}"
                                                        disabled>

                                                    <span id="mixing_result" class="badge bg-light text-dark">
                                                        ({{ old('waktu_mixing', $mincing->waktu_mixing ?? 0) }}) Menit
                                                    </span>
                                                </div>

                                                <input type="hidden" name="waktu_mixing" id="mixing_menit"
                                                    value="{{ old('waktu_mixing', $mincing->waktu_mixing) }}">

                                                <input type="hidden" name="waktu_mixing_start" id="mixing_start_hidden"
                                                    value="{{ old('waktu_mixing_start', $mincing->waktu_mixing_start) }}">

                                                <input type="hidden" name="waktu_mixing_end" id="mixing_end_hidden"
                                                    value="{{ old('waktu_mixing_end', $mincing->waktu_mixing_end) }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start fw-semibold">Suhu Akhir Mixing (Std 2–5°C)</td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1"
                                                        {{ $mincing->suhu_akhir_mixing ? 'disabled' : '' }}>±</button>

                                                    <input type="text" inputmode="decimal" name="suhu_akhir_mixing"
                                                        class="form-control form-control-sm text-center suhu-number-input"
                                                        value="{{ rtrim(rtrim(old('suhu_akhir_mixing', $mincing->suhu_akhir_mixing), '0'), '.') }}"
                                                        {{ $mincing->suhu_akhir_mixing ? 'readonly' : '' }}>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-start fw-semibold">Suhu Akhir Emulsifying (Std 14±2°C)</td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1"
                                                        {{ $mincing->suhu_akhir_emulsi ? 'disabled' : '' }}>±</button>

                                                    <input type="text" inputmode="decimal" name="suhu_akhir_emulsi"
                                                        class="form-control form-control-sm text-center suhu-number-input"
                                                        value="{{ rtrim(rtrim(old('suhu_akhir_emulsi', $mincing->suhu_akhir_emulsi), '0'), '.') }}"
                                                        {{ $mincing->suhu_akhir_emulsi ? 'readonly' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white fw-bold">Catatan</div>
                        <div class="card-body bg-light">
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"
                                {{ $mincing->catatan ? 'readonly' : '' }}>{{ old('catatan', $mincing->catatan) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success px-4"><i class="bi bi-save"></i> Update</button>
                        <a href="{{ route('mincing.index') }}" class="btn btn-secondary px-4"><i
                                class="bi bi-arrow-left"></i> Batal</a>
                    </div>
                </form>
                <hr>
                <div id="resultArea"></div>
            </div>
        </div>
    </div>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('dateInput');
            const shiftInput = document.getElementById('shiftInput');
            if (!dateInput.value) {
                const now = new Date();
                dateInput.value = now.toISOString().split('T')[0];
            }
            if (!shiftInput.value) {
                const hh = new Date().getHours();
                if (hh >= 7 && hh < 15) shiftInput.value = "1";
                else if (hh >= 15 && hh < 23) shiftInput.value = "2";
                else shiftInput.value = "3";
            }
        });

        $(function() {
            const kodeInput = $('#kode_produksi');
            const kodeError = $('#kodeError');
            const form = $('#mincingForm');

            function validateKode() {
                let value = kodeInput.val().toUpperCase().replace(/\s+/g, '');
                kodeInput.val(value);
                kodeError.text('').addClass('d-none');
                if (value.length !== 10) return false;
                if (!/^[A-Z0-9]+$/.test(value)) return false;
                return true;
            }
            kodeInput.on('input', validateKode);
            form.on('submit', function(e) {
                if (!validateKode()) {
                    e.preventDefault();
                    alert('Kode Batch tidak valid! Periksa kembali.');
                    kodeInput.focus();
                }
            });
        });

        // SCRIPT TAMBAH BARIS DINAMIS
        document.addEventListener('DOMContentLoaded', function() {
            const tbodyNon = document.getElementById('tbodyNonPremix');
            const tbodyPremix = document.getElementById('tbodyPremix');
            const tbodySuhu = document.getElementById('tbodySuhuGrinding');

            let indexNon = tbodyNon ? tbodyNon.querySelectorAll('tr').length : 0;
            let indexPremix = tbodyPremix ? tbodyPremix.querySelectorAll('tr').length : 0;
            let indexSuhu = tbodySuhu ? tbodySuhu.querySelectorAll('tr').length : 0;

            // 1. Tambah Non-Premix
            if (document.getElementById('tambahBarisNonPremix')) {
                document.getElementById('tambahBarisNonPremix').addEventListener('click', function() {

                    let optionBahan = `<option value="" selected disabled>-- Pilih Bahan --</option>`;

                    @foreach ($rawMaterials as $rm)
                        optionBahan += `<option value="{{ $rm->nama_bahan_baku }}">
                {{ $rm->nama_bahan_baku }}
            </option>`;
                    @endforeach

                    let optionBatch = `<option value="" disabled selected>-- Pilih Batch --</option>`;

                    @foreach ($inspections as $insp)
                        @if ($insp->inspection)
                            optionBatch += `
                    <option
                        value="{{ $insp->uuid }}"
                        data-bahan="{{ $insp->inspection->bahan_baku }}">
                        {{ $insp->kode_batch }}
                    </option>
                `;
                        @endif
                    @endforeach

                    const row = `
        <tr>
            <td>
                <select
                    name="non_premix[${indexNon}][nama_bahan]"
                    class="form-control form-select-sm text-center nama-bahan-select"
                    required>

                    ${optionBahan}

                </select>
            </td>

            <td>
                <select
                    name="non_premix[${indexNon}][inspection_uuid]"
                    class="form-control form-select-sm text-center kode-batch-select">

                    ${optionBatch}

                </select>
            </td>

            <td>
                <div class="input-group input-group-sm">
                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                    <input type="text" inputmode="decimal"
                        name="non_premix[${indexNon}][suhu_bahan]"
                        class="form-control form-control-sm text-center suhu-number-input">
                </div>
            </td>

            <td>
                <input type="number"
                    name="non_premix[${indexNon}][ph_bahan]"
                    step="0.01"
                    class="form-control form-control-sm text-center">
            </td>

            <td>
                <input type="number"
                    name="non_premix[${indexNon}][berat_bahan]"
                    step="0.01"
                    class="form-control form-control-sm text-center">
            </td>

            <td class="text-center">
                <input type="checkbox"
                    name="non_premix[${indexNon}][sensori]"
                    value="Oke"
                    class="form-check-input">
            </td>

            <td>
                <button type="button"
                    class="btn btn-danger btn-sm hapusBaris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`;

                    tbodyNon.insertAdjacentHTML('beforeend', row);

                    indexNon++;
                });
            }

            // Filter batch berdasarkan bahan yang dipilih
            document.addEventListener('change', function(e) {

                if (e.target.classList.contains('nama-bahan-select')) {

                    const selectedBahan = e.target.value;

                    const row = e.target.closest('tr');

                    const batchSelect = row.querySelector('.kode-batch-select');

                    if (!batchSelect) return;

                    batchSelect.value = '';

                    [...batchSelect.options].forEach(option => {

                        if (!option.value) {
                            option.hidden = false;
                            return;
                        }

                        option.hidden = option.dataset.bahan !== selectedBahan;
                    });
                }

            });

            // 2. Tambah Premix
            if (document.getElementById('tambahBarisPremix')) {
                document.getElementById('tambahBarisPremix').addEventListener('click', function() {
                    const row = `<tr>
                    <td><input type="text" name="premix[${indexPremix}][nama_premix]" class="form-control form-control-sm text-center"></td>
                    <td><input type="text" name="premix[${indexPremix}][kode_premix]" class="form-control form-control-sm text-center"></td>
                    <td><input type="number" name="premix[${indexPremix}][berat_premix]" step="0.01" class="form-control form-control-sm text-center"></td>
                    <td class="text-center"><input type="checkbox" name="premix[${indexPremix}][sensori_premix]" value="Oke" class="form-check-input"></td>
                    <td><button type="button" class="btn btn-danger btn-sm hapusBarisPremix"><i class="bi bi-trash"></i></button></td>
                </tr>`;
                    tbodyPremix.insertAdjacentHTML('beforeend', row);
                    indexPremix++;
                });
            }

            // 3. Tambah Suhu
            if (document.getElementById('tambahBarisSuhu')) {
                document.getElementById('tambahBarisSuhu').addEventListener('click', function() {
                    const row = `<tr>
                    <td style="width: 45%;">
                        <select name="suhu_grinding_input[${indexSuhu}][daging]" class="form-control form-select-sm">
                            <option value="" selected disabled>Pilih Daging</option>
                            <option value="BEEF">BEEF</option>
                            <option value="SBB">SBB</option>
                            <option value="SBL">SBL</option>
                            <option value="MDM">MDM</option>
                            <option value="CCM">CCM</option>
                            <option value="SURIMI">SURIMI</option>
                        </select>
                    </td>
                    <td style="width: 45%;"><div class="input-group input-group-sm">
                        <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                        <input type="text" inputmode="decimal" name="suhu_grinding_input[${indexSuhu}][suhu]" class="form-control form-control-sm text-center suhu-number-input">
                    </div></td>
                    <td style="width: 10%;"><button type="button" class="btn btn-sm btn-danger hapusBarisSuhu"><i class="bi bi-trash"></i></button></td>
                </tr>`;
                    tbodySuhu.insertAdjacentHTML('beforeend', row);
                    indexSuhu++;
                });
            }

            // Event delegation untuk hapus baris dinamis
            document.addEventListener('click', function(e) {
                if (e.target.closest('.hapusBaris')) e.target.closest('tr').remove();
                if (e.target.closest('.hapusBarisPremix')) e.target.closest('tr').remove();
                if (e.target.closest('.hapusBarisSuhu')) {
                    if (tbodySuhu.querySelectorAll('tr').length > 1) {
                        e.target.closest('tr').remove();
                    } else {
                        alert('Minimal harus ada satu baris suhu.');
                    }
                }
            });
        });
    </script>

    <script>
        // --- Script Input Suhu (mendukung nilai minus di HP) ---
        document.addEventListener('input', function(e) {
            if (!e.target.classList.contains('suhu-number-input')) return;
            let val = e.target.value;
            val = val.replace(/[^0-9.,-]/g, '');
            val = val.replace(',', '.');
            if (val.indexOf('-') > 0) {
                val = val.replace(/-/g, '');
                val = '-' + val;
            }
            const parts = val.split('.');
            if (parts.length > 2) {
                val = parts[0] + '.' + parts.slice(1).join('');
            }
            e.target.value = val;
        });

        // --- Tombol ± Toggle Minus ---
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-toggle-minus');
            if (!btn) return;
            const input = btn.closest('.input-group').querySelector('input');
            if (!input) return;
            input.value = input.value.startsWith('-')
                ? input.value.slice(1)
                : '-' + input.value;
            input.dispatchEvent(new Event('input'));
            input.focus();
        });
    </script>

    {{-- JS WAKTU MIXING PREMIX --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const start = document.getElementById("premix_start");
            const end = document.getElementById("premix_end");
            const result = document.getElementById("premix_result");
            const menit = document.getElementById("premix_menit");
            const startHidden = document.getElementById("premix_start_hidden");
            const endHidden = document.getElementById("premix_end_hidden");

            if (!start || !end) return;

            function hitungPremix() {
                startHidden.value = start.value;
                endHidden.value = end.value;

                if (!start.value || !end.value) {
                    result.textContent = "(0) Menit";
                    menit.value = "";
                    return;
                }

                let [sh, sm] = start.value.split(":").map(Number);
                let [eh, em] = end.value.split(":").map(Number);

                let mulai = new Date();
                mulai.setHours(sh, sm, 0, 0);

                let selesai = new Date();
                selesai.setHours(eh, em, 0, 0);

                if (selesai < mulai) {
                    selesai.setDate(selesai.getDate() + 1);
                }

                const diff = Math.round((selesai - mulai) / 60000);

                result.textContent = `(${diff}) Menit`;
                menit.value = diff;
            }

            start.addEventListener("change", hitungPremix);
            end.addEventListener("change", hitungPremix);

            hitungPremix();
        });
    </script>

    {{-- JS BOWL CUTTER --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const start = document.getElementById("bowl_start");
            const end = document.getElementById("bowl_end");
            const result = document.getElementById("bowl_result");
            const menit = document.getElementById("bowl_menit");
            const startHidden = document.getElementById("bowl_start_hidden");
            const endHidden = document.getElementById("bowl_end_hidden");

            if (!start || !end) return;

            function hitungBowl() {
                startHidden.value = start.value;
                endHidden.value = end.value;

                if (!start.value || !end.value) {
                    result.textContent = "(0) Menit";
                    menit.value = "";
                    return;
                }

                let [sh, sm] = start.value.split(":").map(Number);
                let [eh, em] = end.value.split(":").map(Number);

                let mulai = new Date();
                mulai.setHours(sh, sm, 0, 0);

                let selesai = new Date();
                selesai.setHours(eh, em, 0, 0);

                if (selesai < mulai) {
                    selesai.setDate(selesai.getDate() + 1);
                }

                const diff = Math.round((selesai - mulai) / 60000);

                result.textContent = `(${diff}) Menit`;
                menit.value = diff;
            }

            start.addEventListener("change", hitungBowl);
            end.addEventListener("change", hitungBowl);

            hitungBowl();
        });
    </script>


    {{-- JS MIXING --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const start = document.getElementById('mixing_start');
            const end = document.getElementById('mixing_end');

            const result = document.getElementById('mixing_result');
            const menit = document.getElementById('mixing_menit');
            const startHidden = document.getElementById('mixing_start_hidden');
            const endHidden = document.getElementById('mixing_end_hidden');

            function hitungMixing() {
                startHidden.value = start.value;
                endHidden.value = end.value;

                if (!start.value || !end.value) {
                    menit.value = 0;
                    result.innerText = '(0) Menit';
                    return;
                }

                let mulai = new Date(`2000-01-01T${start.value}`);
                let selesai = new Date(`2000-01-01T${end.value}`);

                if (selesai < mulai) {
                    selesai.setDate(selesai.getDate() + 1);
                }

                let total = Math.floor((selesai - mulai) / 60000);

                menit.value = total;
                result.innerText = `(${total}) Menit`;
            }

            start.addEventListener('change', hitungMixing);
            end.addEventListener('change', hitungMixing);

            hitungMixing();
        });
    </script>
@endsection
