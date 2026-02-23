@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="card shadow-lg border-0">
        <div class="card-body">
            {{-- ===================== JUDUL ===================== --}}
            <h4 class="mb-4 fw-bold text-primary">
                <i class="bi bi-clipboard-check-fill me-2"></i>
                Form Input Pemeriksaan Mincing - Emulsifying - Aging
            </h4>

            <form id="mincingForm" action="{{ route('mincing.store') }}" method="POST">
                @csrf

                {{-- ===================== IDENTIFIKASI ===================== --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        IDENTIFIKASI
                    </div>
                    <div class="card-body bg-light">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Shift</label>
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
                                <label class="form-label fw-semibold">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true"
                                    required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kode Batch</label>
                                <input type="text" name="kode_produksi" id="kode_produksi" class="form-control"
                                    maxlength="10" required>
                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== PEMERIKSAAN ===================== --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-info text-white fw-bold">
                        PEMERIKSAAN
                    </div>

                    <div class="card-body bg-light">
                        {{-- Preparation --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-left">Preparation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start fw-semibold">Waktu</td>
                                        <td><input type="time" name="waktu_mulai"
                                                class="form-control form-control-sm text-center"></td>
                                        <td class="fw-bold">s/d</td>
                                        <td><input type="time" name="waktu_selesai"
                                                class="form-control form-control-sm text-center"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- NON PREMIX --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered text-center align-middle" id="tabelNonPremix">
                                <thead class="table-primary">
                                    <tr>
                                        <th colspan="7" class="text-left">Bahan Baku dan Bahan Tambahan (Non-Premix)
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Bahan</th>
                                        <th>Kode</th>
                                        <th>(°C)</th>
                                        <th>*pH</th>
                                        <th>Kg</th>
                                        <th>Sens</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyNonPremix">
                                    <tr>
                                        <td>
                                            <select name="non_premix[0][nama_bahan]" class="form-control form-select-sm text-center" required>
                                                <option value="" selected disabled>-- Pilih Bahan --</option>
                                                @foreach($rawMaterials as $rm)
                                                    <option value="{{ $rm->nama_bahan_baku }}">{{ $rm->nama_bahan_baku }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="non_premix[0][kode_bahan]"
                                                class="form-control form-control-sm text-center"></td>
                                        <td><input type="number" name="non_premix[0][suhu_bahan]" step="0.01"
                                                class="form-control form-control-sm text-center"></td>
                                        <td><input type="number" name="non_premix[0][ph_bahan]" step="0.01"
                                                class="form-control form-control-sm text-center"></td>
                                        <td><input type="number" name="non_premix[0][berat_bahan]" step="0.01"
                                                class="form-control form-control-sm text-center"></td>
                                        <td><input type="checkbox" name="non_premix[0][sensori]" value="Oke"
                                                class="form-check-input"></td>
                                        <td><button type="button" class="btn btn-sm btn-danger hapusBaris"><i
                                                    class="bi bi-trash"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success btn-sm" id="tambahBarisNonPremix">
                                <i class="bi bi-plus-circle"></i> Tambah Bahan
                            </button>
                        </div>

                        {{-- PREMIX --}}
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered text-center align-middle" id="tabelPremix">
                                <thead class="table-primary">
                                    <tr>
                                        <th colspan="5" class="text-left">Premix</th>
                                    </tr>
                                    <tr>
                                        <th>Premix</th>
                                        <th>Kode</th>
                                        <th>Kg</th>
                                        <th>Sens</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyPremix">
                                    <tr>
                                        <td><input type="text" name="premix[0][nama_premix]"
                                                class="form-control form-control-sm text-center"></td>
                                        <td><input type="text" name="premix[0][kode_premix]"
                                                class="form-control form-control-sm text-center"></td>
                                        <td><input type="number" name="premix[0][berat_premix]" step="0.01"
                                                class="form-control form-control-sm text-center"></td>
                                        <td><input type="checkbox" name="premix[0][sensori_premix]" value="Oke"
                                                class="form-check-input"></td>
                                        <td><button type="button" class="btn btn-sm btn-danger hapusBarisPremix"><i
                                                    class="bi bi-trash"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success btn-sm" id="tambahBarisPremix">
                                <i class="bi bi-plus-circle"></i> Tambah Premix
                            </button>
                        </div>

                        {{-- PROSES MIXING & EMULSI --}}
                        <div class="table-responsive">
                            {{-- Suhu Sebelum Grinding --}}
                            <table class="table table-bordered align-middle mb-0">
                                <tbody>
                                    {{-- BARIS SUHU SEBELUM GRINDING --}}
                                    <tr>
                                        <td class="text-start fw-semibold bg-light" style="width: 25%;">Suhu (Sebelum Grinding)</td>
                                        <td colspan="3" class="p-0">
                                            {{-- Tabel anak untuk input dinamis agar tidak merusak lebar kolom utama --}}
                                            <table class="table table-borderless mb-0">
                                                <tbody id="tbodySuhuGrinding">
                                                    <tr>
                                                        <td style="width: 45%;">
                                                            <select name="suhu_grinding_input[0][daging]" class="form-control form-select-sm">
                                                                <option value="" selected disabled>Pilih Daging</option>
                                                                <option value="BEEF">BEEF</option>
                                                                <option value="SBB">SBB</option>
                                                                <option value="SBL">SBL</option>
                                                                <option value="MDM">MDM</option>
                                                                <option value="CCM">CCM</option>
                                                            </select>
                                                        </td>
                                                        <td style="width: 45%;">
                                                            <input type="number" name="suhu_grinding_input[0][suhu]" step="0.01" class="form-control form-control-sm text-center" placeholder="0.00">
                                                        </td>
                                                        <td style="width: 10%;">
                                                            <button type="button" class="btn btn-sm btn-danger hapusBarisSuhu">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            {{-- Tombol tambah diletakkan di bawah baris input --}}
                                            <div class="p-2 border-top bg-white">
                                                <button type="button" class="btn btn-success btn-sm" id="tambahBarisSuhu">
                                                    <i class="bi bi-plus-circle"></i> Tambah Daging
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- BARIS WAKTU MIXING PREMIX (PERSIS SEPERTI GAMBAR) --}}
                                    <tr>
                                        <td class="text-start fw-semibold bg-light">Waktu Mixing Premix (Menit)</td>
                                        <td style="width: 32%;">
                                            <input type="time" name="waktu_mixing_premix_awal" class="form-control form-control-sm text-center">
                                        </td>
                                        <td class="fw-bold text-center" style="width: 6%;">s/d</td>
                                        <td style="width: 37%;">
                                            <input type="time" name="waktu_mixing_premix_akhir" class="form-control form-control-sm text-center">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>


                            {{-- GEL --}}
                            <table class="table table-bordered text-center align-middle mb-4">
                                <thead class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-left">GEL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start fw-semibold">Waktu Bowl Cutter (Menit)</td>
                                        <td><input type="time" name="waktu_bowl_cutter_awal"
                                                class="form-control form-control-sm text-center"></td>
                                        <td class="fw-bold">s/d</td>
                                        <td><input type="time" name="waktu_bowl_cutter_akhir"
                                                class="form-control form-control-sm text-center"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fw-semibold">Waktu Aging Emulsi (Menit)</td>
                                        <td><input type="time" name="waktu_aging_emulsi_awal"
                                                class="form-control form-control-sm text-center"></td>
                                        <td class="fw-bold">s/d</td>
                                        <td><input type="time" name="waktu_aging_emulsi_akhir"
                                                class="form-control form-control-sm text-center"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fw-semibold">Suhu Akhir Emulsi Gel (Std &lt;5°C)</td>
                                        <td colspan="3"><input type="number" name="suhu_akhir_emulsi_gel" step="0.01"
                                                class="form-control form-control-sm text-center"></td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- Waktu Mixing & Emulsifying --}}
                            <table class="table table-bordered text-center align-middle">
                                <tbody>
                                    <tr>
                                        <td class="text-start fw-semibold">Waktu Mixing (Menit)</td>
                                        <td><input type="time" name="waktu_mixing"
                                                class="form-control form-control-sm text-center"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fw-semibold">Suhu Akhir Mixing (Std 2–5°C)</td>
                                        <td><input type="number" name="suhu_akhir_mixing" step="0.01"
                                                class="form-control form-control-sm text-center"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fw-semibold">Suhu Akhir Emulsifying (Std 14±2°C)</td>
                                        <td><input type="number" name="suhu_akhir_emulsi" step="0.01"
                                                class="form-control form-control-sm text-center"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white fw-bold">Catatan</div>
                    <div class="card-body bg-light">
                        <textarea name="catatan" class="form-control" rows="3"
                            placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('mincing.index') }}" class="btn btn-secondary px-4">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>

            <hr>
            <div id="resultArea"></div>

        </div>
    </div>
