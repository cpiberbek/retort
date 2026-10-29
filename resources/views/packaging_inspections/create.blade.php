@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    .form-label { font-weight: 600; color: #495057; margin-bottom: .35rem; }
    .form-control, .form-select { border-radius: 8px; min-height: 44px; }

    .form-control[readonly], .form-select:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
        border-color: #dee2e6;
    }

    .select2-container { width: 100% !important; }
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
        min-height: 44px;
        display: flex;
        align-items: center;
    }

    .dynamic-item-card {
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
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

    .section-title:first-child { margin-top: 0; }

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

    .err-msg {
        display: none;
        color: #dc3545;
        font-size: .8rem;
        margin-top: .3rem;
    }

    .field-error .err-msg { display: block; }

    .field-error .form-control,
    .field-error .form-select,
    .field-error .select2-selection {
        border-color: #dc3545 !important;
    }

    .field-error .btn-check-group {
        box-shadow: 0 0 0 2px #dc3545;
        border-radius: 8px;
    }

    [data-field] > .form-label {
        display: block;
        width: 100%;
    }

    [data-field] > .form-select,
    [data-field] > .form-control {
        display: block;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-3 p-md-4 p-lg-5">

            <h4 class="mb-1">
                <i class="bi bi-box-seam"></i>
                Tambah Pemeriksaan Packaging
            </h4>

            <p class="text-muted mb-4">
                Isi detail formulir pemeriksaan packaging di bawah ini.
                Kolom bertanda <span class="text-danger fw-bold">*</span> wajib diisi.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ada masalah input:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('packaging-inspections.store') }}" method="POST">
                @csrf

                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <strong>
                            <i class="bi bi-info-circle-fill"></i>
                            Informasi Inspeksi
                        </strong>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="inspection_date" class="form-label">
                                    Hari/Tanggal <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="inspection_date"
                                    name="inspection_date"
                                    value="{{ old('inspection_date', date('Y-m-d')) }}"
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label for="shift" class="form-label">
                                    Shift <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="shift"
                                    name="shift"
                                    value="{{ old('shift') }}"
                                    required
                                >
                            </div>

                            <input
                                type="hidden"
                                id="qc_inspector"
                                value="{{ auth()->user()->name }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>
                                <i class="bi bi-list-nested"></i>
                                Detail Item Packaging
                            </strong>

                            <button type="button" id="add-detail-btn" class="btn btn-secondary">
                                <i class="bi bi-plus-lg"></i>
                                Tambah Item
                            </button>
                        </div>
                    </div>

                    <div class="card-body bg-light">
                        <div id="details-container"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-save"></i>
                        Simpan Inspeksi
                    </button>

                    <a href="{{ route('packaging-inspections.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left"></i>
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
$(function () {
    const container = document.getElementById('details-container');
    const addBtn = document.getElementById('add-detail-btn');
    let detailIndex = 0;

    const vehicleConditions = @json($vehicleConditions ?? []);
    const suppliers = @json($suppliers->pluck('nama_supplier')->values());
    const oldItems = @json(old('items', []));

    const esc = value => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    const toArray = value => Array.isArray(value)
        ? value
        : (value ? String(value).split(',').map(s => s.trim()).filter(Boolean) : []);

    const field = (col, label, required, inner, errMsg = 'Wajib diisi') => `
        <div class="${col}" data-field>
            <label class="form-label">${label}${required ? ' <span class="text-danger">*</span>' : ''}</label>
            ${inner}
            <div class="err-msg">${errMsg}</div>
        </div>`;

    const textInput = (name, type, value, required, extra = '') => `
        <input
            type="${type}"
            name="${name}"
            class="form-control"
            value="${esc(value)}"
            ${required ? 'data-req required' : ''}
            ${extra}
        >`;

    function renumber() {
        $('.item-number').each((index, element) => {
            element.textContent = index + 1;
        });

        detailIndex = $('.dynamic-item-card').length;
    }

    function renderDetailForm(data = null) {
        const i = detailIndex;

        const v = key => data?.[key] ?? '';
        const acceptVal = v('acceptance_status') || 'OK';
        const selectedConditions = toArray(v('vehicle_condition'));

        const checkList = [
            { key: 'condition_design', label: 'Kondisi Design' },
            { key: 'condition_sealing', label: 'Kondisi Sealing' },
            { key: 'condition_color', label: 'Kondisi Warna' }
        ];

        const checksHtml = checkList.map(item => {
            const value = v(item.key);

            return field('col-md-4', item.label, true, `
                <input
                    type="hidden"
                    name="items[${i}][${item.key}]"
                    id="${item.key}_${i}"
                    value="${esc(value)}"
                    data-req
                >

                <div class="btn-group btn-check-group w-100" role="group">
                    <button
                        type="button"
                        data-value="OK"
                        data-target-input="#${item.key}_${i}"
                        class="btn ${value === 'OK' ? 'btn-success' : 'btn-outline-secondary'} w-50"
                    >
                        <i class="bi bi-check-lg"></i> OK
                    </button>

                    <button
                        type="button"
                        data-value="Not OK"
                        data-target-input="#${item.key}_${i}"
                        class="btn ${value === 'Not OK' ? 'btn-danger' : 'btn-outline-secondary'} w-50"
                    >
                        <i class="bi bi-x-lg"></i> Not OK
                    </button>
                </div>
            `, 'Pilih OK / Not OK');
        }).join('');

        const supplierOptions = suppliers.map(supplier => `
            <option value="${esc(supplier)}" ${v('supplier') === supplier ? 'selected' : ''}>
                ${esc(supplier)}
            </option>
        `).join('');

        const vehicleCheckboxes = vehicleConditions.map((condition, index) => `
            <div class="form-check form-check-inline">
                <input
                    class="form-check-input"
                    type="checkbox"
                    data-vc
                    name="items[${i}][vehicle_condition][]"
                    value="${esc(condition)}"
                    id="vehicle_${i}_${index}"
                    ${selectedConditions.includes(condition) ? 'checked' : ''}
                >
                <label class="form-check-label" for="vehicle_${i}_${index}">
                    ${esc(condition)}
                </label>
            </div>
        `).join('');

        const card = document.createElement('div');
        card.className = 'dynamic-item-card border p-3 p-md-4 mb-4';

        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-center item-header">
                <h5 class="mb-0">Item #<span class="item-number">${i + 1}</span></h5>

                <button type="button" class="btn btn-danger remove-detail-btn">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>

            <div class="row g-3">
                <div class="col-12 section-title">Data Packaging</div>

                ${field('col-md-6', 'Jenis Packaging', true, `
                    <select
                        name="items[${i}][packaging_type]"
                        class="form-select packaging-select"
                        data-req
                        required
                    >
                        <option value="">-- Pilih Jenis Packaging --</option>
                        @foreach (\App\Models\MasterPackaging::where('plant_uuid', auth()->user()->plant_active)->orderBy('nama_packaging')->get() as $packaging)
                            <option
                                value="{{ $packaging->nama_packaging }}"
                                ${v('packaging_type') === @json($packaging->nama_packaging) ? 'selected' : ''}
                            >
                                {{ $packaging->nama_packaging }}
                            </option>
                        @endforeach
                    </select>
                `, 'Jenis packaging wajib dipilih')}

                ${field('col-md-6', 'Supplier', true, `
                    <select
                        name="items[${i}][supplier]"
                        class="form-select supplier-select"
                        data-req
                        required
                    >
                        <option value="">-- Pilih Supplier --</option>
                        ${supplierOptions}
                    </select>
                `, 'Supplier wajib dipilih')}

                ${field('col-md-6', 'Lot Batch', true,
                    textInput(`items[${i}][lot_batch]`, 'text', v('lot_batch'), true))}

                ${field('col-md-6', 'DO/PO/OP', false,
                    textInput(`items[${i}][pbb_op]`, 'text', v('pbb_op'), false))}

                <div class="col-12 section-title">Kondisi Packaging</div>

                ${checksHtml}

                ${field('col-md-6', 'Dimensi', false,
                    textInput(`items[${i}][condition_dimension]`, 'text', v('condition_dimension'), false))}

                ${field('col-md-6', 'Berat', false,
                    textInput(`items[${i}][condition_weight]`, 'number', v('condition_weight'), false,
                        'min="0" step="0.01" inputmode="decimal"'))}

                <div class="col-12 section-title">Jumlah</div>

                ${field('col-4', 'Qty Barang', true,
                    textInput(`items[${i}][quantity_goods]`, 'number', v('quantity_goods') === '' ? 0 : v('quantity_goods'), true,
                        'min="0" step="1" inputmode="numeric"'))}

                ${field('col-4', 'Qty Sampel', true,
                    textInput(`items[${i}][quantity_sample]`, 'number', v('quantity_sample') === '' ? 0 : v('quantity_sample'), true,
                        'min="0" step="1" inputmode="numeric"'))}

                ${field('col-4', 'Qty Reject', true,
                    textInput(`items[${i}][quantity_reject]`, 'number', v('quantity_reject') === '' ? 0 : v('quantity_reject'), true,
                        'min="0" step="1" inputmode="numeric"'))}

                <div class="col-12 section-title">Kendaraan &amp; Penerimaan</div>

                ${field('col-md-6', 'No. Polisi', true,
                    textInput(`items[${i}][no_pol]`, 'text', v('no_pol'), true))}

                ${field('col-md-6', 'Penerimaan', true, `
                    <select
                        name="items[${i}][acceptance_status]"
                        class="form-select acceptance-select"
                        data-req
                        required
                    >
                        <option value="OK" ${acceptVal === 'OK' ? 'selected' : ''}>OK</option>
                        <option value="Tolak" ${acceptVal === 'Tolak' ? 'selected' : ''}>Tolak</option>
                    </select>
                `)}

                ${field('col-12', 'Kondisi Kendaraan', true, `
                    <div class="vehicle-group">${vehicleCheckboxes}</div>
                `, 'Pilih minimal 1 kondisi kendaraan')}

                <div class="col-12" data-field>
                    <label class="form-label">Keterangan (Optional)</label>
                    <textarea
                        name="items[${i}][notes]"
                        class="form-control"
                        rows="2"
                    >${esc(v('notes'))}</textarea>
                </div>

                <input type="hidden" name="items[${i}][id]" value="${esc(v('id'))}">
                <input type="hidden" name="items[${i}][condition_weight_pcs]" value="${esc(v('condition_weight_pcs'))}">
            </div>
        `;

        container.appendChild(card);

        $(card).find('.supplier-select').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Supplier --',
            allowClear: true,
            width: '100%'
        });

        $(card).find('.packaging-select').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Jenis Packaging --',
            allowClear: true,
            width: '100%'
        });

        detailIndex++;
    }

    $(document).on('click', '.btn-check-group .btn', function (event) {
        event.preventDefault();

        const button = $(this);
        const value = button.data('value');
        const target = button.data('target-input');

        $(target).val(value);

        const group = button.closest('.btn-check-group');
        group.find('.btn')
            .removeClass('btn-success btn-danger')
            .addClass('btn-outline-secondary');

        button
            .removeClass('btn-outline-secondary')
            .addClass(value === 'OK' ? 'btn-success' : 'btn-danger');

        button.closest('[data-field]').removeClass('field-error');
    });

    $(document).on('click', '.remove-detail-btn', function () {
        if ($('.dynamic-item-card').length <= 1) return;

        $(this).closest('.dynamic-item-card').remove();
        renumber();
    });

    $(document).on('change input', '[data-field] input, [data-field] select, [data-field] textarea', function () {
        const wrapper = $(this).closest('[data-field]');

        if (!wrapper.length) return;

        if ($(this).is('[data-req]') && !String(this.value).trim()) return;

        if ($(this).is('[data-vc]') && !wrapper.find('[data-vc]:checked').length) return;

        wrapper.removeClass('field-error');
        $(this).removeClass('is-invalid');
    });

    function validateForm() {
        $('.field-error').removeClass('field-error');
        $('.is-invalid').removeClass('is-invalid');

        const mark = element => {
            $(element).addClass('is-invalid')
                .closest('[data-field]')
                .addClass('field-error');
        };

        $('input[required], select[required], [data-req]').each(function () {
            if (this.disabled) return;

            if (!String(this.value).trim()) {
                mark(this);
            }
        });

        $('input[type="number"]').each(function () {
            if (this.value !== '' && Number(this.value) < 0) {
                mark(this);
            }
        });

        $('.vehicle-group').each(function () {
            const wrapper = $(this).closest('[data-field]');
            const checked = wrapper.find('[data-vc]:checked').length > 0;

            if (!checked) {
                wrapper.addClass('field-error');
            }
        });

        if (!$('input[name="inspection_date"]').val()) {
            $('input[name="inspection_date"]').closest('.col-md-6').addClass('field-error');
        }

        if (!$('input[name="shift"]').val()?.trim()) {
            $('input[name="shift"]').closest('.col-md-6').addClass('field-error');
        }

        const errors = $('.field-error');

        if (errors.length) {
            let errorBox = $('#client-errors');

            if (!errorBox.length) {
                errorBox = $('<div id="client-errors" class="alert alert-danger"></div>');
                $('form[action="{{ route('packaging-inspections.store') }}"]').prepend(errorBox);
            }

            errorBox
                .removeClass('d-none')
                .html(`<strong>Masih ada ${errors.length} kolom yang perlu diperiksa.</strong> Silakan periksa kolom bertanda merah.`);

            errors.first()[0].scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            return false;
        }

        $('#client-errors').addClass('d-none').empty();

        return true;
    }

    $('form[action="{{ route('packaging-inspections.store') }}"]').on('submit', function (event) {
        if (!validateForm()) {
            event.preventDefault();
        }
    });

    addBtn.addEventListener('click', () => renderDetailForm(null));

    if (oldItems.length > 0) {
        oldItems.forEach(item => renderDetailForm(item));
    } else {
        renderDetailForm(null);
    }
});
</script>
@endpush