@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Update Pemeriksaan Personal Hygiene dan Kesehatan Karyawan (QC)
            </h4>

            <form method="POST" action="{{ route('gmp.update_qc', $gmp->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Waktu Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Waktu Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="dateInput" class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control"
                                       value="{{ old('date', $gmp->date) }}" readonly required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pemeriksaan Area --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <strong>Pemeriksaan Area</strong>
                    </div>
                    <div class="card-body">

                        {{-- Catatan Petunjuk --}}
                        <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>
                            <ul>
                               <li><b>Kosongkan</b> checkbox apabila <u>memakai lengkap atau sesuai standar</u>.</li>
                               <li><strong>Centang</strong> checkbox apabila <u><b>tidak memakai</b></u> atau <u>memakai namun tidak benar atau <b>tidak sesuai standar</b></u>.</li>
                           </ul>
                        </div>

                        {{-- Tab Dinamis Area --}}
                        <ul class="nav nav-tabs" id="areaTabs" role="tablist">
                            @foreach($areas as $index => $area)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                            id="tab-{{ Str::slug($area->area) }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#{{ Str::slug($area->area) }}"
                                            type="button" role="tab">
                                        {{ strtoupper($area->area) }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Isi Tab Dinamis --}}
                        <div class="tab-content mt-3" id="areaTabsContent">
                            @foreach($areas as $index => $area)
                                @php 
                                    $namaArea = $area->area;
                                    $slugArea = Str::slug($namaArea, '_');
                                @endphp

                                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" 
                                     id="{{ Str::slug($namaArea) }}" 
                                     role="tabpanel">

                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered text-center align-middle compact-table">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th rowspan="3">Nama Karyawan</th>
                                                    <th colspan="16">Personal Hygiene</th>
                                                    <th colspan="7">Kesehatan Karyawan</th>
                                                    <th rowspan="3">Keterangan</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="9">Aksesoris</th>
                                                    <th colspan="4">Atribut Kerja</th>
                                                    <th colspan="3">Personal</th>
                                                    <th rowspan="2">Diare</th>
                                                    <th rowspan="2">Demam</th>
                                                    <th rowspan="2">Luka Bakar</th>
                                                    <th rowspan="2">Batuk</th>
                                                    <th rowspan="2">Radang</th>
                                                    <th rowspan="2">Influenza</th>
                                                    <th rowspan="2">Sakit Mata</th>
                                                </tr>
                                                <tr>
                                                    <th>Anting</th>
                                                    <th>Kalung</th>
                                                    <th>Cincin</th>
                                                    <th>Jam Tangan</th>
                                                    <th>Peniti</th>
                                                    <th>Bros</th>
                                                    <th>Payet</th>
                                                    <th>Softlens</th>
                                                    <th>Eyelashes</th>
                                                    <th>Seragam</th>
                                                    <th>Boot</th>
                                                    <th>Masker</th>
                                                    <th>Ciput/Hairnet</th>
                                                    <th>Kuku</th>
                                                    <th>Parfum</th>
                                                    <th>Make Up</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($karyawanByArea[$namaArea] ?? [] as $i => $nama_karyawan)
                                                    @php
                                                        $rowData = $oldDataPerArea[$namaArea][$nama_karyawan] ?? [];
                                                    @endphp
                                                    <tr>
                                                        <td class="text-start ps-2">
                                                            {{ $nama_karyawan }}
                                                            <input type="hidden" name="{{ $slugArea }}[{{ $i }}][nama_karyawan]" value="{{ $nama_karyawan }}">
                                                        </td>

                                                        {{-- Checkbox Pemeriksaan --}}
                                                        @foreach([
                                                            'anting','kalung','cincin','jam_tangan','peniti','bros',
                                                            'payet','softlens','eyelashes','seragam','boot','masker',
                                                            'ciput_hairnet','kuku','parfum','make_up',
                                                            'diare','demam','luka_bakar','batuk','radang','influenza','sakit_mata'
                                                        ] as $attr)
                                                            <td>
                                                                @php
                                                                    $isFilled = isset($rowData[$attr]) && $rowData[$attr] == 1;
                                                                @endphp
                                                                @if($isFilled)
                                                                    <input type="checkbox" checked disabled onclick="return false;">
                                                                    <input type="hidden" name="{{ $slugArea }}[{{ $i }}][{{ $attr }}]" value="1">
                                                                @else
                                                                    <input type="hidden" name="{{ $slugArea }}[{{ $i }}][{{ $attr }}]" value="0">
                                                                    <input type="checkbox" name="{{ $slugArea }}[{{ $i }}][{{ $attr }}]" value="1">
                                                                @endif
                                                            </td>
                                                        @endforeach

                                                        {{-- Kolom Keterangan --}}
                                                        <td class="text-start">
                                                            @php
                                                                $hasKeterangan = !empty($rowData['keterangan']);
                                                            @endphp
                                                            <input type="text" 
                                                                   class="form-control form-control-sm w-100"
                                                                   name="{{ $slugArea }}[{{ $i }}][keterangan]"
                                                                   value="{{ $rowData['keterangan'] ?? '' }}"
                                                                   {{ $hasKeterangan ? 'readonly' : '' }}>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex justify-content-between mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Update
                            </button>
                            <a href="{{ route('gmp.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        if (!dateInput.value) {
            let now = new Date();
            let yyyy = now.getFullYear();
            let mm = String(now.getMonth() + 1).padStart(2, '0');
            let dd = String(now.getDate()).padStart(2, '0');
            dateInput.value = `${yyyy}-${mm}-${dd}`;
        }
    });
