@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Kontrol Sanitasi
            </h4>

            <form id="sanitasiForm" action="{{ route('sanitasi.edit_spv', $sanitasi->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- IDENTITAS DATA --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ $sanitasi->date }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control selectpicker" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ $sanitasi->shift == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ $sanitasi->shift == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ $sanitasi->shift == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        {{-- NOTE KONDISI --}}
                        <div class="alert alert-warning py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Keterangan Kondisi:</strong> ✔ = OK / Bersih, 1–11 = Masalah:
                            @php
                            $items = [
                            '✔ OK (Bersih)', '1. Basah', '2. Berdebu', '3. Kerak', '4. Noda',
                            '5. Karat', '6. Sampah', '7. Retak/Pecah', '8. Sisa Produk',
                            '9. Sisa Adonan', '10. Berjamur', '11. Lain-lain'
                            ];
                            $cols = 3;
                            $rows = ceil(count($items)/$cols);
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

                        {{-- AREA --}}
                        <div class="mb-3">
                            <label class="form-label">Area - Sub Area</label>
                            <select name="area" id="areaSelect" 
                                    class="form-control select2 @error('area') is-invalid @enderror" 
                                    required>
                                <option value="">-- Pilih Area --</option>
                                @foreach($areas as $a)
                                    <option value="{{ $a->uuid }}" data-bagian='@json(json_decode($a->bagian))'
                                        {{ $sanitasi->area == $a->uuid ? 'selected' : '' }}>
                                        {{ $a->area }} - {{ $a->sub_area }}
                                    </option>
                                @endforeach
                            </select>
                            @error('area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PEMERIKSAAN DINAMIS --}}
                        <div id="pemeriksaan-wrapper"></div>
                    </div>
                </div>

                {{-- BUTTON SIMPAN & KEMBALI --}}
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });

// PEMERIKSAAN DINAMIS
    document.addEventListener("DOMContentLoaded", function() {
        const areaSelect = document.getElementById('areaSelect');
        const wrapper = document.getElementById('pemeriksaan-wrapper');
        const sanitasiData = @json($sanitasi);

        function renderPemeriksaan(bagianArray, pemeriksaanData = {}) {
            wrapper.innerHTML = '';
            if(!bagianArray || bagianArray.length === 0) return;

            bagianArray.forEach(b => {
                const rowData = pemeriksaanData[b] || {};
                const table = document.createElement('table');
                table.classList.add('table', 'table-bordered', 'mb-3');
                table.innerHTML = `
            <thead class="table-secondary">
                <tr><th colspan="7">${b}</th></tr>
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
                    <td><input type="time" name="pemeriksaan[${b}][waktu]" class="form-control" value="${rowData.waktu ?? ''}"></td>
                    <td>
                        <select name="pemeriksaan[${b}][kondisi]" class="form-control">
                            <option value="✔" ${rowData.kondisi === '✔' ? 'selected' : ''}>✔</option>
                    ${[...Array(11)].map((_, i) => `<option value="${i+1}" ${rowData.kondisi == (i+1) ? 'selected' : ''}>${i+1}</option>`).join('')}
                        </select>
                    </td>
                    <td><input type="text" name="pemeriksaan[${b}][keterangan]" class="form-control" value="${rowData.keterangan ?? ''}"></td>
                    <td><input type="text" name="pemeriksaan[${b}][tindakan]" class="form-control" value="${rowData.tindakan ?? ''}"></td>
                    <td><input type="time" name="pemeriksaan[${b}][waktu_koreksi]" class="form-control" value="${rowData.waktu_koreksi ?? ''}"></td>
                    <td><input type="text" name="pemeriksaan[${b}][dikerjakan_oleh]" class="form-control" value="${rowData.dikerjakan_oleh ?? ''}"></td>
                    <td><input type="time" name="pemeriksaan[${b}][waktu_verifikasi]" class="form-control" value="${rowData.waktu_verifikasi ?? ''}"></td>
                </tr>
            </tbody>
                `;
                wrapper.appendChild(table);
            });
        }

    // Render pemeriksaan saat load
        if(areaSelect.value) {
            const selected = areaSelect.selectedOptions[0];
            const bagianArray = JSON.parse(selected.dataset.bagian || '[]');
            const pemeriksaanData = sanitasiData.pemeriksaan ? JSON.parse(sanitasiData.pemeriksaan) : {};
            renderPemeriksaan(bagianArray, pemeriksaanData);
        }

    // Render saat ganti area (tanpa data lama)
        areaSelect.addEventListener('change', function() {
            const selected = this.selectedOptions[0];
            const bagianArray = JSON.parse(selected.dataset.bagian || '[]');
            renderPemeriksaan(bagianArray);
        });
    });
</script>
@endpush
@endsection
