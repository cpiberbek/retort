@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Edit Pemeriksaan Stuffing Sosis Retort
            </h4>

            <form id="stuffingForm" action="{{ route('stuffing.edit_spv', $stuffing->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Stuffing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $stuffing->date) }}" required>
                                <small class="text-danger">@error('date') {{ $message }} @enderror</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control @error('shift') is-invalid @enderror" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift', $stuffing->shift)=='1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $stuffing->shift)=='2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $stuffing->shift)=='3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                                <small class="text-danger">@error('shift') {{ $message }} @enderror</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker @error('nama_produk') is-invalid @enderror" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $stuffing->nama_produk)==$produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-danger">@error('nama_produk') {{ $message }} @enderror</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <input type="text" name="kode_produksi" id="kode_produksi" class="form-control @error('kode_produksi') is-invalid @enderror" maxlength="10" value="{{ old('kode_produksi', $stuffing->kode_produksi) }}" required>
                                <small id="kodeError" class="text-danger">@error('kode_produksi') {{ $message }} @enderror</small>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="exp_date" id="exp_date" class="form-control @error('exp_date') is-invalid @enderror" value="{{ old('exp_date', $stuffing->exp_date) }}">
                                <small class="text-muted">Dihitung otomatis +7 bulan dari kode batch</small>
                                <small class="text-danger">@error('exp_date') {{ $message }} @enderror</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== DATA STUFFING ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <strong>Data Stuffing</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Mesin</label>
                            <select name="kode_mesin" class="form-control @error('kode_mesin') is-invalid @enderror" required>
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($mesins as $m)
                                <option value="{{ $m->nama_mesin }}" {{ old('kode_mesin', $stuffing->kode_mesin) == $m->nama_mesin ? 'selected' : '' }}>
                                    {{ $m->nama_mesin }}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-danger">@error('kode_mesin') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" id="jamMulaiInput" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai', $stuffing->jam_mulai) }}">
                            <small class="text-danger">@error('jam_mulai') {{ $message }} @enderror</small>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-primary">Parameter Adonan</h6>

                        <div class="mb-3">
                            <label class="form-label">Suhu (°C)</label>
                            <input type="number" step="0.01" name="suhu" class="form-control @error('suhu') is-invalid @enderror" value="{{ old('suhu', $stuffing->suhu) }}">
                            <small class="text-danger">@error('suhu') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sensori</label>
                            <select name="sensori" class="form-control @error('sensori') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('sensori', $stuffing->sensori)=='OK' ? 'selected' : '' }}>OK</option>
                                <option value="Tidak OK" {{ old('sensori', $stuffing->sensori)=='Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                            </select>
                            <small class="text-danger">@error('sensori') {{ $message }} @enderror</small>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-primary">Parameter Stuffing</h6>

                        <div class="mb-3">
                            <label class="form-label">Kecepatan Stuffing (/mnt)</label>
                            <input type="number" step="0.01" name="kecepatan_stuffing" class="form-control @error('kecepatan_stuffing') is-invalid @enderror" value="{{ old('kecepatan_stuffing', $stuffing->kecepatan_stuffing) }}">
                            <small class="text-danger">@error('kecepatan_stuffing') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Panjang per pcs (cm)</label>
                            <input type="number" step="0.01" name="panjang_pcs" class="form-control @error('panjang_pcs') is-invalid @enderror" value="{{ old('panjang_pcs', $stuffing->panjang_pcs) }}">
                            <small class="text-danger">@error('panjang_pcs') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Berat per pcs (gr)</label>
                            <input type="number" step="0.01" name="berat_pcs" class="form-control @error('berat_pcs') is-invalid @enderror" value="{{ old('berat_pcs', $stuffing->berat_pcs) }}">
                            <small class="text-danger">@error('berat_pcs') {{ $message }} @enderror</small>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Kebersihan Ujung Seal</label>
                            <select name="kebersihan_seal" class="form-control @error('kebersihan_seal') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('kebersihan_seal', $stuffing->kebersihan_seal)=='OK' ? 'selected' : '' }}>OK</option>
                                <option value="Tidak OK" {{ old('kebersihan_seal', $stuffing->kebersihan_seal)=='Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                            </select>
                            <small class="text-danger">@error('kebersihan_seal') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kekuatan Seal</label>
                            <select name="kekuatan_seal" class="form-control @error('kekuatan_seal') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('kekuatan_seal', $stuffing->kekuatan_seal)=='OK' ? 'selected' : '' }}>OK</option>
                                <option value="Tidak OK" {{ old('kekuatan_seal', $stuffing->kekuatan_seal)=='Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                            </select>
                            <small class="text-danger">@error('kekuatan_seal') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Diameter Klip (mm)</label>
                            <input type="number" step="0.01" name="diameter_klip" class="form-control @error('diameter_klip') is-invalid @enderror" value="{{ old('diameter_klip', $stuffing->diameter_klip) }}">
                            <small class="text-danger">@error('diameter_klip') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Print Kode Production</label>
                            <select name="print_kode" class="form-control @error('print_kode') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('print_kode', $stuffing->print_kode)=='OK' ? 'selected' : '' }}>OK</option>
                                <option value="Tidak OK" {{ old('print_kode', $stuffing->print_kode)=='Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                            </select>
                            <small class="text-danger">@error('print_kode') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lebar Cassing (mm)</label>
                            <input type="number" step="0.01" name="lebar_cassing" class="form-control @error('lebar_cassing') is-invalid @enderror" value="{{ old('lebar_cassing', $stuffing->lebar_cassing) }}">
                            <small class="text-danger">@error('lebar_cassing') {{ $message }} @enderror</small>
                        </div>
                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan', $stuffing->catatan) }}</textarea>
                        <small class="text-danger">@error('catatan') {{ $message }} @enderror</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('stuffing.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
    });

    document.addEventListener("DOMContentLoaded", function () {
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
    });

    // Validasi & Exp Date
    const kodeInput = document.getElementById('kode_produksi');
    const expDateInput = document.getElementById('exp_date');
    const kodeError = document.getElementById('kodeError');

    kodeInput.addEventListener('input', function() {
        let value = this.value.toUpperCase().replace(/\s+/g, '');
        this.value = value;
        kodeError.textContent = '';
        expDateInput.value = '';

        if(value.length !== 10){
            kodeError.textContent = "Kode batch harus 10 karakter.";
            return;
        }
        if(!/^[A-Z0-9]+$/.test(value)){
            kodeError.textContent = "Hanya huruf besar & angka.";
            return;
        }
        const bulanChar = value.charAt(1);
        if(!/^[A-L]$/.test(bulanChar)){
            kodeError.textContent = "Huruf bulan harus A–L.";
            return;
        }
        const hari = parseInt(value.substr(2,2));
        if(isNaN(hari) || hari <1 || hari>31){
            kodeError.textContent = "Tanggal harus 01–31.";
            return;
        }
        const bulanMap = {A:0,B:1,C:2,D:3,E:4,F:5,G:6,H:7,I:8,J:9,K:10,L:11};
        let kodeBulan = bulanMap[bulanChar];
        let now = new Date();
        let tahun = now.getFullYear();
        if(kodeBulan < now.getMonth()) tahun++;

        let expDate = new Date(tahun, kodeBulan, hari);
        expDate.setMonth(expDate.getMonth() + 7);
        expDateInput.value = expDate.toISOString().slice(0,10);
    });
</script>
@endsection
