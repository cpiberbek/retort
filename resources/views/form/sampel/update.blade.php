@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Data Pengambilan Sampel
            </h4>

            <form id="editForm" action="{{ route('sampel.update_qc', $sampel->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampel</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" 
                                name="date" 
                                id="dateInput" 
                                class="form-control"
                                value="{{ old('date', $sampel->date) }}"
                                required
                                {{ $sampel->date ? 'readonly' : '' }}>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Sampel</label>
                                <select name="jenis_sampel" id="jenis_sampel" 
                                class="form-control selectpicker" 
                                data-live-search="true" 
                                required
                                {{ $sampel->jenis_sampel ? 'disabled' : '' }}>
                                <option value="">-- Pilih Sampel --</option>
                                <option value="Retain QC" {{ old('jenis_sampel', $sampel->jenis_sampel ?? '') == 'Retain QC' ? 'selected' : '' }}>Retain QC</option>
                                <option value="Lab Internal" {{ old('jenis_sampel', $sampel->jenis_sampel ?? '') == 'Lab Internal' ? 'selected' : '' }}>Lab Internal</option>
                                <option value="Lab Eksternal" {{ old('jenis_sampel', $sampel->jenis_sampel ?? '') == 'Lab Eksternal' ? 'selected' : '' }}>Lab Eksternal</option>
                                <option value="RND" {{ old('jenis_sampel', $sampel->jenis_sampel ?? '') == 'RND' ? 'selected' : '' }}>RND</option>
                            </select>
                            {{-- jika select disabled, tambahkan hidden input agar nilainya tetap terkirim --}}
                            @if($sampel->jenis_sampel)
                            <input type="hidden" name="jenis_sampel" value="{{ $sampel->jenis_sampel }}">
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Varian</label>
                            <input list="produkList" id="nama_produk" name="nama_produk"
                            class="form-control"
                            placeholder="Ketik atau pilih varian..."
                            required
                            value="{{ old('nama_produk', $sampel->nama_produk) }}"
                            {{ $sampel->nama_produk ? 'readonly' : '' }}>
                            <datalist id="produkList">
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}">
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <input type="text" 
                                name="kode_produksi" 
                                id="kode_produksi"
                                class="form-control" 
                                maxlength="10"
                                value="{{ old('kode_produksi', $sampel->kode_produksi) }}" 
                                required
                                {{ $sampel->kode_produksi ? 'readonly' : '' }}>
                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3"
                        placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $sampel->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('sampel.index') }}" class="btn btn-secondary">
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

{{-- hanya aktifkan select2 jika belum readonly --}}
<script>
    $(document).ready(function() {
        @if(!$sampel->nama_produk)
        $('#nama_produk').select2({
            tags: true,
            placeholder: "Ketik atau pilih nama varian...",
            allowClear: true
        });
        @endif
    });
</script>

{{-- ===================== VALIDASI KODE PRODUKSI ===================== --}}
@if(!$sampel->kode_produksi)
<script>
    const kodeInput = document.getElementById('kode_produksi');
    const kodeError = document.getElementById('kodeError');

    kodeInput.addEventListener('input', function() {
        let value = this.value.toUpperCase().replace(/\s+/g, '');
        this.value = value;
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

        // Semua valid
        kodeError.textContent = "✔ Kode batch valid.";
        kodeError.classList.remove('text-danger');
        kodeError.classList.add('text-success');
        kodeError.classList.remove('d-none');
    });
</script>
@endif
@endsection
