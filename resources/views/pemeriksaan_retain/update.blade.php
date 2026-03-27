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
    }

    .form-control,
    .form-select {
        border-radius: 8px;
    }

    /* Style untuk Readonly/Disabled agar terlihat jelas terkunci */
    .form-control[readonly],
    .form-select:disabled,
    .form-check-input:disabled {
        background-color: #e9ecef;
        /* Abu-abu */
        cursor: not-allowed;
        border-color: #dee2e6;
        opacity: 1;
        /* Agar checkbox tetap terlihat jelas centangnya */
    }

    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
        min-height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .dynamic-item-card {
        background-color: #fdfdfd;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .temuan-group .form-check-inline {
        margin-right: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            <h4 class="mb-1"><i class="bi bi-clipboard-check"></i> Lengkapi Pemeriksaan Retain</h4>
            <p class="text-muted mb-4">Silakan lengkapi data yang kosong. Data yang sudah terisi tidak dapat diubah.</p>

            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops! Ada masalah input:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Mengarah ke route Update yang sama --}}
            <form id="retainForm"
            action="{{ route('pemeriksaan_retain.update', $pemeriksaanRetain->uuid) }}"
            method="POST">

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
                            <label class="form-label">Tanggal</label>
                            {{-- Header biasanya sudah terisi, jadi kita kunci --}}
                            <input type="date" class="form-control" name="tanggal"
                            value="{{ old('tanggal', $pemeriksaanRetain->tanggal) }}"
                            readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Hari</label>
                            <input type="text" class="form-control" name="hari"
                            value="{{ old('hari', $pemeriksaanRetain->hari) }}"
                            readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD DETAIL ITEM (DINAMIS) --}}
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong><i class="bi bi-list-nested"></i> Detail Pemeriksaan Retain</strong>
                        <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Item Baru</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="details-container">
                        {{-- Item dinamis akan ditambahkan di sini oleh JS --}}
                    </div>
                </div>
            </div>

            {{-- CARD KETERANGAN --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong><i class="bi bi-chat-left-text"></i> Keterangan Tambahan</strong>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        {{-- Logic Readonly untuk Textarea --}}
                        <textarea class="form-control" name="keterangan" rows="3"
                        {{ !empty($pemeriksaanRetain->keterangan) ? 'readonly' : '' }}>{{ old('keterangan', $pemeriksaanRetain->keterangan) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Simpan Perubahan</button>
                <a href="{{ route('pemeriksaan_retain.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
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

        const container = document.getElementById('details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0;

        // --- HELPER FUNCTIONS ---
        // Cek apakah data sudah terisi (Not Null & Not Empty String)
        function isLocked(value) {
            return value !== null && value !== undefined && value !== '';
        }

        // Return atribut readonly
        function getReadonlyAttr(value) {
            return isLocked(value) ? 'readonly' : '';
        }

        // Return class untuk background
        function getBgClass(value) {
            return isLocked(value) ? '' : ''; // Style dihandle via CSS selector [readonly]
        }

        function renderDetailForm(data = null) {
            const i = detailIndex;

            // Siapkan nilai
            const kode_produksi = data?.kode_produksi || '';
            const exp_date = data?.exp_date || '';
            const varian = data?.varian || '';
            const panjang = data?.panjang || '';
            const diameter = data?.diameter || '';

            const rasa_val = data?.sensori_rasa || '';
            const warna_val = data?.sensori_warna || '';
            const aroma_val = data?.sensori_aroma || '';
            const texture_val = data?.sensori_texture || '';

            // Checkbox logic: Jika true -> Locked (keep checked). Jika false -> Editable (bisa dicentang)
            const jamur_val = data?.temuan_jamur || false;
            const lendir_val = data?.temuan_lendir || false;
            const pinehole_val = data?.temuan_pinehole || false;
            const kejepit_val = data?.temuan_kejepit || false;
            const seal_val = data?.temuan_seal || false;

            const garam_val = data?.lab_garam || '';
            const air_val = data?.lab_air || '';
            const mikro_val = data?.lab_mikro || '';

            // Tombol Hapus: Hanya muncul jika ini item baru (data null)
            const deleteBtn = !data ?
        `<button type="button" class="btn btn-danger btn-sm remove-detail-btn"><i class="bi bi-trash"></i> Hapus</button>` :
    `<span class="badge bg-success">Tersimpan</span>`;

    const newDetail = document.createElement('div');
    newDetail.classList.add('dynamic-item-card', 'border', 'p-3', 'mb-3', 'rounded');

    newDetail.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Item #${i + 1}</h5>
                    ${deleteBtn}
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Kode Batch</label>
                       <input type="text"
                            name="items[${i}][kode_produksi]"
                            maxlength="10"
                            class="form-control kode-produksi"
                            value="${kode_produksi}"
                            ${getReadonlyAttr(kode_produksi)}>
                        <small class="text-danger kodeError d-none"></small>

                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Exp Date</label>
                        <input type="date" name="items[${i}][exp_date]" class="form-control" value="${exp_date}" ${getReadonlyAttr(exp_date)}>
                    </div>
                   <div class="col-md-2">
                        <label class="form-label">Varian</label>
                       <select
    name="items[${i}][varian]"
    class="form-select select2-dynamic select2-varian"
    data-placeholder="Pilih varian...">
                            <option value=""></option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}"
                                    ${varian === '{{ $produk->nama_produk }}' ? 'selected' : ''}>
                                    {{ $produk->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Panjang (cm)</label>
                        <input type="number" step="0.01" name="items[${i}][panjang]" class="form-control" value="${panjang}" ${getReadonlyAttr(panjang)}>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Diameter (cm)</label>
                        <input type="number" step="0.01" name="items[${i}][diameter]" class="form-control" value="${diameter}" ${getReadonlyAttr(diameter)}>
                    </div>
                </div>

                <hr>

                <h6>Sensori</h6>
                <div class="row g-4">
                    ${renderSelect(i, 'sensori_rasa', 'Rasa', rasa_val)}
                    ${renderSelect(i, 'sensori_warna', 'Warna', warna_val)}
                    ${renderSelect(i, 'sensori_aroma', 'Aroma', aroma_val)}
                    ${renderSelect(i, 'sensori_texture', 'Texture', texture_val)}
                </div>

                <hr>

                <h6>Temuan</h6>
                <div class="row">
                    <div class="col temuan-group">
                        ${renderCheckbox(i, 'temuan_jamur', 'Jamur', jamur_val)}
                        ${renderCheckbox(i, 'temuan_lendir', 'Lendir', lendir_val)}
                        ${renderCheckbox(i, 'temuan_pinehole', 'Pinehole', pinehole_val)}
                        ${renderCheckbox(i, 'temuan_kejepit', 'Kejepit', kejepit_val)}
                        ${renderCheckbox(i, 'temuan_seal', 'Seal Halus/Lepas', seal_val)}
                    </div>
                </div>

                <hr>

                <h6>Parameter Lab</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kadar Garam (%)</label>
                        <input type="number" step="0.1" name="items[${i}][lab_garam]" class="form-control" value="${garam_val}" ${getReadonlyAttr(garam_val)}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kadar Air (%)</label>
                        <input type="number" step="0.1" name="items[${i}][lab_air]" class="form-control" value="${air_val}" ${getReadonlyAttr(air_val)}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mikro (<10 cfu)</label>
                        <input type="number" step="0.1" name="items[${i}][lab_mikro]" class="form-control" value="${mikro_val}" ${getReadonlyAttr(mikro_val)}>
                    </div>
                </div>
    `;

    container.appendChild(newDetail);

            // Inisialisasi Select2
// ===== INIT VARIAN SAJA =====
    const varianLocked = isLocked(varian);

    const $selectVarian = $(newDetail).find('.select2-varian');

    $selectVarian.select2({
        theme: "bootstrap-5",
        placeholder: "Pilih...",
        allowClear: !varianLocked,
        dropdownAutoWidth: true
    });

    if (varian) {
        $selectVarian.val(varian).trigger('change.select2');
    }

    if (varianLocked) {
        $selectVarian.prop('disabled', true);

        $selectVarian.after(
    `<input type="hidden" name="items[${i}][varian]" value="${varian}">`
    );
    }

// ===== INIT SENSORI =====
    $(newDetail)
    .find('.select2-dynamic:not(.select2-varian)')
    .select2({
        theme: "bootstrap-5",
        placeholder: "Pilih...",
        dropdownAutoWidth: true
    });


    detailIndex++;
}

        // --- Helper Render Select (Dengan Hidden Input jika Disabled) ---
function renderSelect(index, name, label, value) {
    const locked = isLocked(value);
    const disabledAttr = locked ? 'disabled' : '';
            // Jika disabled, select tidak terkirim via POST. Kita butuh hidden input.
    const hiddenInput = locked ? `<input type="hidden" name="items[${index}][${name}]" value="${value}">` : '';

    return `
                <div class="col-md-3">
                    <label class="form-label">${label}</label>
                    <select name="items[${index}][${name}]" class="form-select select2-dynamic" ${disabledAttr}>
                        <option value="" ${value == '' ? 'selected' : ''}>Pilih...</option>
                        <option value="1" ${value == '1' ? 'selected' : ''}>1</option>
                        <option value="2" ${value == '2' ? 'selected' : ''}>2</option>
                        <option value="3" ${value == '3' ? 'selected' : ''}>3</option>
                    </select>
                    ${hiddenInput}
                </div>
    `;
}

        // --- Helper Render Checkbox (Dengan Hidden Input jika Checked/Locked) ---
function renderCheckbox(index, name, label, isChecked) {
            // Logika: Jika sudah dicentang (true), user tidak boleh uncheck (locked).
            // Jika belum (false), user boleh centang.
    const locked = isChecked === true || isChecked === 1;
            const disabledAttr = locked ? 'disabled onclick="return false;"' : ''; // double protection
            const checkedAttr = locked ? 'checked' : '';
            // Jika disabled & checked, kita butuh hidden input bernilai 1 agar controller tahu ini tetap "on".
            const hiddenInput = locked ? `<input type="hidden" name="items[${index}][${name}]" value="1">` : '';

            return `
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" value="1" name="items[${index}][${name}]" id="${name}_${index}" ${checkedAttr} ${disabledAttr}>
                    <label class="form-check-label" for="${name}_${index}">${label}</label>
                    ${hiddenInput}
                </div>
            `;
        }

        addBtn.addEventListener('click', () => renderDetailForm(null));

        container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-detail-btn');
            if (removeBtn) {
                removeBtn.closest('.dynamic-item-card').remove();
            }
        });

        // Load data existing
        const existingDetails = @json(old('items', $pemeriksaanRetain->items ?? []));
        if (existingDetails.length > 0) {
            existingDetails.forEach(itemData => renderDetailForm(itemData));
        } else {
            renderDetailForm(null);
        }

    });
    </script>
    <script>
        $(document).ready(function () {

            const form = $('#retainForm');

            function validateKode(input) {

                const kodeInput = $(input);
                const kodeError = kodeInput.closest('.col-md-3').find('.kodeError');

                let value = kodeInput.val().toUpperCase().replace(/\s+/g, '');
                kodeInput.val(value);

                kodeError.text('').addClass('d-none');

                if (value.length !== 10) {
                    kodeError.text('Kode batch harus 10 karakter').removeClass('d-none');
                    return false;
                }

                if (!/^[A-Z0-9]+$/.test(value)) {
                    kodeError.text('Hanya huruf & angka').removeClass('d-none');
                    return false;
                }

                if (!/^[A-L]$/.test(value.charAt(1))) {
                    kodeError.text('Karakter ke-2 harus huruf bulan (A-L)').removeClass('d-none');
                    return false;
                }

                let hari = parseInt(value.substr(2, 2));
                if (hari < 1 || hari > 31) {
                    kodeError.text('Tanggal tidak valid').removeClass('d-none');
                    return false;
                }

                return true;
            }

    // realtime per input
            $(document).on('input', '.kode-produksi', function () {
                validateKode(this);
            });

    // submit
            form.on('submit', function (e) {

                let valid = true;

                $('.kode-produksi').each(function () {
                    if (!validateKode(this)) {
                        valid = false;
                        $(this).focus();
                        return false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('Ada kode batch tidak valid!');
                }

            });

        });
    </script>

    @endpush