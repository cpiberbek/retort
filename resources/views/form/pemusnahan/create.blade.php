@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Pemusnahan Barang
            </h4>

            <form id="pemusnahanForm" action="{{ route('pemusnahan.store') }}" method="POST">
                @csrf

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <input type="text" name="kode_produksi" id="kode_produksi" class="form-control" maxlength="10" required>
                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="expired_date" id="expired_date" class="form-control">
                                <small class="text-muted">Tanggal ini dihitung otomatis 7 bulan dari kode batch</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Analisa Masalah</strong></div>
                    <div class="card-body">
                        <textarea name="analisa" class="form-control" rows="3" placeholder="Tambahkan analisa bila ada">{{ old('analisa', $data->analisa ?? '') }}</textarea>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $data->keterangan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('pemusnahan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== SCRIPT ===================== --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();
    });

        // Set tanggal, waktu, dan shift otomatis
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.getElementById("timeInput");

        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const hh = String(now.getHours()).padStart(2, '0');
        const min = String(now.getMinutes()).padStart(2, '0');

        dateInput.value = `${yyyy}-${mm}-${dd}`;
        timeInput.value = `${hh}:${min}`;
    });

        // Validasi kode produksi dan generate Exp Date otomatis
    const kodeInput = document.getElementById('kode_produksi');
    const expDateInput = document.getElementById('expired_date');
    const kodeError = document.getElementById('kodeError');

    kodeInput.addEventListener('input', function () {
        let value = this.value.toUpperCase().replace(/\s+/g, '');
        this.value = value;
        kodeError.textContent = '';
        kodeError.classList.add('d-none');
        expDateInput.value = '';

        if (value.length !== 10) {
            kodeError.textContent = "Kode batch harus terdiri dari 10 karakter.";
            kodeError.classList.remove('d-none');
            return;
        }

        const format = /^[A-Z0-9]+$/;
        if (!format.test(value)) {
            kodeError.textContent = "Kode batch hanya boleh huruf besar dan angka.";
            kodeError.classList.remove('d-none');
            return;
        }

        const bulanChar = value.charAt(1);
        const validBulan = /^[A-L]$/;
        if (!validBulan.test(bulanChar)) {
            kodeError.textContent = "Karakter ke-2 harus huruf bulan (A–L).";
            kodeError.classList.remove('d-none');
            return;
        }

        const hariStr = value.substr(2, 2);
        const hari = parseInt(hariStr, 10);
        if (isNaN(hari) || hari < 1 || hari > 31) {
            kodeError.textContent = "Karakter ke-3 dan ke-4 harus tanggal valid (01–31).";
            kodeError.classList.remove('d-none');
            return;
        }

        const bulanMap = { A: 0, B: 1, C: 2, D: 3, E: 4, F: 5, G: 6, H: 7, I: 8, J: 9, K: 10, L: 11 };
        const bulanIndex = bulanMap[bulanChar];
        const tahun = new Date().getFullYear();

        let expDate = new Date(tahun, bulanIndex, hari);
        expDate.setMonth(expDate.getMonth() + 7);

        const yyyy = expDate.getFullYear();
        const mm = String(expDate.getMonth() + 1).padStart(2, '0');
        const dd = String(expDate.getDate()).padStart(2, '0');
        expDateInput.value = `${yyyy}-${mm}-${dd}`;
    });

</script>
@endpush
@endsection
