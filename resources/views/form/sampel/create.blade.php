@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-plus-circle"></i> Form Input Pengambilan Sampel
                </h4>

                <form id="pvdcForm" action="{{ route('sampel.store') }}" method="POST">
                    @csrf

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>Identitas Data Sampel</strong>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date"
                                        name="date"
                                        id="dateInput"
                                        class="form-control"
                                        value="{{ old('date') }}"
                                        required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Jenis Sampel</label>
                                    <select name="jenis_sampel"
                                        id="jenis_sampel"
                                        class="form-control selectpicker"
                                        data-live-search="true"
                                        required>
                                        <option value="">-- Pilih Sampel --</option>
                                        <option value="Retain QC" @selected(old('jenis_sampel') == 'Retain QC')>Retain QC</option>
                                        <option value="Lab Internal" @selected(old('jenis_sampel') == 'Lab Internal')>Lab Internal</option>
                                        <option value="Lab Eksternal" @selected(old('jenis_sampel') == 'Lab Eksternal')>Lab Eksternal</option>
                                        <option value="RND" @selected(old('jenis_sampel') == 'RND')>RND</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="dibuat_oleh" class="form-label">Dibuat Oleh</label>
                                    <input type="text"
                                        class="form-control"
                                        id="dibuat_oleh"
                                        value="{{ auth()->user()->name }}"
                                        readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama_produk" class="form-label fw-semibold">
                                        Nama Varian <span class="text-danger">*</span>
                                    </label>

                                    <select id="nama_produk"
                                        name="nama_produk"
                                        class="form-control"
                                        required>
                                        <option value="">-- Pilih Varian --</option>

                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}"
                                                @selected(old('nama_produk') == $produk->nama_produk)>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <small class="text-muted">
                                        Pilih varian produk terlebih dahulu
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <label for="kode_batch" class="form-label fw-semibold">
                                        Kode Batch <span class="text-danger">*</span>
                                    </label>

                                    <select id="kode_batch"
                                        name="kode_produksi"
                                        class="form-control"
                                        disabled
                                        required>
                                        <option value="">
                                            Pilih Varian terlebih dahulu
                                        </option>
                                    </select>

                                    <small class="text-muted">
                                        Batch akan muncul otomatis
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <strong>Keterangan</strong>
                        </div>

                        <div class="card-body">
                            <textarea name="keterangan"
                                class="form-control"
                                rows="3"
                                placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $data->keterangan ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan
                        </button>

                        <a href="{{ route('sampel.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>

                <hr>

                <div id="resultArea"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dateInput = document.getElementById("dateInput");

            if (!dateInput.value) {
                let now = new Date();
                let yyyy = now.getFullYear();
                let mm = String(now.getMonth() + 1).padStart(2, '0');
                let dd = String(now.getDate()).padStart(2, '0');

                dateInput.value = `${yyyy}-${mm}-${dd}`;
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#nama_produk').on('change', function() {
                let namaProduk = $(this).val();
                let batchSelect = $('#kode_batch');

                if (!namaProduk) {
                    batchSelect.html(
                        '<option value="">Pilih Varian terlebih dahulu</option>'
                    );

                    batchSelect.prop('disabled', true);
                    return;
                }

                let url = "{{ route('lookup.batch', ['nama_produk' => ':nama']) }}"
                    .replace(':nama', encodeURIComponent(namaProduk));

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        batchSelect.prop('disabled', false);

                        batchSelect.html(
                            '<option value="">-- Pilih Batch --</option>'
                        );

                        data.forEach(function(item) {
                            batchSelect.append(
                                `<option value="${item.uuid}">${item.kode_produksi}</option>`
                            );
                        });
                    },
                    error: function(xhr) {
                        batchSelect.prop('disabled', true);

                        batchSelect.html(
                            '<option value="">Gagal mengambil data batch</option>'
                        );

                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection