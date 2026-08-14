@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Pengecekan Metal Detector</h4>

            <form method="POST" action="{{ route('metal.store') }}">
                @csrf

                {{-- ===================== Bagian Identitas ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Metal</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control"
                                value="{{ old('date', $data->date ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="timeInput" name="pukul" class="form-control"
                                value="{{ old('pukul', $data->pukul ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <strong>Metal Detector</strong> 
                    </div>
                    <div class="card-body">
                      <div class="row mb-3">
                        <div class="col-md-4 d-flex flex-column align-items-center">
                            <label class="form-label mb-2" for="fe">FE 1.0 mm</label>
                            <select name="fe" id="fe" class="form-control text-center" style="width: 200px;">
                             <option value="">--Pilih--</option>
                             <option value="Terdeteksi">Terdeteksi</option>
                             <option value="Tidak Terdeteksi">Tidak Terdeteksi</option>
                         </select>
                     </div>

                     <div class="col-md-4 d-flex flex-column align-items-center">
                        <label class="form-label mb-2" for="nfe">NFE 1.5 mm</label>
                        <select name="nfe" id="nfe" class="form-control text-center" style="width: 200px;">
                           <option value="">--Pilih--</option>
                           <option value="Terdeteksi">Terdeteksi</option>
                           <option value="Tidak Terdeteksi">Tidak Terdeteksi</option>
                       </select>
                   </div>

                   <div class="col-md-4 d-flex flex-column align-items-center">
                    <label class="form-label mb-2" for="sus">
                        SUS {{ Auth::user()->plant == '2debd595-89c4-4a7e-bf94-e623cc220ca6' ? '2.5 mm' : '2.0 mm' }}
                    </label>
                    <select name="sus" id="sus" class="form-control text-center" style="width: 200px;">
                     <option value="">--Pilih--</option>
                     <option value="Terdeteksi">Terdeteksi</option>
                     <option value="Tidak Terdeteksi">Tidak Terdeteksi</option>
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
                    <option value="{{ $eng->nama_karyawan }}">{{ $eng->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Nama Karyawan Produksi</label>
                <select name="selected_produksi" class="form-control" required>
                    <option value="">-- Pilih Karyawan Produksi --</option>
                    @foreach($produksi as $prod)
                        <option value="{{ $prod->uuid }}">{{ $prod->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
</div>


{{-- ===================== Catatan ===================== --}}
<div class="card mb-4">
    <div class="card-header bg-light"><strong>Catatan</strong></div>
    <div class="card-body">
        <textarea name="catatan" class="form-control" rows="3"
        placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
    </div>
</div>

{{-- ===================== Tombol Simpan ===================== --}}
<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('metal.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

</form>
</div>
</div>
</div>

{{-- ===================== Script ===================== --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.getElementById("timeInput");

        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');
        let min = String(now.getMinutes()).padStart(2, '0');

        dateInput.value = `${yyyy}-${mm}-${dd}`;
        timeInput.value = `${hh}:${min}`;
    });
</script>

{{-- ===================== Style ===================== --}}
<style>
    .big-checkbox {
        width: 40px;
        height: 40px;
        cursor: pointer;
        margin: 0;
    }
    .big-checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>
@endsection
