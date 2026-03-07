@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pemeriksaan Proses Cartoning
            </h4>

            <form id="pvdcForm" action="{{ route('packing.edit_spv', $packing->uuid) }}" method="POST" enctype="multipart/form-packing">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas packing Packing</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                value="{{ $packing->date }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ $packing->shift == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ $packing->shift == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ $packing->shift == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ $packing->nama_produk == $produk->nama_produk ? 'selected' : '' }}>
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

                        <div class="alert alert-danger py-2 px-3 mb-4" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>
                            <i class="bi bi-check-circle text-success"></i> Centang = <b>OK</b>, Kosong = <b>Tidak OK</b>.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Waktu</label>
                                <input type="time" name="waktu" id="timeInput" class="form-control"
                                value="{{ $packing->waktu }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kalibrasi</label>
                                <select name="kalibrasi" class="form-control">
                                    <option value="Ok" {{ $packing->kalibrasi == 'Ok' ? 'selected' : '' }}>Ok</option>
                                    <option value="Tidak Ok" {{ $packing->kalibrasi == 'Tidak Ok' ? 'selected' : '' }}>Tidak Ok</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">QR Code</label>
                                <select name="qrcode" class="form-control">
                                    <option value="Ok" {{ $packing->qrcode == 'Ok' ? 'selected' : '' }}>Ok</option>
                                    <option value="Tidak Ok" {{ $packing->qrcode == 'Tidak Ok' ? 'selected' : '' }}>Tidak Ok</option>
                                </select>
                            </div>
                            <div class="col-md-6 file-wrapper">
                                <label class="form-label fw-bold">Kode Printing (Upload Gambar)</label>
                                <input type="file" name="kode_printing" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2 MB | Kosongkan jika tidak diubah</small>

                                @if(!empty($packing->kode_printing))
                                <div class="mt-2">
                                    <a href="{{ asset($packing->kode_printing) }}" target="_blank" class="text-primary text-decoration-underline">
                                        Lihat Gambar Saat Ini
                                    </a>
                                </div>
                                @else
                                <p class="text-muted mt-2">Tidak ada gambar</p>
                                @endif
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Toples</label>
                                <input type="text" name="kode_toples" class="form-control"
                                value="{{ $packing->kode_toples }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Karton</label>
                                <input type="text" name="kode_karton" class="form-control"
                                value="{{ $packing->kode_karton }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Suhu</label>
                                <input type="number" step="0.01" name="suhu" class="form-control"
                                value="{{ $packing->suhu }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Speed</label>
                                <input type="number" step="0.01" name="speed" class="form-control"
                                value="{{ $packing->speed }}" min="0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kondisi Segel</label>
                                <select name="kondisi_segel" class="form-control">
                                    <option value="OK" {{ $packing->kondisi_segel == 'OK' ? 'selected' : '' }}>OK</option>
                                    <option value="Tidak OK" {{ $packing->kondisi_segel == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Berat Toples</label>
                                <input type="number" step="0.01" name="berat_toples" class="form-control"
                                value="{{ $packing->berat_toples }}" min="0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Berat Pouch</label>
                                <input type="number" step="0.01" name="berat_pouch" class="form-control"
                                value="{{ $packing->berat_pouch }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">No. Lot</label>
                                <input type="text" name="no_lot" class="form-control"
                                value="{{ $packing->no_lot }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Kedatangan</label>
                                <input type="date" name="tgl_kedatangan" class="form-control"
                                value="{{ $packing->tgl_kedatangan }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Supplier</label>
                                <select name="nama_supplier" class="form-control">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->nama_supplier }}"
                                        {{ $packing->nama_supplier == $supplier->nama_supplier ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3">{{ $packing->keterangan }}</textarea>
                    </div>
                </div>

                {{-- ===================== BUTTON ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update
                    </button>

                    <a href="{{ route('packing.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- == SCRIPT SAMA == --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });

    // TIDAK override waktu/tanggal/shift jika sudah ada value dari database
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");
        const timeInput = document.getElementById("timeInput");

        if (!dateInput.value) {
            const now = new Date();
            dateInput.value = now.toISOString().split('T')[0];
        }

        if (!timeInput.value) {
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            timeInput.value = `${hh}:00`;
        }

        // Shift otomatis hanya jika kosong
        if (!shiftInput.value) {
            const hh = new Date().getHours();
            if (hh >= 7 && hh < 15) shiftInput.value = "1";
            else if (hh >= 15 && hh < 23) shiftInput.value = "2";
            else shiftInput.value = "3";
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

    $(document).on('change', 'input[type="file"]', function () {
        validateFile(this);
    });

    $('#pvdcForm').on('submit', function (e) {
        let ok = true;

        $('input[type="file"]').each(function () {
            if (!validateFile(this)) ok = false;
        });

        if (!ok) e.preventDefault();
    });
</script>

<style>
    .is-invalid { border-color: #dc3545 !important; }
    .file-error { color: #dc3545; }
</style>

@endsection
