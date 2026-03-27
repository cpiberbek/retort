@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    .form-label { font-weight: 600; color: #495057; }
    .form-control, .form-select { border-radius: 8px; }
    
    /* Style Readonly agar terlihat jelas terkunci */
    .form-control[readonly], .form-select:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
        border-color: #dee2e6;
    }

    /* Memastikan Select2 Full Width & Rapi */
    .select2-container { width: 100% !important; } 
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
    }
    .select2-container--bootstrap-5 .select2-selection { border-radius: 8px !important; }
    
    /* Style Tombol Check Group */
    .btn-check-group .btn {
        display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-weight: 500; transition: all 0.2s;
    }
    /* Style tombol Disabled */
    .btn-check-group .btn.disabled {
        opacity: 0.6; cursor: not-allowed; pointer-events: none;
    }

    .dynamic-item-card { background-color: #fdfdfd; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            <h4 class="mb-1"><i class="bi bi-clipboard-check"></i> Lengkapi Pemeriksaan Packaging #{{ $packagingInspection->id }}</h4>
            <p class="text-muted mb-4">Silakan lengkapi data yang masih kosong. Data yang sudah terisi tidak dapat diubah.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Ada masalah input:</strong>
                    <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            @endif
            
            <form action="{{ route('packaging-inspections.update', $packagingInspection->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- CARD INFORMASI UMUM --}}
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Inspeksi</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Hari/Tanggal</label>
                                <input type="date" class="form-control" name="inspection_date" 
                                    value="{{ old('inspection_date', date('Y-m-d', strtotime($packagingInspection->inspection_date))) }}"
                                    {{ $packagingInspection->inspection_date ? 'readonly' : '' }} required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <input type="text" class="form-control" name="shift" 
                                    value="{{ old('shift', $packagingInspection->shift) }}"
                                    {{ $packagingInspection->shift ? 'readonly' : '' }} required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DETAIL ITEM (DINAMIS) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><i class="bi bi-list-nested"></i> Detail Item Packaging</strong>
                            <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Item Baru</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="details-container">
                            {{-- Item dinamis akan ditambahkan di sini oleh JS --}}
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-save"></i> Simpan Perubahan</button>
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
    // --- 1. Logic Button OK/Not OK (Event Delegation) ---
    $(document).ready(function() {
        
        $(document).on('click', '.btn-check-group .btn:not(.disabled)', function(e) {
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

        // Hapus Baris Item (Hanya jika belum tersimpan di DB)
        $(document).on('click', '.remove-detail-btn', function() {
            $(this).closest('.dynamic-item-card').remove();
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        
        const container = document.getElementById('details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0;

        const vehicleConditions = @json($vehicleConditions ?? []);

        // --- FUNGSI HELPER: Cek apakah field terkunci (sudah ada isinya) ---
        function isLocked(value) {
            return value !== null && value !== undefined && value !== '';
        }

        function getReadonlyAttr(value) { return isLocked(value) ? 'readonly' : ''; }

        function renderDetailForm(data = null) {
            const i = detailIndex;
            
            // Ambil data (Default kosong jika null)
            const no_pol = data?.no_pol || '';
            const vehicle_cond = data?.vehicle_condition || '';
            const pbb_op = data?.pbb_op || '';
            const packaging_type = data?.packaging_type || '';
            const supplier = data?.supplier || '';
            const lot_batch = data?.lot_batch || '';
            const dimension = data?.condition_dimension || '';
            const qty_goods = data?.quantity_goods ?? '';
            const qty_sample = data?.quantity_sample ?? '';
            const qty_reject = data?.quantity_reject ?? '';
            const notes = data?.notes || '';
            const accept_val = data?.acceptance_status || 'OK';

            // Logic Lock untuk Select & Button
            const lockVehicle = isLocked(vehicle_cond);
            const lockAccept = isLocked(data?.acceptance_status); 

            // Definisi Field Checkbox Loop
            const checkList = [
                { key: 'condition_design', label: 'Kondisi Design' },
                { key: 'condition_sealing', label: 'Kondisi Sealing' },
                { key: 'condition_color', label: 'Kondisi Warna' }
            ];

            // --- Generate HTML Checkbox Loop ---
            let checksHtml = '';
            checkList.forEach(item => {
                const val = data?.[item.key] || ''; 
                const isItemLocked = isLocked(data?.[item.key]); // Cek per item lock
                
                checksHtml += `
                <div class="col-lg-3 col-md-6">
                    <label class="form-label d-block">${item.label}</label>
                    <input type="hidden" name="items[${i}][${item.key}]" id="${item.key}_${i}" value="${val}" required>
                    
                    <div class="btn-group btn-check-group w-100" role="group">
                        <button type="button" class="btn ${val === 'OK' ? 'btn-success' : 'btn-outline-secondary'} w-50 ${isItemLocked ? 'disabled' : ''}" 
                            data-value="OK" 
                            data-target-input="#${item.key}_${i}">
                            <i class="bi bi-check-lg"></i> OK
                        </button>
                        
                        <button type="button" class="btn ${val === 'Not OK' ? 'btn-danger' : 'btn-outline-secondary'} w-50 ${isItemLocked ? 'disabled' : ''}" 
                            data-value="Not OK" 
                            data-target-input="#${item.key}_${i}">
                            <i class="bi bi-x-lg"></i> Not OK
                        </button>
                    </div>
                </div>
                `;
            });

            const newDetail = document.createElement('div');
            newDetail.classList.add('dynamic-item-card', 'border', 'p-3', 'mb-3', 'rounded', 'shadow-sm'); 
            
            // Tombol Hapus: Hanya muncul jika item ini BARU (belum punya ID di database)
            const showDeleteBtn = !data?.id ? 
                `<button type="button" class="btn btn-danger btn-sm remove-detail-btn"><i class="bi bi-trash"></i> Hapus</button>` : 
                `<span class="badge bg-secondary">Tersimpan</span>`;

            newDetail.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Item #${i + 1}</h5>
                    ${showDeleteBtn}
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis Packaging</label>
                        <input type="text" name="items[${i}][packaging_type]" class="form-control" value="${packaging_type}" ${getReadonlyAttr(packaging_type)} required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <input type="text" name="items[${i}][supplier]" class="form-control" value="${supplier}" ${getReadonlyAttr(supplier)} required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Lot Batch</label>
                        <input type="text" name="items[${i}][lot_batch]" class="form-control" value="${lot_batch}" ${getReadonlyAttr(lot_batch)} required>
                    </div>

                    ${checksHtml} {{-- Render Tombol Loop Disini --}}

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Dimensi</label>
                        <input type="text" name="items[${i}][condition_dimension]" class="form-control" value="${dimension}" ${getReadonlyAttr(dimension)}>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Qty Barang</label>
                        <input type="number" name="items[${i}][quantity_goods]" class="form-control" value="${qty_goods}" min="0" ${getReadonlyAttr(qty_goods)} required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty Sampel</label>
                        <input type="number" name="items[${i}][quantity_sample]" class="form-control" value="${qty_sample}" min="0" ${getReadonlyAttr(qty_sample)} required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Qty Reject</label>
                        <input type="number" name="items[${i}][quantity_reject]" class="form-control" value="${qty_reject}" min="0" ${getReadonlyAttr(qty_reject)} required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Penerimaan</label>
                        <select name="items[${i}][acceptance_status]" class="form-select select2-dynamic" ${lockAccept ? 'disabled' : ''} required>
                            <option value="OK" ${accept_val === 'OK' ? 'selected' : ''}>OK</option>
                            <option value="Tolak" ${accept_val === 'Tolak' ? 'selected' : ''}>Tolak</option>
                        </select>
                        ${lockAccept ? `<input type="hidden" name="items[${i}][acceptance_status]" value="${accept_val}">` : ''}
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">No. Polisi</label>
                        <input type="text" name="items[${i}][no_pol]" class="form-control" value="${no_pol}" ${getReadonlyAttr(no_pol)} required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label d-block">Kondisi Kendaraan</label>
                        <div class="form-check">
                            ${vehicleConditions.map((c, index) => {
                                const isChecked = vehicle_cond?.split(',').includes(c);
                                return `
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="${c}" id="vehicle_${i}_${index}" ${isChecked ? 'checked' : ''} disabled>
                                    ${isChecked ? `<input type="hidden" name="items[${i}][vehicle_condition][]" value="${c}">` : ''}
                                    <label class="form-check-label" for="vehicle_${i}_${index}">${c}</label>
                                </div>`;
                            }).join('')}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">PBB / OP</label>
                        <input type="text" name="items[${i}][pbb_op]" class="form-control" value="${pbb_op}" ${getReadonlyAttr(pbb_op)}>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Keterangan (Optional)</label>
                        <textarea name="items[${i}][notes]" class="form-control" rows="2" ${isLocked(notes) ? 'readonly' : ''}>${notes}</textarea>
                    </div>

                    <input type="hidden" name="items[${i}][id]" value="${data?.id || ''}">
                    <input type="hidden" name="items[${i}][condition_weight_pcs]" value="${data?.condition_weight_pcs || ''}">
                </div>
            `;

            container.appendChild(newDetail);
            
            $(newDetail).find('.select2-dynamic').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih...",
                allowClear: !isLocked(data?.vehicle_condition), // Tidak bisa clear jika locked
                width: '100%',
                dropdownAutoWidth: false
            });

            detailIndex++;
        }

        if (addBtn) addBtn.addEventListener('click', () => renderDetailForm(null));

        // Render Data Existing
        const existingDetails = @json(old('items', $packagingInspection->items ?? []));
        if (existingDetails.length > 0) {
            existingDetails.forEach(itemData => renderDetailForm(itemData));
        } else {
            renderDetailForm(null);
        }
    });
</script>
@endpush