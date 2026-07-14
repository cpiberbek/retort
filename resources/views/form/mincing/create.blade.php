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
                                    <select name="nama_produk" class="form-control selectpicker" data-live-search="true"
                                        required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
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
                                                <input type="time" name="waktu_mulai"
                                                    class="form-control form-control-sm text-center">
                                            </td>
                                            <td class="fw-bold">s/d</td>
                                            <td>
                                                <input type="time" name="waktu_selesai"
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
                                                <select name="non_premix[0][nama_bahan]"
                                                    class="form-control form-select-sm text-center nama-bahan-select">

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
                                                <select name="non_premix[0][inspection_uuid]"
                                                    class="form-control form-select-sm text-center kode-batch-select">

                                                    <option value="" disabled selected>
                                                        -- Pilih Batch --
                                                    </option>

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
                                                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                                                    <input type="text" inputmode="decimal" name="non_premix[0][suhu_bahan]"
                                                        class="form-control form-control-sm text-center suhu-number-input">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="non_premix[0][ph_bahan]"
                                                    class="form-control form-control-sm text-center">
                                            </td>
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
                                            <td>

                                                <select name="premix[0][nama_premix]"
                                                    class="form-control form-select-sm text-center" required>

                                                    <option value="">-- Pilih Premix --</option>

                                                    @foreach ($premixes as $premix)
                                                        <option value="{{ $premix->nama_premix }}">

                                                            {{ $premix->nama_premix }}

                                                        </option>
                                                    @endforeach

                                                </select>

                                            </td>
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
                                                                    class="form-control form-select-sm">
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
                                                                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                                                                    <input type="text" inputmode="decimal" name="suhu_grinding_input[0][suhu]"
                                                                        step="0.01"
                                                                        class="form-control form-control-sm text-center suhu-number-input"
                                                                       >
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
                                                <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                                                <input type="text" inputmode="decimal" name="suhu_akhir_emulsi_gel"
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
                                                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                                                    <input type="text" inputmode="decimal" name="suhu_akhir_mixing" 
                                                        class="form-control form-control-sm text-center suhu-number-input">
                                                </div>
                                             </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start fw-semibold">Suhu Akhir Emulsifying (Std 14±2°C)</td>
                                             <td>
                                                <div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                                                    <input type="text" inputmode="decimal" name="suhu_akhir_emulsi" 
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // =========================
            // AUTO DATE & SHIFT
            // =========================
            const dateInput = document.getElementById("dateInput");
            const shiftInput = document.getElementById("shiftInput");

            if (dateInput && shiftInput) {
                let now = new Date();
                let yyyy = now.getFullYear();
                let mm = String(now.getMonth() + 1).padStart(2, '0');
                let dd = String(now.getDate()).padStart(2, '0');
                let hh = now.getHours();

                dateInput.value = `${yyyy}-${mm}-${dd}`;

                if (hh >= 7 && hh < 15) shiftInput.value = "1";
                else if (hh >= 15 && hh < 23) shiftInput.value = "2";
                else shiftInput.value = "3";
            }

            // =========================
            // HITUNG WAKTU
            // =========================
            function hitungWaktu(startId, endId, resultId, menitId, startHidden, endHidden) {

                const startEl = document.getElementById(startId);
                const endEl = document.getElementById(endId);

                if (!startEl || !endEl) return;

                const start = startEl.value;
                const end = endEl.value;

                if (start && end) {
                    let startTime = new Date("1970-01-01T" + start + ":00");
                    let endTime = new Date("1970-01-01T" + end + ":00");

                    let diff = (endTime - startTime) / 60000;
                    if (diff < 0) diff += 1440;

                    document.getElementById(resultId).innerText =
                        `${start} - ${end} (${diff}) Menit`;

                    document.getElementById(menitId).value = diff;
                    document.getElementById(startHidden).value = start;
                    document.getElementById(endHidden).value = end;
                }
            }

            // =========================
            // EVENT LISTENER AMAN (NO ERROR)
            // =========================
            [
                ['premix_start', 'premix_end', 'premix_result', 'premix_menit', 'premix_start_hidden',
                    'premix_end_hidden'
                ],
                ['bowl_start', 'bowl_end', 'bowl_result', 'bowl_menit', 'bowl_start_hidden', 'bowl_end_hidden'],
                ['mixing_start', 'mixing_end', 'mixing_result', 'mixing_menit', 'mixing_start_hidden',
                    'mixing_end_hidden'
                ]
            ].forEach(ids => {

                const startEl = document.getElementById(ids[0]);
                const endEl = document.getElementById(ids[1]);

                if (startEl && endEl) {
                    startEl.addEventListener('change', () => hitungWaktu(...ids));
                    endEl.addEventListener('change', () => hitungWaktu(...ids));
                }

            });

            // =========================
            // DINAMIS TABLE
            // =========================
            const tbodyNon = document.getElementById('tbodyNonPremix');
            const tbodyPremix = document.getElementById('tbodyPremix');
            const tbodySuhu = document.getElementById('tbodySuhuGrinding');

            let indexNonPremix = tbodyNon ? tbodyNon.querySelectorAll('tr').length : 0;
            let indexPremix = tbodyPremix ? tbodyPremix.querySelectorAll('tr').length : 0;
            let indexSuhu = tbodySuhu ? tbodySuhu.querySelectorAll('tr').length : 0;

            // NON PREMIX
            document.getElementById('tambahBarisNonPremix')?.addEventListener('click', () => {

                let optionBahan = `<option value="" disabled selected>-- Pilih Bahan --</option>`;

                @foreach ($rawMaterials as $rm)
                    optionBahan += `
                    <option value="{{ $rm->nama_bahan_baku }}">
                        {{ $rm->nama_bahan_baku }}
                    </option>
                `;
                @endforeach

                let optionBatch = `<option value="" disabled selected>-- Pilih Batch --</option>`;

                @foreach ($inspections as $insp)

                    @if ($insp->inspection)

                        optionBatch += `
                        <option
                            value="{{ $insp->uuid }}"
                            data-bahan="{{ $insp->inspection->bahan_baku }}">

                            {{ $insp->kode_batch }}

                        </option>
                    `;
                    @endif
                @endforeach

                const row = `
                <tr>

                    <td>
                        <select
                            name="non_premix[${indexNonPremix}][nama_bahan]"
                            class="form-control form-select-sm nama-bahan-select">

                            ${optionBahan}

                        </select>
                    </td>

                    <td>
                        <select
                            name="non_premix[${indexNonPremix}][inspection_uuid]"
                            class="form-control form-select-sm kode-batch-select">

                            ${optionBatch}

                        </select>
                    </td>

                    <td>
                        <div class="input-group input-group-sm">
                            <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                            <input type="text" inputmode="decimal"
                                name="non_premix[${indexNonPremix}][suhu_bahan]"
                                class="form-control form-control-sm suhu-number-input">
                        </div>
                    </td>

                    <td>
                        <input type="number"
                            name="non_premix[${indexNonPremix}][ph_bahan]"
                            step="0.01"
                            class="form-control form-control-sm">
                    </td>

                    <td>
                        <input type="number"
                            name="non_premix[${indexNonPremix}][berat_bahan]"
                            step="0.01"
                            class="form-control form-control-sm">
                    </td>

                    <td>
                        <input type="checkbox"
                            name="non_premix[${indexNonPremix}][sensori]"
                            value="Oke">
                    </td>

                    <td>
                        <button type="button"
                            class="btn btn-danger btn-sm hapusBaris">
                            Hapus
                        </button>
                    </td>

                </tr>
            `;

                tbodyNon.insertAdjacentHTML('beforeend', row);

                indexNonPremix++;
            });

            document.addEventListener('change', function(e) {

                if (e.target.classList.contains('nama-bahan-select')) {

                    const selectedBahan = e.target.value;

                    const row = e.target.closest('tr');

                    const batchSelect = row.querySelector('.kode-batch-select');

                    const options = batchSelect.querySelectorAll('option');

                    batchSelect.value = '';

                    options.forEach(option => {

                        if (!option.dataset.bahan) return;

                        if (option.dataset.bahan === selectedBahan) {
                            option.hidden = false;
                        } else {
                            option.hidden = true;
                        }

                    });

                }

            });

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
                        kodeError.text('Karakter ke-3 & ke-4 harus tanggal valid (01-31)').removeClass(
                            'd-none');
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

            // TAMBAH SUHU / DAGING
            document.getElementById('tambahBarisSuhu')?.addEventListener('click', () => {
                const row = `
                <tr>
                    <td style="width: 45%;">
                        <select name="suhu_grinding_input[${indexSuhu}][daging]" class="form-control form-select-sm">
                            <option value="" selected disabled>Pilih Daging</option>
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
                            <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                            <input type="text" inputmode="decimal" name="suhu_grinding_input[${indexSuhu}][suhu]" class="form-control form-control-sm text-center suhu-number-input">
                        </div>
                    </td>
                    <td style="width: 10%;">
                        <button type="button" class="btn btn-sm btn-danger hapusBarisSuhu"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
                tbodySuhu.insertAdjacentHTML('beforeend', row);
                indexSuhu++;
            });

            // PREMIX
            document.getElementById('tambahBarisPremix')?.addEventListener('click', () => {

                let optionPremix = `
        <option value="">-- Pilih Premix --</option>
    `;

                @foreach ($premixes as $premix)

                    optionPremix += `
            <option value="{{ $premix->nama_premix }}">
                {{ $premix->nama_premix }}
            </option>
        `;
                @endforeach

                const row = `

        <tr>

            <td>

                <select
                    name="premix[${indexPremix}][nama_premix]"
                    class="form-control form-select-sm text-center"
                    required>

                    ${optionPremix}

                </select>

            </td>

            <td>

                <input
                    type="text"
                    name="premix[${indexPremix}][kode_premix]"
                    class="form-control form-control-sm text-center">

            </td>

            <td>

                <input
                    type="number"
                    name="premix[${indexPremix}][berat_premix]"
                    step="0.01"
                    class="form-control form-control-sm text-center">

            </td>

            <td>

                <input
                    type="checkbox"
                    name="premix[${indexPremix}][sensori_premix]"
                    value="Oke">

            </td>

            <td>

                <button
                    type="button"
                    class="btn btn-danger btn-sm hapusBarisPremix">

                    Hapus

                </button>

            </td>

        </tr>
    `;

                tbodyPremix.insertAdjacentHTML('beforeend', row);

                indexPremix++;

            });

            // HAPUS ROW
            document.addEventListener('click', function(e) {
                if (e.target.closest('.hapusBaris')) e.target.closest('tr').remove();
                if (e.target.closest('.hapusBarisPremix')) e.target.closest('tr').remove();

                if (e.target.closest('.hapusBarisSuhu')) {
                    if (tbodySuhu.querySelectorAll('tr').length > 1) {
                        e.target.closest('tr').remove();
                    } else {
                        alert("Minimal satu baris suhu wajib ada");
                    }
                }
            });

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
            input.value = input.value.startsWith('-')
                ? input.value.slice(1)
                : '-' + input.value;
            input.dispatchEvent(new Event('input'));
            input.focus();
        });
    </script>
@endsection
