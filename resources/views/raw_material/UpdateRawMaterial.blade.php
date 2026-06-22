{{-- resources/views/raw_material/UpdateRawMaterial.blade.php --}}

@extends('layouts.app') 

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    .form-label { font-weight: 600; color: #495057; }
    .form-control, .form-select { border-radius: 8px; }
    
    /* Input Readonly Custom Style */
    .form-control[readonly], .form-select[disabled] {
        background-color: #e9ecef; 
        cursor: not-allowed;
    }

    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
    }
    .select2-container--bootstrap-5 .select2-selection { border-radius: 8px !important; }
    
    /* --- STYLE TOMBOL BARU (NETRAL DEFAULT) --- */
    .btn-check-group .btn {
        display: flex; align-items: center; justify-content: center; gap: 0.35rem;
        transition: all 0.2s ease;
    }
    /* Saat aktif (Solid) */
    .btn-check-group .btn-success, .btn-check-group .btn-danger {
        font-weight: 600; border: none;
    }
    /* Saat tidak aktif (Netral/Abu-abu) */
    .btn-check-group .btn-outline-secondary {
        color: #6c757d; border-color: #ced4da;
    }
    .btn-check-group .btn-outline-secondary:hover {
        background-color: #e9ecef; color: #495057;
    }
    /* Saat Disabled */
    .btn-check-group .btn[disabled] {
        opacity: 0.6; cursor: not-allowed;
    }

    .product-detail-item { background-color: #fdfdfd; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            <h4 class="mb-1"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Bahan Baku</h4>
            <p class="text-muted mb-4">Field yang sudah terisi sebelumnya tidak dapat diedit.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Ada masalah input:</strong>
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
                                <input type="datetime-local" 
                                    class="form-control @error('setup_kedatangan') is-invalid @enderror" 
                                    id="setup_kedatangan" 
                                    name="setup_kedatangan" 
                                    value="{{ old('setup_kedatangan', $inspection->setup_kedatangan ? $inspection->setup_kedatangan->format('Y-m-d\TH:i') : '') }}" 
                                    required
                                    {{ $inspection->setup_kedatangan ? 'readonly' : '' }}>
                                @error('setup_kedatangan') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="bahan_baku" class="form-label">Bahan Baku</label>
                                @php $isBahanBakuFilled = !empty($inspection->bahan_baku); @endphp
                                
                                <select class="form-select select2 @error('bahan_baku') is-invalid @enderror" 
                                    id="bahan_baku" 
                                    name="bahan_baku" 
                                    required 
                                    {{ $isBahanBakuFilled ? 'disabled' : '' }}>
                                    
                                    <option></option> {{-- Placeholder --}}
                                    
                                    {{-- Looping Data Master Bahan Baku --}}
                                    @foreach ($masterBahanBaku as $bahan)
                                        <option value="{{ $bahan->nama_bahan_baku }}" {{ old('bahan_baku', $inspection->bahan_baku) == $bahan->nama_bahan_baku ? 'selected' : '' }}>
                                            {{ $bahan->nama_bahan_baku }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                {{-- Hidden input agar nilai tetap terkirim saat disubmit meski select disabled --}}
                                @if($isBahanBakuFilled)
                                    <input type="hidden" name="bahan_baku" value="{{ $inspection->bahan_baku }}">
                                @endif
                                
                                @error('bahan_baku') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="supplier" class="form-label">Supplier</label>
                                
                                {{-- Cek apakah data supplier sudah terisi sebelumnya --}}
                                @php $isSupplierFilled = !empty($inspection->supplier); @endphp
                                
                                <select class="form-select select2 @error('supplier') is-invalid @enderror" 
                                    id="supplier" 
                                    name="supplier" 
                                    required
                                    {{ $isSupplierFilled ? 'disabled' : '' }}>
                                    
                                    <option></option> {{-- Placeholder untuk Select2 --}}
                                    
                                    {{-- Looping data supplier dari controller --}}
                                    @foreach ($suppliers as $sup)
                                        <option value="{{ $sup->nama_supplier }}" {{ old('supplier', $inspection->supplier) == $sup->nama_supplier ? 'selected' : '' }}>
                                            {{ $sup->nama_supplier }}
                                        </option>
                                    @endforeach
                                    
                                </select>
                                
                                {{-- Input hidden wajib ada karena <select> yang didisable tidak akan mengirimkan value ke controller --}}
                                @if($isSupplierFilled)
                                    <input type="hidden" name="supplier" value="{{ $inspection->supplier }}">
                                @endif
                                
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
                
                {{-- CARD KONDISI FISIK --}}
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
                                @php $isFilled = !is_null($inspection->$name); @endphp
                                
                                <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $inspection->$name ? '1' : '0') }}">
                                <div class="btn-group btn-check-group w-100" role="group">
                                    {{-- UPDATE UX: Gunakan btn-outline-secondary untuk default --}}
                                    <button type="button" 
                                        class="btn {{ old($name, $inspection->$name ? '1' : '0') === '1' ? 'btn-success' : 'btn-outline-secondary' }}" 
                                        data-value="1" 
                                        data-target-input="#{{ $name }}"
                                        {{ $isFilled ? 'disabled' : '' }}>
                                        <i class="bi bi-check-lg"></i> OK
                                    </button>
                                    <button type="button" 
                                        class="btn {{ old($name, $inspection->$name ? '1' : '0') === '0' ? 'btn-danger' : 'btn-outline-secondary' }}" 
                                        data-value="0" 
                                        data-target-input="#{{ $name }}"
                                        {{ $isFilled ? 'disabled' : '' }}>
                                        <i class="bi bi-x-lg"></i> Not OK
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- CARD ANALISA PRODUK --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-search"></i> Analisa Produk</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <label for="analisa_ka_ffa" class="form-label d-block">K.A / FFA</label>
                                @php $isKaFfaFilled = !is_null($inspection->analisa_ka_ffa); @endphp
                                <input type="number" step="0.01" 
                                    class="form-control @error('analisa_ka_ffa') is-invalid @enderror" 
                                    id="analisa_ka_ffa" 
                                    name="analisa_ka_ffa" 
                                    value="{{ old('analisa_ka_ffa', $inspection->analisa_ka_ffa ?? '') }}" 
                                    min="0"
                                    {{ $isKaFfaFilled ? 'readonly' : '' }}>
                                @error('analisa_ka_ffa') 
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
                                @enderror
                            </div>

                            {{-- Logo Halal --}}
                            <div class="col-md-3 col-6">
                                <label class="form-label d-block">Logo Halal</label>
                                @php $isHalalFilled = !is_null($inspection->analisa_logo_halal); @endphp
                                <input type="hidden" name="analisa_logo_halal" id="analisa_logo_halal" value="{{ old('analisa_logo_halal', $inspection->analisa_logo_halal ? '1' : '0') }}">
                                <div class="btn-group btn-check-group w-100" role="group">
                                    <button type="button" class="btn {{ old('analisa_logo_halal', $inspection->analisa_logo_halal ? '1' : '0') === '1' ? 'btn-success' : 'btn-outline-secondary' }}" data-value="1" data-target-input="#analisa_logo_halal" {{ $isHalalFilled ? 'disabled' : '' }}><i class="bi bi-check-lg"></i> OK</button>
                                    <button type="button" class="btn {{ old('analisa_logo_halal', $inspection->analisa_logo_halal ? '1' : '0') === '0' ? 'btn-danger' : 'btn-outline-secondary' }}" data-value="0" data-target-input="#analisa_logo_halal" {{ $isHalalFilled ? 'disabled' : '' }}><i class="bi bi-x-lg"></i> Not OK</button>
                                </div>
                            </div>

                            <div class="col-md-6"></div>

                            <div class="col-md-6">
                                <label for="analisa_negara_asal" class="form-label">Negara Asal Produsen</label>
                                <input type="text" class="form-control" id="analisa_negara_asal" name="analisa_negara_asal" 
                                    value="{{ old('analisa_negara_asal', $inspection->analisa_negara_asal) }}" 
                                    required
                                    {{ $inspection->analisa_negara_asal ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="analisa_produsen" class="form-label">Nama Produsen</label>
                                <input type="text" class="form-control" id="analisa_produsen" name="analisa_produsen" 
                                    value="{{ old('analisa_produsen', $inspection->analisa_produsen) }}" 
                                    required
                                    {{ $inspection->analisa_produsen ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DOKUMEN PENDUKUNG (UPDATED UX) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-file-earmark-text"></i> Dokumen Pendukung</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Dokumen Halal</label>
                                <div class="mb-2">
                                    <label class="form-label d-block small mb-1">Status Berlaku?</label>
                                    @php $isDokHalalFilled = !is_null($inspection->dokumen_halal_berlaku); @endphp
                                    <input type="hidden" name="dokumen_halal_berlaku" id="dokumen_halal_berlaku" value="{{ old('dokumen_halal_berlaku', $inspection->dokumen_halal_berlaku ? '1' : '0') }}">
                                    
                                    {{-- Tombol Kecil & W-50 --}}
                                    <div class="btn-group btn-check-group w-50" role="group">
                                        <button type="button" class="btn btn-sm {{ old('dokumen_halal_berlaku', $inspection->dokumen_halal_berlaku ? '1' : '0') === '1' ? 'btn-success' : 'btn-outline-secondary' }}" data-value="1" data-target-input="#dokumen_halal_berlaku" {{ $isDokHalalFilled ? 'disabled' : '' }}><i class="bi bi-check-lg"></i> Berlaku (OK)</button>
                                        <button type="button" class="btn btn-sm {{ old('dokumen_halal_berlaku', $inspection->dokumen_halal_berlaku ? '1' : '0') === '0' ? 'btn-danger' : 'btn-outline-secondary' }}" data-value="0" data-target-input="#dokumen_halal_berlaku" {{ $isDokHalalFilled ? 'disabled' : '' }}><i class="bi bi-x-lg"></i> Tidak (X)</button>
                                    </div>
                                </div>
                                
                                @if($inspection->dokumen_halal_file)
                                    <div class="alert alert-light border py-2">
                                        <i class="bi bi-file-check"></i> File: 
                                        <a href="{{ asset('storage/' . $inspection->dokumen_halal_file) }}" target="_blank">Lihat</a>
                                        <span class="text-muted small ms-2">(Terkunci)</span>
                                    </div>
                                @else
                                    <label class="form-label small mb-1" for="dokumen_halal_file">Upload File Halal Baru</label>
                                    <input type="file" class="form-control form-control-sm" name="dokumen_halal_file" id="dokumen_halal_file">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Dokumen COA</label>
                                {{-- Spacer dihapus agar input naik --}}
                                @if($inspection->dokumen_coa_file)
                                    <div class="alert alert-light border py-2 mt-4"> {{-- mt-4 untuk sejajar visual dgn kiri --}}
                                        <i class="bi bi-file-check"></i> File: 
                                        <a href="{{ asset('storage/' . $inspection->dokumen_coa_file) }}" target="_blank">Lihat</a>
                                        <span class="text-muted small ms-2">(Terkunci)</span>
                                    </div>
                                @else
                                    {{-- Input langsung dibawah label --}}
                                    <div class="mt-4">
                                        <label for="dokumen_coa_file" class="visually-hidden">Upload File COA</label>
                                        <input class="form-control form-control-sm" type="file" id="dokumen_coa_file" name="dokumen_coa_file">
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
                                <input type="text" class="form-control" id="nopol_mobil" name="nopol_mobil" 
                                    value="{{ old('nopol_mobil', $inspection->nopol_mobil) }}" 
                                    required
                                    {{ $inspection->nopol_mobil ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-4">
                                <label for="suhu_mobil" class="form-label">Suhu Mobil (°C)</label>
                                <input type="number" step="0.01" 
                                    class="form-control @error('suhu_mobil') is-invalid @enderror" 
                                    id="suhu_mobil" 
                                    name="suhu_mobil" 
                                    value="{{ old('suhu_mobil', $inspection->suhu_mobil ?? '') }}" 
                                    required 
                                    min="-50" max="50"
                                    {{ $inspection->suhu_mobil ? 'readonly' : '' }}>
                                @error('suhu_mobil') 
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> 
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
                                <input type="text" class="form-control" id="do_po" name="do_po" 
                                    value="{{ old('do_po', $inspection->do_po) }}" 
                                    required
                                    {{ $inspection->do_po ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="no_segel" class="form-label">No. Segel</label>
                                <input type="text" class="form-control" id="no_segel" name="no_segel" 
                                    value="{{ old('no_segel', $inspection->no_segel) }}" 
                                    required
                                    {{ $inspection->no_segel ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="suhu_daging" class="form-label">Suhu Daging/Bahan (°C)</label>
                                <input type="number" step="0.01" class="form-control" id="suhu_daging" name="suhu_daging" 
                                    value="{{ old('suhu_daging', $inspection->suhu_daging) }}" 
                                    required min="-50" max="50"
                                    {{ $inspection->suhu_daging ? 'readonly' : '' }}>
                            </div>
                            <div class="col-12">
                                <label for="keterangan" class="form-label">Keterangan (Optional)</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                {{ $inspection->keterangan ? 'readonly' : '' }}>{{ old('keterangan', $inspection->keterangan) }}</textarea>
                            </div>
                         </div>
                    </div>
                </div>

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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: "bootstrap-5",
            placeholder: "Ketik untuk mencari...",
            allowClear: true,
            dropdownAutoWidth: true,
            width: '49%'
        });
    });

    document.querySelectorAll('input[type="number"][max]').forEach(function(input) {
        input.addEventListener('input', function() {
            const max = parseFloat(this.getAttribute('max'));
            if (parseFloat(this.value) > max) {
                this.value = max;
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        
        const container = document.getElementById('product-details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0; 

        function renderDetailForm(data = null) {
            const i = detailIndex; 
            
            // Cek jika data ada (berarti data lama/saved), kita kunci inputnya
            const isReadOnly = (data !== null); 
            const readOnlyAttr = isReadOnly ? 'readonly style="background-color: #e9ecef;"' : '';
            // Tombol hapus disembunyikan jika data sudah tersimpan (opsional, tergantung kebutuhan)
            const deleteBtn = isReadOnly ? '' : `<button type="button" class="btn btn-danger btn-sm remove-detail-btn"><i class="bi bi-trash"></i> Hapus</button>`;

            const kodeBatch = data ? (data.kode_batch || '') : '';
            const tglProduksi = data ? (data.tanggal_produksi || '') : '';
            const exp = data ? (data.exp || '') : '';
            const jumlah = data ? (data.jumlah || '') : '';
            const jumlahSampel = data ? (data.jumlah_sampel || '') : '';
            const jumlahReject = data ? (data.jumlah_reject || '') : '';

            const newDetail = document.createElement('div');
            newDetail.classList.add('product-detail-item', 'border', 'p-3', 'mb-3', 'rounded', 'shadow-sm');
            
            newDetail.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5>Item #${i + 1} ${isReadOnly ? '<span class="badge bg-success">Tersimpan</span>' : '<span class="badge bg-primary">Baru</span>'}</h5>
                    ${deleteBtn}
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kode Batch</label>
                        <input type="text" name="details[${i}][kode_batch]" class="form-control" value="${kodeBatch}" required ${readOnlyAttr}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Produksi</label>
                        <input type="date" name="details[${i}][tanggal_produksi]" class="form-control" value="${tglProduksi}" required ${readOnlyAttr}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">EXP Date</label>
                        <input type="date" name="details[${i}][exp]" class="form-control" value="${exp}" required ${readOnlyAttr}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Barang</label>
                        <input type="number" step="0.01" name="details[${i}][jumlah]" class="form-control" value="${jumlah}" required ${readOnlyAttr}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Sampel</label>
                        <input type="number" step="0.01" name="details[${i}][jumlah_sampel]" class="form-control" value="${jumlahSampel}" required ${readOnlyAttr}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Reject</label>
                        <input type="number" step="0.01" name="details[${i}][jumlah_reject]" class="form-control" value="${jumlahReject}" required ${readOnlyAttr}>
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

        // --- SCRIPT UPDATE: LOGIKA TOMBOL NETRAL KE BERWARNA ---
        const buttonGroups = document.querySelectorAll('.btn-check-group');

        buttonGroups.forEach(group => {
            group.addEventListener('click', function(e) {
                if (e.target.matches('.btn')) {
                    // Cek jika tombol disabled, jangan lakukan apa-apa
                    if (e.target.hasAttribute('disabled')) return;

                    const button = e.target;
                    const value = button.dataset.value; 
                    const targetInputId = button.dataset.targetInput; 
                    
                    if (!targetInputId) return; 
                    
                    const targetInput = document.querySelector(targetInputId);

                    if (targetInput) {
                        targetInput.value = value;
                    }

                    // Reset semua tombol ke 'Netral' (Outline Secondary)
                    const buttonsInGroup = group.querySelectorAll('button');
                    buttonsInGroup.forEach(btn => {
                        btn.classList.remove('btn-success', 'btn-danger'); // Hapus warna
                        btn.classList.add('btn-outline-secondary'); // Tambah netral
                    });

                    // Set warna pada tombol yang diklik
                    button.classList.remove('btn-outline-secondary'); // Hapus netral
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