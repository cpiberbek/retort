@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Data Sampling Produk
            </h4>

            <form id="samplingForm" action="{{ route('sampling.store') }}" method="POST">
                @csrf

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>
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
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Sampling</label>
                                <input type="text" name="jenis_sampel" id="jenis_sampel_input" class="form-control" value="Sampling Box" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kemasan</label>
                                <select name="jenis_kemasan" id="jenis_kemasan" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Kemasan --</option>
                                    <option value="Pouch">Pouch</option>
                                    <option value="Toples">Toples</option>
                                    <option value="Box">Box</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <input type="text" name="kode_produksi" id="kode_produksi" class="form-control" maxlength="10" required>
                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== ITEM SORTIR ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Item Sortir</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" required step="0.01" min="0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3"><label class="form-label">Jamur</label><input type="number" name="jamur" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-3"><label class="form-label">Lendir</label><input type="number" name="lendir" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-3"><label class="form-label">Klip Tajam</label><input type="number" name="klip_tajam" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-3"><label class="form-label">Pin Hole</label><input type="number" name="pin_hole" class="form-control" step="0.01" min="0"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><label class="form-label">Air Trap PVDC</label><input type="number" name="air_trap_pvdc" class="form-control" step="0.01" min="0"></div>
                            <div class="col-md-3" step="0.01"><label class="form-label">Air Trap Varian</label><input type="number" name="air_trap_produk" class="form-control" min="0"></div>
                            <div class="col-md-3" step="0.01"><label class="form-label">Keriput</label><input type="number" name="keriput" class="form-control" min="0"></div>
                            <div class="col-md-3" step="0.01"><label class="form-label">Bengkok</label><input type="number" name="bengkok" class="form-control" min="0"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3" step="0.01"><label class="form-label">Non Kode</label><input type="number" name="non_kode" class="form-control" min="0"></div>
                            <div class="col-md-3" step="0.01"><label class="form-label">Over Lap</label><input type="number" name="over_lap" class="form-control" min="0"></div>
                            <div class="col-md-3" step="0.01"><label class="form-label">Kecil</label><input type="number" name="kecil" class="form-control" min="0"></div>
                            <div class="col-md-3" step="0.01"><label class="form-label">Terjepit</label><input type="number" name="terjepit" class="form-control" min="0"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3" step="0.01"><label class="form-label">Double Klip</label><input type="number" name="double_klip" class="form-control" min="0"></div>
                            <div class="col-md-3" step="0.01"><label class="form-label">Seal Halus/Lepas</label><input type="number" name="seal_halus" class="form-control" min="0"></div>
                            <div class="col-md-3" step="0.01"><label class="form-label">Basah</label><input type="number" name="basah" class="form-control" min="0"></div>
                            <div class="col-md-3" step="0.01"><label class="form-label">Dll</label><input type="number" name="dll" class="form-control" min="0"></div>
                        </div>
                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
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
                    <a href="{{ route('sampling.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== SCRIPT ===================== --}}
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

        const kodeInput = document.getElementById('kode_produksi');
        const kodeError = document.getElementById('kodeError');
        const form = document.getElementById('samplingForm');

        // validasi langsung saat input
        kodeInput.addEventListener('input', function() {
            validateKode();
        });

        // blok kirim form jika kode tidak valid
        form.addEventListener('submit', function(e) {
            if (!validateKode()) {
                e.preventDefault();
                alert('Kode batch tidak valid! Periksa kembali sebelum menyimpan.');
                kodeInput.focus();
            }
        });

        function validateKode() {
            let value = kodeInput.value.toUpperCase().replace(/\s+/g, '');
            kodeInput.value = value;
            kodeError.textContent = '';
            kodeError.classList.add('d-none');

            if (value.length !== 10) {
                kodeError.textContent = "Kode batch harus terdiri dari 10 karakter.";
                kodeError.classList.remove('d-none');
                return false;
            }

            const format = /^[A-Z0-9]+$/;
            if (!format.test(value)) {
                kodeError.textContent = "Kode batch hanya boleh huruf besar dan angka.";
                kodeError.classList.remove('d-none');
                return false;
            }

            const bulanChar = value.charAt(1);
            const validBulan = /^[A-L]$/;
            if (!validBulan.test(bulanChar)) {
                kodeError.textContent = "Karakter ke-2 harus huruf bulan (A–L).";
                kodeError.classList.remove('d-none');
                return false;
            }

            const hariStr = value.substr(2, 2);
            const hari = parseInt(hariStr, 10);
            if (isNaN(hari) || hari < 1 || hari > 31) {
                kodeError.textContent = "Karakter ke-3 dan ke-4 harus tanggal valid (01–31).";
                kodeError.classList.remove('d-none');
                return false;
            }

            return true; // valid
        }

        // otomatis isi tanggal & shift
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
@endsection
