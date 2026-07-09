@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Kontrol Labelisasi PVDC
            </h4>

            <form id="pvdcEditForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- IDENTITAS --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data PVDC</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" value="{{ $labelisasi_pvdc->date }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" required>
                                    <option value="1" {{ $labelisasi_pvdc->shift=="1"?"selected":"" }}>Shift 1</option>
                                    <option value="2" {{ $labelisasi_pvdc->shift=="2"?"selected":"" }}>Shift 2</option>
                                    <option value="3" {{ $labelisasi_pvdc->shift=="3"?"selected":"" }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ $labelisasi_pvdc->nama_produk==$produk->nama_produk?"selected":"" }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Operator</label>
                                <select name="nama_operator" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Operator --</option>
                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->nama_karyawan }}" {{ $labelisasi_pvdc->nama_operator==$operator->nama_karyawan?"selected":"" }}>
                                        {{ $operator->nama_karyawan }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DATA PVDC --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Data PVDC</strong>
                        <button type="button" id="addRow" class="btn btn-sm btn-light text-primary fw-bold">+ Tambah Mesin</button>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered text-center align-middle" id="pvdcTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Mesin</th>
                                    <th>Kode Batch</th>
                                    <th>Bukti Kode (Upload Gambar)</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pvdcBody">
                                @foreach($labelisasi_pvdcData as $i => $row)
                                <tr>
                                    <td>
                                        <select name="data_pvdc[{{ $i }}][mesin]" class="form-control form-control-sm" required>
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach($mesins as $mesin)
                                            <option value="{{ $mesin->nama_mesin }}" {{ $mesin->nama_mesin==$row['mesin']?'selected':'' }}>
                                                {{ $mesin->nama_mesin }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="data_pvdc[{{ $i }}][kode_batch]" class="form-control form-control-sm batchSelect" required>
                                            @if(!empty($row['kode_batch']))
                                                <option value="{{ $row['kode_batch'] }}" selected>{{ $row['kode_produksi_display'] ?? $row['kode_batch'] }}</option>
                                            @else
                                                <option value="">-- Pilih Batch --</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <input type="file" name="data_pvdc[{{ $i }}][kode_produksi]" class="form-control form-control-sm" accept="image/*">
                                        <div class="preview mt-2">
                                            @if(!empty($row['file']))
                                            @php
                                                $fileUrl = $row['file'];
                                                if (preg_match('/^https?:\/\/[^\/]+\/storage\/(.+)$/i', $fileUrl, $matches)) {
                                                    $fileUrl = asset('storage/' . $matches[1]);
                                                } elseif (!preg_match('/^https?:\/\//i', $fileUrl)) {
                                                    $fileUrl = asset('storage/' . ltrim($fileUrl, '/'));
                                                }
                                            @endphp
                                            <a href="{{ $fileUrl }}" target="_blank">
                                                <img src="{{ $fileUrl }}" width="100" class="img-thumbnail">
                                            </a>
                                            <input type="hidden" name="data_pvdc[{{ $i }}][file_url]" value="{{ $row['file'] }}">
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="data_pvdc[{{ $i }}][keterangan]" class="form-control form-control-sm" value="{{ $row['keterangan'] ?? '' }}">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TOMBOL SIMPAN --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" id="saveBtn" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('labelisasi_pvdc.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>

            <div id="resultArea" class="mt-3"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

{{-- Select2 CSS & JS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function(){
    $('.selectpicker').selectpicker();

    function initBatchSelect(select, produk) {
        if (select.data('select2')) {
            select.select2('destroy');
        }
        
        if (!produk) {
            select.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
            select.prop("disabled", true);
            return;
        }
        
        select.prop("disabled", false);
        
        select.select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: "-- Pilih Batch --",
            allowClear: true,
            ajax: {
                url: "{{ url('/lookup/batch-packing') }}/" + encodeURIComponent(produk),
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

    function loadBatchForAllRows() {
        const produkValue = $('select[name="nama_produk"]').val();
        if (!produkValue) return;

        $('.batchSelect').each(function() {
            let select = $(this);
            
            if (!select.val()) {
                select.html('<option value="">-- Pilih Batch --</option>');
            }
            
            initBatchSelect(select, produkValue);
        });
    }

    // Initialize select2 on load
    loadBatchForAllRows();

    $('select[name="nama_produk"]').on('change', function() {
        $(".batchSelect").empty().trigger('change');
        loadBatchForAllRows();
    });

    // TAMBAH BARIS
    let index = {{ count($labelisasi_pvdcData) }};
    const mesinOptions = `{!! collect($mesins)->map(fn($m) => "<option value='{$m->nama_mesin}'>{$m->nama_mesin}</option>")->implode('') !!}`;

    $('#addRow').click(function(){
        $('#pvdcBody').append(`
        <tr>
            <td>
                <select name="data_pvdc[${index}][mesin]" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Mesin --</option>${mesinOptions}
                </select>
            </td>
            <td>
                <select name="data_pvdc[${index}][kode_batch]" class="form-control form-control-sm batchSelect" required>
                    <option value="">-- Pilih Batch --</option>
                </select>
            </td>
            <td>
                <input type="file" name="data_pvdc[${index}][kode_produksi]" class="form-control form-control-sm" accept="image/*">
                <div class="preview mt-2"></div>
            </td>
            <td>
                <input type="text" name="data_pvdc[${index}][keterangan]" class="form-control form-control-sm">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
            </td>
        </tr>`);
        index++;
        loadBatchForAllRows();
    });

    // HAPUS BARIS
    $('#pvdcBody').on('click','.removeRow',function(){
        $(this).closest('tr').remove();
    });

    // SIMPAN DATA
    $('#saveBtn').click(function(){
        const btn = $(this);
        const form = $('#pvdcEditForm')[0];
        const formData = new FormData(form);

        let hasData = false;
        $('#pvdcBody tr').each(function(){
            const mesin = $(this).find('select[name$="[mesin]"]').val();
            const kodeBatch = $(this).find('input[name$="[kode_batch]"]').val();
            if(mesin && kodeBatch) hasData = true;
        });

        if(!hasData){
            alert('Belum ada data PVDC yang diinputkan!');
            return;
        }

        btn.prop('disabled',true).html('Menyimpan...');

        $.ajax({
            url: "{{ route('labelisasi_pvdc.update.form', $labelisasi_pvdc->uuid) }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                if(res.success) window.location.href = res.redirect_url;
                else alert(res.message);
            },
            complete: function(){
                btn.prop('disabled',false).html('<i class="bi bi-save"></i> Simpan');
            }
        });
    });

});
</script>
@endpush

@push('styles')
<style>
    /* Select2 bootstrap 5 styling override untuk form ini */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(1.5em + .5rem + 2px) !important;
        border-radius: .25rem !important;
        font-size: .875rem;
    }
</style>
@endpush
