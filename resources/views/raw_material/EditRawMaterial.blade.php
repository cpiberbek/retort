{{-- resources/views/raw_material/EditRawMaterial.blade.php --}}

@extends('layouts.app') {{-- Menggunakan layout utama Anda --}}

{{-- Mendefinisikan bagian style kustom --}}
@push('styles')
{{-- Menambahkan link untuk Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
{{-- 1. Include Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- Optional: Theme for Select2 to match Bootstrap 5 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    /* Mengubah font utama untuk tampilan yang lebih modern */
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
    }

    /* Kustomisasi label */
    .form-label {
        font-weight: 600;
        color: #495057;
    }

    /* Kustomisasi input, select, dan textarea */
    .form-control, .form-select {
        border-radius: 8px;
    }

    /* Ensure Select2 takes full width and height */
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px); /* Match Bootstrap's input height */
        padding: .375rem .75rem; /* Match padding */
        border: 1px solid #ced4da; /* Match border */
    }
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Style untuk tombol OK/Not OK (Updated UX: Netral by default) */
    .btn-check-group .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
        border-width: 1px;
    }

    /* Saat tombol aktif/solid */
    .btn-check-group .btn-success,
    .btn-check-group .btn-danger {
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: none;
    }

    /* Saat tombol netral/inactive */
    .btn-check-group .btn-outline-secondary {
        color: #6c757d;
        background-color: transparent;
        border-color: #ced4da;
    }
    .btn-check-group .btn-outline-secondary:hover {
        background-color: #e9ecef;
        color: #495057;
    }

    /* Style untuk item produk dinamis */
    .product-detail-item {
        background-color: #fdfdfd;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-right: 30px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

{{-- Mendefinisikan bagian konten --}}
@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            <h4 class="mb-1"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Bahan Baku</h4>
            <p class="text-muted mb-4">Perbarui detail formulir pemeriksaan bahan baku di bawah ini.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Ada beberapa masalah dengan input Anda:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('inspections.update', $inspection->uuid) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- CARD INFORMASI UMUM --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Umum</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="setup_kedatangan" class="form-label">Waktu Kedatangan</label>
                                <input type="datetime-local" class="form-control @error('setup_kedatangan') is-invalid @enderror" id="setup_kedatangan" name="setup_kedatangan" value="{{ old('setup_kedatangan', $inspection->setup_kedatangan ? $inspection->setup_kedatangan->format('Y-m-d\TH:i') : '') }}" required>
                                @error('setup_kedatangan') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>

                            <div class="col-12">
                                <label for="bahan_baku" class="form-label">Bahan Baku</label>
                                <select class="form-select select2 @error('bahan_baku') is-invalid @enderror" id="bahan_baku" name="bahan_baku" required>
                                    <option></option>
                                    @foreach ($masterBahanBaku as $bahan)
                                        <option value="{{ $bahan->nama_bahan_baku }}" {{ old('bahan_baku', $inspection->bahan_baku) == $bahan->nama_bahan_baku ? 'selected' : '' }}>
                                            {{ $bahan->nama_bahan_baku }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bahan_baku')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="supplier" class="form-label">Supplier</label>
                                <select class="form-select select2 @error('supplier') is-invalid @enderror" id="supplier" name="supplier" required>
                                    <option></option>
                                    @foreach ($suppliers as $sup)
                                        <option value="{{ $sup->nama_supplier }}" {{ old('supplier', $inspection->supplier) == $sup->nama_supplier ? 'selected' : '' }}>
                                            {{ $sup->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DETAIL PRODUK --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><i class="bi bi-list-nested"></i> Detail Produk</strong>
                            <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Item</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="product-details-container" data-details="{{ json_encode(old('details', $inspection->productDetails ?? [])) }}">
                            {{-- Form detail produk akan ditambahkan di sini oleh JS --}}
                        </div>
                    </div>
                </div>

                {{-- CARD KONDISI FISIK (UPDATED UX) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-truck"></i> Kondisi Fisik (Mobil & Kemasan)</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $kondisiFisikFields = [
                                    'mobil_check_warna' => 'Warna',
                                    'mobil_check_kotoran' => 'Kotoran',
                                    'mobil_check_aroma' => 'Aroma',
                                    'mobil_check_kemasan' => 'Kemasan'
                                ];
                            @endphp
                            @foreach ($kondisiFisikFields as $name => $label)
                            <div class="col-md-3 col-6 mb-3">
                                <label class="form-label d-block">{{ $label }}</label>
                                <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $inspection->$name ? '1' : '0') }}">

                                <div class="btn-group btn-check-group w-100" role="group">
                                    <button type="button" class="btn {{ old($name, $inspection->$name ? '1' : '0') === '1' ? 'btn-success' : 'btn-outline-secondary' }}" data-value="1" data-target-input="#{{ $name }}">
                                        <i class="bi bi-check-lg"></i> OK
                                    </button>
                                    <button type="button" class="btn {{ old($name, $inspection->$name ? '1' : '0') === '0' ? 'btn-danger' : 'btn-outline-secondary' }}" data-value="0" data-target-input="#{{ $name }}">
                                        <i class="bi bi-x-lg"></i> Not OK
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- CARD ANALISA PRODUK (DIPERBAIKI UNTUK DECIMAL K.A / FFA) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-search"></i> Analisa Produk</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- 1. K.A / FFA (Menjadi Input Number Decimal) --}}
                            <div class="col-md-3 col-6">
                                <label for="analisa_ka_ffa" class="form-label d-block">K.A / FFA</label>
                                <input type="number" step="0.01" class="form-control @error('analisa_ka_ffa') is-invalid @enderror" id="analisa_ka_ffa" name="analisa_ka_ffa" value="{{ old('analisa_ka_ffa', $inspection->analisa_ka_ffa ?? '') }}" min="0">
                                @error('analisa_ka_ffa')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- 2. Logo Halal --}}
                            <div class="col-md-3 col-6">
                                <label class="form-label d-block">Logo Halal</label>
                                <input type="hidden" name="analisa_logo_halal" id="analisa_logo_halal" value="{{ old('analisa_logo_halal', $inspection->analisa_logo_halal ? '1' : '0') }}">
                                <div class="btn-group btn-check-group w-100" role="group">
                                    <button type="button" class="btn {{ old('analisa_logo_halal', $inspection->analisa_logo_halal ? '1' : '0') === '1' ? 'btn-success' : 'btn-outline-secondary' }}" data-value="1" data-target-input="#analisa_logo_halal"><i class="bi bi-check-lg"></i> OK</button>
                                    <button type="button" class="btn {{ old('analisa_logo_halal', $inspection->analisa_logo_halal ? '1' : '0') === '0' ? 'btn-danger' : 'btn-outline-secondary' }}" data-value="0" data-target-input="#analisa_logo_halal"><i class="bi bi-x-lg"></i> Not OK</button>
                                </div>
                            </div>

                            <div class="col-md-6"></div> {{-- Spacer --}}

                            <div class="col-md-6">
                                <label for="analisa_negara_asal" class="form-label">Negara Asal Produsen</label>
                                <input type="text" class="form-control @error('analisa_negara_asal') is-invalid @enderror" id="analisa_negara_asal" name="analisa_negara_asal" value="{{ old('analisa_negara_asal', $inspection->analisa_negara_asal) }}" required>
                                @error('analisa_negara_asal') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="analisa_produsen" class="form-label">Nama Produsen</label>
                                <input type="text" class="form-control @error('analisa_produsen') is-invalid @enderror" id="analisa_produsen" name="analisa_produsen" value="{{ old('analisa_produsen', $inspection->analisa_produsen) }}" required>
                                @error('analisa_produsen') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DOKUMEN PENDUKUNG --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-file-earmark-text"></i> Dokumen Pendukung</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- KOLOM KIRI --}}
                            <div class="col-md-6">
                                <label class="form-label">Dokumen Halal</label>

                                {{-- Block 1: Status Berlaku --}}
                                <div class="mb-2">
                                    <label class="form-label d-block small mb-1" for="dokumen_halal_berlaku">Status Berlaku?</label>
                                    <input type="hidden" name="dokumen_halal_berlaku" id="dokumen_halal_berlaku" value="{{ old('dokumen_halal_berlaku', $inspection->dokumen_halal_berlaku ? '1' : '0') }}">

                                    <div class="btn-group btn-check-group w-50" role="group">
                                        <button type="button" class="btn btn-sm {{ old('dokumen_halal_berlaku', $inspection->dokumen_halal_berlaku ? '1' : '0') === '1' ? 'btn-success' : 'btn-outline-secondary' }}" data-value="1" data-target-input="#dokumen_halal_berlaku">
                                            <i class="bi bi-check-lg"></i> Berlaku (OK)
                                        </button>
                                        <button type="button" class="btn btn-sm {{ old('dokumen_halal_berlaku', $inspection->dokumen_halal_berlaku ? '1' : '0') === '0' ? 'btn-danger' : 'btn-outline-secondary' }}" data-value="0" data-target-input="#dokumen_halal_berlaku">
                                            <i class="bi bi-x-lg"></i> Tidak (X)
                                        </button>
                                    </div>
                                </div>

                                {{-- Block 2: Input File Halal --}}
                                <div>
                                    <label class="form-label small mb-1" for="dokumen_halal_file">Upload File Halal Baru (Optional)</label>
                                    <input type="file" class="form-control form-control-sm @error('dokumen_halal_file') is-invalid @enderror" name="dokumen_halal_file" id="dokumen_halal_file">
                                    @error('dokumen_halal_file') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                                    @if($inspection->dokumen_halal_file)
                                        <div class="mt-1 text-muted small">
                                            File saat ini: <a href="{{ asset('storage/' . $inspection->dokumen_halal_file) }}" target="_blank">Lihat File</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- KOLOM KANAN --}}
                            <div class="col-md-6">
                                <label for="dokumen_coa_file" class="form-label">Dokumen COA</label>
                                <input class="form-control form-control-sm @error('dokumen_coa_file') is-invalid @enderror" type="file" id="dokumen_coa_file" name="dokumen_coa_file">
                                @error('dokumen_coa_file') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                                @if($inspection->dokumen_coa_file)
                                    <div class="mt-1 text-muted small">
                                        File saat ini: <a href="{{ asset('storage/' . $inspection->dokumen_coa_file) }}" target="_blank">Lihat File</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD TRANSPORTER --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-person-badge"></i> Informasi Transporter</strong>
                    </div>
                    <div class="card-body">
                         <div class="row g-3">
                            <div class="col-md-4">
                                <label for="nopol_mobil" class="form-label">Nopol Mobil</label>
                                <input type="text" class="form-control @error('nopol_mobil') is-invalid @enderror" id="nopol_mobil" name="nopol_mobil" value="{{ old('nopol_mobil', $inspection->nopol_mobil) }}" required>
                                @error('nopol_mobil') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>

                            {{-- SUHU MOBIL (Menjadi Input Number Decimal) --}}
                             <div class="col-md-4">
                                 <label for="suhu_mobil" class="form-label">Suhu Mobil (°C)</label>
                                 <div class="input-group input-group-sm">
                                     <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                                     <input type="text" inputmode="decimal" step="0.01" class="form-control suhu-number-input @error('suhu_mobil') is-invalid @enderror" id="suhu_mobil" name="suhu_mobil" value="{{ old('suhu_mobil', $inspection->suhu_mobil ?? '') }}">
                                 </div>
                                 @error('suhu_mobil')
                                     <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                 @enderror
                             </div>

                            <div class="col-md-4">
                                <label class="form-label">Kondisi Mobil</label>

                                @php
                                    $options = [
                                        'Bersih',
                                        'Kering',
                                        'Tidak Bocor',
                                        'Tidak Berdebu',
                                        'Tidak Basah',
                                        'Bebas Hama',
                                        'Bebas Noda (Karat, cat, tinta)',
                                        'Bebas Bekas oli di lantai/dinding',
                                        'Tidak ada produk non halal'
                                    ];

                                    $selectedKondisi = collect(old('kondisi_mobil', isset($inspection) ? explode(',', $inspection->kondisi_mobil ?? '') : []))
                                        ->filter()
                                        ->toArray();
                                @endphp

                                <div class="row g-2 @error('kondisi_mobil') is-invalid @enderror">
                                    @foreach ($options as $item)
                                        @php $id = 'kondisi_' . \Illuminate\Support\Str::slug($item, '_'); @endphp

                                        <div class="col-6 col-md-4">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="kondisi_mobil[]"
                                                    value="{{ $item }}"
                                                    id="{{ $id }}"
                                                    {{ in_array($item, $selectedKondisi) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="{{ $id }}">
                                                    {{ $item }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('kondisi_mobil')
                                    <span class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                             <div class="col-md-6">
                                <label for="do_po" class="form-label">No. DO / PO</label>
                                <input type="text" class="form-control @error('do_po') is-invalid @enderror" id="do_po" name="do_po" value="{{ old('do_po', $inspection->do_po) }}" required>
                                @error('do_po') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="no_segel" class="form-label">No. Segel</label>
                                <input type="text" class="form-control @error('no_segel') is-invalid @enderror" id="no_segel" name="no_segel" value="{{ old('no_segel', $inspection->no_segel) }}" required>
                                @error('no_segel') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="suhu_daging" class="form-label">Suhu Daging/Bahan (°C)</label>
                                <div class="input-group input-group-sm">
                                    <button type="button" class="btn btn-outline-secondary btn-toggle-minus" tabindex="-1">±</button>
                                    <input type="text" inputmode="decimal" class="form-control suhu-number-input @error('suhu_daging') is-invalid @enderror" id="suhu_daging" name="suhu_daging" value="{{ old('suhu_daging', $inspection->suhu_daging) }}">
                                </div>
                                @error('suhu_daging') <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                            <div class="col-12">
                                <label for="keterangan" class="form-label">Keterangan (Optional)</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $inspection->keterangan) }}</textarea>
                                @error('keterangan') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                         </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-circle"></i> Update Pemeriksaan</button>
                    <a href="{{ route('inspections.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- 1. Include jQuery (Select2 depends on it) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- 2. Include Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // 3. Initialize Select2
    $(document).ready(function() {
        $('.select2').select2({
            theme: "bootstrap-5",
            placeholder: "Ketik untuk mencari...",
            allowClear: true,
            dropdownAutoWidth: true,
            width: '49%'
        });
    });
