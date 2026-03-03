@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Kontrol Sanitasi
            </h4>

            <form id="sanitasiForm" action="{{ route('sanitasi.store') }}" method="POST">
                @csrf

                {{-- IDENTITAS DATA --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control selectpicker" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
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
                            <select name="area" id="areaSelect" class="form-control select2" required>
                                <option value="">-- Pilih Area --</option>
                                @foreach($areas as $area)
                                    @php
                                        $bagianArray = json_decode($area->bagian, true) ?? [];
                                    @endphp
                                    <option 
                                        value="{{ $area->area }}"
                                        data-sub_area="{{ $area->sub_area }}"
                                        data-bagian='@json($bagianArray)'>
                                        {{ $area->area }} - {{ $area->sub_area }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="sub_area" id="subAreaInput">

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
                        <i class="bi bi-save"></i> Simpan
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    $('#areaSelect').select2({
        width: '100%',
        placeholder: "-- Pilih Area --",
        allowClear: true
    });

    const dateInput = $("#dateInput");
    const shiftInput = $("#shiftInput");

    let now = new Date();
    dateInput.val(now.toISOString().split('T')[0]);
    let hour = now.getHours();
    shiftInput.val((hour >= 7 && hour < 15) ? "1" :
                   (hour >= 15 && hour < 23) ? "2" : "3");

    const wrapper = $("#pemeriksaan-wrapper");

    function renderPemeriksaan(bagianArray) {
        wrapper.html('');
        if(!bagianArray || bagianArray.length === 0) return;

        const now = new Date();
        const hh = String(now.getHours()).padStart(2,'0');
        const mm = String(now.getMinutes()).padStart(2,'0');
        const currentTime = `${hh}:${mm}`;

        bagianArray.forEach(b => {
            const table = $(`
                <table class="table table-bordered mb-3">
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
                            <td><input type="time" name="pemeriksaan[${b}][waktu]" class="form-control" value="${currentTime}"></td>
                            <td>
                                <select name="pemeriksaan[${b}][kondisi]" class="form-control">
                                    <option value="✔">✔</option>
                                    ${[...Array(11)].map((_,i)=>`<option value="${i+1}">${i+1}</option>`).join('')}
                                </select>
                            </td>
                            <td><input type="text" name="pemeriksaan[${b}][keterangan]" class="form-control"></td>
                            <td><input type="text" name="pemeriksaan[${b}][tindakan]" class="form-control"></td>
                            <td><input type="time" name="pemeriksaan[${b}][waktu_koreksi]" class="form-control"></td>
                            <td><input type="text" name="pemeriksaan[${b}][dikerjakan_oleh]" class="form-control"></td>
                            <td><input type="time" name="pemeriksaan[${b}][waktu_verifikasi]" class="form-control"></td>
                        </tr>
                    </tbody>
                </table>
            `);
            wrapper.append(table);
        });
    }

    let selectedOption = $('#areaSelect').find(':selected');
    if(selectedOption.val()) {
        let bagianArray = selectedOption.data('bagian');
        renderPemeriksaan(bagianArray);
        $('#subAreaInput').val(selectedOption.data('sub_area') || '');
    }

    $('#areaSelect').on('change', function() {
        let selected = $(this).find(':selected');
        let bagianArray = selected.data('bagian');
        renderPemeriksaan(bagianArray);
        $('#subAreaInput').val(selected.data('sub_area') || '');
    });

});
</script>
@endpush
@endsection
