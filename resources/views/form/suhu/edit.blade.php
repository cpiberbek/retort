@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pemeriksaan Suhu dan RH
            </h4>

            <form id="suhuForm" action="{{ route('suhu.edit_spv', $suhu->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Data Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control"
                                value="{{ $suhu->date }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ $suhu->shift == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ $suhu->shift == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ $suhu->shift == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pukul</label>
                                <input type="time" name="pukul" id="timeInput" class="form-control"
                                value="{{ $suhu->pukul }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== INPUT SUHU PER AREA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Edit Suhu Area</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Area</th>
                                        <th>Standar Suhu (°C)</th>
                                        <th>Hasil Suhu (°C)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   @foreach ($area_suhus as $index => $area)
                                   @php
                                   // ambil nilai lama berdasarkan nama area
                                   $matched = $suhuData[$area->area] ?? null;
                                   $nilai = $matched['nilai'] ?? '';
                                   @endphp
                                   <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $area->area }}</td>
                                        <td class="text-center">{{ $area->standar }}</td>
                                        <td>
                                            <input 
                                            type="text"
                                            inputmode="decimal"
                                            class="form-control suhu-input"
                                            name="hasil_suhu[{{ $index }}][nilai]"
                                            value="{{ $nilai }}"
                                            data-standar="{{ $area->standar }}"
                                            placeholder="Isi angka atau '-'">

                                            <input type="hidden"
                                            name="hasil_suhu[{{ $index }}][area]"
                                            value="{{ $area->area }}">

                                            <small class="text-danger warning-msg d-none">
                                                ⚠️ Suhu di luar standar!
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            {{-- ===================== CATATAN ===================== --}}
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>Keterangan</strong></div>
                <div class="card-body">
                    <textarea name="keterangan" class="form-control" rows="3"
                    placeholder="Tambahkan keterangan bila ada">{{ $suhu->keterangan }}</textarea>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>Catatan</strong></div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control" rows="3"
                    placeholder="Tambahkan catatan bila ada">{{ $suhu->catatan }}</textarea>
                </div>
            </div>

            {{-- ===================== TOMBOL ===================== --}}
            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('suhu.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
</div>

{{-- ===================== SCRIPT VALIDASI ===================== --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll('.suhu-input');

        const lessThanPattern = new RegExp('^<' + '?=?\\s*-?\\d+(?:\\.\\d+)?$');
        const numberExtractPattern = /-?\d+(?:\.\d+)?/g;

        inputs.forEach(function (input) {
            // Jalankan validasi awal jika ada nilai lama
            if (input.value) validateSuhu(input);

            input.addEventListener('input', function () {
                validateSuhu(this);
            });
        });

        function validateSuhu(input) {
            // ==========================================================
            // FILTER INPUT: Hanya izinkan angka, minus, titik, dan koma
            // ==========================================================
            input.value = input.value.replace(/[^0-9.,-]/g, '');

            // Antisipasi koma jadi titik
            let rawValue = input.value.replace(',', '.').trim();
            const val = parseFloat(rawValue);
            const standarText = (input.getAttribute('data-standar') || '').trim();
            const warningMsg = input.parentElement.querySelector('.warning-msg');

            // Reset error state
            input.classList.remove('is-invalid');
            if(warningMsg) warningMsg.classList.add('d-none');

            // ==========================================================
            // KONDISI INPUT KOSONG ATAU STRIP "-"
            // ==========================================================
            if (rawValue === '' || rawValue === '-') return;

            // Jika gagal parse angka
            if (isNaN(val) || !standarText) return;

            let min = null, max = null;

            // format "<10"
            if (lessThanPattern.test(standarText)) {
                const limit = parseFloat(standarText.replace(/[^\d.-]/g, ''));
                if (!isNaN(limit) && val > limit) {
                    if(warningMsg) {
                        warningMsg.textContent = `⚠️ Nilai melebihi batas < ${limit}°C`;
                        warningMsg.classList.remove('d-none');
                    }
                    input.classList.add('is-invalid');
                }
            } else {
            // format rentang "(-18) - (-22)" atau "0 - 4"
                const matches = standarText.replace(/[()]/g, '').match(numberExtractPattern);
                if (matches && matches.length >= 2) {
                    min = parseFloat(matches[0]);
                    max = parseFloat(matches[1]);
                    if (min > max) [min, max] = [max, min];

                    if (val < min || val > max) {
                        if(warningMsg) {
                            warningMsg.textContent = `⚠️ Nilai di luar standar (${min} – ${max}°C)`;
                            warningMsg.classList.remove('d-none');
                        }
                        input.classList.add('is-invalid');
                    }
                }
            }
        }
    });
</script>

<script>
    document.getElementById('timeInput').addEventListener('input', function() {
        let val = this.value;
        if(val){
            let jam = val.split(':')[0];
            this.value = jam.padStart(2,'0') + ':00';
        }
    });
</script>
@endpush

@endsection