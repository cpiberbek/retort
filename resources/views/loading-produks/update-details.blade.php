@extends('layouts.app')

@section('title', 'Update Detail Loading')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body { background-color: #f8f9fa; }
    .form-label { font-weight: 600; color: #495057; }
    .form-control, .form-select { border-radius: 8px; }
    
    /* Styling Field Terkunci */
    .form-control[readonly], .form-select[disabled] {
        background-color: #e9ecef; /* Abu-abu */
        cursor: not-allowed;
        border-color: #dee2e6;
        color: #6c757d;
    }

    /* Select2 Tweaks */
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
    }
    .select2-container--bootstrap-5 .select2-selection { border-radius: 8px !important; }
    
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

            <h4 class="mb-1"><i class="bi bi-pencil-square"></i> Update Detail Pemeriksaan</h4>
            <p class="text-muted mb-4">Kolom yang sudah terisi otomatis terkunci. Silakan update data yang belum lengkap.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Periksa kembali inputan Anda:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form tetap submit ke route UPDATE standar (PUT) --}}
            <form action="{{ route('loading-produks.update', $loadingProduk->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- CARD INFORMASI UTAMA --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Utama</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- TANGGAL --}}
                            <div class="col-md-4">
                                <label class="form-label">Hari/Tanggal <span class="text-danger">*</span></label>
                                @if($loadingProduk->tanggal)
                                    <input type="date" class="form-control" value="{{ $loadingProduk->tanggal }}" readonly>
                                    <input type="hidden" name="tanggal" value="{{ $loadingProduk->tanggal }}">
                                @else
                                    <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal') }}" required>
                                @endif
                            </div>

                            {{-- SHIFT --}}
                            <div class="col-md-4">
                                <label class="form-label">Shift <span class="text-danger">*</span></label>
                                @if($loadingProduk->shift)
                                    <input type="text" class="form-control" value="{{ $loadingProduk->shift }}" readonly>
                                    <input type="hidden" name="shift" value="{{ $loadingProduk->shift }}">
                                @else
                                    <select class="form-select select2-static" name="shift" required>
                                        <option value="Pagi" @selected(old('shift') == 'Pagi')>Pagi</option>
                                        <option value="Malam" @selected(old('shift') == 'Malam')>Malam</option>
                                    </select>
                                @endif
                            </div>

                            {{-- JENIS AKTIVITAS --}}
                            <div class="col-md-4">
                                <label class="form-label">Jenis Aktivitas <span class="text-danger">*</span></label>
                                @if($loadingProduk->jenis_aktivitas)
                                    <input type="text" class="form-control" value="{{ $loadingProduk->jenis_aktivitas }}" readonly>
                                    <input type="hidden" name="jenis_aktivitas" value="{{ $loadingProduk->jenis_aktivitas }}">
                                @else
                                    <select class="form-select select2-static" name="jenis_aktivitas" required>
                                        <option value="Loading" @selected(old('jenis_aktivitas') == 'Loading')>Loading</option>
                                        <option value="Unloading" @selected(old('jenis_aktivitas') == 'Unloading')>Unloading</option>
                                    </select>
                                @endif
                            </div>

                            {{-- JAM --}}
                            <div class="col-md-6">
                                <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                @if($loadingProduk->jam_mulai)
                                    <input type="time" class="form-control" value="{{ \Carbon\Carbon::parse($loadingProduk->jam_mulai)->format('H:i') }}" readonly>
                                    <input type="hidden" name="jam_mulai" value="{{ \Carbon\Carbon::parse($loadingProduk->jam_mulai)->format('H:i') }}">
                                @else
                                    <input type="time" class="form-control" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                @if($loadingProduk->jam_selesai)
                                    <input type="time" class="form-control" value="{{ \Carbon\Carbon::parse($loadingProduk->jam_selesai)->format('H:i') }}" readonly>
                                    <input type="hidden" name="jam_selesai" value="{{ \Carbon\Carbon::parse($loadingProduk->jam_selesai)->format('H:i') }}">
                                @else
                                    <input type="time" class="form-control" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                                @endif
                            </div>

                            <div class="col-12"><hr class="my-2"></div>

                            {{-- KENDARAAN --}}
                            <div class="col-md-4">
                                <label class="form-label">No. Pol Mobil <span class="text-danger">*</span></label>
                                @if($loadingProduk->no_pol_mobil)
                                    <input type="text" class="form-control" value="{{ $loadingProduk->no_pol_mobil }}" readonly>
                                    <input type="hidden" name="no_pol_mobil" value="{{ $loadingProduk->no_pol_mobil }}">
                                @else
                                    <input type="text" class="form-control" name="no_pol_mobil" value="{{ old('no_pol_mobil') }}" required>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nama Supir <span class="text-danger">*</span></label>
                                @if($loadingProduk->nama_supir)
                                    <input type="text" class="form-control" value="{{ $loadingProduk->nama_supir }}" readonly>
                                    <input type="hidden" name="nama_supir" value="{{ $loadingProduk->nama_supir }}">
                                @else
                                    <input type="text" class="form-control" name="nama_supir" value="{{ old('nama_supir') }}" required>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Ekspedisi <span class="text-danger">*</span></label>
                                @if($loadingProduk->ekspedisi)
                                    <input type="text" class="form-control" value="{{ $loadingProduk->ekspedisi }}" readonly>
                                    <input type="hidden" name="ekspedisi" value="{{ $loadingProduk->ekspedisi }}">
                                @else
                                    <input type="text" class="form-control" name="ekspedisi" value="{{ old('ekspedisi') }}" required>
                                @endif
                            </div>
                            
                             <div class="col-md-4">
                                <label class="form-label">Tujuan / Asal <span class="text-danger">*</span></label>
                                @if($loadingProduk->tujuan_asal)
                                    <input type="text" class="form-control" value="{{ $loadingProduk->tujuan_asal }}" readonly>
                                    <input type="hidden" name="tujuan_asal" value="{{ $loadingProduk->tujuan_asal }}">
                                @else
                                    <input type="text" class="form-control" name="tujuan_asal" value="{{ old('tujuan_asal') }}" required>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">No. Segel</label>
                                @if($loadingProduk->no_segel)
                                    <input type="text" class="form-control" value="{{ $loadingProduk->no_segel }}" readonly>
                                    <input type="hidden" name="no_segel" value="{{ $loadingProduk->no_segel }}">
                                @else
                                    <input type="text" class="form-control" name="no_segel" value="{{ old('no_segel') }}">
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jenis Kendaraan</label>
                                @if($loadingProduk->jenis_kendaraan)
                                    <input type="text" class="form-control" value="{{ $loadingProduk->jenis_kendaraan }}" readonly>
                                    <input type="hidden" name="jenis_kendaraan" value="{{ $loadingProduk->jenis_kendaraan }}">
                                @else
                                    <input type="text" class="form-control" name="jenis_kendaraan" value="{{ old('jenis_kendaraan') }}">
                                @endif
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
                                <div class="card p-3 @if(!empty($loadingProduk->kondisi_mobil)) bg-light @endif">
                                    <div class="row">
                                        @php
                                            $kondisiList = [
                                                'bersih' => 'Bersih', 'kering' => 'Kering', 'tidak_bocor' => 'Tidak Bocor',
                                                'tidak_debu' => 'Tidak Berdebu', 'tidak_basah' => 'Tidak Basah',
                                                'bebas_hama' => 'Bebas Hama', 'bebas_noda' => 'Bebas Noda',
                                                'bebas_oli' => 'Bebas Bekas oli', 'tidak_ada_non_halal' => 'Tidak ada produk non halal',
                                            ];
                                            $currentKondisi = $loadingProduk->kondisi_mobil ?? [];
                                            $isKondisiFilled = !empty($currentKondisi);
                                        @endphp

                                        {{-- Hidden input agar data checklist lama tidak hilang saat submit --}}
                                        @if($isKondisiFilled)
                                            @foreach($currentKondisi as $val)
                                                <input type="hidden" name="kondisi_mobil[]" value="{{ $val }}">
                                            @endforeach
                                        @endif

                                        @foreach ($kondisiList as $key => $label)
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="kondisi_mobil[]" value="{{ $key }}" id="kondisi_{{ $key }}"
                                                           @checked(in_array($key, $currentKondisi))
                                                           @if($isKondisiFilled) disabled @endif> 
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
                                    <label class="form-label">Keterangan Total</label>
                                    @if($loadingProduk->keterangan_total)
                                        <textarea class="form-control" rows="2" readonly>{{ $loadingProduk->keterangan_total }}</textarea>
                                        <input type="hidden" name="keterangan_total" value="{{ $loadingProduk->keterangan_total }}">
                                    @else
                                        <textarea class="form-control" name="keterangan_total" rows="2">{{ old('keterangan_total') }}</textarea>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Keterangan Umum</label>
                                    @if($loadingProduk->keterangan_umum)
                                        <textarea class="form-control" rows="2" readonly>{{ $loadingProduk->keterangan_umum }}</textarea>
                                        <input type="hidden" name="keterangan_umum" value="{{ $loadingProduk->keterangan_umum }}">
                                    @else
                                        <textarea class="form-control" name="keterangan_umum" rows="2">{{ old('keterangan_umum') }}</textarea>
                                    @endif
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">PIC QC</label>
                                        @if($loadingProduk->pic_qc)
                                            <input type="text" class="form-control" value="{{ $loadingProduk->pic_qc }}" readonly>
                                            <input type="hidden" name="pic_qc" value="{{ $loadingProduk->pic_qc }}">
                                        @else
                                            <input type="text" class="form-control" name="pic_qc" value="{{ old('pic_qc') }}">
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">PIC Warehouse</label>
                                        @if($loadingProduk->pic_warehouse)
                                            <input type="text" class="form-control" value="{{ $loadingProduk->pic_warehouse }}" readonly>
                                            <input type="hidden" name="pic_warehouse" value="{{ $loadingProduk->pic_warehouse }}">
                                        @else
                                            <input type="text" class="form-control" name="pic_warehouse" value="{{ old('pic_warehouse') }}">
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">PIC QC SPV</label>
                                        @if($loadingProduk->pic_qc_spv)
                                            <input type="text" class="form-control" value="{{ $loadingProduk->pic_qc_spv }}" readonly>
                                            <input type="hidden" name="pic_qc_spv" value="{{ $loadingProduk->pic_qc_spv }}">
                                        @else
                                            <input type="text" class="form-control" name="pic_qc_spv" value="{{ old('pic_qc_spv') }}">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DETAIL ITEM --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><i class="bi bi-list-nested"></i> Detail Item Produk <span class="text-danger">*</span></strong>
                            @if($loadingProduk->details->isEmpty())
                                <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Item</button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        
                        {{-- JIKA DETAIL SUDAH ADA (READONLY) --}}
                        @if($loadingProduk->details->isNotEmpty())
                            <div class="alert alert-info py-2"><small><i class="bi bi-lock-fill"></i> Data item produk sudah dikunci.</small></div>
                            @foreach($loadingProduk->details as $index => $detail)
                            <div class="dynamic-item-card border p-3 mb-3 rounded bg-light">
                                <h6 class="text-muted mb-2">Item Produk #{{ $index + 1 }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Produk (Varian)</label>
                                        <input type="text" name="details[{{$index}}][nama_produk]" class="form-control" value="{{ $detail->nama_produk }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kode Batch</label>
                                        <input type="text" name="details[{{$index}}][kode_produksi]" class="form-control" value="{{ $detail->kode_produksi }}" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kode Expired</label>
                                        <input type="date" name="details[{{$index}}][kode_expired]" class="form-control" value="{{ $detail->kode_expired }}" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Jumlah</label>
                                        <input type="number" name="details[{{$index}}][jumlah]" class="form-control" value="{{ $detail->jumlah }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text" name="details[{{$index}}][keterangan]" class="form-control" value="{{ $detail->keterangan }}" readonly>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        
                        {{-- JIKA DETAIL BELUM ADA (INPUT) --}}
                        @else
                            <div id="details-container"></div>
                        @endif

                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-warning btn-lg"><i class="bi bi-save"></i> Simpan Update</button>
                    <a href="{{ route('loading-produks.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Init Select2 hanya jika elemen tersedia (tidak readonly)
        if ($('.select2-static').length > 0) {
            $('.select2-static').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih...",
                allowClear: false,
                dropdownAutoWidth: true
            });
        }
    });

    // JS Form Dinamis (Hanya aktif jika data belum ada)
    @if($loadingProduk->details->isEmpty())
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0;

        function renderDetailForm(data = null) {
            const i = detailIndex;
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
                        <select name="details[${i}][nama_produk]" class="form-control var-produk-select" required>
                            <option value="">-- Pilih Varian --</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}" ${nama_produk === '{{ $produk->nama_produk }}' ? 'selected' : ''}>
                                    {{ $produk->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kode Batch <span class="text-danger">*</span></label>
                        <select name="details[${i}][kode_produksi]" class="form-control var-batch-select" required>
                            <option value="">Pilih Varian Terlebih Dahulu</option>
                        </select>
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

            let produkSelect = $(newDetail).find('.var-produk-select');
            let batchSelect = $(newDetail).find('.var-batch-select');

            // Initialize produk as Select2 (with search)
            produkSelect.select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: "-- Pilih Varian --",
                allowClear: true
            });

            // Initialize batch as Select2 with dynamic AJAX
            batchSelect.select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: "-- Pilih Kode Batch --",
                allowClear: true,
                ajax: {
                    url: function () {
                        let produkValue = produkSelect.val();
                        if (!produkValue) return '';
                        return "{{ url('/lookup/batch-packing') }}/" + encodeURIComponent(produkValue);
                    },
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

            // Set old batch value if exists
            if (kode_produksi) {
                let newOption = new Option(kode_produksi, kode_produksi, true, true);
                batchSelect.append(newOption).trigger('change');
            }

            // Set initial disabled state
            if (!produkSelect.val()) {
                batchSelect.prop('disabled', true);
            }

            // When produk changes, clear batch and enable/disable
            produkSelect.on('change', function() {
                let currentVal = $(this).val();
                batchSelect.val(null).trigger('change');
                batchSelect.prop('disabled', !currentVal);
            });

            detailIndex++;
        }

        if (addBtn) addBtn.addEventListener('click', () => renderDetailForm(null));
        
        if (container) {
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-detail-btn')) {
                    e.target.closest('.dynamic-item-card').remove();
                }
            });
        }

        // Handle error validation old inputs
        const oldDetails = @json(old('details', []));
        if (oldDetails.length > 0) {
            oldDetails.forEach(item => renderDetailForm(item));
        } else {
            renderDetailForm(null);
        }
    });
    @endif
</script>
@endpush