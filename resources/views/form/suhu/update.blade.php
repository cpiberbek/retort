@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pemeriksaan Suhu dan RH
            </h4>

            <form id="suhuForm" action="{{ route('suhu.update_qc', $suhu->uuid) }}" method="POST">
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
                                       value="{{ $suhu->date }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" readonly>
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
                                       value="{{ $suhu->pukul }}" readonly>
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
                                            // Ambil nilai lama berdasarkan nama area
                                            $matched = $suhuData[$area->area] ?? null;
                                            $nilai = $matched['nilai'] ?? '';
                                            $isReadonly = $nilai !== ''; // jika sudah ada nilai, readonly
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $area->area }}</td>
                                            <td class="text-center">
                                        @if($area->standar_min !== null && $area->standar_max !== null)
                                            ({{ $area->standar_min }}°C) - ({{ $area->standar_max }}°C)
                                        @else
                                            <span class="null-standard text-muted">Standar masih kosong</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <input 
                                                type="number"
                                                step="0.1"
                                                class="form-control suhu-input"
                                                name="hasil_suhu[{{ $index }}][nilai]"
                                                value="{{ $nilai }}"
                                                data-min="{{ $area->standar_min }}"
                                                data-max="{{ $area->standar_max }}"
                                                placeholder="Masukkan suhu">

                                            <input 
                                                type="hidden"
                                                name="hasil_suhu[{{ $index }}][area]"
                                                value="{{ $area->area }}">

                                            <small class="text-danger warning-msg d-none">
                                                ⚠️ Suhu di luar standar!
                                            </small>
                                        </div>
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

    const pad = (num) => String(num).padStart(2, '0');

    // Default date, shift, time
    const dateInput = document.getElementById("dateInput");
    const shiftInput = document.getElementById("shiftInput");
    const timeInput = document.getElementById("timeInput");
    const now = new Date();

    if (dateInput) dateInput.value = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
    if (timeInput && !timeInput.value) timeInput.value = `${pad(now.getHours())}:00`;
    if (shiftInput) {
        const hh = now.getHours();
        shiftInput.value = (hh >= 7 && hh < 15) ? "1" : (hh >= 15 && hh < 23) ? "2" : "3";
    }

    // Validasi suhu
    const suhuInputs = document.querySelectorAll('.suhu-input');
    suhuInputs.forEach(input => {
        input.addEventListener('input', function () {
            const val = parseFloat(this.value);
            const min = parseFloat(this.dataset.min);
            const max = parseFloat(this.dataset.max);
            const warningMsg = this.parentElement.querySelector('.warning-msg');

            // Reset state
            this.classList.remove('is-invalid');
            if (warningMsg) warningMsg.classList.add('d-none');

            // Validasi hanya jika min, max, dan val ada
            if (!isNaN(val) && !isNaN(min) && !isNaN(max)) {
                if (val < min || val > max) {
                    this.classList.add('is-invalid');
                    if (warningMsg) {
                        warningMsg.textContent = `⚠️ Suhu di luar standar (${min} – ${max}°C)`;
                        warningMsg.classList.remove('d-none');
                    }
                }
            }
        });
    });

    // Format waktu selalu HH:00
    if (timeInput) {
        timeInput.addEventListener('input', function () {
            if (this.value) {
                const hh = this.value.split(':')[0].padStart(2,'0');
                this.value = `${hh}:00`;
            }
        });
    }
});
</script>


@endpush
@endsection
