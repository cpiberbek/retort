@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Edit Pemeriksaan Washing - Drying
            </h4>

            <form id="washingForm" action="{{ route('washing.edit_spv', $washing->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- IDENTIFIKASI --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>IDENTIFIKASI</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" 
                                value="{{ old('date', $washing->date ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ (old('shift', $washing->shift ?? '') == '1') ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ (old('shift', $washing->shift ?? '') == '2') ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ (old('shift', $washing->shift ?? '') == '3') ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" 
                                        {{ (old('nama_produk', $washing->nama_produk ?? '') == $produk->nama_produk) ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <input type="text" name="kode_produksi" id="kode_produksi" class="form-control" maxlength="10" 
                                value="{{ old('kode_produksi', $washing->kode_produksi ?? '') }}" required>
                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="timeInput" name="pukul" class="form-control"
                                value="{{ old('pukul', $washing->pukul ?? '') }}" required>
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
                                        <td class="text-left align-middle">Panjang Varian Akhir (Cm)</td>
                                        <td>
                                            <input type="number" name="panjang_produk" id="panjang_produk" class="form-control form-control-sm text-center" step="0.01 " min="0"
                                            value="{{ old('panjang_produk', $washing->panjang_produk ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Diameter Varian Akhir (Mm)</td>
                                        <td>
                                            <input type="number" name="diameter_produk" id="diameter_produk" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('diameter_produk', $washing->diameter_produk ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Airtrap</td>
                                        <td>
                                            <select name="airtrap" id="airtrap" class="form-control form-control-sm text-center">
                                                <option value="Ada" {{ (old('airtrap', $washing->airtrap ?? '') == 'Ada') ? 'selected' : '' }}>Ada</option>
                                                <option value="Tidak Ada" {{ (old('airtrap', $washing->airtrap ?? '') == 'Tidak Ada') ? 'selected' : '' }}>Tidak Ada</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Lengket</td>
                                        <td>
                                            <select name="lengket" id="lengket" class="form-control form-control-sm text-center">
                                                <option value="Ada" {{ (old('lengket', $washing->lengket ?? '') == 'Ada') ? 'selected' : '' }}>Ada</option>
                                                <option value="Tidak Ada" {{ (old('lengket', $washing->lengket ?? '') == 'Tidak Ada') ? 'selected' : '' }}>Tidak Ada</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Sisa Adonan</td>
                                        <td>
                                            <select name="sisa_adonan" id="sisa_adonan" class="form-control form-control-sm text-center">
                                                <option value="Ada" {{ (old('sisa_adonan', $washing->sisa_adonan ?? '') == 'Ada') ? 'selected' : '' }}>Ada</option>
                                                <option value="Tidak Ada" {{ (old('sisa_adonan', $washing->sisa_adonan ?? '') == 'Tidak Ada') ? 'selected' : '' }}>Tidak Ada</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Kekuatan Seal</td>
                                        <td>
                                            <select name="kekuatan_seal" id="kekuatan_seal" class="form-control form-control-sm text-center">
                                                <option value="Ok" {{ (old('kekuatan_seal', $washing->kekuatan_seal ?? '') == 'Ok') ? 'selected' : '' }}>Ok</option>
                                                <option value="Tidak Ok" {{ (old('kekuatan_seal', $washing->kekuatan_seal ?? '') == 'Tidak Ok') ? 'selected' : '' }}>Tidak Ok</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Print Kode Batch</td>
                                        <td>
                                            <select name="print_kode" id="print_kode" class="form-control form-control-sm text-center">
                                                <option value="Ok" {{ (old('print_kode', $washing->print_kode ?? '') == 'Ok') ? 'selected' : '' }}>Ok</option>
                                                <option value="Tidak Ok" {{ (old('print_kode', $washing->print_kode ?? '') == 'Tidak Ok') ? 'selected' : '' }}>Tidak Ok</option>
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
                                            <input type="number" name="konsentrasi_pckleer" id="konsentrasi_pckleer" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('konsentrasi_pckleer', $washing->konsentrasi_pckleer ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Suhu PC Kleer 1 (°C)</td>
                                        <td>
                                            <input type="number" name="suhu_pckleer_1" id="suhu_pckleer_1" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('suhu_pckleer_1', $washing->suhu_pckleer_1 ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Suhu PC Kleer 2 (°C)</td>
                                        <td>
                                            <input type="number" name="suhu_pckleer_2" id="suhu_pckleer_2" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('suhu_pckleer_2', $washing->suhu_pckleer_2 ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">pH PC Kleer</td>
                                        <td>
                                            <input type="number" name="ph_pckleer" id="ph_pckleer" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('ph_pckleer', $washing->ph_pckleer ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Kondisi Air PC Kleer</td>
                                        <td>
                                            <select name="kondisi_air_pckleer" id="kondisi_air_pckleer" class="form-control form-control-sm text-center">
                                                <option value="OK" {{ (old('kondisi_air_pckleer', $washing->kondisi_air_pckleer ?? '') == 'OK') ? 'selected' : '' }}>OK</option>
                                                <option value="Tidak OK" {{ (old('kondisi_air_pckleer', $washing->kondisi_air_pckleer ?? '') == 'Tidak OK') ? 'selected' : '' }}>Tidak OK</option>
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
                                            <input type="number" name="konsentrasi_pottasium" id="konsentrasi_pottasium" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('konsentrasi_pottasium', $washing->konsentrasi_pottasium ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Suhu Pottasium Sorbate (°C)</td>
                                        <td>
                                            <input type="number" name="suhu_pottasium" id="suhu_pottasium" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('suhu_pottasium', $washing->suhu_pottasium ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">pH Pottasium Sorbate</td>
                                        <td>
                                            <input type="number" name="ph_pottasium" id="ph_pottasium" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('ph_pottasium', $washing->ph_pottasium ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Kondisi Air Pottasium Sorbate</td>
                                        <td>
                                            <select name="kondisi_pottasium" id="kondisi_pottasium" class="form-control form-control-sm text-center">
                                                <option value="OK" {{ (old('kondisi_pottasium', $washing->kondisi_pottasium ?? '') == 'OK') ? 'selected' : '' }}>OK</option>
                                                <option value="Tidak OK" {{ (old('kondisi_pottasium', $washing->kondisi_pottasium ?? '') == 'Tidak OK') ? 'selected' : '' }}>Tidak OK</option>
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
                                <li>Suhu Heater   : 125 - 135 °C</li>                           
                            </ul>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <tbody>
                                    <tr>
                                        <td class="text-left align-middle">Suhu Heater (°C)</td>
                                        <td>
                                            <input type="number" name="suhu_heater" id="suhu_heater" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('suhu_heater', $washing->suhu_heater ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Speed Conv. Drying 1</td>
                                        <td>
                                            <input type="number" name="speed_1" id="speed_1" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('speed_1', $washing->speed_1 ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Speed Conv. Drying 2</td>
                                        <td>
                                            <input type="number" name="speed_2" id="speed_2" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('speed_2', $washing->speed_2 ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Speed Conv. Drying 3</td>
                                        <td>
                                            <input type="number" name="speed_3" id="speed_3" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('speed_3', $washing->speed_3 ?? '') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left align-middle">Speed Conv. Drying 4</td>
                                        <td>
                                            <input type="number" name="speed_4" id="speed_4" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old('speed_4', $washing->speed_4 ?? '') }}">
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
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $washing->catatan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('washing.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
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
    $(document).ready(function(){ 
        const kodeInput = $('#kode_produksi');
        const kodeError = $('#kodeError');
        const form = $('#washingForm');

        function validateKode() {
            let value = kodeInput.val().toUpperCase().replace(/\s+/g, '');
            kodeInput.val(value);
            kodeError.text('').addClass('d-none');

            if(value.length !== 10) {
                kodeError.text('Kode batch harus terdiri dari 10 karakter').removeClass('d-none');
                return false;
            }

            if(!/^[A-Z0-9]+$/.test(value)) {
                kodeError.text('Kode batch hanya boleh huruf besar dan angka').removeClass('d-none');
                return false;
            }

            if(!/^[A-L]$/.test(value.charAt(1))) {
                kodeError.text('Karakter ke-2 harus huruf bulan (A-L)').removeClass('d-none');
                return false;
            }

            let hari = parseInt(value.substr(2,2),10);
            if(isNaN(hari) || hari < 1 || hari > 31) {
                kodeError.text('Karakter ke-3 dan ke-4 harus tanggal valid (01-31)').removeClass('d-none');
                return false;
            }

            return true;
        }

        kodeInput.on('input', validateKode);

        form.on('submit', function(e){
            if(!validateKode()){
                e.preventDefault();
                alert('Kode batch tidak valid! Periksa kembali sebelum menyimpan.');
                kodeInput.focus();
            }
        });
    });
</script>
@endsection
