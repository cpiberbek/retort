@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Data Sampling Produk
            </h4>

            <form id="samplingForm" action="{{ route('sampling.edit_spv', $sampling->uuid) }}" method="POST">
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
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                    value="{{ old('date', $sampling->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift', $sampling->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $sampling->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $sampling->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Sampling</label>
                                <input type="text" name="jenis_sampel" id="jenis_sampel_input" class="form-control"
                                    value="{{ old('jenis_sampel', $sampling->jenis_sampel) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kemasan</label>
                                <select name="jenis_kemasan" id="jenis_kemasan"
                                    class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Kemasan --</option>
                                    <option value="Pouch" {{ old('jenis_kemasan', $sampling->jenis_kemasan) == 'Pouch' ? 'selected' : '' }}>Pouch</option>
                                    <option value="Toples" {{ old('jenis_kemasan', $sampling->jenis_kemasan) == 'Toples' ? 'selected' : '' }}>Toples</option>
                                    <option value="Box" {{ old('jenis_kemasan', $sampling->jenis_kemasan) == 'Box' ? 'selected' : '' }}>Box</option>
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
                                            {{ old('nama_produk', $sampling->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                            {{ $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <select name="kode_produksi" id="kode_produksi" class="form-control" required>
                                    @if($sampling->kode_produksi)
                                        <option value="{{ $sampling->kode_produksi }}" selected>{{ $sampling->kode_produksi }}</option>
                                    @else
                                        <option value="">Pilih Varian Terlebih Dahulu</option>
                                    @endif
                                </select>
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
                                <input type="number" name="jumlah" id="jumlah" class="form-control"
                                    value="{{ old('jumlah', $sampling->jumlah) }}" required step="0.01" min="0">
                            </div>
                        </div>

                        @php
                            $fields = [
                                'jamur','lendir','klip_tajam','pin_hole','air_trap_pvdc','air_trap_produk',
                                'keriput','bengkok','non_kode','over_lap','kecil','terjepit',
                                'double_klip','seal_halus','basah','dll'
                            ];
                        @endphp

                        <div class="row mb-3">
                            @foreach($fields as $field)
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                <input type="number" name="{{ $field }}" class="form-control"
                                    value="{{ old($field, $sampling->$field) }}" step="0.01" min="0">
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

        // Otomatis isi tanggal & shift (jika kosong)
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

        if (dateInput && !dateInput.value) {
            let now = new Date();
            let localDate = new Date(now.getTime() - (now.getTimezoneOffset() * 60000));
            dateInput.value = localDate.toISOString().slice(0, 10);
        }

        if (shiftInput && !shiftInput.value) {
            let hour = new Date().getHours();
            if (hour >= 7 && hour < 15) shiftInput.value = "1";
            else if (hour >= 15 && hour < 23) shiftInput.value = "2";
            else shiftInput.value = "3";
        }

        // --- AJAX LOAD BATCH ---
        const batchSelect = $('#kode_produksi');
        const namaProdukInput = $('#nama_produk');

        function loadBatches(namaProduk, oldBatch = '') {
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

        // Trigger saat produk diganti
        $(document).on('change', '#nama_produk', function() {
            loadBatches($(this).val());
        });

        // Load awal saat halaman dibuka
        if (namaProdukInput.val()) {
            let oldBatch = "{{ old('kode_produksi', $sampling->kode_produksi ?? '') }}";
            if (batchSelect.children('option').length <= 1) {
                loadBatches(namaProdukInput.val(), oldBatch);
            }
        }
    });
</script>
@endsection