@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Pemeriksaan Proses Packing</h4>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="pvdcForm" action="{{ route('packing.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Packing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ old('date') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Varian <span class="text-danger">*</span></label>
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk') == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== PEMERIKSAAN ===================== --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <strong>Pemeriksaan Proses</strong>
                    </div>
                    <div class="card-body">

                        <div class="row mb-4 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Waktu</label>
                                <input type="time" id="timeInput" name="waktu" class="form-control" value="{{ old('waktu') }}">
                            </div>

                            <div class="col-md-6 text-center">
                                <label class="form-label fw-bold d-block mb-3">Status Kalibrasi</label>
                                <div class="d-flex flex-wrap justify-content-center" style="gap: 20px;">
                                    <div>
                                        <input type="checkbox" name="kalibrasi" id="kalibrasi_ok" class="hidden-check" value="Ok" {{ old('kalibrasi') == 'Ok' ? 'checked' : '' }}>
                                        <label for="kalibrasi_ok" class="custom-check-btn shadow-sm">
                                            <span class="check-box"></span><span class="check-text">OK</span>
                                        </label>
                                    </div>
                                    <div>
                                        <input type="checkbox" name="kalibrasi" id="kalibrasi_tidak_ok" class="hidden-check" value="Tidak Ok" {{ old('kalibrasi') == 'Tidak Ok' ? 'checked' : '' }}>
                                        <label for="kalibrasi_tidak_ok" class="custom-check-btn shadow-sm">
                                            <span class="check-box"></span><span class="check-text">Tidak OK</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 file-wrapper">
                                <label class="form-label fw-bold">QR Code (Upload Gambar)</label>
                                <input type="file" name="qrcode" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2 MB</small>
                            </div>

                            <div class="col-md-6 file-wrapper">
                                <label class="form-label fw-bold">Kode Printing (Upload Gambar)</label>
                                <input type="file" name="kode_printing" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2 MB</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Toples (Batch) <span class="text-danger">*</span></label>
                                <select id="kode_toples" name="kode_toples" class="form-control" disabled required>
                                    <option value="">Pilih Varian terlebih dahulu</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Karton</label>
                                <input type="text" name="kode_karton" class="form-control" value="{{ old('kode_karton') }}">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Suhu</label>
                                <input type="number" name="suhu" class="form-control" step="0.01" value="{{ old('suhu') }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Speed Conveyor</label>
                                <input type="number" step="0.01" name="speed" class="form-control" value="{{ old('speed') }}" min="0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kondisi Segel</label>
                                <select name="kondisi_segel" class="form-control">
                                    <option value="">--Pilih--</option>
                                    <option value="OK" {{ old('kondisi_segel') == 'OK' ? 'selected' : '' }}>OK</option>
                                    <option value="Tidak OK" {{ old('kondisi_segel') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jumlah Produk</label>
                                <input type="number" name="jumlah_produk" class="form-control" value="{{ old('jumlah_produk') }}" min="0">
                            </div>
                        </div>

                        <hr style="border: 1px solid #000;">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Berat Produk per Pcs (gr)</label>
                                <input type="number" step="0.01" name="berat_pcs" class="form-control" value="{{ old('berat_pcs') }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Berat Produk per Pack (gr)</label>
                                <input type="number" step="0.01" name="berat_pack" class="form-control" value="{{ old('berat_pack') }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== ➕ DATA KEMASAN DINAMIS ===================== --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <strong class="fs-5"><i class="bi bi-box-seam"></i> Data Kemasan Dinamis</strong>
                        <button type="button" id="btn-add-kemasan" class="btn btn-success btn-sm fw-bold rounded-pill px-3">
                            <i class="bi bi-plus-circle-fill"></i> Tambah Kemasan
                        </button>
                    </div>
                    <div class="card-body bg-light-subtle">
                        <div id="wrapper-kemasan">
                            <div class="row border rounded p-3 mb-3 bg-white shadow-sm align-items-end item-kemasan">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label fw-bold">Jenis Kemasan</label>
                                    <select name="data_kemasan[0][jenis_kemasan]" class="form-control" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="Toples">Toples</option>
                                        <option value="Label">Label</option>
                                        <option value="Pouch">Pouch</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label fw-bold">No. Lot Kemasan</label>
                                    <input type="text" name="data_kemasan[0][no_lot_kemasan]" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label fw-bold">Tanggal Kedatangan</label>
                                    <input type="date" name="data_kemasan[0][tgl_kedatangan]" class="form-control">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label fw-bold">Supplier</label>
                                    <select name="data_kemasan[0][nama_supplier]" class="form-control">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->nama_supplier }}">{{ $supplier->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 mb-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-danger btn-remove-kemasan w-100" disabled><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== KETERANGAN GLOBAL ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan (Global)</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Tambahkan keterangan proses packing secara keseluruhan jika ada...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('packing.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        if($.fn.selectpicker){
            $('.selectpicker').selectpicker();
        }

        // ===================== LOGIKA REPEATER DATA KEMASAN =====================
        let countIdx = 1;
        $('#btn-add-kemasan').on('click', function(){
            let htmlRow = `
            <div class="row border rounded p-3 mb-3 bg-white shadow-sm align-items-end item-kemasan">
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">Jenis Kemasan</label>
                    <select name="data_kemasan[${countIdx}][jenis_kemasan]" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="Toples">Toples</option>
                        <option value="Label">Label</option>
                        <option value="Pouch">Pouch</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">No. Lot Kemasan</label>
                    <input type="text" name="data_kemasan[${countIdx}][no_lot_kemasan]" class="form-control" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">Tanggal Kedatangan</label>
                    <input type="date" name="data_kemasan[${countIdx}][tgl_kedatangan]" class="form-control">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label fw-bold">Supplier</label>
                    <select name="data_kemasan[${countIdx}][nama_supplier]" class="form-control">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->nama_supplier }}">{{ $supplier->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 mb-2 d-flex justify-content-center">
                    <button type="button" class="btn btn-danger btn-remove-kemasan w-100"><i class="bi bi-trash"></i></button>
                </div>
            </div>`;
            $('#wrapper-kemasan').append(htmlRow);
            countIdx++;
            toggleRemoveButton();
        });

        $(document).on('click', '.btn-remove-kemasan', function(){
            $(this).closest('.item-kemasan').remove();
            toggleRemoveButton();
        });

        function toggleRemoveButton() {
            if($('.item-kemasan').length > 1) {
                $('.btn-remove-kemasan').prop('disabled', false);
            } else {
                $('.btn-remove-kemasan').prop('disabled', true);
            }
        }

        // ===================== LOGIKA AJAX BATCH MINCING =====================
        const namaProdukSelect = $('#nama_produk');
        const kodeToplesSelect = $('#kode_toples');

        function loadBatches(namaProduk, oldBatch = '') {
            if (!namaProduk) {
                kodeToplesSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>').prop('disabled', true);
                return;
            }

            kodeToplesSelect.prop('disabled', false).html('<option value="">Mencari Batch...</option>');

            $.ajax({
                url: '/lookup/batch/' + encodeURIComponent(namaProduk),
                type: 'GET',
                success: function(data) {
                    kodeToplesSelect.html('<option value="">-- Pilih Kode Toples (Batch) --</option>');
                    if (!data || data.length === 0) {
                        kodeToplesSelect.html('<option value="">Batch Tidak Ditemukan</option>').prop('disabled', true);
                        return;
                    }

                    data.forEach(function(batch) {
                        let isSelected = (oldBatch === batch.kode_produksi) ? 'selected' : '';
                        kodeToplesSelect.append(`<option value="${batch.kode_produksi}" data-uuid="${batch.uuid}" ${isSelected}>${batch.kode_produksi}</option>`);
                    });
                },
                error: function() {
                    kodeToplesSelect.html('<option value="">Gagal memuat data</option>');
                }
            });
        }

        namaProdukSelect.on('change', function() {
            loadBatches($(this).val());
        });

        if (namaProdukSelect.val()) {
            let oldBatch = "{{ old('kode_toples', '') }}";
            loadBatches(namaProdukSelect.val(), oldBatch);
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.getElementById("timeInput");
        const shiftInput = document.getElementById("shiftInput");

        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const hh = now.getHours();
        let min = '00';

        if (!dateInput.value) dateInput.value = `${yyyy}-${mm}-${dd}`;
        if (!timeInput.value) timeInput.value = `${String(hh).padStart(2, '0')}:${min}`;
        if (!shiftInput.value) {
            if (hh >= 7 && hh < 15) shiftInput.value = "1";
            else if (hh >= 15 && hh < 23) shiftInput.value = "2";
            else shiftInput.value = "3";
        }

        const checkOk = document.getElementById('kalibrasi_ok');
        const checkTidakOk = document.getElementById('kalibrasi_tidak_ok');
        if (checkOk && checkTidakOk) {
            checkOk.addEventListener('change', function() { if (this.checked) checkTidakOk.checked = false; });
            checkTidakOk.addEventListener('change', function() { if (this.checked) checkOk.checked = false; });
        }
    });

    function validateFile(input) {
        const file = input.files[0];
        const max = 2 * 1024 * 1024;
        const wrap = $(input).closest('.file-wrapper');
        wrap.find('.file-error').remove();

        if (file && file.size > max) {
            $(input).addClass('is-invalid');
            wrap.append('<div class="text-danger file-error mt-1" style="font-size:0.8rem;">Ukuran file maksimal 2 MB</div>');
            return false;
        }
        $(input).removeClass('is-invalid');
        return true;
    }
    
    $(document).on('change', 'input[type="file"]', function () { validateFile(this); });
    $('#pvdcForm').on('submit', function (e) {
        let ok = true;
        $('input[type="file"]').each(function () { if (!validateFile(this)) ok = false; });
        if (!ok) e.preventDefault();
    });
