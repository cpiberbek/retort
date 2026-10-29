
{{-- resources/views/raw_material/UpdateRawMaterial.blade.php --}}

@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        min-height: 44px;
    }

    .form-control[readonly],
    .form-select:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
        border-color: #dee2e6;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 44px;
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
        display: flex;
        align-items: center;
    }

    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #86b7fe;
        box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-right: 30px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .btn-check-group .btn {
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        transition: all .2s ease;
        border-width: 1px;
    }

    .btn-check-group .btn-success,
    .btn-check-group .btn-danger {
        font-weight: 600;
        border: none;
    }

    .btn-check-group .btn-outline-secondary {
        color: #6c757d;
        border-color: #ced4da;
    }

    .btn-check-group .btn-outline-secondary:hover {
        background-color: #e9ecef;
        color: #495057;
    }

    .btn-check-group .btn[disabled] {
        opacity: .6;
        cursor: not-allowed;
    }

    .product-detail-item {
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
        border-radius: 12px !important;
    }

    .product-detail-item .item-header {
        padding-bottom: .75rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .section-title {
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6c757d;
        padding-bottom: .35rem;
        border-bottom: 1px dashed #dee2e6;
        margin-top: 1.25rem;
    }

    .section-title:first-child {
        margin-top: 0;
    }

    .form-check {
        padding: .45rem .6rem .45rem 1.8rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        min-height: 42px;
        display: flex;
        align-items: center;
        background-color: #fff;
    }

    .form-check-input {
        cursor: pointer;
    }

    .form-check-label {
        cursor: pointer;
        font-size: .9rem;
        color: #495057;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .readonly-note {
        font-size: .8rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-3 p-md-4 p-lg-5">

            <h4 class="mb-1">
                <i class="bi bi-pencil-square"></i>
                Edit Pemeriksaan Bahan Baku
            </h4>
            <p class="text-muted mb-4">
                Field yang sudah terisi sebelumnya tidak dapat diedit.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Ada masalah input:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('inspections.update', $inspection->uuid) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- INFORMASI UMUM --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <strong>
                            <i class="bi bi-info-circle-fill"></i>
                            Informasi Umum
                        </strong>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="setup_kedatangan" class="form-label">Waktu Kedatangan</label>
                                <input
                                    type="datetime-local"
                                    class="form-control @error('setup_kedatangan') is-invalid @enderror"
                                    id="setup_kedatangan"
                                    name="setup_kedatangan"
                                    value="{{ old('setup_kedatangan', $inspection->setup_kedatangan ? $inspection->setup_kedatangan->format('Y-m-d\TH:i') : '') }}"
                                    required
                                    {{ $inspection->setup_kedatangan ? 'readonly' : '' }}
                                >
                                @error('setup_kedatangan')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="bahan_baku" class="form-label">Bahan Baku</label>
                                @php $isBahanBakuFilled = !empty($inspection->bahan_baku); @endphp

                                <select
                                    class="form-select select2 @error('bahan_baku') is-invalid @enderror"
                                    id="bahan_baku"
                                    name="bahan_baku"
                                    required
                                    {{ $isBahanBakuFilled ? 'disabled' : '' }}
                                >
                                    <option></option>
                                    @foreach ($masterBahanBaku as $bahan)
                                        <option
                                            value="{{ $bahan->nama_bahan_baku }}"
                                            {{ old('bahan_baku', $inspection->bahan_baku) == $bahan->nama_bahan_baku ? 'selected' : '' }}
                                        >
                                            {{ $bahan->nama_bahan_baku }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($isBahanBakuFilled)
                                    <input type="hidden" name="bahan_baku" value="{{ $inspection->bahan_baku }}">
                                @endif

                                @error('bahan_baku')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="supplier" class="form-label">Supplier</label>
                                @php $isSupplierFilled = !empty($inspection->supplier); @endphp

                                <select
                                    class="form-select select2 @error('supplier') is-invalid @enderror"
                                    id="supplier"
                                    name="supplier"
                                    required
                                    {{ $isSupplierFilled ? 'disabled' : '' }}
                                >
                                    <option></option>
                                    @foreach ($suppliers as $sup)
                                        <option
                                            value="{{ $sup->nama_supplier }}"
                                            {{ old('supplier', $inspection->supplier) == $sup->nama_supplier ? 'selected' : '' }}
                                        >
                                            {{ $sup->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($isSupplierFilled)
                                    <input type="hidden" name="supplier" value="{{ $inspection->supplier }}">
                                @endif

                                @error('supplier')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DETAIL PRODUK --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>
                                <i class="bi bi-list-nested"></i>
                                Detail Produk
                            </strong>
                            <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm">
                                <i class="bi bi-plus-lg"></i> Tambah Item
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="product-details-container" data-details="{{ json_encode(old('details', $inspection->productDetails ?? [])) }}"></div>
                    </div>
                </div>

                {{-- KONDISI FISIK --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <strong>
                            <i class="bi bi-truck"></i>
                            Kondisi Fisik (Mobil & Kemasan)
                        </strong>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            @php
                                $kondisiFisikFields = [
                                    'mobil_check_warna' => 'Warna',
                                    'mobil_check_kotoran' => 'Kotoran',
                                    'mobil_check_aroma' => 'Aroma',
                                    'mobil_check_kemasan' => 'Kemasan',
                                ];
                            @endphp

                            @foreach ($kondisiFisikFields as $name => $label)
                                <div class="col-md-6">
                                    <label class="form-label d-block">{{ $label }}</label>

                                    @php
                                        $currentValue = old($name, $inspection->$name ? '1' : '0');
                                        $isFilled = !is_null($inspection->$name);
                                    @endphp

                                    <input
                                        type="hidden"
                                        name="{{ $name }}"
                                        id="{{ $name }}"
                                        value="{{ $currentValue }}"
                                    >

                                    <div class="btn-group btn-check-group w-100" role="group">
                                        <button
                                            type="button"
                                            class="btn {{ (string) $currentValue === '1' ? 'btn-success' : 'btn-outline-secondary' }}"
                                            data-value="1"
                                            data-target-input="#{{ $name }}"
                                            {{ $isFilled ? 'disabled' : '' }}
                                        >
                                            <i class="bi bi-check-lg"></i> OK
                                        </button>

                                        <button
                                            type="button"
                                            class="btn {{ (string) $currentValue === '0' ? 'btn-danger' : 'btn-outline-secondary' }}"
                                            data-value="0"
                                            data-target-input="#{{ $name }}"
                                            {{ $isFilled ? 'disabled' : '' }}
                                        >
                                            <i class="bi bi-x-lg"></i> Not OK
                                        </button>
                                    </div>

                                    @if ($isFilled)
                                        <div class="readonly-note mt-1">Data sudah terisi dan terkunci.</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ANALISA PRODUK --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <strong>
                            <i class="bi bi-search"></i>
                            Analisa Produk
                        </strong>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="analisa_ka_ffa" class="form-label">K.A / FFA</label>
                                @php $isKaFfaFilled = !is_null($inspection->analisa_ka_ffa); @endphp

                                <input
                                    type="text"
                                    inputmode="decimal"
                                    class="form-control @error('analisa_ka_ffa') is-invalid @enderror"
                                    id="analisa_ka_ffa"
                                    name="analisa_ka_ffa"
                                    value="{{ old('analisa_ka_ffa', $inspection->analisa_ka_ffa ?? '') }}"
                                    oninput="this.value = this.value.replace(/[^0-9.-]/g, '')"
                                    {{ $isKaFfaFilled ? 'readonly' : '' }}
                                >

                                @error('analisa_ka_ffa')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label d-block">Logo Halal</label>
                                @php
                                    $isHalalFilled = !is_null($inspection->analisa_logo_halal);
                                    $halalValue = old('analisa_logo_halal', $inspection->analisa_logo_halal ? '1' : '0');
                                @endphp

                                <input
                                    type="hidden"
                                    name="analisa_logo_halal"
                                    id="analisa_logo_halal"
                                    value="{{ $halalValue }}"
                                >

                                <div class="btn-group btn-check-group w-100" role="group">
                                    <button
                                        type="button"
                                        class="btn {{ (string) $halalValue === '1' ? 'btn-success' : 'btn-outline-secondary' }}"
                                        data-value="1"
                                        data-target-input="#analisa_logo_halal"
                                        {{ $isHalalFilled ? 'disabled' : '' }}
                                    >
                                        <i class="bi bi-check-lg"></i> OK
                                    </button>

                                    <button
                                        type="button"
                                        class="btn {{ (string) $halalValue === '0' ? 'btn-danger' : 'btn-outline-secondary' }}"
                                        data-value="0"
                                        data-target-input="#analisa_logo_halal"
                                        {{ $isHalalFilled ? 'disabled' : '' }}
                                    >
                                        <i class="bi bi-x-lg"></i> Not OK
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="analisa_negara_asal" class="form-label">Negara Asal Produsen</label>
                                <input
                                    type="text"
                                    class="form-control @error('analisa_negara_asal') is-invalid @enderror"
                                    id="analisa_negara_asal"
                                    name="analisa_negara_asal"
                                    value="{{ old('analisa_negara_asal', $inspection->analisa_negara_asal) }}"
                                    required
                                    {{ $inspection->analisa_negara_asal ? 'readonly' : '' }}
                                >
                                @error('analisa_negara_asal')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="analisa_produsen" class="form-label">Nama Produsen</label>
                                <input
                                    type="text"
                                    class="form-control @error('analisa_produsen') is-invalid @enderror"
                                    id="analisa_produsen"
                                    name="analisa_produsen"
                                    value="{{ old('analisa_produsen', $inspection->analisa_produsen) }}"
                                    required
                                    {{ $inspection->analisa_produsen ? 'readonly' : '' }}
                                >
                                @error('analisa_produsen')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DOKUMEN PENDUKUNG --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <strong>
                            <i class="bi bi-file-earmark-text"></i>
                            Dokumen Pendukung
                        </strong>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Dokumen Halal</label>

                                <div class="mb-3">
                                    <label class="form-label d-block small mb-1">Status Berlaku?</label>

                                    @php
                                        $isDokHalalFilled = !is_null($inspection->dokumen_halal_berlaku);
                                        $dokHalalValue = old('dokumen_halal_berlaku', $inspection->dokumen_halal_berlaku ? '1' : '0');
                                    @endphp

                                    <input
                                        type="hidden"
                                        name="dokumen_halal_berlaku"
                                        id="dokumen_halal_berlaku"
                                        value="{{ $dokHalalValue }}"
                                    >

                                    <div class="btn-group btn-check-group w-100" role="group">
                                        <button
                                            type="button"
                                            class="btn btn-sm {{ (string) $dokHalalValue === '1' ? 'btn-success' : 'btn-outline-secondary' }}"
                                            data-value="1"
                                            data-target-input="#dokumen_halal_berlaku"
                                            {{ $isDokHalalFilled ? 'disabled' : '' }}
                                        >
                                            <i class="bi bi-check-lg"></i> Berlaku (OK)
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm {{ (string) $dokHalalValue === '0' ? 'btn-danger' : 'btn-outline-secondary' }}"
                                            data-value="0"
                                            data-target-input="#dokumen_halal_berlaku"
                                            {{ $isDokHalalFilled ? 'disabled' : '' }}
                                        >
                                            <i class="bi bi-x-lg"></i> Tidak (X)
                                        </button>
                                    </div>
                                </div>

                                @if ($inspection->dokumen_halal_file)
                                    <div class="alert alert-light border py-2 mb-0">
                                        <i class="bi bi-file-check"></i>
                                        File:
                                        <a href="{{ asset('storage/' . $inspection->dokumen_halal_file) }}" target="_blank">Lihat</a>
                                        <span class="text-muted small ms-2">(Terkunci)</span>
                                    </div>
                                @else
                                    <label class="form-label small mb-1" for="dokumen_halal_file">Upload File Halal Baru</label>
                                    <input
                                        type="file"
                                        class="form-control form-control-sm @error('dokumen_halal_file') is-invalid @enderror"
                                        name="dokumen_halal_file"
                                        id="dokumen_halal_file"
                                    >
                                    @error('dokumen_halal_file')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Dokumen COA</label>

                                @if ($inspection->dokumen_coa_file)
                                    <div class="alert alert-light border py-2 mb-0">
                                        <i class="bi bi-file-check"></i>
                                        File:
                                        <a href="{{ asset('storage/' . $inspection->dokumen_coa_file) }}" target="_blank">Lihat</a>
                                        <span class="text-muted small ms-2">(Terkunci)</span>
                                    </div>
                                @else
                                    <label for="dokumen_coa_file" class="form-label small mb-1">Upload File COA</label>
                                    <input
                                        class="form-control form-control-sm @error('dokumen_coa_file') is-invalid @enderror"
                                        type="file"
                                        id="dokumen_coa_file"
                                        name="dokumen_coa_file"
                                    >
                                    @error('dokumen_coa_file')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INFORMASI TRANSPORTER --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <strong>
                            <i class="bi bi-person-badge"></i>
                            Informasi Transporter
                        </strong>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nopol_mobil" class="form-label">Nopol Mobil</label>
                                <input
                                    type="text"
                                    class="form-control @error('nopol_mobil') is-invalid @enderror"
                                    id="nopol_mobil"
                                    name="nopol_mobil"
                                    value="{{ old('nopol_mobil', $inspection->nopol_mobil) }}"
                                    required
                                    {{ $inspection->nopol_mobil ? 'readonly' : '' }}
                                >
                                @error('nopol_mobil')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="suhu_mobil" class="form-label">Suhu Mobil (°C)</label>
                                @php $isSuhuMobilFilled = !is_null($inspection->suhu_mobil); @endphp

                                <div class="input-group">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-toggle-minus"
                                        tabindex="-1"
                                        title="Toggle minus"
                                        {{ $isSuhuMobilFilled ? 'disabled' : '' }}
                                    >±</button>

                                    <input
                                        type="text"
                                        inputmode="decimal"
                                        class="form-control suhu-number-input @error('suhu_mobil') is-invalid @enderror"
                                        id="suhu_mobil"
                                        name="suhu_mobil"
                                        value="{{ old('suhu_mobil', $inspection->suhu_mobil ?? '') }}"
                                        {{ $isSuhuMobilFilled ? 'readonly' : '' }}
                                    >
                                </div>

                                @error('suhu_mobil')
                                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-12">
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
                                        'Tidak ada produk non halal',
                                    ];

                                    $selectedKondisi = old('kondisi_mobil');

                                    if ($selectedKondisi === null) {
                                        $storedKondisi = $inspection->kondisi_mobil
                                            ? str_replace(
                                                'Bebas Noda (Karat, cat, tinta)',
                                                'Bebas Noda (Karat|cat|tinta)',
                                                $inspection->kondisi_mobil
                                            )
                                            : '';

                                        $selectedKondisi = array_map('trim', explode(',', $storedKondisi));

                                        $selectedKondisi = array_map(
                                            fn ($item) => str_replace(
                                                'Bebas Noda (Karat|cat|tinta)',
                                                'Bebas Noda (Karat, cat, tinta)',
                                                $item
                                            ),
                                            $selectedKondisi
                                        );
                                    }
                                @endphp

                                <div class="row g-2 @error('kondisi_mobil') is-invalid @enderror">
                                    @foreach ($options as $item)
                                        @php $id = 'kondisi_' . \Illuminate\Support\Str::slug($item, '_'); @endphp

                                        <div class="col-12 col-sm-6 col-lg-4">
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

                                <div class="readonly-note mt-2">
                                    Kondisi mobil dapat dicentang atau di-uncheck kapan saja.
                                </div>

                                @error('kondisi_mobil')
                                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="do_po" class="form-label">No. DO / PO</label>
                                <input
                                    type="text"
                                    class="form-control @error('do_po') is-invalid @enderror"
                                    id="do_po"
                                    name="do_po"
                                    value="{{ old('do_po', $inspection->do_po) }}"
                                    required
                                    {{ $inspection->do_po ? 'readonly' : '' }}
                                >
                                @error('do_po')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="no_segel" class="form-label">No. Segel</label>
                                <input
                                    type="text"
                                    class="form-control @error('no_segel') is-invalid @enderror"
                                    id="no_segel"
                                    name="no_segel"
                                    value="{{ old('no_segel', $inspection->no_segel) }}"
                                    required
                                    {{ $inspection->no_segel ? 'readonly' : '' }}
                                >
                                @error('no_segel')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="suhu_daging" class="form-label">Suhu Daging/Bahan (°C)</label>
                                @php $isSuhuDagingFilled = !is_null($inspection->suhu_daging); @endphp

                                <div class="input-group">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-toggle-minus"
                                        tabindex="-1"
                                        title="Toggle minus"
                                        {{ $isSuhuDagingFilled ? 'disabled' : '' }}
                                    >±</button>

                                    <input
                                        type="text"
                                        inputmode="decimal"
                                        class="form-control suhu-number-input @error('suhu_daging') is-invalid @enderror"
                                        id="suhu_daging"
                                        name="suhu_daging"
                                        value="{{ old('suhu_daging', $inspection->suhu_daging ?? '') }}"
                                        {{ $isSuhuDagingFilled ? 'readonly' : '' }}
                                    >
                                </div>

                                @error('suhu_daging')
                                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="keterangan" class="form-label">Keterangan (Optional)</label>
                                <textarea
                                    class="form-control @error('keterangan') is-invalid @enderror"
                                    id="keterangan"
                                    name="keterangan"
                                    rows="3"
                                    {{ $inspection->keterangan ? 'readonly' : '' }}
                                >{{ old('keterangan', $inspection->keterangan) }}</textarea>

                                @error('keterangan')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="d-flex flex-wrap justify-content-between gap-2 mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle"></i> Update Pemeriksaan
                    </button>

                    <a href="{{ route('inspections.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('.select2').each(function () {
            const isDisabled = $(this).is(':disabled');

            $(this).select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketik untuk mencari...',
                allowClear: !isDisabled,
                dropdownAutoWidth: true,
                width: '100%'
            });
        });
    });

    document.querySelectorAll('input[type="number"][max]').forEach(function (input) {
        input.addEventListener('input', function () {
            const max = parseFloat(this.getAttribute('max'));

            if (this.value !== '' && parseFloat(this.value) > max) {
                this.value = max;
            }
        });
    });

    // Input suhu mendukung nilai minus dan desimal.
    document.querySelectorAll('.suhu-number-input').forEach(function (input) {
        input.addEventListener('input', function () {
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

    // Tombol toggle tanda minus.
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-toggle-minus');

        if (!btn || btn.disabled) return;

        const input = btn.closest('.input-group')?.querySelector('input');

        if (!input || input.readOnly || input.disabled) return;

        input.value = input.value.startsWith('-')
            ? input.value.slice(1)
            : '-' + input.value;

        input.dispatchEvent(new Event('input'));
        input.focus();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('product-details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0;

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[char];
            });
        }

        function renderDetailForm(data = null) {
            const i = detailIndex;
            const isReadOnly = data !== null;
            const readOnlyAttr = isReadOnly
                ? 'readonly style="background-color:#e9ecef;"'
                : '';

            const deleteBtn = isReadOnly
                ? ''
                : `<button type="button" class="btn btn-danger btn-sm remove-detail-btn">
                       <i class="bi bi-trash"></i> Hapus
                   </button>`;

            const kodeBatch = escapeHtml(data?.kode_batch ?? '');
            const exp = escapeHtml(data?.exp ?? '');
            const jumlah = escapeHtml(data?.jumlah ?? '');
            const jumlahSampel = escapeHtml(data?.jumlah_sampel ?? '');
            const jumlahReject = escapeHtml(data?.jumlah_reject ?? '');

            const newDetail = document.createElement('div');
            newDetail.classList.add('product-detail-item', 'border', 'p-3', 'p-md-4', 'mb-3');

            newDetail.innerHTML = `
                <div class="item-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Item #${i + 1}</h6>
                        <span class="badge ${isReadOnly ? 'bg-success' : 'bg-primary'} text-white">
                            ${isReadOnly ? 'Tersimpan' : 'Baru'}
                        </span>
                    </div>
                    ${deleteBtn}
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode Batch</label>
                        <input
                            type="text"
                            name="details[${i}][kode_batch]"
                            class="form-control"
                            value="${kodeBatch}"
                            required
                            ${readOnlyAttr}
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">EXP Date</label>
                        <input
                            type="date"
                            name="details[${i}][exp]"
                            class="form-control"
                            value="${exp}"
                            required
                            ${readOnlyAttr}
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jumlah Barang</label>
                        <input
                            type="number"
                            step="0.01"
                            name="details[${i}][jumlah]"
                            class="form-control"
                            value="${jumlah}"
                            required
                            ${readOnlyAttr}
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jumlah Sampel</label>
                        <input
                            type="number"
                            step="0.01"
                            name="details[${i}][jumlah_sampel]"
                            class="form-control"
                            value="${jumlahSampel}"
                            required
                            ${readOnlyAttr}
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jumlah Reject</label>
                        <input
                            type="number"
                            step="0.01"
                            name="details[${i}][jumlah_reject]"
                            class="form-control"
                            value="${jumlahReject}"
                            required
                            ${readOnlyAttr}
                        >
                    </div>
                </div>
            `;

            container.appendChild(newDetail);
            detailIndex++;
        }

        if (addBtn && container) {
            addBtn.addEventListener('click', function () {
                renderDetailForm(null);
            });
        }

        if (container) {
            container.addEventListener('click', function (e) {
                const removeBtn = e.target.closest('.remove-detail-btn');

                if (removeBtn) {
                    removeBtn.closest('.product-detail-item').remove();
                }
            });

            try {
                const existingDetails = JSON.parse(container.dataset.details || '[]');

                if (existingDetails.length > 0) {
                    existingDetails.forEach(function (detail) {
                        renderDetailForm(detail);
                    });
                } else {
                    renderDetailForm(null);
                }
            } catch (error) {
                console.error('Gagal parse data detail:', error);
                renderDetailForm(null);
            }
        }

        // Tombol OK/Not OK.
        document.querySelectorAll('.btn-check-group').forEach(function (group) {
            group.addEventListener('click', function (e) {
                const button = e.target.closest('button.btn');

                if (!button || button.disabled) return;

                const value = button.dataset.value;
                const targetInputId = button.dataset.targetInput;

                if (!targetInputId) return;

                const targetInput = document.querySelector(targetInputId);

                if (targetInput) {
                    targetInput.value = value;
                }

                group.querySelectorAll('button.btn').forEach(function (btn) {
                    btn.classList.remove('btn-success', 'btn-danger');
                    btn.classList.add('btn-outline-secondary');
                });

                button.classList.remove('btn-outline-secondary');
                button.classList.add(value === '1' ? 'btn-success' : 'btn-danger');
            });
        });
    });
</script>
@endpush