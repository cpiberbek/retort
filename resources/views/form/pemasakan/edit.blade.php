@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pengecekan Pemasakan
            </h4>
            
            <form id="pemasakanForm" action="{{ route('pemasakan.edit_spv', $pemasakan->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ====== IDENTIFIKASI ====== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>IDENTIFIKASI</strong>
                    </div>
                    <div class="card-body">
                        {{-- Baris 1: Tanggal & Shift --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                value="{{ old('date', $pemasakan->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift', $pemasakan->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $pemasakan->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $pemasakan->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        {{-- Baris 2: Produk & Chamber --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $pemasakan->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Chamber</label>
                                <select name="no_chamber" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Chamber --</option>
                                    @foreach($list_chambers as $list_chamber)
                                    <option value="{{ $list_chamber->nama_mesin }}" 
                                        {{ old('no_chamber', $pemasakan->no_chamber ?? '') == $list_chamber->nama_mesin ? 'selected' : '' }}>
                                        {{ $list_chamber->nama_mesin }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ====== BATCH & TRAY ARRAY ====== --}}
                        <div id="batchContainer">
                            {{-- $pemasakan->kode_produksi sudah berupa ARRAY karena cast di model --}}
                            @foreach($pemasakan->kode_produksi as $index => $kp)
                            <div class="row mb-3 batch-row">
                                <div class="col-md-6">
                                    <label class="form-label">Kode Batch</label>
                                    @php
                                        $mincing = \App\Models\Mincing::where('uuid', $kp)->first();
                                        $batchText = $mincing ? $mincing->kode_produksi : $kp;
                                    @endphp
                                    <select name="kode_produksi[]" class="form-control kode_produksi" required>
                                        <option value="{{ $kp }}" selected>{{ $batchText }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jumlah Tray</label>
                                    {{-- Mengakses array langsung --}}
                                    <input type="number" name="jumlah_tray[]" class="form-control jumlah_tray" 
                                        value="{{ $pemasakan->jumlah_tray[$index] ?? '' }}" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    @if($loop->first)
                                        <button type="button" class="btn btn-success w-100 addRow"><i class="bi bi-plus-circle"></i> Tambah</button>
                                    @else
                                        <button type="button" class="btn btn-danger w-100 removeRow"><i class="bi bi-trash"></i> Hapus</button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-6">
                                <small id="traySummary" class="fw-bold"></small>
                            </div>
                        </div>

                        {{-- Baris 4: Suhu Produk & Jumlah Tray --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Suhu Varian (°C)</label><br>
                                <small class="text-danger">Standar: 19 ± 1 °C</small>
                                <input type="number" name="suhu_produk" id="suhu_produk"
                                class="form-control" step="0.1"
                                value="{{ old('suhu_produk', $pemasakan->suhu_produk) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Berat Varian (gram)</label>
                                <input type="number" name="berat_produk" id="berat_produk"
                                class="form-control" step="0.1"
                                value="{{ old('berat_produk', $pemasakan->berat_produk) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                $cooking = is_string($pemasakan->cooking) 
                    ? json_decode($pemasakan->cooking, true) 
                    : $pemasakan->cooking;
                $cooking = is_array($cooking) ? $cooking : [];
                @endphp

                {{-- ================= PERSIAPAN ================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>PERSIAPAN</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered text-center align-middle">
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
                                    <td><input type="text" name="cooking[tekanan_angin]" value="{{ $cooking['tekanan_angin'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan Steam</td>
                                    <td>Kg/cm²</td>
                                    <td>6 - 9</td>
                                    <td><input type="text" name="cooking[tekanan_steam]" value="{{ $cooking['tekanan_steam'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan Air</td>
                                    <td>Kg/cm²</td>
                                    <td>2 - 2.5</td>
                                    <td><input type="text" name="cooking[tekanan_air]" value="{{ $cooking['tekanan_air'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <td><input type="number" name="cooking[suhu_air_awal]" value="{{ $cooking['suhu_air_awal'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0.26</td>
                                    <td><input type="number" name="cooking[tekanan_awal]" value="{{ $cooking['tekanan_awal'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="2" class="align-middle">1.5 - 2.5 menit</td>
                                    <td><input type="time" id="waktu_mulai_awal" name="cooking[waktu_mulai_awal]" value="{{ $cooking['waktu_mulai_awal'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td><input type="time" id="waktu_selesai_awal" name="cooking[waktu_selesai_awal]" value="{{ $cooking['waktu_selesai_awal'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <td><input type="number" name="cooking[suhu_air_proses]" value="{{ $cooking['suhu_air_proses'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan</td>
                                    <td>Mpa</td>
                                    <td colspan="2">0.26</td>
                                    <td><input type="number" name="cooking[tekanan_proses]" value="{{ $cooking['tekanan_proses'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td colspan="2">8 - 10 menit</td>
                                    <td><input type="time" id="waktu_mulai_proses" name="cooking[waktu_mulai_proses]" value="{{ $cooking['waktu_mulai_proses'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td colspan="2">8 - 10 menit</td>
                                    <td><input type="time" id="waktu_selesai_proses" name="cooking[waktu_selesai_proses]" value="{{ $cooking['waktu_selesai_proses'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= STERILISASI ================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>STERILISASI</strong></div>
                    <div class="card-body">
                        <table class="table table-bordered text-center align-middle">
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
                                    @foreach(range(0,3) as $i)
                                    <td><input type="number" name="cooking[suhu_air_sterilisasi][]" value="{{ $cooking['suhu_air_sterilisasi'][$i] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start">Thermometer Retort</td>
                                    <td>°C</td>
                                    <td>121.2</td>
                                    <td>119</td>
                                    @foreach(range(0,3) as $i)
                                    <td><input type="number" name="cooking[thermometer_retort][]" value="{{ $cooking['thermometer_retort'][$i] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan</td>
                                    <td>Mpa</td>
                                    <td colspan="2">0.26</td>
                                    @foreach(range(0,3) as $i)
                                    <td><input type="number" name="cooking[tekanan_sterilisasi][]" value="{{ $cooking['tekanan_sterilisasi'][$i] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="3" class="align-middle">12 menit</td>
                                    <td rowspan="3" class="align-middle">16 menit</td>
                                        <td colspan="4"><input type="time" id="waktu_mulai_sterilisasi" name="cooking[waktu_mulai_sterilisasi]" value="{{ $cooking['waktu_mulai_sterilisasi'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Pengecekan</td>
                                    <td>WIB</td>
                                    @foreach(range(0,3) as $i)
                                    <td><input type="time" name="cooking[waktu_pengecekan_sterilisasi][]" value="{{ $cooking['waktu_pengecekan_sterilisasi'][$i] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                        <td colspan="4"><input type="time" id="waktu_selesai_sterilisasi" name="cooking[waktu_selesai_sterilisasi]" value="{{ $cooking['waktu_selesai_sterilisasi'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <td><input type="number" name="cooking[suhu_air_pendinginan_awal]" value="{{ $cooking['suhu_air_pendinginan_awal'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0.26</td>
                                    <td><input type="number" name="cooking[tekanan_pendinginan_awal]" value="{{ $cooking['tekanan_pendinginan_awal'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="2" class="align-middle">3 - 6 menit</td>
                                    <td><input type="time" id="waktu_mulai_pendinginan_awal" name="cooking[waktu_mulai_pendinginan_awal]" value="{{ $cooking['waktu_mulai_pendinginan_awal'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td><input type="time" id="waktu_selesai_pendinginan_awal" name="cooking[waktu_selesai_pendinginan_awal]" value="{{ $cooking['waktu_selesai_pendinginan_awal'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <td><input type="number" name="cooking[suhu_air_pendinginan]" value="{{ $cooking['suhu_air_pendinginan'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0.26</td>
                                    <td><input type="number" name="cooking[tekanan_pendinginan]" value="{{ $cooking['tekanan_pendinginan'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="2" class="align-middle">5 menit</td>
                                    <td><input type="time" id="waktu_mulai_pendinginan" name="cooking[waktu_mulai_pendinginan]" value="{{ $cooking['waktu_mulai_pendinginan'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td><input type="time" id="waktu_selesai_pendinginan" name="cooking[waktu_selesai_pendinginan]" value="{{ $cooking['waktu_selesai_pendinginan'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <td><input type="number" name="cooking[suhu_air_akhir]" value="{{ $cooking['suhu_air_akhir'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0</td>
                                    <td><input type="number" name="cooking[tekanan_akhir]" value="{{ $cooking['tekanan_akhir'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="2" class="align-middle">2 - 3 menit</td>
                                    <td><input type="time" id="waktu_mulai_akhir" name="cooking[waktu_mulai_akhir]" value="{{ $cooking['waktu_mulai_akhir'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td><input type="time" id="waktu_selesai_akhir" name="cooking[waktu_selesai_akhir]" value="{{ $cooking['waktu_selesai_akhir'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                            <input type="time" name="cooking[waktu_mulai_total]" id="waktu_mulai_total" value="{{ $cooking['waktu_mulai_total'] ?? '' }}" class="form-control form-control-sm text-center" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Waktu Selesai</td>
                                        <td>WIB</td>
                                        <td>
                                            <input type="time" name="cooking[waktu_selesai_total]" id="waktu_selesai_total" value="{{ $cooking['waktu_selesai_total'] ?? '' }}" class="form-control form-control-sm text-center" readonly>
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
                                ['field'=>'suhu_produk_akhir','label'=>'Suhu Varian Akhir','satuan'=>'°C','standar'=>'48 ± 2','alternatif'=>''],
                                ['field'=>'panjang','label'=>'Panjang','satuan'=>'Cm','standar'=>'14 - 15','alternatif'=>'9 - 10.5'],
                                ['field'=>'diameter','label'=>'Diameter','satuan'=>'Cm','standar'=>'14.0 - 14.5','alternatif'=>'13.5 - 14.5'],
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
                                        <input type="number" name="cooking[{{ $item['field'] }}]" class="form-control form-control-sm text-center" step="0.01" value="{{ $cooking[$item['field']] ?? '' }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================= TOTAL REJECT ================= --}}
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <strong>TOTAL REJECT</strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                            </thead>
                            <tbody id="rejectTableBody">
                                <tr>
                                    <td colspan="3" class="text-muted py-4">Belum ada batch dipilih</td>
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
                    placeholder="Tambahkan catatan bila ada">{{ old('catatan', $pemasakan->catatan) }}</textarea>
                </div>
            </div>

            {{-- ===================== Tombol ===================== --}}
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

@endsection

@push('scripts')
{{-- Select2 CSS & JS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

{{-- Bootstrap Select CSS & JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }

        // === Select2 Batch Integration ===
        const produkSelect = $('#nama_produk');
        let initialRejects = @json(is_array($pemasakan->total_reject) ? $pemasakan->total_reject : (json_decode($pemasakan->total_reject, true) ?? []));
        let isFirstLoad = true;

        function initBatchSelect(selectElem) {
            let produkValue = produkSelect.val();
            
            if (selectElem.data('select2')) {
                selectElem.select2('destroy');
            }
            
            if (!produkValue) {
                selectElem.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
                selectElem.prop("disabled", true);
                return;
            }
            
            selectElem.prop("disabled", false);
            
            selectElem.select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: "-- Pilih Batch --",
                allowClear: true,
                ajax: {
                    url: "{{ url('/lookup/batch-packing') }}/" + encodeURIComponent(produkValue),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true
                }
            });
        }

        // Initialize select2 for existing kode_produksi selects
        $('.kode_produksi').each(function() {
            initBatchSelect($(this));
        });

        // LOGIC UNTUK RENDER TABEL REJECT
        function renderRejectTable() {
            let currentRejects = [];
            $('.reject-input').each(function() {
                let idx = $(this).data('index');
                currentRejects[idx] = $(this).val();
            });

            if (isFirstLoad) {
                currentRejects = initialRejects;
                isFirstLoad = false;
            }

            let tbody = $('#rejectTableBody');
            tbody.empty();
            let hasData = false;

            $('.kode_produksi').each(function(index) {
                let value = $(this).val();
                let text = $(this).find('option:selected').text();

                if (value && value !== "") {
                    hasData = true;
                    
                    let rawVal = currentRejects[index];
                    let rejectVal = (rawVal === null || rawVal === undefined || rawVal === '') ? '' : rawVal;

                    tbody.append(`
                        <tr>
                            <td class="text-start fw-semibold">${text}</td>
                            <td>Kg</td>
                            <td>
                                <input type="number"
                                    step="0.01"
                                    name="total_reject[${index}]" 
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
                $('.kode_produksi').html('<option value="">Pilih Varian Terlebih Dahulu</option>').prop('disabled', true);
                renderRejectTable();
                return;
            }
            
            $('.kode_produksi').each(function() {
                initBatchSelect($(this));
            });
            
            renderRejectTable();
        });

        // Trigger render saat opsi batch dipilih manual
        $(document).on('change', '.kode_produksi', function() {
            renderRejectTable();
        });

        // Tambah Row
        $(document).on('click', '.addRow', function() {
            // Destroy select2 before clone
            const firstSelect = $('.batch-row:first').find('.kode_produksi');
            if (firstSelect.hasClass('select2-hidden-accessible')) {
                firstSelect.select2('destroy');
            }

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
            
            // Re-initialize select2 for all selects
            $('.kode_produksi').each(function() {
                initBatchSelect($(this));
            });
            
            renderRejectTable();
        });

        // Hapus Row
        $(document).on('click', '.removeRow', function() {
            $(this).closest('.batch-row').remove();
            hitungTotalTray();
            renderRejectTable();
        });

        // Hitung Tray
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
        
        hitungTotalTray();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function autoChainTime(fromId, toId) {
            const fromInput = document.getElementById(fromId);
            const toInput = document.getElementById(toId);

            if (!fromInput || !toInput) return;

            fromInput.addEventListener('change', function() {
                if (this.value && !toInput.disabled) {
                    toInput.value = this.value;
                    // Trigger manual change event agar chain reaction berjalan berantai
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
        
        // 6. PROSES AKHIR -> TOTAL WAKTU PROSES (WAKTU SELESAI TOTAL)
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
            syncWaktuMulaiTotal(); 
        }
    });
</script>

@endpush