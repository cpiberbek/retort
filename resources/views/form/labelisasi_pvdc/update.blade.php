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
                                <input type="date" name="date" value="{{ $labelisasi_pvdc->date }}" class="form-control"
                                    {{ $labelisasi_pvdc->date ? 'readonly' : '' }}
                                required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" {{ $labelisasi_pvdc->shift ? 'disabled' : ''
                                    }}
                                    required>
                                    <option value="1" {{ $labelisasi_pvdc->shift=="1"?"selected":"" }}>Shift 1</option>
                                    <option value="2" {{ $labelisasi_pvdc->shift=="2"?"selected":"" }}>Shift 2</option>
                                    <option value="3" {{ $labelisasi_pvdc->shift=="3"?"selected":"" }}>Shift 3</option>
                                </select>
                                @if($labelisasi_pvdc->shift)
                                <input type="hidden" name="shift" value="{{ $labelisasi_pvdc->shift }}">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" {{
                                    $labelisasi_pvdc->nama_produk ? 'disabled' : '' }}
                                    required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ $labelisasi_pvdc->
                                        nama_produk==$produk->nama_produk?"selected":"" }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($labelisasi_pvdc->nama_produk)
                                <input type="hidden" name="nama_produk" value="{{ $labelisasi_pvdc->nama_produk }}">
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Operator</label>
                                <select name="nama_operator" class="form-control selectpicker" data-live-search="true"
                                    required>
                                    <option value="">-- Pilih Operator --</option>
                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->nama_karyawan }}" {{ $labelisasi_pvdc->
                                        nama_operator==$operator->nama_karyawan?"selected":"" }}>
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
                        <button type="button" id="addRow" class="btn btn-sm btn-light text-primary fw-bold">+ Tambah
                            Mesin</button>
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
                                    {{-- MESIN --}}
                                    <td>
                                        @php $mesinLocked = !empty($row['mesin']); @endphp
                                        <select name="data_pvdc[{{ $i }}][mesin]" class="form-control form-control-sm"
                                            {{ $mesinLocked ? 'disabled' : '' }} required>
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach($mesins as $mesin)
                                            <option value="{{ $mesin->nama_mesin }}" {{ $mesin->nama_mesin ==
                                                $row['mesin'] ? 'selected' : '' }}>
                                                {{ $mesin->nama_mesin }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if($mesinLocked)
                                        <input type="hidden" name="data_pvdc[{{ $i }}][mesin]"
                                            value="{{ $row['mesin'] }}">
                                        @endif
                                    </td>

                                    {{-- KODE BATCH --}}
                                    <td>
                                        @php $batchLocked = !empty($row['kode_batch']); @endphp
                                        @if($batchLocked)
                                        <input type="hidden" name="data_pvdc[{{ $i }}][kode_batch]"
                                            value="{{ $row['kode_batch'] }}">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $row['kode_produksi_display'] ?? $row['kode_batch'] }}" readonly>
                                        @else
                                        <select name="data_pvdc[{{ $i }}][kode_batch]"
                                            class="form-control form-control-sm batchSelect" required disabled>
                                            <option value="">Pilih Produk Terlebih Dahulu</option>
                                        </select>
                                        @endif
                                    </td>

                                    {{-- FILE --}}
                                    <td>
                                        @php $fileLocked = !empty($row['file']); @endphp
                                        <input type="file" name="data_pvdc[{{ $i }}][kode_produksi]"
                                            class="form-control form-control-sm" accept="image/*" {{ $fileLocked
                                            ? 'disabled' : '' }}>
                                        <div class="preview mt-2">
                                            @if(!empty($row['file']))
                                            <a href="{{ $row['file'] }}" target="_blank">
                                                <img src="{{ $row['file'] }}" width="100" class="img-thumbnail">
                                            </a>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- KETERANGAN --}}
                                    <td>
                                        @php $ketLocked = !empty($row['keterangan']); @endphp
                                        <input type="text" name="data_pvdc[{{ $i }}][keterangan]"
                                            class="form-control form-control-sm" value="{{ $row['keterangan'] ?? '' }}"
                                            {{ $ketLocked ? 'readonly' : '' }}>
                                        <div class="invalid-feedback"></div>
                                    </td>

                                    {{-- TOMBOL --}}
                                    <td>
                                        @if($mesinLocked && $batchLocked && $fileLocked && $ketLocked)
                                        <button type="button" class="btn btn-success btn-sm" disabled>
                                            <i class="bi bi-check-circle"></i> Tersimpan
                                        </button>
                                        @else
                                        <!-- <button type="button" class="btn btn-primary btn-sm saveRow">Simpan</button> -->
                                        <!-- <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button> -->
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

                {{-- TOMBOL SIMPAN --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" id="saveBtn" class="btn btn-success"><i class="bi bi-save"></i>
                        Simpan</button>
                    <a href="{{ route('labelisasi_pvdc.index') }}" class="btn btn-secondary"><i
                            class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>

            <div id="resultArea" class="mt-3"></div>
        </div>
    </div>
</div>

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function(){
        if (typeof $.fn.selectpicker === 'function') {
            $('.selectpicker').selectpicker();
        }
    });
</script>

<script>
    $(document).ready(function(){
    // =================== VALIDASI KODE BATCH ===================
    const produkSelect = $("#nama_produk");
    const produkHidden = $('input[name="nama_produk"]');

        // Initialize batch loading if product is already selected
        let currentProduk = produkHidden.length > 0 ? produkHidden.val() : produkSelect.val();
        if (currentProduk) {
            loadBatchForAllRows();
        }

    // ===================== VALIDASI =====================

        function validateFile(input){
            input.next(".invalid-feedback").remove();
            input.removeClass("is-invalid");

        // File baru diupload
            if(input[0].files && input[0].files.length>0){
                return true;
            }

        // Cek file lama dari preview
            const preview = input.closest('td').find('.preview img, .preview a').length;
            if(preview > 0){
                return true;
            }

            input.addClass("is-invalid");
            input.after('<div class="invalid-feedback d-block">Gambar wajib diunggah!</div>');
            return false;
        }

    // Trigger saat produk diganti
    produkSelect.on("change", function () {
        loadBatchForAllRows();
    });

    function loadBatchForAllRows() {
        let produk = produkHidden.length > 0 ? produkHidden.val() : produkSelect.val();
        let batchSelects = $(".batchSelect");

        // Store current selections before clearing
        let currentSelections = {};
        batchSelects.each(function (index) {
            let select = $(this);
            currentSelections[index] = select.val();
        });

        batchSelects.each(function (index) {
            let select = $(this);
            select.html("");
            select.prop("disabled", true);

            if (!produk) {
                select.html('<option value="">Pilih Produk Terlebih Dahulu</option>');
                return;
            }

            const url = "{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}".replace('__PRODUK__', encodeURIComponent(produk));
                fetch(url)
                .then(res => res.json())
                .then(data => {

                    if (data.length === 0) {
                        select.html('<option value="">Batch Tidak Ditemukan</option>');
                        select.prop("disabled", true);
                        return;
                    }

                    select.prop("disabled", false);
                    select.html('<option value="">-- Pilih Batch --</option>');

                    data.forEach(batch => {
                        select.append(`
                            <option value="${batch.uuid}" kode_batch="${batch.kode_produksi}">
                                ${batch.kode_produksi}
                            </option>
                        `);
                    });

                    // Restore previous selection if it still exists
                    if (currentSelections[index] && select.find(`option[value="${currentSelections[index]}"]`).length > 0) {
                        select.val(currentSelections[index]);
                    }
                });
        });
    }

    // Trigger saat batch dipilih → isi kode batch pada input text (jika dipakai)
    $(document).on("change", ".batchSelect", function () {
        let kode = $(this).find(":selected").data("kode") ?? "";
        $(this).closest("tr").find(".kodeBatchText").val(kode);
    });

    // ===================== TAMBAH BARIS =====================
        let index = {{ count($labelisasi_pvdcData)-1 }};
        const mesinOptions = `{!! collect($mesins)->map(fn($m)=>"<option value='{$m->nama_mesin}'>{$m->nama_mesin}</option>")->implode('') !!}`;

        $('#addRow').click(function(){
            index++;
            let produk = produkHidden.length > 0 ? produkHidden.val() : produkSelect.val();
            let disabledAttr = produk ? '' : 'disabled';
            let initialOption = produk ? '<option value="">-- Pilih Batch --</option>' : '<option value="">Pilih Produk Terlebih Dahulu</option>';

            $('#pvdcBody').append(`
        <tr>
            <td>
                <select name="data_pvdc[${index}][mesin]" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Mesin --</option>${mesinOptions}
                </select>
            </td>
            <td>
                <select name="data_pvdc[${index}][kode_batch]" class="form-control form-control-sm batchSelect" required ${disabledAttr}>
                    ${initialOption}
                </select>
            </td>
            <td>
                <input type="file" name="data_pvdc[${index}][kode_produksi]" class="form-control form-control-sm" accept="image/*" required>
                <div class="preview mt-2"></div>
            </td>
            <td><input type="text" name="data_pvdc[${index}][keterangan]" class="form-control form-control-sm"><div class="invalid-feedback"></div></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
            </td>
        </tr>
            `);
            loadBatchForAllRows();
        });

    // ===================== HAPUS BARIS =====================
        $('#pvdcBody').on('click', '.removeRow', function(){ $(this).closest('tr').remove(); });

        $('#pvdcBody').on('click', '.saveRow', function(){
            const row = $(this).closest('tr');
            const btn = $(this);
            const mesin = row.find('select[name^="data_pvdc"]').val();
            const kodeBatch = row.find('input[name$="[kode_batch]"]').val();
            const fileInput = row.find('input[type="file"]');
            const file = fileInput[0].files[0];
            const ket = row.find('input[name$="[keterangan]"]').val();

            row.find('.invalid-feedback').remove();
            let valid = true;
            if(!mesin){ row.find('select[name^="data_pvdc"]').after('<div class="invalid-feedback d-block">Mesin wajib dipilih</div>'); valid=false;}
            // if(!validateKodeBatch(row.find('input[name$="[kode_batch]"]'))) valid=false;
            if(!validateFile(fileInput)) valid=false;
            if(!valid) return;

            const formData = new FormData();
            formData.append('mesin', mesin);
            formData.append('kode_batch', kodeBatch);
            if(file) formData.append('file', file);
            formData.append('keterangan', ket || '');
            formData.append('_token','{{ csrf_token() }}');

            btn.prop('disabled',true).html('<i class="bi bi-hourglass-split"></i> Menyimpan...');
            $.ajax({
                url: "{{ route('labelisasi_pvdc.saveRowTemp') }}",
                type: "POST",
                data: formData,
                processData:false,
                contentType:false,
                success:function(res){
                    if(res.success){
                        if(res.file){
                            const preview = `<a href="${res.file}" target="_blank" class="d-block mt-2"><img src="${res.file}" width="100" class="img-thumbnail"></a>`;
                            row.find('.preview').html(preview);
                        }
                        btn.removeClass('btn-primary').addClass('btn-success').html('<i class="bi bi-check-circle"></i> Tersimpan');
                    }else{
                        row.find('input[type="file"]').after('<div class="invalid-feedback d-block">'+res.message+'</div>');
                        btn.prop('disabled',false).html('Simpan');
                    }
                },
                error:function(err){
                    let errors = err.responseJSON?.errors || {};
                    if(errors.kode_batch) row.find('input[name$="[kode_batch]"]').after('<div class="invalid-feedback d-block">'+errors.kode_batch[0]+'</div>');
                    if(errors.file) row.find('input[type="file"]').after('<div class="invalid-feedback d-block">'+errors.file[0]+'</div>');
                    btn.prop('disabled',false).html('Simpan');
                }
            });
        });

        $('#saveBtn').click(function(){
            const btn = $(this);
            let isValid = true;
            $('#pvdcBody tr').each(function(){
                const kodeBatchInput = $(this).find('input[name$="[kode_batch]"]');
                const kodeBatchSelect = $(this).find('select[name$="[kode_batch]"]');
                const fileInput = $(this).find('input[type="file"]');
                const mesinSelect = $(this).find('select[name^="data_pvdc"]');

                // Validate kode_batch - either input or select
                if(kodeBatchInput.length > 0 && !kodeBatchInput.val()) isValid=false;
                if(kodeBatchSelect.length > 0 && !kodeBatchSelect.val()){
                    kodeBatchSelect.addClass('is-invalid');
                    if(kodeBatchSelect.next('.invalid-feedback').length === 0){
                        kodeBatchSelect.after('<div class="invalid-feedback d-block">Kode batch wajib dipilih</div>');
                    }
                    isValid=false;
                }

                if(!validateFile(fileInput)) isValid=false;
                if(!mesinSelect.val()){
                    mesinSelect.addClass('is-invalid');
                    if(mesinSelect.next('.invalid-feedback').length === 0){
                        mesinSelect.after('<div class="invalid-feedback d-block">Mesin wajib dipilih</div>');
                    }
                    isValid=false;
                }
            });
            if(!isValid) return;

            btn.prop('disabled',true).html('<i class="bi bi-hourglass-split"></i> Menyimpan...');
            const formData = new FormData($('#pvdcEditForm')[0]);
            $.ajax({
                url: "{{ route('labelisasi_pvdc.update_qc',$labelisasi_pvdc->uuid) }}",
                type: "POST",
                data: formData,
                processData:false,
                contentType:false,
                success:function(res){
                    if(res.success) window.location.href = res.redirect_url;
                    else $('#resultArea').html('<div class="alert alert-danger">'+res.message+'</div>');
                },
                error:function(){ $('#resultArea').html('<div class="alert alert-danger">Terjadi kesalahan saat menyimpan data.</div>'); },
                complete:function(){ btn.prop('disabled',false).html('<i class="bi bi-save"></i> Simpan'); }
            });
        });
    });
</script>
@endsection