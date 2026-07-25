@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-plus-circle"></i> Form Input Pengecekan Klorin
                </h4>

                {{-- âœ… Tampilkan error validasi dari backend --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="klorinForm" action="{{ route('klorin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- ===================== IDENTITAS ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>Pengecekan Data Klorin</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="date" id="dateInput" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Pukul</label>
                                    <input type="time" name="pukul" id="timeInput" class="form-control" required>
                                </div>
                            </div>

                            {{-- LOKASI --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Lokasi <span class="text-danger">*</span>
                                    </label>

                                    <select name="lokasi" class="form-control" required>
                                        <option value="">-- Pilih Lokasi --</option>
                                        <option value="Packing">Packing</option>
                                        <option value="Meat Preparation">Meat Preparation</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== PEMERIKSAAN ===================== --}}
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
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
                                            accept="image/*" required>
                                        <div class="mt-2">
                                            <a id="footbasin-link" href="#" target="_blank">
                                                <img id="footbasin-preview" src="#" class="img-fluid d-none" style="max-height:200px">
                                            </a>
                                        </div>
                                        <small class="text-muted">*Gambar > 5 MB akan dikompresi otomatis sebelum diunggah.</small>
                                        <small id="footbasin-error" class="text-danger"></small>
                                    </div>

                                    {{-- HANDBASIN --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Hand Basin (Std 50-100 ppm)</label>
                                        <input type="file" id="handbasin" name="handbasin" class="form-control"
                                            accept="image/*" required>
                                        <div class="mt-2">
                                            <a id="handbasin-link" href="#" target="_blank">
                                                <img id="handbasin-preview" src="#" class="img-fluid d-none" style="max-height:200px">
                                            </a>
                                        </div>
                                        <small class="text-muted">*Gambar > 5 MB akan dikompresi otomatis sebelum diunggah.</small>
                                        <small id="handbasin-error" class="text-danger"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== CATATAN ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light"><strong>Catatan</strong></div>
                        <div class="card-body">
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan') }}</textarea>
                        </div>
                    </div>

                    {{-- ===================== TOMBOL ===================== --}}
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('klorin.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>

                <hr>
                <div id="resultArea"></div>
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

                        compressImage(file, maxFileSize, function(compressedFile) {

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

                    preview.classList.remove("d-none");

                }
            }


            function compressImage(file, maxSize, callback) {

                const img = new Image();
                const reader = new FileReader();

                reader.onload = function(e) {
                    img.src = e.target.result;
                };


                img.onload = function() {

                    const canvas = document.createElement("canvas");

                    let width = img.width;
                    let height = img.height;

                    const maxDimension = 1920;


                    if (width > maxDimension || height > maxDimension) {

                        if (width > height) {
                            height = height * maxDimension / width;
                            width = maxDimension;
                        } else {
                            width = width * maxDimension / height;
                            height = maxDimension;
                        }

                    }


                    canvas.width = width;
                    canvas.height = height;

                    canvas.getContext("2d")
                        .drawImage(img, 0, 0, width, height);


                    let quality = 0.9;


                    function compress() {

                        canvas.toBlob(function(blob) {

                            if (blob.size > maxSize && quality > 0.1) {

                                quality -= 0.1;
                                compress();
                                return;

                            }


                            callback(new File(
                                [blob],
                                file.name,
                                {
                                    type: "image/jpeg"
                                }
                            ));


                        }, "image/jpeg", quality);

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


            const dateInput = document.getElementById("dateInput");
            const timeInput = document.getElementById("timeInput");

            const now = new Date();

            dateInput.value = now.toISOString().split("T")[0];
            timeInput.value = now.toTimeString().slice(0,5);

        });
    </script>
@endsection
