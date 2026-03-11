@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="card shadow-lg border-0">
        <div class="card-body">
            <h4 class="mb-4 fw-bold text-primary">
                <i class="bi bi-pencil-square me-2"></i>
                Edit Pemeriksaan Mincing - Emulsifying - Aging (SPV)
            </h4>

            <form id="mincingForm" action="{{ route('mincing.edit_spv', $mincing->uuid) }}" method="POST">
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
                                    value="{{ old('date', $mincing->date) }}" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift', $mincing->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $mincing->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $mincing->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->nama_produk }}"
                                            {{ old('nama_produk', $mincing->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                            {{ $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kode Batch</label>
                                <input type="text" name="kode_produksi" id="kode_produksi"
                                    class="form-control text-uppercase" maxlength="10"
                                    value="{{ old('kode_produksi', $mincing->kode_produksi) }}" required>
                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== PEMERIKSAAN / PERSIAPAN ===================== --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-info text-white fw-bold d-flex justify-content-between align-items-center">
                        <span>PEMERIKSAAN</span>
                    </div>

                    <div class="card-body bg-light">
                        {{-- Preparation --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr><th colspan="4" class="text-start">Preparation</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start fw-semibold">Waktu Mulai</td>
                                        <td><input type="time" name="waktu_mulai" class="form-control form-control-sm text-center"
                                            value="{{ old('waktu_mulai', $mincing->waktu_mulai) }}"></td>
                                        <td class="fw-bold">s/d</td>
                                        <td><input type="time" name="waktu_selesai" class="form-control form-control-sm text-center"
                                            value="{{ old('waktu_selesai', $mincing->waktu_selesai) }}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- ===================== NON-PREMIX ===================== --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered text-center align-middle" id="tabelNonPremix">
                                <thead class="table-primary">
                                    <tr>
                                        <th colspan="7" class="text-start">Bahan Baku dan Bahan Tambahan (Non-Premix)</th>
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
                                        $nonPremixData = is_array($mincing->non_premix) 
                                            ? $mincing->non_premix 
                                            : json_decode($mincing->non_premix ?? '[]', true);
                                    @endphp

                                    @if(!empty($nonPremixData) && is_array($nonPremixData))
                                        @foreach($nonPremixData as $i => $np)
                                            <tr>
                                                <td>
                                                    <select name="non_premix[{{ $i }}][nama_bahan]" class="form-control form-select-sm text-center" required>
                                                        <option value="" disabled>-- Pilih Bahan --</option>
                                                        @foreach($rawMaterials as $rm)
                                                            <option value="{{ $rm->nama_bahan_baku }}" 
                                                                {{ old("non_premix.$i.nama_bahan", $np['nama_bahan'] ?? '') == $rm->nama_bahan_baku ? 'selected' : '' }}>
                                                                {{ $rm->nama_bahan_baku }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" name="non_premix[{{ $i }}][kode_bahan]" value="{{ old("non_premix.$i.kode_bahan", $np['kode_bahan'] ?? '') }}" class="form-control form-control-sm text-center"></td>
                                                <td><input type="number" name="non_premix[{{ $i }}][suhu_bahan]" step="0.01" value="{{ old("non_premix.$i.suhu_bahan", $np['suhu_bahan'] ?? '') }}" class="form-control form-control-sm text-center"></td>
                                                <td><input type="number" name="non_premix[{{ $i }}][ph_bahan]" step="0.01" value="{{ old("non_premix.$i.ph_bahan", $np['ph_bahan'] ?? '') }}" class="form-control form-control-sm text-center"></td>
                                                <td><input type="number" name="non_premix[{{ $i }}][berat_bahan]" step="0.01" value="{{ old("non_premix.$i.berat_bahan", $np['berat_bahan'] ?? '') }}" class="form-control form-control-sm text-center"></td>
                                                <td class="text-center"><input type="checkbox" name="non_premix[{{ $i }}][sensori]" value="Oke" {{ old("non_premix.$i.sensori", $np['sensori'] ?? '') == 'Oke' ? 'checked' : '' }} class="form-check-input"></td>
                                                <td><button type="button" class="btn btn-danger btn-sm hapusBaris"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <select name="non_premix[0][nama_bahan]" class="form-control form-select-sm text-center" required>
                                                    <option value="" selected disabled>-- Pilih Bahan --</option>
                                                    @foreach($rawMaterials as $rm)
                                                        <option value="{{ $rm->nama_bahan_baku }}">{{ $rm->nama_bahan_baku }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="non_premix[0][kode_bahan]" class="form-control form-control-sm text-center"></td>
                                            <td><input type="number" name="non_premix[0][suhu_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
                                            <td><input type="number" name="non_premix[0][ph_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
                                            <td><input type="number" name="non_premix[0][berat_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
                                            <td class="text-center"><input type="checkbox" name="non_premix[0][sensori]" value="Oke" class="form-check-input"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm hapusBaris"><i class="bi bi-trash"></i></button></td>
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
                                    <tr><th colspan="5" class="text-start">Premix</th></tr>
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
                                        $premixData = is_array($mincing->premix) 
                                            ? $mincing->premix 
                                            : json_decode($mincing->premix ?? '[]', true);
                                    @endphp

                                    @if(!empty($premixData) && is_array($premixData))
                                        @foreach($premixData as $i => $px)
                                            <tr>
                                                <td><input type="text" name="premix[{{ $i }}][nama_premix]" value="{{ old("premix.$i.nama_premix", $px['nama_premix'] ?? '') }}" class="form-control form-control-sm text-center"></td>
                                                <td><input type="text" name="premix[{{ $i }}][kode_premix]" value="{{ old("premix.$i.kode_premix", $px['kode_premix'] ?? '') }}" class="form-control form-control-sm text-center"></td>
                                                <td><input type="number" name="premix[{{ $i }}][berat_premix]" step="0.01" value="{{ old("premix.$i.berat_premix", $px['berat_premix'] ?? '') }}" class="form-control form-control-sm text-center"></td>
                                                <td class="text-center"><input type="checkbox" name="premix[{{ $i }}][sensori_premix]" value="Oke" {{ old("premix.$i.sensori_premix", $px['sensori_premix'] ?? '') == 'Oke' ? 'checked' : '' }} class="form-check-input"></td>
                                                <td><button type="button" class="btn btn-danger btn-sm hapusBarisPremix"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td><input type="text" name="premix[0][nama_premix]" class="form-control form-control-sm text-center"></td>
                                            <td><input type="text" name="premix[0][kode_premix]" class="form-control form-control-sm text-center"></td>
                                            <td><input type="number" name="premix[0][berat_premix]" step="0.01" class="form-control form-control-sm text-center"></td>
                                            <td class="text-center"><input type="checkbox" name="premix[0][sensori_premix]" value="Oke" class="form-check-input"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm hapusBarisPremix"><i class="bi bi-trash"></i></button></td>
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
                                        <td class="text-start fw-semibold bg-light" style="width: 25%;">Suhu (Sebelum Grinding)</td>
                                        <td colspan="3" class="p-0">
                                            <table class="table table-borderless mb-0">
                                                <tbody id="tbodySuhuGrinding">
                                                    @php
                                                        $rawSuhu = $mincing->suhu_sebelum_grinding ?? '[]';
                                                        $suhuDataLocal = is_string($rawSuhu) ? json_decode($rawSuhu, true) : $rawSuhu;
                                                        if (!is_array($suhuDataLocal)) $suhuDataLocal = [];
                                                    @endphp
                                                    
                                                    @forelse($suhuDataLocal as $key => $item)
                                                    <tr>
                                                        <td style="width: 45%;">
                                                            <select name="suhu_grinding_input[{{$key}}][daging]" class="form-control form-select-sm">
                                                                <option value="" disabled>Pilih Daging</option>
                                                                <option value="BEEF" {{ ($item['daging'] ?? '') == 'BEEF' ? 'selected' : '' }}>BEEF</option>
                                                                <option value="SBB" {{ ($item['daging'] ?? '') == 'SBB' ? 'selected' : '' }}>SBB</option>
                                                                <option value="SBL" {{ ($item['daging'] ?? '') == 'SBL' ? 'selected' : '' }}>SBL</option>
                                                                <option value="MDM" {{ ($item['daging'] ?? '') == 'MDM' ? 'selected' : '' }}>MDM</option>
                                                                <option value="CCM" {{ ($item['daging'] ?? '') == 'CCM' ? 'selected' : '' }}>CCM</option>
                                                            </select>
                                                        </td>
                                                        <td style="width: 45%;">
                                                            <input type="number" name="suhu_grinding_input[{{$key}}][suhu]" value="{{ $item['suhu'] ?? '' }}" step="0.01" class="form-control form-control-sm text-center" placeholder="0.00">
                                                        </td>
                                                        <td style="width: 10%;">
                                                            <button type="button" class="btn btn-sm btn-danger hapusBarisSuhu"><i class="bi bi-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td style="width: 45%;">
                                                            <select name="suhu_grinding_input[0][daging]" class="form-control form-select-sm">
                                                                <option value="" selected disabled>Pilih Daging</option>
                                                                <option value="BEEF">BEEF</option>
                                                                <option value="SBB">SBB</option>
                                                                <option value="SBL">SBL</option>
                                                                <option value="MDM">MDM</option>
                                                                <option value="CCM">CCM</option>
                                                            </select>
                                                        </td>
                                                        <td style="width: 45%;">
                                                            <input type="number" name="suhu_grinding_input[0][suhu]" step="0.01" class="form-control form-control-sm text-center" placeholder="0.00">
                                                        </td>
                                                        <td style="width: 10%;">
                                                            <button type="button" class="btn btn-sm btn-danger hapusBarisSuhu"><i class="bi bi-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            <div class="p-2 border-top bg-white">
                                                <button type="button" class="btn btn-success btn-sm" id="tambahBarisSuhu">
                                                    <i class="bi bi-plus-circle"></i> Tambah Daging
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- BARIS WAKTU MIXING PREMIX --}}
                                    <tr>
                                        <td class="text-start fw-semibold bg-light" style="width: 25%;">Waktu Mixing Premix</td>
                                        <td colspan="3">
                                            <div class="input-group">
                                                <input type="number" name="waktu_mixing_premix" class="form-control text-center m-0" placeholder="0" min="0" style="height: 31px; min-height: 31px; border-right: 0;" value="{{ old('waktu_mixing_premix', $mincing->waktu_mixing_premix) }}">
                                                <span class="input-group-text bg-light text-muted d-flex align-items-center justify-content-center" style="height: 31px; min-height: 31px; font-size: 0.875rem;">Menit</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- GEL --}}
                            <table class="table table-bordered text-center align-middle mb-4">
                                <thead class="table-light">
                                    <tr><th colspan="4" class="text-start">GEL</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start fw-semibold" style="width: 25%;">Waktu Bowl Cutter</td>
                                        <td colspan="3">
                                            <div class="input-group">
                                                <input type="number" name="waktu_bowl_cutter" class="form-control text-center m-0" placeholder="0" min="0" style="height: 31px; min-height: 31px; border-right: 0;" value="{{ old('waktu_bowl_cutter', $mincing->waktu_bowl_cutter) }}">
                                                <span class="input-group-text bg-light text-muted d-flex align-items-center justify-content-center" style="height: 31px; min-height: 31px; font-size: 0.875rem;">Menit</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="text-start fw-semibold">Waktu Aging Emulsi (Menit)</td>
                                        <td><input type="time" name="waktu_aging_emulsi_awal" class="form-control form-control-sm text-center" value="{{ old('waktu_aging_emulsi_awal', $mincing->waktu_aging_emulsi_awal) }}"></td>
                                        <td class="fw-bold" style="width: 5%;">s/d</td>
                                        <td><input type="time" name="waktu_aging_emulsi_akhir" class="form-control form-control-sm text-center" value="{{ old('waktu_aging_emulsi_akhir', $mincing->waktu_aging_emulsi_akhir) }}"></td>
                                    </tr>

                                    <tr>
                                        <td class="text-start fw-semibold">Suhu Akhir Emulsi Gel (Std &lt;5°C)</td>
                                        <td colspan="3"><input type="number" name="suhu_akhir_emulsi_gel" step="0.01" class="form-control form-control-sm text-center" value="{{ old('suhu_akhir_emulsi_gel', $mincing->suhu_akhir_emulsi_gel) }}"></td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- Waktu Mixing & Emulsifying --}}
                            <table class="table table-bordered text-center align-middle">
                                <tbody>
                                    <tr>
                                        <td class="text-start fw-semibold" style="width: 25%;">Waktu Mixing</td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="waktu_mixing" class="form-control text-center m-0" placeholder="0" min="0" style="height: 31px; min-height: 31px; border-right: 0;" value="{{ old('waktu_mixing', $mincing->waktu_mixing) }}">
                                                <span class="input-group-text bg-light text-muted d-flex align-items-center justify-content-center" style="height: 31px; min-height: 31px; font-size: 0.875rem;">Menit</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fw-semibold">Suhu Akhir Mixing (Std 2–5°C)</td>
                                        <td><input type="number" name="suhu_akhir_mixing" step="0.01" class="form-control form-control-sm text-center" value="{{ old('suhu_akhir_mixing', $mincing->suhu_akhir_mixing) }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fw-semibold">Suhu Akhir Emulsifying (Std 14±2°C)</td>
                                        <td><input type="number" name="suhu_akhir_emulsi" step="0.01" class="form-control form-control-sm text-center" value="{{ old('suhu_akhir_emulsi', $mincing->suhu_akhir_emulsi) }}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white fw-bold">Catatan</div>
                    <div class="card-body bg-light">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $mincing->catatan) }}</textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('mincing.index') }}" class="btn btn-secondary px-4">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                </div>

            </form>

            <hr>
            <div id="resultArea"></div>

        </div>
    </div>
