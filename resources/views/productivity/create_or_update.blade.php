@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-header py-3">
            <h4 class="mb-0 fw-bold text-primary">
                <i class="bi bi-clipboard-check-fill me-2"></i>
                @if(isset($productivity))
                    Update Data Rekap Produktivitas - {{ \Carbon\Carbon::parse($productivity->date)->translatedFormat('F Y') }}
                @else
                    Buat Data Rekap Produktivitas
                @endif
            </h4>
        </div>

        <div class="card-body">

            @php
                $defaultDate = now()->format('Y-m');

                if (isset($productivity)) {
                    $defaultDate = \Carbon\Carbon::parse($productivity->date)->format('Y-m');
                } elseif (request()->filled('month')) {
                    $defaultDate = \Carbon\Carbon::createFromDate(now()->year, (int) request('month'), 1)->format('Y-m');
                }

                $maxDaysInMonth = \Carbon\Carbon::createFromFormat('Y-m', $defaultDate)->daysInMonth;
            @endphp

            <form method="POST" action="{{ route('productivity.store') }}" id="productivityForm">
                @csrf

                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="date" class="font-weight-bold">
                            Bulan & Tahun
                        </label>

                        @if(isset($productivity))
                            <input type="month"
                                id="date_display"
                                class="form-control"
                                value="{{ $defaultDate }}"
                                disabled>
                            <input type="hidden" name="date" value="{{ old('date', $defaultDate) }}">
                        @else
                            <input type="month"
                                name="date"
                                id="date"
                                class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date', $defaultDate) }}"
                                required>
                        @endif

                        @error('date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="hari_kerja" class="font-weight-bold">
                            Hari Kerja
                        </label>

                        <div class="input-group">
                            <input type="number"
                                name="hari_kerja"
                                id="hari_kerja"
                                class="form-control @error('hari_kerja') is-invalid @enderror"
                                value="{{ old('hari_kerja', $productivity->hari_kerja ?? '') }}"
                                min="1"
                                max="{{ $maxDaysInMonth }}"
                                step="1"
                                placeholder="Masukkan jumlah hari kerja"
                                required>

                            <div class="input-group-append">
                                <span class="input-group-text">Hari</span>
                            </div>
                        </div>

                        <small class="form-text text-muted" id="hariKerjaHint">
                            Maksimal {{ $maxDaysInMonth }} hari untuk bulan ini
                        </small>

                        @error('hari_kerja')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="tonase_bulanan" class="font-weight-bold">
                            Tonase Bulanan
                        </label>

                        @php
                            $tonase = old('tonase_bulanan', $productivity->tonase_bulanan ?? '');
                            $tonase = $tonase !== '' && fmod((float) $tonase, 1) == 0
                                ? number_format($tonase, 0, '.', '')
                                : $tonase;
                        @endphp

                        <div class="input-group">
                            <input type="number"
                                name="tonase_bulanan"
                                id="tonase_bulanan"
                                class="form-control @error('tonase_bulanan') is-invalid @enderror"
                                value="{{ $tonase }}"
                                step="0.01"
                                min="0"
                                placeholder="Masukkan tonase bulanan"
                                required>

                            <div class="input-group-append">
                                <span class="input-group-text">Ton</span>
                            </div>
                        </div>

                        @error('tonase_bulanan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="total_manpower" class="font-weight-bold">
                            Total Manpower
                        </label>

                        <div class="input-group">
                            <input type="number"
                                name="total_manpower"
                                id="total_manpower"
                                class="form-control @error('total_manpower') is-invalid @enderror"
                                value="{{ old('total_manpower', $productivity->total_manpower ?? '') }}"
                                min="0"
                                step="1"
                                placeholder="Masukkan total manpower"
                                required>

                            <div class="input-group-append">
                                <span class="input-group-text">Orang</span>
                            </div>
                        </div>

                        @error('total_manpower')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('productivity.index') }}"
                        class="btn btn-secondary mr-2">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection

@if(!isset($productivity))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('date');
    const hariKerjaInput = document.getElementById('hari_kerja');
    const hariKerjaHint = document.getElementById('hariKerjaHint');

    function updateMaxHariKerja() {
        if (!dateInput.value) return;

        const [year, month] = dateInput.value.split('-').map(Number);
        const daysInMonth = new Date(year, month, 0).getDate();

        hariKerjaInput.max = daysInMonth;
        hariKerjaHint.textContent = 'Maksimal ' + daysInMonth + ' hari untuk bulan ini';

        if (hariKerjaInput.value && parseInt(hariKerjaInput.value, 10) > daysInMonth) {
            hariKerjaInput.value = daysInMonth;
        }
    }

    dateInput.addEventListener('change', updateMaxHariKerja);
    updateMaxHariKerja();
});
</script>
@endpush
@endif