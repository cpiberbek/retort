@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-pencil-square"></i> Edit Data Pengecekan Klorin
                </h4>

                {{-- âœ… Error dari backend --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="klorinEditForm" action="{{ route('klorin.edit_spv', $klorin->uuid) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- ===================== IDENTITAS ===================== --}}
                    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">

                        {{-- HEADER --}}
                        <div class="card-header border-0 py-3"
                            style="background: linear-gradient(135deg, #0d6efd, #3b82f6);">
                            <h5 class="mb-0 text-white fw-bold">
                                <i class="bi bi-clipboard-data me-2"></i>
                                Identitas Data Stuffing
                            </h5>
                        </div>

                        {{-- BODY --}}
                        <div class="card-body p-4 bg-light">

                            <div class="row g-4">

                                {{-- TANGGAL --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-calendar-event me-1 text-primary"></i>
                                        Tanggal
                                    </label>

                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-calendar-date text-primary"></i>
                                        </span>

                                        <input type="date" name="date" class="form-control border-start-0"
                                            value="{{ old('date', $klorin->date) }}" required>
                                    </div>
                                </div>

                                {{-- PUKUL --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-clock me-1 text-primary"></i>
                                        Pukul
                                    </label>

                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-alarm text-primary"></i>
                                        </span>

                                        <input type="time" name="pukul" class="form-control border-start-0"
                                            value="{{ old('pukul', $klorin->pukul) }}" required>
                                    </div>
                                </div>

                                {{-- LOKASI --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">
                                        <i class="bi bi-geo-alt me-1 text-primary"></i>
                                        Lokasi
                                    </label>

                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-building text-primary"></i>
                                        </span>

                                        <select name="lokasi" class="form-select border-start-0" required>

                                            <option value="">-- Pilih Lokasi --</option>

                                            <option value="Packing"
                                                {{ old('lokasi', $klorin->lokasi) == 'Packing' ? 'selected' : '' }}>
                                                Packing
                                            </option>

                                            <option value="Meat Preparation"
                                                {{ old('lokasi', $klorin->lokasi) == 'Meat Preparation' ? 'selected' : '' }}>
                                                Meat Preparation
                                            </option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ===================== PEMERIKSAAN ===================== --}}
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <strong>Pemeriksaan Klorin</strong>
                        </div>

                        <div class="card-body p-0">
                            <div class="alert alert-danger mt-2 py-3 px-3" style="font-size: 0.9rem;">
                                <i class="bi bi-info-circle"></i>
                                <strong> Standar Pemeriksaan:</strong>
                                <ul class="mb-2 mt-2">
                                    <li>Foot Basin : 200 ppm</li>
                                    <li>Hand Basin : 50 - 100 ppm</li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="row mb-3">

                                    {{-- FOOTBASIN --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Foot Basin (Std 200 ppm)</label>

                                        <input type="file" id="footbasin" name="footbasin" class="form-control"
                                            accept="image/*">
                                        
                                        <small id="footbasin-error" class="text-danger"></small>

                                        <div class="mt-2">
                                            <a id="footbasin-link"
                                                href="{{ $klorin->footbasin ? asset('storage/' . str_replace('public/', '', $klorin->footbasin)) : '#' }}"
                                                target="_blank">

                                                <img id="footbasin-preview"
                                                    src="{{ $klorin->footbasin ? asset('storage/' . str_replace('public/', '', $klorin->footbasin)) : '#' }}"
                                                    alt="Footbasin"
                                                    class="img-fluid {{ $klorin->footbasin ? '' : 'd-none' }}"
                                                    style="width:100px;height:100px;object-fit:cover;border-radius:8px;">

                                            </a>
                                        </div>
                                        <small class="text-muted">*Gambar > 5 MB akan dikompresi otomatis sebelum diunggah. | Kosongkan jika tidak diubah</small>
                                    </div>


                                    {{-- HANDBASIN --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Hand Basin (Std 50-100 ppm)</label>

                                        <input type="file" id="handbasin" name="handbasin" class="form-control"
                                            accept="image/*">

                                        <small id="handbasin-error" class="text-danger"></small>

                                        <div class="mt-2">
                                            <a id="handbasin-link"
                                                href="{{ $klorin->handbasin ? asset('storage/' . str_replace('public/', '', $klorin->handbasin)) : '#' }}"
                                                target="_blank">

                                                <img id="handbasin-preview"
                                                    src="{{ $klorin->handbasin ? asset('storage/' . str_replace('public/', '', $klorin->handbasin)) : '#' }}"
                                                    alt="Handbasin"
                                                    class="img-fluid {{ $klorin->handbasin ? '' : 'd-none' }}"
                                                    style="width:100px;height:100px;object-fit:cover;border-radius:8px;">

                                            </a>
                                        </div>
                                        <small class="text-muted">*Gambar > 5 MB akan dikompresi otomatis sebelum diunggah. | Kosongkan jika tidak diubah</small>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== CATATAN ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light"><strong>Catatan</strong></div>
                        <div class="card-body">
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $klorin->catatan) }}</textarea>
                        </div>
                    </div>

                    {{-- ===================== TOMBOL ===================== --}}
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('klorin.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== SCRIPT ===================== --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const maxFileSize = 5 * 1024 * 1024;

            function handleImageUpload(inputId, errorId, previewId, linkId) {

                const input = document.getElementById(inputId);
                const error = document.getElementById(errorId);
                const preview = document.getElementById(previewId);
                const link = document.getElementById(linkId);

                input.addEventListener("change", function () {

                    error.textContent = "";

                    const file = this.files[0];

                    if (!file) return;


                    if (file.size > maxFileSize) {

                        compressImage(file, maxFileSize, function(compressedFile){

                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressedFile);

                            input.files = dataTransfer.files;

                            setPreview(compressedFile);

                        });

                    } else {

                        setPreview(file);

                    }

                });


                function setPreview(file) {

                    const url = URL.createObjectURL(file);

                    preview.src = url;
                    link.href = url;

                }

            }


            function compressImage(file, maxSize, callback) {

                const img = new Image();
                const reader = new FileReader();

                reader.onload = function(e){
                    img.src = e.target.result;
                };


                img.onload = function(){

                    const canvas = document.createElement("canvas");

                    let width = img.width;
                    let height = img.height;

                    const maxDimension = 1920;


                    if(width > maxDimension || height > maxDimension){

                        if(width > height){
                            height = height * maxDimension / width;
                            width = maxDimension;
                        }else{
                            width = width * maxDimension / height;
                            height = maxDimension;
                        }

                    }


                    canvas.width = width;
                    canvas.height = height;


                    canvas.getContext("2d")
                        .drawImage(img,0,0,width,height);


                    let quality = 0.9;


                    function compress(){

                        canvas.toBlob(function(blob){

                            if(blob.size > maxSize && quality > 0.1){

                                quality -= 0.1;
                                compress();
                                return;

                            }


                            callback(new File(
                                [blob],
                                file.name,
                                {
                                    type:"image/jpeg"
                                }
                            ));


                        },"image/jpeg",quality);

                    }


                    compress();

                };


                reader.readAsDataURL(file);

            }


            handleImageUpload(
                "footbasin",
                "footbasin-error",
                "footbasin-preview",
                "footbasin-link"
            );


            handleImageUpload(
                "handbasin",
                "handbasin-error",
                "handbasin-preview",
                "handbasin-link"
            );

        });
    </script>

@endsection
