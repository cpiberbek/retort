@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Data No. Lot PVDC</h4>

            <form method="POST" action="{{ route('pvdc.store') }}">
                @csrf

                {{-- ===================== Bagian Identitas ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data PVDC</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control"
                                    value="{{ old('date', $data->date ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker"
                                    data-live-search="true" title="Ketik nama produk..." required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $data->nama_produk
                                        ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Supplier</label>
                                <select id="nama_supplier" name="nama_supplier" class="form-control selectpicker"
                                    data-live-search="true" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->nama_supplier }}">{{ $supplier->nama_supplier }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Kedatangan PVDC</label>
                                <input type="date" id="tgl_kedatangan" name="tgl_kedatangan" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Expired</label>
                                <input type="date" id="tgl_expired" name="tgl_expired" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== Bagian Data PVDC ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Data PVDC</strong>
                        <button type="button" id="addMesinRow" class="btn btn-primary btn-sm">+ Tambah Mesin</button>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm text-center align-middle" id="pvdcTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Mesin</th>
                                    <th>Batch</th>
                                    <th>No. Lot</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pvdcBody">
                                {{-- Baris pertama default --}}
                                <tr class="mesin-row">
                                    <td rowspan="1" class="mesin-cell">
                                        <select name="data_pvdc[0][mesin]" class="form-control form-control-sm"
                                            required>
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach($mesins as $mesin)
                                            <option value="{{ $mesin->nama_mesin }}">{{ $mesin->nama_mesin }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="data_pvdc[0][detail][0][batch]"
                                            class="form-control form-control-sm batchSelect" required disabled>
                                            <option value="">Pilih Produk Terlebih Dahulu</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="data_pvdc[0][detail][0][no_lot]"
                                            class="form-control form-control-sm"></td>
                                    <td><input type="time" name="data_pvdc[0][detail][0][waktu]"
                                            class="form-control form-control-sm"></td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm addBatchRow">+
                                            Batch</button>
                                        <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ===================== Catatan ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3"
                            placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- ===================== Tombol Simpan ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('pvdc.index') }}" class="btn btn-secondary w-auto"><i
                            class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

    // Ambil waktu sekarang
        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');
        let min = String(now.getMinutes()).padStart(2, '0');

    // Set value tanggal dan jam
        dateInput.value = `${yyyy}-${mm}-${dd}`;

    // Tentukan shift berdasarkan jam
        let hour = parseInt(hh);
        if (hour >= 7 && hour < 15) {
            shiftInput.value = "1";
        } else if (hour >= 15 && hour < 23) {
            shiftInput.value = "2";
        } else {
            shiftInput.value = "3"; 
        }

    });
</script>
<script>
    $(document).ready(function(){

    // Ambil daftar mesin dari Laravel (Blade ke JS)
        const mesinOptions = `{!! collect($mesins)
        ->map(fn($m) => "<option value='{$m->nama_mesin}'>{$m->nama_mesin}</option>")
        ->implode('') !!}`;

        const tableBody = $('#pvdcBody');
        let mesinIndex = 0;

    // === Tambah Mesin ===
        $('#addMesinRow').on('click', function() {
            mesinIndex++;
            const newRow = `
        <tr class="mesin-row">
            <td rowspan="1" class="mesin-cell">
                <select name="data_pvdc[${mesinIndex}][mesin]" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Mesin --</option>
                    ${mesinOptions}
                </select>
            </td>
            <td>
                <select name="data_pvdc[${mesinIndex}][detail][0][batch]"
                    class="form-control form-control-sm batchSelect" required disabled>
                    <option value="">Pilih Produk Terlebih Dahulu</option>
                </select>
            </td>
            <td><input type="text" name="data_pvdc[${mesinIndex}][detail][0][no_lot]" class="form-control form-control-sm"></td>
            <td><input type="time" name="data_pvdc[${mesinIndex}][detail][0][waktu]" class="form-control form-control-sm"></td>
            <td>
                <button type="button" class="btn btn-success btn-sm addBatchRow">+ Batch</button>
                <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
            </td>
            </tr>`;
            tableBody.append(newRow);
            const newBatchSelect = tableBody.find('.batchSelect').last();
            loadBatchForSelect(newBatchSelect);
        });

        const produkSelect = $("#nama_produk");

    // Trigger saat produk diganti
    produkSelect.on("change", function () {
        loadBatchForAllRows();
    });

    function loadBatchForSelect(select) {
        let produk = produkSelect.val();
        select.html("");
        select.prop("disabled", true);

        if (!produk) {
            select.html('<option value="">Pilih Produk Terlebih Dahulu</option>');
            return;
        }

        fetch("{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}".replace('__PRODUK__', encodeURIComponent(produk)))
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
                        <option value="${batch.uuid}" batch="${batch.kode_produksi}">
                            ${batch.kode_produksi}
                        </option>
                    `);
                });
            });
    }

    function loadBatchForAllRows() {
        let batchSelects = $(".batchSelect");
        batchSelects.each(function () {
            loadBatchForSelect($(this));
        });
    }

    // === Tambah Batch ===
        tableBody.on('click', '.addBatchRow', function() {
            const mesinRow = $(this).closest('tr');
            const mesinIndex = mesinRow.find('select').attr('name').match(/\[(\d+)\]/)[1];
            const batchCount = tableBody.find(`input[name^="data_pvdc[${mesinIndex}][detail]"]`).length / 3;

            const newBatchRow = `
            <tr class="batch-row">
                <td>
                    <select name="data_pvdc[${mesinIndex}][detail][${batchCount}][batch]"
                        class="form-control form-control-sm batchSelect" required disabled>
                        <option value="">Pilih Produk Terlebih Dahulu</option>
                    </select>
                </td>
                <td><input type="text" name="data_pvdc[${mesinIndex}][detail][${batchCount}][no_lot]" class="form-control form-control-sm"></td>
                <td><input type="time" name="data_pvdc[${mesinIndex}][detail][${batchCount}][waktu]" class="form-control form-control-sm"></td>
                <td><button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button></td>
            </tr>`;

            mesinRow.after(newBatchRow);
            const newBatchSelect = mesinRow.next().find('.batchSelect');
            loadBatchForSelect(newBatchSelect);
        // Tambah rowspan
            const mesinCell = mesinRow.find('.mesin-cell');
            mesinCell.attr('rowspan', parseInt(mesinCell.attr('rowspan')) + 1);
        });

    // === Hapus Row Mesin / Batch ===
        tableBody.on('click', '.removeRow', function() {
            const tr = $(this).closest('tr');
            if (tr.find('.mesin-cell').length) {
                let rowspan = parseInt(tr.find('.mesin-cell').attr('rowspan'));
                tr.nextAll(':lt(' + (rowspan - 1) + ')').remove();
            } else {
                const mesinRow = tr.prevAll('.mesin-row:first');
                const mesinCell = mesinRow.find('.mesin-cell');
                mesinCell.attr('rowspan', parseInt(mesinCell.attr('rowspan')) - 1);
            }
            tr.remove();
        });
    });
</script>

<style>
    .table-bordered th,
    .table-bordered td {
        text-align: center;
        vertical-align: middle;
    }

    .form-control-sm {
        min-width: 120px;
    }
</style>
@endsection