</script>

<script>
    // --- Script Validasi Max & Min (DIPERBAIKI MENGGUNAKAN 'change') ---
    document.querySelectorAll('input[type="number"][max]').forEach(function(input) {
        input.addEventListener('change', function() {
            const max = parseFloat(this.getAttribute('max'));
            if (this.value !== '' && parseFloat(this.value) > max) {
                this.value = max;
            }
        });
    });

    document.querySelectorAll('input[type="number"][min]').forEach(function(input) {
        input.addEventListener('change', function() {
            const min = parseFloat(this.getAttribute('min'));
            if (this.value !== '' && parseFloat(this.value) < min) {
                this.value = min;
            }
        });
    });

    // --- Script Input Suhu (mendukung nilai minus di HP) ---
    document.querySelectorAll('.suhu-number-input').forEach(function(input) {
        input.addEventListener('input', function() {
            let val = this.value;
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
            this.value = val;
        });
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

    document.addEventListener('DOMContentLoaded', function() {
        // --- Script for Dynamic Product Details (MODIFIED FOR EDIT) ---
        const container = document.getElementById('product-details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0;

        function renderDetailForm(data = null) {
            const i = detailIndex;

            const newDetail = document.createElement('div');
            newDetail.classList.add('product-detail-item', 'border', 'p-3', 'mb-3', 'rounded', 'shadow-sm');

            const kodeBatch = data ? (data.kode_batch || '') : '';
            const tglProduksi = data ? (data.tanggal_produksi || '') : '';
            const exp = data ? (data.exp || '') : '';
            const jumlah = data ? (data.jumlah || '') : '';
            const jumlahSampel = data ? (data.jumlah_sampel || '') : '';
            const jumlahReject = data ? (data.jumlah_reject || '') : '';

            newDetail.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5>Item #${i + 1}</h5>
                    <button type="button" class="btn btn-danger btn-sm remove-detail-btn"><i class="bi bi-trash"></i> Hapus</button>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kode Batch</label>
                        <input type="text" name="details[${i}][kode_batch]" class="form-control" value="${kodeBatch}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Produksi</label>
                        <input type="date" name="details[${i}][tanggal_produksi]" class="form-control" value="${tglProduksi}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">EXP Date</label>
                        <input type="date" name="details[${i}][exp]" class="form-control" value="${exp}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Barang</label>
                        <input type="number" step="0.01" name="details[${i}][jumlah]" class="form-control" value="${jumlah}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Sampel</label>
                        <input type="number" step="0.01" name="details[${i}][jumlah_sampel]" class="form-control" value="${jumlahSampel}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Reject</label>
                        <input type="number" step="0.01" name="details[${i}][jumlah_reject]" class="form-control" value="${jumlahReject}" required>
                    </div>
                </div>
            `;
            container.appendChild(newDetail);
            detailIndex++;
        }

        if (addBtn) {
            addBtn.addEventListener('click', () => renderDetailForm(null));
        }

        if (container) {
            container.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-detail-btn');
                if (removeBtn) {
                    removeBtn.closest('.product-detail-item').remove();
                }
            });

            // Render existing data
            try {
                const existingDetails = JSON.parse(container.dataset.details || '[]');
                if (existingDetails.length > 0) {
                    existingDetails.forEach(detail => {
                        renderDetailForm(detail);
                    });
                } else {
                    renderDetailForm(null);
                }
            } catch (e) {
                console.error("Gagal parse data detail: ", e);
                renderDetailForm(null);
            }
        }

        // --- Script for OK/Not OK Buttons (UPDATED UX for Edit Page) ---
        const buttonGroups = document.querySelectorAll('.btn-check-group');

        buttonGroups.forEach(group => {
            group.addEventListener('click', function(e) {
                const button = e.target.closest('.btn');

                if (button) {
                    const value = button.dataset.value;
                    const targetInputId = button.dataset.targetInput;

                    if (!targetInputId) return;

                    const targetInput = document.querySelector(targetInputId);

                    // 1. Update nilai input hidden
                    if (targetInput) {
                        targetInput.value = value;
                    }

                    // 2. RESET semua tombol dalam grup ini ke mode "Netral" (Abu-abu Outline)
                    const buttonsInGroup = group.querySelectorAll('.btn');
                    buttonsInGroup.forEach(btn => {
                        btn.classList.remove('btn-success', 'btn-danger'); // Hapus warna solid
                        btn.classList.add('btn-outline-secondary'); // Tambah outline abu-abu
                        btn.style.opacity = '0.6';
                    });

                    // 3. SET warna Solid pada tombol yang DIKLIK
                    button.classList.remove('btn-outline-secondary'); // Hapus outline abu-abu
                    button.style.opacity = '1'; // Pastikan opacity penuh

                    if (value === '1') {
                        button.classList.add('btn-success');
                    } else {
                        button.classList.add('btn-danger');
                    }
                }
            });
        });
    });
</script>
@endpush