</script>

<style>
    .compact-table td, .compact-table th { padding: 0.3rem !important; font-size: 0.85rem; line-height: 1.2; vertical-align: middle; }
    .compact-table tbody td:first-child { min-width: 250px !important; width: 250px !important; text-align: left !important; padding-left: 8px !important; }
    .compact-table tbody td:last-child { min-width: 220px !important; width: 220px !important; text-align: left !important; }
    .nav-tabs .nav-link { font-weight: 600; }
    textarea.form-control { resize: none; }
    .table thead th { text-align: center; vertical-align: middle; white-space: nowrap; }
    .table tbody td { text-align: center; vertical-align: middle; }
    .table tbody td.text-start { text-align: left; }
    input[type="checkbox"] { width: 1rem; height: 1rem; margin: auto; display: block; }
    .card-header strong { display: block; text-align: left; }
    .table tbody tr:hover { background-color: #f1f1f1; }
    /* =========================================================
           🔥 FIX STICKY COLUMN NAMA KARYAWAN (RESPONSIVE ALL DEVICES) 
           ========================================================= */
        
        /* 1. Reset posisi th agar tidak bertumpuk */
        .compact-table th {
            position: static;
        }

        /* 2. Hanya kunci Nama Karyawan (baris pertama thead) dan kolom pertama tbody */
        .compact-table thead tr:first-child th:first-child,
        .compact-table tbody td:first-child {
            position: -webkit-sticky; /* Support untuk Safari/iOS */
            position: sticky;
            left: 0;
            z-index: 2;
        }

        /* 3. Background untuk Header Nama Karyawan agar tidak tembus */
        .compact-table thead tr:first-child th:first-child {
            background-color: #e2e3e5 !important; /* Warna table-secondary */
            z-index: 3; /* Lebih tinggi agar tidak tertimpa baris di bawahnya */
        }

        /* 4. Background untuk Body Nama Karyawan + Efek Shadow */
        .compact-table tbody td:first-child {
            background-color: #ffffff !important;
            box-shadow: inset -3px 0 5px -3px rgba(0,0,0,0.15);
        }

        /* =========================================================
           🔥 FIX SCROLL MOBILE & TABLET (SWIPE TANPA BLOCK TEKS)
           ========================================================= */
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important; 
            touch-action: pan-x pan-y; 
        }

        .compact-table thead tr:first-child th:first-child,
        .compact-table tbody td:first-child {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none; 
        }
</style>

@endsection
