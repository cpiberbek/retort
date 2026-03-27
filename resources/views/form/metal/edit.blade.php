@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Pengecekan Metal Detector</h4>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('metal.edit_spv', $metal->uuid) }}">
                @csrf
                @method('PUT')

                {{-- Identitas --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>Identitas Data Metal</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control"
                                value="{{ old('date', $metal->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="timeInput" name="pukul" class="form-control"
                                value="{{ old('pukul', $metal->pukul) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Metal Detector --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>Metal Detector</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 d-flex flex-column align-items-center">
                                <label class="form-label mb-2" for="fe">FE</label>
                                <select name="fe" id="fe" class="form-control text-center" style="width: 200px;">
                                    <option value="">--Pilih--</option>
                                    <option value="Terdeteksi" {{ $metal->fe == 'Terdeteksi' ? 'selected' : '' }}>Terdeteksi</option>
                                    <option value="Tidak Terdeteksi" {{ $metal->fe == 'Tidak Terdeteksi' ? 'selected' : '' }}>Tidak Terdeteksi</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-flex flex-column align-items-center">
                                <label class="form-label mb-2" for="nfe">NFE</label>
                                <select name="nfe" id="nfe" class="form-control text-center" style="width: 200px;">
                                   <option value="">--Pilih--</option>
                                   <option value="Terdeteksi" {{ $metal->nfe == 'Terdeteksi' ? 'selected' : '' }}>Terdeteksi</option>
                                   <option value="Tidak Terdeteksi" {{ $metal->nfe == 'Tidak Terdeteksi' ? 'selected' : '' }}>Tidak Terdeteksi</option>
                               </select>
                           </div>

                           <div class="col-md-4 d-flex flex-column align-items-center">
                            <label class="form-label mb-2" for="sus">
                                SUS {{ Auth::user()->plant == '2debd595-89c4-4a7e-bf94-e623cc220ca6' ? '2.5 mm' : '2.0 mm' }}
                            </label>
                            <select name="sus" id="sus" class="form-control text-center" style="width: 200px;">
                               <option value="">--Pilih--</option>
                               <option value="Terdeteksi" {{ $metal->sus == 'Terdeteksi' ? 'selected' : '' }}>Terdeteksi</option>
                               <option value="Tidak Terdeteksi" {{ $metal->sus == 'Tidak Terdeteksi' ? 'selected' : '' }}>Tidak Terdeteksi</option>
                           </select>
                       </div>
                   </div>
                   <br>
                   <hr>
                   <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Nama Engineer</label>
                        <select name="nama_engineer" class="form-control" required>
                            <option value="">-- Pilih Engineer --</option>
                            @foreach($engineers as $eng)
                            <option value="{{ $eng->nama_karyawan }}"
                                {{ $metal->nama_engineer == $eng->nama_karyawan ? 'selected' : '' }}>
                                {{ $eng->nama_karyawan }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="card mb-4">
            <div class="card-header bg-light"><strong>Catatan</strong></div>
            <div class="card-body">
                <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $metal->catatan) }}</textarea>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Perbarui</button>
            <a href="{{ route('metal.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
</div>
</div>

{{-- Script --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.getElementById("timeInput");

        if(!dateInput.value) {
            let now = new Date();
            dateInput.value = now.toISOString().split('T')[0];
        }

        if(!timeInput.value) {
            let now = new Date();
            let hh = String(now.getHours()).padStart(2,'0');
            timeInput.value = hh + ':00';
        }
    });
</script>

{{-- Style --}}
<style>
    .big-checkbox {
        width: 40px;
        height: 40px;
        cursor: pointer;
        margin: 0 auto;
        display: block;
    }
</style>
@endsection
