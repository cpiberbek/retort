@extends('layouts.app')

@section('content')

    <div class="container-fluid py-4">

        <div class="card shadow-sm border-0" style="border-radius: 12px;">

            {{-- Header --}}
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center"
                style="border-top-left-radius: 12px; border-top-right-radius: 12px;">

                <h4 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-plus-circle me-2" style="color: #198754;"></i>

                    Tambah Premix
                </h4>

                <a href="{{ route('premix.index') }}" class="btn btn-sm btn-outline-dark px-3">

                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <div class="card-body px-4 py-4">

                {{-- Error --}}
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">
                        <ul class="mb-0">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>
                    </div>
                @endif

                <form action="{{ route('premix.store') }}" method="POST">

                    @csrf

                    <div class="row">

                        {{-- Nama Premix --}}
                        <div class="col-md-6 mb-4">

                            <label class="form-label fw-semibold">
                                Nama Premix
                            </label>

                            <input type="text" name="nama_premix" class="form-control custom-input"
                                value="{{ old('nama_premix') }}" placeholder="Masukkan nama premix" required>

                        </div>

                        {{-- Kode Internal --}}
                        <div class="col-md-6 mb-4">

                            <label class="form-label fw-semibold">
                                Kode Internal
                            </label>

                            <input type="text" name="kode_internal" class="form-control custom-input"
                                value="{{ old('kode_internal') }}" placeholder="Masukkan kode internal">

                        </div>

                        {{-- Satuan --}}
                        <div class="col-md-6 mb-4">

                            <label class="form-label fw-semibold">
                                Satuan
                            </label>

                            <select name="satuan" class="form-select custom-input" required>

                                <option value="">-- Pilih Satuan --</option>

                                <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>
                                    KG
                                </option>

                                <option value="gr" {{ old('satuan') == 'gr' ? 'selected' : '' }}>
                                    GR
                                </option>

                                <option value="liter" {{ old('satuan') == 'liter' ? 'selected' : '' }}>
                                    LITER
                                </option>

                                <option value="sak" {{ old('satuan') == 'sak' ? 'selected' : '' }}>
                                    SAK
                                </option>

                            </select>

                        </div>

                    </div>

                    {{-- Button --}}
                    <div class="d-flex justify-content-end mt-3">

                        <button type="submit" class="btn btn-dark px-4 py-2 btn-save">

                            <i class="bi bi-save me-1"></i>
                            Simpan Data
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>

    <style>
        .custom-input {
            border-radius: 8px;
            padding: 0.6rem 0.9rem;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }

        .custom-input:focus {
            border-color: #212529 !important;
            box-shadow: 0 0 0 0.1rem rgba(33, 37, 41, 0.1) !important;
        }

        .btn-save {
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 37, 41, 0.25);
        }
    </style>

@endsection
