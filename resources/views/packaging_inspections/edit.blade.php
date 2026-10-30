@extends('layouts.app')

@php
    $existingItems = old('items', $packagingInspection->items ?? []);
    $firstItem = collect($existingItems)->first() ?? [];

    $sharedNoPol = old('no_pol', data_get($firstItem, 'no_pol', ''));
    $sharedPbbOp = old('pbb_op', data_get($firstItem, 'pbb_op', ''));

    $sharedVehicleCondition = old(
        'vehicle_condition',
        data_get($firstItem, 'vehicle_condition', [])
    );

    if (is_string($sharedVehicleCondition)) {
        $sharedVehicleCondition = array_filter(
            array_map('trim', explode(',', $sharedVehicleCondition))
        );
    }

    $packagingOptions = \App\Models\MasterPackaging::where(
        'plant_uuid',
        auth()->user()->plant_active
    )->get();
@endphp

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

    .form-control[readonly],
    .form-control:disabled,
    .form-select:disabled {
        background-color: #e9ecef;
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

    .select2-container--bootstrap-5 .select2-selection.is-invalid {
        border-color: #dc3545 !important;
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

    .vehicle-condition-group .form-check-input,
    .vehicle-condition-group .form-check-label {
        cursor: pointer;
    }

    .vehicle-condition-group.is-invalid .form-check {
        border-color: #dc3545;
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

    .invalid-feedback-custom {
        display: none;
        color: #dc3545;
        font-size: .875rem;
        margin-top: .35rem;
    }

    .is-invalid + .invalid-feedback-custom,
    .invalid-feedback-custom.show {
        display: block;
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

            <form
                id="packaging-inspection-form"
                action="{{ route('packaging-inspections.update', $packagingInspection->uuid) }}"
                method="POST"
                novalidate
            >
                @csrf
                @method('PUT')

                {{-- INFORMASI INSPEKSI --}}
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
                                    data-req="Tanggal inspeksi wajib diisi."
                                >

                                <div class="invalid-feedback-custom">
                                    Tanggal inspeksi wajib diisi.
                                </div>
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
                                    data-req="Shift wajib diisi."
                                >

                                <div class="invalid-feedback-custom">
                                    Shift wajib diisi.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INFORMASI TAMBAHAN --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header inspection-section-header">
                        <strong>
                            <i class="bi bi-truck me-1"></i>
                            Informasi Tambahan
                        </strong>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="pbb_op" class="form-label">
                                    DO/PO/OP
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="pbb_op"
                                    name="pbb_op"
                                    value="{{ $sharedPbbOp }}"
                                    placeholder="Masukkan nomor DO/PO/OP"
                                >
                            </div>

                            <div class="col-md-6">
                                <label for="no_pol" class="form-label">
                                    No. Polisi
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="no_pol"
                                    name="no_pol"
                                    value="{{ $sharedNoPol }}"
                                    placeholder="Masukkan nomor polisi"
                                    required
                                    data-req="Nomor polisi wajib diisi."
                                >

                                <div class="invalid-feedback-custom">
                                    Nomor polisi wajib diisi.
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label d-block">
                                    Kondisi Kendaraan
                                </label>

                                <div class="vehicle-condition-group" id="vehicle-condition-group">
                                    @foreach ($vehicleConditions ?? [] as $condition)
                                        <div class="form-check">
                                            <input
                                                class="form-check-input vehicle-condition-input"
                                                type="checkbox"
                                                name="vehicle_condition[]"
                                                value="{{ $condition }}"
                                                id="vehicle_condition_{{ $loop->index }}"
                                                {{ in_array($condition, $sharedVehicleCondition) ? 'checked' : '' }}
                                            >

                                            <label
                                                class="form-check-label"
                                                for="vehicle_condition_{{ $loop->index }}"
                                            >
                                                {{ $condition }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="invalid-feedback-custom" id="vehicle-condition-error">
                                    Pilih minimal satu kondisi kendaraan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DETAIL ITEM PACKAGING --}}
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('packaging-inspection-form');
        const container = document.getElementById('details-container');
        const addBtn = document.getElementById('add-detail-btn');

        let detailIndex = 0;

        const existingDetails = @json($existingItems);
        const vehicleConditions = @json($vehicleConditions ?? []);
        const suppliers = @json(
            collect($suppliers ?? [])->pluck('nama_supplier')->values()
        );
        const packagingOptions = @json(
            $packagingOptions->pluck('nama_packaging')->values()
        );

        const escapeHtml = function(value) {
            return String(value ?? '').replace(/[&<>"']/g, function(character) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[character];
            });
        };

        const normalizeVehicleConditions = function(value) {
            if (Array.isArray(value)) {
                return value;
            }

            if (typeof value === 'string' && value.trim() !== '') {
                return value.split(',').map(item => item.trim()).filter(Boolean);
            }

            return [];
        };

        function initializeSelect2(element) {
            $(element).select2({
                theme: 'bootstrap-5',
                placeholder: element.classList.contains('packaging-select')
                    ? '-- Pilih atau ketik Jenis Packaging --'
                    : '-- Pilih Supplier --',
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: false,
                tags: element.classList.contains('packaging-select'),
                createTag: function(params) {
                    const term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });
        }

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

            const packagingType = data?.packaging_type ?? '';
            const supplier = data?.supplier ?? '';
            const lotBatch = data?.lot_batch ?? '';
            const dimension = data?.condition_dimension ?? '';
            const weight = data?.condition_weight ?? '';
            const qtyGoods = data?.quantity_goods ?? 0;
            const qtySample = data?.quantity_sample ?? 0;
            const qtyReject = data?.quantity_reject ?? 0;
            const notes = data?.notes ?? '';
            const acceptanceStatus = data?.acceptance_status ?? 'OK';

            let checksHtml = '';

            checkList.forEach(item => {
                const value = data?.[item.key] ?? '';

                checksHtml += `
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label d-block">
                            ${item.label}
                        </label>

                        <input
                            type="hidden"
                            name="items[${i}][${item.key}]"
                            id="${item.key}_${i}"
                            value="${escapeHtml(value)}"
                            data-check-required="true"
                        >

                        <div class="btn-group btn-check-group w-100" role="group">
                            <button
                                type="button"
                                class="btn ${value === 'OK' ? 'btn-success' : 'btn-outline-secondary'} w-50"
                                data-value="OK"
                                data-target-input="#${item.key}_${i}"
                            >
                                <i class="bi bi-check-lg"></i>
                                OK
                            </button>

                            <button
                                type="button"
                                class="btn ${value === 'Not OK' ? 'btn-danger' : 'btn-outline-secondary'} w-50"
                                data-value="Not OK"
                                data-target-input="#${item.key}_${i}"
                            >
                                <i class="bi bi-x-lg"></i>
                                Not OK
                            </button>
                        </div>

                        <div class="invalid-feedback-custom">
                            ${item.label} wajib dipilih.
                        </div>
                    </div>
                `;
            });

            const packagingOptionsHtml = packagingOptions.map(option => `
                <option
                    value="${escapeHtml(option)}"
                    ${String(packagingType) === String(option) ? 'selected' : ''}
                >
                    ${escapeHtml(option)}
                </option>
            `).join('');

            const supplierOptionsHtml = suppliers.map(option => `
                <option
                    value="${escapeHtml(option)}"
                    ${String(supplier) === String(option) ? 'selected' : ''}
                >
                    ${escapeHtml(option)}
                </option>
            `).join('');

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

                        <select
                            name="items[${i}][packaging_type]"
                            class="form-select packaging-select"
                            required
                            data-req="Jenis Packaging wajib dipilih atau diisi."
                        >
                            <option value=""></option>
                            ${packagingOptionsHtml}
                        </select>

                        <div class="invalid-feedback-custom">
                            Jenis Packaging wajib dipilih atau diisi.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Supplier
                        </label>

                        <select
                            name="items[${i}][supplier]"
                            class="form-select supplier-select"
                            required
                            data-req="Supplier wajib dipilih."
                        >
                            <option value=""></option>
                            ${supplierOptionsHtml}
                        </select>

                        <div class="invalid-feedback-custom">
                            Supplier wajib dipilih.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Lot Batch
                        </label>

                        <input
                            type="text"
                            name="items[${i}][lot_batch]"
                            class="form-control"
                            value="${escapeHtml(lotBatch)}"
                            required
                            data-req="Lot Batch wajib diisi."
                        >

                        <div class="invalid-feedback-custom">
                            Lot Batch wajib diisi.
                        </div>
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
                            value="${escapeHtml(dimension)}"
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
                            value="${escapeHtml(weight)}"
                            min="0"
                            step="0.01"
                            data-numeric="true"
                        >

                        <div class="invalid-feedback-custom">
                            Berat tidak boleh bernilai negatif.
                        </div>
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
                            value="${escapeHtml(qtyGoods)}"
                            min="0"
                            step="1"
                            required
                            data-req="Qty Barang wajib diisi."
                            data-numeric="true"
                        >

                        <div class="invalid-feedback-custom">
                            Qty Barang wajib diisi dan tidak boleh negatif.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Qty Sampel
                        </label>

                        <input
                            type="number"
                            name="items[${i}][quantity_sample]"
                            class="form-control"
                            value="${escapeHtml(qtySample)}"
                            min="0"
                            step="1"
                            required
                            data-req="Qty Sampel wajib diisi."
                            data-numeric="true"
                        >

                        <div class="invalid-feedback-custom">
                            Qty Sampel wajib diisi dan tidak boleh negatif.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Qty Reject
                        </label>

                        <input
                            type="number"
                            name="items[${i}][quantity_reject]"
                            class="form-control"
                            value="${escapeHtml(qtyReject)}"
                            min="0"
                            step="1"
                            required
                            data-req="Qty Reject wajib diisi."
                            data-numeric="true"
                        >

                        <div class="invalid-feedback-custom">
                            Qty Reject wajib diisi dan tidak boleh negatif.
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="section-title">Penerimaan</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Penerimaan
                        </label>

                        <select
                            name="items[${i}][acceptance_status]"
                            class="form-select acceptance-select"
                            required
                            data-req="Status penerimaan wajib dipilih."
                        >
                            <option value="OK" ${acceptanceStatus === 'OK' ? 'selected' : ''}>
                                OK
                            </option>

                            <option value="Tolak" ${acceptanceStatus === 'Tolak' ? 'selected' : ''}>
                                Tolak
                            </option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">
                            Keterangan (Optional)
                        </label>

                        <textarea
                            name="items[${i}][notes]"
                            class="form-control"
                            rows="2"
                        >${escapeHtml(notes)}</textarea>
                    </div>

                    <input
                        type="hidden"
                        name="items[${i}][id]"
                        value="${escapeHtml(data?.id ?? '')}"
                    >

                    <input
                        type="hidden"
                        name="items[${i}][condition_weight_pcs]"
                        value="${escapeHtml(data?.condition_weight_pcs ?? '')}"
                    >

                    <input
                        type="hidden"
                        name="items[${i}][no_pol]"
                        class="shared-no-pol"
                        value=""
                    >

                    <input
                        type="hidden"
                        name="items[${i}][pbb_op]"
                        class="shared-pbb-op"
                        value=""
                    >

                    <div class="shared-vehicle-condition-inputs"></div>

                </div>
            `;

            container.appendChild(newDetail);

            const packagingSelect = newDetail.querySelector('.packaging-select');

            if (
                packagingType &&
                !Array.from(packagingSelect.options).some(option => option.value === String(packagingType))
            ) {
                packagingSelect.add(new Option(packagingType, packagingType, true, true));
            }

            initializeSelect2(packagingSelect);
            initializeSelect2(newDetail.querySelector('.supplier-select'));
            initializeSelect2(newDetail.querySelector('.acceptance-select'));

            detailIndex++;
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const cards = container.querySelectorAll('.dynamic-item-card');
            const removeButtons = container.querySelectorAll('.remove-detail-btn');

            removeButtons.forEach(button => {
                button.disabled = cards.length <= 1;
            });
        }

        function reindexDetails() {
            container.querySelectorAll('.dynamic-item-card').forEach((card, index) => {
                card.querySelector('h5').textContent = `Item #${index + 1}`;

                card.querySelectorAll('[name]').forEach(element => {
                    const name = element.getAttribute('name');

                    if (name) {
                        element.setAttribute(
                            'name',
                            name.replace(/items\[\d+\]/, `items[${index}]`)
                        );
                    }
                });

                card.querySelectorAll('[id]').forEach(element => {
                    const id = element.getAttribute('id');

                    if (id) {
                        element.setAttribute(
                            'id',
                            id.replace(/_\d+$/, `_${index}`)
                        );
                    }
                });

                card.querySelectorAll('[data-target-input]').forEach(element => {
                    const target = element.getAttribute('data-target-input');

                    if (target) {
                        element.setAttribute(
                            'data-target-input',
                            target.replace(/_\d+$/, `_${index}`)
                        );
                    }
                });

                card.querySelectorAll('label[for]').forEach(element => {
                    const value = element.getAttribute('for');

                    if (value) {
                        element.setAttribute(
                            'for',
                            value.replace(/_\d+_\d+$/, `_${index}_${value.split('_').pop()}`)
                        );
                    }
                });
            });

            detailIndex = container.querySelectorAll('.dynamic-item-card').length;
            updateRemoveButtons();
        }

        function clearValidation(element) {
            element.classList.remove('is-invalid');

            const wrapper = element.closest('.col-12, .col-md-6, .col-md-4, .col-md-8, .col-lg-3, .col-lg-2');

            if (wrapper) {
                wrapper.querySelectorAll('.invalid-feedback-custom').forEach(error => {
                    error.classList.remove('show');
                });
            }

            if ($(element).hasClass('select2-hidden-accessible')) {
                $(element).next('.select2-container')
                    .find('.select2-selection')
                    .removeClass('is-invalid');
            }
        }

        function showValidation(element, message = null) {
            element.classList.add('is-invalid');

            if ($(element).hasClass('select2-hidden-accessible')) {
                $(element).next('.select2-container')
                    .find('.select2-selection')
                    .addClass('is-invalid');
            }

            const wrapper = element.closest('.col-12, .col-md-6, .col-md-4, .col-md-8, .col-lg-3, .col-lg-2');

            if (wrapper) {
                const error = wrapper.querySelector('.invalid-feedback-custom');

                if (error) {
                    if (message) {
                        error.textContent = message;
                    }

                    error.classList.add('show');
                }
            }
        }

        function validateForm() {
            let firstInvalid = null;
            let isValid = true;

            form.querySelectorAll('.is-invalid').forEach(element => {
                clearValidation(element);
            });

            form.querySelectorAll('.invalid-feedback-custom').forEach(error => {
                error.classList.remove('show');
            });

            const inspectionDate = document.getElementById('inspection_date');
            const shift = document.getElementById('shift');
            const noPol = document.getElementById('no_pol');

            const requiredFields = [
                inspectionDate,
                shift,
                noPol
            ];

            requiredFields.forEach(element => {
                if (!element.value.trim()) {
                    isValid = false;
                    showValidation(element);

                    if (!firstInvalid) {
                        firstInvalid = element;
                    }
                }
            });

            const vehicleInputs = document.querySelectorAll('.vehicle-condition-input');
            const selectedVehicleConditions = Array.from(vehicleInputs).filter(input => input.checked);

            const vehicleGroup = document.getElementById('vehicle-condition-group');
            const vehicleError = document.getElementById('vehicle-condition-error');

            if (selectedVehicleConditions.length === 0) {
                isValid = false;
                vehicleGroup.classList.add('is-invalid');
                vehicleError.classList.add('show');

                if (!firstInvalid) {
                    firstInvalid = vehicleGroup;
                }
            }

            const itemCards = container.querySelectorAll('.dynamic-item-card');

            if (itemCards.length === 0) {
                isValid = false;

                if (!firstInvalid) {
                    firstInvalid = addBtn;
                }
            }

            itemCards.forEach(card => {
                card.querySelectorAll('[data-req]').forEach(element => {
                    if (!element.value.trim()) {
                        isValid = false;
                        showValidation(element);

                        if (!firstInvalid) {
                            firstInvalid = element;
                        }
                    }
                });

                card.querySelectorAll('[data-check-required]').forEach(element => {
                    if (!element.value) {
                        isValid = false;
                        showValidation(element);

                        if (!firstInvalid) {
                            firstInvalid = element;
                        }
                    }
                });

                card.querySelectorAll('[data-numeric]').forEach(element => {
                    const value = element.value.trim();

                    if (value === '' && !element.required) {
                        return;
                    }

                    const number = Number(value);
                    const isIntegerField = element.step === '1';

                    if (
                        value === '' ||
                        !Number.isFinite(number) ||
                        number < 0 ||
                        (isIntegerField && !Number.isInteger(number))
                    ) {
                        isValid = false;
                        showValidation(element);

                        if (!firstInvalid) {
                            firstInvalid = element;
                        }
                    }
                });
            });

            if (!isValid && firstInvalid) {
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                if (typeof firstInvalid.focus === 'function') {
                    setTimeout(() => firstInvalid.focus(), 300);
                }
            }

            return isValid;
        }

        function copySharedInformation() {
            const noPol = document.getElementById('no_pol').value.trim();
            const pbbOp = document.getElementById('pbb_op').value.trim();

            const vehicleConditions = Array.from(
                document.querySelectorAll('.vehicle-condition-input:checked')
            ).map(input => input.value);

            container.querySelectorAll('.dynamic-item-card').forEach(card => {
                card.querySelector('[name$="[no_pol]"]').value = noPol;
                card.querySelector('[name$="[pbb_op]"]').value = pbbOp;

                const conditionContainer = card.querySelector('.shared-vehicle-condition-inputs');

                conditionContainer.innerHTML = '';

                vehicleConditions.forEach(condition => {
                    const input = document.createElement('input');

                    input.type = 'hidden';
                    input.name = card.querySelector('[name$="[id]"]').name
                        .replace('[id]', '[vehicle_condition][]');
                    input.value = condition;

                    conditionContainer.appendChild(input);
                });
            });
        }

        $(document).on('click', '.btn-check-group .btn', function(event) {
            event.preventDefault();

            const button = $(this);
            const value = button.data('value');
            const targetInput = $(button.data('target-input'));
            const group = button.closest('.btn-check-group');

            targetInput.val(value);

            group.find('.btn')
                .removeClass('btn-success btn-danger')
                .addClass('btn-outline-secondary');

            if (value === 'OK') {
                button
                    .removeClass('btn-outline-secondary')
                    .addClass('btn-success');
            } else {
                button
                    .removeClass('btn-outline-secondary')
                    .addClass('btn-danger');
            }

            const wrapper = group.closest('.col-lg-2, .col-md-6');
            wrapper.find('.invalid-feedback-custom').removeClass('show');
        });

        $(document).on('change', '.packaging-select, .supplier-select, .acceptance-select', function() {
            clearValidation(this);
        });

        $(document).on('input change', '[data-req], [data-numeric]', function() {
            clearValidation(this);
        });

        $(document).on('change', '.vehicle-condition-input', function() {
            const selected = document.querySelectorAll('.vehicle-condition-input:checked').length > 0;

            if (selected) {
                document.getElementById('vehicle-condition-group').classList.remove('is-invalid');
                document.getElementById('vehicle-condition-error').classList.remove('show');
            }
        });

        $(document).on('click', '.remove-detail-btn', function() {
            const cards = container.querySelectorAll('.dynamic-item-card');

            if (cards.length <= 1) {
                return;
            }

            $(this).closest('.dynamic-item-card').remove();
            reindexDetails();
        });

        addBtn.addEventListener('click', function() {
            renderDetailForm(null);
        });

        form.addEventListener('submit', function(event) {
            if (!validateForm()) {
                event.preventDefault();
                alert('Periksa kembali formulir. Masih terdapat data yang belum lengkap atau tidak valid.');
                return;
            }

            copySharedInformation();
        });

        if (Array.isArray(existingDetails) && existingDetails.length > 0) {
            existingDetails.forEach(itemData => {
                renderDetailForm(itemData);
            });
        } else {
            renderDetailForm(null);
        }

        updateRemoveButtons();
    });
</script>
@endpush