</script>
@endpush

<style>
    .is-invalid { border-color: #dc3545 !important; }
    .file-error { color: #dc3545; }
    .hidden-check { display: none; }
    .custom-check-btn {
        display: inline-flex; align-items: center; padding: 8px 16px; border: 1px solid #ced4da; border-radius: 6px; cursor: pointer; background-color: #fff; color: #212529; transition: all 0.2s ease-in-out; min-width: 120px;
    }
    .check-box { width: 20px; height: 20px; border: 2px solid #ced4da; border-radius: 4px; margin-right: 12px; display: inline-flex; align-items: center; justify-content: center; background-color: #fff; transition: all 0.2s ease-in-out; }
    .check-text { font-weight: 500; font-size: 1rem; }
    .hidden-check:checked + .custom-check-btn { background-color: #0ea5e9; border-color: #0ea5e9; color: #fff; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3); }
    .hidden-check:checked + .custom-check-btn .check-box { border-color: #fff; background-color: #fff; }
    .hidden-check:checked + .custom-check-btn .check-box::after { content: ""; width: 5px; height: 10px; border: solid #0ea5e9; border-width: 0 2.5px 2.5px 0; transform: rotate(45deg); margin-bottom: 2px; }
    .hidden-check:not(:checked):not(:disabled) + .custom-check-btn:hover { border-color: #0ea5e9; background-color: #f0f9ff; }
    .hidden-check:disabled + .custom-check-btn { cursor: not-allowed; opacity: 0.6; }
</style>
@endsection