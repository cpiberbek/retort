@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Release Packing
            </h4>

            <form id="releasepackingForm" action="{{ route('release_packing.store') }}" method="POST">
                @csrf

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Release Packing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required>
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
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" id="nama_produk"
                                    class="form-control @error('nama_produk') is-invalid @enderror"
                                    data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk')==$produk->
                                        nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-danger">@error('nama_produk') {{ $message }} @enderror</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>

                                <select name="kode_produksi" id="kode_produksi"
                                    class="form-control select2 @error('kode_produksi') is-invalid @enderror" required
                                    disabled>
                                    <option value="">Pilih Varian Terlebih Dahulu</option>
                                </select>

                                <small id="kodeError" class="text-danger">
                                    @error('kode_produksi') {{ $message }} @enderror
                                </small>
                            </div>


                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="expired_date" id="expired_date" class="form-control">
                                <small class="text-muted">Tanggal ini dihitung otomatis 7 bulan dari kode
                                    batch</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Palet</label>
                                <input type="text" name="no_palet" id="no_palet" class="form-control" required>
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
                                <input type="number" name="release" id="release" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $data->keterangan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('release_packing.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== SCRIPT ===================== --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{--
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script> --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Set tanggal, waktu, dan shift otomatis
    // document.addEventListener("DOMContentLoaded", function () {
        $(document).ready(function () {

        const dateInput = document.getElementById("dateInput");

        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const hh = String(now.getHours()).padStart(2, '0');
        const min = String(now.getMinutes()).padStart(2, '0');

        dateInput.value = `${yyyy}-${mm}-${dd}`;

        // const produkSelect = document.querySelector('select[name="nama_produk"]');
        // const batchSelect = document.getElementById('kode_produksi');
    // ===================== LOGIKA AJAX BATCH MINCING DENGAN SELECT2 =====================
    let produkSelect = $('#nama_produk');
    let batchSelect  = $('#kode_produksi');

    function initBatchSelect() {
        let produkValue = produkSelect.val();
        
        if (batchSelect.data('select2')) {
            batchSelect.select2('destroy');
        }
        
        if (!produkValue) {
            batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
            batchSelect.prop("disabled", true);
            return;
        }
        
        batchSelect.html('<option value="">-- Pilih Batch --</option>');
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
                    return { results: data };
                },
                cache: true
            }
        });
    }

    produkSelect.on('change', function() {
        initBatchSelect();
    });

    if (produkSelect.val()) {
        initBatchSelect();
    }


        const expDateInput = document.getElementById('expired_date');
        // Exp date update ketika batch dipilih
        batchSelect.on('change', function () {
            let selectedText = this.options[this.selectedIndex]?.text;
            let kodeProduksi = selectedText?.split(" - ")[0]?.trim();

            if (!kodeProduksi) {
                expDateInput.value = '';
                return;
            }
            const bulanChar = kodeProduksi.charAt(1);
            const hari = parseInt(kodeProduksi.substr(2, 2));
            const bulanMap = {A:0,B:1,C:2,D:3,E:4,F:5,G:6,H:7,I:8,J:9,K:10,L:11};
            let kodeBulan = bulanMap[bulanChar];
            let now = new Date();
            let tahun = now.getFullYear();
            if (kodeBulan < now.getMonth()) tahun++;
            let expDate = new Date(tahun, kodeBulan, hari);
            expDate.setMonth(expDate.getMonth() + 7);
            expDateInput.value = expDate.toISOString().slice(0, 10);
        });
    });



</script>
@endpush
@endsection
