@extends('layouts.app')

@section('title', 'Tambah Pemeriksaan Loading')

@push('styles')
{{-- Menambahkan link untuk Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
{{-- 1. Include Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- 2. Theme for Select2 to match Bootstrap 5 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    /* Mengubah font utama untuk tampilan yang lebih modern */
    body {
        background-color: #f8f9fa;
        /* Opsional: font yang lebih modern jika Anda memilikinya */
        /* font-family: 'Inter', sans-serif; */
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
    
    /* Memastikan Select2 sesuai dengan tinggi Bootstrap */
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px); /* Sesuaikan dengan tinggi form-control Anda */
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
    }
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Style untuk item dinamis */
    .dynamic-item-card {
        background-color: #fdfdfd;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            <h4 class="mb-1"><i class="bi bi-truck-ramp-box"></i> Tambah Pemeriksaan Loading</h4>
            <p class="text-muted mb-4">Isi detail formulir pemeriksaan loading di bawah ini.</p>

            {{-- Tampilkan error jika ada --}}
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

            <form action="{{ route('loading-produks.store') }}" method="POST">
                @csrf

                {{-- CARD INFORMASI UTAMA & KENDARAAN --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Utama & Kendaraan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Informasi Utama --}}
                            <div class="col-md-4">
                                <label for="tanggal" class="form-label">Hari/Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                       value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="shift" class="form-label">Shift <span class="text-danger">*</span></label>
                                <select class="form-select select2-static" id="shift" name="shift" required>
                                    <option value="Pagi" @selected(old('shift') == 'Pagi')>Pagi</option>
                                    <option value="Malam" @selected(old('shift') == 'Malam')>Malam</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="jenis_aktivitas" class="form-label">Jenis Aktivitas <span class="text-danger">*</span></label>
                                <select class="form-select select2-static" id="jenis_aktivitas" name="jenis_aktivitas" required>
                                    <option value="Loading" @selected(old('jenis_aktivitas') == 'Loading')>Loading</option>
                                    <option value="Unloading" @selected(old('jenis_aktivitas') == 'Unloading')>Unloading</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" 
                                       value="{{ old('jam_mulai') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" 
                                       value="{{ old('jam_selesai') }}" required>
                            </div>
                            
                            {{-- Garis Pemisah --}}
                            <div class="col-12"><hr class="my-2"></div>

                            {{-- Informasi Kendaraan --}}
                            <div class="col-md-4">
                                <label for="no_pol_mobil" class="form-label">No. Pol Mobil <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="no_pol_mobil" name="no_pol_mobil" 
                                       value="{{ old('no_pol_mobil') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="nama_supir" class="form-label">Nama Supir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_supir" name="nama_supir" 
                                       value="{{ old('nama_supir') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="ekspedisi" class="form-label">Ekspedisi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ekspedisi" name="ekspedisi" 
                                       value="{{ old('ekspedisi') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="tujuan_asal" class="form-label">Tujuan / Asal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tujuan_asal" name="tujuan_asal" 
                                       value="{{ old('tujuan_asal') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="no_segel" class="form-label">No. Segel</label>
                                <input type="text" class="form-control" id="no_segel" name="no_segel" 
                                       value="{{ old('no_segel') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                                <input type="text" class="form-control" id="jenis_kendaraan" name="jenis_kendaraan" 
                                       value="{{ old('jenis_kendaraan') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD KONDISI & KETERANGAN --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-clipboard2-check"></i> Kondisi Mobil & Keterangan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Kondisi Mobil --}}
                            <div class="col-md-6">
                                <label class="form-label mb-2">Kondisi Mobil (Checklist)</label>
                                <div class="card p-3">
                                    <div class="row">
                                        @php
                                            $kondisiList = [
                                                'bersih' => 'Bersih',
                                                'kering' => 'Kering',
                                                'tidak_bocor' => 'Tidak Bocor',
                                                'tidak_debu' => 'Tidak Berdebu',
                                                'tidak_basah' => 'Tidak Basah',
                                                'bebas_hama' => 'Bebas Hama',
                                                'bebas_noda' => 'Bebas Noda (Karat, cat, tinta)',
                                                'bebas_oli' => 'Bebas Bekas oli di lantai/dinding',
                                                'tidak_ada_non_halal' => 'Tidak ada produk non halal',
                                            ];
                                            $currentKondisi = old('kondisi_mobil', []);
                                        @endphp
                                        @foreach ($kondisiList as $key => $label)
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="kondisi_mobil[]" value="{{ $key }}" id="kondisi_{{ $key }}"
                                                       @checked(in_array($key, $currentKondisi))>
                                                <label class="form-check-label" for="kondisi_{{ $key }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Keterangan & PIC --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="keterangan_total" class="form-label">Keterangan Total (Varian & Jumlah)</label>
                                    <textarea class="form-control" id="keterangan_total" name="keterangan_total" rows="2">{{ old('keterangan_total') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan_umum" class="form-label">Keterangan Umum (Catatan)</label>
                                    <textarea class="form-control" id="keterangan_umum" name="keterangan_umum" rows="2">{{ old('keterangan_umum') }}</textarea>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="pic_qc" class="form-label">PIC QC</label>
                                        <input type="text" class="form-control" id="pic_qc" name="pic_qc" value="{{ old('pic_qc') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pic_warehouse" class="form-label">PIC Warehouse</label>
                                        <input type="text" class="form-control" id="pic_warehouse" name="pic_warehouse" value="{{ old('pic_warehouse') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pic_qc_spv" class="form-label">PIC QC SPV</label>
                                        <input type="text" class="form-control" id="pic_qc_spv" name="pic_qc_spv" value="{{ old('pic_qc_spv') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DETAIL ITEM (DINAMIS) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><i class="bi bi-list-nested"></i> Detail Item Produk <span class="text-danger">*</span></strong>
                            <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Item</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="details-container">
                            {{-- Item dinamis akan ditambahkan di sini oleh JS --}}
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-save"></i> Simpan Data</button>
                    <a href="{{ route('loading-produks.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Batal</a>
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
    // 3. Initialize Select2 untuk dropdown statis
    $(document).ready(function() {
        $('.select2-static').select2({
            theme: "bootstrap-5",
            placeholder: "Pilih...",
            allowClear: false,
            dropdownAutoWidth: true
        });
    });

    // 4. Script untuk form dinamis
    document.addEventListener('DOMContentLoaded', function() {
        
        const container = document.getElementById('details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0;

        /**
         * Fungsi untuk merender form detail, bisa dengan data (untuk old()) atau kosong
         */
        function renderDetailForm(data = null) {
            const i = detailIndex;
            
            // Siapkan nilai default atau dari 'old' data
            const nama_produk = data?.nama_produk || '';
            const kode_produksi = data?.kode_produksi || '';
            const kode_expired = data?.kode_expired || '';
            const jumlah = data?.jumlah || 1;
            const keterangan = data?.keterangan || '';

            const newDetail = document.createElement('div');
            newDetail.classList.add('dynamic-item-card', 'border', 'p-3', 'mb-3', 'rounded'); 
            
            newDetail.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Item Produk #${i + 1}</h5>
                    <button type="button" class="btn btn-danger btn-sm remove-detail-btn"><i class="bi bi-trash"></i> Hapus</button>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Produk (Varian) <span class="text-danger">*</span></label>
                        <input type="text" name="details[${i}][nama_produk]" class="form-control" value="${nama_produk}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kode Batch <span class="text-danger">*</span></label>
                        <input type="text" name="details[${i}][kode_produksi]" class="form-control" value="${kode_produksi}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode Expired</label>
                        <input type="date" name="details[${i}][kode_expired]" class="form-control" value="${kode_expired}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="details[${i}][jumlah]" class="form-control" value="${jumlah}" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="details[${i}][keterangan]" class="form-control" value="${keterangan}">
                    </div>
                </div>
            `;

            container.appendChild(newDetail);
            
            // Karena tidak ada Select2 dinamis di form ini, kita tidak perlu inisialisasi di sini
            
            detailIndex++;
        }

        // --- Event Listener untuk Tombol "Tambah Item" ---
        if (addBtn) {
            addBtn.addEventListener('click', () => renderDetailForm(null));
        }
        
        // --- Event Listener untuk Tombol Hapus ---
        if (container) {
            container.addEventListener('click', function(e) {
                // Logika Tombol Hapus
                const removeBtn = e.target.closest('.remove-detail-btn');
                if (removeBtn) {
                    removeBtn.closest('.dynamic-item-card').remove();
                }
            });
        }

        // --- Render data 'old' jika ada (setelah validasi gagal) ---
        const oldItems = @json(old('details', []));
        if (oldItems.length > 0) {
            oldItems.forEach(itemData => {
                renderDetailForm(itemData);
            });
        } else {
            // Tambah satu form kosong saat halaman dimuat pertama kali
            renderDetailForm(null);
        }

    });
</script>
@endpush