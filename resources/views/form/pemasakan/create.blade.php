@extends('layouts.app')

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-plus-circle"></i> Form Input Pengecekan Pemasakan
                </h4>

                <form id="pemasakanForm" action="{{ route('pemasakan.store') }}" method="POST">
                    @csrf

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>IDENTIFIKASI</strong>
                        </div>
                        <div class="card-body">
                            {{-- ====== Baris 1: Tanggal & Shift ====== --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="date" id="dateInput" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Shift <span class="text-danger">*</span>
                                    </label>
                                    <select name="shift" id="shiftInput" class="form-control" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="1">Shift 1</option>
                                        <option value="2">Shift 2</option>
                                        <option value="3">Shift 3</option>
                                    </select>
                                </div>
                            </div>

                            {{-- ====== Baris 2: Produk & Chamber ====== --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Nama Varian <span class="text-danger">*</span>
                                    </label>
                                    <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true"
                                        required>
                                        <option value="">-- Pilih Varian --</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Nomor Chamber <span class="text-danger">*</span>
                                    </label>
                                    <select name="no_chamber" class="form-control selectpicker" data-live-search="true"
                                        required>
                                        <option value="">-- Pilih Chamber --</option>
                                        @foreach ($list_chambers as $list_chamber)
                                            <option value="{{ $list_chamber->nama_mesin }}">{{ $list_chamber->nama_mesin }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- ====== Baris 3: Kode Produksi & Berat Produk ====== --}}
                            <div id="batchContainer">
                                <div class="row mb-3 batch-row">
                                    <div class="col-md-6">
                                        <label class="form-label">Kode Batch <span class="text-danger">*</span></label>
                                        <select name="kode_produksi[]" class="form-control kode_produksi" required disabled>
                                            <option value="">Pilih Varian Terlebih Dahulu</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Jumlah Tray <span class="text-danger">*</span></label>
                                        <input type="number" name="jumlah_tray[]" class="form-control jumlah_tray" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-success w-100 addRow">
                                            <i class="bi bi-plus-circle"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-6">
                                    <small id="traySummary" class="fw-bold"></small>
                                </div>
                            </div>

                            {{-- ====== Baris 4: Suhu Produk & Jumlah Tray ====== --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Suhu Varian (°C) <span class="text-danger">*</span></label><br>
                                    <input type="number" name="suhu_produk" id="suhu_produk" class="form-control" step="0.1" required>
                                    <small class="text-danger">Standar: 19 ± 1 °C</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Berat Varian (gram) <span class="text-danger">*</span></label>
                                    <input type="number" name="berat_produk" id="berat_produk" class="form-control" step="0.1" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= PERSIAPAN ================= --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <strong>PERSIAPAN</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Persiapan</th>
                                            <th>Satuan</th>
                                            <th>Standar</th>
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Tekanan Angin</td>
                                            <td>Kg/cm²</td>
                                            <td>5 – 8</td>
                                            <td>
                                                <input type="text" name="cooking[tekanan_angin]" id="tekanan_angin"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Tekanan Steam</td>
                                            <td>Kg/cm²</td>
                                            <td>6 - 9</td>
                                            <td>
                                                <input type="text" name="cooking[tekanan_steam]" id="tekanan_steam"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Tekanan Air</td>
                                            <td>Kg/cm²</td>
                                            <td>2 - 2.5</td>
                                            <td>
                                                <input type="text" name="cooking[tekanan_air]" id="tekanan_air"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ================= PEMANASAN AWAL ================= --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <strong>PEMANASAN AWAL</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Pemanasan Awal</th>
                                            <th>Satuan</th>
                                            <th>Standar</th>
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Suhu Air</td>
                                            <td>°C</td>
                                            <td>100 - 110</td>
                                            <td>
                                                <input type="number" name="cooking[suhu_air_awal]" id="suhu_air_awal"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Tekanan</td>
                                            <td>Mpa</td>
                                            <td>0.26</td>
                                            <td>
                                                <input type="number" name="cooking[tekanan_awal]" id="tekanan_awal"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Mulai</td>
                                            <td>WIB</td>
                                            <td rowspan="2" class="text-center align-middle">1.5 - 2.5 menit</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_mulai_awal]"
                                                    id="waktu_mulai_awal"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Selesai</td>
                                            <td>WIB</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_selesai_awal]"
                                                    id="waktu_selesai_awal"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ================= PROSES PEMANASAN ================= --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <strong>PROSES PEMANASAN</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Proses Pemanasan</th>
                                            <th>Satuan</th>
                                            <th>Standar</th>
                                            <th>Alternatif</th>
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Suhu Air</td>
                                            <td>°C</td>
                                            <td>121.2</td>
                                            <td>119</td>
                                            <td>
                                                <input type="number" name="cooking[suhu_air_proses]"
                                                    id="suhu_air_proses" class="form-control form-control-sm text-center"
                                                    step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Tekanan</td>
                                            <td>Mpa</td>
                                            <td colspan="2">0.26</td>
                                            <td>
                                                <input type="number" name="cooking[tekanan_proses]" id="tekanan_proses"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Mulai</td>
                                            <td>WIB</td>
                                            <td colspan="2">8 - 10 menit</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_mulai_proses]"
                                                    id="waktu_mulai_proses"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Selesai</td>
                                            <td>WIB</td>
                                            <td colspan="2">8 - 10 menit</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_selesai_proses]"
                                                    id="waktu_selesai_proses"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ================= STERILISASI ================= --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <strong>STERILISASI</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
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
                                        <tr>
                                            <td class="text-start">Suhu Air</td>
                                            <td>°C</td>
                                            <td>121.2</td>
                                            <td>119</td>
                                            <td><input type="number" name="cooking[suhu_air_sterilisasi][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                            <td><input type="number" name="cooking[suhu_air_sterilisasi][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                            <td><input type="number" name="cooking[suhu_air_sterilisasi][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                            <td><input type="number" name="cooking[suhu_air_sterilisasi][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Thermometer Retort</td>
                                            <td>°C</td>
                                            <td>121.2</td>
                                            <td>119</td>
                                            <td><input type="number" name="cooking[thermometer_retort][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                            <td><input type="number" name="cooking[thermometer_retort][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                            <td><input type="number" name="cooking[thermometer_retort][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                            <td><input type="number" name="cooking[thermometer_retort][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Tekanan</td>
                                            <td>Mpa</td>
                                            <td colspan="2">0.26</td>
                                            <td><input type="number" name="cooking[tekanan_sterilisasi][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                            <td><input type="number" name="cooking[tekanan_sterilisasi][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                            <td><input type="number" name="cooking[tekanan_sterilisasi][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                            <td><input type="number" name="cooking[tekanan_sterilisasi][]"
                                                    class="form-control form-control-sm text-center" step="0.01"></td>
                                        </tr>

                                        {{-- Field waktu --}}
                                        <tr>
                                            <td class="text-start">Waktu Mulai</td>
                                            <td>WIB</td>
                                            <td rowspan="3" class="align-middle text-center">12 menit</td>
                                            <td rowspan="3" class="align-middle text-center">16 menit</td>
                                            <td colspan="4">
                                                <input type="time" name="cooking[waktu_mulai_sterilisasi]"
                                                    id="waktu_mulai_sterilisasi"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Pengecekan</td>
                                            <td>WIB</td>
                                            <td><input type="time" name="cooking[waktu_pengecekan_sterilisasi][]"
                                                    class="form-control form-control-sm text-center"></td>
                                            <td><input type="time" name="cooking[waktu_pengecekan_sterilisasi][]"
                                                    class="form-control form-control-sm text-center"></td>
                                            <td><input type="time" name="cooking[waktu_pengecekan_sterilisasi][]"
                                                    class="form-control form-control-sm text-center"></td>
                                            <td><input type="time" name="cooking[waktu_pengecekan_sterilisasi][]"
                                                    class="form-control form-control-sm text-center"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Selesai</td>
                                            <td>WIB</td>
                                            <td colspan="4">
                                                <input type="time" name="cooking[waktu_selesai_sterilisasi]"
                                                    id="waktu_selesai_sterilisasi"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ================= PENDINGINAN AWAL ================= --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <strong>PENDINGINAN AWAL</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Pendinginan Awal</th>
                                            <th>Satuan</th>
                                            <th>Standar</th>
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Suhu Air</td>
                                            <td>°C</td>
                                            <td>30 - 35</td>
                                            <td>
                                                <input type="number" step="0.01" name="cooking[suhu_air_pendinginan_awal]"
                                                    id="suhu_air_pendinginan_awal"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Tekanan</td>
                                            <td>Mpa</td>
                                            <td>0.26</td>
                                            <td>
                                                <input type="number" step="0.01" name="cooking[tekanan_pendinginan_awal]"
                                                    id="tekanan_pendinginan_awal"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Mulai</td>
                                            <td>WIB</td>
                                            <td rowspan="2" class="text-center align-middle">3 - 6 menit</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_mulai_pendinginan_awal]"
                                                    id="waktu_mulai_pendinginan_awal"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Selesai</td>
                                            <td>WIB</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_selesai_pendinginan_awal]"
                                                    id="waktu_selesai_pendinginan_awal"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ================= PENDINGINAN ================= --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <strong>PENDINGINAN</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Pendinginan</th>
                                            <th>Satuan</th>
                                            <th>Standar</th>
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Suhu Air</td>
                                            <td>°C</td>
                                            <td>50 ± 3</td>
                                            <td>
                                                <input type="number" step="0.01" name="cooking[suhu_air_pendinginan]"
                                                    id="suhu_air_pendinginan"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Tekanan</td>
                                            <td>Mpa</td>
                                            <td>0.26</td>
                                            <td>
                                                <input type="number" step="0.01" name="cooking[tekanan_pendinginan]"
                                                    id="tekanan_pendinginan"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Mulai</td>
                                            <td>WIB</td>
                                            <td rowspan="2" class="text-center align-middle">5 menit</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_mulai_pendinginan]"
                                                    id="waktu_mulai_pendinginan"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Selesai</td>
                                            <td>WIB</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_selesai_pendinginan]"
                                                    id="waktu_selesai_pendinginan"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ================= PROSES AKHIR ================= --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <strong>PROSES AKHIR</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Proses Akhir</th>
                                            <th>Satuan</th>
                                            <th>Standar</th>
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Suhu Air</td>
                                            <td>°C</td>
                                            <td>36 - 42</td>
                                            <td>
                                                <input type="number" step="0.01" name="cooking[suhu_air_akhir]" id="suhu_air_akhir"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Tekanan</td>
                                            <td>Mpa</td>
                                            <td>0</td>
                                            <td>
                                                <input type="number" step="0.01" name="cooking[tekanan_akhir]" id="tekanan_akhir"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Mulai</td>
                                            <td>WIB</td>
                                            <td rowspan="2" class="text-center align-middle">2 - 3 menit</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_mulai_akhir]"
                                                    id="waktu_mulai_akhir"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Selesai</td>
                                            <td>WIB</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_selesai_akhir]"
                                                    id="waktu_selesai_akhir"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
                                                <input type="time" name="cooking[waktu_mulai_total]"
                                                    id="waktu_mulai_total"
                                                    class="form-control form-control-sm text-center" readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Selesai</td>
                                            <td>WIB</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_selesai_total]"
                                                    id="waktu_selesai_total"
                                                    class="form-control form-control-sm text-center" readonly>
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
                                            <input type="number" name="cooking[{{ $item['field'] }}]" class="form-control form-control-sm text-center" step="0.01">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ===================== TOTAL REJECT ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <strong>TOTAL REJECT</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                </thead>
                                <tbody id="rejectTableBody">
                                    <tr>
                                        <td colspan="3" class="text-muted py-4">
                                            Belum ada batch dipilih
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ===================== Catatan ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"></textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('pemasakan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
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
    $(document).ready(function() {

        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }

        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');

        if(dateInput) dateInput.value = `${yyyy}-${mm}-${dd}`;

        let hour = parseInt(hh);
        if(shiftInput) {
            if (hour >= 7 && hour < 15) {
                shiftInput.value = "1";
            } else if (hour >= 15 && hour < 23) {
                shiftInput.value = "2";
            } else {
                shiftInput.value = "3";
            }
        }

        // BATCH LOGIC
        let batchedData = [];

        function populateBatches() {
            let options = '<option value="">-- Pilih Batch --</option>';
            if (batchedData.length === 0) {
                options = '<option value="">Batch Tidak Ditemukan / Pilih Varian</option>';
            } else {
                batchedData.forEach(item => {
                    options += `<option value="${item.uuid}">${item.kode_produksi}</option>`;
                });
            }

            $('.kode_produksi').each(function() {
                let currentValue = $(this).val();
                $(this).html(options).prop('disabled', batchedData.length === 0);
                if (currentValue) {
                    $(this).val(currentValue);
                }
            });
        }

        // LOGIC UNTUK RENDER TABEL REJECT
        function renderRejectTable() {
            // 1. Simpan nilai input saat ini agar tidak hilang saat dirender ulang
            let currentRejects = [];
            $('.reject-input').each(function() {
                let idx = $(this).data('index');
                currentRejects[idx] = $(this).val();
            });

            let tbody = $('#rejectTableBody');
            tbody.empty();
            let hasData = false;

            $('.kode_produksi').each(function(index) {
                let value = $(this).val();
                let text = $(this).find('option:selected').text();

                if (value && value !== "") {
                    hasData = true;
                    
                    // 2. Terapkan nilai yang sudah diketik sebelumnya (jika ada)
                    let rawVal = currentRejects[index];
                    let rejectVal = (rawVal === null || rawVal === undefined || rawVal === '') ? '' : rawVal;

                    tbody.append(`
                        <tr>
                            <td class="text-start fw-semibold">${text}</td>
                            <td>Kg</td>
                            <td>
                                <input type="number" 
                                    step="0.01" 
                                    name="total_reject[]" 
                                    value="${rejectVal}"
                                    class="form-control form-control-sm text-center reject-input" 
                                    data-index="${index}"
                                    placeholder="">
                            </td>
                        </tr>
                    `);
                }
            });

            if (!hasData) {
                tbody.html(`
                    <tr>
                        <td colspan="3" class="text-muted py-4">
                            Belum ada batch dipilih
                        </td>
                    </tr>
                `);
            }
        }

        $('#nama_produk').on('change', function() {
            let namaProduk = $(this).val();

            if (!namaProduk) {
                batchedData = [];
                populateBatches();
                renderRejectTable();
                return;
            }

            $('.kode_produksi').html('<option value="">Mencari Batch...</option>').prop('disabled', false);

            $.ajax({
                url: '{{ url('/lookup/batch') }}/' + encodeURIComponent(namaProduk),
                type: 'GET',
                success: function(data) {
                    batchedData = data;
                    populateBatches();
                    renderRejectTable(); // Reset/update tabel reject saat varian diubah
                }
            });
        });

        // Trigger render saat opsi batch dipilih manual
        $(document).on('change', '.kode_produksi', function() {
            renderRejectTable();
        });

        // TAMBAH ROW
        $(document).on('click', '.addRow', function() {
            let row = `
            <div class="row mb-3 batch-row">
                <div class="col-md-6">
                    <label class="form-label">Kode Batch</label>
                    <select name="kode_produksi[]" class="form-control kode_produksi" required>
                        <option value="">Pilih Varian Terlebih Dahulu</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Tray</label>
                    <input type="number" name="jumlah_tray[]" class="form-control jumlah_tray" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger w-100 removeRow"><i class="bi bi-trash"></i> Hapus</button>
                </div>
            </div>`;

            $('#batchContainer').append(row);
            populateBatches(); 
            // Render dipanggil agar tabel reject mengikuti jumlah baris baru
            renderRejectTable();
        });

        // HAPUS ROW
        $(document).on('click', '.removeRow', function() {
            $(this).closest('.batch-row').remove();
            hitungTotalTray();
            renderRejectTable(); // Render ulang tabel reject saat row batch dihapus
        });

        // HITUNG TRAY
        $(document).on('input', '.jumlah_tray', function() {
            hitungTotalTray();
        });

        function hitungTotalTray() {
            let total = 0;
            $('.jumlah_tray').each(function() {
                total += parseInt($(this).val()) || 0;
            });
            const summary = $('#traySummary');
            if (total === 0) summary.text('');
            else if (total > 28) summary.html(`<span class="text-danger">Total: ${total} tray (MELEBIHI standar 28!)</span>`);
            else summary.html(`<span class="text-success">Total: ${total} tray (Maks: 28)</span>`);
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function autoChainTime(fromId, toId) {
            const fromInput = document.getElementById(fromId);
            const toInput = document.getElementById(toId);

            if (!fromInput || !toInput) return;

            // Menggunakan event 'input' atau 'change'
            fromInput.addEventListener('change', function() {
                if (this.value) {
                    toInput.value = this.value;
                    // Trigger event change manual pada toInput agar jika toInput 
                    // adalah trigger untuk fungsi lain, fungsinya ikut berjalan berantai
                    toInput.dispatchEvent(new Event('change')); 
                }
            });
        }

        // 1. PEMANASAN AWAL -> PROSES PEMANASAN
        autoChainTime('waktu_selesai_awal', 'waktu_mulai_proses');
        
        // 2. PROSES PEMANASAN -> STERILISASI
        autoChainTime('waktu_selesai_proses', 'waktu_mulai_sterilisasi');
        
        // 3. STERILISASI -> PENDINGINAN AWAL
        autoChainTime('waktu_selesai_sterilisasi', 'waktu_mulai_pendinginan_awal');
        
        // 4. PENDINGINAN AWAL -> PENDINGINAN
        autoChainTime('waktu_selesai_pendinginan_awal', 'waktu_mulai_pendinginan');
        
        // 5. PENDINGINAN -> PROSES AKHIR
        autoChainTime('waktu_selesai_pendinginan', 'waktu_mulai_akhir');
        
        // 6. PROSES AKHIR -> TOTAL WAKTU PROSES (WAKTU SELESAI) -> INI YANG DIPERBAIKI
        autoChainTime('waktu_selesai_akhir', 'waktu_selesai_total');

        // ==========================================
        // SINKRONISASI WAKTU MULAI AWAL -> WAKTU MULAI TOTAL
        // ==========================================
        const waktuMulaiAwal = document.getElementById('waktu_mulai_awal');
        const waktuMulaiTotal = document.getElementById('waktu_mulai_total');

        function syncWaktuMulaiTotal() {
            if (waktuMulaiAwal && waktuMulaiTotal && waktuMulaiAwal.value) {
                waktuMulaiTotal.value = waktuMulaiAwal.value;
            }
        }

        if(waktuMulaiAwal) {
            waktuMulaiAwal.addEventListener('change', syncWaktuMulaiTotal);
            syncWaktuMulaiTotal(); // Set nilai awal jika sudah terisi (saat edit)
        }
    });
</script>
@endsection