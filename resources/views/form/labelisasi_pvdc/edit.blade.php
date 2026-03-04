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
                                        <input type="text" name="data_pvdc[{{ $i }}][kode_batch]" class="form-control form-control-sm" value="{{ $row['kode_batch'] }}" required>
                                    </td>
                                    <td>
                                        <input type="file" name="data_pvdc[{{ $i }}][kode_produksi]" class="form-control form-control-sm" accept="image/*">
                                        <div class="preview mt-2">
                                            @if(!empty($row['file']))
                                            <a href="{{ $row['file'] }}" target="_blank">
                                                <img src="{{ $row['file'] }}" width="100" class="img-thumbnail">
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

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
$(document).ready(function(){
    $('.selectpicker').selectpicker();

    // VALIDASI KODE BATCH
    function validateKodeBatch(input){
        let value = input.val().toUpperCase().replace(/\s+/g,'');
        input.val(value);
        input.next(".invalid-feedback").remove();
        input.removeClass("is-invalid");

        if(value.length !== 10){
            showError(input,"Kode batch harus terdiri dari 10 karakter.");
            return false;
        }
        const format = /^[A-Z0-9]+$/;
        if(!format.test(value)){
            showError(input,"Kode batch hanya boleh huruf besar dan angka.");
            return false;
        }
        const bulanChar = value.charAt(1);
        if(!/^[A-L]$/.test(bulanChar)){
            showError(input,"Karakter ke-2 harus huruf bulan (A–L).");
            return false;
        }
        const rework = value.charAt(9);
        if(!["0","1"].includes(rework)){
            showError(input,"Karakter terakhir harus 0 (belum rework) atau 1 (rework).");
            return false;
        }
        return true;
    }

    function showError(input,msg){
        input.addClass("is-invalid");
        input.after(`<div class="invalid-feedback">${msg}</div>`);
    }

    $(document).on("input blur",'input[name$="[kode_batch]"]', function(){
        validateKodeBatch($(this));
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
                <input type="text" name="data_pvdc[${index}][kode_batch]" class="form-control form-control-sm" required>
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
@endsection
