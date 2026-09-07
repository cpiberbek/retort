@extends('layouts.app')

@section('content')
    <style>
        html,
        body {
            min-width: 1200px;
            overflow-x: auto;
        }

        .mincing-page {
            width: 1200px;
            min-width: 1200px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .mincing-page .card {
            width: 100%;
            min-width: 0;
        }

        .mincing-page .table-responsive {
            overflow: visible;
        }

        .mincing-page table {
            width: 100%;
            min-width: 0;
        }

        .mincing-page input,
        .mincing-page textarea,
        .mincing-page .form-control,
        .mincing-page .form-select {
            max-width: none;
        }

        .mincing-page .select2-container {
            width: 100% !important;
        }
    </style>
    <div class="container-fluid py-4 mincing-page">

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
                                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="dateInput" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Shift <span class="text-danger">*</span></label>
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
                                    <label class="form-label fw-semibold">Nama Varian <span
                                            class="text-danger">*</span></label>
                                    <select name="nama_produk" id="namaProdukSelect" class="form-control select2" required>
                                        <option value="">-- Pilih Produk --</option>

                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}"
                                                data-bahan-baku='@json($produk->bahan_baku ?? [])'>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kode Batch <span
                                            class="text-danger">*</span></label>
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
                                            <td class="text-start fw-semibold">
                                                Waktu Mulai
                                            </td>
                                            <td>
                                                <input type="time" name="waktu_mulai" id="waktu_mulai"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                            <td class="fw-bold">s/d</td>
                                            <td>
                                                <input type="time" name="waktu_selesai" id="waktu_selesai"
                                                    class="form-control form-control-sm text-center">
                                            </td>
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
                                            <th style="width: 30%;">Bahan</th>
                                            <th style="width: 30%;">Kode</th>
                                            <th style="width: 10%;">(°C)</th>
                                            <th style="width: 10%;">*pH</th>
                                            <th style="width: 10%;">Kg</th>
                                            <th style="width: 5%;">Sens</th>
                                            <th style="width: 5%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyNonPremix">
                                        <tr>
                                            <td>
                                                <select name="non_premix[0][nama_bahan]"
                                                    class="form-control form-select-sm text-center nama-bahan-select select2">

                                                    <option value="" selected disabled>
                                                        -- Pilih Bahan --
                                                    </option>

                                                    @foreach ($rawMaterials as $rm)
                                                        <option value="{{ $rm->nama_bahan_baku }}">
                                                            {{ $rm->nama_bahan_baku }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </td>

                                            <td>
                                                <select name="non_premix[0][inspection_uuid][]"
                                                    class="form-control form-select-sm text-center kode-batch-select select2"
                                                    multiple disabled>

                                                    <option value="" disabled selected>Pilih Bahan dahulu</option>

                                                    @foreach ($inspections as $insp)
                                                        @if ($insp->inspection)
                                                            <option value="{{ $insp->uuid }}"
                                                                data-bahan="{{ $insp->inspection->bahan_baku }}">
                                                                {{ $insp->kode_batch }}
                                                            </option>
                                                        @endif
                                                    @endforeach

                                                </select>
                                            </td>

                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-toggle-minus"
                                                        tabindex="-1">±</button>
                                                    <input type="text" inputmode="decimal"
                                                        name="non_premix[0][suhu_bahan]"
                                                        class="form-control form-control-sm text-center suhu-number-input">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-toggle-minus"
                                                        tabindex="-1">±</button>
                                                    <input type="text" inputmode="decimal"
                                                        name="non_premix[0][ph_bahan]"
                                                        class="form-control form-control-sm text-center suhu-number-input">
                                                </div>
                                            </td>
                                            <td><input type="number" name="non_premix[0][berat_bahan]" step="0.01"
                                                    class="form-control form-control-sm text-center"></td>
                                            <td><input type="checkbox" name="non_premix[0][sensori]" value="Oke"
                                                    class="form-check-input"></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger hapusBaris"
                                                    title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
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
                                            <td>

                                                <select name="premix[0][nama_premix]"
                                                    class="form-control form-select-sm text-center select2">

                                                    <option value="">-- Pilih Premix --</option>

                                                    @foreach ($premixes as $premix)
                                                        <option value="{{ $premix->nama_premix }}">

                                                            {{ $premix->nama_premix }}

                                                        </option>
                                                    @endforeach

                                                </select>

                                            </td>
                                            <td>
                                                <select name="premix[0][kode_premix]"
                                                    class="form-control form-select-sm text-center select2">
                                                    <option value="">-- Pilih Kode Batch --</option>
                                                    @foreach ($inspections as $insp)
                                                        @if ($insp->inspection)
                                                            <option value="{{ $insp->kode_batch }}">
                                                                {{ $insp->kode_batch }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="premix[0][berat_premix]" step="0.0001"
                                                    min="0" class="form-control form-control-sm text-center"></td>
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
                                            <td class="text-start fw-semibold bg-light" style="width: 25%;">Suhu (Sebelum
                                                Grinding)</td>
                                            <td colspan="3" class="p-0">
                                                {{-- Tabel anak untuk input dinamis agar tidak merusak lebar kolom utama
                                            --}}
                                                <table class="table table-borderless mb-0">
                                                    <tbody id="tbodySuhuGrinding">
                                                        <tr>
                                                            <td style="width: 45%;">
                                                                <select name="suhu_grinding_input[0][daging]"
                                                                    class="form-control form-select-sm select2">
                                                                    <option value="" selected disabled>Pilih Daging
                                                                    </option>
                                                                    <option value="BEEF">BEEF</option>
                                                                    <option value="SBB">SBB</option>
                                                                    <option value="SBL">SBL</option>
                                                                    <option value="MDM">MDM</option>
                                                                    <option value="CCM">CCM</option>
                                                                    <option value="SURIMI">SURIMI</option>
                                                                </select>
                                                            </td>
                                                            <td style="width: 45%;">
                                                                <div class="input-group input-group-sm">
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary btn-toggle-minus"
                                                                        tabindex="-1">±</button>
                                                                    <input type="text" inputmode="decimal"
                                                                        name="suhu_grinding_input[0][suhu]" step="0.01"
                                                                        class="form-control form-control-sm text-center suhu-number-input">
                                                                </div>
                                                            </td>
                                                            <td style="width: 10%;">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger hapusBarisSuhu">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                {{-- Tombol tambah diletakkan di bawah baris input --}}
                                                <div class="p-2 border-top bg-white">
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        id="tambahBarisSuhu">
                                                        <i class="bi bi-plus-circle"></i> Tambah Daging
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- BARIS WAKTU MIXING PREMIX --}}
                                        <tr>
                                            <td class="text-start fw-semibold bg-light">Waktu Mixing Premix</td>
                                            <td colspan="3">
                                                <div class="d-flex align-items-center gap-2">

                                                    <input type="time" id="premix_start"
                                                        class="form-control form-control-sm">
                                                    <span>-</span>
                                                    <input type="time" id="premix_end"
                                                        class="form-control form-control-sm">

                                                    <span id="premix_result" class="badge bg-light text-dark">(0)
                                                        Menit</span>

                                                </div>

                                                <!-- hidden -->
                                                <input type="hidden" name="waktu_mixing_premix" id="premix_menit">
                                                <input type="hidden" name="waktu_mixing_premix_start"
                                                    id="premix_start_hidden">
                                                <input type="hidden" name="waktu_mixing_premix_end"
                                                    id="premix_end_hidden">
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
                                            <td class="text-start fw-semibold" style="width: 25%;">Waktu Bowl Cutter</td>
                                            <td colspan="3">

                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="time" id="bowl_start"
                                                        class="form-control form-control-sm">
                                                    <span>-</span>
                                                    <input type="time" id="bowl_end"
                                                        class="form-control form-control-sm">

                                                    <span id="bowl_result" class="badge bg-light text-dark">(0)
                                                        Menit</span>
                                                </div>

                                                <!-- hidden -->
                                                <input type="hidden" name="waktu_bowl_cutter" id="bowl_menit">
                                                <input type="hidden" name="waktu_bowl_cutter_start"
                                                    id="bowl_start_hidden">
                                                <input type="hidden" name="waktu_bowl_cutter_end" id="bowl_end_hidden">

                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start fw-semibold">Waktu Aging Emulsi (Menit)</td>
                                            <td><input type="time" name="waktu_aging_emulsi_awal"
                                                    class="form-control form-control-sm text-center"></td>
                                            <td class="fw-bold" style="width: 5%;">s/d</td>
                                            <td><input type="time" name="waktu_aging_emulsi_akhir"
                                                    class="form-control form-control-sm text-center"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-start fw-semibold">Suhu Akhir Emulsi Gel (Std &lt;5°C)</td>
                                            <td colspan="3">
                                                <div class="input-group input-group-sm">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-toggle-minus"
                                                        tabindex="-1">±</button>
                                                    <input type="number" name="suhu_akhir_emulsi_gel" step="0.01"
                                                        inputmode="decimal"
                                                        class="form-control form-control-sm text-center suhu-number-input">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- Waktu Mixing & Emulsifying --}}
                                <table class="table table-bordered text-center align-middle">
                                    <tbody>
                                        <tr>
                                            <td class="text-start fw-semibold" style="width: 25%;">Waktu Mixing</td>
                                            <td>

                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="time" id="mixing_start"
                                                        class="form-control form-control-sm">
                                                    <span>-</span>
                                                    <input type="time" id="mixing_end"
                                                        class="form-control form-control-sm">

                                                    <span id="mixing_result" class="badge bg-light text-dark">(0)
                                                        Menit</span>
                                                </div>

                                                <!-- hidden -->
                                                <input type="hidden" name="waktu_mixing" id="mixing_menit">
                                                <input type="hidden" name="waktu_mixing_start" id="mixing_start_hidden">
                                                <input type="hidden" name="waktu_mixing_end" id="mixing_end_hidden">

                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start fw-semibold">Suhu Akhir Mixing (Std 2–5°C)</td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-toggle-minus"
                                                        tabindex="-1">±</button>
                                                    <input type="number" name="suhu_akhir_mixing" step="0.01"
                                                        inputmode="decimal"
                                                        class="form-control form-control-sm text-center suhu-number-input">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start fw-semibold">Suhu Akhir Emulsifying (Std 14±2°C)</td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-toggle-minus"
                                                        tabindex="-1">±</button>
                                                    <input type="number" name="suhu_akhir_emulsi" step="0.01"
                                                        inputmode="decimal"
                                                        class="form-control form-control-sm text-center suhu-number-input">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- ===================== CATATAN ===================== --}}
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header bg-secondary text-white fw-bold">Catatan</div>
                                <div class="card-body bg-light">
                                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
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


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =========================================================
            // DATA DARI LARAVEL
            // =========================================================

            const rawMaterials = @json($rawMaterials->pluck('nama_bahan_baku')->values());

            const inspections = [
                @foreach ($inspections as $insp)
                    @if ($insp->inspection)
                        {
                            uuid: @json($insp->uuid),
                            kode_batch: @json($insp->kode_batch),
                            bahan_baku: @json($insp->inspection->bahan_baku)
                        },
                    @endif
                @endforeach
            ];

            const premixes = @json($premixes->pluck('nama_premix')->values());


            // =========================================================
            // SELECT2
            // =========================================================

            $('.select2').select2({
                width: '100%',
                placeholder: '-- Pilih --',
                allowClear: true
            });


            // =========================================================
            // ELEMENT
            // =========================================================

            const tbodyNon = document.getElementById('tbodyNonPremix');
            const tbodyPremix = document.getElementById('tbodyPremix');
            const tbodySuhu = document.getElementById('tbodySuhuGrinding');

            let indexNonPremix = tbodyNon ?
                tbodyNon.querySelectorAll('tr').length :
                0;

            let indexPremix = tbodyPremix ?
                tbodyPremix.querySelectorAll('tr').length :
                0;

            let indexSuhu = tbodySuhu ?
                tbodySuhu.querySelectorAll('tr').length :
                0;


            // =========================================================
            // OPTION BAHAN
            // =========================================================

            function getOptionBahan(selected = '') {

                let html = `
            <option value="">
                -- Pilih Bahan --
            </option>
        `;

                rawMaterials.forEach(function(nama) {

                    const selectedAttr =
                        nama === selected ? 'selected' : '';

                    html += `
                <option value="${escapeHtml(nama)}" ${selectedAttr}>
                    ${escapeHtml(nama)}
                </option>
            `;
                });

                return html;
            }


            // =========================================================
            // OPTION BATCH
            // =========================================================

            function getOptionBatch() {

                let html = '';

                inspections.forEach(function(item) {

                    html += `
                <option
                    value="${escapeHtml(item.uuid)}"
                    data-bahan="${escapeHtml(item.bahan_baku ?? '')}">
                    ${escapeHtml(item.kode_batch ?? '')}
                </option>
            `;

                });

                return html;
            }


            // =========================================================
            // ESCAPE HTML
            // =========================================================

            function escapeHtml(value) {

                if (value === null || value === undefined) {
                    return '';
                }

                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');

            }


            // =========================================================
            // BUAT ROW NON PREMIX
            // =========================================================

            function createNonPremixRow(index, namaBahan = '') {

                const row = document.createElement('tr');

                row.innerHTML = `

            <td>
                <select
                    name="non_premix[${index}][nama_bahan]"
                    class="form-control form-select-sm text-center nama-bahan-select select2">

                    ${getOptionBahan(namaBahan)}

                </select>
            </td>

            <td>
                <select
                    name="non_premix[${index}][inspection_uuid][]"
                    class="form-control form-select-sm text-center kode-batch-select select2"
                    multiple
                    disabled>

                    <option value="" disabled>
                        Pilih Bahan dahulu
                    </option>

                    ${getOptionBatch()}

                </select>
            </td>

            <td>
                <div class="input-group input-group-sm">

                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-toggle-minus"
                        tabindex="-1">
                        ±
                    </button>

                    <input
                        type="text"
                        inputmode="decimal"
                        name="non_premix[${index}][suhu_bahan]"
                        class="form-control form-control-sm text-center suhu-number-input">

                </div>
            </td>

            <td>
                <div class="input-group input-group-sm">

                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-toggle-minus"
                        tabindex="-1">
                        ±
                    </button>

                    <input
                        type="text"
                        inputmode="decimal"
                        name="non_premix[${index}][ph_bahan]"
                        class="form-control form-control-sm text-center suhu-number-input">

                </div>
            </td>

            <td>
                <input
                    type="number"
                    name="non_premix[${index}][berat_bahan]"
                    step="0.01"
                    class="form-control form-control-sm text-center">
            </td>

            <td>
                <input
                    type="checkbox"
                    name="non_premix[${index}][sensori]"
                    value="Oke"
                    class="form-check-input">
            </td>

            <td>
                <button
                    type="button"
                    class="btn btn-sm btn-danger hapusBaris"
                    title="Hapus">

                    <i class="bi bi-trash"></i>

                </button>
            </td>

        `;

                tbodyNon.appendChild(row);


                // =====================================================
                // INIT SELECT2
                // =====================================================

                $(row).find('.select2').select2({
                    width: '100%',
                    placeholder: '-- Pilih --',
                    allowClear: true
                });


                // =====================================================
                // SIMPAN SEMUA OPTION BATCH
                // =====================================================

                const batchSelect =
                    $(row).find('.kode-batch-select');

                batchSelect.data(
                    'all-options',
                    batchSelect.find('option').clone()
                );


                // =====================================================
                // FILTER BATCH OTOMATIS
                // =====================================================

                if (namaBahan) {

                    filterBatchByBahan(
                        row.querySelector('.nama-bahan-select')
                    );

                }

                return row;
            }


            // =========================================================
            // FILTER BATCH
            // =========================================================

            function filterBatchByBahan(selectElement) {

                const row =
                    $(selectElement).closest('tr');

                const selectedBahan =
                    $(selectElement).val();

                const batchSelect =
                    row.find('.kode-batch-select');


                let allOptions =
                    batchSelect.data('all-options');


                if (!allOptions) {

                    allOptions =
                        batchSelect.find('option').clone();

                    batchSelect.data(
                        'all-options',
                        allOptions
                    );

                }


                // Hancurkan Select2
                if (
                    batchSelect.hasClass(
                        'select2-hidden-accessible'
                    )
                ) {
                    batchSelect.select2('destroy');
                }


                // Kosongkan
                batchSelect.empty();


                // =====================================================
                // BELUM PILIH BAHAN
                // =====================================================

                if (!selectedBahan) {

                    batchSelect.append(`
                <option value="" disabled selected>
                    Pilih Bahan dahulu
                </option>
            `);

                    batchSelect.prop(
                        'disabled',
                        true
                    );

                }

                // =====================================================
                // SUDAH PILIH BAHAN
                // =====================================================
                else {

                    let ditemukan = false;

                    allOptions.each(function() {

                        const option =
                            $(this).clone();

                        const bahanBatch =
                            option.attr('data-bahan');


                        if (
                            bahanBatch &&
                            bahanBatch.trim() ===
                            selectedBahan.trim()
                        ) {

                            batchSelect.append(option);

                            ditemukan = true;

                        }

                    });


                    if (!ditemukan) {

                        batchSelect.append(`
                    <option value="" disabled>
                        Tidak ada kode batch untuk bahan ini
                    </option>
                `);

                    }


                    batchSelect.prop(
                        'disabled',
                        false
                    );

                }


                batchSelect.val(null);


                // Init Select2 kembali
                batchSelect.select2({

                    width: '100%',

                    placeholder: selectedBahan ?
                        '-- Pilih Batch --' : 'Pilih Bahan dahulu',

                    allowClear: true

                });

            }


            // =========================================================
            // CHANGE BAHAN MANUAL
            // =========================================================

            $(document).on(
                'change',
                '.nama-bahan-select',
                function() {

                    filterBatchByBahan(this);

                }
            );


            // =========================================================
            // PILIH PRODUK
            // =========================================================

            $('#namaProdukSelect').on(
                'change',
                function() {

                    const selectedOption =
                        $(this).find('option:selected');


                    let bahanBaku =
                        selectedOption.attr(
                            'data-bahan-baku'
                        );


                    // =================================================
                    // PARSE JSON BAHAN BAKU
                    // =================================================

                    if (bahanBaku) {

                        try {

                            bahanBaku =
                                JSON.parse(bahanBaku);

                        } catch (error) {

                            console.error(
                                'JSON bahan baku tidak valid:',
                                error
                            );

                            bahanBaku = [];

                        }

                    } else {

                        bahanBaku = [];

                    }


                    console.log(
                        'Produk:',
                        selectedOption.val()
                    );

                    console.log(
                        'Bahan baku:',
                        bahanBaku
                    );


                    // =================================================
                    // KOSONGKAN TABEL
                    // =================================================

                    tbodyNon.innerHTML = '';

                    indexNonPremix = 0;


                    // =================================================
                    // JIKA TIDAK ADA BAHAN
                    // =================================================

                    if (
                        !Array.isArray(bahanBaku) ||
                        bahanBaku.length === 0
                    ) {

                        createNonPremixRow(
                            indexNonPremix
                        );

                        indexNonPremix++;

                        return;

                    }


                    // =================================================
                    // BUAT ROW SESUAI BAHAN PRODUK
                    // =================================================

                    bahanBaku.forEach(
                        function(namaBahan) {

                            createNonPremixRow(
                                indexNonPremix,
                                namaBahan
                            );

                            indexNonPremix++;

                        }
                    );

                }
            );


            // =========================================================
            // TAMBAH BAHAN MANUAL
            // =========================================================

            $('#tambahBarisNonPremix').on(
                'click',
                function() {

                    createNonPremixRow(
                        indexNonPremix
                    );

                    indexNonPremix++;

                }
            );


            // =========================================================
            // TAMBAH SUHU / DAGING
            // =========================================================

            $('#tambahBarisSuhu').on(
                'click',
                function() {

                    const row = `

                <tr>

                    <td style="width: 45%;">

                        <select
                            name="suhu_grinding_input[${indexSuhu}][daging]"
                            class="form-control form-select-sm select2">

                            <option
                                value=""
                                selected
                                disabled>
                                Pilih Daging
                            </option>

                            <option value="BEEF">BEEF</option>
                            <option value="SBB">SBB</option>
                            <option value="SBL">SBL</option>
                            <option value="MDM">MDM</option>
                            <option value="CCM">CCM</option>
                            <option value="SURIMI">SURIMI</option>

                        </select>

                    </td>

                    <td style="width: 45%;">

                        <div class="input-group input-group-sm">

                            <button
                                type="button"
                                class="btn btn-outline-secondary btn-toggle-minus"
                                tabindex="-1">
                                ±
                            </button>

                            <input
                                type="text"
                                inputmode="decimal"
                                name="suhu_grinding_input[${indexSuhu}][suhu]"
                                class="form-control form-control-sm text-center suhu-number-input">

                        </div>

                    </td>

                    <td style="width: 10%;">

                        <button
                            type="button"
                            class="btn btn-sm btn-danger hapusBarisSuhu">

                            <i class="bi bi-trash"></i>

                        </button>

                    </td>

                </tr>
            `;


                    tbodySuhu.insertAdjacentHTML(
                        'beforeend',
                        row
                    );


                    const newRow =
                        tbodySuhu.lastElementChild;


                    $(newRow)
                        .find('.select2')
                        .select2({

                            width: '100%',

                            placeholder: '-- Pilih --',

                            allowClear: true

                        });


                    indexSuhu++;

                }
            );


            // =========================================================
            // TAMBAH PREMIX
            // =========================================================

            $('#tambahBarisPremix').on(
                'click',
                function() {

                    let optionPremix = `
                <option value="">
                    -- Pilih Premix --
                </option>
            `;

                    premixes.forEach(function(nama) {

                        optionPremix += `
                    <option value="${escapeHtml(nama)}">
                        ${escapeHtml(nama)}
                    </option>
                `;

                    });


                    let optionKodePremix = `
                <option value="">
                    -- Pilih Kode Batch --
                </option>
            `;

                    inspections.forEach(function(item) {

                        optionKodePremix += `
                    <option value="${escapeHtml(item.kode_batch)}">
                        ${escapeHtml(item.kode_batch)}
                    </option>
                `;

                    });


                    const row = `

                <tr>

                    <td>

                        <select
                            name="premix[${indexPremix}][nama_premix]"
                            class="form-control form-select-sm text-center select2">

                            ${optionPremix}

                        </select>

                    </td>

                    <td>

                        <select
                            name="premix[${indexPremix}][kode_premix]"
                            class="form-control form-select-sm text-center select2">

                            ${optionKodePremix}

                        </select>

                    </td>

                    <td>

                        <input
                            type="number"
                            name="premix[${indexPremix}][berat_premix]"
                            step="0.0001"
                            min="0"
                            class="form-control form-control-sm text-center">

                    </td>

                    <td>

                        <input
                            type="checkbox"
                            name="premix[${indexPremix}][sensori_premix]"
                            value="Oke"
                            class="form-check-input">

                    </td>

                    <td>

                        <button
                            type="button"
                            class="btn btn-danger btn-sm hapusBarisPremix">

                            <i class="bi bi-trash"></i>

                        </button>

                    </td>

                </tr>

            `;


                    tbodyPremix.insertAdjacentHTML(
                        'beforeend',
                        row
                    );


                    const newRow =
                        tbodyPremix.lastElementChild;


                    $(newRow)
                        .find('.select2')
                        .select2({

                            width: '100%',

                            placeholder: '-- Pilih --',

                            allowClear: true

                        });


                    indexPremix++;

                }
            );


            // =========================================================
            // HAPUS ROW
            // =========================================================

            document.addEventListener(
                'click',
                function(e) {


                    // NON PREMIX
                    const hapusNon =
                        e.target.closest(
                            '.hapusBaris'
                        );

                    if (hapusNon) {

                        if (
                            tbodyNon.querySelectorAll('tr').length > 1
                        ) {

                            const row =
                                hapusNon.closest('tr');

                            $(row)
                                .find('.select2')
                                .each(function() {

                                    if (
                                        $(this).hasClass(
                                            'select2-hidden-accessible'
                                        )
                                    ) {

                                        $(this)
                                            .select2('destroy');

                                    }

                                });

                            row.remove();

                        } else {

                            alert(
                                'Minimal satu baris Non-Premix wajib ada'
                            );

                        }

                    }


                    // PREMIX
                    const hapusPremix =
                        e.target.closest(
                            '.hapusBarisPremix'
                        );

                    if (hapusPremix) {

                        if (
                            tbodyPremix.querySelectorAll('tr').length > 1
                        ) {

                            const row =
                                hapusPremix.closest('tr');

                            $(row)
                                .find('.select2')
                                .each(function() {

                                    if (
                                        $(this).hasClass(
                                            'select2-hidden-accessible'
                                        )
                                    ) {

                                        $(this)
                                            .select2('destroy');

                                    }

                                });

                            row.remove();

                        } else {

                            alert(
                                'Minimal satu baris Premix wajib ada'
                            );

                        }

                    }


                    // SUHU
                    const hapusSuhu =
                        e.target.closest(
                            '.hapusBarisSuhu'
                        );

                    if (hapusSuhu) {

                        if (
                            tbodySuhu.querySelectorAll('tr').length > 1
                        ) {

                            const row =
                                hapusSuhu.closest('tr');

                            $(row)
                                .find('.select2')
                                .each(function() {

                                    if (
                                        $(this).hasClass(
                                            'select2-hidden-accessible'
                                        )
                                    ) {

                                        $(this)
                                            .select2('destroy');

                                    }

                                });

                            row.remove();

                        } else {

                            alert(
                                'Minimal satu baris suhu wajib ada'
                            );

                        }

                    }

                }
            );


            // =========================================================
            // INPUT SUHU / PH
            // =========================================================

            document.addEventListener(
                'input',
                function(e) {

                    if (
                        !e.target.classList.contains(
                            'suhu-number-input'
                        )
                    ) {
                        return;
                    }


                    let val =
                        e.target.value;


                    val =
                        val.replace(
                            /[^0-9.,-]/g,
                            ''
                        );


                    val =
                        val.replace(
                            ',',
                            '.'
                        );


                    if (
                        val.indexOf('-') > 0
                    ) {

                        val =
                            val.replace(
                                /-/g,
                                ''
                            );

                        val =
                            '-' + val;

                    }


                    const parts =
                        val.split('.');


                    if (
                        parts.length > 2
                    ) {

                        val =
                            parts[0] +
                            '.' +
                            parts.slice(1).join('');

                    }


                    e.target.value =
                        val;

                }
            );


            // =========================================================
            // TOMBOL MINUS
            // =========================================================

            document.addEventListener(
                'click',
                function(e) {

                    const btn =
                        e.target.closest(
                            '.btn-toggle-minus'
                        );

                    if (!btn) {
                        return;
                    }


                    const input =
                        btn
                        .closest('.input-group')
                        ?.querySelector('input');


                    if (!input) {
                        return;
                    }


                    input.value =
                        input.value.startsWith('-') ?
                        input.value.slice(1) :
                        '-' + input.value;


                    input.dispatchEvent(
                        new Event('input')
                    );

                    input.focus();

                }
            );


            // =========================================================
            // AUTO DATE & SHIFT
            // =========================================================

            const dateInput =
                document.getElementById(
                    'dateInput'
                );

            const shiftInput =
                document.getElementById(
                    'shiftInput'
                );


            if (
                dateInput &&
                shiftInput
            ) {

                const now =
                    new Date();


                const yyyy =
                    now.getFullYear();

                const mm =
                    String(
                        now.getMonth() + 1
                    ).padStart(2, '0');

                const dd =
                    String(
                        now.getDate()
                    ).padStart(2, '0');

                const hh =
                    now.getHours();


                dateInput.value =
                    `${yyyy}-${mm}-${dd}`;


                if (
                    hh >= 7 &&
                    hh < 15
                ) {

                    shiftInput.value = '1';

                } else if (
                    hh >= 15 &&
                    hh < 23
                ) {

                    shiftInput.value = '2';

                } else {

                    shiftInput.value = '3';

                }

            }


            // =========================================================
            // HITUNG WAKTU
            // =========================================================

            function hitungWaktu(
                startId,
                endId,
                resultId,
                menitId,
                startHidden,
                endHidden
            ) {

                const startEl =
                    document.getElementById(startId);

                const endEl =
                    document.getElementById(endId);


                if (
                    !startEl ||
                    !endEl
                ) {
                    return;
                }


                const start =
                    startEl.value;

                const end =
                    endEl.value;


                if (
                    start &&
                    end
                ) {

                    let startTime =
                        new Date(
                            '1970-01-01T' +
                            start +
                            ':00'
                        );

                    let endTime =
                        new Date(
                            '1970-01-01T' +
                            end +
                            ':00'
                        );


                    let diff =
                        (endTime - startTime) /
                        60000;


                    if (diff < 0) {
                        diff += 1440;
                    }


                    document.getElementById(
                            resultId
                        ).innerText =
                        `${start} - ${end} (${diff}) Menit`;


                    document.getElementById(
                        menitId
                    ).value = diff;


                    document.getElementById(
                        startHidden
                    ).value = start;


                    document.getElementById(
                        endHidden
                    ).value = end;

                }

            }


            [
                [
                    'premix_start',
                    'premix_end',
                    'premix_result',
                    'premix_menit',
                    'premix_start_hidden',
                    'premix_end_hidden'
                ],

                [
                    'bowl_start',
                    'bowl_end',
                    'bowl_result',
                    'bowl_menit',
                    'bowl_start_hidden',
                    'bowl_end_hidden'
                ],

                [
                    'mixing_start',
                    'mixing_end',
                    'mixing_result',
                    'mixing_menit',
                    'mixing_start_hidden',
                    'mixing_end_hidden'
                ]

            ].forEach(function(ids) {

                const startEl =
                    document.getElementById(ids[0]);

                const endEl =
                    document.getElementById(ids[1]);


                if (
                    startEl &&
                    endEl
                ) {

                    startEl.addEventListener(
                        'change',
                        function() {
                            hitungWaktu(...ids);
                        }
                    );

                    endEl.addEventListener(
                        'change',
                        function() {
                            hitungWaktu(...ids);
                        }
                    );

                }

            });


            // =========================================================
            // RELASI WAKTU PREPARATION
            // =========================================================

            const waktuMulaiPreparation =
                document.getElementById(
                    'waktu_mulai'
                );

            const waktuSelesaiPreparation =
                document.getElementById(
                    'waktu_selesai'
                );

            const bowlStart =
                document.getElementById(
                    'bowl_start'
                );

            const mixingStart =
                document.getElementById(
                    'mixing_start'
                );


            if (
                waktuMulaiPreparation &&
                bowlStart
            ) {

                waktuMulaiPreparation.addEventListener(
                    'change',
                    function() {

                        if (this.value) {

                            bowlStart.value =
                                this.value;

                            bowlStart.dispatchEvent(
                                new Event('change')
                            );

                        }

                    }
                );

            }


            if (
                waktuSelesaiPreparation &&
                mixingStart
            ) {

                waktuSelesaiPreparation.addEventListener(
                    'change',
                    function() {

                        if (this.value) {

                            mixingStart.value =
                                this.value;

                            mixingStart.dispatchEvent(
                                new Event('change')
                            );

                        }

                    }
                );

            }


            // =========================================================
            // VALIDASI KODE PRODUKSI
            // =========================================================

            const kodeInput =
                $('#kode_produksi');

            const kodeError =
                $('#kodeError');

            const form =
                $('#mincingForm');


            function validateKode() {

                let value =
                    kodeInput
                    .val()
                    .toUpperCase()
                    .replace(/\s+/g, '');


                kodeInput.val(value);

                kodeError
                    .text('')
                    .addClass('d-none');


                if (
                    value.length !== 10
                ) {

                    kodeError
                        .text(
                            'Kode Batch harus 10 karakter'
                        )
                        .removeClass('d-none');

                    return false;

                }


                if (
                    !/^[A-Z0-9]+$/.test(value)
                ) {

                    kodeError
                        .text(
                            'Hanya huruf besar & angka'
                        )
                        .removeClass('d-none');

                    return false;

                }


                if (
                    !/^[A-L]$/.test(
                        value.charAt(1)
                    )
                ) {

                    kodeError
                        .text(
                            'Karakter ke-2 harus huruf bulan (A-L)'
                        )
                        .removeClass('d-none');

                    return false;

                }


                const hari =
                    parseInt(
                        value.substr(2, 2),
                        10
                    );


                if (
                    isNaN(hari) ||
                    hari < 1 ||
                    hari > 31
                ) {

                    kodeError
                        .text(
                            'Karakter ke-3 & ke-4 harus tanggal valid (01-31)'
                        )
                        .removeClass('d-none');

                    return false;

                }


                return true;

            }


            kodeInput.on(
                'input',
                validateKode
            );


            form.on(
                'submit',
                function(e) {

                    if (!validateKode()) {

                        e.preventDefault();

                        alert(
                            'Kode Batch tidak valid! Periksa kembali.'
                        );

                        kodeInput.focus();

                        return;

                    }


                    // Aktifkan select batch agar
                    // nilainya tetap terkirim
                    $('.kode-batch-select').each(
                        function() {

                            $(this).prop(
                                'disabled',
                                false
                            );

                        }
                    );

                }
            );

        });
    </script>

    <script>
        // --- Script Input Suhu (mendukung nilai minus di HP) ---
        document.addEventListener('input', function(e) {
            if (!e.target.classList.contains('suhu-number-input')) return;
            let val = e.target.value;
            val = val.replace(/[^0-9.,-]/g, '');
            val = val.replace(',', '.');
            if (val.indexOf('-') > 0) {
                val = val.replace(/-/g, '');
                val = '-' + val;
            }
            const parts = val.split('.');
            if (parts.length > 2) {
                val = parts[0] + '.' + parts.slice(1).join('');
            }
            e.target.value = val;
        });

        // --- Tombol ± Toggle Minus ---
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-toggle-minus');
            if (!btn) return;
            const input = btn.closest('.input-group').querySelector('input');
            if (!input) return;
            input.value = input.value.startsWith('-') ?
                input.value.slice(1) :
                '-' + input.value;
            input.dispatchEvent(new Event('input'));
            input.focus();
        });

        // ==========================================
        // RELASI WAKTU PROSES
        // ==========================================

        const waktuMulaiPreparation = document.getElementById('waktu_mulai');
        const waktuSelesaiPreparation = document.getElementById('waktu_selesai');

        const bowlStart = document.getElementById('bowl_start');
        const mixingStart = document.getElementById('mixing_start');


        // Waktu Mulai Preparation
        // → otomatis menjadi Waktu Awal Bowl Cutter
        if (waktuMulaiPreparation && bowlStart) {

            waktuMulaiPreparation.addEventListener('change', function() {

                if (this.value) {
                    bowlStart.value = this.value;

                    // Trigger change jika ada proses lain
                    bowlStart.dispatchEvent(new Event('change'));
                }

            });
        }


        // Waktu Akhir Preparation
        // → otomatis menjadi Waktu Awal Mixing
        if (waktuSelesaiPreparation && mixingStart) {

            waktuSelesaiPreparation.addEventListener('change', function() {

                if (this.value) {
                    mixingStart.value = this.value;

                    // Trigger change jika ada proses lain
                    mixingStart.dispatchEvent(new Event('change'));
                }

            });
        }
    </script>
@endsection
