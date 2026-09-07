@extends('layouts.app')

@section('title', 'Update Pemeriksaan Kekuatan Magnet Trap')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
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

        /* Styling field Readonly/Locked */
        .form-control[readonly],
        .locked-input {
            background-color: #e9ecef;
            cursor: not-allowed;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-0">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">

                <h4 class="mb-1"><i class="bi bi-pencil-square"></i> Update Pemeriksaan Magnet Trap</h4>
                <p class="text-muted mb-4">Lengkapi data yang masih kosong. Data yang sudah diinput sebelumnya terkunci.</p>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form tetap submit ke route UPDATE standar --}}
                <form action="{{ route('pemeriksaan-kekuatan-magnet-trap.update', $pemeriksaanKekuatanMagnetTrap->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    {{-- CARD 1: INFORMASI UTAMA & PETUGAS --}}
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <strong><i class="bi bi-info-circle-fill"></i> Informasi Utama & Petugas</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" id="tanggal" class="form-control"
                                            value="{{ old('tanggal', $pemeriksaanKekuatanMagnetTrap->tanggal ? $pemeriksaanKekuatanMagnetTrap->tanggal->format('Y-m-d') : '') }}"
                                            {{ !empty($pemeriksaanKekuatanMagnetTrap->tanggal) ? 'readonly' : '' }}
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="kondisi_magnet_trap" class="form-label">Kondisi Magnet Trap (Visual)
                                            <span class="text-danger">*</span></label>
                                        <input type="text" name="kondisi_magnet_trap" id="kondisi_magnet_trap"
                                            class="form-control"
                                            value="{{ old('kondisi_magnet_trap', $pemeriksaanKekuatanMagnetTrap->kondisi_magnet_trap) }}"
                                            {{ !empty($pemeriksaanKekuatanMagnetTrap->kondisi_magnet_trap) ? 'readonly' : '' }}
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="petugas_qc" class="form-label">Petugas QC</label>
                                        <input type="text" name="petugas_qc" id="petugas_qc" class="form-control"
                                            value="{{ old('petugas_qc', $pemeriksaanKekuatanMagnetTrap->petugas_qc) }}"
                                            {{ !empty($pemeriksaanKekuatanMagnetTrap->petugas_qc) ? 'readonly' : '' }}>
                                    </div>
                                    <div class="mb-3">
                                        <label for="petugas_eng" class="form-label">Petugas ENG</label>
                                        <input type="text" name="petugas_eng" id="petugas_eng" class="form-control"
                                            value="{{ old('petugas_eng', $pemeriksaanKekuatanMagnetTrap->petugas_eng) }}"
                                            {{ !empty($pemeriksaanKekuatanMagnetTrap->petugas_eng) ? 'readonly' : '' }}>
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

                            <label class="form-label">Kekuatan Medan Magnet (Gauss)</label>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">
                                    Magnet yang sudah diinput sebelumnya akan terkunci.
                                </small>

                                <div class="d-flex gap-2">

                                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnTambahMagnet">
                                        <i class="bi bi-plus-circle"></i>
                                        Tambah Magnet
                                    </button>

                                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnHapusMagnet"
                                        style="display: none;">
                                        <i class="bi bi-trash"></i>
                                        Hapus Magnet
                                    </button>

                                </div>
                            </div>

                            <div class="row g-3 mb-3" id="magnetContainer">

                                {{-- Magnet 1 --}}
                                <div class="col-md-4 magnet-field" data-magnet="1">
                                    <label for="kekuatan_median_1" class="form-label small">
                                        Magnet 1
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_1" id="kekuatan_median_1"
                                        class="form-control"
                                        value="{{ old('kekuatan_median_1', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_1) }}"
                                        {{ !is_null($pemeriksaanKekuatanMagnetTrap->kekuatan_median_1) ? 'readonly' : '' }}>
                                </div>

                                {{-- Magnet 2 --}}
                                <div class="col-md-4 magnet-field" data-magnet="2">
                                    <label for="kekuatan_median_2" class="form-label small">
                                        Magnet 2
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_2" id="kekuatan_median_2"
                                        class="form-control"
                                        value="{{ old('kekuatan_median_2', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_2) }}"
                                        {{ !is_null($pemeriksaanKekuatanMagnetTrap->kekuatan_median_2) ? 'readonly' : '' }}>
                                </div>

                                {{-- Magnet 3 --}}
                                <div class="col-md-4 magnet-field" data-magnet="3">
                                    <label for="kekuatan_median_3" class="form-label small">
                                        Magnet 3
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_3" id="kekuatan_median_3"
                                        class="form-control"
                                        value="{{ old('kekuatan_median_3', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_3) }}"
                                        {{ !is_null($pemeriksaanKekuatanMagnetTrap->kekuatan_median_3) ? 'readonly' : '' }}>
                                </div>

                                {{-- Magnet 4 --}}
                                <div class="col-md-4 magnet-field
        {{ is_null($pemeriksaanKekuatanMagnetTrap->kekuatan_median_4) ? 'd-none' : '' }}"
                                    data-magnet="4">

                                    <label for="kekuatan_median_4" class="form-label small">
                                        Magnet 4
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_4" id="kekuatan_median_4"
                                        class="form-control"
                                        value="{{ old('kekuatan_median_4', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_4) }}"
                                        {{ !is_null($pemeriksaanKekuatanMagnetTrap->kekuatan_median_4) ? 'readonly' : '' }}>
                                </div>

                                {{-- Magnet 5 --}}
                                <div class="col-md-4 magnet-field
        {{ is_null($pemeriksaanKekuatanMagnetTrap->kekuatan_median_5) ? 'd-none' : '' }}"
                                    data-magnet="5">

                                    <label for="kekuatan_median_5" class="form-label small">
                                        Magnet 5
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_5" id="kekuatan_median_5"
                                        class="form-control"
                                        value="{{ old('kekuatan_median_5', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_5) }}"
                                        {{ !is_null($pemeriksaanKekuatanMagnetTrap->kekuatan_median_5) ? 'readonly' : '' }}>
                                </div>

                                {{-- Magnet 6 --}}
                                <div class="col-md-4 magnet-field
        {{ is_null($pemeriksaanKekuatanMagnetTrap->kekuatan_median_6) ? 'd-none' : '' }}"
                                    data-magnet="6">

                                    <label for="kekuatan_median_6" class="form-label small">
                                        Magnet 6
                                    </label>

                                    <input type="number" step="0.01" name="kekuatan_median_6" id="kekuatan_median_6"
                                        class="form-control"
                                        value="{{ old('kekuatan_median_6', $pemeriksaanKekuatanMagnetTrap->kekuatan_median_6) }}"
                                        {{ !is_null($pemeriksaanKekuatanMagnetTrap->kekuatan_median_6) ? 'readonly' : '' }}>
                                </div>

                            </div>

                            <hr class="my-4">

                            <label class="form-label">Parameter Setingan <span class="text-danger">*</span></label>
                            {{-- Logika untuk Radio Button: Jika data sudah ada, kunci dengan onclick return false --}}
                            @php
                                $isLocked = !is_null($pemeriksaanKekuatanMagnetTrap->parameter_sesuai);
                            @endphp

                            <div class="card p-3 {{ $isLocked ? 'bg-light' : '' }}">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="parameter_sesuai"
                                        id="param_sesuai" value="1"
                                        {{ old('parameter_sesuai', $pemeriksaanKekuatanMagnetTrap->parameter_sesuai) == 1 ? 'checked' : '' }}
                                        {{ $isLocked ? 'onclick=return(false);' : '' }} required>
                                    <label class="form-check-label" for="param_sesuai">Sesuai (√)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="parameter_sesuai"
                                        id="param_tidak_sesuai" value="0"
                                        {{ old('parameter_sesuai', $pemeriksaanKekuatanMagnetTrap->parameter_sesuai) === 0 ? 'checked' : '' }}
                                        {{ $isLocked ? 'onclick=return(false);' : '' }} required>
                                    <label class="form-check-label" for="param_tidak_sesuai">Tidak Sesuai (X)</label>
                                </div>
                                @if ($isLocked)
                                    <small class="text-muted mt-2 d-block">* Pilihan terkunci.</small>
                                @endif
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
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3"
                                    {{ !empty($pemeriksaanKekuatanMagnetTrap->keterangan) ? 'readonly' : '' }}>{{ old('keterangan', $pemeriksaanKekuatanMagnetTrap->keterangan) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-save"></i> Simpan
                            Perubahan</button>
                        <a href="{{ route('pemeriksaan-kekuatan-magnet-trap.index') }}"
                            class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
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

                // Minimal tetap 3 magnet
                if (jumlahMagnet <= 3) {
                    return;
                }

                const magnet = document.querySelector(
                    `.magnet-field[data-magnet="${jumlahMagnet}"]`
                );

                if (magnet) {

                    const input = magnet.querySelector('input');

                    /*
                     * Hanya boleh menghapus magnet yang belum
                     * tersimpan sebelumnya.
                     *
                     * Kalau sudah readonly berarti data lama.
                     */
                    if (input && input.hasAttribute('readonly')) {

                        alert(
                            'Magnet ini sudah tersimpan dan tidak dapat dihapus melalui form update.'
                        );

                        return;
                    }

                    // Bersihkan nilai input
                    if (input) {
                        input.value = '';
                    }

                    // Sembunyikan magnet
                    magnet.classList.add('d-none');
                }

                jumlahMagnet--;

                updateButton();
            });

            // ==========================================
            // UPDATE TOMBOL
            // ==========================================

            function updateButton() {

                // Tombol Tambah
                if (jumlahMagnet >= 6) {

                    btnTambah.style.display = 'none';

                } else {

                    btnTambah.style.display = 'inline-block';

                }

                // Tombol Hapus
                if (jumlahMagnet > 3) {

                    btnHapus.style.display = 'inline-block';

                } else {

                    btnHapus.style.display = 'none';

                }
            }

        });
    </script>
@endsection