</div>

{{-- ===================== SCRIPT ===================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
    });
</script>

{{-- Otomatis Isi Tanggal & Shift --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");
        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = now.getHours();
        dateInput.value = `${yyyy}-${mm}-${dd}`;
        if (hh >= 7 && hh < 15) shiftInput.value = "1";
        else if (hh >= 15 && hh < 23) shiftInput.value = "2";
        else shiftInput.value = "3";
    });
</script>

{{-- Validasi Kode Produksi --}}
<script>
    $(function() {
        const kodeInput = $('#kode_produksi');
        const kodeError = $('#kodeError');
        const form = $('#mincingForm');

        function validateKode() {
            let value = kodeInput.val().toUpperCase().replace(/\s+/g, '');
            kodeInput.val(value);
            kodeError.text('').addClass('d-none');

            if (value.length !== 10) {
                kodeError.text('Kode Batch harus 10 karakter').removeClass('d-none');
                return false;
            }
            if (!/^[A-Z0-9]+$/.test(value)) {
                kodeError.text('Hanya huruf besar & angka').removeClass('d-none');
                return false;
            }
            if (!/^[A-L]$/.test(value.charAt(1))) {
                kodeError.text('Karakter ke-2 harus huruf bulan (A-L)').removeClass('d-none');
                return false;
            }
            let hari = parseInt(value.substr(2, 2), 10);
            if (isNaN(hari) || hari < 1 || hari > 31) {
                kodeError.text('Karakter ke-3 & ke-4 harus tanggal valid (01-31)').removeClass('d-none');
                return false;
            }
            return true;
        }

        kodeInput.on('input', validateKode);
        form.on('submit', function(e) {
            if (!validateKode()) {
                e.preventDefault();
                alert('Kode Batch tidak valid! Periksa kembali.');
                kodeInput.focus();
            }
        });
    });
