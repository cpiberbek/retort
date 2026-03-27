@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Update Pemeriksaan Stuffing Sosis Retort
            </h4>

            <form id="stuffingForm" action="{{ route('stuffing.update_qc', $stuffing->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Stuffing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            {{-- Tanggal --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput"
                                    class="form-control @error('date') is-invalid @enderror"
                                    value="{{ old('date', $stuffing->date) }}" {{ $stuffing->date ? 'readonly' : '' }}
                                required>
                                <small class="text-danger">@error('date') {{ $message }} @enderror</small>
                            </div>

                            {{-- Shift --}}
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                @if($stuffing->shift)
                                <input type="text" class="form-control" value="Shift {{ $stuffing->shift }}" readonly>
                                <input type="hidden" name="shift" value="{{ $stuffing->shift }}">
                                @else
                                <select name="shift" id="shiftInput"
                                    class="form-control @error('shift') is-invalid @enderror" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift', $stuffing->shift)=='1' ? 'selected' : '' }}>Shift
                                        1</option>
                                    <option value="2" {{ old('shift', $stuffing->shift)=='2' ? 'selected' : '' }}>Shift
                                        2</option>
                                    <option value="3" {{ old('shift', $stuffing->shift)=='3' ? 'selected' : '' }}>Shift
                                        3</option>
                                </select>
                                @endif
                                <small class="text-danger">@error('shift') {{ $message }} @enderror</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- Nama Varian --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                @if($stuffing->nama_produk)
                                <input type="text" class="form-control" value="{{ $stuffing->nama_produk }}" readonly>
                                <input type="hidden" name="nama_produk" value="{{ $stuffing->nama_produk }}">
                                @else
                                <select name="nama_produk"
                                    class="form-control selectpicker @error('nama_produk') is-invalid @enderror"
                                    data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $stuffing->
                                        nama_produk)==$produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                                @endif
                                <small class="text-danger">@error('nama_produk') {{ $message }} @enderror</small>
                            </div>

                            {{-- Kode Batch --}}
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <input type="text" name="kode_produksi" id="kode_produksi"
                                    class="form-control @error('kode_produksi') is-invalid @enderror" maxlength="10"
                                    value="{{ old('kode_produksi', $stuffing->mincing->kode_produksi ?? '-') }}" {{
                                    $stuffing->kode_produksi ? 'readonly' : '' }} required>
                                <small id="kodeError" class="text-danger">@error('kode_produksi') {{ $message }}
                                    @enderror</small>
                            </div>

                            {{-- Exp Date --}}
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="exp_date" id="exp_date"
                                    class="form-control @error('exp_date') is-invalid @enderror"
                                    value="{{ old('exp_date', $stuffing->exp_date) }}" {{ $stuffing->exp_date ?
                                'readonly' : '' }}>
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
                        {{-- Nama Mesin --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Mesin</label>
                            @if($stuffing->kode_mesin)
                            <input type="text" class="form-control" value="{{ $stuffing->kode_mesin }}" readonly>
                            <input type="hidden" name="kode_mesin" value="{{ $stuffing->kode_mesin }}">
                            @else
                            <select name="kode_mesin" class="form-control @error('kode_mesin') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($mesins as $m)
                                <option value="{{ $m->nama_mesin }}" {{ old('kode_mesin', $stuffing->kode_mesin) ==
                                    $m->nama_mesin ? 'selected' : '' }}>
                                    {{ $m->nama_mesin }}
                                </option>
                                @endforeach
                            </select>
                            @endif
                            <small class="text-danger">@error('kode_mesin') {{ $message }} @enderror</small>
                        </div>

                        {{-- Jam Mulai --}}
                        <div class="mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" id="jamMulaiInput" name="jam_mulai"
                                class="form-control @error('jam_mulai') is-invalid @enderror"
                                value="{{ old('jam_mulai', $stuffing->jam_mulai) }}" {{ $stuffing->jam_mulai ?
                            'readonly' : '' }}>
                            <small class="text-danger">@error('jam_mulai') {{ $message }} @enderror</small>
                        </div>

                        {{-- Parameter Adonan --}}
                        <hr>
                        <h6 class="fw-bold text-primary">Parameter Adonan</h6>
                        <div class="mb-3">
                            <label class="form-label">Suhu (°C)</label>
                            <input type="number" step="0.01" name="suhu"
                                class="form-control @error('suhu') is-invalid @enderror"
                                value="{{ old('suhu', $stuffing->suhu) }}" {{ $stuffing->suhu ? 'readonly' : '' }}>
                            <small class="text-danger">@error('suhu') {{ $message }} @enderror</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sensori</label>
                            @if($stuffing->sensori)
                            <input type="text" class="form-control" value="{{ $stuffing->sensori }}" readonly>
                            <input type="hidden" name="sensori" value="{{ $stuffing->sensori }}">
                            @else
                            <select name="sensori" class="form-control @error('sensori') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('sensori', $stuffing->sensori)=='OK' ? 'selected' : '' }}>OK
                                </option>
                                <option value="Tidak OK" {{ old('sensori', $stuffing->sensori)=='Tidak OK' ? 'selected'
                                    : '' }}>Tidak OK</option>
                            </select>
                            @endif
                            <small class="text-danger">@error('sensori') {{ $message }} @enderror</small>
                        </div>

                        {{-- Parameter Stuffing --}}
                        <hr>
                        <h6 class="fw-bold text-primary">Parameter Stuffing</h6>
                        @php
                        $stuffingFields = [
                        'kecepatan_stuffing','panjang_pcs','berat_pcs',
                        'kebersihan_seal','kekuatan_seal','diameter_klip','print_kode','lebar_cassing'
                        ];
                        @endphp

                        @foreach($stuffingFields as $field)
                        <div class="mb-3">
                            <label class="form-label">{{ ucwords(str_replace('_',' ',$field)) }}</label>
                            @if(in_array($field,['kebersihan_seal','kekuatan_seal','print_kode']))
                            @if($stuffing->$field)
                            <input type="text" class="form-control" value="{{ $stuffing->$field }}" readonly>
                            <input type="hidden" name="{{ $field }}" value="{{ $stuffing->$field }}">
                            @else
                            <select name="{{ $field }}" class="form-control @error($field) is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old($field, $stuffing->$field)=='OK' ? 'selected' : '' }}>OK
                                </option>
                                <option value="Tidak OK" {{ old($field, $stuffing->$field)=='Tidak OK' ? 'selected' : ''
                                    }}>Tidak OK</option>
                            </select>
                            @endif
                            @else
                            <input type="number" step="0.01" name="{{ $field }}"
                                class="form-control @error($field) is-invalid @enderror"
                                value="{{ old($field, $stuffing->$field) }}" {{ $stuffing->$field ? 'readonly' : '' }}>
                            @endif
                            <small class="text-danger">@error($field) {{ $message }} @enderror</small>
                        </div>
                        @endforeach

                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror"
                            rows="3">{{ old('catatan', $stuffing->catatan) }}</textarea>
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
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
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

    if(kodeInput && !kodeInput.readOnly){
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
    }
</script>
@endsection