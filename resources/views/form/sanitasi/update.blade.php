@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Kontrol Sanitasi
            </h4>

            @php
                $isReadOnly = !empty($sanitasi->pemeriksaan);
            @endphp

            <form id="sanitasiForm" action="{{ route('sanitasi.update_qc', $sanitasi->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control" value="{{ $sanitasi->date }}" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <input type="text" class="form-control" value="Shift {{ $sanitasi->shift }}" readonly>
                                <input type="hidden" name="shift" value="{{ $sanitasi->shift }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Area - Sub Area</label>
                                <select id="areaSelect"
                                    class="form-control select2 @error('area') is-invalid @enderror"
                                    required disabled>
                                    <option value="">-- Pilih Area --</option>
                                    @foreach($areas as $a)
                                        <option value="{{ $a->uuid }}"
                                            data-bagian='@json(json_decode($a->bagian))'
                                            {{ $sanitasi->area == $a->uuid ? 'selected' : '' }}>
                                            {{ $a->area }} - {{ $a->sub_area }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <input type="hidden" name="area" value="{{ $sanitasi->area }}">
                            </div>
                        </div>

                        <div class="alert alert-warning py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Keterangan Kondisi:</strong> ✔ = OK / Bersih, 1–11 = Masalah:

                            @php
                                $items = [
                                    '✔ OK (Bersih)',
                                    '1. Basah',
                                    '2. Berdebu',
                                    '3. Kerak',
                                    '4. Noda',
                                    '5. Karat',
                                    '6. Sampah',
                                    '7. Retak/Pecah',
                                    '8. Sisa Produk',
                                    '9. Sisa Adonan',
                                    '10. Berjamur',
                                    '11. Lain-lain'
                                ];
                                $cols = 3;
                                $rows = ceil(count($items) / $cols);
                            @endphp

                            <div class="d-flex mt-1">
                                @for($c = 0; $c < $cols; $c++)
                                    <div class="me-4" style="flex: 1;">
                                        @for($r = 0; $r < $rows; $r++)
                                            @php $index = $r + $c * $rows; @endphp
                                            @if(isset($items[$index]))
                                                <div>{{ $items[$index] }}</div>
                                            @endif
                                        @endfor
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div id="pemeriksaan-wrapper"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update
                    </button>

                    <a href="{{ route('sanitasi.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('pemeriksaan-wrapper');
    const sanitasiData = @json($sanitasi);

    const kondisiMap = {
        "✔": "OK (Bersih)",
        "1": "Basah",
        "2": "Berdebu",
        "3": "Kerak",
        "4": "Noda",
        "5": "Karat",
        "6": "Sampah",
        "7": "Retak/Pecah",
        "8": "Sisa Produk",
        "9": "Sisa Adonan",
        "10": "Berjamur",
        "11": "Lain-lain"
    };

    function normalizeKondisi(value) {
        if (Array.isArray(value)) {
            return value.map(String);
        }

        return value ? [String(value)] : [];
    }

    function renderPemeriksaan(pemeriksaanData = {}) {
        wrapper.innerHTML = '';

        Object.keys(pemeriksaanData).forEach(b => {
            const rowData = pemeriksaanData[b] || {};
            const kondisi = normalizeKondisi(rowData.kondisi);

            const table = document.createElement('table');
            table.classList.add('table', 'table-bordered', 'mb-3');

            table.innerHTML = `
                <thead class="table-secondary">
                    <tr>
                        <th colspan="7">${b}</th>
                    </tr>
                    <tr>
                        <th>Waktu</th>
                        <th>Kondisi</th>
                        <th>Keterangan</th>
                        <th>Rencana Tindakan</th>
                        <th>Waktu Pengerjaan</th>
                        <th>Dikerjakan Oleh</th>
                        <th>Waktu Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="time"
                                name="pemeriksaan[${b}][waktu]"
                                class="form-control"
                                value="${rowData.waktu ?? ''}">
                        </td>

                        <td>
                            <select
                                name="pemeriksaan[${b}][kondisi][]"
                                class="form-control kondisi-select"
                                multiple>
                                <option value="✔" ${kondisi.includes('✔') ? 'selected' : ''}>OK (Bersih)</option>
                                ${[...Array(11)].map((_, i) => `
                                    <option value="${i + 1}" ${kondisi.includes(String(i + 1)) ? 'selected' : ''}>
                                        ${i + 1}. ${kondisiMap[String(i + 1)]}
                                    </option>
                                `).join('')}
                            </select>
                        </td>

                        <td>
                            <input type="text"
                                name="pemeriksaan[${b}][keterangan]"
                                class="form-control keterangan-input"
                                value="${rowData.keterangan ?? ''}">
                        </td>

                        <td>
                            <input type="text"
                                name="pemeriksaan[${b}][tindakan]"
                                class="form-control"
                                value="${rowData.tindakan ?? ''}">
                        </td>

                        <td>
                            <input type="time"
                                name="pemeriksaan[${b}][waktu_koreksi]"
                                class="form-control"
                                value="${rowData.waktu_koreksi ?? ''}">
                        </td>

                        <td>
                            <input type="text"
                                name="pemeriksaan[${b}][dikerjakan_oleh]"
                                class="form-control"
                                value="${rowData.dikerjakan_oleh ?? ''}">
                        </td>

                        <td>
                            <input type="time"
                                name="pemeriksaan[${b}][waktu_verifikasi]"
                                class="form-control"
                                value="${rowData.waktu_verifikasi ?? ''}">
                        </td>
                    </tr>
                </tbody>
            `;

            wrapper.appendChild(table);

            const select = $(table).find('.kondisi-select');

            select.select2({
                width: '100%',
                placeholder: '-- Pilih Kondisi --'
            });

            select.data('previous', kondisi);

            if (kondisi.length) {
                select.val(kondisi).trigger('change.select2');
            }
        });
    }

    $(document).on('change', '.kondisi-select', function () {
        const select = $(this);
        let values = select.val() || [];
        const previous = select.data('previous') || [];
        const row = select.closest('tr');
        const keteranganInput = row.find('.keterangan-input');

        if (values.includes('✔') && !previous.includes('✔')) {
            values = ['✔'];
        } else if (values.includes('✔') && values.length > 1) {
            values = values.filter(value => value !== '✔');
        }

        select.val(values).trigger('change.select2');
        select.data('previous', values);

        keteranganInput.val(
            values.map(value => kondisiMap[value]).join(', ')
        );
    });

    if (sanitasiData.pemeriksaan) {
        const pemeriksaanData = typeof sanitasiData.pemeriksaan === 'string'
            ? JSON.parse(sanitasiData.pemeriksaan)
            : sanitasiData.pemeriksaan;

        renderPemeriksaan(pemeriksaanData);
    }

    document.querySelectorAll(
        '#sanitasiForm input[type="text"], ' +
        '#sanitasiForm input[type="number"], ' +
        '#sanitasiForm input[type="time"], ' +
        '#sanitasiForm textarea'
    ).forEach(field => {
        if (field.value.trim() !== '') {
            field.readOnly = true;
        }
    });

    document.querySelectorAll('.kondisi-select').forEach(field => {
        if ($(field).val()?.length) {
            $(field).next('.select2-container').css({
                'pointer-events': 'none',
                'opacity': '0.7'
            });
        }
    });
});
</script>
@endpush
@endsection