</script>

{{-- Tambah & Hapus Baris Non-Premix / Premix --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let indexNonPremix = 1;
        let indexPremix = 1;
        const tbodyNon = document.getElementById('tbodyNonPremix');
        const tbodyPremix = document.getElementById('tbodyPremix');

        document.getElementById('tambahBarisNonPremix').addEventListener('click', () => {
            const row = `
        <tr>
            <td><input type="text" name="non_premix[${indexNonPremix}][nama_bahan]" class="form-control form-control-sm text-center"></td>
            <td><input type="text" name="non_premix[${indexNonPremix}][kode_bahan]" class="form-control form-control-sm text-center"></td>
            <td><input type="number" name="non_premix[${indexNonPremix}][suhu_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
            <td><input type="number" name="non_premix[${indexNonPremix}][ph_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
            <td><input type="number" name="non_premix[${indexNonPremix}][berat_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
            <td><input type="checkbox" name="non_premix[${indexNonPremix}][sensori]" value="Oke" class="form-check-input"></td>
            <td><button type="button" class="btn btn-danger btn-sm hapusBaris"><i class="bi bi-trash"></i></button></td>
            </tr>`;
            tbodyNon.insertAdjacentHTML('beforeend', row);
            indexNonPremix++;
        });

        tbodyNon.addEventListener('click', e => {
            if (e.target.closest('.hapusBaris')) e.target.closest('tr').remove();
        });

        document.getElementById('tambahBarisPremix').addEventListener('click', () => {
            const row = `
        <tr>
            <td><input type="text" name="premix[${indexPremix}][nama_premix]" class="form-control form-control-sm text-center"></td>
            <td><input type="text" name="premix[${indexPremix}][kode_premix]" class="form-control form-control-sm text-center"></td>
            <td><input type="number" name="premix[${indexPremix}][berat_premix]" step="0.01" class="form-control form-control-sm text-center"></td>
            <td><input type="checkbox" name="premix[${indexPremix}][sensori_premix]" value="Oke" class="form-check-input"></td>
            <td><button type="button" class="btn btn-danger btn-sm hapusBarisPremix"><i class="bi bi-trash"></i></button></td>
            </tr>`;
            tbodyPremix.insertAdjacentHTML('beforeend', row);
            indexPremix++;
        });

        tbodyPremix.addEventListener('click', e => {
            if (e.target.closest('.hapusBarisPremix')) e.target.closest('tr').remove();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Definisi Element ---
        const tbodyNon = document.getElementById('tbodyNonPremix');
        const tbodyPremix = document.getElementById('tbodyPremix');
        const tbodySuhu = document.getElementById('tbodySuhuGrinding');

        // --- 2. Inisialisasi Index ---
        let indexNonPremix = tbodyNon ? tbodyNon.querySelectorAll('tr').length : 0;
        let indexPremix = tbodyPremix ? tbodyPremix.querySelectorAll('tr').length : 0;
        let indexSuhu = tbodySuhu ? tbodySuhu.querySelectorAll('tr').length : 0;

        // --- 3. Logic Non Premix ---
        const btnTambahNon = document.getElementById('tambahBarisNonPremix');
        btnTambahNon.addEventListener('click', () => {
                // Render data raw material dari PHP ke variabel JavaScript
                let optionBahan = `<option value="" selected disabled>-- Pilih Bahan --</option>`;
                @foreach($rawMaterials as $rm)
                    optionBahan += `<option value="{{ $rm->nama_bahan_baku }}">{{ $rm->nama_bahan_baku }}</option>`;
                @endforeach

                const row = `<tr>
                    <td>
                        <select name="non_premix[${indexNonPremix}][nama_bahan]" class="form-control form-select-sm text-center" required>
                            ${optionBahan}
                        </select>
                    </td>
                    <td><input type="text" name="non_premix[${indexNonPremix}][kode_bahan]" class="form-control form-control-sm text-center"></td>
                    <td><input type="number" name="non_premix[${indexNonPremix}][suhu_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
                    <td><input type="number" name="non_premix[${indexNonPremix}][ph_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
                    <td><input type="number" name="non_premix[${indexNonPremix}][berat_bahan]" step="0.01" class="form-control form-control-sm text-center"></td>
                    <td><input type="checkbox" name="non_premix[${indexNonPremix}][sensori]" value="Oke" class="form-check-input"></td>
                    <td><button type="button" class="btn btn-danger btn-sm hapusBaris"><i class="bi bi-trash"></i></button></td>
                </tr>`;
                tbodyNon.insertAdjacentHTML('beforeend', row);
                indexNonPremix++;
            });
        }

        // --- 4. Logic Premix ---
        const btnTambahPremix = document.getElementById('tambahBarisPremix');
        if(btnTambahPremix) {
            btnTambahPremix.addEventListener('click', () => {
                const row = `<tr>
                    <td><input type="text" name="premix[${indexPremix}][nama_premix]" class="form-control form-control-sm text-center"></td>
                    <td><input type="text" name="premix[${indexPremix}][kode_premix]" class="form-control form-control-sm text-center"></td>
                    <td><input type="number" name="premix[${indexPremix}][berat_premix]" step="0.01" class="form-control form-control-sm text-center"></td>
                    <td><input type="checkbox" name="premix[${indexPremix}][sensori_premix]" value="Oke" class="form-check-input"></td>
                    <td><button type="button" class="btn btn-danger btn-sm hapusBarisPremix"><i class="bi bi-trash"></i></button></td>
                </tr>`;
                tbodyPremix.insertAdjacentHTML('beforeend', row);
                indexPremix++;
            });
        }

        // --- 5. Logic Suhu Grinding ---
        const btnTambahSuhu = document.getElementById('tambahBarisSuhu');
        if (btnTambahSuhu) {
            btnTambahSuhu.addEventListener('click', function() {
                const row = `<tr>
                    <td style="width: 45%;">
                        <select name="suhu_grinding_input[${indexSuhu}][daging]" class="form-control form-select-sm">
                            <option value="" selected disabled>Pilih Daging</option>
                            <option value="BEEF">BEEF</option>
                            <option value="SBB">SBB</option>
                            <option value="SBL">SBL</option>
                            <option value="MDM">MDM</option>
                            <option value="CCM">CCM</option>
                        </select>
                    </td>
                    <td style="width: 45%;">
                        <input type="number" name="suhu_grinding_input[${indexSuhu}][suhu]" step="0.01" class="form-control form-control-sm text-center" placeholder="0.00">
                    </td>
                    <td style="width: 10%;">
                        <button type="button" class="btn btn-sm btn-danger hapusBarisSuhu"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
                tbodySuhu.insertAdjacentHTML('beforeend', row);
                indexSuhu++;
            });
        }

        // --- 6. Event Delegation untuk Hapus ---
        document.addEventListener('click', function(e) {
            if (e.target.closest('.hapusBaris')) e.target.closest('tr').remove();
            if (e.target.closest('.hapusBarisPremix')) e.target.closest('tr').remove();
            if (e.target.closest('.hapusBarisSuhu')) {
                if (tbodySuhu.querySelectorAll('tr').length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert("Minimal harus ada satu input suhu.");
                }
            }
        });
    });
</script>
@endsection