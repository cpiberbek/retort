@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">✏️ Edit Form</h3>

            {{-- Alert error jika validasi gagal --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ups!</strong> Ada kesalahan pada inputan Anda:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Form Edit --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('list_form.update', $list_form->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="laporan" class="form-label">Nama Laporan</label>
                            <input
                                type="text"
                                name="laporan"
                                class="form-control @error('laporan') is-invalid @enderror"
                                value="{{ old('laporan', $list_form->laporan) }}" readonly
                            >
                            @error('laporan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_dokumen" class="form-label">No. Dokumen</label>
                            <input
                                type="text"
                                name="no_dokumen"
                                class="form-control @error('no_dokumen') is-invalid @enderror"
                                value="{{ old('no_dokumen', $list_form->no_dokumen) }}"
                            >
                            @error('no_dokumen')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        
                        @switch($list_form->laporan)

                            @case('Pemeriksaan Mincing - Emulsifying - Aging')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    $rowsStandar = [
                                        ['suhu_akhir_emulsi_gel', 'Suhu Akhir Emulsi Gel (°C)', false],
                                        ['suhu_akhir_mixing', 'Suhu Akhir Mixing (°C)', true],
                                        ['suhu_akhir_emulsi', 'Suhu Akhir Emulsifying (°C)', true],
                                        ['suhu_sebelum_grinding', 'Suhu Sebelum Grinding (°C)', true],
                                        ['non_premix.suhu_bahan', 'Suhu Bahan Non-Premix (°C)', true],
                                        ['non_premix.ph_bahan', 'pH Bahan Non-Premix', true],
                                    ];

                                    $sensoriNonPremix = old(
                                        'standar_ref.non_premix.sensori',
                                        data_get($std, 'non_premix.sensori')
                                    );

                                    $sensoriPremix = old(
                                        'standar_ref.premix.sensori_premix',
                                        data_get($std, 'premix.sensori_premix')
                                    );
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan isian jika field tidak ingin dipaksa saat membuat data audit.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rowsStandar as [$key, $label, $hasMin])
                                                    @php
                                                        $nameKey = 'standar_ref[' . str_replace('.', '][', $key) . ']';
                                                    @endphp

                                                    <tr>
                                                        <td class="align-middle">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                @if ($hasMin)
                                                                    <input
                                                                        type="number"
                                                                        step="any"
                                                                        class="form-control form-control-sm"
                                                                        name="{{ $nameKey }}[min]"
                                                                        placeholder="Min"
                                                                        value="{{ old('standar_ref.' . $key . '.min', data_get($std, $key . '.min')) }}"
                                                                    >

                                                                    <span class="text-muted" style="margin: 0 0.5rem;">s/d</span>

                                                                    <input
                                                                        type="number"
                                                                        step="any"
                                                                        class="form-control form-control-sm"
                                                                        name="{{ $nameKey }}[max]"
                                                                        placeholder="Max"
                                                                        value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                    >
                                                                @else
                                                                    <span class="text-muted" style="margin-right: 0.5rem; font-weight: 600;">&lt;</span>

                                                                    <input
                                                                        type="number"
                                                                        step="any"
                                                                        class="form-control form-control-sm"
                                                                        name="{{ $nameKey }}[max]"
                                                                        placeholder="Batas atas"
                                                                        value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                    >
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div class="mt-3">
                                            <div class="custom-control custom-checkbox">
                                                <input
                                                    type="hidden"
                                                    name="standar_ref[non_premix][sensori]"
                                                    value=""
                                                >

                                                <input
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="sensori_non_premix"
                                                    name="standar_ref[non_premix][sensori]"
                                                    value="Oke"
                                                    {{ $sensoriNonPremix === 'Oke' ? 'checked' : '' }}
                                                >

                                                <label class="custom-control-label" for="sensori_non_premix">
                                                    Check Sensori Non-Premix
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            <div class="custom-control custom-checkbox">
                                                <input
                                                    type="hidden"
                                                    name="standar_ref[premix][sensori_premix]"
                                                    value=""
                                                >

                                                <input
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="sensori_premix"
                                                    name="standar_ref[premix][sensori_premix]"
                                                    value="Oke"
                                                    {{ $sensoriPremix === 'Oke' ? 'checked' : '' }}
                                                >

                                                <label class="custom-control-label" for="sensori_premix">
                                                    Check Sensori Premix
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @break

                            @case('Pemeriksaan Metal Detector')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    $metalFields = [
                                        ['fe',  'Fe (Ferrous)'],
                                        ['nfe', 'NFe (Non-Ferrous)'],
                                        ['sus', 'SUS (Stainless Steel)'],
                                    ];

                                    $metalOptions = ['Terdeteksi', 'Tidak Terdeteksi'];
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Pilih hasil yang akan diterapkan otomatis saat membuat data audit.
                                            Pilih <b>Tidak dirubah</b> jika field tidak ingin diubah (memakai data asli).
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($metalFields as [$key, $label])
                                                    @php
                                                        $current = old('standar_ref.' . $key, data_get($std, $key));
                                                    @endphp

                                                    <tr>
                                                        <td class="align-middle">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            <select
                                                                name="standar_ref[{{ $key }}]"
                                                                class="form-select form-control form-control-sm"
                                                            >
                                                                <option value="">— Tidak Dirubah —</option>
                                                                @foreach ($metalOptions as $opt)
                                                                    <option value="{{ $opt }}" {{ $current === $opt ? 'selected' : '' }}>
                                                                        {{ $opt }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Checklist Cleaning Magnet Trap')
                                @php
                                    $std = $list_form->standar_ref ?? [];
                                    $magnetOptions = [
                                        'v' => 'OK',
                                        'x' => 'Not OK',
                                    ];
                                    $current = old('standar_ref.status', data_get($std, 'status', 'v'));
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Pilih status yang akan diterapkan otomatis saat membuat data audit.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="align-middle">Status Cleaning Magnet Trap</td>
                                                    <td class="align-middle">
                                                        <select
                                                            name="standar_ref[status]"
                                                            class="form-select form-control form-control-sm"
                                                        >
                                                            @foreach ($magnetOptions as $value => $label)
                                                                <option value="{{ $value }}"
                                                                    {{ $current === $value ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Pemeriksaan Kekuatan Magnet Trap')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    $currentParameter = old(
                                        'standar_ref.parameter_sesuai',
                                        data_get($std, 'parameter_sesuai')
                                    );
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan isian jika field tidak ingin dipaksa saat membuat data audit.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 1; $i <= 6; $i++)
                                                    @php
                                                        $key = 'kekuatan_median_' . $i;
                                                        $currentMin = old("standar_ref.$key.min", data_get($std, "$key.min"));
                                                        $currentMax = old("standar_ref.$key.max", data_get($std, "$key.max"));
                                                    @endphp

                                                    <tr>
                                                        <td class="align-middle">
                                                            Kekuatan Magnet {{ $i }}
                                                        </td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                <input
                                                                    type="number"
                                                                    step="0.01"
                                                                    name="standar_ref[{{ $key }}][min]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Min"
                                                                    value="{{ $currentMin }}"
                                                                >

                                                                <span class="text-muted" style="margin: 0 0.5rem;">s/d</span>

                                                                <input
                                                                    type="number"
                                                                    step="0.01"
                                                                    name="standar_ref[{{ $key }}][max]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Max"
                                                                    value="{{ $currentMax }}"
                                                                >
                                                                <span class="text-muted" style="margin: 0 0.5rem;">Gauss</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endfor

                                                <tr>
                                                    <td class="align-middle">Parameter Sesuai</td>
                                                    <td class="align-middle">
                                                        <select
                                                            name="standar_ref[parameter_sesuai]"
                                                            class="form-control form-control-sm"
                                                        >
                                                            <option value="">— Tidak Diubah —</option>
                                                            <option value="1" {{ (string) $currentParameter === '1' ? 'selected' : '' }}>
                                                                Sesuai (√)
                                                            </option>
                                                            <option value="0" {{ (string) $currentParameter === '0' ? 'selected' : '' }}>
                                                                Tidak Sesuai (X)
                                                            </option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Pemeriksaan Stuffing Sosis Retort')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    $stuffingFields = [
                                        ['suhu', 'Suhu (°C)', 'range'],
                                        ['sensori', 'Sensori', 'select'],
                                        ['kecepatan_stuffing', 'Kecepatan Stuffing (/mnt)', 'range'],
                                        ['panjang_pcs', 'Panjang per PCS (cm)', 'range'],
                                        ['berat_pcs', 'Berat per PCS (gr)', 'range'],
                                        ['diameter_klip', 'Diameter Klip (mm)', 'range'],
                                        ['lebar_cassing', 'Lebar Cassing (mm)', 'range'],
                                        ['kebersihan_seal', 'Kebersihan Ujung Seal', 'select'],
                                        ['kekuatan_seal', 'Kekuatan Seal', 'select'],
                                        ['print_kode', 'Print Kode Production', 'select'],
                                        ['catatan', 'Catatan', 'text'],
                                    ];

                                    $stuffingOptions = ['OK', 'Tidak OK'];
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diseragamkan
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($stuffingFields as [$key, $label, $type])
                                                    @php
                                                        $current = old('standar_ref.' . $key, data_get($std, $key));
                                                    @endphp

                                                    <tr>
                                                        <td class="align-middle">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            @if ($type === 'range')
                                                                <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                    <input
                                                                        type="number"
                                                                        step="any"
                                                                        name="standar_ref[{{ $key }}][min]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Min"
                                                                        value="{{ old('standar_ref.' . $key . '.min', data_get($std, $key . '.min')) }}"
                                                                    >

                                                                    <span class="text-muted mx-2">s/d</span>

                                                                    <input
                                                                        type="number"
                                                                        step="any"
                                                                        name="standar_ref[{{ $key }}][max]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Max"
                                                                        value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                    >
                                                                </div>
                                                            @elseif ($type === 'text')
                                                                <input
                                                                    type="text"
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Catatan untuk semua mesin"
                                                                    value="{{ $current }}"
                                                                >
                                                            @else
                                                                <select
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                >
                                                                    <option value="">— Tidak Diubah —</option>
                                                                    @foreach ($stuffingOptions as $option)
                                                                        <option
                                                                            value="{{ $option }}"
                                                                            {{ $current === $option ? 'selected' : '' }}
                                                                        >
                                                                            {{ $option }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break                

                            @case('Pemeriksaan Washing - Drying')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    $washingFields = [
                                        ['panjang_produk', 'Panjang Produk (cm)', 'range'],
                                        ['diameter_produk', 'Diameter Produk (mm)', 'range'],
                                        ['airtrap', 'Air Trap', 'ada'],
                                        ['lengket', 'Lengket', 'ada'],
                                        ['sisa_adonan', 'Sisa Adonan', 'ada'],
                                        ['kebocoran', 'Kebocoran', 'ada'],
                                        ['kekuatan_seal', 'Kekuatan Seal', 'ok'],
                                        ['print_kode', 'Print Kode', 'ok'],
                                        ['konsentrasi_pckleer', 'Konsentrasi Pckleer', 'range'],
                                        ['suhu_pckleer_1', 'Suhu Pckleer 1 (°C)', 'range'],
                                        ['suhu_pckleer_2', 'Suhu Pckleer 2 (°C)', 'range'],
                                        ['ph_pckleer', 'pH Pckleer', 'range'],
                                        ['kondisi_air_pckleer', 'Kondisi Air Pckleer', 'ok'],
                                        ['konsentrasi_pottasium', 'Konsentrasi Pottasium', 'range'],
                                        ['suhu_pottasium', 'Suhu Pottasium (°C)', 'range'],
                                        ['ph_pottasium', 'pH Pottasium', 'range'],
                                        ['kondisi_pottasium', 'Kondisi Pottasium', 'ok'],
                                        ['suhu_heater', 'Suhu Heater (°C)', 'range'],
                                        ['speed_1', 'Speed 1', 'range'],
                                        ['speed_2', 'Speed 2', 'range'],
                                        ['speed_3', 'Speed 3', 'range'],
                                        ['speed_4', 'Speed 4', 'range'],
                                        ['catatan', 'Catatan', 'text'],
                                    ];

                                    $washingOptions = [
                                        'ada' => ['Ada', 'Tidak Ada'],
                                        'ok' => ['OK', 'Tidak OK'],
                                    ];
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diubah saat membuat data audit.
                                            Field numerik menggunakan rentang min–max, hasil yang diharapkan akan diacak sesuai rentang sedangkan field pilihan menggunakan nilai yang ditentukan.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($washingFields as [$key, $label, $type])
                                                    @php
                                                        $current = old('standar_ref.' . $key, data_get($std, $key));
                                                    @endphp

                                                    <tr>
                                                        <td class="align-middle">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            @if ($type === 'text')
                                                                <input
                                                                    type="text"
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Catatan Pemeriksaan"
                                                                    value="{{ $current }}"
                                                                >
                                                            @elseif ($type === 'range')
                                                                <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                    <input
                                                                        type="number"
                                                                        step="any"
                                                                        name="standar_ref[{{ $key }}][min]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Min"
                                                                        value="{{ old('standar_ref.' . $key . '.min', data_get($std, $key . '.min')) }}"
                                                                    >

                                                                    <span class="text-muted mx-2">s/d</span>

                                                                    <input
                                                                        type="number"
                                                                        step="any"
                                                                        name="standar_ref[{{ $key }}][max]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Max"
                                                                        value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                    >
                                                                </div>
                                                            @else
                                                                <select
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                >
                                                                    <option value="">— Tidak Diubah —</option>

                                                                    @foreach ($washingOptions[$type] as $option)
                                                                        <option
                                                                            value="{{ $option }}"
                                                                            {{ $current === $option ? 'selected' : '' }}
                                                                        >
                                                                            {{ $option }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break
                            
                            @case('Pemeriksaan Pemasakan')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    // [key, label, type, grup]  -> grup mengikuti section di form create pemasakan
                                    $pemasakanFields = [
                                        // IDENTIFIKASI
                                        ['jumlah_tray', 'Jumlah Tray', 'range_integer', 'IDENTIFIKASI'],
                                        ['suhu_produk', 'Suhu Produk (°C)', 'range', 'IDENTIFIKASI'],
                                        ['berat_produk', 'Berat Produk', 'range', 'IDENTIFIKASI'],

                                        // PERSIAPAN
                                        ['cooking.tekanan_angin', 'Tekanan Angin', 'range', 'PERSIAPAN'],
                                        ['cooking.tekanan_steam', 'Tekanan Steam', 'range', 'PERSIAPAN'],
                                        ['cooking.tekanan_air', 'Tekanan Air', 'range', 'PERSIAPAN'],

                                        // PEMANASAN AWAL
                                        ['cooking.suhu_air_awal', 'Suhu Air Awal (°C)', 'range', 'PEMANASAN AWAL'],
                                        ['cooking.tekanan_awal', 'Tekanan Awal', 'range', 'PEMANASAN AWAL'],

                                        // PROSES PEMANASAN
                                        ['cooking.suhu_air_proses', 'Suhu Air Proses (°C)', 'range', 'PROSES PEMANASAN'],
                                        ['cooking.tekanan_proses', 'Tekanan Proses', 'range', 'PROSES PEMANASAN'],

                                        // STERILISASI
                                        ['cooking.suhu_air_sterilisasi', 'Suhu Air Sterilisasi (°C)', 'range_array', 'STERILISASI'],
                                        ['cooking.thermometer_retort', 'Thermometer Retort (°C)', 'range_array', 'STERILISASI'],
                                        ['cooking.tekanan_sterilisasi', 'Tekanan Sterilisasi', 'range_array', 'STERILISASI'],

                                        // PENDINGINAN AWAL
                                        ['cooking.suhu_air_pendinginan_awal', 'Suhu Air Pendinginan Awal (°C)', 'range', 'PENDINGINAN AWAL'],
                                        ['cooking.tekanan_pendinginan_awal', 'Tekanan Pendinginan Awal', 'range', 'PENDINGINAN AWAL'],

                                        // PENDINGINAN
                                        ['cooking.suhu_air_pendinginan', 'Suhu Air Pendinginan (°C)', 'range', 'PENDINGINAN'],
                                        ['cooking.tekanan_pendinginan', 'Tekanan Pendinginan', 'range', 'PENDINGINAN'],

                                        // PROSES AKHIR
                                        ['cooking.suhu_air_akhir', 'Suhu Air Akhir (°C)', 'range', 'PROSES AKHIR'],
                                        ['cooking.tekanan_akhir', 'Tekanan Akhir', 'range', 'PROSES AKHIR'],

                                        // HASIL PEMASAKAN
                                        ['cooking.suhu_produk_akhir', 'Suhu Produk Akhir (°C)', 'range', 'HASIL PEMASAKAN'],
                                        ['cooking.panjang', 'Panjang Produk', 'range', 'HASIL PEMASAKAN'],
                                        ['cooking.diameter', 'Diameter Produk', 'range', 'HASIL PEMASAKAN'],
                                        ['cooking.rasa', 'Rasa', 'rating', 'HASIL PEMASAKAN'],
                                        ['cooking.warna', 'Warna', 'rating', 'HASIL PEMASAKAN'],
                                        ['cooking.aroma', 'Aroma', 'rating', 'HASIL PEMASAKAN'],
                                        ['cooking.texture', 'Texture', 'rating', 'HASIL PEMASAKAN'],
                                        ['cooking.sobek_seal', 'Sobek Seal', 'number_integer', 'HASIL PEMASAKAN'],

                                        // TOTAL REJECT
                                        ['total_reject', 'Total Reject', 'number_integer', 'TOTAL REJECT'],

                                        // CATATAN
                                        ['catatan', 'Catatan', 'text', 'CATATAN'],
                                    ];

                                    $pemasakanOptions = [];
                                    $lastGroup = null;
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan jika tidak ingin mengubah nilai saat audit. Untuk rentang, isi nilai minimum dan maksimum yang nantinya akan diacak tapi sesuai rentang. Untuk field yang kemungkinan memiliki banyak item seperti Jumlah Tray / Total Reject, standar akan diterapkan ke setiap item tanpa mengubah jumlahnya.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pemasakanFields as [$key, $label, $type, $group])
                                                    @php
                                                        $nameKey = 'standar_ref[' . str_replace('.', '][', $key) . ']';
                                                        $current = old('standar_ref.' . $key, data_get($std, $key));
                                                    @endphp

                                                    {{-- Header section: tampil sekali setiap grup berganti --}}
                                                    @if ($group !== $lastGroup)
                                                        <tr class="table-secondary">
                                                            <td colspan="2" style="font-weight: 700; letter-spacing: .03em;">
                                                                {{ $group }}
                                                            </td>
                                                        </tr>
                                                        @php $lastGroup = $group; @endphp
                                                    @endif

                                                    <tr>
                                                        <td class="align-middle pl-4" style="padding-left: 1.5rem;">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            @if (in_array($type, ['range', 'range_array', 'range_integer']))
                                                                <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                    <input
                                                                        type="number"
                                                                        step="{{ $type === 'range_integer' ? '1' : 'any' }}"
                                                                        @if ($type === 'range_integer') min="0" @endif
                                                                        name="{{ $nameKey }}[min]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Min"
                                                                        value="{{ old('standar_ref.' . $key . '.min', data_get($std, $key . '.min')) }}"
                                                                    >

                                                                    <span class="text-muted mx-2">s/d</span>

                                                                    <input
                                                                        type="number"
                                                                        step="{{ $type === 'range_integer' ? '1' : 'any' }}"
                                                                        @if ($type === 'range_integer') min="0" @endif
                                                                        name="{{ $nameKey }}[max]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Max"
                                                                        value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                    >
                                                                </div>
                                                            @elseif ($type === 'rating')
                                                                <input
                                                                    type="number"
                                                                    min="1"
                                                                    max="3"
                                                                    step="1"
                                                                    name="{{ $nameKey }}"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="1 - 3"
                                                                    value="{{ $current }}"
                                                                >
                                                            @elseif ($type === 'number_integer')
                                                                <input
                                                                    type="number"
                                                                    step="1"
                                                                    min="0"
                                                                    name="{{ $nameKey }}"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Nilai tetap (bilangan bulat)"
                                                                    value="{{ $current }}"
                                                                >
                                                            @elseif ($type === 'number')
                                                                <input
                                                                    type="number"
                                                                    step="any"
                                                                    name="{{ $nameKey }}"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Nilai tetap"
                                                                    value="{{ $current }}"
                                                                >
                                                            @else
                                                                <input
                                                                    type="text"
                                                                    name="{{ $nameKey }}"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Kosongkan jika tidak ingin diubah"
                                                                    value="{{ $current }}"
                                                                >
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break
                                            
                            @case('Verifikasi Timer Chamber')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    $timerFields = [];

                                    foreach ([5, 10, 20, 30, 60] as $menit) {
                                        $timerFields[] = [
                                            "plc_menit_{$menit}",
                                            "PLC Menit ({$menit} Menit)",
                                            'integer_range',
                                        ];

                                        $timerFields[] = [
                                            "plc_detik_{$menit}",
                                            "PLC Detik ({$menit} Menit)",
                                            'integer_range',
                                        ];

                                        $timerFields[] = [
                                            "stopwatch_menit_{$menit}",
                                            "Stopwatch Menit ({$menit} Menit)",
                                            'integer_range',
                                        ];

                                        $timerFields[] = [
                                            "stopwatch_detik_{$menit}",
                                            "Stopwatch Detik ({$menit} Menit)",
                                            'integer_range',
                                        ];

                                        $timerFields[] = [
                                            "faktor_koreksi_{$menit}",
                                            "Faktor Koreksi ({$menit} Menit)",
                                            'text',
                                        ];
                                    }

                                    $timerFields[] = [
                                        'catatan',
                                        'Catatan',
                                        'text',
                                    ];
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diubah saat membuat data audit.
                                            Field menit dan detik menggunakan rentang bilangan bulat dan hasil akan diacak
                                            berdasarkan rentang yang ditetapkan, sedangkan faktor koreksi dan catatan
                                            menggunakan nilai tetap.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($timerFields as [$key, $label, $type])
                                                    @php
                                                        $current = old(
                                                            'standar_ref.' . $key,
                                                            data_get($std, $key)
                                                        );
                                                    @endphp

                                                    <tr>
                                                        <td class="align-middle">
                                                            {{ $label }}
                                                        </td>

                                                        <td class="align-middle">
                                                            @if ($type === 'integer_range')
                                                                <div
                                                                    class="d-flex align-items-center"
                                                                    style="white-space: nowrap;"
                                                                >
                                                                    <input
                                                                        type="number"
                                                                        step="1"
                                                                        min="0"
                                                                        name="standar_ref[{{ $key }}][min]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Min"
                                                                        value="{{ old(
                                                                            'standar_ref.' . $key . '.min',
                                                                            data_get($std, $key . '.min')
                                                                        ) }}"
                                                                    >

                                                                    <span class="text-muted mx-2">s/d</span>

                                                                    <input
                                                                        type="number"
                                                                        step="1"
                                                                        min="0"
                                                                        name="standar_ref[{{ $key }}][max]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Max"
                                                                        value="{{ old(
                                                                            'standar_ref.' . $key . '.max',
                                                                            data_get($std, $key . '.max')
                                                                        ) }}"
                                                                    >
                                                                </div>
                                                            @else
                                                                <input
                                                                    type="text"
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Masukkan {{ $label }}"
                                                                    value="{{ $current }}"
                                                                >
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Pemeriksaan Proses Sampling Finish Good')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    $samplingFields = [
                                        ['kalibrasi', 'Kalibrasi', 'select', null],
                                        ['berat_produk', 'Berat Produk', 'range_integer', null],
                                        ['release', 'Release', 'range_integer', 'pair'],
                                        ['reject', 'Reject', 'range_integer', 'pair'],
                                        ['hold', 'Hold', 'range_integer', 'pair'],
                                        ['item_mutu', 'Item Mutu', 'text', null],
                                        ['keterangan', 'Keterangan', 'text', null],
                                        ['catatan', 'Catatan', 'text', null],
                                    ];

                                    $kalibrasiOptions = ['Sesuai', 'Tidak Sesuai'];
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diubah saat membuat data audit.
                                            Field rentang akan diacak berdasarkan rentang bilangan bulat yang ditetapkan,
                                            sedangkan field teks menggunakan nilai tetap.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($samplingFields as [$key, $label, $type, $note])
                                                    @php
                                                        $current = old('standar_ref.' . $key, data_get($std, $key));
                                                    @endphp

                                                    <tr>
                                                        <td class="align-middle">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            @if ($type === 'range_integer')
                                                                <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                    <input
                                                                        type="number"
                                                                        step="1"
                                                                        min="0"
                                                                        name="standar_ref[{{ $key }}][min]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Min"
                                                                        value="{{ old('standar_ref.' . $key . '.min', data_get($std, $key . '.min')) }}"
                                                                    >

                                                                    <span class="text-muted mx-2">s/d</span>

                                                                    <input
                                                                        type="number"
                                                                        step="1"
                                                                        min="0"
                                                                        name="standar_ref[{{ $key }}][max]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Max"
                                                                        value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                    >
                                                                </div>

                                                                @if ($note === 'pair')
                                                                    <small class="text-muted d-block mt-1">
                                                                        Min &amp; Max harus diisi keduanya. Kosongkan keduanya jika tidak ingin diubah.
                                                                        Untuk nilai tepat 0, isi <b>0 s/d 0</b>.
                                                                    </small>
                                                                @endif
                                                            @elseif ($type === 'text')
                                                                <input
                                                                    type="text"
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Kosongkan jika tidak ingin diubah"
                                                                    value="{{ $current }}"
                                                                >
                                                            @else
                                                                <select
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                >
                                                                    <option value="">— Tidak Diubah —</option>
                                                                    @foreach ($kalibrasiOptions as $option)
                                                                        <option
                                                                            value="{{ $option }}"
                                                                            {{ $current === $option ? 'selected' : '' }}
                                                                        >
                                                                            {{ $option }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Data Sampling Produk')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    $samplingsFields = [
                                        ['jamur', 'Jamur'],
                                        ['lendir', 'Lendir'],
                                        ['klip_tajam', 'Klip Tajam'],
                                        ['pin_hole', 'Pin Hole'],
                                        ['air_trap_pvdc', 'Air Trap PVDC'],
                                        ['air_trap_produk', 'Air Trap Produk'],
                                        ['keriput', 'Keriput'],
                                        ['bengkok', 'Bengkok'],
                                        ['non_kode', 'Non Kode'],
                                        ['over_lap', 'Over Lap'],
                                        ['kecil', 'Kecil'],
                                        ['terjepit', 'Terjepit'],
                                        ['double_klip', 'Double Klip'],
                                        ['seal_halus', 'Seal Halus'],
                                        ['basah', 'Basah'],
                                        ['dll', 'Dll'],
                                    ];
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diubah saat membuat data audit.
                                            Field jumlah cacat diacak berdasarkan rentang bilangan bulat. Min &amp; Max harus diisi
                                            keduanya, atau kosongkan keduanya jika tidak ingin diubah. Untuk nilai tepat 0, isi <b>0 s/d 0</b>.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($samplingsFields as [$key, $label])
                                                    <tr>
                                                        <td class="align-middle">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                <input
                                                                    type="number"
                                                                    step="1"
                                                                    min="0"
                                                                    name="standar_ref[{{ $key }}][min]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Min"
                                                                    value="{{ old('standar_ref.' . $key . '.min', data_get($std, $key . '.min')) }}"
                                                                >

                                                                <span class="text-muted mx-2">s/d</span>

                                                                <input
                                                                    type="number"
                                                                    step="1"
                                                                    min="0"
                                                                    name="standar_ref[{{ $key }}][max]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Max"
                                                                    value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                >
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                <tr>
                                                    <td class="align-middle">Catatan</td>
                                                    <td class="align-middle">
                                                        <input
                                                            type="text"
                                                            name="standar_ref[catatan]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Kosongkan jika tidak ingin diubah"
                                                            value="{{ old('standar_ref.catatan', data_get($std, 'catatan')) }}"
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Data Release Packing')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diubah saat membuat data audit.
                                            Release diacak berdasarkan rentang bilangan bulat. Min &amp; Max harus diisi keduanya,
                                            atau kosongkan keduanya jika tidak ingin diubah. Untuk nilai tepat tertentu, isi Min dan Max
                                            dengan angka yang sama.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="align-middle">Release</td>
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                            <input
                                                                type="number"
                                                                step="1"
                                                                min="0"
                                                                name="standar_ref[release][min]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Min"
                                                                value="{{ old('standar_ref.release.min', data_get($std, 'release.min')) }}"
                                                            >

                                                            <span class="text-muted mx-2">s/d</span>

                                                            <input
                                                                type="number"
                                                                step="1"
                                                                min="0"
                                                                name="standar_ref[release][max]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Max"
                                                                value="{{ old('standar_ref.release.max', data_get($std, 'release.max')) }}"
                                                            >
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="align-middle">Keterangan</td>
                                                    <td class="align-middle">
                                                        <input
                                                            type="text"
                                                            name="standar_ref[keterangan]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Kosongkan jika tidak ingin diubah"
                                                            value="{{ old('standar_ref.keterangan', data_get($std, 'keterangan')) }}"
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break
                            
                            @case('Pemeriksaan Proses Packing')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diubah saat membuat data audit.
                                            Min dan Max harus diisi keduanya, atau kosongkan keduanya jika tidak ingin diubah.
                                            Untuk nilai tepat tertentu, isi Min dan Max dengan angka yang sama.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ([
                                                    'suhu' => 'Suhu',
                                                    'speed' => 'Speed',
                                                    'berat_pcs' => 'Berat PCS',
                                                    'berat_pack' => 'Berat Pack',
                                                ] as $key => $label)
                                                    <tr>
                                                        <td class="align-middle">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                <input
                                                                    type="number"
                                                                    step="any"
                                                                    name="standar_ref[{{ $key }}][min]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Min"
                                                                    value="{{ old("standar_ref.{$key}.min", data_get($std, "{$key}.min")) }}"
                                                                >

                                                                <span class="text-muted mx-2">s/d</span>

                                                                <input
                                                                    type="number"
                                                                    step="any"
                                                                    name="standar_ref[{{ $key }}][max]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Max"
                                                                    value="{{ old("standar_ref.{$key}.max", data_get($std, "{$key}.max")) }}"
                                                                >
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @foreach ([
                                                    'kalibrasi' => [
                                                        'label' => 'Kalibrasi',
                                                        'options' => ['Oke', 'Tidak Oke'],
                                                    ],
                                                    'kondisi_segel' => [
                                                        'label' => 'Kondisi Segel',
                                                        'options' => ['OKE', 'TIDAK OKE'],
                                                    ],
                                                ] as $key => $field)
                                                    <tr>
                                                        <td class="align-middle">{{ $field['label'] }}</td>
                                                        <td class="align-middle">
                                                            <select
                                                                name="standar_ref[{{ $key }}]"
                                                                class="form-control form-control-sm"
                                                            >
                                                                <option value="">-- Pilih Standar --</option>
                                                                @foreach ($field['options'] as $option)
                                                                    <option
                                                                        value="{{ $option }}"
                                                                        {{ old("standar_ref.{$key}", data_get($std, $key)) === $option ? 'selected' : '' }}
                                                                    >
                                                                        {{ $option }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                <tr>
                                                    <td class="align-middle">Keterangan</td>
                                                    <td class="align-middle">
                                                        <input
                                                            type="text"
                                                            name="standar_ref[keterangan]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Kosongkan jika tidak ingin diubah"
                                                            value="{{ old('standar_ref.keterangan', data_get($std, 'keterangan')) }}"
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Kontrol Labelisasi Cartooning')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diubah saat membuat data audit.
                                            Min dan Max harus diisi keduanya, atau kosongkan keduanya jika tidak ingin diubah.
                                            Untuk nilai tepat tertentu, isi Min dan Max dengan angka yang sama.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="align-middle">Jumlah</td>
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                            <input
                                                                type="number"
                                                                step="1"
                                                                min="0"
                                                                name="standar_ref[jumlah][min]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Min"
                                                                value="{{ old('standar_ref.jumlah.min', data_get($std, 'jumlah.min')) }}"
                                                            >

                                                            <span class="text-muted mx-2">s/d</span>

                                                            <input
                                                                type="number"
                                                                step="1"
                                                                min="0"
                                                                name="standar_ref[jumlah][max]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Max"
                                                                value="{{ old('standar_ref.jumlah.max', data_get($std, 'jumlah.max')) }}"
                                                            >
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="align-middle">Keterangan</td>
                                                    <td class="align-middle">
                                                        <input
                                                            type="text"
                                                            name="standar_ref[keterangan]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Kosongkan jika tidak ingin diubah"
                                                            value="{{ old('standar_ref.keterangan', data_get($std, 'keterangan')) }}"
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Pemeriksaan Organoleptik')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    $organoFields = [
                                        ['penampilan', 'Penampilan'],
                                        ['aroma', 'Aroma'],
                                        ['kekenyalan', 'Kekenyalan'],
                                        ['rasa_asin', 'Rasa Asin'],
                                        ['rasa_gurih', 'Rasa Gurih'],
                                        ['rasa_manis', 'Rasa Manis'],
                                        ['rasa_daging', 'Rasa Daging / Ayam / BBQ / Ikan'],
                                        ['rasa_keseluruhan', 'Rasa Keseluruhan'],
                                    ];
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika parameter tidak ingin diubah saat membuat data audit.
                                            Skor 1 sampai 3 (bilangan bulat) dan diacak per baris kode produksi di dalam rentang.
                                            Min dan Max harus diisi keduanya. Untuk nilai pasti, isi Min dan Max dengan angka yang sama.
                                            Rata-rata skor dan hasil Release / Tidak Release tetap dihitung ulang otomatis di form.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Parameter</th>
                                                    <th style="width: 50%;">Standar Skor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($organoFields as [$key, $label])
                                                    <tr>
                                                        <td class="align-middle">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                <input
                                                                    type="number"
                                                                    step="1"
                                                                    min="1"
                                                                    max="3"
                                                                    name="standar_ref[{{ $key }}][min]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Min (1-3)"
                                                                    value="{{ old('standar_ref.' . $key . '.min', data_get($std, $key . '.min')) }}"
                                                                >

                                                                <span class="text-muted mx-2">s/d</span>

                                                                <input
                                                                    type="number"
                                                                    step="1"
                                                                    min="1"
                                                                    max="3"
                                                                    name="standar_ref[{{ $key }}][max]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Max (1-3)"
                                                                    value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                >
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Pemeriksaan Sampel Retain')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    // [key, label, type, grup]
                                    $retainFields = [
                                        ['panjang', 'Panjang (cm)', 'range', 'DIMENSI'],
                                        ['diameter', 'Diameter (cm)', 'range', 'DIMENSI'],

                                        ['sensori_rasa', 'Rasa', 'range_13', 'SENSORI'],
                                        ['sensori_warna', 'Warna', 'range_13', 'SENSORI'],
                                        ['sensori_aroma', 'Aroma', 'range_13', 'SENSORI'],
                                        ['sensori_texture', 'Texture', 'range_13', 'SENSORI'],

                                        ['temuan_jamur', 'Jamur', 'check', 'TEMUAN'],
                                        ['temuan_lendir', 'Lendir', 'check', 'TEMUAN'],
                                        ['temuan_pinehole', 'Pinehole', 'check', 'TEMUAN'],
                                        ['temuan_kejepit', 'Kejepit', 'check', 'TEMUAN'],
                                        ['temuan_seal', 'Seal Halus/Lepas', 'check', 'TEMUAN'],

                                        ['lab_garam', 'Kadar Garam (%)', 'range', 'PARAMETER LAB'],
                                        ['lab_air', 'Kadar Air (%)', 'range', 'PARAMETER LAB'],
                                        ['lab_mikro', 'Mikro (<10 cfu)', 'range', 'PARAMETER LAB'],

                                        ['keterangan', 'Keterangan', 'text', 'KETERANGAN'],
                                    ];

                                    $lastGroup = null;
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diubah saat membuat data audit.
                                            Field rentang diacak per item pemeriksaan di dalam rentang. Min &amp; Max harus diisi keduanya.
                                            Untuk nilai pasti, isi Min dan Max dengan angka yang sama.
                                            Standar diterapkan ke setiap item tanpa mengubah jumlah item.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($retainFields as [$key, $label, $type, $group])
                                                    @php
                                                        $current = old('standar_ref.' . $key, data_get($std, $key));
                                                    @endphp

                                                    @if ($group !== $lastGroup)
                                                        <tr class="table-secondary">
                                                            <td colspan="2" style="font-weight: 700; letter-spacing: .03em;">
                                                                {{ $group }}
                                                                @if ($group === 'SENSORI')
                                                                    <span class="text-muted" style="font-weight: 400;">(skor 1 - 3)</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @php $lastGroup = $group; @endphp
                                                    @endif

                                                    <tr>
                                                        <td class="align-middle" style="padding-left: 1.5rem;">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            @if (in_array($type, ['range', 'range_13']))
                                                                <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                    <input
                                                                        type="number"
                                                                        @if ($type === 'range_13') step="1" min="1" max="3" @else step="any" @endif
                                                                        name="standar_ref[{{ $key }}][min]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Min"
                                                                        value="{{ old('standar_ref.' . $key . '.min', data_get($std, $key . '.min')) }}"
                                                                    >

                                                                    <span class="text-muted mx-2">s/d</span>

                                                                    <input
                                                                        type="number"
                                                                        @if ($type === 'range_13') step="1" min="1" max="3" @else step="any" @endif
                                                                        name="standar_ref[{{ $key }}][max]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Max"
                                                                        value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                    >
                                                                </div>
                                                            @elseif ($type === 'check')
                                                                <select
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                >
                                                                    <option value="">— Tidak Diubah —</option>
                                                                    <option value="1" {{ (string) $current === '1' ? 'selected' : '' }}>
                                                                        Dicentang
                                                                    </option>
                                                                    <option value="0" {{ (string) $current === '0' ? 'selected' : '' }}>
                                                                        Tidak Dicentang
                                                                    </option>
                                                                </select>
                                                            @else
                                                                <input
                                                                    type="text"
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Kosongkan jika tidak ingin diubah"
                                                                    value="{{ $current }}"
                                                                >
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break

                            @case('Pemeriksaan Pre Packing')
                                @php
                                    $std = $list_form->standar_ref ?? [];

                                    if (is_string($std)) {
                                        $std = json_decode($std, true) ?? [];
                                    }

                                    $prepackingFields = [
                                        ['suhu_prepacking', 'Suhu Varian (°C) - berlaku untuk Suhu 1, 2, 3', 'range', 'SUHU VARIAN'],

                                        ['pcs', 'Berat per Pcs - berlaku untuk Pcs 1, 2, 3', 'range', 'BERAT VARIAN'],
                                        ['toples', 'Berat per Toples (berat kotor) - berlaku untuk Toples 1, 2, 3', 'range', 'BERAT VARIAN'],

                                        ['basah_air_ujung', 'Basah - Air Ujung (%)', 'range', 'KONDISI PRODUK'],
                                        ['basah_air_seal', 'Basah - Air Seal (%)', 'range', 'KONDISI PRODUK'],
                                        ['kering_air_ujung', 'Kering - Air Ujung (%)', 'range', 'KONDISI PRODUK'],
                                        ['kering_air_seal', 'Kering - Air Seal (%)', 'range', 'KONDISI PRODUK'],
                                        ['basah_minyak_ujung', 'Basah - Minyak Ujung (%)', 'range', 'KONDISI PRODUK'],
                                        ['basah_minyak_seal', 'Basah - Minyak Seal (%)', 'range', 'KONDISI PRODUK'],
                                        ['kering_minyak_ujung', 'Kering - Minyak Ujung (%)', 'range', 'KONDISI PRODUK'],
                                        ['kering_minyak_seal', 'Kering - Minyak Seal (%)', 'range', 'KONDISI PRODUK'],

                                        ['catatan', 'Catatan', 'text', 'CATATAN'],
                                    ];

                                    $lastGroup = null;
                                @endphp

                                <div class="card mb-3 border">
                                    <div class="card-header bg-info text-white" style="font-weight: 600;">
                                        Standar Data Audit
                                    </div>

                                    <div class="card-body">
                                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                            Kosongkan standar jika field tidak ingin diubah saat membuat data audit.
                                            Field rentang diacak sesuai rentang min-max, tiap titik ukur diacak sendiri.
                                            Min &amp; Max harus diisi keduanya. Untuk nilai pasti, isi Min dan Max dengan angka yang sama.
                                            Total Air / Minyak pada kondisi produk dihitung ulang otomatis.
                                        </p>

                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th style="width: 50%;">Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($prepackingFields as [$key, $label, $type, $group])
                                                    @php
                                                        $current = old('standar_ref.' . $key, data_get($std, $key));
                                                    @endphp

                                                    @if ($group !== $lastGroup)
                                                        <tr class="table-secondary">
                                                            <td colspan="2" style="font-weight: 700; letter-spacing: .03em;">
                                                                {{ $group }}
                                                            </td>
                                                        </tr>
                                                        @php $lastGroup = $group; @endphp
                                                    @endif

                                                    <tr>
                                                        <td class="align-middle" style="padding-left: 1.5rem;">{{ $label }}</td>
                                                        <td class="align-middle">
                                                            @if ($type === 'range')
                                                                <div class="d-flex align-items-center" style="white-space: nowrap;">
                                                                    <input
                                                                        type="number"
                                                                        step="any"
                                                                        name="standar_ref[{{ $key }}][min]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Min"
                                                                        value="{{ old('standar_ref.' . $key . '.min', data_get($std, $key . '.min')) }}"
                                                                    >

                                                                    <span class="text-muted mx-2">s/d</span>

                                                                    <input
                                                                        type="number"
                                                                        step="any"
                                                                        name="standar_ref[{{ $key }}][max]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Max"
                                                                        value="{{ old('standar_ref.' . $key . '.max', data_get($std, $key . '.max')) }}"
                                                                    >
                                                                </div>
                                                            @else
                                                                <input
                                                                    type="text"
                                                                    name="standar_ref[{{ $key }}]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Kosongkan jika tidak ingin diubah"
                                                                    value="{{ $current }}"
                                                                >
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @break
                                                
                        @endswitch

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">💾 Update</button>
                            <a href="{{ route('list_form.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

    {{-- rule submit standards --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[action*="list_form"]');

            if (!form) return;

            const pairKeys = [];

            for (let i = 1; i <= 6; i++) {
                pairKeys.push(`kekuatan_median_${i}`);
            }

            // Samplings
            pairKeys.push(
                'jamur', 'lendir', 'klip_tajam', 'pin_hole', 'air_trap_pvdc', 'air_trap_produk',
                'keriput', 'bengkok', 'non_kode', 'over_lap', 'kecil', 'terjepit',
                'double_klip', 'seal_halus', 'basah', 'dll'
            );

            // Organoleptik
            pairKeys.push(
                'penampilan', 'aroma', 'kekenyalan', 'rasa_asin',
                'rasa_gurih', 'rasa_manis', 'rasa_daging', 'rasa_keseluruhan'
            );

            // Sampel Retain
            pairKeys.push(
                'panjang', 'diameter',
                'sensori_rasa', 'sensori_warna', 'sensori_aroma', 'sensori_texture',
                'lab_garam', 'lab_air', 'lab_mikro'
            );

            // Pre Packing
            pairKeys.push(
                'suhu_prepacking', 'pcs', 'toples',
                'basah_air_ujung', 'basah_air_seal', 'kering_air_ujung', 'kering_air_seal',
                'basah_minyak_ujung', 'basah_minyak_seal', 'kering_minyak_ujung', 'kering_minyak_seal'
            );

            pairKeys.push('release', 'reject', 'hold');

            const pairFields = pairKeys
                .map(function (key) {
                    return {
                        min: form.querySelector(`[name="standar_ref[${key}][min]"]`),
                        max: form.querySelector(`[name="standar_ref[${key}][max]"]`)
                    };
                })
                .filter(function (field) {
                    return field.min && field.max;
                });

            function validatePair(field) {
                const minFilled = field.min.value.trim() !== '';
                const maxFilled = field.max.value.trim() !== '';

                field.min.setCustomValidity('');
                field.max.setCustomValidity('');

                if (minFilled && !maxFilled) {
                    field.max.setCustomValidity('Maksimum wajib diisi jika minimum diisi.');
                } else if (!minFilled && maxFilled) {
                    field.min.setCustomValidity('Minimum wajib diisi jika maksimum diisi.');
                }
            }

            pairFields.forEach(function (field) {
                field.min.addEventListener('input', function () { validatePair(field); });
                field.max.addEventListener('input', function () { validatePair(field); });
            });

            form.addEventListener('submit', function (event) {
                pairFields.forEach(validatePair);

                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.reportValidity();
                }
            });
        });
    </script>

@endsection