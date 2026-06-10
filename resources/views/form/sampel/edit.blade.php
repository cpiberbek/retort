@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Data Pengambilan Sampel
            </h4>

            <form id="editForm" action="{{ route('sampel.edit_spv', $sampel->uuid) }}" method="POST">
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
                                <input type="date" name="date" id="dateInput" 
                                class="form-control" 
                                value="{{ old('date', $sampel->date) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Sampel</label>
                                <select name="jenis_sampel" id="jenis_sampel" 
                                class="form-control selectpicker" data-live-search="true" required>
                                <option value="">-- Pilih Sampel --</option>
                                <option value="Retain QC" {{ old('jenis_sampel', $sampel->jenis_sampel ?? '') == 'Retain QC' ? 'selected' : '' }}>Retain QC</option>
                                <option value="Lab Internal" {{ old('jenis_sampel', $sampel->jenis_sampel ?? '') == 'Lab Internal' ? 'selected' : '' }}>Lab Internal</option>
                                <option value="Lab Eksternal" {{ old('jenis_sampel', $sampel->jenis_sampel ?? '') == 'Lab Eksternal' ? 'selected' : '' }}>Lab Eksternal</option>
                                <option value="RND" {{ old('jenis_sampel', $sampel->jenis_sampel ?? '') == 'RND' ? 'selected' : '' }}>RND</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                           <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $sampel->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
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
                                        <option value="{{ $batch->uuid }}" {{ $sampel->kode_produksi == $batch->uuid ? 'selected' : '' }}>
                                            {{ $batch->kode_produksi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
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
<script>
    $(document).ready(function() {
        $('#nama_produk').select2({
            tags: true, 
            placeholder: "Ketik atau pilih nama varian...",
            allowClear: true
        });
    });
</script>

{{-- ===================== VALIDASI KODE PRODUKSI ===================== --}}
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
    let initialBatch = "{{ $sampel->kode_produksi ?? '' }}";

    if (initialProduk) {
        loadBatch(initialProduk, initialBatch);
    }

});
</script>
@endsection
