@extends('layouts.app') {{-- Layout utama --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">✏️ Edit Area</h3>

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
                    <form action="{{ route('area_sanitasi.update', $area_sanitasi->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nama Area --}}
                        <div class="mb-3">
                            <label for="area" class="form-label">Nama Area</label>
                            <input
                                type="text"
                                name="area"
                                class="form-control @error('area') is-invalid @enderror"
                                placeholder="Masukkan Nama Area"
                                value="{{ old('area', $area_sanitasi->area) }}">
                            @error('area')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- Sub-Area --}}
                        <div class="mb-3">
                            <label for="sub_area" class="form-label">Sub-Area</label>
                            <input
                                type="text"
                                name="sub_area"
                                class="form-control @error('sub_area') is-invalid @enderror"
                                placeholder="Masukkan Sub-Area dari Area ini"
                                value="{{ old('sub_area', $area_sanitasi->sub_area) }}"
                                required>
                            @error('sub_area')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- Bagian --}}
                        <div class="mb-3">
                            <label for="bagian" class="form-label">Bagian</label>
                            <div id="bagian-wrapper">
                                @php
                                    $bagians = old('bagian', json_decode($area_sanitasi->bagian, true) ?? []);
                                @endphp

                                @if(count($bagians) > 0)
                                    @foreach($bagians as $i => $b)
                                    <div class="input-group mb-2">
                                        <input type="text" name="bagian[]" class="form-control @error('bagian') is-invalid @enderror" value="{{ $b }}">
                                        @if($i == 0)
                                        <button type="button" class="btn btn-success btn-add">+</button>
                                        @else
                                        <button type="button" class="btn btn-danger btn-remove">-</button>
                                        @endif
                                    </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="text" name="bagian[]" class="form-control @error('bagian') is-invalid @enderror">
                                        <button type="button" class="btn btn-success btn-add">+</button>
                                    </div>
                                @endif
                            </div>
                            @error('bagian')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- JS Dinamis --}}
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const wrapper = document.getElementById('bagian-wrapper');

                            wrapper.addEventListener('click', function(e) {
                                if(e.target && e.target.classList.contains('btn-add')) {
                                    const newInputGroup = document.createElement('div');
                                    newInputGroup.classList.add('input-group', 'mb-2');
                                    newInputGroup.innerHTML = `
                                        <input type="text" name="bagian[]" class="form-control @error('bagian') is-invalid @enderror">
                                        <button type="button" class="btn btn-danger btn-remove">-</button>
                                    `;
                                    wrapper.appendChild(newInputGroup);
                                }

                                if(e.target && e.target.classList.contains('btn-remove')) {
                                    e.target.parentElement.remove();
                                }
                            });
                        });
                        </script>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">💾 Update</button>
                            <a href="{{ route('area_sanitasi.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
