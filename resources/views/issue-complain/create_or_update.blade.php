@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-header py-3"> <h4 class="mb-0 fw-bold text-primary"> <i class="bi bi-clipboard-check-fill me-2"></i> @if($issueComplain) Update Issue & Komplain @else Buat Issue & Komplain @endif </h4> </div>

        <div class="card-body">

            <form method="POST" action="{{ route('issue-complain.store') }}">
                @csrf

                @if($issueComplain)
                    <input type="hidden"
                        name="uuid"
                        value="{{ $issueComplain->uuid }}">
                @endif

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label for="date" class="font-weight-bold">
                            Tanggal
                        </label>

                        <input type="date"
                            name="date"
                            id="date"
                            class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', $issueComplain->date ?? now()->format('Y-m-d')) }}"
                            required>

                        @error('date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="judul_isu" class="font-weight-bold">
                            Judul Issue
                        </label>

                        <input type="text"
                            name="judul_isu"
                            id="judul_isu"
                            class="form-control @error('judul_isu') is-invalid @enderror"
                            value="{{ old('judul_isu', $issueComplain->judul_isu ?? '') }}"
                            placeholder="Masukkan judul isu"
                            required>

                        @error('judul_isu')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="jenis" class="font-weight-bold">
                            Jenis
                        </label>

                        <select name="jenis"
                            id="jenis"
                            class="form-control @error('jenis') is-invalid @enderror"
                            required>

                            <option value="">-- Pilih Jenis --</option>

                            <option value="progress"
                                {{ old('jenis', $issueComplain->jenis ?? '') === 'progress' ? 'selected' : '' }}>
                                Progress
                            </option>

                            <option value="penyelesaian"
                                {{ old('jenis', $issueComplain->jenis ?? '') === 'penyelesaian' ? 'selected' : '' }}>
                                Penyelesaian
                            </option>

                            <option value="update"
                                {{ old('jenis', $issueComplain->jenis ?? '') === 'update' ? 'selected' : '' }}>
                                Update
                            </option>

                        </select>

                        @error('jenis')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="detail" class="font-weight-bold">
                            Detail
                        </label>

                        <textarea name="detail"
                            id="detail"
                            rows="6"
                            class="form-control @error('detail') is-invalid @enderror"
                            placeholder="Masukkan detail isu atau komplain..."
                            required>{{ old('detail', $issueComplain->detail ?? '') }}</textarea>

                        @error('detail')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-3">

                    <a href="{{ route('issue-complain.index') }}"
                        class="btn btn-secondary mr-2">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        {{ $issueComplain ? 'Update' : 'Simpan' }}
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection