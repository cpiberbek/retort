@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Edit Pengecekan Pre Packing
            </h4>

            <form id="prepackingForm" action="{{ route('prepacking.edit_spv', $prepacking->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- IDENTIFIKASI --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>IDENTIFIKASI</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ old('date', $prepacking->date) }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                           <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $prepacking->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            @php
                                $batches = collect();
                            @endphp

                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <select name="kode_produksi" class="form-control" id="kode_batch" required>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->uuid }}" {{ $prepacking->kode_produksi == $batch->uuid ? 'selected' : '' }}>
                                            {{ $batch->kode_produksi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PENGECEKAN SUHU --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>Pengecekan</strong></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <tbody>
                                    <tr>
                                        <td class="text-start">No. Conveyor</td>
                                        <td>
                                            <input type="text" name="conveyor" id="conveyor" class="form-control form-control-sm text-center" value="{{ old('conveyor', $prepacking->conveyor) }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3" class="text-center align-middle">Suhu Varian (°C)</td>
                                        <td>
                                            <input type="number" name="suhu_produk[suhu_1]" id="suhu_1" class="form-control form-control-sm text-center" step="0.01" value="{{ old('suhu_produk.suhu_1', $suhuData['suhu_1'] ?? '') }}" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="number" name="suhu_produk[suhu_2]" id="suhu_2" class="form-control form-control-sm text-center" step="0.01" value="{{ old('suhu_produk.suhu_2', $suhuData['suhu_2'] ?? '') }}" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="number" name="suhu_produk[suhu_3]" id="suhu_3" class="form-control form-control-sm text-center" step="0.01" value="{{ old('suhu_produk.suhu_3', $suhuData['suhu_3'] ?? '') }}" min="0">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- KONDISI PRODUK --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>Kondisi Varian</strong></div>
                    <div class="card-body">
                        {{-- Air --}}
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="2">Bagian</th>
                                        <th colspan="2">Air (%)</th>
                                    </tr>
                                    <tr>
                                        <th>Basah</th>
                                        <th>Kering</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start">Ujung</td>
                                        <td><input type="number" name="kondisi_produk[basah_air_ujung]" id="basah_air_ujung" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.basah_air_ujung', $kondisiData['basah_air_ujung'] ?? '') }}"></td>
                                        <td><input type="number" name="kondisi_produk[kering_air_ujung]" id="kering_air_ujung" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.kering_air_ujung', $kondisiData['kering_air_ujung'] ?? '') }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Seal</td>
                                        <td><input type="number" name="kondisi_produk[basah_air_seal]" id="basah_air_seal" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.basah_air_seal', $kondisiData['basah_air_seal'] ?? '') }}"></td>
                                        <td><input type="number" name="kondisi_produk[kering_air_seal]" id="kering_air_seal" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.kering_air_seal', $kondisiData['kering_air_seal'] ?? '') }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Total</td>
                                        <td><input type="number" name="kondisi_produk[basah_air_total]" id="basah_air_total" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.basah_air_total', $kondisiData['basah_air_total'] ?? '') }}" readonly></td>
                                        <td><input type="number" name="kondisi_produk[kering_air_total]" id="kering_air_total" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.kering_air_total', $kondisiData['kering_air_total'] ?? '') }}" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Minyak --}}
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="2">Bagian</th>
                                        <th colspan="2">Minyak (%)</th>
                                    </tr>
                                    <tr>
                                        <th>Basah</th>
                                        <th>Kering</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start">Ujung</td>
                                        <td><input type="number" name="kondisi_produk[basah_minyak_ujung]" id="basah_minyak_ujung" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.basah_minyak_ujung', $kondisiData['basah_minyak_ujung'] ?? '') }}"></td>
                                        <td><input type="number" name="kondisi_produk[kering_minyak_ujung]" id="kering_minyak_ujung" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.kering_minyak_ujung', $kondisiData['kering_minyak_ujung'] ?? '') }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Seal</td>
                                        <td><input type="number" name="kondisi_produk[basah_minyak_seal]" id="basah_minyak_seal" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.basah_minyak_seal', $kondisiData['basah_minyak_seal'] ?? '') }}"></td>
                                        <td><input type="number" name="kondisi_produk[kering_minyak_seal]" id="kering_minyak_seal" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.kering_minyak_seal', $kondisiData['kering_minyak_seal'] ?? '') }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Total</td>
                                        <td><input type="number" name="kondisi_produk[basah_minyak_total]" id="basah_minyak_total" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.basah_minyak_total', $kondisiData['basah_minyak_total'] ?? '') }}" readonly></td>
                                        <td><input type="number" name="kondisi_produk[kering_minyak_total]" id="kering_minyak_total" class="form-control form-control-sm text-center" value="{{ old('kondisi_produk.kering_minyak_total', $kondisiData['kering_minyak_total'] ?? '') }}" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- BERAT PRODUK --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>Berat Varian per</strong></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th>Pcs</th>
                                        <th>Toples (berat kotor)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i=1; $i<=3; $i++)
                                    <tr>
                                        <td><input type="number" name="berat_produk[pcs_{{ $i }}]" id="pcs_{{ $i }}" class="form-control form-control-sm text-center" step="0.01" value="{{ old("berat_produk.pcs_$i", $beratData["pcs_$i"] ?? '') }}"></td>
                                        <td><input type="number" name="berat_produk[toples_{{ $i }}]" id="toples_{{ $i }}" class="form-control form-control-sm text-center" step="0.01" value="{{ old("berat_produk.toples_$i", $beratData["toples_$i"] ?? '') }}"></td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- CATATAN --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $prepacking->catatan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                    <a href="{{ route('prepacking.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>
<script>
    $(document).ready(function(){
        const kodeInput = $('#kode_produksi');
        const kodeError = $('#kodeError');
        const form = $('#prepackingForm');

        function validateKode() {
            let value = kodeInput.val().toUpperCase().replace(/\s+/g, '');
            kodeInput.val(value);
            kodeError.text('').addClass('d-none');

            if(value.length !== 10) {
                kodeError.text('Kode batch harus terdiri dari 10 karakter').removeClass('d-none');
                return false;
            }

            if(!/^[A-Z0-9]+$/.test(value)) {
                kodeError.text('Kode batch hanya boleh huruf besar dan angka').removeClass('d-none');
                return false;
            }

            if(!/^[A-L]$/.test(value.charAt(1))) {
                kodeError.text('Karakter ke-2 harus huruf bulan (A-L)').removeClass('d-none');
                return false;
            }

            let hari = parseInt(value.substr(2,2),10);
            if(isNaN(hari) || hari < 1 || hari > 31) {
                kodeError.text('Karakter ke-3 dan ke-4 harus tanggal valid (01-31)').removeClass('d-none');
                return false;
            }

            return true;
        }

        kodeInput.on('input', validateKode);

        form.on('submit', function(e){
            if(!validateKode()){
                e.preventDefault();
                alert('Kode batch tidak valid! Periksa kembali sebelum menyimpan.');
                kodeInput.focus();
            }
        });

        // Otomatis hitung total kondisi produk
        function hitungTotal(type){
            let ujung = parseFloat($(`#${type}_ujung`).val()) || 0;
            let seal  = parseFloat($(`#${type}_seal`).val()) || 0;
            $(`#${type}_total`).val(ujung + seal);
        }

        const fields = [
            'basah_air', 'kering_air',
            'basah_minyak', 'kering_minyak'
        ];

        fields.forEach(type => {
            $(`#${type}_ujung, #${type}_seal`).on('input', function(){
                hitungTotal(type);
            });
        });

        // Hitung total awal saat load
        fields.forEach(type => hitungTotal(type));
    });
</script>

    <script>
$(function () {

    const batchSelect = $('#kode_batch');

    function loadBatch(namaProduk, selected = null) {

        batchSelect.prop('disabled', true);
        batchSelect.html('<option value="">-- Pilih Batch --</option>');

        if (!namaProduk) return;

        let url = "{{ route('lookup.batch', ['nama_produk' => ':nama']) }}".replace(':nama', namaProduk);

        $.get(url, function (data) {

            data.forEach(function (item) {
                batchSelect.append(
                    `<option value="${item.uuid}" ${selected == item.uuid ? 'selected' : ''}>${item.kode_produksi}</option>`
                );
            });

            batchSelect.prop('disabled', false);
        });
    }

    $('#nama_produk').on('change', function () {
        loadBatch($(this).val());
    });

    let initialProduk = $('#nama_produk').val();
    let initialBatch = "{{ $prepacking->kode_produksi ?? '' }}";

    if (initialProduk) {
        loadBatch(initialProduk, initialBatch);
    }

});
</script>
@endsection
