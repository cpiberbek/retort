@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Stuffing Sosis Retort
            </h4>

            <form id="stuffingForm" action="{{ route('stuffing.store') }}" method="POST">
                @csrf

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Stuffing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput"
                                    class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}"
                                    required>
                                <small class="text-danger">@error('date') {{ $message }} @enderror</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput"
                                    class="form-control @error('shift') is-invalid @enderror" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift')=='1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift')=='2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift')=='3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                                <small class="text-danger">@error('shift') {{ $message }} @enderror</small>
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
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="exp_date" id="exp_date"
                                    class="form-control @error('exp_date') is-invalid @enderror"
                                    value="{{ old('exp_date') }}">
                                <small class="text-muted">Dihitung otomatis +7 bulan dari kode produksi</small>
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
                            <select name="kode_mesin" class="form-control @error('kode_mesin') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($mesins as $m)
                                <option value="{{ $m->nama_mesin }}" {{ old('kode_mesin')==$m->nama_mesin ? 'selected' :
                                    '' }}>
                                    {{ $m->nama_mesin }}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-danger">@error('kode_mesin') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" id="jamMulaiInput" name="jam_mulai"
                                class="form-control @error('jam_mulai') is-invalid @enderror"
                                value="{{ old('jam_mulai') }}">
                            <small class="text-danger">@error('jam_mulai') {{ $message }} @enderror</small>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-primary">Parameter Adonan</h6>

                        <div class="mb-3">
                            <label class="form-label">Suhu (°C)</label>
                            <input type="number" step="0.01" name="suhu"
                                class="form-control @error('suhu') is-invalid @enderror" value="{{ old('suhu') }}">
                            <small class="text-danger">@error('suhu') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sensori</label>
                            <select name="sensori" class="form-control @error('sensori') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('sensori')=='OK' ? 'selected' : '' }}>OK</option>
                                <option value="Tidak OK" {{ old('sensori')=='Tidak OK' ? 'selected' : '' }}>Tidak OK
                                </option>
                            </select>
                            <small class="text-danger">@error('sensori') {{ $message }} @enderror</small>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-primary">Parameter Stuffing</h6>

                        <div class="mb-3">
                            <label class="form-label">Kecepatan Stuffing (/mnt)</label>
                            <input type="number" step="0.01" name="kecepatan_stuffing"
                                class="form-control @error('kecepatan_stuffing') is-invalid @enderror"
                                value="{{ old('kecepatan_stuffing') }}">
                            <small class="text-danger">@error('kecepatan_stuffing') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Panjang per pcs (cm)</label>
                            <input type="number" step="0.01" name="panjang_pcs"
                                class="form-control @error('panjang_pcs') is-invalid @enderror"
                                value="{{ old('panjang_pcs') }}">
                            <small class="text-danger">@error('panjang_pcs') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Berat per pcs (gr)</label>
                            <input type="number" step="0.01" name="berat_pcs"
                                class="form-control @error('berat_pcs') is-invalid @enderror"
                                value="{{ old('berat_pcs') }}">
                            <small class="text-danger">@error('berat_pcs') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cek Vakum</label>
                            <select name="cek_vakum" class="form-control @error('cek_vakum') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('cek_vakum')=='OK' ? 'selected' : '' }}>OK</option>
                                <option value="Tidak OK" {{ old('cek_vakum')=='Tidak OK' ? 'selected' : '' }}>Tidak OK
                                </option>
                            </select>
                            <small class="text-danger">@error('cek_vakum') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kebersihan Ujung Seal</label>
                            <select name="kebersihan_seal"
                                class="form-control @error('kebersihan_seal') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('kebersihan_seal')=='OK' ? 'selected' : '' }}>OK</option>
                                <option value="Tidak OK" {{ old('kebersihan_seal')=='Tidak OK' ? 'selected' : '' }}>
                                    Tidak OK</option>
                            </select>
                            <small class="text-danger">@error('kebersihan_seal') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kekuatan Seal</label>
                            <select name="kekuatan_seal"
                                class="form-control @error('kekuatan_seal') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('kekuatan_seal')=='OK' ? 'selected' : '' }}>OK</option>
                                <option value="Tidak OK" {{ old('kekuatan_seal')=='Tidak OK' ? 'selected' : '' }}>Tidak
                                    OK</option>
                            </select>
                            <small class="text-danger">@error('kekuatan_seal') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Diameter Klip (mm)</label>
                            <input type="number" step="0.01" name="diameter_klip"
                                class="form-control @error('diameter_klip') is-invalid @enderror"
                                value="{{ old('diameter_klip') }}">
                            <small class="text-danger">@error('diameter_klip') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Print Kode Production</label>
                            <select name="print_kode" class="form-control @error('print_kode') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="OK" {{ old('print_kode')=='OK' ? 'selected' : '' }}>OK</option>
                                <option value="Tidak OK" {{ old('print_kode')=='Tidak OK' ? 'selected' : '' }}>Tidak OK
                                </option>
                            </select>
                            <small class="text-danger">@error('print_kode') {{ $message }} @enderror</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lebar Cassing (mm)</label>
                            <input type="number" step="0.01" name="lebar_cassing"
                                class="form-control @error('lebar_cassing') is-invalid @enderror"
                                value="{{ old('lebar_cassing') }}">
                            <small class="text-danger">@error('lebar_cassing') {{ $message }} @enderror</small>
                        </div>
                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror"
                            rows="3">{{ old('catatan') }}</textarea>
                        <small class="text-danger">@error('catatan') {{ $message }} @enderror</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
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

    const produkSelect = document.querySelector('select[name="nama_produk"]');
    const batchSelect = document.getElementById('kode_produksi');
    const expDateInput = document.getElementById('exp_date');

    // Disable batch saat awal load (jika tidak ada old value)
    if (!produkSelect.value) {
        batchSelect.disabled = true;
    }

    produkSelect.addEventListener('change', function () {
    let namaProduk = this.value;

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
                batchSelect.innerHTML += `<option value="${batch.uuid}">${batch.kode_produksi}</option>`;
            });
        })
        .catch(err => {
            console.error('Fetch error:', err);
            batchSelect.innerHTML = '<option value="">Gagal memuat data batch</option>';
            batchSelect.disabled = true;
        });
    });


    // Exp date update ketika batch dipilih
    batchSelect.addEventListener('change', function () {
        let selectedText = this.options[this.selectedIndex]?.text;
        let kodeProduksi = selectedText?.split(" - ")[0]?.trim();

        if (!kodeProduksi) {
            expDateInput.value = '';
            return;
        }
        const bulanChar = kodeProduksi.charAt(1);
        const hari = parseInt(kodeProduksi.substr(2, 2));
        const bulanMap = {A:0,B:1,C:2,D:3,E:4,F:5,G:6,H:7,I:8,J:9,K:10,L:11};
        let kodeBulan = bulanMap[bulanChar];
        let now = new Date();
        let tahun = now.getFullYear();
        if (kodeBulan < now.getMonth()) tahun++;
        let expDate = new Date(tahun, kodeBulan, hari);
        expDate.setMonth(expDate.getMonth() + 7);
        expDateInput.value = expDate.toISOString().slice(0, 10);
    });
});
</script>



@endsection