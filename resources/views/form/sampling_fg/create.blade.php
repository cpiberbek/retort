@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Proses Sampling Finish Good
                </h4>

                <form id="samplingForm" action="{{ route('sampling_fg.store') }}" method="POST">
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
                                    <label class="form-label">Palet</label>
                                    <input type="text" name="palet" id="palet" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nama_produk" class="form-label fw-semibold">
                                        Nama Varian <span class="text-danger">*</span>
                                    </label>
                                    <select id="nama_produk" name="nama_produk" class="form-control" required>
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
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kode_batch" class="form-label fw-semibold">
                                        Kode Batch <span class="text-danger">*</span>
                                    </label>
                                    <select id="kode_batch" name="kode_produksi" class="form-control" disabled required>
                                        <option value="">Pilih Varian terlebih dahulu</option>
                                    </select>
                                    <small class="text-muted">
                                        Batch akan muncul otomatis
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Exp. Date</label>
                                    <input type="date" name="exp_date" id="exp_date" class="form-control">
                                    <small class="text-muted">Tanggal ini dihitung otomatis 7 bulan dari kode batch</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== PEMERIKSAAN PROSES CARTONING ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <strong>Pemeriksaan Proses Cartoning</strong>
                        </div>
                        <div class="card-body p-3">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Waktu</label>
                                    <input type="time" id="timeInput" name="pukul" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Kalibrasi</label>
                                    <select class="form-control" id="kalibrasi" name="kalibrasi">
                                        <option value="Sesuai">Sesuai</option>
                                        <option value="Tidak Sesuai">Tidak Sesuai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Berat Varian per Box (gr)</label>
                                    <input type="number" name="berat_produk" id="berat_produk" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" name="keterangan" id="keterangan" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Isi Varian per Box</label>
                                    <input type="number" name="isi_per_box" id="isi_per_box" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kemasan</label>
                                    <select name="kemasan" id="kemasan" class="form-control selectpicker">
                                        <option value="">-- Pilih Jenis Kemasan --</option>
                                        <option value="Jar">Toples</option>
                                        <option value="Pouch">Pouch</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah (Box)</label>
                                    <input type="number" name="jumlah_box" id="jumlah_box" class="form-control"
                                        readonly placeholder="Otomatis dari Release Packing">
                                </div>
                            </div>

                            <hr>

                            <label class="form-label"><b>Status Varian</b></label>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Release (Box)</label>
                                    <input type="number" name="release" id="release" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Reject (Box)</label>
                                    <input type="number" name="reject" id="reject" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hold (Box)</label>
                                    <input type="number" name="hold" id="hold" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <strong>Koordinator</strong>
                        </div>
                        <div class="card-body">
                            <div class="col-md-6">
                                <label class="form-label">Nama KR</label>
                                <select id="nama_koordinator" name="nama_koordinator" class="form-control selectpicker"
                                    data-live-search="true" required>
                                    <option value="">-- Pilih Koordinator --</option>
                                    @foreach ($koordinators as $koordinator)
                                        <option value="{{ $koordinator->nama_karyawan }}">{{ $koordinator->nama_karyawan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- ===================== CATATAN ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light"><strong>Item Mutu</strong></div>
                        <div class="card-body">
                            <textarea name="item_mutu" class="form-control" rows="3" placeholder="Tambahkan Item Mutu bila ada">{{ old('item_mutu', $data->item_mutu ?? '') }}</textarea>
                        </div>
                    </div>
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
                        <a href="{{ route('sampling_fg.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== SCRIPT ===================== --}}
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

        <script>
            $(document).ready(function() {
                $('.selectpicker').selectpicker();
            });

            // Set tanggal, waktu, dan shift otomatis
            document.addEventListener("DOMContentLoaded", function() {
                const dateInput = document.getElementById("dateInput");
                const shiftInput = document.getElementById("shiftInput");
                const timeInput = document.getElementById("timeInput");

                const now = new Date();
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const hh = String(now.getHours()).padStart(2, '0');
                const min = String(now.getMinutes()).padStart(2, '0');

                dateInput.value = `${yyyy}-${mm}-${dd}`;
                timeInput.value = `${hh}:${min}`;

                const hour = parseInt(hh);
                if (hour >= 7 && hour < 15) shiftInput.value = "1";
                else if (hour >= 15 && hour < 23) shiftInput.value = "2";
                else shiftInput.value = "3";
            });

            // Validasi kode produksi dan generate Exp Date otomatis
            const kodeInput = document.getElementById('kode_batch');
            const expDateInput = document.getElementById('exp_date');
            const kodeError = document.getElementById('kodeError');

            kodeInput.addEventListener('change', function() {
                let value = this.value.toUpperCase().replace(/\s+/g, '');
                this.value = value;
                kodeError.textContent = '';
                kodeError.classList.add('d-none');
                expDateInput.value = '';

                if (value.length !== 10) {
                    kodeError.textContent = "Kode batch harus terdiri dari 10 karakter.";
                    kodeError.classList.remove('d-none');
                    return;
                }

                const format = /^[A-Z0-9]+$/;
                if (!format.test(value)) {
                    kodeError.textContent = "Kode batch hanya boleh huruf besar dan angka.";
                    kodeError.classList.remove('d-none');
                    return;
                }

                const bulanChar = value.charAt(1);
                const validBulan = /^[A-L]$/;
                if (!validBulan.test(bulanChar)) {
                    kodeError.textContent = "Karakter ke-2 harus huruf bulan (A–L).";
                    kodeError.classList.remove('d-none');
                    return;
                }

                const hariStr = value.substr(2, 2);
                const hari = parseInt(hariStr, 10);
                if (isNaN(hari) || hari < 1 || hari > 31) {
                    kodeError.textContent = "Karakter ke-3 dan ke-4 harus tanggal valid (01–31).";
                    kodeError.classList.remove('d-none');
                    return;
                }

                const bulanMap = {
                    A: 0,
                    B: 1,
                    C: 2,
                    D: 3,
                    E: 4,
                    F: 5,
                    G: 6,
                    H: 7,
                    I: 8,
                    J: 9,
                    K: 10,
                    L: 11
                };
                const bulanIndex = bulanMap[bulanChar];
                const tahun = new Date().getFullYear();

                let expDate = new Date(tahun, bulanIndex, hari);
                expDate.setMonth(expDate.getMonth() + 7);

                const yyyy = expDate.getFullYear();
                const mm = String(expDate.getMonth() + 1).padStart(2, '0');
                const dd = String(expDate.getDate()).padStart(2, '0');
                expDateInput.value = `${yyyy}-${mm}-${dd}`;
            });

            // Kalibrasi checkbox: sesuai/tidak sesuai
            const kalibrasi = document.getElementById('kalibrasi');
            const kalibrasiLabel = document.getElementById('kalibrasiLabel');

            kalibrasi.addEventListener('change', function() {
                if (this.checked) {
                    kalibrasiLabel.textContent = 'Sesuai';
                    this.value = 'Sesuai';
                } else {
                    kalibrasiLabel.textContent = 'Tidak Sesuai';
                    this.value = 'Tidak Sesuai';
                }
            });

            // Default nilai awal kalibrasi
            kalibrasi.value = 'Tidak Sesuai';

            document.addEventListener('DOMContentLoaded', function() {

            const namaProdukSelect = document.getElementById('nama_produk');
            const kodeBatchSelect = document.getElementById('kode_batch');
            const jumlahBoxInput = document.getElementById('jumlah_box');

            function fetchJumlahBox() {

                const nama_produk = namaProdukSelect.value;
                const kode_produksi = kodeBatchSelect.value;

                if (nama_produk && kode_produksi) {

                    fetch(
                            `{{ route('get.jumlah.box') }}?nama_produk=${encodeURIComponent(nama_produk)}&kode_produksi=${encodeURIComponent(kode_produksi)}`)
                        .then(response => response.json())
                        .then(data => {

                            console.log(data);

                            jumlahBoxInput.value = data.total_box ?? 0;

                        })
                        .catch(error => {

                            console.error(error);
                            jumlahBoxInput.value = '';

                        });

                } else {

                    jumlahBoxInput.value = '';

                }
            }

            namaProdukSelect.addEventListener('change', fetchJumlahBox);
            kodeBatchSelect.addEventListener('change', fetchJumlahBox);

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
                            batchSelect.append(`
                                <option value="${item.kode_produksi}">
                                    ${item.kode_produksi}
                                </option>
                            `);
                        });
                    }
                });

            });
        </script>
    @endpush
@endsection
