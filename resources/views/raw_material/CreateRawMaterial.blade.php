{{-- resources/views/raw_material/CreateRawMaterial.blade.php --}}

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
            margin-bottom: .4rem;
        }

        .form-control,
        .form-select {
            width: 100%;
            min-height: 38px;
            border-radius: 8px;
        }

        .card {
            border-radius: 10px;
        }

        .card-header {
            padding: .75rem 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: .3rem .75rem;
            border: 1px solid #ced4da;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 8px !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .15);
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-right: 30px !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-check-group {
            display: flex;
            width: 100%;
        }

        .btn-check-group .btn {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            white-space: nowrap;
            font-weight: 500;
            padding: .45rem .5rem;
        }

        .product-detail-item {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
            border-radius: 10px !important;
        }

        .product-detail-item > .d-flex {
            padding-bottom: .75rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .product-detail-item h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
        }

        .condition-field {
            height: 100%;
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #fff;
        }

        .condition-field .form-label {
            display: block;
            margin-bottom: .65rem;
        }

        .condition-field .btn-check-group {
            width: 100%;
        }

        .condition-field .btn {
            min-height: 38px;
        }

        .input-group .form-control {
            min-width: 0;
        }

        .satuan-text {
            min-width: 40px;
            justify-content: center;
            background-color: #f1f3f5;
        }

        .form-check-label {
            overflow-wrap: anywhere;
        }

        @media (max-width: 767.98px) {
            .card-body {
                padding: .85rem;
            }

            .condition-field {
                padding: .75rem;
            }

            .btn-check-group .btn {
                padding: .4rem .25rem;
                font-size: .85rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-0">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 p-md-4 p-lg-5">

                <h4 class="mb-1">
                    <i class="bi bi-box-seam"></i>
                    Tambah Pemeriksaan Bahan Baku
                </h4>

                <p class="text-muted mb-4">
                    Isi detail formulir pemeriksaan bahan baku di bawah ini.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops! Ada beberapa masalah dengan input Anda:</strong>
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

                <form action="{{ route('inspections.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- CARD INFORMASI UMUM --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>
                                <i class="bi bi-info-circle-fill"></i>
                                Informasi Umum
                            </strong>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="dibuat_oleh" class="form-label">Dibuat Oleh</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="dibuat_oleh"
                                        value="{{ auth()->user()->name }}"
                                        readonly
                                    >
                                </div>

                                <div class="col-12">
                                    <label for="setup_kedatangan" class="form-label">Waktu Kedatangan</label>
                                    <input
                                        type="datetime-local"
                                        class="form-control @error('setup_kedatangan') is-invalid @enderror"
                                        id="setup_kedatangan"
                                        name="setup_kedatangan"
                                        value="{{ old('setup_kedatangan') }}"
                                        required
                                    >
                                    @error('setup_kedatangan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="bahan_baku" class="form-label">Bahan Baku</label>
                                    <select
                                        class="form-select select2 @error('bahan_baku') is-invalid @enderror"
                                        id="bahan_baku"
                                        name="bahan_baku"
                                        required
                                    >
                                        <option></option>
                                        @foreach ($masterBahanBaku as $bahan)
                                            <option
                                                value="{{ $bahan->nama_bahan_baku }}"
                                                data-satuan="{{ $bahan->satuan }}"
                                                {{ old('bahan_baku') == $bahan->nama_bahan_baku ? 'selected' : '' }}
                                            >
                                                {{ $bahan->nama_bahan_baku }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bahan_baku')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="supplier" class="form-label">Supplier</label>
                                    <select
                                        class="form-select select2 @error('supplier') is-invalid @enderror"
                                        id="supplier"
                                        name="supplier"
                                        required
                                    >
                                        <option></option>
                                        @foreach ($suppliers as $sup)
                                            <option
                                                value="{{ $sup->nama_supplier }}"
                                                {{ old('supplier', $inspection->supplier ?? '') == $sup->nama_supplier ? 'selected' : '' }}
                                            >
                                                {{ $sup->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD DETAIL PRODUK --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>
                                    <i class="bi bi-list-nested"></i>
                                    Detail Produk
                                </strong>

                                <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-plus-lg"></i>
                                    Tambah Item
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="product-details-container"></div>
                        </div>
                    </div>

                    {{-- CARD KONDISI FISIK --}}
                    <div class="card mb-4">
                        <div class="card-header">
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
                                        <div class="condition-field">
                                            <label class="form-label">{{ $label }}</label>

                                            <input
                                                type="hidden"
                                                name="{{ $name }}"
                                                id="{{ $name }}"
                                                value="{{ old($name) }}"
                                            >

                                            <div class="btn-group btn-check-group" role="group">
                                                <button
                                                    type="button"
                                                    class="btn {{ old($name) === '1' ? 'btn-success' : 'btn-outline-secondary' }}"
                                                    data-value="1"
                                                    data-target-input="#{{ $name }}"
                                                >
                                                    <i class="bi bi-check-lg"></i>
                                                    OK
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn {{ old($name) === '0' ? 'btn-danger' : 'btn-outline-secondary' }}"
                                                    data-value="0"
                                                    data-target-input="#{{ $name }}"
                                                >
                                                    <i class="bi bi-x-lg"></i>
                                                    Not OK
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- CARD ANALISA PRODUK --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong>
                                <i class="bi bi-search"></i>
                                Analisa Produk
                            </strong>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3 col-6">
                                    <label for="analisa_ka_ffa" class="form-label">K.A / FFA</label>
                                    <input
                                        type="text"
                                        class="form-control @error('analisa_ka_ffa') is-invalid @enderror"
                                        id="analisa_ka_ffa"
                                        name="analisa_ka_ffa"
                                        value="{{ old('analisa_ka_ffa', $inspection->analisa_ka_ffa ?? '') }}"
                                        oninput="this.value = this.value.replace(/[^0-9.-]/g, '')"
                                    >
                                    @error('analisa_ka_ffa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-3 col-6">
                                    <label class="form-label">Logo Halal</label>
                                    <input
                                        type="hidden"
                                        name="analisa_logo_halal"
                                        id="analisa_logo_halal"
                                        value="{{ old('analisa_logo_halal') }}"
                                    >

                                    <div class="btn-group btn-check-group" role="group">
                                        <button
                                            type="button"
                                            class="btn {{ old('analisa_logo_halal') === '1' ? 'btn-success' : 'btn-outline-secondary' }}"
                                            data-value="1"
                                            data-target-input="#analisa_logo_halal"
                                        >
                                            <i class="bi bi-check-lg"></i>
                                            OK
                                        </button>

                                        <button
                                            type="button"
                                            class="btn {{ old('analisa_logo_halal') === '0' ? 'btn-danger' : 'btn-outline-secondary' }}"
                                            data-value="0"
                                            data-target-input="#analisa_logo_halal"
                                        >
                                            <i class="bi bi-x-lg"></i>
                                            Not OK
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6"></div>

                                <div class="col-md-6">
                                    <label for="analisa_negara_asal" class="form-label">Negara Asal Produsen</label>
                                    <input
                                        type="text"
                                        class="form-control @error('analisa_negara_asal') is-invalid @enderror"
                                        id="analisa_negara_asal"
                                        name="analisa_negara_asal"
                                        value="{{ old('analisa_negara_asal') }}"
                                        required
                                    >
                                    @error('analisa_negara_asal')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="analisa_produsen" class="form-label">Nama Produsen</label>
                                    <input
                                        type="text"
                                        class="form-control @error('analisa_produsen') is-invalid @enderror"
                                        id="analisa_produsen"
                                        name="analisa_produsen"
                                        value="{{ old('analisa_produsen') }}"
                                        required
                                    >
                                    @error('analisa_produsen')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD DOKUMEN PENDUKUNG --}}
                    <div class="card mb-4">
                        <div class="card-header">
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
                                        <label class="form-label d-block small mb-1" for="dokumen_halal_berlaku">
                                            Status Berlaku?
                                        </label>

                                        <input
                                            type="hidden"
                                            name="dokumen_halal_berlaku"
                                            id="dokumen_halal_berlaku"
                                            value="{{ old('dokumen_halal_berlaku') }}"
                                        >

                                        <div class="btn-group btn-check-group" role="group">
                                            <button
                                                type="button"
                                                class="btn btn-sm {{ old('dokumen_halal_berlaku') === '1' ? 'btn-success' : 'btn-outline-secondary' }}"
                                                data-value="1"
                                                data-target-input="#dokumen_halal_berlaku"
                                            >
                                                <i class="bi bi-check-lg"></i>
                                                Berlaku (OK)
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-sm {{ old('dokumen_halal_berlaku') === '0' ? 'btn-danger' : 'btn-outline-secondary' }}"
                                                data-value="0"
                                                data-target-input="#dokumen_halal_berlaku"
                                            >
                                                <i class="bi bi-x-lg"></i>
                                                Tidak (X)
                                            </button>
                                        </div>
                                    </div>

                                    <label class="form-label small mb-1" for="dokumen_halal_file">
                                        Upload File Halal
                                    </label>
                                    <input
                                        type="file"
                                        class="form-control form-control-sm @error('dokumen_halal_file') is-invalid @enderror"
                                        name="dokumen_halal_file"
                                        id="dokumen_halal_file"
                                    >
                                    @error('dokumen_halal_file')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="dokumen_coa_file" class="form-label">Dokumen COA</label>
                                    <input
                                        class="form-control form-control-sm @error('dokumen_coa_file') is-invalid @enderror mt-3"
                                        type="file"
                                        id="dokumen_coa_file"
                                        name="dokumen_coa_file"
                                    >
                                    @error('dokumen_coa_file')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD TRANSPORTER --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong>
                                <i class="bi bi-person-badge"></i>
                                Informasi Transporter
                            </strong>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="nopol_mobil" class="form-label">Nopol Mobil</label>
                                    <input
                                        type="text"
                                        class="form-control @error('nopol_mobil') is-invalid @enderror"
                                        id="nopol_mobil"
                                        name="nopol_mobil"
                                        value="{{ old('nopol_mobil') }}"
                                        required
                                    >
                                    @error('nopol_mobil')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="suhu_mobil" class="form-label">Suhu Mobil (°C)</label>
                                    <div class="input-group">
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary btn-toggle-minus"
                                            tabindex="-1"
                                            title="Toggle minus"
                                        >±</button>

                                        <input
                                            type="text"
                                            inputmode="decimal"
                                            class="form-control suhu-number-input @error('suhu_mobil') is-invalid @enderror"
                                            id="suhu_mobil"
                                            name="suhu_mobil"
                                            value="{{ old('suhu_mobil', $inspection->suhu_mobil ?? '') }}"
                                        >
                                    </div>
                                    @error('suhu_mobil')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
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
                                            'Tidak ada produk non halal',
                                        ];

                                        $selectedKondisi = collect(
                                            old(
                                                'kondisi_mobil',
                                                isset($inspection)
                                                    ? explode(',', $inspection->kondisi_mobil ?? '')
                                                    : [],
                                            ),
                                        )->filter()->toArray();
                                    @endphp

                                    <div class="row g-2 @error('kondisi_mobil') is-invalid @enderror">
                                        @foreach ($options as $item)
                                            @php
                                                $id = 'kondisi_' . \Illuminate\Support\Str::slug($item, '_');
                                            @endphp

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
                                    <input
                                        type="text"
                                        class="form-control @error('do_po') is-invalid @enderror"
                                        id="do_po"
                                        name="do_po"
                                        value="{{ old('do_po') }}"
                                        required
                                    >
                                    @error('do_po')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="no_segel" class="form-label">No. Segel</label>
                                    <input
                                        type="text"
                                        class="form-control @error('no_segel') is-invalid @enderror"
                                        id="no_segel"
                                        name="no_segel"
                                        value="{{ old('no_segel') }}"
                                        required
                                    >
                                    @error('no_segel')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="suhu_daging" class="form-label">Suhu Daging/Bahan (°C)</label>
                                    <div class="input-group">
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary btn-toggle-minus"
                                            tabindex="-1"
                                            title="Toggle minus"
                                        >±</button>

                                        <input
                                            type="text"
                                            inputmode="decimal"
                                            class="form-control suhu-number-input @error('suhu_daging') is-invalid @enderror"
                                            id="suhu_daging"
                                            name="suhu_daging"
                                            value="{{ old('suhu_daging') }}"
                                        >
                                    </div>
                                    @error('suhu_daging')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="keterangan" class="form-label">Keterangan (Optional)</label>
                                    <textarea
                                        class="form-control @error('keterangan') is-invalid @enderror"
                                        id="keterangan"
                                        name="keterangan"
                                        rows="3"
                                    >{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-save"></i>
                            Simpan Pemeriksaan
                        </button>

                        <a href="{{ route('inspections.index') }}" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
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
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketik untuk mencari...',
                allowClear: true,
                dropdownAutoWidth: true,
                width: '100%'
            });
        });
    </script>

    <script>
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
            const container = document.getElementById('product-details-container');
            const addBtn = document.getElementById('add-detail-btn');
            const bahanBakuSelect = document.getElementById('bahan_baku');

            let detailIndex = 0;

            function updateSatuan() {
                let satuan = '-';
                const selectedOption = $('#bahan_baku option:selected');

                if (selectedOption.length > 0) {
                    satuan = selectedOption.data('satuan') || '-';
                }

                document.querySelectorAll('.satuan-text').forEach(el => {
                    el.textContent = satuan;
                });
            }

            function addDetailForm(oldData = {}) {
                const newDetail = document.createElement('div');

                newDetail.classList.add(
                    'product-detail-item',
                    'border',
                    'p-3',
                    'mb-3',
                    'rounded',
                    'shadow-sm'
                );

                newDetail.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Item #${detailIndex + 1}</h5>

                        <button type="button" class="btn btn-danger btn-sm remove-detail-btn">
                            <i class="bi bi-trash"></i>
                            Hapus
                        </button>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kode Batch</label>
                            <input
                                type="text"
                                name="details[${detailIndex}][kode_batch]"
                                class="form-control"
                                required
                                value="${oldData.kode_batch || ''}"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">EXP Date</label>
                            <input
                                type="date"
                                name="details[${detailIndex}][exp]"
                                class="form-control"
                                required
                                value="${oldData.exp || ''}"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jumlah Barang</label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    step="0.01"
                                    name="details[${detailIndex}][jumlah]"
                                    class="form-control"
                                    required
                                    value="${oldData.jumlah || ''}"
                                >
                                <span class="input-group-text satuan-text">-</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jumlah Sampel</label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    step="0.01"
                                    name="details[${detailIndex}][jumlah_sampel]"
                                    class="form-control"
                                    required
                                    value="${oldData.jumlah_sampel || ''}"
                                >
                                <span class="input-group-text satuan-text">-</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jumlah Reject</label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    step="0.01"
                                    name="details[${detailIndex}][jumlah_reject]"
                                    class="form-control"
                                    required
                                    value="${oldData.jumlah_reject || ''}"
                                >
                                <span class="input-group-text satuan-text">-</span>
                            </div>
                        </div>
                    </div>
                `;

                container.appendChild(newDetail);
                detailIndex++;
            }

            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    addDetailForm();
                    updateSatuan();
                });
            }

            if (container) {
                container.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.remove-detail-btn');

                    if (removeBtn) {
                        removeBtn.closest('.product-detail-item').remove();
                    }
                });

                const oldDetails = @json(old('details', []));

                if (Array.isArray(oldDetails) && oldDetails.length > 0) {
                    oldDetails.forEach(data => {
                        addDetailForm(data);
                    });
                } else {
                    addDetailForm();
                }
            }

            document.querySelectorAll('.btn-check-group').forEach(group => {
                group.addEventListener('click', function(e) {
                    const button = e.target.closest('.btn');

                    if (!button) return;

                    const value = button.dataset.value;
                    const targetInputId = button.dataset.targetInput;

                    if (!targetInputId) return;

                    const targetInput = document.querySelector(targetInputId);

                    if (targetInput) {
                        targetInput.value = value;
                    }

                    group.querySelectorAll('.btn').forEach(btn => {
                        btn.classList.remove('btn-success', 'btn-danger');
                        btn.classList.add('btn-outline-secondary');
                        btn.style.opacity = '0.6';
                    });

                    button.classList.remove('btn-outline-secondary');
                    button.style.opacity = '1';

                    if (value === '1') {
                        button.classList.add('btn-success');
                    } else {
                        button.classList.add('btn-danger');
                    }
                });
            });

            if (bahanBakuSelect) {
                $('#bahan_baku').on('change', updateSatuan);
                updateSatuan();
            }
        });
    </script>
@endpush