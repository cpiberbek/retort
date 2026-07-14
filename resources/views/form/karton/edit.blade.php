@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-pencil-square"></i> Edit Kontrol Labelisasi Karton
                </h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="samplingForm" action="{{ route('karton.edit_spv', $karton->uuid) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- ===================== IDENTITAS DATA ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>Identitas Data Packing</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="date" id="dateInput" class="form-control"
                                        value="{{ old('date', $karton->date) }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                           <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $karton->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            @php
                                $kode_batch_text = \App\Models\Mincing::where('uuid', $karton->kode_produksi)->value('kode_produksi') ?? $karton->kode_produksi;
                            @endphp

                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <select name="kode_produksi" class="form-control" id="kode_batch" required>
                                    @if($karton->kode_produksi)
                                        <option value="{{ $karton->kode_produksi }}" selected>{{ $kode_batch_text }}</option>
                                    @else
                                        <option value="">Pilih Varian Terlebih Dahulu</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        </div>
                    </div>

                    {{-- ===================== ITEM SORTIR ===================== --}}
                    <div class="card shadow-sm border-0 mb-4">

                        {{-- HEADER --}}
                        <div class="card-header bg-info text-white d-flex align-items-center">
                            <i class="bi bi-clipboard-check me-2"></i>
                            <strong>Pemeriksaan</strong>
                        </div>

                        <div class="card-body">

                            {{-- ================= WAKTU & TANGGAL ================= --}}
                            <div class="row g-3 mb-4">

                                {{-- WAKTU --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Waktu Proses
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-clock"></i>
                                        </span>

                                        <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control"
                                            value="{{ old('waktu_mulai', $karton->waktu_mulai) }}">

                                        <span class="input-group-text">
                                            s/d
                                        </span>

                                        <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control"
                                            value="{{ old('waktu_selesai', $karton->waktu_selesai) }}">
                                    </div>
                                </div>

                                {{-- TANGGAL --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Tanggal Kedatangan
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-calendar-event"></i>
                                        </span>

                                        <input type="date" name="tgl_kedatangan" id="tgl_kedatangan" class="form-control"
                                            value="{{ old('tgl_kedatangan', $karton->tgl_kedatangan) }}" required>
                                    </div>
                                </div>

                            </div>

                            {{-- ================= SUPPLIER ================= --}}
                            <div class="row g-3 mb-4">

                                <div class="col-md-6">
                                    <label class="form-label">Jumlah / Tambahan</label>
                                    <input type="number" name="jumlah" id="jumlah" class="form-control"
                                        value="{{ old('jumlah', $karton->jumlah) }}" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Nama Supplier
                                    </label>

                                    <select id="nama_supplier" name="nama_supplier" class="form-control selectpicker"
                                        data-live-search="true" required>

                                        <option value="">-- Pilih Supplier --</option>

                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->nama_supplier }}"
                                                {{ old('nama_supplier', $karton->nama_supplier) == $supplier->nama_supplier ? 'selected' : '' }}>
                                                {{ $supplier->nama_supplier }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                            </div>

                            {{-- ================= LOT & FILE ================= --}}
                            <div class="row g-3">

                                {{-- LOT --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        No. Lot Karton
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-upc-scan"></i>
                                        </span>

                                        <input type="text" name="no_lot" id="no_lot" class="form-control"
                                            placeholder="Masukkan nomor lot" value="{{ old('no_lot', $karton->no_lot) }}">
                                    </div>
                                </div>

                                {{-- FILE --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Upload Kode Batch (Karton)
                                    </label>

                                    <input type="file" id="kode_karton" name="kode_karton" class="form-control"
                                        accept="image/*">

                                    @if ($karton->kode_karton)
                                        @php
                                            $kartonPath = str_replace('public/', 'storage/', $karton->kode_karton);
                                        @endphp

                                        <small class="d-block mt-2">
                                            <a href="{{ asset($kartonPath) }}" target="_blank"
                                                class="text-primary text-decoration-none fw-semibold">

                                                <i class="bi bi-image me-1"></i>
                                                Lihat Gambar Sebelumnya
                                            </a>
                                        </small>
                                    @endif

                                    <small id="kode-karton-error" class="text-danger"></small>
                                </div>

                            </div>

                        </div>
                    </div>
                    {{-- ===================== OPERATOR ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-white">
                            <strong>Operator - KR</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Operator</label>
                                    <select id="nama_operator" name="nama_operator" class="form-control selectpicker"
                                        data-live-search="true" required>
                                        <option value="">-- Pilih Operator --</option>
                                        @foreach ($operators as $operator)
                                            <option value="{{ $operator->nama_karyawan }}"
                                                {{ old('nama_operator', $karton->nama_operator) == $operator->nama_karyawan ? 'selected' : '' }}>
                                                {{ $operator->nama_karyawan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama KR</label>
                                    <select id="nama_koordinator" name="nama_koordinator"
                                        class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">-- Pilih Koordinator --</option>
                                        @foreach ($koordinators as $koordinator)
                                            <option value="{{ $koordinator->nama_karyawan }}"
                                                {{ old('nama_koordinator', $karton->nama_koordinator) == $koordinator->nama_karyawan ? 'selected' : '' }}>
                                                {{ $koordinator->nama_karyawan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== CATATAN ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light"><strong>Keterangan</strong></div>
                        <div class="card-body">
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $karton->keterangan) }}</textarea>
                        </div>
                    </div>

                    {{-- ===================== TOMBOL ===================== --}}
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" id="submitBtn" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('karton.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Include jQuery (Select2 depends on it) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <script>
        $(document).ready(function() {
            if (typeof $.fn.selectpicker === 'function') {
                $('.selectpicker').selectpicker();
            }
            const kodeInput = document.getElementById('kode_produksi');
            const kodeError = document.getElementById('kodeError');
            const form = document.getElementById('samplingForm');

            // validasi langsung saat input
            kodeInput.addEventListener('input', function() {
                validateKode();
            });

            // blok kirim form jika kode tidak valid
            form.addEventListener('submit', function(e) {
                if (!validateKode()) {
                    e.preventDefault();
                    alert('Kode batch tidak valid! Periksa kembali sebelum menyimpan.');
                    kodeInput.focus();
                }
            });

            function validateKode() {
                let value = kodeInput.value.toUpperCase().replace(/\s+/g, '');
                kodeInput.value = value;
                kodeError.textContent = '';
                kodeError.classList.add('d-none');

                if (value.length !== 10) {
                    kodeError.textContent = "Kode batch harus terdiri dari 10 karakter.";
                    kodeError.classList.remove('d-none');
                    return false;
                }

                const format = /^[A-Z0-9]+$/;
                if (!format.test(value)) {
                    kodeError.textContent = "Kode batch hanya boleh huruf besar dan angka.";
                    kodeError.classList.remove('d-none');
                    return false;
                }

                const bulanChar = value.charAt(1);
                const validBulan = /^[A-L]$/;
                if (!validBulan.test(bulanChar)) {
                    kodeError.textContent = "Karakter ke-2 harus huruf bulan (Aâ€“L).";
                    kodeError.classList.remove('d-none');
                    return false;
                }

                const hariStr = value.substr(2, 2);
                const hari = parseInt(hariStr, 10);
                if (isNaN(hari) || hari < 1 || hari > 31) {
                    kodeError.textContent = "Karakter ke-3 dan ke-4 harus tanggal valid (01â€“31).";
                    kodeError.classList.remove('d-none');
                    return false;
                }

                return true;
            }

            // Validasi File Upload â‰¤ 5MB
            const maxFileSize = 5 * 1024 * 1024;
            const fileInput = document.getElementById("kode_karton");
            const fileError = document.getElementById("kode-karton-error");

            fileInput.addEventListener("change", function() {
                fileError.textContent = "";
                const file = this.files[0];

                if (!file) return;
                if (file.size > maxFileSize) {
                    fileError.textContent = "Ukuran file maksimal 5MB.";
                    this.value = "";
                }
            });
        });
    </script>
    <script>
$(function () {
    const namaProdukSelect = $('#nama_produk');
    const batchSelect = $('#kode_batch');
    
    let initialBatch = "{{ $karton->kode_produksi ?? '' }}";
    let isFirstLoad = true;

    function initBatchSelect() {
        let produkValue = namaProdukSelect.val();
        
        if (batchSelect.data('select2')) {
            batchSelect.select2('destroy');
        }
        
        if (!produkValue) {
            batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
            batchSelect.prop("disabled", true);
            return;
        }
        
        if (!isFirstLoad) {
            batchSelect.html('<option value="">-- Pilih Kode Batch --</option>');
        }
        batchSelect.prop("disabled", false);
        
        batchSelect.select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: "-- Pilih Kode Batch --",
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
        isFirstLoad = false;
    }

    namaProdukSelect.on('change', function () {
        isFirstLoad = false;
        initBatchSelect();
    });

    let initialProduk = namaProdukSelect.val();
    if (initialProduk) {
        initBatchSelect();
        if(initialBatch){
            let newOption = new Option(initialBatch, initialBatch, true, true);
            batchSelect.append(newOption).trigger('change');
        }
    }

});
</script>
@endpush
