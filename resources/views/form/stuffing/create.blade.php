@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Stuffing Sosis Retort
                </h4>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4 shadow-sm" role="alert">
                        <strong class="d-block mb-2"><i class="bi bi-exclamation-triangle-fill"></i> Terjadi Kesalahan!</strong>
                        Terdapat input yang tidak valid atau terlewat. Silakan periksa kembali:
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Shift</label>
                                    <select name="shift" id="shiftInput"
                                        class="form-control @error('shift') is-invalid @enderror" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="1" {{ old('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                                        <option value="2" {{ old('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                                        <option value="3" {{ old('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Varian</label>
                                    <select name="nama_produk" id="nama_produk"
                                        class="form-control selectpicker @error('nama_produk') is-invalid @enderror"
                                        data-live-search="true" required>
                                        <option value="">-- Pilih Varian --</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}"
                                                {{ old('nama_produk') == $produk->nama_produk ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kode Batch</label>
                                    <select name="kode_produksi" id="kode_produksi"
                                        class="form-control select2-batch @error('kode_produksi') is-invalid @enderror" required disabled>
                                        <option value="">Pilih Varian Terlebih Dahulu</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Exp. Date</label>
                                    <input type="date" name="exp_date" id="exp_date"
                                        class="form-control @error('exp_date') is-invalid @enderror"
                                        value="{{ old('exp_date') }}" readonly>
                                    <small class="text-muted">Dihitung otomatis +7 bulan dari kode batch</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== DATA STUFFING ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                            <strong>Data Stuffing</strong>
                            <button type="button" class="btn btn-dark btn-sm btnTambah" id="btnTambahStuffing">
                                <i class="bi bi-plus-circle"></i> Tambah Data
                            </button>
                        </div>

                        <div class="card-body">
                            <div class="accordion" id="accordionStuffing">

                                {{-- LOGIKA LOOPING OLD VALUE --}}
                                @php
                                    // Ambil data stuffing sebelumnya jika ada error, jika tidak ada, beri 1 array kosong sebagai default
                                    $oldStuffing = old('stuffing', [[]]); 
                                @endphp

                                @foreach ($oldStuffing as $index => $item)
                                    <div class="accordion-item stuffing-item">
                                        <h5 class="accordion-header" id="heading{{ $index }}">
                                            {{-- Buka accordion pertama otomatis, sisanya tutup --}}
                                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $index }}">
                                                <i class="bi bi-clipboard-check me-2 text-warning"></i>
                                                Stuffing {{ $index + 1 }}
                                            </button>
                                        </h5>

                                        <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                            data-bs-parent="#accordionStuffing">
                                            <div class="accordion-body">

                                                <div class="text-end mb-3">
                                                    <button type="button" class="btn btn-outline-danger btn-sm btnHapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                {{-- ================= FORM ================= --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Nama Mesin</label>
                                                    <select name="stuffing[{{ $index }}][kode_mesin]" class="form-control @error("stuffing.$index.kode_mesin") is-invalid @enderror" required>
                                                        <option value="">-- Pilih Mesin --</option>
                                                        @foreach ($mesins as $m)
                                                            <option value="{{ $m->nama_mesin }}" {{ old("stuffing.$index.kode_mesin") == $m->nama_mesin ? 'selected' : '' }}>
                                                                {{ $m->nama_mesin }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Jam Mulai</label>
                                                    {{-- Gunakan id="jamMulaiInput" hanya pada index 0 untuk auto-fill script JS --}}
                                                    <input type="time" name="stuffing[{{ $index }}][jam_mulai]" 
                                                           {{ $index == 0 ? 'id=jamMulaiInput' : '' }} 
                                                           class="form-control @error("stuffing.$index.jam_mulai") is-invalid @enderror" 
                                                           value="{{ old("stuffing.$index.jam_mulai") }}">
                                                </div>

                                                <hr>
                                                <h6 class="fw-bold text-primary">Parameter Adonan</h6>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Suhu (°C)</label>
                                                    <input type="number" step="0.01" name="stuffing[{{ $index }}][suhu]" 
                                                           class="form-control" value="{{ old("stuffing.$index.suhu") }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Sensori</label>
                                                    <select name="stuffing[{{ $index }}][sensori]" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="OK" {{ old("stuffing.$index.sensori") == 'OK' ? 'selected' : '' }}>OK</option>
                                                        <option value="Tidak OK" {{ old("stuffing.$index.sensori") == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                    </select>
                                                </div>

                                                <hr>
                                                <h6 class="fw-bold text-primary">Parameter Stuffing</h6>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Kecepatan Stuffing (/mnt)</label>
                                                    <input type="number" step="0.01" name="stuffing[{{ $index }}][kecepatan_stuffing]" 
                                                           class="form-control" value="{{ old("stuffing.$index.kecepatan_stuffing") }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Panjang per pcs (cm)</label>
                                                    <input type="number" step="0.01" name="stuffing[{{ $index }}][panjang_pcs]" 
                                                           class="form-control" value="{{ old("stuffing.$index.panjang_pcs") }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Berat per pcs (gr)</label>
                                                    <input type="number" step="0.01" name="stuffing[{{ $index }}][berat_pcs]" 
                                                           class="form-control" value="{{ old("stuffing.$index.berat_pcs") }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Kebersihan Ujung Seal</label>
                                                    <select name="stuffing[{{ $index }}][kebersihan_seal]" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="OK" {{ old("stuffing.$index.kebersihan_seal") == 'OK' ? 'selected' : '' }}>OK</option>
                                                        <option value="Tidak OK" {{ old("stuffing.$index.kebersihan_seal") == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Kekuatan Seal</label>
                                                    <select name="stuffing[{{ $index }}][kekuatan_seal]" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="OK" {{ old("stuffing.$index.kekuatan_seal") == 'OK' ? 'selected' : '' }}>OK</option>
                                                        <option value="Tidak OK" {{ old("stuffing.$index.kekuatan_seal") == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Diameter Klip (mm)</label>
                                                    <input type="number" step="0.01" name="stuffing[{{ $index }}][diameter_klip]" 
                                                           class="form-control" value="{{ old("stuffing.$index.diameter_klip") }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Print Kode Production</label>
                                                    <select name="stuffing[{{ $index }}][print_kode]" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="OK" {{ old("stuffing.$index.print_kode") == 'OK' ? 'selected' : '' }}>OK</option>
                                                        <option value="Tidak OK" {{ old("stuffing.$index.print_kode") == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Lebar Cassing (mm)</label>
                                                    <input type="number" step="0.01" name="stuffing[{{ $index }}][lebar_cassing]" 
                                                           class="form-control" value="{{ old("stuffing.$index.lebar_cassing") }}">
                                                </div>

                                                {{-- CATATAN --}}
                                                <div class="card mt-4">
                                                    <div class="card-header bg-light">
                                                        <strong>Catatan</strong>
                                                    </div>
                                                    <div class="card-body">
                                                        <textarea name="stuffing[{{ $index }}][catatan]" class="form-control" rows="3">{{ old("stuffing.$index.catatan") }}</textarea>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
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
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    
    {{-- Select2 CSS & JS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. AUTO-FILL TANGGAL & WAKTU (Aman dari Crash)
            try {
                const dateInput = document.getElementById("dateInput");
                const shiftInput = document.getElementById("shiftInput");
                const timeInput = document.getElementById("jamMulaiInput");

                if (dateInput && !dateInput.value) {
                    let now = new Date();
                    let localDate = new Date(now.getTime() - (now.getTimezoneOffset() * 60000));
                    dateInput.value = localDate.toISOString().slice(0, 10);
                    
                    if (timeInput) timeInput.value = now.toTimeString().slice(0, 5);

                    let hour = now.getHours();
                    if (hour >= 7 && hour < 15) shiftInput.value = "1";
                    else if (hour >= 15 && hour < 23) shiftInput.value = "2";
                    else shiftInput.value = "3";
                }
            } catch (err) {
                console.error("⚠️ Error Auto-Fill:", err);
            }

            // 2. INIT SELECTPICKER DENGAN TRY-CATCH
            try {
                if ($.fn.selectpicker) {
                    $('.selectpicker').selectpicker();
                } else {
                    console.warn("⚠️ Selectpicker plugin tidak terdeteksi!");
                }
            } catch (err) {
                console.error("⚠️ Error saat memuat Selectpicker:", err);
            }

            const batchSelect = $('#kode_produksi');
            const expDateInput = $('#exp_date');

            if (!$('#nama_produk').val()) {
                batchSelect.prop('disabled', true);
            }

            // 3. EVENT DELEGATION UNTUK NAMA VARIAN (SELECT2 AJAX)
            $(document).on('change', '#nama_produk', function() {
                let namaProduk = $(this).val();

                batchSelect.empty().trigger('change');

                if (!namaProduk) {
                    batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
                    batchSelect.prop('disabled', true);
                    expDateInput.val('');
                    return;
                }

                batchSelect.prop('disabled', false);
                
                if (batchSelect.data('select2')) {
                    batchSelect.select2('destroy');
                }
                
                batchSelect.html('<option value="">-- Pilih Batch --</option>');
                
                batchSelect.select2({
                    theme: "bootstrap-5",
                    width: '100%',
                    placeholder: "-- Pilih Batch --",
                    allowClear: true,
                    ajax: {
                        url: "{{ url('/lookup/batch-packing') }}/" + encodeURIComponent(namaProduk),
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
            });

            // 4. EVENT KETIKA BATCH DIUBAH (HITUNG EXP DATE)
            $(document).on('change', '#kode_produksi', function() {
                let selectedText = $(this).find("option:selected").text();
                let kodeProduksi = selectedText.split(" - ")[0].trim();

                if (!kodeProduksi || kodeProduksi.includes('-- Pilih Batch') || kodeProduksi.includes('Tidak Ditemukan')) {
                    expDateInput.val('');
                    return;
                }

                const bulanChar = kodeProduksi.charAt(1);
                const hari = parseInt(kodeProduksi.substr(2, 2));
                const bulanMap = { A: 0, B: 1, C: 2, D: 3, E: 4, F: 5, G: 6, H: 7, I: 8, J: 9, K: 10, L: 11 };
                
                let kodeBulan = bulanMap[bulanChar];
                if (kodeBulan === undefined) return;

                let now = new Date();
                let tahun = now.getFullYear();
                
                if (kodeBulan < now.getMonth()) tahun++;
                
                let expDate = new Date(tahun, kodeBulan, hari);
                expDate.setMonth(expDate.getMonth() + 7);
                
                let localExp = new Date(expDate.getTime() - (expDate.getTimezoneOffset() * 60000));
                expDateInput.val(localExp.toISOString().slice(0, 10));
            });

            // 5. VALIDASI WARNA INPUT SUHU
            $(document).on('input', 'input[name$="[suhu]"]', function() {
                if ($(this).val().trim() !== '') $(this).addClass('is-valid');
                else $(this).removeClass('is-valid');
            });

            // JALANKAN OTOMATIS JIKA HALAMAN RE-LOAD ADA ISINYA
            if ($('#nama_produk').val()) {
                $('#nama_produk').trigger('change');
            }
        });
    </script>

    <script>
        // LOGIC TAMBAH FORM ACCORDION
        // Sesuaikan index awal dengan jumlah form yang dirender dari PHP (jika ada old data)
        let stuffingIndex = {{ count($oldStuffing) }};

        document.getElementById('btnTambahStuffing').addEventListener('click', function() {
            const firstItem = document.querySelector('.stuffing-item');
            const clone = firstItem.cloneNode(true);

            clone.querySelector('.accordion-button').innerHTML = `<i class="bi bi-clipboard-check me-2 text-warning"></i> Stuffing ${stuffingIndex + 1}`;
            clone.querySelector('.accordion-header').id = `heading${stuffingIndex}`;
            clone.querySelector('.accordion-button').setAttribute('data-bs-target', `#collapse${stuffingIndex}`);
            clone.querySelector('.accordion-collapse').id = `collapse${stuffingIndex}`;

            let timeInputClone = clone.querySelector('#jamMulaiInput');
            if (timeInputClone) timeInputClone.removeAttribute('id');

            clone.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
                else el.value = '';
                
                // Hilangkan class is-invalid bawaan jika ada dari elemen aslinya
                el.classList.remove('is-invalid');

                let name = el.getAttribute('name');
                if (name) el.setAttribute('name', name.replace(/\[\d+\]/, `[${stuffingIndex}]`));
            });

            clone.querySelector('.accordion-collapse').classList.remove('show');
            clone.querySelector('.accordion-button').classList.add('collapsed');
            
            document.getElementById('accordionStuffing').appendChild(clone);
            stuffingIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.btnHapus')) {
                let items = document.querySelectorAll('.stuffing-item');
                if (items.length > 1) e.target.closest('.stuffing-item').remove();
            }
        });
    </script>

    <script>
        // --------------------------------------------------------
        // FIX: AUTO-OPEN ACCORDION SAAT ADA INPUT YANG INVALID
        // --------------------------------------------------------
        document.addEventListener('invalid', function(e) {
            let invalidInput = e.target;
            let accordionPane = invalidInput.closest('.accordion-collapse');
            
            if (accordionPane && !accordionPane.classList.contains('show')) {
                let accordionButton = document.querySelector(`[data-bs-target="#${accordionPane.id}"]`);
                if(accordionButton) accordionButton.classList.remove('collapsed');

                let bsCollapse = new bootstrap.Collapse(accordionPane, { toggle: false });
                bsCollapse.show();

                setTimeout(() => {
                    invalidInput.focus();
                    invalidInput.classList.add('is-invalid');
                }, 350); 
            }
        }, true);
    </script>

@endpush

@push('styles')
    <style>
        .accordion-item { border-radius: 12px !important; overflow: hidden; margin-bottom: 12px; border: 1px solid #e9ecef; }
        .accordion-button { font-weight: 600; background: #f8f9fa; box-shadow: none !important; }
        .accordion-button:not(.collapsed) { background: #fff3cd; color: #000; }
        .accordion-body { background: #fff; padding: 20px; }
        .form-label { font-weight: 600; margin-bottom: 6px; }
        .form-control { border-radius: 10px; min-height: 42px; }
        
        /* Select2 bootstrap 5 styling override untuk form ini */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: calc(2.25rem + 2px) !important;
            border-radius: 10px !important;
        }

        .btnTambah { border-radius: 10px; font-weight: 600; }
        .btnHapus { border-radius: 8px; }
        .card { border-radius: 14px; overflow: hidden; }
    </style>
@endpush