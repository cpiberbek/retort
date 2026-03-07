@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pemeriksaan Proses Cartoning
            </h4>

            <form id="pvdcForm" action="{{ route('packing.update_qc', $packing->uuid) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Packing</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            {{-- Tanggal --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                @php $hasDate = !empty($packing->date); @endphp
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ old('date', $packing->date) }}" {{ $hasDate ? 'readonly' : '' }} required>
                                @if($hasDate)
                                {{-- disabled/read-only fields need a hidden input so value tetap terkirim --}}
                                <input type="hidden" name="date" value="{{ $packing->date }}">
                                <small class="text-muted">Tanggal sudah tercatat dan tidak dapat diubah</small>
                                @endif
                            </div>

                            {{-- Shift --}}
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                @php $hasShift = !empty($packing->shift); @endphp
                                <select name="shift" id="shiftInput" class="form-control" {{ $hasShift ? 'disabled' : '' }} required>
                                    <option value="">-- Pilih Shift --</option>
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
                            {{-- Nama Produk --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                @php $hasProduk = !empty($packing->nama_produk); @endphp
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" {{ $hasProduk ? 'disabled' : '' }} required>
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

                        <div class="alert alert-danger py-2 px-3 mb-4" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>
                            <i class="bi bi-check-circle text-success"></i> Centang = <b>OK</b>, Kosong = <b>Tidak OK</b>.
                        </div>

                        {{-- Waktu & Kalibrasi --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Waktu</label>
                                @php $hasWaktu = !empty($packing->waktu); @endphp
                                <input type="time" name="waktu" id="timeInput" class="form-control" value="{{ old('waktu', $packing->waktu) }}" {{ $hasWaktu ? 'readonly' : '' }}>
                                @if($hasWaktu)
                                <input type="hidden" name="waktu" value="{{ $packing->waktu }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kalibrasi</label>
                                @php $hasKalibrasi = !empty($packing->kalibrasi); @endphp
                                <select name="kalibrasi" class="form-control" {{ $hasKalibrasi ? 'disabled' : '' }}>
                                    <option value="">--Pilih--</option>
                                    <option value="Ok" {{ $packing->kalibrasi == 'Ok' ? 'selected' : '' }}>Ok</option>
                                    <option value="Tidak Ok" {{ $packing->kalibrasi == 'Tidak Ok' ? 'selected' : '' }}>Tidak Ok</option>
                                </select>
                                @if($hasKalibrasi)
                                <input type="hidden" name="kalibrasi" value="{{ $packing->kalibrasi }}">
                                @endif
                            </div>
                        </div>

                        {{-- QR Code & Kode Printing (file) --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">QR Code</label>
                                @php $hasQr = !empty($packing->qrcode); @endphp
                                <select name="qrcode" class="form-control" {{ $hasQr ? 'disabled' : '' }}>
                                    <option value="">--Pilih--</option>
                                    <option value="Ok" {{ $packing->qrcode == 'Ok' ? 'selected' : '' }}>Ok</option>
                                    <option value="Tidak Ok" {{ $packing->qrcode == 'Tidak Ok' ? 'selected' : '' }}>Tidak Ok</option>
                                </select>
                                @if($hasQr)
                                <input type="hidden" name="qrcode" value="{{ $packing->qrcode }}">
                                @endif
                            </div>

                            <div class="col-md-6 file-wrapper">
                                <label class="form-label fw-bold">Kode Printing (Upload Gambar)</label>

                                @if(!empty($packing->kode_printing))
                                {{-- Jika sudah ada gambar: tampilkan link dan hidden input, sembunyikan upload --}}
                                <div>
                                    <a href="{{ asset($packing->kode_printing) }}" target="_blank" class="text-primary text-decoration-underline">
                                        Lihat Gambar Saat Ini
                                    </a>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Gambar sudah ada dan tidak dapat diubah lewat form ini.</small>
                                </div>
                                {{-- kirim path lama agar backend tahu tetap memakai file yang sama --}}
                                <input type="hidden" name="kode_printing" value="{{ $packing->kode_printing }}">
                                @else
                                {{-- belum ada gambar: tampilkan input file --}}
                                <input type="file" name="kode_printing" class="form-control" accept="image/*">
                                @endif
                            </div>
                        </div>

                        {{-- Kode Toples & Kode Karton --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Toples</label>
                                @php $hasToples = !empty($packing->kode_toples); @endphp
                                <input type="text" name="kode_toples" class="form-control" value="{{ old('kode_toples', $packing->kode_toples) }}" {{ $hasToples ? 'readonly' : '' }}>
                                @if($hasToples)
                                <input type="hidden" name="kode_toples" value="{{ $packing->kode_toples }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Karton</label>
                                @php $hasKarton = !empty($packing->kode_karton); @endphp
                                <input type="text" name="kode_karton" class="form-control" value="{{ old('kode_karton', $packing->kode_karton) }}" {{ $hasKarton ? 'readonly' : '' }}>
                                @if($hasKarton)
                                <input type="hidden" name="kode_karton" value="{{ $packing->kode_karton }}">
                                @endif
                            </div>
                        </div>

                        {{-- Suhu & Speed --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Suhu</label>
                                @php $hasSuhu = !is_null($packing->suhu) && $packing->suhu !== ''; @endphp
                                <input type="number" step="0.01" name="suhu" class="form-control" value="{{ old('suhu', $packing->suhu) }}" {{ $hasSuhu ? 'readonly' : '' }}>
                                @if($hasSuhu)
                                <input type="hidden" name="suhu" value="{{ $packing->suhu }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Speed</label>
                                @php $hasSpeed = !is_null($packing->speed) && $packing->speed !== ''; @endphp
                                <input type="number" step="0.01" name="speed" class="form-control" value="{{ old('speed', $packing->speed) }}" {{ $hasSpeed ? 'readonly' : '' }}>
                                @if($hasSpeed)
                                <input type="hidden" name="speed" value="{{ $packing->speed }}">
                                @endif
                            </div>
                        </div>

                        {{-- Kondisi Segel & Berat Toples --}}
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
                                <label class="form-label fw-bold">Berat Toples</label>
                                @php $hasBeratToples = !is_null($packing->berat_toples) && $packing->berat_toples !== ''; @endphp
                                <input type="number" step="0.01" name="berat_toples" class="form-control" value="{{ old('berat_toples', $packing->berat_toples) }}" {{ $hasBeratToples ? 'readonly' : '' }}>
                                @if($hasBeratToples)
                                <input type="hidden" name="berat_toples" value="{{ $packing->berat_toples }}">
                                @endif
                            </div>
                        </div>

                        {{-- Berat Pouch & No. Lot --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Berat Pouch</label>
                                @php $hasBeratPouch = !is_null($packing->berat_pouch) && $packing->berat_pouch !== ''; @endphp
                                <input type="number" step="0.01" name="berat_pouch" class="form-control" value="{{ old('berat_pouch', $packing->berat_pouch) }}" {{ $hasBeratPouch ? 'readonly' : '' }}>
                                @if($hasBeratPouch)
                                <input type="hidden" name="berat_pouch" value="{{ $packing->berat_pouch }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">No. Lot</label>
                                @php $hasNoLot = !empty($packing->no_lot); @endphp
                                <input type="text" name="no_lot" class="form-control" value="{{ old('no_lot', $packing->no_lot) }}" {{ $hasNoLot ? 'readonly' : '' }}>
                                @if($hasNoLot)
                                <input type="hidden" name="no_lot" value="{{ $packing->no_lot }}">
                                @endif
                            </div>
                        </div>

                        {{-- Tanggal Kedatangan & Supplier --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Kedatangan</label>
                                @php $hasTglKed = !empty($packing->tgl_kedatangan); @endphp
                                <input type="date" name="tgl_kedatangan" class="form-control" value="{{ old('tgl_kedatangan', $packing->tgl_kedatangan) }}" {{ $hasTglKed ? 'readonly' : '' }}>
                                @if($hasTglKed)
                                <input type="hidden" name="tgl_kedatangan" value="{{ $packing->tgl_kedatangan }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Supplier</label>
                                @php $hasSupplier = !empty($packing->nama_supplier); @endphp
                                <select name="nama_supplier" class="form-control" {{ $hasSupplier ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->nama_supplier }}" {{ $packing->nama_supplier == $supplier->nama_supplier ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($hasSupplier)
                                <input type="hidden" name="nama_supplier" value="{{ $packing->nama_supplier }}">
                                @endif
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

{{-- == SCRIPTS == --}}
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

        // hanya set default jika kosong
        if (dateInput && !dateInput.value) {
            const now = new Date();
            dateInput.value = now.toISOString().split('T')[0];
        }

        if (timeInput && !timeInput.value) {
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            timeInput.value = `${hh}:00`;
        }

        if (shiftInput && !shiftInput.value) {
            const hh = new Date().getHours();
            if (hh >= 7 && hh < 15) shiftInput.value = "1";
            else if (hh >= 15 && hh < 23) shiftInput.value = "2";
            else shiftInput.value = "3";
            // jika selectpicker dipakai, refresh
            $('.selectpicker').selectpicker('refresh');
        }
    });

    // VALIDASI UKURAN FILE
    function validateFile(input) {
        const file = input.files[0];
        const max = 2 * 1024 * 1024; // 2MB
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

        if (!ok) {
            e.preventDefault();
            // bisa tunjukkan alert sederhana
            alert('Periksa ukuran file, maksimal 2 MB.');
        }
    });
</script>

<style>
    .is-invalid { border-color: #dc3545 !important; }
    .file-error { color: #dc3545; }
    small.text-muted { font-size: 0.85rem; }
</style>

@endsection
