@extends('layouts.app')

@section('content')
@php
    $currentBatch = $sampel->kode_produksi
        ? \App\Models\Mincing::where('uuid', $sampel->kode_produksi)->first(['uuid', 'kode_produksi'])
        : null;

    $jenisOptions = ['Retain QC', 'Lab Internal', 'Lab Eksternal', 'RND'];

    $backRoute = auth()->user()->isAuditView() ? route('sampel.audit') : route('sampel.index');
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Data Pengambilan Sampel
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

            <form id="editForm" action="{{ route('sampel.edit_spv', $sampel->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampel</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="dateInput">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                    value="{{ old('date', \Carbon\Carbon::parse($sampel->date)->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="jenis_sampel">Jenis Sampel</label>
                                <select name="jenis_sampel" id="jenis_sampel" class="form-control" required>
                                    <option value="">-- Pilih Sampel --</option>
                                    @foreach ($jenisOptions as $opt)
                                        <option value="{{ $opt }}"
                                            {{ old('jenis_sampel', $sampel->jenis_sampel) == $opt ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label" for="nama_produk">Nama Varian</label>
                                <select name="nama_produk" id="nama_produk" class="form-control" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach ($produks as $produk)
                                        <option value="{{ $produk->nama_produk }}"
                                            {{ old('nama_produk', $sampel->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                            {{ $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="kode_batch">Kode Batch</label>
                                <select name="kode_produksi" id="kode_batch" class="form-control" required>
                                    <option value="">-- Pilih Batch --</option>
                                    @if ($currentBatch)
                                        <option value="{{ $currentBatch->uuid }}" selected>
                                            {{ $currentBatch->kode_produksi }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== KETERANGAN ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $sampel->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ $backRoute }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function () {
    const produkSelect = $('#nama_produk');
    const batchSelect  = $('#kode_batch');

    const lookupTemplate = @json(route('lookup.batch', ['nama_produk' => '__NAMA__']));
    const current = @json($currentBatch ? ['uuid' => $currentBatch->uuid, 'kode' => $currentBatch->kode_produksi] : null);

    const select2Opts = { theme: 'bootstrap-5', width: '100%' };

    produkSelect.select2({ ...select2Opts, placeholder: 'Ketik atau pilih nama varian...', allowClear: true });
    batchSelect.select2({ ...select2Opts, placeholder: 'Pilih kode batch...' });

    function fillBatch(items, selected) {
        batchSelect.empty().append(new Option('-- Pilih Batch --', '', false, false));

        items.forEach(function (item) {
            batchSelect.append(new Option(item.kode_produksi, item.uuid, false, item.uuid === selected));
        });

        // batch tersimpan tetap dipertahankan walau tidak ada di hasil lookup
        if (selected && current && current.uuid === selected && !items.some(i => i.uuid === selected)) {
            batchSelect.append(new Option(current.kode, current.uuid, true, true));
        }

        batchSelect.val(selected || '').trigger('change');
    }

    function loadBatch(namaProduk, selected) {
        if (!namaProduk) {
            fillBatch([], null);
            return;
        }

        batchSelect.prop('disabled', true);

        const url = lookupTemplate.replace('__NAMA__', encodeURIComponent(namaProduk));

        $.getJSON(url)
            .done(function (data) { fillBatch(Array.isArray(data) ? data : [], selected); })
            .fail(function () { fillBatch([], selected); })
            .always(function () { batchSelect.prop('disabled', false); });
    }

    // ganti varian -> pilihan batch di-reset
    produkSelect.on('change', function () {
        loadBatch($(this).val(), null);
    });

    // muat awal: pertahankan batch yang tersimpan
    if (produkSelect.val()) {
        loadBatch(produkSelect.val(), current ? current.uuid : null);
    }
});
</script>
@endpush