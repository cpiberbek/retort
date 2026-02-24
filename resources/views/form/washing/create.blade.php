@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Washing - Drying
            </h4>

            <form id="washingForm" action="{{ route('washing.store') }}" method="POST">
                @csrf

                {{-- IDENTIFIKASI --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>IDENTIFIKASI</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift')=='1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift')=='2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift')=='3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select name="nama_produk"
                                    class="form-control @error('nama_produk') is-invalid @enderror"
                                    data-live-search="true" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk')==$produk->
                                        nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-danger">@error('nama_produk') {{ $message }} @enderror</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <select name="kode_produksi" id="kode_produksi"
                                    class="form-control @error('kode_produksi') is-invalid @enderror" required disabled>
                                    <option value="">Pilih Varian Terlebih Dahulu</option>
                                </select>
                                <small id="kodeError" class="text-danger">
                                    @error('kode_produksi') {{ $message }} @enderror
                                </small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="jamMulaiInput" name="pukul" class="form-control"
                                    value="{{ old('pukul', $data->pukul ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PENGECEKAN --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>Pengecekan</strong></div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <tbody>
                                    <tr>
                                        <td class="text-left align-middle">Panjang Produk Akhir (Cm)</td>
                                        <td>
                                            <input type="number" name="panjang_produk" id="panjang_produk" 
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Diameter Produk Akhir (Mm)</td>
                                        <td>
                                            <input type="number" name="diameter_produk" id="diameter_produk"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Airtrap</td>
                                        <td>
                                            <select name="airtrap" id="airtrap"
                                                class="form-control form-control-sm text-center">
                                                <option value="Ada">Ada</option>
                                                <option value="Tidak Ada">Tidak Ada</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Lengket</td>
                                        <td>
                                            <select name="lengket" id="lengket"
                                                class="form-control form-control-sm text-center">
                                                <option value="Ada">Ada</option>
                                                <option value="Tidak Ada">Tidak Ada</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Sisa Adonan</td>
                                        <td>
                                            <select name="sisa_adonan" id="sisa_adonan"
                                                class="form-control form-control-sm text-center">
                                                <option value="Ada">Ada</option>
                                                <option value="Tidak Ada">Tidak Ada</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Cek Kebocoran / Vacuum</td>
                                        <td>
                                            <select name="kebocoran" id="kebocoran"
                                                class="form-control form-control-sm text-center">
                                                <option value="Ok">Ok</option>
                                                <option value="Tidak Ok">Tidak Ok</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Kekuatan Seal</td>
                                        <td>
                                            <select name="kekuatan_seal" id="kekuatan_seal"
                                                class="form-control form-control-sm text-center">
                                                <option value="Ok">Ok</option>
                                                <option value="Tidak Ok">Tidak Ok</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Print Kode Produksi</td>
                                        <td>
                                            <select name="print_kode" id="print_kode"
                                                class="form-control form-control-sm text-center">
                                                <option value="Ok">Ok</option>
                                                <option value="Tidak Ok">Tidak Ok</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- PC KLEER --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>PC Kleer</strong></div>

                    <div class="card-body">

                        <div class="alert alert-danger mt-2 py-3 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong> Standar Pemeriksaan:</strong>
                            <ul class="mb-2 mt-2">
                                <li>Suhu PC Kleer : 46 ± 3 °C</li>
                                <li>Kons. PC Kleer : 0.7% (ayam); 1% (sapi dan RTE); 0.8% (cuci ulang)</li>
                            </ul>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <tbody>
                                    <tr>
                                        <td class="text-left align-middle">Konsentrasi PC Kleer 1 (%)</td>
                                        <td>
                                            <input type="number" name="konsentrasi_pckleer" id="konsentrasi_pckleer"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Suhu PC Kleer 1 (°C)</td>
                                        <td>
                                            <input type="number" name="suhu_pckleer_1" id="suhu_pckleer_1"
                                                class="form-control form-control-sm text-center" step="0.01" min="0"> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Suhu PC Kleer 2 (°C)</td>
                                        <td>
                                            <input type="number" name="suhu_pckleer_2" id="suhu_pckleer_2"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">pH PC Kleer</td>
                                        <td>
                                            <input type="number" name="ph_pckleer" id="ph_pckleer"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Kondisi Air PC Kleer</td>
                                        <td>
                                            <select name="kondisi_air_pckleer" id="kondisi_air_pckleer"
                                                class="form-control form-control-sm text-center">
                                                <option value="OK">OK</option>
                                                <option value="Tidak OK">Tidak OK</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Pottasium Sorbate --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>Pottasium Sorbate</strong></div>
                    <div class="card-body">
                        <div class="alert alert-danger mt-2 py-3 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong> Standar Pemeriksaan:</strong>
                            <ul class="mb-2 mt-2">
                                <li>Kons. Pottasium Sorbate : 0.15%</li>
                            </ul>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <tbody>
                                    <tr>
                                        <td class="text-left align-middle">Konsentrasi Pottasium Sorbate (%)</td>
                                        <td>
                                            <input type="number" name="konsentrasi_pottasium" id="konsentrasi_pottasium"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Suhu Pottasium Sorbate (°C)</td>
                                        <td>
                                            <input type="number" name="suhu_pottasium" id="suhu_pottasium"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">pH Pottasium Sorbate</td>
                                        <td>
                                            <input type="number" name="ph_pottasium" id="ph_pottasium"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Kondisi Air Pottasium Sorbate</td>
                                        <td>
                                            <select name="kondisi_pottasium" id="kondisi_pottasium"
                                                class="form-control form-control-sm text-center">
                                                <option value="OK">OK</option>
                                                <option value="Tidak OK">Tidak OK</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Suhu & Speed --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>Suhu & Speed Conveyor</strong></div>
                    <div class="card-body">
                        <div class="alert alert-danger mt-2 py-3 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong> Standar Pemeriksaan:</strong>
                            <ul class="mb-2 mt-2">
                                <li>Suhu Heater : 125 - 135 °C</li>
                            </ul>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <tbody>
                                    <tr>
                                        <td class="text-left align-middle">Suhu Heater (°C)</td>
                                        <td>
                                            <input type="number" name="suhu_heater" id="suhu_heater"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Speed Conv. Drying 1</td>
                                        <td>
                                            <input type="number" name="speed_1" id="speed_1"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Speed Conv. Drying 2</td>
                                        <td>
                                            <input type="number" name="speed_2" id="speed_2"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Speed Conv. Drying 3</td>
                                        <td>
                                            <input type="number" name="speed_3" id="speed_3"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Speed Conv. Drying 4</td>
                                        <td>
                                            <input type="number" name="speed_4" id="speed_4"
                                                class="form-control form-control-sm text-center" step="0.01" min="0">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- CATATAN --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3"
                            placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('washing.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i>
                        Kembali</a>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function(){ 
        
    // $('.selectpicker').selectpicker();

    const dateInput = document.getElementById("dateInput");
    const shiftInput = document.getElementById("shiftInput");
    const timeInput = document.getElementById("jamMulaiInput");

    if(!dateInput.value){
        let now = new Date();
        dateInput.value = now.toISOString().slice(0,10);
        timeInput.value = now.toTimeString().slice(0,5);

        let hour = now.getHours();
        if(hour >= 7 && hour < 15) shiftInput.value = "1";
        else if(hour >= 15 && hour < 23) shiftInput.value = "2";
        else shiftInput.value = "3";
    }

    const produkSelect = document.querySelector('select[name="nama_produk"]');
    const batchSelect = document.getElementById('kode_produksi');
    const expDateInput = document.getElementById('exp_date');

    // Disable batch saat awal load (jika tidak ada old value)
    if (!produkSelect.value) {
        batchSelect.disabled = true;
    }

    produkSelect.addEventListener('change', function () {
        let namaProduk = this.value;
        // console.log("Nama Produk dipilih:", namaProduk);

        // Jika user mengosongkan nama produk
        if (!namaProduk) {
            batchSelect.innerHTML = '<option value="">Pilih Varian Terlebih Dahulu</option>';
            batchSelect.disabled = true;
            expDateInput.value = '';
            return;
        }

        const url = "{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}".replace('__PRODUK__', encodeURIComponent(namaProduk));
        fetch(url)
        .then(response => response.json())
        .then(data => {
            batchSelect.disabled = false; 
            batchSelect.innerHTML = ""; // bersihkan dulu

            if (data.length === 0) {
                batchSelect.innerHTML = '<option value="">Batch Tidak Ditemukan</option>';
                batchSelect.disabled = true;
                return;
            }

            // Jika ada data, baru tampilkan default option
            batchSelect.innerHTML = '<option value="">-- Pilih Batch --</option>';

            data.forEach(batch => {
                batchSelect.innerHTML += `<option value="${batch.kode_produksi}">${batch.kode_produksi}</option>`;
            });
        });
    });

    });
</script>
@endsection