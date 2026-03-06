@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Data No. Lot Wire</h4>

            <form method="POST" action="{{ route('wire.store') }}">
                @csrf

                {{-- ===================== Bagian Identitas ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Wire</strong>
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
                                <label class="form-label">Nama Varian</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama varian..." required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $data->nama_produk ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Supplier</label>
                                <select id="nama_supplier" name="nama_supplier" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->nama_supplier }}">{{ $supplier->nama_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== Bagian Data Wire ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Data Wire</strong>
                        <button type="button" id="addMesinRow" class="btn btn-primary btn-sm">+ Tambah Mesin</button>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm text-center align-middle" id="wireTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Mesin</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>No. Lot</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="wireBody">
                                {{-- Baris pertama default --}}
                                <tr class="mesin-row">
                                    <td rowspan="1" class="mesin-cell">
                                        <select name="data_wire[0][mesin]" class="form-control form-control-sm" required>
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach($mesins as $mesin)
                                            <option value="{{ $mesin->nama_mesin }}">{{ $mesin->nama_mesin }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="time" name="data_wire[0][detail][0][start]" class="form-control form-control-sm"></td>
                                    <td><input type="time" name="data_wire[0][detail][0][end]" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="data_wire[0][detail][0][no_lot]" class="form-control form-control-sm"></td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm addBatchRow">+ Batch</button>
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
                    <a href="{{ route('wire.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
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

        const tableBody = $('#wireBody');
        let mesinIndex = 0;

    // === Tambah Mesin ===
        $('#addMesinRow').on('click', function() {
            mesinIndex++;
            const newRow = `
        <tr class="mesin-row">
            <td rowspan="1" class="mesin-cell">
                <select name="data_wire[${mesinIndex}][mesin]" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Mesin --</option>
                    ${mesinOptions}
                </select>
            </td>
            <td><input type="time" name="data_wire[${mesinIndex}][detail][0][start]" class="form-control form-control-sm"></td>
            <td><input type="time" name="data_wire[${mesinIndex}][detail][0][end]" class="form-control form-control-sm"></td>
            <td><input type="text" name="data_wire[${mesinIndex}][detail][0][no_lot]" class="form-control form-control-sm"></td>
            <td>
                <button type="button" class="btn btn-success btn-sm addBatchRow">+ Batch</button>
                <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
            </td>
            </tr>`;
            tableBody.append(newRow);
        });
// === Tambah Batch ===
        tableBody.on('click', '.addBatchRow', function() {
    const mesinRow = $(this).closest('tr'); // baris utama mesin
    const mesinIndex = mesinRow.find('select').attr('name').match(/\[(\d+)\]/)[1];

    // Cari semua baris batch dari mesin yang sama (setelah mesin utama)
    const sameMesinRows = mesinRow.nextAll('tr').filter(function () {
        const nameAttr = $(this).find('input[name*="[start]"]').attr('name');
        return nameAttr && nameAttr.startsWith(`data_wire[${mesinIndex}]`);
    });

    const batchCount = sameMesinRows.length + 1;

    // 🔍 Ambil waktu END terakhir dari batch sebelumnya atau mesin utama
    let lastEndTime = '';
    if (sameMesinRows.length > 0) {
        // Ambil dari batch terakhir
        lastEndTime = sameMesinRows.last().find('input[name*="[end]"]').val();
    } else {
        // Ambil dari mesin utama
        lastEndTime = mesinRow.find('input[name*="[end]"]').val();
    }

    // Kalau masih kosong (belum diisi user), pakai waktu sekarang
    if (!lastEndTime) {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        lastEndTime = `${hh}:${mm}`;
    }

    // 🔧 Pastikan value benar-benar terpasang (debugging)
    console.log("Start baru:", lastEndTime);

    // Buat baris batch baru
    const newBatchRow = `
        <tr class="batch-row">
            <td>
                <input type="time" name="data_wire[${mesinIndex}][detail][${batchCount}][start]" 
                       class="form-control form-control-sm" value="${lastEndTime}">
            </td>
            <td><input type="time" name="data_wire[${mesinIndex}][detail][${batchCount}][end]" class="form-control form-control-sm"></td>
            <td><input type="text" name="data_wire[${mesinIndex}][detail][${batchCount}][no_lot]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button></td>
    </tr>`;

    // Tambahkan batch baru setelah batch terakhir mesin
    if (sameMesinRows.length > 0) {
        sameMesinRows.last().after(newBatchRow);
    } else {
        mesinRow.after(newBatchRow);
    }

    // Update rowspan supaya tabel tetap rapi
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
    .table-bordered th, .table-bordered td {
        text-align: center;
        vertical-align: middle;
    }
    .form-control-sm { min-width: 120px; }
</style>
@endsection
