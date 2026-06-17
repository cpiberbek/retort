@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">

                <h4 class="mb-3">
                    <i class="bi bi-plus-circle"></i>
                    Form Input Pemeriksaan Personal Hygiene dan Kesehatan Karyawan
                </h4>

                {{-- NOTE MASTER DATA --}}
                <div class="alert alert-warning mb-4">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Perhatian:</strong>
                    <ul class="mb-0 mt-2">
                        <li><b>Area pemeriksaan</b> dikelola melalui <u>Master Area</u>.</li>
                        <li><b>Nama karyawan</b> dikelola melalui <u>Master Karyawan</u>.</li>
                        <li>Apabila data area atau karyawan belum tersedia, form ini <b>tidak dapat diisi</b>.</li>
                    </ul>
                </div>

                {{-- 🔥 FIX: Tambahkan id="gmpForm" untuk intercept JavaScript --}}
                <form id="gmpForm" method="POST" action="{{ route('gmp.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- WAKTU PEMERIKSAAN --}}
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <strong>Waktu Pemeriksaan</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" id="dateInput" name="date" class="form-control"
                                        value="{{ old('date') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PEMERIKSAAN AREA --}}
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <strong>Pemeriksaan Area</strong>
                        </div>
                        <div class="card-body">

                            {{-- CATATAN --}}
                            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: .9rem">
                                <i class="bi bi-info-circle"></i>
                                <strong>Catatan:</strong>
                                <ul class="mb-0">
                                    <li>Kosongkan checkbox jika <b>sesuai standar</b>.</li>
                                    <li>Centang checkbox jika <b>tidak sesuai / tidak memakai</b>.</li>
                                </ul>
                            </div>

                            {{-- TAB AREA --}}
                            @if ($areas->count() > 0)
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    @foreach ($areas as $i => $area)
                                        <li class="nav-item">
                                            <button class="nav-link {{ $i == 0 ? 'active' : '' }}" data-bs-toggle="tab"
                                                data-bs-target="#{{ Str::slug($area->area) }}" type="button">
                                                {{ strtoupper($area->area) }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="alert alert-danger">
                                    <i class="bi bi-x-circle-fill"></i>
                                    Data area belum tersedia.
                                    Silakan lengkapi di <b>Master Area</b>.
                                </div>
                            @endif

                            {{-- ISI TAB --}}
                            @if ($areas->count() > 0)
                                <div class="tab-content">
                                    @foreach ($areas as $i => $area)
                                        @php
                                            $namaArea = $area->area;
                                            $slugArea = Str::slug($namaArea, '_');
                                            $karyawans = $karyawanByArea[$namaArea] ?? [];
                                        @endphp

                                        <div class="tab-pane fade {{ $i == 0 ? 'show active' : '' }}"
                                            id="{{ Str::slug($namaArea) }}">

                                            @if (count($karyawans) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered compact-table">
                                                        <thead class="table-secondary text-center">
                                                            <tr>
                                                                <th rowspan="3">Nama Karyawan</th>
                                                                <th colspan="16">Personal Hygiene</th>
                                                                <th colspan="7">Kesehatan</th>
                                                                <th rowspan="3">Keterangan</th>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="9">Aksesoris</th>
                                                                <th colspan="4">Atribut</th>
                                                                <th colspan="3">Personal</th>
                                                                <th rowspan="2">Diare</th>
                                                                <th rowspan="2">Demam</th>
                                                                <th rowspan="2">Luka</th>
                                                                <th rowspan="2">Batuk</th>
                                                                <th rowspan="2">Radang</th>
                                                                <th rowspan="2">Flu</th>
                                                                <th rowspan="2">Mata</th>
                                                            </tr>
                                                            <tr>
                                                                <th>Anting</th>
                                                                <th>Kalung</th>
                                                                <th>Cincin</th>
                                                                <th>Jam</th>
                                                                <th>Peniti</th>
                                                                <th>Bros</th>
                                                                <th>Payet</th>
                                                                <th>Softlens</th>
                                                                <th>Lash</th>
                                                                <th>Seragam</th>
                                                                <th>Boot</th>
                                                                <th>Masker</th>
                                                                <th>Hairnet</th>
                                                                <th>Kuku</th>
                                                                <th>Parfum</th>
                                                                <th>Make Up</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            @foreach ($karyawans as $x => $nama)
                                                                <tr>
                                                                    <td class="text-start">
                                                                        {{ $nama }}
                                                                        <input type="hidden"
                                                                            name="{{ $slugArea }}[{{ $x }}][nama_karyawan]"
                                                                            value="{{ $nama }}">
                                                                    </td>

                                                                    @foreach (['anting', 'kalung', 'cincin', 'jam_tangan', 'peniti', 'bros', 'payet', 'softlens', 'eyelashes', 'seragam', 'boot', 'masker', 'ciput_hairnet', 'kuku', 'parfum', 'make_up', 'diare', 'demam', 'luka_bakar', 'batuk', 'radang', 'influenza', 'sakit_mata'] as $attr)
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="{{ $slugArea }}[{{ $x }}][{{ $attr }}]"
                                                                                value="0">
                                                                            <input type="checkbox"
                                                                                name="{{ $slugArea }}[{{ $x }}][{{ $attr }}]"
                                                                                value="1"
                                                                                {{ old("$slugArea.$x.$attr") == 1 ? 'checked' : '' }}>
                                                                        </td>
                                                                    @endforeach

                                                                    <td>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            name="{{ $slugArea }}[{{ $x }}][keterangan]"
                                                                            value="{{ old("$slugArea.$x.keterangan") }}">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle-fill"></i>
                                                    Tidak ada karyawan pada area <b>{{ $namaArea }}</b>.
                                                    Silakan lengkapi di <u>Master Karyawan</u>.
                                                </div>
                                            @endif

                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- AKSI --}}
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-success" {{ $areas->count() == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-save"></i> Simpan Area Terpilih
                        </button>
                        <a href="{{ route('gmp.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- AUTO TANGGAL & INTERCEPT SUBMIT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Set Auto Tanggal Hari Ini
            const date = document.getElementById("dateInput");
            if (!date.value) {
                date.value = new Date().toISOString().slice(0, 10);
            }

            // 🔥 FIX LOGIK: Hanya kirim data dari TAB YANG SEDANG AKTIF
            const form = document.getElementById("gmpForm");
            form.addEventListener("submit", function(e) {
                // Cari semua tab yang TIDAK aktif (.tab-pane yang tidak punya class .active)
                const inactiveTabs = document.querySelectorAll(".tab-pane:not(.active)");
                
                inactiveTabs.forEach(function(tab) {
                    // Disable seluruh elemen input & checkbox di dalam tab non-aktif
                    // agar tidak ikut terkirim ke server Laravel
                    tab.querySelectorAll("input").forEach(function(input) {
                        input.disabled = true;
                    });
                });
            });
        });
    </script>

    {{-- STYLE --}}
    <style>
        .compact-table th,
        .compact-table td {
            padding: .3rem;
            font-size: .85rem;
            vertical-align: middle;
            text-align: center;
        }

        .compact-table td:first-child {
            min-width: 230px;
            text-align: left;
        }

        .compact-table td:last-child {
            min-width: 200px;
        }

        input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
        }
    </style>
@endsection