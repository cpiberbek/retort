@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Data No. Lot PVDC</h4>

                <form method="POST" action="{{ route('pvdc.edit_spv', $pvdc->uuid) }}">
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
                                        value="{{ old('date', $pvdc->date) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Shift</label>
                                    <select name="shift" class="form-control" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="1" {{ old('shift', $pvdc->shift) == '1' ? 'selected' : '' }}>
                                            Shift 1</option>
                                        <option value="2" {{ old('shift', $pvdc->shift) == '2' ? 'selected' : '' }}>
                                            Shift 2</option>
                                        <option value="3" {{ old('shift', $pvdc->shift) == '3' ? 'selected' : '' }}>
                                            Shift 3</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Varian</label>
                                    <select id="nama_produk" name="nama_produk" class="form-control selectpicker"
                                        data-live-search="true" required>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}"
                                                {{ old('nama_produk', $pvdc->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
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
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->nama_supplier }}"
                                                {{ old('nama_supplier', $pvdc->nama_supplier) == $supplier->nama_supplier ? 'selected' : '' }}>
                                                {{ $supplier->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Kedatangan PVDC</label>
                                    <input type="date" name="tgl_kedatangan" class="form-control"
                                        value="{{ old('tgl_kedatangan', $pvdc->tgl_kedatangan) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Expired</label>
                                    <input type="date" name="tgl_expired" class="form-control"
                                        value="{{ old('tgl_expired', $pvdc->tgl_expired) }}" required>
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
                                    @forelse($pvdcData as $mi => $mesin)
                                        @foreach ($mesin['detail'] as $bi => $batch)
                                            <tr class="{{ $bi == 0 ? 'mesin-row' : 'batch-row' }}">
                                                @if ($bi == 0)
                                                    <td rowspan="{{ count($mesin['detail']) }}" class="mesin-cell">
                                                        <select name="data_pvdc[{{ $mi }}][mesin]"
                                                            class="form-control form-control-sm" required>
                                                            <option value="">-- Pilih Mesin --</option>
                                                            @foreach ($mesins as $m)
                                                                <option value="{{ $m->nama_mesin }}"
                                                                    {{ $mesin['mesin'] == $m->nama_mesin ? 'selected' : '' }}>
                                                                    {{ $m->nama_mesin }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                @endif
                                                <td>
                                                    <select
                                                        name="data_pvdc[{{ $mi }}][detail][{{ $bi }}][batch]"
                                                        class="form-control form-control-sm batchSelect"
                                                        data-selected="{{ $batch['batch'] ?? '' }}">
                                                        @if(!empty($batch['batch']))
                                                            <option value="{{ $batch['batch'] }}" selected>{{ $batch['batch_display'] ?? $batch['batch'] }}</option>
                                                        @else
                                                            <option value="">-- Pilih Batch --</option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td><input type="text"
                                                        name="data_pvdc[{{ $mi }}][detail][{{ $bi }}][no_lot]"
                                                        value="{{ $batch['no_lot'] ?? '' }}"
                                                        class="form-control form-control-sm"></td>
                                                <td><input type="time"
                                                        name="data_pvdc[{{ $mi }}][detail][{{ $bi }}][waktu]"
                                                        value="{{ $batch['waktu'] ?? '' }}"
                                                        class="form-control form-control-sm"></td>
                                                <td>
                                                    @if ($bi == 0)
                                                        <button type="button" class="btn btn-success btn-sm addBatchRow">+
                                                            Batch</button>
                                                    @endif
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm removeRow">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr class="mesin-row">
                                            <td rowspan="1" class="mesin-cell">
                                                <select name="data_pvdc[0][mesin]" class="form-control form-control-sm"
                                                    required>
                                                    <option value="">-- Pilih Mesin --</option>
                                                    @foreach ($mesins as $m)
                                                        <option value="{{ $m->nama_mesin }}">{{ $m->nama_mesin }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="data_pvdc[0][detail][0][batch]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="text" name="data_pvdc[0][detail][0][no_lot]"
                                                    class="form-control form-control-sm"></td>
                                            <td><input type="time" name="data_pvdc[0][detail][0][waktu]"
                                                    class="form-control form-control-sm"></td>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm addBatchRow">+
                                                    Batch</button>
                                                <button type="button"
                                                    class="btn btn-danger btn-sm removeRow">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                        <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Update</button>
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
                const produkValue = $('#nama_produk').val();
                
                if (!produkValue) return;

                $('.batchSelect').each(function() {
                    let select = $(this);
                    
                    if (!select.val()) {
                        select.html('<option value="">-- Pilih Batch --</option>');
                    }
                    
                    initBatchSelect(select, produkValue);
                });
            }

            $('#nama_produk').on('change', function() {
                $(".batchSelect").empty().trigger('change');
                loadBatchForAllRows();
            });

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
