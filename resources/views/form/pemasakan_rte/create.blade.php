@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-plus-circle"></i> Form Input Pengecekan Pemasakan RTE
                </h4>

                <form id="pemasakanForm" action="{{ route('pemasakan_rte.store') }}" method="POST">
                    @csrf

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>IDENTIFIKASI</strong>
                        </div>
                        <div class="card-body">
                            {{-- ====== Baris 1: Tanggal & Shift ====== --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="date" id="dateInput" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Shift</label>
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
                                    <label for="nama_produk" class="form-label fw-semibold">
                                        Nama Varian <span class="text-danger">*</span>
                                    </label>
                                    <select id="nama_produk" class="form-control" required>
                                        <option value="">-- Pilih Varian --</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}">
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        Pilih varian produk terlebih dahulu
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. Chamber</label>
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
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kode_batch" class="form-label fw-semibold">
                                        Kode Batch <span class="text-danger">*</span>
                                    </label>
                                    <select id="kode_batch" class="form-control" disabled required>
                                        <option value="">Pilih Varian terlebih dahulu</option>
                                    </select>
                                    <small class="text-muted">
                                        Batch akan muncul otomatis
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Berat Varian (gram)</label>
                                    <input type="number" name="berat_produk" id="berat_produk" class="form-control"
                                        step="0.1" required>
                                </div>
                            </div>

                            {{-- ====== Baris 4: Suhu Varian & Jumlah Tray ====== --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Suhu Varian (°C)</label><br>
                                    <small class="text-danger">Standar: 15 - 18 °C</small>
                                    <input type="number" name="suhu_produk" id="suhu_produk" class="form-control"
                                        step="0.1" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah Tray</label><br>
                                    <small class="text-danger">Standar: 28 tray</small>
                                    <input type="text" name="jumlah_tray" id="jumlah_tray" class="form-control" required>
                                    <small class="text-muted">Bisa lebih dari satu jumlah, pisahkan dengan tanda
                                        <strong>+</strong></small><br>
                                    <small id="trayTotal" class="text-success fw-bold"></small><br>
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
                                            <td>5 – 8</td>
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
                                                <input type="time" name="cooking[waktu_mulai_awal]"
                                                    id="waktu_mulai_awal"
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
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Suhu Air</td>
                                            <td>°C</td>
                                            <td>121.2</td>
                                            <td>
                                                <input type="number" name="cooking[suhu_air_proses]"
                                                    id="suhu_air_proses" class="form-control form-control-sm text-center"
                                                    step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Tekanan</td>
                                            <td>Mpa</td>
                                            <td>0.26</td>
                                            <td>
                                                <input type="number" name="cooking[tekanan_proses]" id="tekanan_proses"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Mulai</td>
                                            <td>WIB</td>
                                            <td rowspan="2" class="text-center align-middle">8 - 10 menit</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_mulai_proses]"
                                                    id="waktu_mulai_proses"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Waktu Selesai</td>
                                            <td>WIB</td>
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
                                            <th colspan="4">Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Suhu Air</td>
                                            <td>°C</td>
                                            <td>121.2</td>
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
                                            <td>0.26</td>
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
                                            <td rowspan="3" class="align-middle text-center">50-55 menit</td>
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
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Waktu Mulai</td>
                                            <td>WIB</td>
                                            <td rowspan="2" class="text-center align-middle">85 - 90 menit</td>
                                            <td>
                                                <input type="time" name="cooking[waktu_mulai_total]"
                                                    id="waktu_mulai_total"
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
                            <strong>SENSORI</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2" class="text-center align-middle">Hasil Pemasakan</th>
                                            <th class="text-center align-middle">Satuan</th>
                                            <th rowspan="2" class="text-center align-middle">Hasil</th>
                                        </tr>
                                        <tr>
                                            <th>200 gram</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Suhu Varian Akhir</td>
                                            <td>°C</td>
                                            <td>
                                                <input type="number" name="cooking[suhu_produk_akhir]"
                                                    id="suhu_produk_akhir"
                                                    class="form-control form-control-sm text-center" step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Sobek Seal</td>
                                            <td></td>
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
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- ===================== TOMBOL ===================== --}}
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('pemasakan_rte.index') }}" class="btn btn-secondary">
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
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
            const bulanMap = {
                'A': 0,
                'B': 1,
                'C': 2,
                'D': 3,
                'E': 4,
                'F': 5,
                'G': 6,
                'H': 7,
                'I': 8,
                'J': 9,
                'K': 10,
                'L': 11
            };

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
    <script>
        $('#nama_produk').on('change', function() {

            let namaProduk = $(this).val();
            let batchSelect = $('#kode_batch');

            if (!namaProduk) {
                batchSelect.html('<option>Pilih Varian dulu</option>');
                batchSelect.prop('disabled', true);
                return;
            }

            $.ajax({
                url: '/lookup/batch/' + namaProduk,
                type: 'GET',
                success: function(data) {

                    batchSelect.prop('disabled', false);
                    batchSelect.html('<option value="">-- Pilih Batch --</option>');

                    data.forEach(function(item) {
                        batchSelect.append(
                            `<option value="${item.uuid}">${item.kode_produksi}</option>`
                        );
                    });
                }
            });

        });
    </script>
@endsection
