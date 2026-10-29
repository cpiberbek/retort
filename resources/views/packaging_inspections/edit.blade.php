@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: .35rem;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        min-height: 44px;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
        min-height: 44px;
        display: flex;
        align-items: center;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        line-height: normal;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 42px;
    }

    .dynamic-item-card {
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
        border-radius: 12px !important;
    }

    .item-header {
        padding-bottom: .75rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .section-title {
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6c757d;
        padding-bottom: .35rem;
        border-bottom: 1px dashed #dee2e6;
        margin-top: 1.25rem;
    }

    .section-title:first-child {
        margin-top: 0;
    }

    .btn-check-group .btn {
        min-height: 44px;
        white-space: nowrap;
        padding: .4rem .5rem;
        font-weight: 500;
        transition: all .2s;
    }

    .btn-check-group .btn.disabled {
        opacity: .6;
        cursor: not-allowed;
        pointer-events: none;
    }

    .vehicle-condition-group {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .vehicle-condition-group .form-check {
        margin: 0;
        padding: .5rem .75rem .5rem 2rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background-color: #fff;
    }

    .vehicle-condition-group .form-check-input {
        cursor: pointer;
    }

    .vehicle-condition-group .form-check-label {
        cursor: pointer;
    }

    .inspection-section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
        background-color: #fff;
    }

    .inspection-section-header strong {
        color: #343a40;
    }

    .inspection-section-header.bg-primary strong {
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem !important;
        }

        .inspection-section-header {
            padding: .875rem 1rem;
        }

        .inspection-section-header .btn {
            white-space: nowrap;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-3 p-md-4 p-lg-5">

            <div class="mb-4">
                <h4 class="mb-1">
                    <i class="bi bi-pencil-square"></i>
                    Edit Pemeriksaan Packaging #{{ $packagingInspection->id }}
                </h4>

                <p class="text-muted mb-0">
                    Perbarui detail formulir pemeriksaan packaging di bawah ini.
                </p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-semibold mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Terdapat kesalahan pada formulir.
                    </div>

                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('packaging-inspections.update', $packagingInspection->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header inspection-section-header bg-primary text-white">
                        <strong>
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Informasi Inspeksi
                        </strong>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="inspection_date" class="form-label">
                                    Hari/Tanggal
                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    id="inspection_date"
                                    name="inspection_date"
                                    value="{{ old('inspection_date', date('Y-m-d', strtotime($packagingInspection->inspection_date))) }}"
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label for="shift" class="form-label">
                                    Shift
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="shift"
                                    name="shift"
                                    value="{{ old('shift', $packagingInspection->shift) }}"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header inspection-section-header">
                        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                            <strong>
                                <i class="bi bi-list-nested me-1"></i>
                                Detail Item Packaging
                            </strong>

                            <button
                                type="button"
                                id="add-detail-btn"
                                class="btn btn-secondary"
                            >
                                <i class="bi bi-plus-lg me-1"></i>
                                Tambah Item
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-4 bg-light">
                        <div id="details-container"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save me-1"></i>
                        Update Inspeksi
                    </button>

                    <a
                        href="{{ route('packaging-inspections.index') }}"
                        class="btn btn-secondary btn-lg"
                    >
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali
                    </a>
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
                $(this)
                    .removeClass('btn-success btn-danger')
                    .addClass('btn-outline-secondary');
            });

            if (value === 'OK') {
                button
                    .removeClass('btn-outline-secondary')
                    .addClass('btn-success');
            } else {
                button
                    .removeClass('btn-outline-secondary')
                    .addClass('btn-danger');
            }
        });

        $(document).on('click', '.remove-detail-btn', function() {
            $(this).closest('.dynamic-item-card').remove();
            reindexDetails();
        });

    });

    function reindexDetails() {
        $('#details-container .dynamic-item-card').each(function(index) {
            const card = $(this);

            card.find('h5').first().text(`Item #${index + 1}`);

            card.find('[name]').each(function() {
                const element = $(this);
                const name = element.attr('name');

                if (!name) {
                    return;
                }

                element.attr(
                    'name',
                    name.replace(/items\[\d+\]/, `items[${index}]`)
                );
            });

            card.find('[id]').each(function() {
                const element = $(this);
                const id = element.attr('id');

                if (!id) {
                    return;
                }

                element.attr(
                    'id',
                    id.replace(/_\d+$/, `_${index}`)
                );
            });

            card.find('[data-target-input]').each(function() {
                const element = $(this);
                const target = element.attr('data-target-input');

                if (!target) {
                    return;
                }

                element.attr(
                    'data-target-input',
                    target.replace(/_\d+$/, `_${index}`)
                );
            });

            card.find('label[for]').each(function() {
                const element = $(this);
                const value = element.attr('for');

                if (!value) {
                    return;
                }

                element.attr(
                    'for',
                    value.replace(/_\d+_\d+$/, `_${index}_${value.split('_').pop()}`)
                );
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {

        const container = document.getElementById('details-container');
        const addBtn = document.getElementById('add-detail-btn');

        let detailIndex = 0;

        const vehicleConditions = @json($vehicleConditions ?? []);

        function renderDetailForm(data = null) {
            const i = detailIndex;

            const checkList = [
                {
                    key: 'condition_design',
                    label: 'Kondisi Design'
                },
                {
                    key: 'condition_sealing',
                    label: 'Kondisi Sealing'
                },
                {
                    key: 'condition_color',
                    label: 'Kondisi Warna'
                }
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
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label d-block">
                            ${item.label}
                        </label>

                        <input
                            type="hidden"
                            name="items[${i}][${item.key}]"
                            id="${item.key}_${i}"
                            value="${val}"
                            required
                        >

                        <div class="btn-group btn-check-group w-100" role="group">
                            <button
                                type="button"
                                class="btn ${val === 'OK' ? 'btn-success' : 'btn-outline-secondary'} w-50"
                                data-value="OK"
                                data-target-input="#${item.key}_${i}"
                            >
                                <i class="bi bi-check-lg"></i>
                                OK
                            </button>

                            <button
                                type="button"
                                class="btn ${val === 'Not OK' ? 'btn-danger' : 'btn-outline-secondary'} w-50"
                                data-value="Not OK"
                                data-target-input="#${item.key}_${i}"
                            >
                                <i class="bi bi-x-lg"></i>
                                Not OK
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
                'p-md-4',
                'mb-4',
                'rounded'
            );

            newDetail.innerHTML = `
                <div class="item-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0 fw-semibold">
                        Item #${i + 1}
                    </h5>

                    <button
                        type="button"
                        class="btn btn-danger btn-sm remove-detail-btn"
                    >
                        <i class="bi bi-trash me-1"></i>
                        Hapus
                    </button>
                </div>

                <div class="row g-3">

                    <div class="col-12">
                        <div class="section-title">Data Packaging</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Jenis Packaging
                        </label>

                        <input
                            type="text"
                            name="items[${i}][packaging_type]"
                            class="form-control"
                            value="${packaging_type}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Supplier
                        </label>

                        <select
                            name="items[${i}][supplier]"
                            class="form-select supplier-select"
                            required
                        >
                            <option value="">-- Pilih Supplier --</option>

                            @foreach ($suppliers as $supplierItem)
                                <option
                                    value="{{ $supplierItem->nama_supplier }}"
                                    ${supplier === @json($supplierItem->nama_supplier) ? 'selected' : ''}
                                >
                                    {{ $supplierItem->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Lot Batch
                        </label>

                        <input
                            type="text"
                            name="items[${i}][lot_batch]"
                            class="form-control"
                            value="${lot_batch}"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <div class="section-title">Kondisi Packaging</div>
                    </div>

                    ${checksHtml}

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            Dimensi
                        </label>

                        <input
                            type="text"
                            name="items[${i}][condition_dimension]"
                            class="form-control"
                            value="${dimension}"
                        >
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            Berat
                        </label>

                        <input
                            type="number"
                            name="items[${i}][condition_weight]"
                            class="form-control"
                            value="${data?.condition_weight || ''}"
                            min="0"
                            step="0.01"
                        >
                    </div>

                    <div class="col-12">
                        <div class="section-title">Jumlah</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Qty Barang
                        </label>

                        <input
                            type="number"
                            name="items[${i}][quantity_goods]"
                            class="form-control"
                            value="${qty_goods}"
                            min="0"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Qty Sampel
                        </label>

                        <input
                            type="number"
                            name="items[${i}][quantity_sample]"
                            class="form-control"
                            value="${qty_sample}"
                            min="0"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Qty Reject
                        </label>

                        <input
                            type="number"
                            name="items[${i}][quantity_reject]"
                            class="form-control"
                            value="${qty_reject}"
                            min="0"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <div class="section-title">Kendaraan & Penerimaan</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Penerimaan
                        </label>

                        <select
                            name="items[${i}][acceptance_status]"
                            class="form-select acceptance-select"
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
                        <label class="form-label">
                            No. Polisi
                        </label>

                        <input
                            type="text"
                            name="items[${i}][no_pol]"
                            class="form-control"
                            value="${no_pol}"
                            required
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            DO/PO/OP
                        </label>

                        <input
                            type="text"
                            name="items[${i}][pbb_op]"
                            class="form-control"
                            value="${pbb_op}"
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label d-block">
                            Kondisi Kendaraan
                        </label>

                        <div class="vehicle-condition-group">
                            ${vehicleConditions.map((c, index) => {
                                const isChecked = vehicle_cond?.split(',').includes(c)
                                    ? 'checked'
                                    : '';

                                return `
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="items[${i}][vehicle_condition][]"
                                            value="${c}"
                                            id="vehicle_${i}_${index}"
                                            ${isChecked}
                                        >

                                        <label
                                            class="form-check-label"
                                            for="vehicle_${i}_${index}"
                                        >
                                            ${c}
                                        </label>
                                    </div>
                                `;
                            }).join('')}
                        </div>
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

            $(newDetail).find('.supplier-select').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Supplier --',
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: false
            });

            $(newDetail).find('.acceptance-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih...',
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

        const existingDetails = @json(old('items', $packagingInspection->items ?? []));

        if (existingDetails.length > 0) {
            existingDetails.forEach(itemData => {
                renderDetailForm(itemData);
            });
        } else {
            renderDetailForm(null);
        }
    });
</script>
@endpush