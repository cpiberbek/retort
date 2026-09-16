@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create / Update Productivity</h1>

        <a href="{{ route('productivity.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Data Productivity
            </h6>
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('productivity.store') }}">
                @csrf

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label for="date" class="font-weight-bold">
                            Bulan & Tahun
                        </label>

                        <input type="month"
                            name="date"
                            id="date"
                            class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', isset($productivity) ? \Carbon\Carbon::parse($productivity->date)->format('Y-m') : now()->format('Y-m')) }}"
                            {{ isset($productivity) ? 'readonly' : '' }}
                            required>

                        @error('date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="tonase_bulanan" class="font-weight-bold">
                            Tonase Bulanan
                        </label>

                        @php
                            $tonase = old('tonase_bulanan', $productivity->tonase_bulanan ?? '');
                            $tonase = $tonase !== '' && fmod((float) $tonase, 1) == 0
                                ? number_format($tonase, 0, '.', '')
                                : $tonase;
                        @endphp

                        <input type="number"
                            name="tonase_bulanan"
                            id="tonase_bulanan"
                            class="form-control @error('tonase_bulanan') is-invalid @enderror"
                            value="{{ $tonase }}"
                            step="0.01"
                            min="0"
                            placeholder="Masukkan tonase bulanan"
                            required>

                        @error('tonase_bulanan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="total_manpower" class="font-weight-bold">
                            Total Manpower
                        </label>

                        <input type="number"
                            name="total_manpower"
                            id="total_manpower"
                            class="form-control @error('total_manpower') is-invalid @enderror"
                            value="{{ old('total_manpower', $productivity->total_manpower ?? '') }}"
                            min="0"
                            step="1"
                            placeholder="Masukkan total manpower"
                            required>

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