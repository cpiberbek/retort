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
                                        @foreach ($produks as $produk)
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
                                        @foreach ($suppliers as $supplier)
    <option value="{{ $supplier->nama_supplier }}"
                                            {{ isset($pvdc['nama_supplier']) && $pvdc['nama_supplier'] == $supplier->nama_supplier ? 'selected' : '' }}>
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
                                                        $details =
                                                            isset($mesin['detail']) && is_array($mesin['detail'])
                                                                ? $mesin['detail']
                                                                : [];
                                                    @endphp

                                                    @forelse($details as $bi => $detail)
                                                        <tr>
                                                            @if ($loop->first)
                                                                <td rowspan="{{ count($details) }}"
                                                                    class="fw-semibold text-dark bg-light">
                                                                    {{ $mesin['mesin'] ?? '-' }}

                                                                    <input type="hidden"
                                                                        name="data_pvdc_old[{{ $mi }}][mesin]"
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
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $pvdc->catatan) }}</textarea>
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

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    
    {{-- Select2 CSS & JS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });
    </script>
    <script>
        $(document).ready(function() {

            const produkValue = "{{ $pvdc->nama_produk }}";

            const mesinOptions = `{!! collect($mesins)->map(fn($m) => "<option value='{$m->nama_mesin}'>{$m->nama_mesin}</option>")->implode('') !!}`;

            const tableBody = $('#pvdcBody');
            let mesinIndex = {{ count($pvdcData) }};

            // ==========================
            // LOAD BATCH
            // ==========================
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
                // Gunakan produkValue dari blade, karena di form update tidak ada input #nama_produk
                if (!produkValue) return;

                $('.batchSelect').each(function() {
                    let select = $(this);
                    
                    if (!select.val()) {
                        select.html('<option value="">-- Pilih Batch --</option>');
                    }
                    
                    initBatchSelect(select, produkValue);
                });
            }

            // Load pertama kali
            loadBatchForAllRows();

            // ==========================
            // TAMBAH MESIN
            // ==========================
            $('#addMesinRow').on('click', function() {

                const newRow = `
        <tr class="mesin-row">
            <td rowspan="1" class="mesin-cell">
                <select name="data_pvdc[${mesinIndex}][mesin]"
                        class="form-control form-control-sm"
                        required>
                    <option value="">-- Pilih Mesin --</option>
                    ${mesinOptions}
                </select>
            </td>

            <td>
                <select
                    name="data_pvdc[${mesinIndex}][detail][0][batch]"
                    class="form-control form-control-sm batchSelect">
                    <option value="">-- Pilih Batch --</option>
                </select>
            </td>

            <td>
                <input type="text"
                    name="data_pvdc[${mesinIndex}][detail][0][no_lot]"
                    class="form-control form-control-sm">
            </td>

            <td>
                <input type="time"
                    name="data_pvdc[${mesinIndex}][detail][0][waktu]"
                    class="form-control form-control-sm">
            </td>

            <td>
                <button type="button"
                    class="btn btn-success btn-sm addBatchRow">
                    + Batch
                </button>

                <button type="button"
                    class="btn btn-danger btn-sm removeRow">
                    Hapus
                </button>
            </td>
        </tr>`;

                tableBody.append(newRow);

                loadBatchForAllRows();

                mesinIndex++;
            });

            // ==========================
            // TAMBAH BATCH
            // ==========================
            tableBody.on('click', '.addBatchRow', function() {

                const mesinRow = $(this).closest('tr');
                const mesinCell = mesinRow.find('.mesin-cell');

                const mesinIdx = mesinRow
                    .find('select[name*="[mesin]"]')
                    .attr('name')
                    .match(/\[(\d+)\]/)[1];

                const currentBatchRows = tableBody.find(
                    `select[name^="data_pvdc[${mesinIdx}][detail]"][name$="[batch]"]`
                ).length;

                const batchIndex = currentBatchRows;

                const newBatchRow = `
        <tr class="batch-row">

            <td>
                <select
                    name="data_pvdc[${mesinIdx}][detail][${batchIndex}][batch]"
                    class="form-control form-control-sm batchSelect">
                    <option value="">-- Pilih Batch --</option>
                </select>
            </td>

            <td>
                <input type="text"
                    name="data_pvdc[${mesinIdx}][detail][${batchIndex}][no_lot]"
                    class="form-control form-control-sm">
            </td>

            <td>
                <input type="time"
                    name="data_pvdc[${mesinIdx}][detail][${batchIndex}][waktu]"
                    class="form-control form-control-sm">
            </td>

            <td>
                <button type="button"
                    class="btn btn-danger btn-sm removeRow">
                    Hapus
                </button>
            </td>

        </tr>`;

                let lastBatchRow = mesinRow;

                while (lastBatchRow.next().hasClass('batch-row')) {
                    lastBatchRow = lastBatchRow.next();
                }

                lastBatchRow.after(newBatchRow);

                mesinCell.attr(
                    'rowspan',
                    parseInt(mesinCell.attr('rowspan')) + 1
                );

                loadBatchForAllRows();
            });

            // ==========================
            // HAPUS BARIS
            // ==========================
            tableBody.on('click', '.removeRow', function() {

                const tr = $(this).closest('tr');

                if (tr.hasClass('mesin-row')) {

                    const rowspan = parseInt(
                        tr.find('.mesin-cell').attr('rowspan')
                    );

                    tr.nextAll(':lt(' + (rowspan - 1) + ')').remove();

                    tr.remove();

                } else {

                    const mesinRow = tr.prevAll('.mesin-row:first');
                    const mesinCell = mesinRow.find('.mesin-cell');

                    mesinCell.attr(
                        'rowspan',
                        parseInt(mesinCell.attr('rowspan')) - 1
                    );

                    tr.remove();
                }
            });

        });
    </script>

@endpush

@push('styles')
    <style>
        .table-bordered th,
        .table-bordered td {
            text-align: center;
            vertical-align: middle;
        }

        .form-control-sm {
            min-width: 120px;
        }
        
        /* Select2 bootstrap 5 styling override untuk form ini */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: calc(1.5em + .5rem + 2px) !important;
            border-radius: .25rem !important;
            font-size: .875rem;
        }
    </style>
@endpush
