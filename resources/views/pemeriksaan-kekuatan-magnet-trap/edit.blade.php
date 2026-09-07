@extends('layouts.app')

@section('title', 'Edit Pemeriksaan Kekuatan Magnet Trap')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Style kustom dari CRUD sebelumnya */
        body {
            background-color: #f8f9fa;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
        }

        .card-header {
            border-radius: 8px 8px 0 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-0">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">

                {{-- Header & Variabel diubah --}}
                <h4 class="mb-1"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan:
                    {{ $pemeriksaanKekuatanMagnetTrap->magnet_ke }}</h4>
                <p class="text-muted mb-4">Perbarui detail formulir di bawah ini.</p>

                {{-- Tampilkan error jika ada --}}
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Terjadi Kesalahan!</h4>
                        <p>Silakan periksa kembali input Anda:</p>
                        <hr>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Route name & Variabel diubah --}}
                <form action="{{ route('pemeriksaan-kekuatan-magnet-trap.update', $pemeriksaanKekuatanMagnetTrap->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    {{-- CARD 1: INFORMASI UTAMA & PETUGAS --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong><i class="bi bi-info-circle-fill"></i> Informasi Utama & Petugas</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal <span
                                                class="text-danger">*</span></label>
                                        {{-- Variabel diubah --}}
                                        <input type="date" name="tanggal" id="tanggal" class="form-control"
                                            value="{{ old('tanggal', $pemeriksaanKekuatanMagnetTrap->tanggal->format('Y-m-d')) }}"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="kondisi_magnet_trap" class="form-label">Kondisi Magnet Trap (Visual)
                                            <span class="text-danger">*</span></label>
                                        {{-- Variabel diubah --}}
                                        <input type="text" name="kondisi_magnet_trap" id="kondisi_magnet_trap"
                                            class="form-control" placeholder="Contoh: Bersih, tidak gempil"
                                            value="{{ old('kondisi_magnet_trap', $pemeriksaanKekuatanMagnetTrap->kondisi_magnet_trap) }}"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="petugas_qc" class="form-label">Petugas QC</label>
                                        {{-- Variabel diubah --}}
                                        <input type="text" name="petugas_qc" id="petugas_qc" class="form-control"
                                            value="{{ old('petugas_qc', $pemeriksaanKekuatanMagnetTrap->petugas_qc) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="petugas_eng" class="form-label">Petugas ENG</label>
                                        {{-- Variabel diubah --}}
                                        <input type="text" name="petugas_eng" id="petugas_eng" class="form-control"
                                            value="{{ old('petugas_eng', $pemeriksaanKekuatanMagnetTrap->petugas_eng) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: HASIL PENGECEKAN --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong><i class="bi bi-check2-circle"></i> Hasil Pengecekan</strong>
                        </div>
                        <div class="card-body">

                            {{-- KEKUATAN MEDAN MAGNET --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">
                                    Kekuatan Medan Magnet (Gauss)
                                </label>

                                <div class="d-flex gap-2">

                                    {{-- Tombol Tambah --}}
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnTambahMagnet">
                                        <i class="bi bi-plus-circle"></i>
                                        Tambah Magnet
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnHapusMagnet"
                                        style="display: none;">
                                        <i class="bi bi-trash"></i>
                                        Hapus Magnet
                                    </button>

                                </div>
                            </div>

                            <div class="row g-3 mb-3" id="magnetContainer">

                                {{-- ===================================== --}}
                                {{-- MAGNET 1 --}}
                                {{-- ===================================== --}}
                                <div class="col-md-4 magnet-field" data-magnet="1">
                                    <label for="kekuatan_median_1" class="form-label small">
                                        Magnet 1
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_1" id="kekuatan_median_1"
                                        class="form-control" placeholder="Contoh: 9000.50"
                                        value="{{ old('kekuatan_median_1', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_1) }}">
                                </div>


                                {{-- ===================================== --}}
                                {{-- MAGNET 2 --}}
                                {{-- ===================================== --}}
                                <div class="col-md-4 magnet-field" data-magnet="2">
                                    <label for="kekuatan_median_2" class="form-label small">
                                        Magnet 2
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_2" id="kekuatan_median_2"
                                        class="form-control" placeholder="Contoh: 8500.00"
                                        value="{{ old('kekuatan_median_2', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_2) }}">
                                </div>


                                {{-- ===================================== --}}
                                {{-- MAGNET 3 --}}
                                {{-- ===================================== --}}
                                <div class="col-md-4 magnet-field" data-magnet="3">
                                    <label for="kekuatan_median_3" class="form-label small">
                                        Magnet 3
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_3" id="kekuatan_median_3"
                                        class="form-control" placeholder="Contoh: 8800.75"
                                        value="{{ old('kekuatan_median_3', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_3) }}">
                                </div>


                                {{-- ===================================== --}}
                                {{-- MAGNET 4 --}}
                                {{-- ===================================== --}}
                                <div class="col-md-4 magnet-field
                                    {{ old('kekuatan_median_4', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_4) !== null &&
                                    old('kekuatan_median_4', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_4) !== ''
                                        ? ''
                                        : 'd-none' }}"
                                    data-magnet="4">

                                    <label for="kekuatan_median_4" class="form-label small">
                                        Magnet 4
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_4" id="kekuatan_median_4"
                                        class="form-control" placeholder="Contoh: 9000.00"
                                        value="{{ old('kekuatan_median_4', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_4) }}">
                                </div>


                                {{-- ===================================== --}}
                                {{-- MAGNET 5 --}}
                                {{-- ===================================== --}}
                                <div class="col-md-4 magnet-field
                                    {{ old('kekuatan_median_5', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_5) !== null &&
                                    old('kekuatan_median_5', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_5) !== ''
                                        ? ''
                                        : 'd-none' }}"
                                    data-magnet="5">

                                    <label for="kekuatan_median_5" class="form-label small">
                                        Magnet 5
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_5" id="kekuatan_median_5"
                                        class="form-control" placeholder="Contoh: 8700.25"
                                        value="{{ old('kekuatan_median_5', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_5) }}">
                                </div>


                                {{-- ===================================== --}}
                                {{-- MAGNET 6 --}}
                                {{-- ===================================== --}}
                                <div class="col-md-4 magnet-field
                                    {{ old('kekuatan_median_6', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_6) !== null &&
                                    old('kekuatan_median_6', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_6) !== ''
                                        ? ''
                                        : 'd-none' }}"
                                    data-magnet="6">

                                    <label for="kekuatan_median_6" class="form-label small">
                                        Magnet 6
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_6" id="kekuatan_median_6"
                                        class="form-control" placeholder="Contoh: 8600.00"
                                        value="{{ old('kekuatan_median_6', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_6) }}">
                                </div>

                            </div>

                            <small class="text-muted">
                                Minimal 3 magnet. Maksimal 6 magnet.
                            </small>


                            <hr class="my-4">

                            <label class="form-label">Parameter Setingan <span class="text-danger">*</span></label>
                            <div class="card p-3">
                                <div class="form-check">
                                    {{-- Variabel diubah --}}
                                    <input class="form-check-input" type="radio" name="parameter_sesuai"
                                        id="param_sesuai" value="1"
                                        {{ old('parameter_sesuai', $pemeriksaanKekuatanMagnetTrap->parameter_sesuai) ? 'checked' : '' }}
                                        required>
                                    <label class="form-check-label" for="param_sesuai">
                                        Sesuai (√)
                                    </label>
                                </div>
                                <div class="form-check">
                                    {{-- Variabel diubah --}}
                                    <input class="form-check-input" type="radio" name="parameter_sesuai"
                                        id="param_tidak_sesuai" value="0"
                                        {{ !old('parameter_sesuai', $pemeriksaanKekuatanMagnetTrap->parameter_sesuai) ? 'checked' : '' }}
                                        required>
                                    <label class="form-check-label" for="param_tidak_sesuai">
                                        Tidak Sesuai (X)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 3: KETERANGAN (OPSIONAL) --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong><i class="bi bi-paperclip"></i> Keterangan (Opsional)</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Catatan</label>
                                {{-- Variabel diubah --}}
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $pemeriksaanKekuatanMagnetTrap->keterangan) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-warning btn-lg"><i class="bi bi-check-circle"></i> Update
                            Data</button>
                        {{-- Route name diubah --}}
                        <a href="{{ route('pemeriksaan-kekuatan-magnet-trap.index') }}"
                            class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Batal</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const btnTambah = document.getElementById('btnTambahMagnet');
                const btnHapus = document.getElementById('btnHapusMagnet');

                let jumlahMagnet = 3;

                // ==========================================
                // CEK MAGNET YANG SUDAH ADA
                // ==========================================
                for (let i = 4; i <= 6; i++) {

                    const magnet = document.querySelector(
                        `.magnet-field[data-magnet="${i}"]`
                    );

                    const input = document.getElementById(
                        `kekuatan_median_${i}`
                    );

                    if (
                        magnet &&
                        input &&
                        input.value !== ''
                    ) {
                        magnet.classList.remove('d-none');
                        jumlahMagnet = i;
                    }
                }


                // ==========================================
                // UPDATE TOMBOL
                // ==========================================
                function updateButton() {

                    // Tambah
                    if (jumlahMagnet >= 6) {
                        btnTambah.style.display = 'none';
                    } else {
                        btnTambah.style.display = 'inline-block';
                    }


                    // Hapus
                    if (jumlahMagnet > 3) {
                        btnHapus.style.display = 'inline-block';
                    } else {
                        btnHapus.style.display = 'none';
                    }
                }


                // Jalankan pertama kali
                updateButton();


                // ==========================================
                // TAMBAH MAGNET
                // ==========================================
                btnTambah.addEventListener('click', function() {

                    if (jumlahMagnet >= 6) {
                        return;
                    }

                    jumlahMagnet++;

                    const magnet = document.querySelector(
                        `.magnet-field[data-magnet="${jumlahMagnet}"]`
                    );

                    if (magnet) {

                        magnet.classList.remove('d-none');

                        const input = magnet.querySelector('input');

                        if (input) {
                            input.focus();
                        }
                    }

                    updateButton();
                });


                // ==========================================
                // HAPUS MAGNET TERAKHIR
                // ==========================================
                btnHapus.addEventListener('click', function() {

                    if (jumlahMagnet <= 3) {
                        return;
                    }

                    const magnet = document.querySelector(
                        `.magnet-field[data-magnet="${jumlahMagnet}"]`
                    );

                    if (magnet) {

                        const input = magnet.querySelector('input');

                        // Kosongkan nilai
                        if (input) {
                            input.value = '';
                        }

                        // Sembunyikan
                        magnet.classList.add('d-none');
                    }

                    jumlahMagnet--;

                    updateButton();
                });

            });
        </script>
    @endpush

@endsection
