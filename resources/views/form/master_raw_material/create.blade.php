@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">➕ Tambah Bahan Baku Baru</h3>

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
                    <form action="{{ route('raw-material.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nama_bahan_baku" class="form-label">Nama Bahan Baku</label>
                            <input
                            type="text"
                            name="nama_bahan_baku"
                            id="nama_bahan_baku"
                            class="form-control @error('nama_bahan_baku') is-invalid @enderror"
                            placeholder="Contoh: Daging Sapi Triming"
                            value="{{ old('nama_bahan_baku') }}">
                            @error('nama_bahan_baku')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            {{-- Input Kode Internal --}}
                            <div class="col-md-6">
                                <label for="kode_internal" class="form-label">Kode Internal</label>
                                <input
                                type="text"
                                name="kode_internal"
                                id="kode_internal"
                                class="form-control @error('kode_internal') is-invalid @enderror"
                                placeholder="Contoh: RM-001"
                                value="{{ old('kode_internal') }}">
                                @error('kode_internal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Dropdown Satuan --}}
                            <div class="col-md-6 mt-3 mt-md-0">
                                <label for="satuan" class="form-label">Satuan</label>
                                <select name="satuan" id="satuan" class="form-select @error('satuan') is-invalid @enderror" required>
                                    <option value="" selected disabled>-- Pilih Satuan --</option>
                                    <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                    <option value="gr" {{ old('satuan') == 'gr' ? 'selected' : '' }}>Gram (gr)</option>
                                    <option value="liter" {{ old('satuan') == 'liter' ? 'selected' : '' }}>Liter (L)</option>
                                    <option value="sak" {{ old('satuan') == 'sak' ? 'selected' : '' }}>Sak</option>
                                </select>
                                @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-3">
                            <button type="submit" class="btn btn-primary px-4">💾 Simpan</button>
                            <a href="{{ route('raw-material.index') }}" class="btn btn-secondary px-4">⬅ Kembali</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection