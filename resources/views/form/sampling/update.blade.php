@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Data Sampling Produk (QC)
            </h4>

            <form id="samplingForm" action="{{ route('sampling.update_qc', $sampling->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">
                            {{-- TANGGAL --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                value="{{ old('date', $sampling->date) }}"
                                {{ $sampling->date ? 'readonly' : '' }} required>

                                @if($sampling->date)
                                <input type="hidden" name="date" value="{{ $sampling->date }}">
                                @endif
                            </div>

                            {{-- SHIFT --}}
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                @if($sampling->shift)
                                    <input type="text" class="form-control" value="Shift {{ $sampling->shift }}" readonly>
                                    <input type="hidden" name="shift" id="shiftInput" value="{{ $sampling->shift }}">
                                @else
                                    <select name="shift" id="shiftInput" class="form-control" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="1" {{ old('shift', $sampling->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                        <option value="2" {{ old('shift', $sampling->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                        <option value="3" {{ old('shift', $sampling->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                    </select>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- JENIS SAMPLING --}}
                            <div class="col-md-6">
                                <label class="form-label">Jenis Sampling</label>
                                <input type="text" name="jenis_sampel" class="form-control"
                                value="{{ old('jenis_sampel', $sampling->jenis_sampel) }}"
                                {{ $sampling->jenis_sampel ? 'readonly' : '' }} required>

                                @if($sampling->jenis_sampel)
                                <input type="hidden" name="jenis_sampel" value="{{ $sampling->jenis_sampel }}">
                                @endif
                            </div>

                            {{-- JENIS KEMASAN --}}
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kemasan</label>
                                @if($sampling->jenis_kemasan)
                                    <input type="text" class="form-control" value="{{ $sampling->jenis_kemasan }}" readonly>
                                    <input type="hidden" name="jenis_kemasan" value="{{ $sampling->jenis_kemasan }}">
                                @else
                                    <select name="jenis_kemasan" class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">-- Pilih Kemasan --</option>
                                        <option value="Pouch" {{ old('jenis_kemasan', $sampling->jenis_kemasan) == 'Pouch' ? 'selected' : '' }}>Pouch</option>
                                        <option value="Toples" {{ old('jenis_kemasan', $sampling->jenis_kemasan) == 'Toples' ? 'selected' : '' }}>Toples</option>
                                        <option value="Box" {{ old('jenis_kemasan', $sampling->jenis_kemasan) == 'Box' ? 'selected' : '' }}>Box</option>
                                    </select>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- NAMA PRODUK --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                @if($sampling->nama_produk)
                                    <input type="text" class="form-control" value="{{ $sampling->nama_produk }}" readonly>
                                    <input type="hidden" name="nama_produk" id="nama_produk" value="{{ $sampling->nama_produk }}">
                                @else
                                    <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">-- Pilih Varian --</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}"
                                                {{ old('nama_produk', $sampling->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            {{-- KODE PRODUKSI --}}
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                @if($sampling->kode_produksi)
                                    <input type="text" class="form-control" value="{{ $sampling->kode_produksi }}" readonly>
                                    <input type="hidden" name="kode_produksi" id="kode_produksi" value="{{ $sampling->kode_produksi }}">
                                @else
                                    <select name="kode_produksi" id="kode_produksi" class="form-control" required>
                                        <option value="">Pilih Varian Terlebih Dahulu</option>
                                    </select>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== ITEM SORTIR ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Item Sortir</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" class="form-control" step="0.01" 
                                value="{{ old('jumlah', $sampling->jumlah) }}"
                                {{ $sampling->jumlah ? 'readonly' : '' }} required>

                                @if($sampling->jumlah)
                                <input type="hidden" name="jumlah" value="{{ $sampling->jumlah }}" step="0.01">
                                @endif
                            </div>
                        </div>

                        @php
                        $fields = [
                        'jamur','lendir','klip_tajam','pin_hole','air_trap_pvdc',
                        'air_trap_produk','keriput','bengkok','non_kode','over_lap',
                        'kecil','terjepit','double_klip','seal_halus','basah','dll'
                        ];
                        @endphp

                        <div class="row mb-3">
                            @foreach($fields as $field)
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                <input type="number" name="{{ $field }}" class="form-control" step="0.01" 
                                value="{{ old($field, $sampling->$field) }}"
                                {{ $sampling->$field !== null ? 'readonly' : '' }}>

                                @if($sampling->$field !== null)
                                <input type="hidden" name="{{ $field }}" value="{{ $sampling->$field }}" step="0.01">
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3"
                        placeholder="Tambahkan catatan bila ada">{{ old('catatan', $sampling->catatan) }}</textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('sampling.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== SCRIPT ===================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        // Inisialisasi Selectpicker
        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }

        // --- Mencegah AJAX Error Saat Input Readonly ---
        const batchSelect = $('#kode_produksi');
        const namaProdukInput = $('#nama_produk');

        function loadBatches(namaProduk, oldBatch = '') {
            // HANYA jalankan jika batch berupa SELECT (bukan input hidden/text readonly)
            if (!batchSelect.is('select')) return;

            if (!namaProduk) {
                batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
                batchSelect.prop('disabled', true);
                return;
            }

            batchSelect.prop('disabled', false);
            batchSelect.html('<option value="">Mencari Batch...</option>');

            let url = "{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}";
            url = url.replace('__PRODUK__', encodeURIComponent(namaProduk));

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    batchSelect.html('<option value="">-- Pilih Batch --</option>');

                    if (!data || data.length === 0) {
                        batchSelect.html('<option value="">Batch Tidak Ditemukan</option>');
                        batchSelect.prop('disabled', true);
                        return;
                    }

                    data.forEach(function(batch) {
                        let isSelected = (oldBatch === batch.kode_produksi) ? 'selected' : '';
                        batchSelect.append(`<option value="${batch.kode_produksi}" ${isSelected}>${batch.kode_produksi}</option>`);
                    });
                },
                error: function(xhr, status, error) {
                    alert("Gagal mengambil data Batch dari server!");
                    batchSelect.html('<option value="">Gagal Terhubung ke Server</option>');
                    batchSelect.prop('disabled', true);
                }
            });
        }

        // Event handler hanya akan tereksekusi jika elementnya bisa diubah (select)
        $(document).on('change', '#nama_produk', function() {
            if ($(this).is('select')) {
                loadBatches($(this).val());
            }
        });

        // Trigger pertama kali untuk load jika sedang kosong datanya
        if (namaProdukInput.val() && batchSelect.is('select') && !batchSelect.val()) {
            let oldBatch = "{{ old('kode_produksi', $sampling->kode_produksi ?? '') }}";
            loadBatches(namaProdukInput.val(), oldBatch);
        }
    });
</script>
@endsection