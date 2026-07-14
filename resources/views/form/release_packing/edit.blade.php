@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Edit Release Packing
            </h4>

            <form id="releasepackingForm" action="{{ route('release_packing.edit_spv', $release_packing->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- IDENTITAS --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Release Packing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                value="{{ old('date', $release_packing->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kemasan</label>
                                <select name="jenis_kemasan" id="jenis_kemasan" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Kemasan --</option>
                                    <option value="Pouch" {{ old('jenis_kemasan', $release_packing->jenis_kemasan) == 'Pouch' ? 'selected' : '' }}>Pouch</option>
                                    <option value="Toples" {{ old('jenis_kemasan', $release_packing->jenis_kemasan) == 'Toples' ? 'selected' : '' }}>Toples</option>
                                    <option value="Box" {{ old('jenis_kemasan', $release_packing->jenis_kemasan) == 'Box' ? 'selected' : '' }}>Box</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $release_packing->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            @php
                                $kode_batch_text = \App\Models\Mincing::where('uuid', $release_packing->kode_produksi)->value('kode_produksi') ?? $release_packing->kode_produksi;
                            @endphp

                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <select name="kode_produksi" class="form-control" id="kode_produksi" required>
                                    @if($release_packing->kode_produksi)
                                        <option value="{{ $release_packing->kode_produksi }}" selected>{{ $kode_batch_text }}</option>
                                    @else
                                        <option value="">Pilih Varian Terlebih Dahulu</option>
                                    @endif
                                </select>
                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="expired_date" id="expired_date" class="form-control"
                                value="{{ old('expired_date', $release_packing->expired_date) }}">
                                <small class="text-muted">Tanggal ini dihitung otomatis 7 bulan dari kode batch</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Palet</label>
                                <input type="text" name="no_palet" id="no_palet" class="form-control"
                                value="{{ old('no_palet', $release_packing->no_palet) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PEMERIKSAAN --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>Jumlah Release</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Release</label>
                                <input type="number" name="release" id="release" class="form-control"
                                value="{{ old('release', $release_packing->release) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KETERANGAN --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $release_packing->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('release_packing.verification') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        if (typeof $.fn.selectpicker === 'function') {
            $('.selectpicker').selectpicker();
        }

        // ===================== LOGIKA AJAX BATCH MINCING DENGAN SELECT2 =====================
        let produkSelect = $('#nama_produk');
        let batchSelect  = $('#kode_produksi');
        
        let initialBatch = "{{ $release_packing->kode_produksi ?? '' }}";
        let initialBatchText = "{{ $kode_batch_text ?? '' }}";

        batchSelect.select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: "-- Pilih Kode Batch --",
            allowClear: true,
            ajax: {
                delay: 250,
                transport: function (params, success, failure) {
                    let produkValue = produkSelect.val();
                    if (!produkValue) return;

                    return $.ajax({
                        url: "{{ route('lookup.batch_packing', ['nama_produk' => '__PRODUK__']) }}".replace('__PRODUK__', encodeURIComponent(produkValue)),
                        dataType: 'json',
                        data: { q: params.data.term },
                        success: success,
                        error: failure
                    });
                },
                processResults: function (data) {
                    return { results: data };
                }
            }
        });

        produkSelect.on('change', function () {
            batchSelect.prop('disabled', !this.value).val(null).trigger('change');
        });

        if (produkSelect.val()) {
            batchSelect.prop('disabled', false);
            if(initialBatch){
                let newOption = new Option(initialBatchText || initialBatch, initialBatch, true, true);
                batchSelect.append(newOption).trigger('change');
            }
        } else {
            batchSelect.prop('disabled', true);
        }
    });
</script>
@endpush
@endsection
