@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pengecekan Pemasakan
            </h4>

            <form id="pvdcForm" action="{{ route('pemasakan.update_qc', $pemasakan->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ====== IDENTIFIKASI ====== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>IDENTIFIKASI</strong></div>
                    <div class="card-body">
                        {{-- Baris 1 --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control"
                                value="{{ old('date', $pemasakan->date) }}"
                                {{ $pemasakan->date ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select class="form-control" disabled>
                                    @foreach([1,2,3] as $s)
                                    <option value="{{ $s }}" {{ old('shift', $pemasakan->shift) == $s ? 'selected' : '' }}>Shift {{ $s }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="shift" value="{{ $pemasakan->shift }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select class="form-control selectpicker" data-live-search="true" disabled>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $pemasakan->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="nama_produk" value="{{ $pemasakan->nama_produk }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Chamber</label>
                                <select class="form-control selectpicker" data-live-search="true" disabled>
                                    @foreach($list_chambers as $list_chamber)
                                    <option value="{{ $list_chamber->nama_mesin }}"
                                        {{ old('no_chamber', $data->no_chamber ?? '') == $list_chamber->nama_mesin ? 'selected' : '' }}>
                                        {{ $list_chamber->nama_mesin }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="no_chamber" value="{{ $pemasakan->no_chamber }}">
                            </div>
                        </div>

                        {{-- Baris 3 --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                @php
                                $kodeProduksi = old('kode_produksi');

                                if (!$kodeProduksi) {
                                    if (is_array($pemasakan->kode_produksi)) {
                                        $kodeProduksi = $pemasakan->kode_produksi;
                                    } elseif (is_string($pemasakan->kode_produksi)) {
                                        $kodeProduksi = explode('/', $pemasakan->kode_produksi);
                                    } else {
                                        $kodeProduksi = [];
                                    }
                                }
                                @endphp

                                @foreach($kodeProduksi as $i => $kp)
                                <input type="text"
                                name="kode_produksi[]"
                                class="form-control mb-2"
                                value="{{ $kp }}"
                                {{ $pemasakan->kode_produksi ? 'readonly' : '' }}>
                                @endforeach

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Berat Varian (gram)</label>
                                <input type="number" name="berat_produk" class="form-control" step="0.1"
                                value="{{ old('berat_produk', $pemasakan->berat_produk) }}"
                                {{ $pemasakan->berat_produk ? 'readonly' : '' }}>
                            </div>
                        </div>

                        {{-- Baris 4 --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Suhu Varian (°C)</label>
                                <input type="number" name="suhu_produk" class="form-control" step="0.1"
                                value="{{ old('suhu_produk', $pemasakan->suhu_produk) }}"
                                {{ $pemasakan->suhu_produk ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Tray</label>
                                @php
                                $jumlahTray = old('jumlah_tray');

                                if (!$jumlahTray) {
                                    if (is_array($pemasakan->jumlah_tray)) {
                                        $jumlahTray = $pemasakan->jumlah_tray;
                                    } elseif (is_string($pemasakan->jumlah_tray)) {
                                        $jumlahTray = explode('/', $pemasakan->jumlah_tray);
                                    } else {
                                        $jumlahTray = [];
                                    }
                                }
                                @endphp


                                @foreach($jumlahTray as $jt)
                                <input type="text"
                                name="jumlah_tray[]"
                                class="form-control mb-2"
                                value="{{ $jt }}"
                                {{ $pemasakan->jumlah_tray ? 'readonly' : '' }}>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

                @php $cooking = json_decode($pemasakan->cooking, true); @endphp

                {{-- ================= PERSIAPAN ================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>PERSIAPAN</strong></div>
                    <div class="card-body">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Parameter</th>
                                    <th>Satuan</th>
                                    <th>Standar</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Tekanan Angin</td>
                                    <td>Kg/cm²</td>
                                    <td>5 – 8</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[tekanan_angin]"
                                        value="{{ $cooking['tekanan_angin'] ?? '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['tekanan_angin']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tekanan</td>
                                    <td>Kg/cm²</td>
                                    <td>6 - 9</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[tekanan_steam]"
                                        value="{{ $cooking['tekanan_steam'] ?? '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['tekanan_steam']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tekanan</td>
                                    <td>Kg/cm²</td>
                                    <td>2 - 2.5</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[tekanan_air]"
                                        value="{{ $cooking['tekanan_air'] ?? '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['tekanan_air']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- ================= PEMANASAN AWAL ================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>PEMANASAN AWAL</strong></div>
                    <div class="card-body">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Parameter</th>
                                    <th>Satuan</th>
                                    <th>Standar</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Suhu Air</td>
                                    <td>°C</td>
                                    <td>100 - 110</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[suhu_air_awal]"
                                        value="{{ $cooking['suhu_air_awal'] ?? '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['suhu_air_awal']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0.26</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[tekanan_awal]"
                                        value="{{ $cooking['tekanan_awal'] ?? '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['tekanan_awal']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td>1.5 - 2.5 menit</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_mulai_awal]"
                                        value="{{ isset($cooking['waktu_mulai_awal']) ? date('H:i', strtotime($cooking['waktu_mulai_awal'])) : '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['waktu_mulai_awal']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td>1.5 - 2.5 menit</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_selesai_awal]"
                                        value="{{ isset($cooking['waktu_selesai_awal']) ? date('H:i', strtotime($cooking['waktu_selesai_awal'])) : '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['waktu_selesai_awal']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= PROSES PEMANASAN ================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>PROSES PEMANASAN</strong></div>
                    <div class="card-body">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Parameter</th>
                                    <th>Satuan</th>
                                    <th>Standar</th>
                                    <th>Alternatif</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Suhu Air</td>
                                    <td>°C</td>
                                    <td>121.2</td>
                                    <td>119</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[suhu_air_proses]"
                                        value="{{ $cooking['suhu_air_proses'] ?? '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['suhu_air_proses']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tekanan</td>
                                    <td>Mpa</td>
                                    <td colspan="2" class="text-center align-middle">0.26</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[tekanan_proses]"
                                        value="{{ $cooking['tekanan_proses'] ?? '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['tekanan_proses']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="2" class="text-center align-middle">8 - 10 menit</td>
                                    <td rowspan="2" class="text-center align-middle">8 - 10 menit</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_mulai_proses]"
                                        value="{{ isset($cooking['waktu_mulai_proses']) ? date('H:i', strtotime($cooking['waktu_mulai_proses'])) : '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['waktu_mulai_proses']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_selesai_proses]"
                                        value="{{ isset($cooking['waktu_selesai_proses']) ? date('H:i', strtotime($cooking['waktu_selesai_proses'])) : '' }}"
                                        class="form-control text-center"
                                        {{ isset($cooking['waktu_selesai_proses']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= STERILISASI ================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>STERILISASI</strong></div>
                    <div class="card-body">
                        <table class="table table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Sterilisasi</th>
                                    <th>Satuan</th>
                                    <th>Standar</th>
                                    <th>Alternatif</th>
                                    <th colspan="4">Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Field numerik --}}
                                @foreach(['suhu_air_sterilisasi','thermometer_retort','tekanan_sterilisasi'] as $field)
                                <tr>
                                    <td class="text-start">{{ ucwords(str_replace('_',' ',$field)) }}</td>
                                    <td>{{ $field=='tekanan_sterilisasi'?'Mpa':'°C' }}</td>
                                    <td>{{ $field=='tekanan_sterilisasi'?0.26:121.2 }}</td>
                                    <td>{{ $field=='tekanan_sterilisasi'?0.26:119 }}</td>
                                    @foreach(range(0,3) as $i)
                                    <td>
                                        <input type="number" step="0.01" name="cooking[{{ $field }}][]"
                                        value="{{ $cooking[$field][$i] ?? '' }}"
                                        class="form-control form-control-sm text-center"
                                        {{ isset($cooking[$field][$i]) ? 'readonly' : '' }}>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach

                                {{-- Field waktu --}}
                                <tr>
                                    <td class="text-start">Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="3" class="align-middle text-center">12 menit</td>
                                    <td rowspan="3" class="align-middle text-center">16 menit</td>
                                    <td colspan="4">
                                        <input type="time" name="cooking[waktu_mulai_sterilisasi]"
                                        value="{{ $cooking['waktu_mulai_sterilisasi'] ?? '' }}"
                                        class="form-control form-control-sm text-center"
                                        {{ isset($cooking['waktu_mulai_sterilisasi']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Pengecekan</td>
                                    <td>WIB</td>
                                    @foreach(range(0,3) as $i)
                                    <td>
                                        <input type="time" name="cooking[waktu_pengecekan_sterilisasi][]"
                                        value="{{ $cooking['waktu_pengecekan_sterilisasi'][$i] ?? '' }}"
                                        class="form-control form-control-sm text-center"
                                        {{ isset($cooking['waktu_pengecekan_sterilisasi'][$i]) ? 'readonly' : '' }}>
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td colspan="4">
                                        <input type="time" name="cooking[waktu_selesai_sterilisasi]"
                                        value="{{ $cooking['waktu_selesai_sterilisasi'] ?? '' }}"
                                        class="form-control form-control-sm text-center"
                                        {{ isset($cooking['waktu_selesai_sterilisasi']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- ================= PENDINGINAN AWAL ================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>PENDINGINAN AWAL</strong></div>
                    <div class="card-body">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Parameter</th>
                                    <th>Satuan</th>
                                    <th>Standar</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Suhu Air</td>
                                    <td>°C</td>
                                    <td>30 - 35</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[suhu_air_pendinginan_awal]"
                                        value="{{ $cooking['suhu_air_pendinginan_awal'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['suhu_air_pendinginan_awal']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0.26</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[tekanan_pendinginan_awal]"
                                        value="{{ $cooking['tekanan_pendinginan_awal'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['tekanan_pendinginan_awal']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="2" class="text-center align-middle">3 - 6 menit</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_mulai_pendinginan_awal]"
                                        value="{{ $cooking['waktu_mulai_pendinginan_awal'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['waktu_mulai_pendinginan_awal']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_selesai_pendinginan_awal]"
                                        value="{{ $cooking['waktu_selesai_pendinginan_awal'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['waktu_selesai_pendinginan_awal']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= PENDINGINAN ================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>PENDINGINAN</strong></div>
                    <div class="card-body">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Parameter</th>
                                    <th>Satuan</th>
                                    <th>Standar</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Suhu Air</td>
                                    <td>°C</td>
                                    <td>50 ± 3</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[suhu_air_pendinginan]"
                                        value="{{ $cooking['suhu_air_pendinginan'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['suhu_air_pendinginan']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0.26</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[tekanan_pendinginan]"
                                        value="{{ $cooking['tekanan_pendinginan'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['tekanan_pendinginan']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="2" class="text-center align-middle">5 menit</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_mulai_pendinginan]"
                                        value="{{ $cooking['waktu_mulai_pendinginan'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['waktu_mulai_pendinginan']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_selesai_pendinginan]"
                                        value="{{ $cooking['waktu_selesai_pendinginan'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['waktu_selesai_pendinginan']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= PROSES AKHIR ================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>PROSES AKHIR</strong></div>
                    <div class="card-body">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Parameter</th>
                                    <th>Satuan</th>
                                    <th>Standar</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Suhu Air</td>
                                    <td>°C</td>
                                    <td>36 - 42</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[suhu_air_akhir]"
                                        value="{{ $cooking['suhu_air_akhir'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['suhu_air_akhir']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0</td>
                                    <td>
                                        <input type="number" step="0.01" name="cooking[tekanan_akhir]"
                                        value="{{ $cooking['tekanan_akhir'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['tekanan_akhir']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="2" class="text-center align-middle">2 - 3 menit</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_mulai_akhir]"
                                        value="{{ $cooking['waktu_mulai_akhir'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['waktu_mulai_akhir']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td>
                                        <input type="time" name="cooking[waktu_selesai_akhir]"
                                        value="{{ $cooking['waktu_selesai_akhir'] ?? '' }}" class="form-control text-center"
                                        {{ isset($cooking['waktu_selesai_akhir']) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <strong>TOTAL WAKTU PROSES</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Waktu Proses</th>
                                        <th>Satuan</th>
                                        <th>Standar</th>
                                        <th>Alternatif</th>
                                        <th>Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start">Waktu Mulai</td>
                                        <td>WIB</td>
                                        <td rowspan="2" class="text-center align-middle">32.5 - 38.5 menit</td>
                                        <td rowspan="2" class="text-center align-middle">36.5 - 42.5 menit</td>
                                        <td>
                                            <input type="time" name="cooking[waktu_mulai_total]" id="waktu_mulai_total"
                                            value="{{ $cooking['waktu_mulai_total'] ?? '' }}"
                                            class="form-control form-control-sm text-center"
                                            {{ isset($cooking['waktu_mulai_total']) ? 'readonly' : '' }}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Waktu Selesai</td>
                                        <td>WIB</td>
                                        <td>
                                            <input type="time" name="cooking[waktu_selesai_total]" id="waktu_selesai_total"
                                            value="{{ $cooking['waktu_selesai_total'] ?? '' }}"
                                            class="form-control form-control-sm text-center"
                                            {{ isset($cooking['waktu_selesai_total']) ? 'readonly' : '' }}>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <strong>HASIL PEMASAKAN</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                 <tr>
                                    <th rowspan="2" class="text-center align-middle">Hasil Pemasakan</th>
                                    <th rowspan="2" class="text-center align-middle">Satuan</th>
                                    <th>Standar</th>
                                    <th>Alternatif</th>
                                    <th>Hasil</th>
                                </tr>
                                <tr>
                                    <th>21 gram</th>
                                    <th>12.5 gram</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                ['field'=>'suhu_produk_akhir','label'=>'Suhu Varian Akhir','satuan'=>'°C','standar'=>'48 ± 2','alternatif'=>'48 ± 2'],
                                ['field'=>'panjang','label'=>'Panjang','satuan'=>'Cm','standar'=>'14 - 15','alternatif'=>'9 - 10.5'],
                                ['field'=>'diameter','label'=>'Diameter','satuan'=>'Mm','standar'=>'14.0 - 14.5','alternatif'=>'13.5 - 14.5'],
                                ['field'=>'rasa','label'=>'Rasa Asin/Manis/Gurih','satuan'=>'','standar'=>'1 - 3','alternatif'=>''],
                                ['field'=>'warna','label'=>'Warna','satuan'=>'','standar'=>'1 - 3','alternatif'=>''],
                                ['field'=>'aroma','label'=>'Aroma','satuan'=>'','standar'=>'1 - 3','alternatif'=>''],
                                ['field'=>'texture','label'=>'Texture','satuan'=>'','standar'=>'1 - 3','alternatif'=>''],
                                ['field'=>'sobek_seal','label'=>'Sobek Seal','satuan'=>'','standar'=>'','alternatif'=>''],
                                ] as $item)
                                <tr>
                                    <td class="text-start">{{ $item['label'] }}</td>
                                    <td>{{ $item['satuan'] }}</td>
                                    <td>{{ $item['standar'] }}</td>
                                    <td>{{ $item['alternatif'] }}</td>
                                    <td>
                                        <input type="number" name="cooking[{{ $item['field'] }}]" class="form-control form-control-sm text-center" step="0.01"
                                        value="{{ $cooking[$item['field']] ?? '' }}"
                                        {{ isset($cooking[$item['field']]) ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <strong>TOTAL REJECT</strong>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                       <thead class="table-light">
                        <tr>
                            <th>Keterangan</th>
                            <th>Satuan</th>
                            <th>Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-start">Total Reject</td>
                            <td>Kg</td>
                            <td><input type="number" name="total_reject" value="{{ $pemasakan->total_reject ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= CATATAN ================= --}}
        <div class="card mb-4">
            <div class="card-header bg-light"><strong>Catatan</strong></div>
            <div class="card-body">
                <textarea name="catatan" class="form-control" rows="3"
                placeholder="Tambahkan catatan bila ada">{{ old('catatan', $pemasakan->catatan) }}</textarea>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update
            </button>
            <a href="{{ route('pemasakan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Batal
            </a>
        </div>
    </form>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>
<script>
// ==== VALIDASI KODE PRODUKSI (bisa lebih dari 1, pisahkan dengan /) ====
    const kodeInput = document.getElementById('kode_produksi');
    const kodeError = document.getElementById('kodeError');

    kodeInput.addEventListener('input', function() {
        let value = this.value.toUpperCase().replace(/\s+/g, '');
        this.value = value;
        kodeError.textContent = '';
        kodeError.classList.add('d-none');

        if (!value) return;

        const kodeList = value.split('/');
        const bulanMap = { 'A':0,'B':1,'C':2,'D':3,'E':4,'F':5,'G':6,'H':7,'I':8,'J':9,'K':10,'L':11 };

        for (let kode of kodeList) {
            if (kode.length !== 10) {
                kodeError.textContent = "Setiap kode batch harus terdiri dari 10 karakter.";
                kodeError.classList.remove('d-none');
                return false;
            }

            const format = /^[A-Z0-9]+$/;
            if (!format.test(kode)) {
                kodeError.textContent = "Kode batch hanya boleh huruf besar dan angka.";
                kodeError.classList.remove('d-none');
                return false;
            }

            const bulanChar = kode.charAt(1);
            if (!/^[A-L]$/.test(bulanChar)) {
                kodeError.textContent = "Karakter ke-2 harus huruf bulan (A–L).";
                kodeError.classList.remove('d-none');
                return false;
            }

            const hari = parseInt(kode.substr(2, 2), 10);
            if (isNaN(hari) || hari < 1 || hari > 31) {
                kodeError.textContent = "Karakter ke-3 dan ke-4 harus tanggal valid (01–31).";
                kodeError.classList.remove('d-none');
                return false;
            }
        }
    });

// ==== JUMLAH TRAY (bisa pisahkan dengan '+') ====
    const jumlahInput = document.getElementById('jumlah_tray');
    const trayTotal = document.getElementById('trayTotal');

    jumlahInput.addEventListener('input', function() {
        const total = this.value
        .split('+')
        .map(v => parseInt(v.trim()) || 0)
        .reduce((a, b) => a + b, 0);

        if (this.value.includes('+')) {
            trayTotal.textContent = `Total: ${total} tray`;
        } else {
            trayTotal.textContent = '';
        }
    });
</script>
@endsection
