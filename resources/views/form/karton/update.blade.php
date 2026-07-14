@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-pencil-square"></i> Edit Kontrol Labelisasi Karton
                </h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="samplingForm" action="{{ route('karton.update_qc', $karton->uuid) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- ===================== IDENTITAS DATA ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>Identitas Data Packing</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="date" class="form-control"
                                        value="{{ old('date', $karton->date) }}" {{ $karton->date ? 'readonly' : '' }}
                                        required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Varian</label>
                                    <select name="nama_produk" class="form-control selectpicker" data-live-search="true"
                                        {{ $karton->nama_produk ? 'disabled' : '' }} required>
                                        <option value="">-- Pilih Varian --</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}"
                                                {{ old('nama_produk', $karton->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- HIDDEN FIX --}}
                                    @if ($karton->nama_produk)
                                        <input type="hidden" name="nama_produk" value="{{ $karton->nama_produk }}">
                                    @endif

                                </div>

                                @php
                                    $kode = \App\Models\Mincing::where('uuid', $karton->kode_produksi)->value('kode_produksi') ?? $karton->kode_produksi;
                                @endphp

                                <div class="col-md-6">
                                    <label class="form-label">Kode Batch</label>
                                    <input type="text" class="form-control" value="{{ $kode }}" readonly>
                                    <input type="hidden" name="kode_produksi" value="{{ $karton->kode_produksi }}">
                                    <small id="kodeError" class="text-danger d-none"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== ITEM SORTIR ===================== --}}
                    <div class="card shadow-sm border-0 mb-4">

                        {{-- HEADER --}}
                        <div class="card-header bg-info text-white d-flex align-items-center">
                            <i class="bi bi-clipboard-check me-2"></i>
                            <strong>Pemeriksaan</strong>
                        </div>

                        <div class="card-body">

                            {{-- ================= WAKTU & TANGGAL ================= --}}
                            <div class="row g-3 mb-4">

                                {{-- WAKTU --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Waktu Proses
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-clock"></i>
                                        </span>

                                        <input type="time" name="waktu_mulai" class="form-control"
                                            value="{{ old('waktu_mulai', $karton->waktu_mulai) }}"
                                            {{ $karton->waktu_mulai ? 'readonly' : '' }}>

                                        <span class="input-group-text">
                                            s/d
                                        </span>

                                        <input type="time" name="waktu_selesai" class="form-control"
                                            value="{{ old('waktu_selesai', $karton->waktu_selesai) }}"
                                            {{ $karton->waktu_selesai ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                {{-- TANGGAL --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Tanggal Kedatangan
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-calendar-event"></i>
                                        </span>

                                        <input type="date" name="tgl_kedatangan" class="form-control"
                                            value="{{ old('tgl_kedatangan', $karton->tgl_kedatangan) }}"
                                            {{ $karton->tgl_kedatangan ? 'readonly' : '' }} required>
                                    </div>
                                </div>

                            </div>

                            {{-- ================= SUPPLIER ================= --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah / Tambahan</label>
                                    <input type="number" name="jumlah" class="form-control"
                                        value="{{ old('jumlah', $karton->jumlah) }}"
                                        {{ $karton->jumlah ? 'readonly' : '' }}>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Nama Supplier
                                    </label>

                                    <select name="nama_supplier" class="form-control selectpicker" data-live-search="true"
                                        {{ $karton->nama_supplier ? 'disabled' : '' }} required>

                                        <option value="">-- Pilih Supplier --</option>

                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->nama_supplier }}"
                                                {{ old('nama_supplier', $karton->nama_supplier) == $supplier->nama_supplier ? 'selected' : '' }}>
                                                {{ $supplier->nama_supplier }}
                                            </option>
                                        @endforeach

                                    </select>

                                    {{-- Hidden input supaya value tetap terkirim --}}
                                    @if ($karton->nama_supplier)
                                        <input type="hidden" name="nama_supplier" value="{{ $karton->nama_supplier }}">
                                    @endif
                                </div>

                            </div>

                            {{-- ================= LOT & FILE ================= --}}
                            <div class="row g-3">

                                {{-- LOT --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        No. Lot Karton
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-upc-scan"></i>
                                        </span>

                                        <input type="text" name="no_lot" class="form-control"
                                            placeholder="Masukkan nomor lot" value="{{ old('no_lot', $karton->no_lot) }}"
                                            {{ $karton->no_lot ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                {{-- FILE --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Kode Batch (Karton)
                                    </label>

                                    <input type="file" name="kode_karton" id="kode_karton" class="form-control"
                                        accept="image/*" {{ $karton->kode_karton ? 'disabled' : '' }}>

                                    {{-- Preview file sebelumnya --}}
                                    @if ($karton->kode_karton)
                                        @php
                                            $kartonPath = str_replace('public/', 'storage/', $karton->kode_karton);
                                        @endphp

                                        <small class="d-block mt-2">
                                            <a href="{{ asset($kartonPath) }}" target="_blank"
                                                class="text-primary text-decoration-none fw-semibold">

                                                <i class="bi bi-image me-1"></i>
                                                Lihat Gambar Sebelumnya
                                            </a>
                                        </small>
                                    @endif

                                    <small id="kode-karton-error" class="text-danger"></small>
                                </div>

                            </div>

                        </div>
                    </div>

            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <strong>Operator - KR</strong>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Operator</label>
                        <select id="nama_operator" name="nama_operator" class="form-control selectpicker"
                            data-live-search="true" required>
                            <option value="">-- Pilih Operator --</option>
                            @foreach ($operators as $operator)
                                <option value="{{ $operator->nama_karyawan }}"
                                    {{ old('nama_operator', $karton->nama_operator) == $operator->nama_karyawan ? 'selected' : '' }}>
                                    {{ $operator->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama KR</label>
                        <select id="nama_koordinator" name="nama_koordinator" class="form-control selectpicker"
                            data-live-search="true" required>
                            <option value="">-- Pilih Koordinator --</option>
                            @foreach ($koordinators as $koordinator)
                                <option value="{{ $koordinator->nama_karyawan }}"
                                    {{ old('nama_koordinator', $karton->nama_koordinator) == $koordinator->nama_karyawan ? 'selected' : '' }}>
                                    {{ $koordinator->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== CATATAN ===================== --}}
        <div class="card mb-4">
            <div class="card-header bg-light"><strong>Keterangan</strong></div>
            <div class="card-body">
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $karton->keterangan) }}</textarea>
            </div>
        </div>

        {{-- ===================== TOMBOL ===================== --}}
        <div class="d-flex justify-content-between mt-3">
            <button type="submit" id="submitBtn" class="btn btn-success">
                <i class="bi bi-save"></i> Simpan
            </button>

            <a href="{{ route('karton.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        </form>
    </div>
    </div>
    </div>

    {{-- SCRIPT --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();

            // Validasi kode produksi
            const kodeInput = document.getElementById('kode_produksi');
            const kodeError = document.getElementById('kodeError');
            const form = document.getElementById('samplingForm');

            if (!kodeInput.hasAttribute('readonly')) {
                kodeInput.addEventListener('input', function() {
                    validateKode();
                });
            }

            form.addEventListener('submit', function(e) {
                if (!kodeInput.hasAttribute('readonly') && !validateKode()) {
                    e.preventDefault();
                    alert('Kode batch tidak valid!');
                }
            });

            function validateKode() {
                let value = kodeInput.value.toUpperCase().replace(/\s+/g, '');
                kodeInput.value = value;
                kodeError.textContent = '';
                kodeError.classList.add('d-none');

                if (value.length !== 10) {
                    kodeError.textContent = "Kode batch harus 10 karakter.";
                    kodeError.classList.remove('d-none');
                    return false;
                }

                const format = /^[A-Z0-9]+$/;
                if (!format.test(value)) {
                    kodeError.textContent = "Hanya huruf besar dan angka.";
                    kodeError.classList.remove('d-none');
                    return false;
                }

                const bulanChar = value.charAt(1);
                if (!/^[A-L]$/.test(bulanChar)) {
                    kodeError.textContent = "Karakter ke-2 harus huruf bulan (Aâ€“L).";
                    kodeError.classList.remove('d-none');
                    return false;
                }

                const hari = parseInt(value.substr(2, 2), 10);
                if (isNaN(hari) || hari < 1 || hari > 31) {
                    kodeError.textContent = "Tanggal tidak valid (01â€“31).";
                    kodeError.classList.remove('d-none');
                    return false;
                }

                return true;
            }

            // Validasi file 5MB
            const fileInput = document.getElementById("kode_karton");
            const fileError = document.getElementById("kode-karton-error");
            const maxFileSize = 5 * 1024 * 1024;

            fileInput.addEventListener("change", function() {
                fileError.textContent = "";
                const file = this.files[0];
                if (file && file.size > maxFileSize) {
                    fileError.textContent = "Maksimal 5MB.";
                    this.value = "";
                }
            });
        });
    </script>
@endsection
