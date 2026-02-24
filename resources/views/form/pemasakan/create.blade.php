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
                                    Nama Produk <span class="text-danger">*</span>
                                </label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true"
                                    required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produks as $produk)
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
                                    @foreach($list_chambers as $list_chamber)
                                    <option value="{{ $list_chamber->nama_mesin }}">{{ $list_chamber->nama_mesin }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ====== Baris 3: Kode Produksi & Berat Produk ====== --}}
                        <div class="row mb-3 batch-row">

                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi <span class="text-danger">*</span></label>
                                <select name="kode_produksi[]" id="kode_produksi" class="form-control kode_produksi"
                                    required>
                                    <option value="">Pilih Varian Terlebih Dahulu</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jumlah Tray <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah_tray[]" class="form-control jumlah_tray" required>
                                <small class="text-danger">Total Standart: 28 tray</small>
                            </div>

                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-success w-100 addRow">
                                    <i class="bi bi-plus-circle"></i> Tambah
                                </button>
                            </div>

                        </div>

                        <!-- TEMPAT BARIS TAMBAHAN -->
                        <div id="batchContainer"></div>



                        {{-- ====== Baris 4: Suhu Produk & Jumlah Tray ====== --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Suhu Produk (°C) <span
                                        class="text-danger">*</span></label><br>
                                <input type="number" name="suhu_produk" id="suhu_produk" class="form-control" step="0.1"
                                    required>
                                <small class="text-danger">Standar: 19 ± 1 °C</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Berat Produk (gram) <span class="text-danger">*</span></label>
                                <input type="number" name="berat_produk" id="berat_produk" class="form-control"
                                    step="0.1" required>
                            </div>
                        </div>
                    </div>
                </div>

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
                                            <input type="time" name="cooking[waktu_mulai_awal]" id="waktu_mulai_awal"
                                                class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Waktu Selesai</td>
                                        <td>WIB</td>
                                        <!-- <td>1.5 - 2.5 menit</td> -->
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
                                            <input type="number" name="cooking[suhu_air_proses]" id="suhu_air_proses"
                                                class="form-control form-control-sm text-center" step="0.01">
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
                                    <tr>
                                        <td class="text-start">Waktu Mulai</td>
                                        <td>WIB</td>
                                        <td rowspan="3" class="align-middle text-center">12 menit</td>
                                        <td rowspan="3" class="align-middle text-center">16 menit</td>
                                        <td colspan="4">
                                            <input type="time" name="cooking[waktu_mulai_sterilisasi]"
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
                                            <input type="number" name="cooking[suhu_air_pendinginan_awal]"
                                                id="suhu_air_pendinginan_awal"
                                                class="form-control form-control-sm text-center" step="0.01">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Tekanan</td>
                                        <td>Mpa</td>
                                        <td>0.26</td>
                                        <td>
                                            <input type="number" name="cooking[tekanan_pendinginan_awal]"
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

                <div class="card mb-4">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <strong>PENDINGINAN</strong>
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
                                        <td>50 ± 3</td>
                                        <td>
                                            <input type="number" name="cooking[suhu_air_pendinginan]"
                                                id="suhu_air_pendinginan"
                                                class="form-control form-control-sm text-center" step="0.01">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Tekanan</td>
                                        <td>Mpa</td>
                                        <td>0.26</td>
                                        <td>
                                            <input type="number" name="cooking[tekanan_pendinginan]"
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
                                            <input type="number" name="cooking[suhu_air_akhir]" id="suhu_air_akhir"
                                                class="form-control form-control-sm text-center" step="0.01">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Tekanan</td>
                                        <td>Mpa</td>
                                        <td>0</td>
                                        <td>
                                            <input type="number" name="cooking[tekanan_akhir]" id="tekanan_akhir"
                                                class="form-control form-control-sm text-center" step="0.01">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Waktu Mulai</td>
                                        <td>WIB</td>
                                        <td rowspan="2" class="text-center align-middle">2 - 3 menit</td>
                                        <td>
                                            <input type="time" name="cooking[waktu_mulai_akhir]" id="waktu_mulai_akhir"
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
                                            <input type="time" name="cooking[waktu_mulai_total]" id="waktu_mulai_total"
                                                class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Waktu Selesai</td>
                                        <td>WIB</td>
                                        <td>
                                            <input type="time" name="cooking[waktu_selesai_total]"
                                                id="waktu_selesai_total"
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
                                    <tr>
                                        <td class="text-start">Suhu Produk Akhir</td>
                                        <td>°C</td>
                                        <td colspan="2">48 ± 2</td>
                                        <td>
                                            <input type="number" name="cooking[suhu_produk_akhir]"
                                                id="suhu_produk_akhir" class="form-control form-control-sm text-center"
                                                step="0.01">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Panjang</td>
                                        <td>Cm</td>
                                        <td>14 - 15</td>
                                        <td>9 - 10.5</td>
                                        <td>
                                            <input type="number" name="cooking[panjang]" id="panjang"
                                                class="form-control form-control-sm text-center" step="0.01">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Diameter</td>
                                        <td>Cm</td>
                                        <td>14.0 - 14.5</td>
                                        <td>13.5 - 14.5</td>
                                        <td>
                                            <input type="number" name="cooking[diameter]" id="diameter"
                                                class="form-control form-control-sm text-center" step="0.01">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Rasa Asin/Manis/Gurih</td>
                                        <td></td>
                                        <td colspan="2">1 - 3</td>
                                        <td>
                                            <input type="number" name="cooking[rasa]" id="rasa"
                                                class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Warna</td>
                                        <td></td>
                                        <td colspan="2">1 - 3</td>
                                        <td>
                                            <input type="number" name="cooking[warna]" id="warna"
                                                class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Aroma</td>
                                        <td></td>
                                        <td colspan="2">1 - 3</td>
                                        <td>
                                            <input type="number" name="cooking[aroma]" id="aroma"
                                                class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Texture</td>
                                        <td></td>
                                        <td colspan="2">1 - 3</td>
                                        <td>
                                            <input type="number" name="cooking[texture]" id="texture"
                                                class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Sobek Seal</td>
                                        <td></td>
                                        <td colspan="2"></td>
                                        <td>
                                            <input type="number" name="cooking[sobek_seal]" id="sobek_seal"
                                                class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>TOTAL REJECT</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <!-- <tr>
                                        <th>Total Reject</th>
                                        <th>Satuan</th>
                                        <th>Hasil</th>
                                    </tr> -->
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start">Total Reject</td>
                                        <td>Kg</td>
                                        <td>
                                            <input type="number" name="total_reject" id="total_reject"
                                                class="form-control form-control-sm text-center" step="0.01">
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
                        <textarea name="catatan" class="form-control" rows="3"
                            placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
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

            <hr>
            <div id="resultArea"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function(){    

        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');

        dateInput.value = `${yyyy}-${mm}-${dd}`;

        let hour = parseInt(hh);
        if (hour >= 7 && hour < 15) {
            shiftInput.value = "1";
        } else if (hour >= 15 && hour < 23) {
            shiftInput.value = "2";
        } else {
            shiftInput.value = "3"; 
        }

    const produkSelect = document.querySelector('select[name="nama_produk"]');
    const allBatchSelect = document.querySelectorAll('.kode_produksi');

    // Disable batch saat awal load (jika tidak ada old value)
    if (!produkSelect.value) {
        allBatchSelect.disabled = true;
    }

    produkSelect.addEventListener('change', function () {

        let namaProduk = this.value;

        document.querySelectorAll('.kode_produksi').forEach(select => {
            select.disabled = true;
            select.innerHTML = '<option value="">Pilih Varian Terlebih Dahulu</option>';
        });

        if (!namaProduk) return;

        const url = "{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}".replace('__PRODUK__', encodeURIComponent(namaProduk));
            fetch(url)
            .then(res => res.json())
            .then(data => {

                document.querySelectorAll('.kode_produksi').forEach(select => {
                    select.innerHTML = '';

                    if (data.length === 0) {
                        select.innerHTML = '<option value="">Batch Tidak Ditemukan</option>';
                        select.disabled = true;
                        return;
                    }

                    select.disabled = false;
                    select.innerHTML = '<option value="">-- Pilih Batch --</option>';

                    data.forEach(batch => {
                        select.innerHTML += `
                            <option value="${batch.uuid}">${batch.kode_produksi}</option>
                        `;
                    });
                });

            });
    });


        // TAMBAH ROW
$(document).on('click', '.addRow', function () {
    let row = `
    <div class="row mb-3 batch-row">

        <div class="col-md-6">
            <select name="kode_produksi[]" class="form-control kode_produksi" disabled required>
                <option value="">Pilih Varian Terlebih Dahulu</option>
            </select>
        </div>

        <div class="col-md-4">
            <input type="number" name="jumlah_tray[]" class="form-control jumlah_tray" required>
            <small id="traySummary" class="fw-bold"></small>
        </div>

        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="btn btn-danger w-100 removeRow">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>

    </div>
    `;

    $('#batchContainer').append(row);

    // setelah append, panggil ulang fungsi untuk refresh opsi
    produkSelect.dispatchEvent(new Event('change'));
    hitungTotalTray();
});


    // HAPUS ROW
    $(document).on('click', '.removeRow', function () {
        $(this).closest('.batch-row').remove();
        hitungTotalTray();
    });

// ==== JUMLAH TRAY (bisa pisahkan dengan '+') ====
$(document).on('input', '.jumlah_tray', function () {
    hitungTotalTray();
});

    function hitungTotalTray() {
    let total = 0;

    document.querySelectorAll('.jumlah_tray').forEach(input => {
        let val = input.value.trim();

        if (val.includes('+')) {
            let sum = val.split('+')
                .map(v => parseInt(v.trim()) || 0)
                .reduce((a, b) => a + b, 0);
            total += sum;
        } else {
            total += parseInt(val) || 0;
        }
    });

    const summary = document.getElementById('traySummary');

    if (total === 0) {
        summary.textContent = '';
        return;
    }

    if (total > 28) {
        summary.innerHTML = `<span class="text-danger">Total: ${total} tray (MELEBIHI standar 28!)</span>`;
    } else {
        summary.innerHTML = `<span class="text-success">Total: ${total} tray (Maks: 28)</span>`;
    }
}

});
</script>

@endsection