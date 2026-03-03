@extends('layouts.app') {{-- Layout utama --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">➕ Tambah Area</h3>

            {{-- Alert error jika validasi gagal --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ups!</strong> Ada kesalahan pada inputan Anda:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Form Input --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('area_suhu.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="area" class="form-label">Nama Area</label>
                            <input type="text" name="area" class="form-control @error('area') is-invalid @enderror"
                                placeholder="Masukkan Area Baru   " value="{{ old('area') }}">
                            @error('area')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Standar Suhu (°C)</label>

                            <div class="row g-2">
                                <div class="col">
                                    <input type="number" step="0.1" name="standar_min"
                                        class="form-control @error('standar_min') is-invalid @enderror"
                                        placeholder="Minimum" value="{{ old('standar_min') }}">
                                    @error('standar_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-auto d-flex align-items-center">
                                    <span class="fw-bold">–</span>
                                </div>

                                <div class="col">
                                    <input type="number" step="0.1" name="standar_max"
                                        class="form-control @error('standar_max') is-invalid @enderror"
                                        placeholder="Maksimum" value="{{ old('standar_max') }}">
                                    @error('standar_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">💾 Simpan</button>
                            <a href="{{ route('area_suhu.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection