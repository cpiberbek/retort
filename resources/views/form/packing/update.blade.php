@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Update Pemeriksaan Proses Packing
            </h4>

            <form id="pvdcForm" action="{{ route('packing.update_qc', $packing->uuid) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Packing</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                @php $hasDate = !empty($packing->date); @endphp
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ old('date', $packing->date) }}" {{ $hasDate ? 'readonly' : '' }} required>
                                @if($hasDate)
                                <input type="hidden" name="date" value="{{ $packing->date }}">
                                <small class="text-muted">Tanggal sudah tercatat dan tidak dapat diubah</small>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                @php $hasShift = !empty($packing->shift); @endphp
                                <select name="shift" id="shiftInput" class="form-control" {{ $hasShift ? 'disabled' : '' }} required>
                                    <option value="1" {{ $packing->shift == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ $packing->shift == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ $packing->shift == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                                @if($hasShift)
                                <input type="hidden" name="shift" value="{{ $packing->shift }}">
                                <small class="text-muted d-block">Shift sudah tercatat dan tidak dapat diubah</small>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Varian <span class="text-danger">*</span></label>
                                @php $hasProduk = !empty($packing->nama_produk); @endphp
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" {{ $hasProduk ? 'disabled' : '' }} required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ $packing->nama_produk == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($hasProduk)
                                <input type="hidden" name="nama_produk" value="{{ $packing->nama_produk }}">
                                <small class="text-muted d-block">Varian sudah tercatat dan tidak dapat diubah</small>
                                @endif
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
                                @php $hasWaktu = !empty($packing->waktu); @endphp
                                <input type="time" name="waktu" id="timeInput" class="form-control" value="{{ old('waktu', $packing->waktu) }}" {{ $hasWaktu ? 'readonly' : '' }}>
                                @if($hasWaktu)
                                <input type="hidden" name="waktu" value="{{ $packing->waktu }}">
                                @endif
                            </div>

                            <div class="col-md-6 text-center">
                                <label class="form-label fw-bold d-block mb-3">Status Kalibrasi</label>
                                @php $hasKalibrasi = !empty($packing->kalibrasi); @endphp
                                
                                <div class="d-flex flex-wrap justify-content-center" style="gap: 20px;">
                                    <div>
                                        <input type="checkbox" name="kalibrasi" id="kalibrasi_ok" class="hidden-check" value="Ok" 
                                        {{ old('kalibrasi', $packing->kalibrasi) == 'Ok' ? 'checked' : '' }} {{ $hasKalibrasi ? 'disabled' : '' }}>
                                        <label for="kalibrasi_ok" class="custom-check-btn shadow-sm"><span class="check-box"></span><span class="check-text">OK</span></label>
                                    </div>
                                    <div>
                                        <input type="checkbox" name="kalibrasi" id="kalibrasi_tidak_ok" class="hidden-check" value="Tidak Ok" 
                                        {{ old('kalibrasi', $packing->kalibrasi) == 'Tidak Ok' ? 'checked' : '' }} {{ $hasKalibrasi ? 'disabled' : '' }}>
                                        <label for="kalibrasi_tidak_ok" class="custom-check-btn shadow-sm"><span class="check-box"></span><span class="check-text">Tidak OK</span></label>
                                    </div>
                                </div>
                                @if($hasKalibrasi)
                                <input type="hidden" name="kalibrasi" value="{{ $packing->kalibrasi }}">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 file-wrapper">
                                <label class="form-label fw-bold">QR Code (Upload Gambar)</label>
                                @php $hasQrcode = !empty($packing->qrcode) && !in_array($packing->qrcode, ['Ok', 'Tidak Ok']); @endphp
                                @if($hasQrcode)
                                    <div class="mb-2"><a href="{{ asset($packing->qrcode) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-image"></i> Lihat Gambar Saat Ini</a></div>
                                    <small class="text-muted d-block">Kosongkan jika tidak ingin mengubah gambar.</small>
                                @endif
                                <input type="file" name="qrcode" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2 MB | Kosongkan jika tidak diubah</small>
                            </div>

                            <div class="col-md-6 file-wrapper">
                                <label class="form-label fw-bold">Kode Printing (Upload Gambar)</label>
                                @php $hasPrinting = !empty($packing->kode_printing); @endphp
                                @if($hasPrinting)
                                    <div class="mb-2"><a href="{{ asset($packing->kode_printing) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-image"></i> Lihat Gambar Saat Ini</a></div>
                                    <small class="text-muted d-block">Kosongkan jika tidak ingin mengubah gambar.</small>
                                @endif
                                <input type="file" name="kode_printing" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2 MB | Kosongkan jika tidak diubah</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Kode Toples (Batch) <span class="text-danger">*</span></label>
                                @php $hasToples = !empty($packing->kode_toples); @endphp
                                <select name="kode_toples" id="kode_toples" class="form-control" {{ $hasToples ? 'disabled' : '' }} required>
                                    @if($hasToples)
                                        <option value="{{ $packing->kode_toples }}" selected>{{ $packing->kode_toples }}</option>
                                    @else
                                        <option value="">Pilih Varian Terlebih Dahulu</option>
                                    @endif
                                </select>
                                @if($hasToples)
                                <input type="hidden" name="kode_toples" value="{{ $packing->kode_toples }}">
                                @endif
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Suhu</label>
                                @php $hasSuhu = !is_null($packing->suhu) && $packing->suhu !== ''; @endphp
                                <input type="number" step="0.01" name="suhu" class="form-control" value="{{ old('suhu', $packing->suhu) }}" {{ $hasSuhu ? 'readonly' : '' }} min="0">
                                @if($hasSuhu)
                                <input type="hidden" name="suhu" value="{{ $packing->suhu }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Speed Conveyor</label>
                                @php $hasSpeed = !is_null($packing->speed) && $packing->speed !== ''; @endphp
                                <input type="number" step="0.01" name="speed" class="form-control" value="{{ old('speed', $packing->speed) }}" {{ $hasSpeed ? 'readonly' : '' }} min="0">
                                @if($hasSpeed)
                                <input type="hidden" name="speed" value="{{ $packing->speed }}">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kondisi Segel</label>
                                @php $hasSegel = !empty($packing->kondisi_segel); @endphp
                                <select name="kondisi_segel" class="form-control" {{ $hasSegel ? 'disabled' : '' }}>
                                    <option value="">--Pilih--</option>
                                    <option value="OK" {{ $packing->kondisi_segel == 'OK' ? 'selected' : '' }}>OK</option>
                                    <option value="Tidak OK" {{ $packing->kondisi_segel == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                </select>
                                @if($hasSegel)
                                <input type="hidden" name="kondisi_segel" value="{{ $packing->kondisi_segel }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jumlah Produk</label>
                                @php $hasJml = !is_null($packing->jumlah_produk) && $packing->jumlah_produk !== ''; @endphp
                                <input type="number" name="jumlah_produk" class="form-control" value="{{ old('jumlah_produk', $packing->jumlah_produk) }}" {{ $hasJml ? 'readonly' : '' }} min="0">
                                @if($hasJml)
                                <input type="hidden" name="jumlah_produk" value="{{ $packing->jumlah_produk }}">
                                @endif
                            </div>
                        </div>

                        <hr style="border: 1px solid #000;">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Berat Produk per Pcs (gr)</label>
                                @php $hasBeratPcs = !is_null($packing->berat_pcs) && $packing->berat_pcs !== ''; @endphp
                                <input type="number" step="0.01" name="berat_pcs" class="form-control" value="{{ old('berat_pcs', $packing->berat_pcs) }}" {{ $hasBeratPcs ? 'readonly' : '' }} min="0">
                                @if($hasBeratPcs)
                                <input type="hidden" name="berat_pcs" value="{{ $packing->berat_pcs }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Berat Produk per Pack (gr)</label>
                                @php $hasBeratPack = !is_null($packing->berat_pack) && $packing->berat_pack !== ''; @endphp
                                <input type="number" step="0.01" name="berat_pack" class="form-control" value="{{ old('berat_pack', $packing->berat_pack) }}" {{ $hasBeratPack ? 'readonly' : '' }} min="0">
                                @if($hasBeratPack)
                                <input type="hidden" name="berat_pack" value="{{ $packing->berat_pack }}">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== ➕ DATA KEMASAN DINAMIS ===================== --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <strong class="fs-5"><i class="bi bi-box-seam"></i> Data Kemasan Dinamis</strong>
                        
                        @php 
                            $existingKemasan = json_decode($packing->data_kemasan, true) ?? [];
                            $hasKemasan = count($existingKemasan) > 0 && !empty($existingKemasan[0]['jenis_kemasan']);
                        @endphp

                        <button type="button" id="btn-add-kemasan" class="btn btn-success btn-sm fw-bold rounded-pill px-3">
                            <i class="bi bi-plus-circle-fill"></i> Tambah Kemasan
                        </button>
                    </div>
                    <div class="card-body bg-light-subtle">
                        <div id="wrapper-kemasan">
                            @forelse($existingKemasan as $index => $item)
                            <div class="row border rounded p-3 mb-3 bg-white shadow-sm align-items-end item-kemasan">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label fw-bold">Jenis Kemasan</label>
                                    <select name="data_kemasan[{{ $index }}][jenis_kemasan]" class="form-control" {{ $hasKemasan ? 'disabled' : 'required' }}>
                                        <option value="Toples" {{ $item['jenis_kemasan'] == 'Toples' ? 'selected' : '' }}>Toples</option>
                                        <option value="Label" {{ $item['jenis_kemasan'] == 'Label' ? 'selected' : '' }}>Label</option>
                                        <option value="Pouch" {{ $item['jenis_kemasan'] == 'Pouch' ? 'selected' : '' }}>Pouch</option>
                                    </select>
                                    @if($hasKemasan)
                                    <input type="hidden" name="data_kemasan[{{ $index }}][jenis_kemasan]" value="{{ $item['jenis_kemasan'] }}">
                                    @endif
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label fw-bold">No. Lot Kemasan</label>
                                    <input type="text" name="data_kemasan[{{ $index }}][no_lot_kemasan]" class="form-control" value="{{ $item['no_lot_kemasan'] ?? '' }}" {{ $hasKemasan ? 'readonly' : 'required' }}>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label fw-bold">Tanggal Kedatangan</label>
                                    <input type="date" name="data_kemasan[{{ $index }}][tgl_kedatangan]" class="form-control" value="{{ $item['tgl_kedatangan'] ?? '' }}" {{ $hasKemasan ? 'readonly' : '' }}>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label fw-bold">Supplier</label>
                                    <select name="data_kemasan[{{ $index }}][nama_supplier]" class="form-control" {{ $hasKemasan ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->nama_supplier }}" {{ ($item['nama_supplier'] ?? '') == $supplier->nama_supplier ? 'selected' : '' }}>{{ $supplier->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                    @if($hasKemasan)
                                    <input type="hidden" name="data_kemasan[{{ $index }}][nama_supplier]" value="{{ $item['nama_supplier'] ?? '' }}">
                                    @endif
                                </div>
                                <!-- Per-item keterangan removed: use single global keterangan field instead -->
                            </div>
                            @empty
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
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ===================== KETERANGAN GLOBAL ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan (Global)</strong></div>
                    <div class="card-body">
                        @php $hasKetGlobal = !empty($packing->keterangan); @endphp
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" placeholder="Tambahkan keterangan proses packing secara keseluruhan jika ada..." {{ $hasKetGlobal ? 'readonly' : '' }}>{{ old('keterangan', $packing->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Update</button>
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
        // Inisialisasi selectpicker
        if($.fn.selectpicker){
            $('.selectpicker').selectpicker();
        }

        // ===================== LOGIKA REPEATER DATA KEMASAN =====================
        let countIdx = $('.item-kemasan').length || 1;
        
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
            if (!kodeToplesSelect.is('select') || kodeToplesSelect.prop('disabled')) return;

            if (!namaProduk) {
                kodeToplesSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>').prop('disabled', true);
                return;
            }

            kodeToplesSelect.prop('disabled', false).html('<option value="">Mencari Batch...</option>');

            let url = "{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}";
            url = url.replace('__PRODUK__', encodeURIComponent(namaProduk));

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    kodeToplesSelect.html('<option value="">-- Pilih Kode Toples (Batch) --</option>');
                    if (!Array.isArray(data) || data.length === 0) {
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

        

        if (namaProdukSelect.val() && kodeToplesSelect.is('select') && !kodeToplesSelect.prop('disabled')) {
            let oldBatch = "{{ old('kode_toples', $packing->kode_toples ?? '') }}";
            loadBatches(namaProdukSelect.val(), oldBatch);
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
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
        if (!ok) { e.preventDefault(); alert('Periksa ukuran file, maksimal 2 MB.'); }
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