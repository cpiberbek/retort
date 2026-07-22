@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-plus-circle"></i> Form Input Data Sampling Produk
                </h4>

                <form id="samplingForm" action="{{ route('sampling.store') }}" method="POST">
                    @csrf

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
                                    <input type="date" name="date" id="dateInput" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Shift</label>
                                    <select name="shift" id="shiftInput" class="form-control" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="1">Shift 1</option>
                                        <option value="2">Shift 2</option>
                                        <option value="3">Shift 3</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Sampling</label>
                                    <input type="text" name="jenis_sampel" id="jenis_sampel_input" class="form-control"
                                        value="Sampling Box" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Kemasan</label>
                                    <select name="jenis_kemasan" id="jenis_kemasan" class="form-control selectpicker"
                                        data-live-search="true" required>
                                        <option value="">-- Pilih Kemasan --</option>
                                        <option value="Pouch">Pouch</option>
                                        <option value="Toples">Toples</option>
                                        <option value="Box">Box</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama_produk" class="form-label fw-semibold">
                                        Nama Varian <span class="text-danger">*</span>
                                    </label>
                                    <select name="nama_produk" id="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">-- Pilih Varian --</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}">
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        Pilih varian produk terlebih dahulu
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <label for="kode_batch" class="form-label fw-semibold">
                                        Kode Batch <span class="text-danger">*</span>
                                    </label>
                                    <select name="kode_produksi" id="kode_batch" class="form-control" required>
                                        <option value="">Pilih Varian terlebih dahulu</option>
                                    </select>
                                    <small class="text-muted">
                                        Batch akan muncul otomatis
                                    </small>
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
                                    <input type="number" name="jumlah" id="jumlah" class="form-control" required
                                        step="1" min="0">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3"><label class="form-label">Jamur</label><input type="number" name="jamur" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Lendir</label><input type="number" name="lendir" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Klip Tajam</label><input type="number" name="klip_tajam" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Pin Hole</label><input type="number" name="pin_hole" class="form-control" step="1" min="0"></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3"><label class="form-label">Air Trap PVDC</label><input type="number" name="air_trap_pvdc" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Air Trap Produk</label><input type="number" name="air_trap_produk" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Keriput</label><input type="number" name="keriput" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Bengkok</label><input type="number" name="bengkok" class="form-control" step="1" min="0"></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3"><label class="form-label">Non Kode</label><input type="number" name="non_kode" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Over Lap</label><input type="number" name="over_lap" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Kecil</label><input type="number" name="kecil" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Terjepit</label><input type="number" name="terjepit" class="form-control" step="1" min="0"></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3"><label class="form-label">Double Klip</label><input type="number" name="double_klip" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Seal Halus/Lepas</label><input type="number" name="seal_halus" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Basah</label><input type="number" name="basah" class="form-control" step="1" min="0"></div>
                                <div class="col-md-3"><label class="form-label">Dll</label><input type="number" name="dll" class="form-control" step="1" min="0"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== CATATAN ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light"><strong>Catatan</strong></div>
                        <div class="card-body">
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- ===================== TOMBOL ===================== --}}
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('sampling.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== SCRIPT ===================== --}}
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

{{-- Select2 CSS & JS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        if (typeof $.fn.selectpicker === 'function') {
            $('.selectpicker').selectpicker();
        }

        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

        let today = new Date();
        let yyyy = today.getFullYear();
        let mm = String(today.getMonth() + 1).padStart(2, '0');
        let dd = String(today.getDate()).padStart(2, '0');
        let hh = String(today.getHours()).padStart(2, '0');

        if (dateInput) dateInput.value = `${yyyy}-${mm}-${dd}`;

        if (shiftInput) {
            let hour = parseInt(hh);
            if (hour >= 7 && hour < 15) {
                shiftInput.value = "1";
            } else if (hour >= 15 && hour < 23) {
                shiftInput.value = "2";
            } else {
                shiftInput.value = "3";
            }
        }

        // === Select2 Batch Integration ===
        const produkSelect = $('#nama_produk');
        const batchSelect = $('#kode_batch');

        function initBatchSelect(produkValue) {
            if (batchSelect.data('select2')) {
                batchSelect.select2('destroy');
            }
            
            if (!produkValue) {
                batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
                batchSelect.prop("disabled", true);
                return;
            }
            
            batchSelect.prop("disabled", false);
            
            batchSelect.select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: "-- Pilih Batch --",
                allowClear: true,
                ajax: {
                    url: "{{ url('/lookup/batch-packing') }}/" + encodeURIComponent(produkValue),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        var processedData = $.map(data, function(obj) {
                            obj.id = obj.text; // Mapping UUID ke String Batch karena db nyimpan string!
                            return obj;
                        });
                        return { results: processedData };
                    },
                    cache: true
                }
            });
        }

        if (produkSelect.val()) {
            initBatchSelect(produkSelect.val());
        } else {
            batchSelect.prop("disabled", true);
        }

        produkSelect.on('change', function () {
            let namaProduk = $(this).val();
            
            if (!namaProduk) {
                batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
                batchSelect.prop("disabled", true);
                return;
            }

            batchSelect.html('<option value="">-- Pilih Batch --</option>');
            initBatchSelect(namaProduk);
        });
    });
</script>
@endpush