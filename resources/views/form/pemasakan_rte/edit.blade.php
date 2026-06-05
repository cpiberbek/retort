@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pengecekan Pemasakan RTE
            </h4>
            
            <form id="pemasakanForm" action="{{ route('pemasakan_rte.edit_spv', $pemasakan_rte->uuid) }}" method="POST">
                @csrf
                @method('PUT')

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
                                value="{{ old('date', $pemasakan_rte->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift', $pemasakan_rte->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $pemasakan_rte->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $pemasakan_rte->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
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
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $pemasakan_rte->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
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
                                        {{ old('no_chamber', $pemasakan_rte->no_chamber ?? '') == $list_chamber->nama_mesin ? 'selected' : '' }}>
                                        {{ $list_chamber->nama_mesin }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ?? --}}
                        {{-- Baris 3: Kode Produksi & Berat Produk --}}
                        <div class="row mb-3">
                            @php
                            $batches = collect();
                            @endphp

                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <select name="kode_produksi" class="form-control" id="kode_batch" required>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->uuid }}" {{ $pemasakan_rte->kode_produksi == $batch->uuid ? 'selected' : '' }}>
                                            {{ $batch->kode_produksi }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pisahkan dengan tanda <strong>/</strong></small><br>
                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Berat Varian (gram)</label>
                                <input type="number" name="berat_produk" id="berat_produk"
                                class="form-control" step="0.1"
                                value="{{ old('berat_produk', $pemasakan_rte->berat_produk) }}" required>
                            </div>
                        </div>

                        {{-- Baris 4: Suhu Produk & Jumlah Tray --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Suhu Varian (°C)</label><br>
                                <small class="text-danger">Standar: 19 ± 1 °C</small>
                                <input type="number" name="suhu_produk" id="suhu_produk"
                                class="form-control" step="0.1"
                                value="{{ old('suhu_produk', $pemasakan_rte->suhu_produk) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Tray</label><br>
                                <small class="text-danger">Standar: 28 tray</small>
                                <input type="text" name="jumlah_tray" id="jumlah_tray"
                                class="form-control" required
                                value="{{ old('jumlah_tray', $pemasakan_rte->jumlah_tray) }}">
                                <small class="text-muted">Pisahkan dengan tanda <strong>+</strong></small><br>
                                <small id="trayTotal" class="text-success fw-bold"></small><br>
                            </div>
                        </div>
                    </div>
                </div>


                @php
                $cooking = json_decode($pemasakan_rte->cooking, true);
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
                                    <td><input type="time" name="cooking[waktu_mulai_awal]" value="{{ $cooking['waktu_mulai_awal'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td><input type="time" name="cooking[waktu_selesai_awal]" value="{{ $cooking['waktu_selesai_awal'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start">Suhu Air</td>
                                    <td>°C</td>
                                    <td>121.2</td>
                                    <td><input type="number" name="cooking[suhu_air_proses]" value="{{ $cooking['suhu_air_proses'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0.26</td>
                                    <td><input type="number" name="cooking[tekanan_proses]" value="{{ $cooking['tekanan_proses'] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="2" class="text-center align-middle">8 - 10 menit</td>
                                    <td><input type="time" name="cooking[waktu_mulai_proses]" value="{{ $cooking['waktu_mulai_proses'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td><input type="time" name="cooking[waktu_selesai_proses]" value="{{ $cooking['waktu_selesai_proses'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <th colspan="4">Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start">Suhu Air</td>
                                    <td>°C</td>
                                    <td>121.2</td>
                                    @foreach(range(0,3) as $i)
                                    <td><input type="number" name="cooking[suhu_air_sterilisasi][]" value="{{ $cooking['suhu_air_sterilisasi'][$i] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start">Thermometer Retort</td>
                                    <td>°C</td>
                                    <td>121.2</td>
                                    @foreach(range(0,3) as $i)
                                    <td><input type="number" name="cooking[thermometer_retort][]" value="{{ $cooking['thermometer_retort'][$i] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start">Tekanan</td>
                                    <td>Mpa</td>
                                    <td>0.26</td>
                                    @foreach(range(0,3) as $i)
                                    <td><input type="number" name="cooking[tekanan_sterilisasi][]" value="{{ $cooking['tekanan_sterilisasi'][$i] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Mulai</td>
                                    <td>WIB</td>
                                    <td rowspan="3" class="align-middle">50 - 55 menit</td>
                                    <td colspan="4"><input type="time" name="cooking[waktu_mulai_sterilisasi]" value="{{ $cooking['waktu_mulai_sterilisasi'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <td colspan="4"><input type="time" name="cooking[waktu_selesai_sterilisasi]" value="{{ $cooking['waktu_selesai_sterilisasi'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <td><input type="time" name="cooking[waktu_mulai_pendinginan_awal]" value="{{ $cooking['waktu_mulai_pendinginan_awal'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td><input type="time" name="cooking[waktu_selesai_pendinginan_awal]" value="{{ $cooking['waktu_selesai_pendinginan_awal'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <td><input type="time" name="cooking[waktu_mulai_pendinginan]" value="{{ $cooking['waktu_mulai_pendinginan'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td><input type="time" name="cooking[waktu_selesai_pendinginan]" value="{{ $cooking['waktu_selesai_pendinginan'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                    <td><input type="time" name="cooking[waktu_mulai_akhir]" value="{{ $cooking['waktu_mulai_akhir'] ?? '' }}" class="form-control form-control-sm text-center"></td>
                                </tr>
                                <tr>
                                    <td class="text-start">Waktu Selesai</td>
                                    <td>WIB</td>
                                    <td><input type="time" name="cooking[waktu_selesai_akhir]" value="{{ $cooking['waktu_selesai_akhir'] ?? '' }}" class="form-control form-control-sm text-center"></td>
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
                                        <th>Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start">Waktu Mulai</td>
                                        <td>WIB</td>
                                        <td rowspan="2" class="text-center align-middle">85 - 90 menit</td>
                                        <td>
                                            <input type="time" name="cooking[waktu_mulai_total]" id="waktu_mulai_total" value="{{ $cooking['waktu_mulai_total'] ?? '' }}" class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Waktu Selesai</td>
                                        <td>WIB</td>
                                        <td>
                                            <input type="time" name="cooking[waktu_selesai_total]" id="waktu_selesai_total" value="{{ $cooking['waktu_selesai_total'] ?? '' }}" class="form-control form-control-sm text-center">
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
                                    <th rowspan="2" class="text-center align-middle">Sensori</th>
                                    <th>Satuan</th>
                                    <th rowspan="2" class="text-center align-middle">Hasil</th>
                                </tr>
                                <tr>
                                    <th>200 gram</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                ['field'=>'suhu_produk_akhir','label'=>'Suhu Varian Akhir','satuan'=>'°C'],
                                ['field'=>'sobek_seal','label'=>'Sobek Seal','satuan'=>''],
                                ] as $item)
                                <tr>
                                    <td class="text-start">{{ $item['label'] }}</td>
                                    <td>{{ $item['satuan'] }}</td>
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
                    <table class="table table-bordered text-center align-middle">
                        <tbody>
                            <tr>
                                <td class="text-start">Total Reject</td>
                                <td>Kg</td>
                                <td><input type="number" name="total_reject" value="{{ $pemasakan_rte->total_reject ?? '' }}" class="form-control form-control-sm text-center" step="0.01"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- ===================== Catatan ===================== --}}
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>Catatan</strong></div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control" rows="3"
                    placeholder="Tambahkan catatan bila ada">{{ old('catatan', $pemasakan_rte->catatan) }}</textarea>
                </div>
            </div>

            {{-- ===================== Tombol ===================== --}}
            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('pemasakan_rte.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Batal
                </a>
            </div>
        </form>

        <hr>
        <div id="resultArea"></div>
    </div>
</div>
</div>

{{-- ====== Script Tambahan ====== --}}
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
                kodeError.textContent = "Setiap kode produksi harus terdiri dari 10 karakter.";
                kodeError.classList.remove('d-none');
                return false;
            }

            const format = /^[A-Z0-9]+$/;
            if (!format.test(kode)) {
                kodeError.textContent = "Kode produksi hanya boleh huruf besar dan angka.";
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
$(function () {

    const batchSelect = $('#kode_batch');

    function loadBatch(namaProduk, selected = null) {

        batchSelect.prop('disabled', true);
        batchSelect.html('<option value="">-- Pilih Batch --</option>');

        if (!namaProduk) return;

        let url = "{{ route('lookup.batch', ['nama_produk' => ':nama']) }}".replace(':nama', namaProduk);

        $.get(url, function (data) {

            data.forEach(function (item) {
                batchSelect.append(
                    `<option value="${item.uuid}" ${selected == item.uuid ? 'selected' : ''}>${item.kode_produksi}</option>`
                );
            });

            batchSelect.prop('disabled', false);
        });
    }

    $('#nama_produk').on('change', function () {
        loadBatch($(this).val());
    });

    let initialProduk = $('#nama_produk').val();
    let initialBatch = "{{ $pemasakan_rte->kode_produksi ?? '' }}";

    if (initialProduk) {
        loadBatch(initialProduk, initialBatch);
    }

});
</script>

@endsection
