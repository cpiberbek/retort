@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-plus-circle"></i> Form Input Pengecekan Pre Packing
                </h4>

                <form id="prepackingForm" action="{{ route('prepacking.store') }}" method="POST">
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
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama_produk" class="form-label fw-semibold">
                                        Nama Varian <span class="text-danger">*</span>
                                    </label>
                                    <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
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
                                <div class="col-md-6">
                                    <label for="kode_batch" class="form-label fw-semibold">
                                        Kode Batch <span class="text-danger">*</span>
                                    </label>
                                    <select name="kode_produksi" id="kode_batch" class="form-control" required>
                                        <option value="">Pilih Varian terlebih dahulu</option>
                                    </select>
                                    <small class="text-muted">
                                        Batch akan muncul otomatis
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PENGECEKAN SUHU --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white"><strong>Pengecekan</strong></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <tbody>
                                        <tr>
                                            <td class="text-start">No. Conveyor</td>
                                            <td>
                                                <input type="text" name="conveyor" id="conveyor"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td rowspan="3" class="text-center align-middle">Suhu Varian (°C)</td>
                                            <td>
                                                <input type="number" name="suhu_produk[suhu_1]" id="suhu_1"
                                                    class="form-control form-control-sm text-center" step="0.01"
                                                    min="0">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="number" name="suhu_produk[suhu_2]" id="suhu_2"
                                                    class="form-control form-control-sm text-center" step="0.01"
                                                    min="0">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="number" name="suhu_produk[suhu_3]" id="suhu_3"
                                                    class="form-control form-control-sm text-center" step="0.01"
                                                    min="0">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- KONDISI PRODUK --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white"><strong>Kondisi Varian</strong></div>
                        <div class="card-body">
                            {{-- Air --}}
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2">Bagian</th>
                                            <th colspan="2">Air (%)</th>
                                        </tr>
                                        <tr>
                                            <th>Basah</th>
                                            <th>Kering</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Ujung</td>
                                            <td><input type="number" name="kondisi_produk[basah_air_ujung]"
                                                    id="basah_air_ujung" class="form-control form-control-sm text-center"
                                                    min="0"></td>
                                            <td><input type="number" name="kondisi_produk[kering_air_ujung]"
                                                    id="kering_air_ujung" class="form-control form-control-sm text-center"
                                                    min="0" readonly></td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Seal</td>
                                            <td><input type="number" name="kondisi_produk[basah_air_seal]"
                                                    id="basah_air_seal" class="form-control form-control-sm text-center"
                                                    min="0"></td>
                                            <td><input type="number" name="kondisi_produk[kering_air_seal]"
                                                    id="kering_air_seal" class="form-control form-control-sm text-center"
                                                    min="0" readonly></td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Total</td>
                                            <td><input type="number" name="kondisi_produk[basah_air_total]"
                                                    id="basah_air_total" class="form-control form-control-sm text-center"
                                                    readonly></td>
                                            <td><input type="number" name="kondisi_produk[kering_air_total]"
                                                    id="kering_air_total" class="form-control form-control-sm text-center"
                                                    readonly></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Minyak --}}
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2">Bagian</th>
                                            <th colspan="2">Minyak (%)</th>
                                        </tr>
                                        <tr>
                                            <th>Basah</th>
                                            <th>Kering</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Ujung</td>
                                            <td><input type="number" name="kondisi_produk[basah_minyak_ujung]"
                                                    id="basah_minyak_ujung"
                                                    class="form-control form-control-sm text-center" min="0"></td>
                                            <td><input type="number" name="kondisi_produk[kering_minyak_ujung]"
                                                    id="kering_minyak_ujung"
                                                    class="form-control form-control-sm text-center" min="0"
                                                    readonly></td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Seal</td>
                                            <td><input type="number" name="kondisi_produk[basah_minyak_seal]"
                                                    id="basah_minyak_seal"
                                                    class="form-control form-control-sm text-center" min="0"></td>
                                            <td><input type="number" name="kondisi_produk[kering_minyak_seal]"
                                                    id="kering_minyak_seal"
                                                    class="form-control form-control-sm text-center" min="0"
                                                    readonly></td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Total</td>
                                            <td><input type="number" name="kondisi_produk[basah_minyak_total]"
                                                    id="basah_minyak_total"
                                                    class="form-control form-control-sm text-center" readonly></td>
                                            <td><input type="number" name="kondisi_produk[kering_minyak_total]"
                                                    id="kering_minyak_total"
                                                    class="form-control form-control-sm text-center" readonly></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- BERAT PRODUK --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white"><strong>Berat Varian per</strong></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead>
                                        <tr>
                                            <th>Pcs</th>
                                            <th>Toples (berat kotor)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 1; $i <= 3; $i++)
                                            <tr>
                                                <td><input type="number" name="berat_produk[pcs_{{ $i }}]"
                                                        id="pcs_{{ $i }}"
                                                        class="form-control form-control-sm text-center" step="0.01"
                                                        min="0"></td>
                                                <td><input type="number" name="berat_produk[toples_{{ $i }}]"
                                                        id="toples_{{ $i }}"
                                                        class="form-control form-control-sm text-center" step="0.01"
                                                        min="0"></td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- CATATAN --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light"><strong>Catatan</strong></div>
                        <div class="card-body">
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- TOMBOL --}}
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
                        <a href="{{ route('prepacking.index') }}" class="btn btn-secondary"><i
                                class="bi bi-arrow-left"></i> Kembali</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    {{-- Select2 CSS & JS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            if (typeof $.fn.selectpicker === 'function') {
                $('.selectpicker').selectpicker();
            }

            // =========================
            // DEFAULT TANGGAL
            // =========================
            const dateInput = $('#dateInput');
            let now = new Date();
            dateInput.val(now.toISOString().split('T')[0]);

            // =========================
            // VALIDASI KODE PRODUKSI
            // =========================
            const kodeInput = $('#kode_produksi');
            const kodeError = $('#kodeError');
            const form = $('#prepackingForm');

            function validateKode() {
                let value = kodeInput.val().toUpperCase().replace(/\s+/g, '');
                kodeInput.val(value);
                kodeError.text('').addClass('d-none');

                if (value.length !== 10) {
                    kodeError.text('Kode batch harus terdiri dari 10 karakter').removeClass('d-none');
                    return false;
                }

                if (!/^[A-Z0-9]+$/.test(value)) {
                    kodeError.text('Kode batch hanya boleh huruf besar dan angka').removeClass('d-none');
                    return false;
                }

                if (!/^[A-L]$/.test(value.charAt(1))) {
                    kodeError.text('Karakter ke-2 harus huruf bulan (A-L)').removeClass('d-none');
                    return false;
                }

                let hari = parseInt(value.substr(2, 2), 10);
                if (isNaN(hari) || hari < 1 || hari > 31) {
                    kodeError.text('Karakter ke-3 dan ke-4 harus tanggal valid (01-31)').removeClass('d-none');
                    return false;
                }

                return true;
            }

            kodeInput.on('input', validateKode);

            form.on('submit', function(e) {
                if (!validateKode()) {
                    e.preventDefault();
                    alert('Kode batch tidak valid! Periksa kembali sebelum menyimpan.');
                    kodeInput.focus();
                }
            });

            // =====================================
            // FUNGSI HITUNG TOTAL BASAH & KERING
            // =====================================
            function hitungTotalAirMinyak(type) {
                let inputUjung = $(`#basah_${type}_ujung`).val();
                let inputSeal  = $(`#basah_${type}_seal`).val();

                let basahUjung = parseFloat(inputUjung) || 0;
                let basahSeal  = parseFloat(inputSeal) || 0;

                let totalBasah = basahUjung + basahSeal;
                let totalKering = 100 - totalBasah;
                if (totalKering < 0) totalKering = 0;

                if (inputUjung === '') {
                    $(`#kering_${type}_ujung`).val('');
                } else {
                    $(`#kering_${type}_ujung`).val(Math.max(0, 100 - basahUjung).toFixed(2));
                }

                if (inputSeal === '') {
                    $(`#kering_${type}_seal`).val('');
                } else {
                    $(`#kering_${type}_seal`).val(Math.max(0, 100 - basahSeal).toFixed(2));
                }

                if (inputUjung === '' && inputSeal === '') {
                    $(`#basah_${type}_total`).val('');
                    $(`#kering_${type}_total`).val('');
                } else {
                    $(`#basah_${type}_total`).val(totalBasah.toFixed(2));
                    $(`#kering_${type}_total`).val(totalKering.toFixed(2));
                }

                if (totalBasah > 100) {
                    alert('Total basah tidak boleh lebih dari 100%');
                }
            }

            ['air', 'minyak'].forEach(type => {
                $(`#basah_${type}_ujung, #basah_${type}_seal`).on('input', function() {
                    hitungTotalAirMinyak(type);
                });
                hitungTotalAirMinyak(type);
            });

            // === Select2 Batch Integration ===
            const produkSelect = $('#nama_produk');
            const batchSelect = $('#kode_batch');

            function initBatchSelect(produkValue) {
                if (batchSelect.data('select2')) {
                    batchSelect.select2('destroy');
                }
                
                if (!produkValue) {
                    batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
                    batchSelect.prop("disabled", true);
                    return;
                }
                
                batchSelect.html('<option value="">-- Pilih Batch --</option>');
                batchSelect.prop("disabled", false);
                
                batchSelect.select2({
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

            if (produkSelect.val()) {
                initBatchSelect(produkSelect.val());
            } else {
                batchSelect.prop("disabled", true);
            }

            produkSelect.on('change', function () {
                let namaProduk = $(this).val();
                
                if (!namaProduk) {
                    batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
                    batchSelect.prop("disabled", true);
                    return;
                }

                initBatchSelect(namaProduk);
            });

        });
    </script>
@endpush
