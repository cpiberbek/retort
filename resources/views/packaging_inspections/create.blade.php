@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    .form-label { font-weight: 600; color: #495057; }
    .form-control, .form-select { border-radius: 8px; }

    /* Memastikan Select2 Full Width dan Rapi */
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
    }
    .select2-container--bootstrap-5 .select2-selection { border-radius: 8px !important; }

    /* Style Tombol Check Group */
    .btn-check-group .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .dynamic-item-card { background-color: #fdfdfd; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            <h4 class="mb-1"><i class="bi bi-box-seam"></i> Tambah Pemeriksaan Packaging</h4>
            <p class="text-muted mb-4">Isi detail formulir pemeriksaan packaging di bawah ini.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            @endif

            <form action="{{ route('packaging-inspections.store') }}" method="POST">
                @csrf

                {{-- CARD INFORMASI UMUM --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Inspeksi</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="inspection_date" class="form-label">Hari/Tanggal</label>
                                <input type="date"
                                    class="form-control"
                                    id="inspection_date"
                                    name="inspection_date"
                                    value="{{ old('inspection_date', date('Y-m-d')) }}"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label for="qc_inspector" class="form-label">QC Inspector</label>
                                <input type="text"
                                    class="form-control"
                                    id="qc_inspector"
                                    value="{{ auth()->user()->name }}"
                                    readonly>
                            </div>

                            <div class="col-md-4">
                                <label for="shift" class="form-label">Shift</label>
                                <input type="text"
                                    class="form-control"
                                    id="shift"
                                    name="shift"
                                    value="{{ old('shift') }}"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DETAIL ITEM (DINAMIS) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><i class="bi bi-list-nested"></i> Detail Item Packaging</strong>
                            <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Item</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="details-container">
                            {{-- Item dinamis akan ditambahkan di sini oleh JS --}}
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-save"></i> Simpan Inspeksi</button>
                    <a href="{{ route('packaging-inspections.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-check-group .btn', function(e) {
            e.preventDefault();

            const button = $(this);
            const value = button.data('value');
            const targetInputId = button.data('target-input');

            if (targetInputId) {
                $(targetInputId).val(value);
            }

            const group = button.closest('.btn-check-group');

            group.find('.btn').each(function() {
                $(this).removeClass('btn-success btn-danger').addClass('btn-outline-secondary');
            });

            if (value === 'OK') {
                button.removeClass('btn-outline-secondary').addClass('btn-success');
            } else {
                button.removeClass('btn-outline-secondary').addClass('btn-danger');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0;

        function reindexDetails() {
            $('.dynamic-item-card').each(function(index) {
                $(this).find('h5').text(`Item #${index + 1}`);

                $(this).find('[name]').each(function() {
                    const name = $(this).attr('name');

                    if (name) {
                        $(this).attr(
                            'name',
                            name.replace(/items\[\d+\]/, `items[${index}]`)
                        );
                    }
                });

                $(this).find('[id]').each(function() {
                    const id = $(this).attr('id');

                    if (id) {
                        $(this).attr(
                            'id',
                            id.replace(/_\d+$/, `_${index}`)
                        );
                    }
                });

                $(this).find('[data-target-input]').each(function() {
                    const target = $(this).attr('data-target-input');

                    if (target) {
                        $(this).attr(
                            'data-target-input',
                            target.replace(/_\d+$/, `_${index}`)
                        );
                    }
                });

                $(this).find('label[for]').each(function() {
                    const forValue = $(this).attr('for');

                    if (forValue) {
                        $(this).attr(
                            'for',
                            forValue.replace(/_\d+_/, `_${index}_`)
                        );
                    }
                });
            });

            detailIndex = $('.dynamic-item-card').length;
        }

        $(document).on('click', '.remove-detail-btn', function() {
            if ($('.dynamic-item-card').length <= 1) {
                return;
            }

            $(this).closest('.dynamic-item-card').remove();

            reindexDetails();
        });

        const vehicleConditions = @json($vehicleConditions ?? []);

        function renderDetailForm(data = null) {
            const i = detailIndex;

            const checkList = [
                { key: 'condition_design', label: 'Kondisi Design' },
                { key: 'condition_sealing', label: 'Kondisi Sealing' },
                { key: 'condition_color', label: 'Kondisi Warna' }
            ];

            const no_pol = data?.no_pol || '';
            const vehicle_cond = data?.vehicle_condition || '';
            const pbb_op = data?.pbb_op || '';
            const packaging_type = data?.packaging_type || '';
            const supplier = data?.supplier || '';
            const lot_batch = data?.lot_batch || '';
            const dimension = data?.condition_dimension || '';
            const qty_goods = data?.quantity_goods || 0;
            const qty_sample = data?.quantity_sample || 0;
            const qty_reject = data?.quantity_reject || 0;
            const notes = data?.notes || '';
            const accept_val = data?.acceptance_status || 'OK';

            let checksHtml = '';

            checkList.forEach(item => {
                const val = data?.[item.key] || '';

                checksHtml += `
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label d-block">${item.label}</label>

                        <input
                            type="hidden"
                            name="items[${i}][${item.key}]"
                            id="${item.key}_${i}"
                            value="${val}"
                        >

                        <div class="btn-group btn-check-group w-100" role="group">
                            <button
                                type="button"
                                class="btn ${val === 'OK' ? 'btn-success' : 'btn-outline-secondary'} w-50"
                                data-value="OK"
                                data-target-input="#${item.key}_${i}"
                            >
                                <i class="bi bi-check-lg"></i> OK
                            </button>

                            <button
                                type="button"
                                class="btn ${val === 'Not OK' ? 'btn-danger' : 'btn-outline-secondary'} w-50"
                                data-value="Not OK"
                                data-target-input="#${item.key}_${i}"
                            >
                                <i class="bi bi-x-lg"></i> Not OK
                            </button>
                        </div>
                    </div>
                `;
            });

            const newDetail = document.createElement('div');

            newDetail.classList.add(
                'dynamic-item-card',
                'border',
                'p-3',
                'mb-3',
                'rounded',
                'shadow-sm'
            );

            newDetail.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Item #${i + 1}</h5>

                    <button
                        type="button"
                        class="btn btn-danger btn-sm remove-detail-btn"
                    >
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis Packaging</label>

                        <input
                            type="text"
                            name="items[${i}][packaging_type]"
                            class="form-control"
                            value="${packaging_type}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>

                        <select
                            name="items[${i}][supplier]"
                            class="form-select select2-dynamic"
                            required
                        >
                            <option value="">-- Pilih Supplier --</option>

                            @foreach($suppliers as $s)
                                <option
                                    value="{{ $s->nama_supplier }}"
                                    ${supplier === @json($s->nama_supplier) ? 'selected' : ''}
                                >
                                    {{ $s->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Lot Batch</label>

                        <input
                            type="text"
                            name="items[${i}][lot_batch]"
                            class="form-control"
                            value="${lot_batch}"
                            required
                        >
                    </div>

                    ${checksHtml}

                    <div class="col-lg-1 col-md-6">
                        <label class="form-label">Dimensi</label>

                        <input
                            type="text"
                            name="items[${i}][condition_dimension]"
                            class="form-control"
                            value="${dimension}"
                        >
                    </div>

                    <div class="col-lg-1 col-md-6">
                        <label class="form-label">Berat</label>

                        <input
                            type="number"
                            name="items[${i}][condition_weight]"
                            class="form-control"
                            value="${data?.condition_weight || ''}"
                            min="0"
                            step="0.01"
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Qty Barang</label>

                        <input
                            type="number"
                            name="items[${i}][quantity_goods]"
                            class="form-control"
                            value="${qty_goods}"
                            min="0"
                            required
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Qty Sampel</label>

                        <input
                            type="number"
                            name="items[${i}][quantity_sample]"
                            class="form-control"
                            value="${qty_sample}"
                            min="0"
                            required
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Qty Reject</label>

                        <input
                            type="number"
                            name="items[${i}][quantity_reject]"
                            class="form-control"
                            value="${qty_reject}"
                            min="0"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Penerimaan</label>

                        <select
                            name="items[${i}][acceptance_status]"
                            class="form-select select2-dynamic"
                            required
                        >
                            <option value="OK" ${accept_val === 'OK' ? 'selected' : ''}>
                                OK
                            </option>

                            <option value="Tolak" ${accept_val === 'Tolak' ? 'selected' : ''}>
                                Tolak
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">No. Polisi</label>

                        <input
                            type="text"
                            name="items[${i}][no_pol]"
                            class="form-control"
                            value="${no_pol}"
                            required
                        >
                    </div>

                    <div class="col-md-5">
                        <label class="form-label d-block">
                            Kondisi Kendaraan
                        </label>

                        <div class="form-check">
                            ${vehicleConditions.map(c => {
                                const isChecked = vehicle_cond?.split(',').includes(c)
                                    ? 'checked'
                                    : '';

                                return `
                                    <div class="form-check form-check-inline">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="items[${i}][vehicle_condition][]"
                                            value="${c}"
                                            id="vehicle_${i}_${c}"
                                            ${isChecked}
                                        >

                                        <label
                                            class="form-check-label"
                                            for="vehicle_${i}_${c}"
                                        >
                                            ${c}
                                        </label>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">PBB / OP</label>

                        <input
                            type="text"
                            name="items[${i}][pbb_op]"
                            class="form-control"
                            value="${pbb_op}"
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            Keterangan (Optional)
                        </label>

                        <textarea
                            name="items[${i}][notes]"
                            class="form-control"
                            rows="2"
                        >${notes}</textarea>
                    </div>

                    <input
                        type="hidden"
                        name="items[${i}][id]"
                        value="${data?.id || ''}"
                    >

                    <input
                        type="hidden"
                        name="items[${i}][condition_weight_pcs]"
                        value="${data?.condition_weight_pcs || ''}"
                    >
                </div>
            `;

            container.appendChild(newDetail);

            $(newDetail).find('.select2-dynamic').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih...",
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: false
            });

            detailIndex++;
        }

        if (addBtn) {
            addBtn.addEventListener('click', function() {
                renderDetailForm(null);
            });
        }

        const oldItems = @json(old('items', []));

        if (oldItems.length > 0) {
            oldItems.forEach(itemData => {
                renderDetailForm(itemData);
            });
        } else {
            renderDetailForm(null);
        }
    });
</script>
@endpush
