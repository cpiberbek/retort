@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Proses Cartoning
            </h4>
            
            {{-- ===================== 1. GLOBAL ERROR ALERT ===================== --}}
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
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
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

                        <!-- <div class="alert alert-danger py-2 px-3 mb-4" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>
                            <i class="bi bi-check-circle text-success"></i> Centang = <b>OK</b>, Kosong = <b>Tidak OK</b>.
                        </div> -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Waktu</label>
                                <input type="time" id="timeInput" name="waktu" class="form-control" value="{{ old('waktu') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kalibrasi</label>
                                <select name="kalibrasi" class="form-control">
                                    <option value="">--Pilih--</option>
                                    <option value="Ok" {{ old('kalibrasi') == 'Ok' ? 'selected' : '' }}>Ok</option>
                                    <option value="Tidak Ok" {{ old('kalibrasi') == 'Tidak Ok' ? 'selected' : '' }}>Tidak Ok</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">QR Code</label>
                                <select name="qrcode" class="form-control">
                                    <option value="">--Pilih--</option>
                                    <option value="Ok" {{ old('qrcode') == 'Ok' ? 'selected' : '' }}>Ok</option>
                                    <option value="Tidak Ok" {{ old('qrcode') == 'Tidak Ok' ? 'selected' : '' }}>Tidak Ok</option>
                                </select>
                            </div>

                            <div class="col-md-6 file-wrapper">
                                <label class="form-label fw-bold">Kode Printing (Upload Gambar)</label>
                                <input type="file" name="kode_printing" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2 MB</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Toples</label>
                                <input type="text" name="kode_toples" class="form-control" value="{{ old('kode_toples') }}">
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
                                <label class="form-label fw-bold">Speed</label>
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
                                <label class="form-label fw-bold">Berat Toples</label>
                                <input type="number" step="0.01" name="berat_toples" class="form-control" value="{{ old('berat_toples') }}" min="0">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Berat Pouch</label>
                                <input type="number" step="0.01" name="berat_pouch" class="form-control" value="{{ old('berat_pouch') }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">No. Lot</label>
                                <input type="text" name="no_lot" class="form-control" value="{{ old('no_lot') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Kedatangan</label>
                                <input type="date" name="tgl_kedatangan" class="form-control" value="{{ old('tgl_kedatangan') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Supplier</label>
                                <select name="nama_supplier" class="form-control">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->nama_supplier }}" {{ old('nama_supplier') == $supplier->nama_supplier ? 'selected' : '' }}>
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
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $data->keterangan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- ===================== BUTTON ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
                    </button>

                    <a href="{{ route('packing.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- ===================== SCRIPT ===================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });

    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");
        const timeInput = document.getElementById("timeInput");

        // Generate Waktu Sekarang
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const hh = now.getHours();
        let min = '00';

        // LOGIKA PENTING: Hanya isi otomatis jika input KOSONG
        // Jika validasi gagal, Laravel sudah mengisi value dengan data lama (old)
        // Jadi kita tidak boleh menimpanya dengan waktu sekarang
        
        if (!dateInput.value) {
            dateInput.value = `${yyyy}-${mm}-${dd}`;
        }
        
        if (!timeInput.value) {
            timeInput.value = `${hh}:${min}`;
        }

        if (!shiftInput.value) {
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
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .file-error {
        color: #dc3545;
    }
</style>

@endsection