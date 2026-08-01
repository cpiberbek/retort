@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">✏️ Edit Form</h3>

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

            {{-- Form Edit --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('list_form.update', $list_form->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="laporan" class="form-label">Nama Laporan</label>
                            <input
                                type="text"
                                name="laporan"
                                class="form-control @error('laporan') is-invalid @enderror"
                                value="{{ old('laporan', $list_form->laporan) }}" readonly
                            >
                            @error('laporan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_dokumen" class="form-label">No. Dokumen</label>
                            <input
                                type="text"
                                name="no_dokumen"
                                class="form-control @error('no_dokumen') is-invalid @enderror"
                                value="{{ old('no_dokumen', $list_form->no_dokumen) }}"
                            >
                            @error('no_dokumen')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">💾 Update</button>
                            <a href="{{ route('list_form.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