</div>

{{-- ===================== SCRIPT & ASSETS ===================== --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
$(document).ready(function(){
    $('.selectpicker').selectpicker();
});
</script>

{{-- Auto date & shift (tetap set jika field kosong) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('dateInput');
    const shiftInput = document.getElementById('shiftInput');

    if (!dateInput.value) {
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth()+1).padStart(2,'0');
        const dd = String(now.getDate()).padStart(2,'0');
        dateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    if (!shiftInput.value) {
        const hh = new Date().getHours();
        if (hh >= 7 && hh < 15) shiftInput.value = "1";
        else if (hh >= 15 && hh < 23) shiftInput.value = "2";
        else shiftInput.value = "3";
    }
});
</script>

{{-- Validasi Kode Produksi --}}
<script>
$(function(){
    const kodeInput = $('#kode_produksi');
    const kodeError = $('#kodeError');
    const form = $('#mincingForm');

    function validateKode(){
        let value = kodeInput.val().toUpperCase().replace(/\s+/g, '');
        kodeInput.val(value);
        kodeError.text('').addClass('d-none');

        if (value.length !== 10) {
            kodeError.text('Kode Batch harus 10 karakter').removeClass('d-none');
            return false;
        }
        if (!/^[A-Z0-9]+$/.test(value)) {
            kodeError.text('Hanya huruf besar & angka').removeClass('d-none');
            return false;
        }
        return true;
    }

    kodeInput.on('input', validateKode);

    form.on('submit', function(e){
        if (!validateKode()) {
            e.preventDefault();
            alert('Kode Batch tidak valid! Periksa kembali.');
            kodeInput.focus();
        }
    });
});
</script>

{{-- Tambah / Hapus Row Dinamis --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Definisi Elemen Tabel
    const tbodyNon = document.getElementById('tbodyNonPremix');
    const tbodyPremix = document.getElementById('tbodyPremix');
    const tbodySuhu = document.getElementById('tbodySuhuGrinding');

    // Index Counter berdasarkan jumlah baris saat ini
    let indexNon = tbodyNon ? tbodyNon.querySelectorAll('tr').length : 0;
    let indexPremix = tbodyPremix ? tbodyPremix.querySelectorAll('tr').length : 0;
    let indexSuhu = tbodySuhu ? tbodySuhu.querySelectorAll('tr').length : 0;

    // 1. Tambah Non-Premix
    const btnTambahNon = document.getElementById('tambahBarisNonPremix');
    if (btnTambahNon) {
        btnTambahNon.addEventListener('click', function() {
            let optionBahan = `<option value="" selected disabled>-- Pilih Bahan --</option>`;
            @foreach($rawMaterials as $rm)
                optionBahan += `<option value="{{ $rm->nama_bahan_baku }}">{{ $rm->nama_bahan_baku }}</option>`;
            @endforeach

            const row = `
            <tr>
                <td>
                    <select name="non_premix[${indexNon}][nama_bahan]" class="form-control form-select-sm text-center" required>
                        ${optionBahan}
                    </select>
                </td>
                <td><input type="text" name="non_premix[${indexNon}][kode_bahan]" class="form-control form-control-sm text-center"></td>
                <td><input type="number" name="non_premix[${indexNon}][suhu_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
                <td><input type="number" name="non_premix[${indexNon}][ph_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
                <td><input type="number" name="non_premix[${indexNon}][berat_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
                <td class="text-center"><input type="checkbox" name="non_premix[${indexNon}][sensori]" value="Oke" class="form-check-input"></td>
                <td><button type="button" class="btn btn-danger btn-sm hapusBaris"><i class="bi bi-trash"></i></button></td>
            </tr>`;
            tbodyNon.insertAdjacentHTML('beforeend', row);
            indexNon++;
        });
    }

    // 2. Tambah Premix
    const btnTambahPremix = document.getElementById('tambahBarisPremix');
    if (btnTambahPremix) {
        btnTambahPremix.addEventListener('click', function() {
            const row = `
            <tr>
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

    // 3. Tambah Suhu Grinding
    const btnTambahSuhu = document.getElementById('tambahBarisSuhu');
    if (btnTambahSuhu) {
        btnTambahSuhu.addEventListener('click', function() {
            const row = `
            <tr>
                <td style="width: 45%;">
                    <select name="suhu_grinding_input[${indexSuhu}][daging]" class="form-control form-select-sm">
                        <option value="" selected disabled>Pilih Daging</option>
                        <option value="BEEF">BEEF</option>
                        <option value="SBB">SBB</option>
                        <option value="SBL">SBL</option>
                        <option value="MDM">MDM</option>
                        <option value="CCM">CCM</option>
                    </select>
                </td>
                <td style="width: 45%;">
                    <input type="number" name="suhu_grinding_input[${indexSuhu}][suhu]" step="0.01" class="form-control form-control-sm text-center" placeholder="0.00">
                </td>
                <td style="width: 10%;">
                    <button type="button" class="btn btn-sm btn-danger hapusBarisSuhu"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
            tbodySuhu.insertAdjacentHTML('beforeend', row);
            indexSuhu++;
        });
    }

    // Event Delegation untuk Semua Tombol Hapus Baris
    document.addEventListener('click', function(e) {
        if (e.target.closest('.hapusBaris')) e.target.closest('tr').remove();
        if (e.target.closest('.hapusBarisPremix')) e.target.closest('tr').remove();
        if (e.target.closest('.hapusBarisSuhu')) {
            if (tbodySuhu.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert("Minimal harus ada satu input suhu.");
            }
        }
    });
});
</script>

@endsection