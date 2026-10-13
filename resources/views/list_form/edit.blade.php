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
@endsection