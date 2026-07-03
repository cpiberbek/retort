@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-3">
                <i class="bi bi-pencil-square"></i>
                Edit Pemeriksaan Personal Hygiene dan Kesehatan Karyawan (SPV)
            </h4>

            {{-- NOTE MASTER DATA --}}
            <div class="alert alert-warning mb-4">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Perhatian:</strong>
                <ul class="mb-0 mt-2">
                    <li><b>Area pemeriksaan</b> dikelola melalui <u>Master Area</u>.</li>
                    <li><b>Nama karyawan</b> dikelola melalui <u>Master Karyawan</u>.</li>
                    <li>Data yang sudah diinput sebelumnya <b>ditampilkan secara otomatis</b> dan dapat diubah.</li>
                </ul>
            </div>

            <form id="editForm" method="POST" action="{{ route('gmp.edit_spv', $gmp->uuid) }}" enctype="multipart/form-data">
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
                                    value="{{ old('date', $gmp->date) }}" required>
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
                        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: .9rem">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>
                            <ul class="mb-0">
                                <li>Kosongkan checkbox jika <b>sesuai standar</b>.</li>
                                <li>Centang checkbox jika <b>tidak sesuai / tidak memakai</b>.</li>
                            </ul>
                        </div>

                        {{-- TAB AREA --}}
                        @if (count($areas) > 0)
                            <ul class="nav nav-tabs mb-3" id="areaTabs" role="tablist">
                                @foreach ($areas as $i => $area)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $i == 0 ? 'active' : '' }}"
                                            id="tab-{{ Str::slug($area->area) }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#{{ Str::slug($area->area) }}"
                                            type="button" role="tab">
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
                        @if (count($areas) > 0)
                            <div class="tab-content" id="areaTabsContent">
                                @foreach ($areas as $i => $area)
                                    @php
                                        $namaArea = $area->area;
                                        $slugArea = Str::slug($namaArea, '_');
                                        $karyawans = $karyawanByArea[$namaArea] ?? [];
                                    @endphp

                                    <div class="tab-pane fade {{ $i == 0 ? 'show active' : '' }}"
                                        id="{{ Str::slug($namaArea) }}"
                                        role="tabpanel">

                                        @if (count($karyawans) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered compact-table">
                                                    <thead class="table-secondary text-center">
                                                        <tr>
                                                            <th rowspan="3" style="text-align: left; width: 1%; white-space: nowrap;">Nama<br>Karyawan</th>
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
                                                        @foreach ($karyawans as $x => $nama)
                                                            @php
                                                                $rowData = $oldDataPerArea[$namaArea][$nama] ?? [];
                                                            @endphp
                                                            <tr>
                                                                <td class="text-start ps-2">
                                                                    {{ $nama }}
                                                                    <input type="hidden"
                                                                        name="{{ $slugArea }}[{{ $x }}][nama_karyawan]"
                                                                        value="{{ $nama }}">
                                                                </td>

                                                                @foreach (['anting','kalung','cincin','jam_tangan','peniti','bros','payet','softlens','eyelashes','seragam','boot','masker','ciput_hairnet','kuku','parfum','make_up','diare','demam','luka_bakar','batuk','radang','influenza','sakit_mata'] as $attr)
                                                                    <td class="text-center">
                                                                        <input type="hidden"
                                                                            name="{{ $slugArea }}[{{ $x }}][{{ $attr }}]"
                                                                            value="0">
                                                                        <input type="checkbox"
                                                                            name="{{ $slugArea }}[{{ $x }}][{{ $attr }}]"
                                                                            value="1"
                                                                            {{ isset($rowData[$attr]) && $rowData[$attr] == 1 ? 'checked' : '' }}>
                                                                    </td>
                                                                @endforeach

                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        name="{{ $slugArea }}[{{ $x }}][keterangan]"
                                                                        value="{{ $rowData['keterangan'] ?? '' }}">
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

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success" {{ count($areas) == 0 ? 'disabled' : '' }}>
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('gmp.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>



<style>
    .compact-table td, .compact-table th { padding: 0.3rem !important; font-size: 0.85rem; line-height: 1.2; vertical-align: middle; }
    .compact-table tbody td:first-child { white-space: nowrap !important; text-align: left !important; padding-left: 8px !important; }
    .compact-table tbody td:last-child { min-width: 220px !important; width: 220px !important; text-align: left !important; }
    .nav-tabs .nav-link { font-weight: 600; }
    .table thead th { text-align: center; vertical-align: middle; white-space: nowrap; }
    .table tbody td { text-align: center; vertical-align: middle; }
    .table tbody td.text-start { text-align: left; }
    input[type="checkbox"] { width: 1rem; height: 1rem; margin: auto; display: block; }
    .table tbody tr:hover { background-color: #f1f1f1; }

    /* Sticky kolom Nama Karyawan */
    .compact-table th { position: static; }
    .compact-table thead tr:first-child th:first-child,
    .compact-table tbody td:first-child {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 2;
    }
    .compact-table thead tr:first-child th:first-child {
        background-color: #e2e3e5 !important;
        z-index: 3;
    }
    .compact-table tbody td:first-child {
        background-color: #ffffff !important;
        box-shadow: inset -3px 0 5px -3px rgba(0,0,0,0.15);
    }

    /* Scroll mobile */
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
