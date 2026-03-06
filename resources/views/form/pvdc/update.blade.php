@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Update Data No. Lot PVDC</h4>

            <form method="POST" action="{{ route('pvdc.update_qc', $pvdc->uuid) }}">
                @csrf
                @method('PUT')

                {{-- ===================== Bagian Identitas ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data PVDC</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control"
                                    value="{{ old('date', $pvdc->date) }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" readonly>
                                    <option value="1" {{ $pvdc->shift == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ $pvdc->shift == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ $pvdc->shift == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <input type="text" name="nama_produk" class="form-control"
                                    value="{{ old('nama_produk', $pvdc->nama_produk) }}" readonly>
                                <!-- <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk...">
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ $pvdc->nama_produk == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select> -->
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Supplier</label>
                                <input type="text" name="nama_supplier" class="form-control"
                                    value="{{ old('nama_supplier', $pvdc->nama_supplier) }}" readonly>
                                <!-- <select id="nama_supplier" name="nama_supplier" class="form-control selectpicker" data-live-search="true">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->nama_supplier }}"
                                        {{ (isset($pvdc['nama_supplier']) && $pvdc['nama_supplier'] == $supplier->nama_supplier) ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                    @endforeach
                                </select> -->
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Kedatangan PVDC</label>
                                <input type="date" name="tgl_kedatangan" class="form-control"
                                    value="{{ old('tgl_kedatangan', $pvdc->tgl_kedatangan) }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Expired</label>
                                <input type="date" name="tgl_expired" class="form-control"
                                    value="{{ old('tgl_expired', $pvdc->tgl_expired) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Data PVDC</strong>
                        <!-- <button type="button" id="addMesinRow" class="btn btn-primary btn-sm">+ Tambah Mesin</button> -->
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            {{-- ===================== TOGGLE DATA SEBELUMNYA ===================== --}}
                            <div class="d-flex justify-content-between mb-3">
                                <button type="button" id="toggleOldData" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-eye-slash"></i> Sembunyikan Data Sebelumnya
                                </button>
                            </div>

                            {{-- ===================== TABEL DATA SEBELUMNYA ===================== --}}
                            <div id="oldPvdcSection">
                                <h6 class="fw-bold mb-2 text-secondary">
                                    <i class="bi bi-clock-history"></i> Data Sebelumnya
                                </h6>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm text-center align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Mesin</th>
                                                <th>Batch</th>
                                                <th>No. Lot</th>
                                                <th>Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pvdcData as $mi => $mesin)

                                            @php
                                            $details = isset($mesin['detail']) && is_array($mesin['detail'])
                                            ? $mesin['detail']
                                            : [];
                                            @endphp

                                            @forelse($details as $bi => $detail)
                                            <tr>
                                                @if($loop->first)
                                                <td rowspan="{{ count($details) }}"
                                                    class="fw-semibold text-dark bg-light">
                                                    {{ $mesin['mesin'] ?? '-' }}

                                                    <input type="hidden" name="data_pvdc_old[{{ $mi }}][mesin]"
                                                        value="{{ $mesin['mesin'] ?? '' }}">
                                                </td>
                                                @endif

                                                <td>
                                                    {{ $detail['batch_display'] ?? '-' }}

                                                    <input type="hidden"
                                                        name="data_pvdc_old[{{ $mi }}][detail][{{ $bi }}][batch]"
                                                        value="{{ $detail['batch'] ?? '' }}">
                                                </td>

                                                <td>
                                                    {{ $detail['no_lot'] ?? '-' }}
                                                    <input type="hidden"
                                                        name="data_pvdc_old[{{ $mi }}][detail][{{ $bi }}][no_lot]"
                                                        value="{{ $detail['no_lot'] ?? '' }}">
                                                </td>

                                                <td>
                                                    {{ $detail['waktu'] ?? '-' }}
                                                    <input type="hidden"
                                                        name="data_pvdc_old[{{ $mi }}][detail][{{ $bi }}][waktu]"
                                                        value="{{ $detail['waktu'] ?? '' }}">
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-muted fst-italic">
                                                    Tidak ada detail batch
                                                </td>
                                            </tr>
                                            @endforelse

                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-muted fst-italic">
                                                    Belum ada data sebelumnya
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>

                                    </table>
                                </div>
                            </div>

                            {{-- ===================== TABEL TAMBAH DATA BARU ===================== --}}
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="bi bi-plus-circle text-primary"></i> Tambah Data Baru
                                    </h6>
                                    <button type="button" id="addMesinRow" class="btn btn-primary btn-sm">+ Tambah
                                        Mesin</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm text-center align-middle"
                                        id="pvdcTable">
                                        <thead class="table-warning">
                                            <tr>
                                                <th>Mesin</th>
                                                <th>Batch</th>
                                                <th>No. Lot</th>
                                                <th>Waktu</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pvdcBody">
                                            <tr class="text-center text-muted">
                                                <td colspan="5">
                                                    Belum ada data baru. Klik <strong>+ Tambah Mesin</strong> untuk
                                                    menambah.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ===================== Catatan ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3"
                            placeholder="Tambahkan catatan bila ada">{{ old('catatan', $pvdc->catatan) }}</textarea>
                    </div>
                </div>

                {{-- ===================== Tombol Simpan ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Update Data</button>
                    <a href="{{ route('pvdc.index') }}" class="btn btn-secondary w-auto"><i
                            class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== SCRIPT ===================== --}}
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
    const produkValue = "{{ $pvdc->nama_produk }}";

        // Initialize batch loading if product exists
        if (produkValue) {
            loadBatchForAllRows();
        }

    function loadBatchForAllRows() {
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

            if (!produkValue) {
                select.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
                return;
            }

            fetch(`/lookup/batch/${produkValue}`)
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
        let mesinIndex = {{ count($pvdcData) }};

    // Tambah Mesin Baru
        document.getElementById('addMesinRow').addEventListener('click', function() {
            const produkValue = "{{ $pvdc->nama_produk }}";
            const disabledAttr = produkValue ? '' : 'disabled';
            const initialOption = produkValue ? '<option value="">-- Pilih Batch --</option>' : '<option value="">Pilih Varian Terlebih Dahulu</option>';

            const newRow = `
            <tr class="mesin-row">
                <td rowspan="1" class="mesin-cell">
                    <select name="data_pvdc[${mesinIndex}][mesin]" class="form-control form-control-sm mesin-input">
                        <option value="">-- Pilih Mesin --</option>
                        @foreach($mesins as $m)
                            <option value="{{ $m->nama_mesin }}">{{ $m->nama_mesin }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="data_pvdc[${mesinIndex}][detail][0][batch]" class="form-control form-control-sm batchSelect" ${disabledAttr}>
                        ${initialOption}
                    </select>
                </td>
                <td><input type="text" name="data_pvdc[${mesinIndex}][detail][0][no_lot]" class="form-control form-control-sm"></td>
                <td><input type="time" name="data_pvdc[${mesinIndex}][detail][0][waktu]" class="form-control form-control-sm"></td>
                <td>
                    <button type="button" class="btn btn-success btn-sm addBatchRow">+ Batch</button>
                    <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
                </td>
            </tr>
            `;
            document.querySelector('#pvdcBody').insertAdjacentHTML('beforeend', newRow);
            loadBatchForAllRows();
            mesinIndex++;
        });

    // Delegasi Event
        document.querySelector('#pvdcBody').addEventListener('click', function(e) {
            const target = e.target;

        // Tambah Batch Baru
            if (target.classList.contains('addBatchRow')) {
                const mesinRow = target.closest('.mesin-row');
                const mesinCell = mesinRow.querySelector('.mesin-cell');
                const currentRowspan = parseInt(mesinCell.getAttribute('rowspan')) || 1;
                const mesinIdx = mesinRow.querySelector('.mesin-input').name.match(/\d+/)[0];
                const batchIdx = currentRowspan;

            // Baris batch baru
                const produkValue = "{{ $pvdc->nama_produk }}";
                const disabledAttr = produkValue ? '' : 'disabled';
                const initialOption = produkValue ? '<option value="">-- Pilih Batch --</option>' : '<option value="">Pilih Produk Terlebih Dahulu</option>';

                const newBatchRow = `
                <tr class="batch-row">
                    <td>
                        <select name="data_pvdc[${mesinIdx}][detail][${batchIdx}][batch]" class="form-control form-control-sm batchSelect" ${disabledAttr}>
                            ${initialOption}
                        </select>
                    </td>
                    <td><input type="text" name="data_pvdc[${mesinIdx}][detail][${batchIdx}][no_lot]" class="form-control form-control-sm"></td>
                    <td><input type="time" name="data_pvdc[${mesinIdx}][detail][${batchIdx}][waktu]" class="form-control form-control-sm"></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button></td>
                </tr>
                `;

            // ✅ Cari batch terakhir dari mesin yang sama
                let lastBatchRow = mesinRow;
                let nextRow = mesinRow.nextElementSibling;
                while (nextRow && !nextRow.classList.contains('mesin-row')) {
                    lastBatchRow = nextRow;
                    nextRow = nextRow.nextElementSibling;
                }

            // Tambahkan batch baru di bawah batch terakhir
                lastBatchRow.insertAdjacentHTML('afterend', newBatchRow);
                mesinCell.setAttribute('rowspan', currentRowspan + 1);
            }

        // Hapus Baris
            if (target.classList.contains('removeRow')) {
                const row = target.closest('tr');
                const mesinRow = row.classList.contains('mesin-row') ? row : row.previousElementSibling.closest('.mesin-row');

                if (row.classList.contains('mesin-row')) {
                // Hapus semua batch milik mesin tersebut
                    let nextRow = row.nextElementSibling;
                    while (nextRow && !nextRow.classList.contains('mesin-row')) {
                        const temp = nextRow.nextElementSibling;
                        nextRow.remove();
                        nextRow = temp;
                    }
                    row.remove();
                } else {
                    row.remove();
                    const mesinCell = mesinRow.querySelector('.mesin-cell');
                    mesinCell.setAttribute('rowspan', parseInt(mesinCell.getAttribute('rowspan')) - 1);
                }
            }
        });
    });
</script>
{{-- ===================== SCRIPT TOGGLE ===================== --}}
<script>
    document.getElementById('toggleOldData').addEventListener('click', function() {
        const section = document.getElementById('oldPvdcSection');
        const icon = this.querySelector('i');
        if (section.style.display === 'none') {
            section.style.display = '';
            this.innerHTML = '<i class="bi bi-eye-slash"></i> Sembunyikan Data Sebelumnya';
        } else {
            section.style.display = 'none';
            this.innerHTML = '<i class="bi bi-eye"></i> Tampilkan Data Sebelumnya';
        }
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

    .bg-light input[readonly] {
        background-color: #f8f9fa !important;
        color: #555;
    }

    .table-active td {
        background-color: #fff3cd !important;
    }
</style>
@endsection