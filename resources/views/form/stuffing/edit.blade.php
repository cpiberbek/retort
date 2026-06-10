@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Edit Pemeriksaan Stuffing Sosis Retort (SPV)
            </h4>

            @if ($errors->any())
                <div class="alert alert-danger mb-4 shadow-sm" role="alert">
                    <strong class="d-block mb-2"><i class="bi bi-exclamation-triangle-fill"></i> Terjadi Kesalahan!</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="stuffingForm" action="{{ route('stuffing.edit_spv', $stuffing->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS (EDITABLE UNTUK SPV) ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Stuffing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ old('date', $stuffing->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="1" {{ old('shift', $stuffing->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $stuffing->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $stuffing->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $stuffing->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <select name="kode_produksi" id="kode_produksi" class="form-control" required>
                                    <option value="{{ $stuffing->kode_produksi }}" selected>{{ $stuffing->mincing->kode_produksi ?? '-' }}</option>
                                </select>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="exp_date" id="exp_date" class="form-control" value="{{ old('exp_date', $stuffing->exp_date) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== DATA STUFFING ACCORDION ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Data Stuffing</strong>
                        <button type="button" class="btn btn-dark btn-sm btnTambah" id="btnTambahStuffing">
                            <i class="bi bi-plus-circle"></i> Tambah Data Baru
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="accordionStuffing">
                            
                            @php
                                $oldStuffing = old('stuffing', is_array($stuffing->data_stuffing) ? $stuffing->data_stuffing : [[]]);
                                $originalCount = is_array($stuffing->data_stuffing) ? count($stuffing->data_stuffing) : 0;
                            @endphp

                            @foreach ($oldStuffing as $index => $item)
                                @php $isExisting = $index < $originalCount; @endphp
                                <div class="accordion-item stuffing-item">
                                    <h5 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                                            <i class="bi bi-clipboard-check me-2 text-warning"></i>
                                            Stuffing {{ $index + 1 }} {!! $isExisting ? '<span class="badge bg-primary ms-2">Data Existing</span>' : '<span class="badge bg-success ms-2">Baru</span>' !!}
                                        </button>
                                    </h5>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#accordionStuffing">
                                        <div class="accordion-body">
                                            
                                            <div class="text-end mb-3">
                                                <button type="button" class="btn btn-outline-danger btn-sm btnHapus"><i class="bi bi-trash"></i></button>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nama Mesin</label>
                                                <select name="stuffing[{{ $index }}][kode_mesin]" class="form-control" required>
                                                    <option value="">-- Pilih Mesin --</option>
                                                    @foreach($mesins as $m)
                                                        <option value="{{ $m->nama_mesin }}" {{ ($item['kode_mesin'] ?? '') == $m->nama_mesin ? 'selected' : '' }}>{{ $m->nama_mesin }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Jam Mulai</label>
                                                <input type="time" name="stuffing[{{ $index }}][jam_mulai]" class="form-control" value="{{ $item['jam_mulai'] ?? '' }}" required>
                                            </div>

                                            <hr><h6 class="fw-bold text-primary">Parameter Adonan</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Suhu (°C)</label>
                                                <input type="number" step="0.01" name="stuffing[{{ $index }}][suhu]" class="form-control" value="{{ $item['suhu'] ?? '' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Sensori</label>
                                                <select name="stuffing[{{ $index }}][sensori]" class="form-control">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="OK" {{ ($item['sensori'] ?? '') == 'OK' ? 'selected' : '' }}>OK</option>
                                                    <option value="Tidak OK" {{ ($item['sensori'] ?? '') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                </select>
                                            </div>

                                            <hr><h6 class="fw-bold text-primary">Parameter Stuffing</h6>
                                            @php
                                                $fields = ['kecepatan_stuffing' => 'Kecepatan Stuffing (/mnt)', 'panjang_pcs' => 'Panjang per pcs (cm)', 'berat_pcs' => 'Berat per pcs (gr)', 'diameter_klip' => 'Diameter Klip (mm)', 'lebar_cassing' => 'Lebar Cassing (mm)'];
                                                $selects = ['kebersihan_seal' => 'Kebersihan Ujung Seal', 'kekuatan_seal' => 'Kekuatan Seal', 'print_kode' => 'Print Kode Production'];
                                            @endphp

                                            @foreach($fields as $key => $label)
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $label }}</label>
                                                    <input type="number" step="0.01" name="stuffing[{{ $index }}][{{ $key }}]" class="form-control" value="{{ $item[$key] ?? '' }}">
                                                </div>
                                            @endforeach

                                            @foreach($selects as $key => $label)
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $label }}</label>
                                                    <select name="stuffing[{{ $index }}][{{ $key }}]" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="OK" {{ ($item[$key] ?? '') == 'OK' ? 'selected' : '' }}>OK</option>
                                                        <option value="Tidak OK" {{ ($item[$key] ?? '') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                    </select>
                                                </div>
                                            @endforeach

                                            <div class="card mt-4">
                                                <div class="card-header bg-light"><strong>Catatan</strong></div>
                                                <div class="card-body">
                                                    <textarea name="stuffing[{{ $index }}][catatan]" class="form-control" rows="3">{{ $item['catatan'] ?? '' }}</textarea>
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
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Update & Simpan</button>
                    <a href="{{ route('stuffing.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Template dinamis untuk data tambahan --}}
<template id="stuffingTemplate">
    <div class="accordion-item stuffing-item">
        <h5 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{INDEX}">
                <i class="bi bi-clipboard-check me-2 text-warning"></i> Stuffing {NOMOR} <span class="badge bg-success ms-2">Baru</span>
            </button>
        </h5>
        <div id="collapse{INDEX}" class="accordion-collapse collapse show" data-bs-parent="#accordionStuffing">
            <div class="accordion-body">
                <div class="text-end mb-3">
                    <button type="button" class="btn btn-outline-danger btn-sm btnHapus"><i class="bi bi-trash"></i></button>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Mesin</label>
                    <select name="stuffing[{INDEX}][kode_mesin]" class="form-control" required>
                        <option value="">-- Pilih Mesin --</option>
                        @foreach($mesins as $m)
                            <option value="{{ $m->nama_mesin }}">{{ $m->nama_mesin }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="stuffing[{INDEX}][jam_mulai]" class="form-control" required>
                </div>

                <hr><h6 class="fw-bold text-primary">Parameter Adonan</h6>
                <div class="mb-3">
                    <label class="form-label">Suhu (°C)</label>
                    <input type="number" step="0.01" name="stuffing[{INDEX}][suhu]" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sensori</label>
                    <select name="stuffing[{INDEX}][sensori]" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="OK">OK</option>
                        <option value="Tidak OK">Tidak OK</option>
                    </select>
                </div>

                <hr><h6 class="fw-bold text-primary">Parameter Stuffing</h6>
                @foreach($fields as $key => $label)
                    <div class="mb-3">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" step="0.01" name="stuffing[{INDEX}][{{ $key }}]" class="form-control">
                    </div>
                @endforeach

                @foreach($selects as $key => $label)
                    <div class="mb-3">
                        <label class="form-label">{{ $label }}</label>
                        <select name="stuffing[{INDEX}][{{ $key }}]" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="OK">OK</option>
                            <option value="Tidak OK">Tidak OK</option>
                        </select>
                    </div>
                @endforeach

                <div class="card mt-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="stuffing[{INDEX}][catatan]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function() {
        if ($.fn.selectpicker) { $('.selectpicker').selectpicker(); }

        const batchSelect = $('#kode_produksi');
        const expDateInput = $('#exp_date');
        const namaProdukInput = $('#nama_produk');

        function loadBatches(namaProduk, oldBatch = '') {
            if (!namaProduk) return;
            batchSelect.prop('disabled', false);
            batchSelect.html('<option value="">Mencari Batch...</option>');

            let url = "{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}";
            url = url.replace('__PRODUK__', encodeURIComponent(namaProduk));

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    batchSelect.html('<option value="">-- Pilih Batch --</option>');
                    if (!data || data.length === 0) {
                        batchSelect.html('<option value="">Batch Tidak Ditemukan</option>');
                        return;
                    }
                    data.forEach(function(batch) {
                        let isSelected = (oldBatch === batch.uuid || oldBatch === batch.kode_produksi) ? 'selected' : '';
                        batchSelect.append(`<option value="${batch.uuid}" ${isSelected}>${batch.kode_produksi}</option>`);
                    });
                    if (oldBatch) { batchSelect.trigger('change'); }
                }
            });
        }

        $(document).on('change', '#nama_produk', function() { loadBatches($(this).val()); });
        if (namaProdukInput.val() && !batchSelect.val()) {
            let oldBatch = "{{ old('kode_produksi', $stuffing->kode_produksi ?? '') }}";
            loadBatches(namaProdukInput.val(), oldBatch);
        }

        $(document).on('change', '#kode_produksi', function() {
            let selectedText = $(this).find("option:selected").text().split(" - ")[0].trim();
            if (!selectedText || selectedText.includes('-- Pilih Batch')) return;

            const bulanChar = selectedText.charAt(1);
            const hari = parseInt(selectedText.substr(2, 2));
            const bulanMap = { A: 0, B: 1, C: 2, D: 3, E: 4, F: 5, G: 6, H: 7, I: 8, J: 9, K: 10, L: 11 };
            let kodeBulan = bulanMap[bulanChar];
            if (kodeBulan === undefined) return;

            let now = new Date();
            let tahun = now.getFullYear();
            if (kodeBulan < now.getMonth()) tahun++;
            
            let expDate = new Date(tahun, kodeBulan, hari);
            expDate.setMonth(expDate.getMonth() + 7);
            expDateInput.val(new Date(expDate.getTime() - (expDate.getTimezoneOffset() * 60000)).toISOString().slice(0, 10));
        });
    });

    let stuffingIndex = {{ count($oldStuffing) }};
    document.getElementById('btnTambahStuffing').addEventListener('click', function() {
        let template = document.getElementById('stuffingTemplate').innerHTML;
        let html = template.replace(/{INDEX}/g, stuffingIndex).replace(/{NOMOR}/g, stuffingIndex + 1);
        let container = document.createElement('div');
        container.innerHTML = html;
        document.getElementById('accordionStuffing').appendChild(container.firstElementChild);
        stuffingIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btnHapus')) { e.target.closest('.stuffing-item').remove(); }
    });
</script>

<style>
    .accordion-item { border-radius: 12px !important; overflow: hidden; margin-bottom: 12px; border: 1px solid #e9ecef; }
    .accordion-button { font-weight: 600; background: #f8f9fa; box-shadow: none !important; }
    .accordion-button:not(.collapsed) { background: #fff3cd; color: #000; }
    .accordion-body { background: #fff; padding: 20px; }
    .form-label { font-weight: 600; margin-bottom: 6px; }
    .form-control { border-radius: 10px; min-height: 42px; }
    .card { border-radius: 14px; overflow: hidden; }
</style>
@endsection