@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Data Sampling Produk
            </h4>

            <form id="samplingForm" action="{{ route('sampling.update_qc', $sampling->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">
                            {{-- TANGGAL --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                value="{{ old('date', $sampling->date) }}"
                                {{ $sampling->date ? 'readonly' : '' }} required>

                                @if($sampling->date)
                                <input type="hidden" name="date" value="{{ $sampling->date }}">
                                @endif
                            </div>

                            {{-- SHIFT --}}
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control"
                                {{ $sampling->shift ? 'disabled' : '' }} required>
                                <option value="">-- Pilih Shift --</option>
                                <option value="1" {{ $sampling->shift == '1' ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ $sampling->shift == '2' ? 'selected' : '' }}>Shift 2</option>
                                <option value="3" {{ $sampling->shift == '3' ? 'selected' : '' }}>Shift 3</option>
                            </select>

                            @if($sampling->shift)
                            <input type="hidden" name="shift" value="{{ $sampling->shift }}">
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        {{-- JENIS SAMPLING --}}
                        <div class="col-md-6">
                            <label class="form-label">Jenis Sampling</label>
                            <input type="text" name="jenis_sampel" class="form-control"
                            value="{{ old('jenis_sampel', $sampling->jenis_sampel) }}"
                            {{ $sampling->jenis_sampel ? 'readonly' : '' }} required>

                            @if($sampling->jenis_sampel)
                            <input type="hidden" name="jenis_sampel" value="{{ $sampling->jenis_sampel }}">
                            @endif
                        </div>

                        {{-- JENIS KEMASAN --}}
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kemasan</label>
                            <select name="jenis_kemasan" class="form-control selectpicker"
                            data-live-search="true" {{ $sampling->jenis_kemasan ? 'disabled' : '' }} required>
                            <option value="">-- Pilih Kemasan --</option>
                            <option value="Pouch" {{ $sampling->jenis_kemasan == 'Pouch' ? 'selected' : '' }}>Pouch</option>
                            <option value="Toples" {{ $sampling->jenis_kemasan == 'Toples' ? 'selected' : '' }}>Toples</option>
                            <option value="Box" {{ $sampling->jenis_kemasan == 'Box' ? 'selected' : '' }}>Box</option>
                        </select>

                        @if($sampling->jenis_kemasan)
                        <input type="hidden" name="jenis_kemasan" value="{{ $sampling->jenis_kemasan }}">
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    {{-- NAMA PRODUK --}}
                    <div class="col-md-6">
                        <label class="form-label">Nama Varian</label>
                        <select name="nama_produk" class="form-control selectpicker"
                        data-live-search="true" {{ $sampling->nama_produk ? 'disabled' : '' }} required>
                        <option value="">-- Pilih Varian --</option>
                        @foreach($produks as $produk)
                        <option value="{{ $produk->nama_produk }}"
                            {{ $sampling->nama_produk == $produk->nama_produk ? 'selected' : '' }}>
                            {{ $produk->nama_produk }}
                        </option>
                        @endforeach
                    </select>

                    @if($sampling->nama_produk)
                    <input type="hidden" name="nama_produk" value="{{ $sampling->nama_produk }}">
                    @endif
                </div>

                {{-- KODE PRODUKSI --}}
                <div class="col-md-6">
                    <label class="form-label">Kode Batch</label>
                    <input type="text" name="kode_produksi" id="kode_produksi"
                    class="form-control" maxlength="10"
                    value="{{ old('kode_produksi', $sampling->kode_produksi) }}"
                    {{ $sampling->kode_produksi ? 'readonly' : '' }} required>
                    <small id="kodeError" class="text-danger d-none"></small>

                    @if($sampling->kode_produksi)
                    <input type="hidden" name="kode_produksi" value="{{ $sampling->kode_produksi }}">
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== ITEM SORTIR ===================== --}}
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <strong>Item Sortir</strong>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" step="0.01" 
                    value="{{ old('jumlah', $sampling->jumlah) }}"
                    {{ $sampling->jumlah ? 'readonly' : '' }} required>

                    @if($sampling->jumlah)
                    <input type="hidden" name="jumlah" value="{{ $sampling->jumlah }}" step="0.01">
                    @endif
                </div>
            </div>

            @php
            $fields = [
            'jamur','lendir','klip_tajam','pin_hole','air_trap_pvdc',
            'air_trap_produk','keriput','bengkok','non_kode','over_lap',
            'kecil','terjepit','double_klip','seal_halus','basah','dll'
            ];
            @endphp

            <div class="row mb-3">
                @foreach($fields as $field)
                <div class="col-md-3 mb-3">
                    <label class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>

                    <input type="number" name="{{ $field }}" class="form-control" step="0.01" 
                    value="{{ old($field, $sampling->$field) }}"
                    {{ $sampling->$field !== null ? 'readonly' : '' }}>

                    @if($sampling->$field !== null)
                    <input type="hidden" name="{{ $field }}" value="{{ $sampling->$field }}" step="0.01">
                    @endif
                </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- ===================== CATATAN ===================== --}}
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>Catatan</strong></div>
        <div class="card-body">
            <textarea name="catatan" class="form-control" rows="3"
            placeholder="Tambahkan catatan bila ada">{{ old('catatan', $sampling->catatan) }}</textarea>
        </div>
    </div>

    {{-- ===================== TOMBOL ===================== --}}
    <div class="d-flex justify-content-between mt-3">
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-save"></i> Update
        </button>
        <a href="{{ route('sampling.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Batal
        </a>
    </div>

</form>
</div>
</div>
</div>

{{-- ===================== SCRIPT ===================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

{{-- VALIDASI KODE PRODUKSI --}}
<script>
    $(document).ready(function(){
        const kodeInput = document.getElementById('kode_produksi');
        const kodeError = document.getElementById('kodeError');
        const form = document.getElementById('samplingForm');

        // Hanya validasi jika kode_produksi MASIH BISA diedit
        if (!kodeInput.hasAttribute('readonly')) {

            kodeInput.addEventListener('input', function() {
                validateKode();
            });

            form.addEventListener('submit', function(e) {
                if (!validateKode()) {
                    e.preventDefault();
                    alert('Kode batch tidak valid! Periksa kembali sebelum menyimpan.');
                    kodeInput.focus();
                }
            });
        }

        function validateKode() {
            let value = kodeInput.value.toUpperCase().replace(/\s+/g, '');
            kodeInput.value = value;
            kodeError.textContent = '';
            kodeError.classList.add('d-none');

            if (value.length !== 10) {
                kodeError.textContent = "Kode batch harus terdiri dari 10 karakter.";
                kodeError.classList.remove('d-none');
                return false;
            }

            const format = /^[A-Z0-9]+$/;
            if (!format.test(value)) {
                kodeError.textContent = "Kode batch hanya boleh huruf besar dan angka.";
                kodeError.classList.remove('d-none');
                return false;
            }

            const bulanChar = value.charAt(1);
            const validBulan = /^[A-L]$/;
            if (!validBulan.test(bulanChar)) {
                kodeError.textContent = "Karakter ke-2 harus huruf bulan (A–L).";
                kodeError.classList.remove('d-none');
                return false;
            }

            const hariStr = value.substr(2, 2);
            const hari = parseInt(hariStr, 10);
            if (isNaN(hari) || hari < 1 || hari > 31) {
                kodeError.textContent = "Karakter ke-3 dan ke-4 harus tanggal valid (01–31).";
                kodeError.classList.remove('d-none');
                return false;
            }

            return true;
        }
    });
</script>

@endsection
