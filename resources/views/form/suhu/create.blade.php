@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Suhu dan RH
            </h4>

            <form id="suhuForm" action="{{ route('suhu.store') }}" method="POST">
                @csrf

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Pemeriksaan Suhu</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pukul</label>
                                <input
                                type="time"
                                name="pukul"
                                id="timeInput"
                                class="form-control"
                                step="3600"
                                required
                                onkeydown="return false">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Input Suhu Area</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th style="width: 5%">No</th>
                                        <th>Area</th>
                                        <th>Standar Suhu (°C)</th>
                                        <th>Hasil Suhu (°C)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($area_suhus as $index => $area)
                                    @php
                                    // Cari nilai suhu berdasarkan nama area
                                    $matched = collect($suhuData)->firstWhere('area', $area->area);
                                    $nilai = $matched['nilai'] ?? '';
                                    @endphp

                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <input type="hidden" name="hasil_suhu[a{{ $index }}][area]" value="{{ $area->area }}">
                                            {{ $area->area }}
                                        </td>
                                        {{-- <td class="text-center">{{ $area->standar }}</td> --}}
                                        <td class="text-center">
                                            @if($area->standar_min !== null && $area->standar_max !== null)
                                                ({{ $area->standar_min }}°C) - ({{ $area->standar_max }}°C)
                                            @else
                                                <span class="null-standard text-muted">Standar masih kosong, (Cek Master Data Suhu)</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input
                                                type="number"
                                                step="0.1"
                                                name="hasil_suhu[a{{ $index }}][nilai]"
                                                value="{{ $nilai }}"
                                                class="form-control suhu-input"
                                                data-min="{{ $area->standar_min }}"
                                                data-max="{{ $area->standar_max }}"
                                                placeholder="Masukkan suhu">

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
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    @php
                        $hasNullStandard = collect($area_suhus)->contains(function($area) {
                            return $area->standar_min === null || $area->standar_max === null;
                        });
                    @endphp

                    <button type="submit" class="btn btn-success"
                        @if($hasNullStandard)
                            disabled
                            title="⚠️ Silahkan cek Master Suhu dan lengkapi Standar Suhu"
                        @endif
                    >
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('suhu.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {

        // ===== Helper untuk set tanggal & shift =====
        const pad = (num) => String(num).padStart(2, '0');
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");
        const timeInput = document.getElementById("timeInput");
        const now = new Date();

        dateInput.value = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
        if (!timeInput.value) timeInput.value = `${pad(now.getHours())}:00`;

        const hh = now.getHours();
        shiftInput.value = (hh >= 7 && hh < 15) ? "1" : (hh >= 15 && hh < 23) ? "2" : "3";

        // ===== Validasi Suhu =====
        const inputs = document.querySelectorAll('.suhu-input');

        inputs.forEach(input => {
            input.addEventListener('input', function () {
                const val = parseFloat(this.value);
                const min = parseFloat(this.dataset.min); // data-standar-min
                const max = parseFloat(this.dataset.max); // data-standar-max
                const warningMsg = this.parentElement.querySelector('.warning-msg');

                // Reset
                this.classList.remove('is-invalid');
                warningMsg.classList.add('d-none');

                // Jika min/max tidak ada atau val tidak angka → skip
                if (isNaN(val) || isNaN(min) || isNaN(max)) return;

                // Alert jika di luar standar
                if (val < min || val > max) {
                    warningMsg.textContent = `⚠️ Suhu di luar standar (${min} – ${max}°C)`;
                    warningMsg.classList.remove('d-none');
                    this.classList.add('is-invalid');
                }
            });
        });

    });
</script>

@endpush

@endsection
