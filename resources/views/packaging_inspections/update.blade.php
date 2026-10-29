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

    /* Select2 */
    .select2-container { width: 100% !important; }
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
        min-height: 44px;
        display: flex;
        align-items: center;
    }

    /* Item card */
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

    /* Toggle OK / Not OK */
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

    /* Chips kondisi kendaraan */
    .chip-group { display: flex; flex-wrap: wrap; gap: .5rem; }
    .chip-group .btn {
        min-height: 44px;
        border-radius: 999px;
        padding: .4rem 1.1rem;
    }
    .chip-group .btn-check:disabled + .btn { opacity: .75; cursor: not-allowed; }

    /* Error state */
    .err-msg { display: none; color: #dc3545; font-size: .8rem; margin-top: .3rem; }
    .field-error .err-msg { display: block; }
    .field-error .form-control,
    .field-error .form-select,
    .field-error .select2-selection { border-color: #dc3545 !important; }
    .field-error .btn-check-group { box-shadow: 0 0 0 2px #dc3545; border-radius: 8px; }
    .field-error .chip-group { padding: .4rem; border: 1px solid #dc3545; border-radius: 12px; }

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
                <i class="bi bi-clipboard-check"></i>
                Lengkapi Pemeriksaan Packaging #{{ $packagingInspection->id }}
            </h4>

            <p class="text-muted mb-4">
                Silakan lengkapi data yang masih kosong. Data yang sudah terisi tidak dapat diubah.
                Kolom bertanda <span class="text-danger fw-bold">*</span> wajib diisi.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Ada masalah input:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div id="client-errors" class="alert alert-danger d-none" role="alert"></div>

            <form
                id="inspection-form"
                action="{{ route('packaging-inspections.update', $packagingInspection->uuid) }}"
                method="POST"
                novalidate
            >
                @csrf
                @method('PUT')

                {{-- INFORMASI INSPEKSI --}}
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Inspeksi</strong>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6" data-field>
                                <label class="form-label">Hari/Tanggal <span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    class="form-control"
                                    name="inspection_date"
                                    data-req
                                    value="{{ old('inspection_date', $packagingInspection->inspection_date ? date('Y-m-d', strtotime($packagingInspection->inspection_date)) : '') }}"
                                    {{ $packagingInspection->inspection_date ? 'readonly' : '' }}
                                >
                                <div class="err-msg">Tanggal wajib diisi</div>
                            </div>

                            <div class="col-md-6" data-field>
                                <label class="form-label">Shift <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="shift"
                                    data-req
                                    value="{{ old('shift', $packagingInspection->shift) }}"
                                    {{ $packagingInspection->shift ? 'readonly' : '' }}
                                >
                                <div class="err-msg">Shift wajib diisi</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DETAIL ITEM --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><i class="bi bi-list-nested"></i> Detail Item Packaging</strong>

                            <button type="button" id="add-detail-btn" class="btn btn-secondary">
                                <i class="bi bi-plus-lg"></i> Tambah Item
                            </button>
                        </div>
                    </div>

                    <div class="card-body bg-light">
                        <div id="details-container"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" id="submit-btn" class="btn btn-success btn-lg">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>

                    <a href="{{ route('packaging-inspections.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left"></i> Kembali
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
    const form = document.getElementById('inspection-form');

    const suppliers = @json($suppliers->pluck('nama_supplier')->values());
    const vehicleConditions = @json($vehicleConditions ?? []);
    const existingDetails = @json(old('items', $packagingInspection->items ?? []));

    // Data asli dari DB -> dipakai untuk menentukan field mana yang terkunci
    const originalMap = {};
    (@json($packagingInspection->items ?? [])).forEach(it => { originalMap[it.id] = it; });

    let detailIndex = 0;

    /* ---------- Helpers ---------- */
    const esc = v => String(v ?? '')
        .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
        .replace(/</g, '&lt;').replace(/>/g, '&gt;');

    const isLocked = v => v !== null && v !== undefined && v !== '';

    const toArray = v => Array.isArray(v)
        ? v
        : (v ? String(v).split(',').map(s => s.trim()).filter(Boolean) : []);

    const roAttr = locked => locked ? 'readonly' : '';

    // Pembungkus field: label + * + pesan error
    const field = (col, label, required, inner, errMsg = 'Wajib diisi') => `
        <div class="${col}" data-field>
            <label class="form-label">${label}${required ? ' <span class="text-danger">*</span>' : ''}</label>
            ${inner}
            <div class="err-msg">${errMsg}</div>
        </div>`;

    const textInput = (name, type, value, locked, required, extra = '') => `
        <input type="${type}" name="${name}" class="form-control"
               value="${esc(value)}" ${roAttr(locked)} ${required ? 'data-req' : ''} ${extra}>`;

    /* ---------- Render satu item ---------- */
    function renderDetailForm(data = null) {
        const i = detailIndex;
        const orig = data?.id ? (originalMap[data.id] || null) : null;

        // nilai tampil (bisa dari old()) ; kunci ditentukan dari data DB
        const v = k => data?.[k] ?? '';
        const lock = k => isLocked(orig?.[k]);

        const lockSupplier = lock('supplier');
        const lockAccept = lock('acceptance_status');
        const acceptVal = data?.acceptance_status || 'OK';

        /* Kondisi Design / Sealing / Color */
        const checkList = [
            { key: 'condition_design',  label: 'Kondisi Design' },
            { key: 'condition_sealing', label: 'Kondisi Sealing' },
            { key: 'condition_color',   label: 'Kondisi Warna' },
        ];

        const checksHtml = checkList.map(item => {
            const val = v(item.key);
            const locked = lock(item.key);
            return field('col-md-4', item.label, true, `
                <input type="hidden" name="items[${i}][${item.key}]" id="${item.key}_${i}"
                       value="${esc(val)}" data-req>
                <div class="btn-group btn-check-group w-100" role="group">
                    <button type="button" data-value="OK" data-target-input="#${item.key}_${i}"
                        class="btn ${val === 'OK' ? 'btn-success' : 'btn-outline-secondary'} w-50 ${locked ? 'disabled' : ''}">
                        <i class="bi bi-check-lg"></i> OK
                    </button>
                    <button type="button" data-value="Not OK" data-target-input="#${item.key}_${i}"
                        class="btn ${val === 'Not OK' ? 'btn-danger' : 'btn-outline-secondary'} w-50 ${locked ? 'disabled' : ''}">
                        <i class="bi bi-x-lg"></i> Not OK
                    </button>
                </div>`, 'Pilih OK / Not OK');
        }).join('');

        /* Supplier */
        const supplierOptions = suppliers.map(s =>
            `<option value="${esc(s)}" ${v('supplier') === s ? 'selected' : ''}>${esc(s)}</option>`
        ).join('');

        /* Kondisi kendaraan (chip) */
        const lockVehicle = toArray(orig?.vehicle_condition).length > 0;
        const vcSelected = lockVehicle ? toArray(orig.vehicle_condition) : toArray(data?.vehicle_condition);

        const chipsHtml = vehicleConditions.map((c, idx) => `
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" data-vc
                    name="items[${i}][vehicle_condition][]" value="${esc(c)}"
                    id="vehicle_${i}_${idx}"
                    ${vcSelected.includes(c) ? 'checked' : ''} ${lockVehicle ? 'disabled' : ''}>
                <label class="form-check-label" for="vehicle_${i}_${idx}">${esc(c)}</label>
            </div>
        `).join('');

        // Jika terkunci: checkbox disabled tidak terkirim -> kirim via hidden sebagai array
        const vcHidden = lockVehicle
            ? vcSelected.map(c => `<input type="hidden" data-vc name="items[${i}][vehicle_condition][]" value="${esc(c)}">`).join('')
            : '';

        const deleteOrBadge = !data?.id
            ? `<button type="button" class="btn btn-danger remove-detail-btn">
                   <i class="bi bi-trash"></i> Hapus
               </button>`
            : `<span class="badge bg-light text-dark border fs-6 fw-normal">Tersimpan</span>`;

        const card = document.createElement('div');
        card.className = 'dynamic-item-card border p-3 p-md-4 mb-4';

        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-center item-header">
                <h5 class="mb-0">Item #<span class="item-number"></span></h5>
                ${deleteOrBadge}
            </div>

            <div class="row g-3">

                <div class="col-12 section-title">Data Packaging</div>

                ${field('col-md-6', 'Jenis Packaging', true,
                    textInput(`items[${i}][packaging_type]`, 'text', v('packaging_type'), lock('packaging_type'), true))}

                ${field('col-md-6', 'Supplier', true, `
                    <select name="items[${i}][supplier]" class="form-control supplier-select"
                            ${lockSupplier ? 'disabled' : ''} data-req>
                        <option value="">-- Pilih Supplier --</option>
                        ${supplierOptions}
                    </select>
                    ${lockSupplier ? `<input type="hidden" name="items[${i}][supplier]" value="${esc(v('supplier'))}">` : ''}
                `, 'Supplier wajib dipilih')}

                ${field('col-md-6', 'Lot Batch', true,
                    textInput(`items[${i}][lot_batch]`, 'text', v('lot_batch'), lock('lot_batch'), true))}

                ${field('col-md-6', 'DO/PO/OP', false,
                    textInput(`items[${i}][pbb_op]`, 'text', v('pbb_op'), lock('pbb_op'), false))}

                <div class="col-12 section-title">Kondisi Packaging</div>

                ${checksHtml}

                ${field('col-md-6', 'Dimensi', false,
                    textInput(`items[${i}][condition_dimension]`, 'text', v('condition_dimension'), lock('condition_dimension'), false))}

                ${field('col-md-6', 'Berat', false,
                    textInput(`items[${i}][condition_weight]`, 'number', v('condition_weight'), lock('condition_weight'), false,
                        'min="0" step="0.01" inputmode="decimal"'))}

                <div class="col-12 section-title">Jumlah</div>

                ${field('col-4', 'Qty Barang', true,
                    textInput(`items[${i}][quantity_goods]`, 'number', v('quantity_goods'), lock('quantity_goods'), true,
                        'min="0" step="1" inputmode="numeric"'))}

                ${field('col-4', 'Qty Sampel', true,
                    textInput(`items[${i}][quantity_sample]`, 'number', v('quantity_sample'), lock('quantity_sample'), true,
                        'min="0" step="1" inputmode="numeric"'))}

                ${field('col-4', 'Qty Reject', true,
                    textInput(`items[${i}][quantity_reject]`, 'number', v('quantity_reject'), lock('quantity_reject'), true,
                        'min="0" step="1" inputmode="numeric"'))}

                <div class="col-12 section-title">Kendaraan &amp; Penerimaan</div>

                ${field('col-md-6', 'No. Polisi', true,
                    textInput(`items[${i}][no_pol]`, 'text', v('no_pol'), lock('no_pol'), true))}

                ${field('col-md-6', 'Penerimaan', true, `
                    <select name="items[${i}][acceptance_status]" class="form-select acceptance-select"
                            ${lockAccept ? 'disabled' : ''} data-req>
                        <option value="OK" ${acceptVal === 'OK' ? 'selected' : ''}>OK</option>
                        <option value="Tolak" ${acceptVal === 'Tolak' ? 'selected' : ''}>Tolak</option>
                    </select>
                    ${lockAccept ? `<input type="hidden" name="items[${i}][acceptance_status]" value="${esc(acceptVal)}">` : ''}
                `)}

                ${field('col-12', 'Kondisi Kendaraan', true, `
                    <div class="chip-group vehicle-group">${chipsHtml}</div>
                    ${vcHidden}
                `, 'Pilih minimal 1 kondisi kendaraan')}

                <div class="col-12">
                    <label class="form-label">Keterangan (Optional)</label>
                    <textarea name="items[${i}][notes]" class="form-control" rows="2"
                        ${roAttr(lock('notes'))}>${esc(v('notes'))}</textarea>
                </div>

                <input type="hidden" name="items[${i}][id]" value="${esc(data?.id || '')}">
                <input type="hidden" name="items[${i}][condition_weight_pcs]" value="${esc(data?.condition_weight_pcs || '')}">
            </div>
        `;

        container.appendChild(card);

        $(card).find('.supplier-select').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Supplier --',
            allowClear: !lockSupplier,
            width: '100%'
        });

        detailIndex++;
        renumber();
    }

    function renumber() {
        $('.item-number').each((k, el) => { el.textContent = k + 1; });
    }

    /* ---------- Event: toggle OK / Not OK ---------- */
    $(document).on('click', '.btn-check-group .btn:not(.disabled)', function (e) {
        e.preventDefault();
        const button = $(this);
        const value = button.data('value');
        $(button.data('target-input')).val(value);

        const group = button.closest('.btn-check-group');
        group.find('.btn').removeClass('btn-success btn-danger').addClass('btn-outline-secondary');
        button.removeClass('btn-outline-secondary').addClass(value === 'OK' ? 'btn-success' : 'btn-danger');

        button.closest('[data-field]').removeClass('field-error');
    });

    /* ---------- Event: hapus item ---------- */
    $(document).on('click', '.remove-detail-btn', function () {
        $(this).closest('.dynamic-item-card').remove();
        renumber();
    });

    /* ---------- Hilangkan error saat user mengisi ---------- */
    $(document).on('input change', '[data-field] input, [data-field] select, [data-field] textarea', function () {
        const $w = $(this).closest('[data-field]');
        if (!$w.length) return;
        if ($(this).is('[data-req]') && !String(this.value).trim()) return;
        if ($(this).is('[data-vc]') && !$w.find('[data-vc]:checked').length) return;
        $w.removeClass('field-error');
        $(this).removeClass('is-invalid');
    });

    /* ---------- Validasi sebelum submit ---------- */
    function validateAll() {
        $('.field-error').removeClass('field-error');
        $('.is-invalid').removeClass('is-invalid');

        const mark = el => {
            $(el).addClass('is-invalid').closest('[data-field]').addClass('field-error');
        };

        // 1. Semua field bertanda data-req (text, number, select, hidden toggle)
        $(form).find('[data-req]').each(function () {
            if (this.disabled) return;
            if (!String(this.value).trim()) mark(this);
        });

        // 2. Angka tidak boleh negatif
        $(form).find('input[type="number"]').each(function () {
            if (this.value !== '' && Number(this.value) < 0) mark(this);
        });

        // 3. Kondisi kendaraan: minimal 1 per item
        $('.vehicle-group').each(function () {
            const $w = $(this).closest('[data-field]');
            const filled = $w.find('[data-vc]').filter(function () {
                return this.type === 'hidden' || (this.checked && !this.disabled);
            }).length > 0;
            if (!filled) $w.addClass('field-error');
        });

        const $errs = $('.field-error');
        const $box = $('#client-errors');

        if ($errs.length) {
            $box.removeClass('d-none')
                .html(`<strong>Masih ada ${$errs.length} kolom wajib yang belum diisi.</strong> Silakan periksa kolom bertanda merah.`);
            $errs.first()[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        $box.addClass('d-none').empty();
        return true;
    }

    form.addEventListener('submit', function (e) {
        if (!container.children.length) {
            e.preventDefault();
            $('#client-errors').removeClass('d-none').text('Minimal harus ada 1 item packaging.');
            return;
        }

        if (!validateAll()) {
            e.preventDefault();
            return;
        }

        $('#submit-btn').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
    });

    /* ---------- Init ---------- */
    addBtn.addEventListener('click', () => renderDetailForm(null));

    if (existingDetails && existingDetails.length > 0) {
        existingDetails.forEach(item => renderDetailForm(item));
    } else {
        renderDetailForm(null);
    }
});
</script>
@endpush