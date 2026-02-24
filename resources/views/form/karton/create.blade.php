@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Kontrol Labelisasi Karton
            </h4>

            <form id="samplingForm" action="{{ route('karton.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Packing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required max="9999-12-31">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select name="nama_produk" id="nama_produk" class="form-select select2" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <select name="kode_produksi" id="kode_produksi"
                                    class="form-select select2 @error('kode_produksi') is-invalid @enderror" disabled
                                    required>
                                    <option></option>
                                </select>

                                @error('kode_produksi')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== ITEM SORTIR ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Pemeriksaan</strong>
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Waktu Proses</label>
                                <div class="input-group">
                                    <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control"
                                        required>
                                    <span class="input-group-text"> - </span>
                                    <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tgl Kedatangan</label>
                                <input type="date" name="tgl_kedatangan" id="tgl_kedatangan" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah / Tambahan</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" required min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Supplier</label>
                                <select id="nama_supplier" name="nama_supplier" class="form-control selectpicker"
                                    data-live-search="true" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->nama_supplier }}">{{ $supplier->nama_supplier }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No. Lot Karton</label>
                                <input type="text" name="no_lot" id="no_lot" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi (Karton)</label>
                                <input type="file" id="kode_karton" name="kode_karton" class="form-control"
                                    accept="image/*">
                                <small id="kode-karton-error" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <strong>Operator - KR</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Operator</label>
                                <select id="nama_operator" name="nama_operator" class="form-control selectpicker"
                                    data-live-search="true">
                                    <option value="">-- Pilih Operator --</option>
                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->nama_karyawan }}">{{ $operator->nama_karyawan }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama KR</label>
                                <select id="nama_koordinator" name="nama_koordinator" class="form-control selectpicker"
                                    data-live-search="true">
                                    <option value="">-- Pilih Koordinator --</option>
                                    @foreach($koordinators as $koordinator)
                                    <option value="{{ $koordinator->nama_karyawan }}">{{ $koordinator->nama_karyawan }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Tambahkan keterangan bila ada">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('karton.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- ===================== SCRIPT ===================== --}}
@push('scripts')
{{-- Include jQuery (Select2 depends on it) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<script>
    $(document).ready(function() {

       
    const produkSelect = document.querySelector('select[name="nama_produk"]');
    const batchSelect = document.getElementById('kode_produksi');
    const kodeError = document.getElementById('kodeError');
    const form = document.getElementById('samplingForm');
    const fileInput = document.getElementById('kode_karton');
    const fileError = document.getElementById('kode-karton-error');
    const maxFileSize = 2 * 1024 * 1024; // 2MB

    // Disable batch saat awal load (jika tidak ada old value)
    $('#nama_produk').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Ketik untuk mencari produk...',
        allowClear: true
    });

    // Kode produksi (DINAMIS)
    $('#kode_produksi').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Cari kode produksi...',
        allowClear: true,
        ajax: {
            delay: 300,
            transport: function (params, success, failure) {
                const produk = $('#nama_produk').val();
                if (!produk) {
                    return;
                }

                return $.ajax({
                    url: "{{ route('lookup.batch_packing', ['nama_produk' => '__PRODUK__']) }}".replace('__PRODUK__', encodeURIComponent(produk)),
                    data: { q: params.data.term },
                    success,
                    error: failure
                });
            },
            processResults: function (data) {
                return { results: data };
            }
        }
    });

    // Enable / reset saat produk berubah
    // $('#nama_produk').on('change', function () {
    //     $('#kode_produksi')
    //         .val(null)
    //         .trigger('change')
    //         .prop('disabled', !this.value);
    // });
    $('#nama_produk').on('change', function () {
        $('#kode_produksi')
            .prop('disabled', !this.value)
            .val(null)
            .trigger('change');
    });
    // ============ VALIDASI FILE (langsung muncul di bawah kolom) ============
    fileInput.addEventListener('change', function() {
        fileError.textContent = "";
        const file = fileInput.files[0];
        if (file && file.size > maxFileSize) {
            fileError.textContent = "Ukuran file maksimal 2MB.";
            fileInput.value = "";
        }
    });

    form.addEventListener('submit', function(e) {

        // validasi file upload
        const file = fileInput.files[0];
        fileError.textContent = "";

        // if (!file) {
        //     e.preventDefault();
        //     fileError.textContent = "File wajib diupload.";
        //     return;
        // }

        if (file.size > maxFileSize) {
            e.preventDefault();
            fileError.textContent = "Ukuran file maksimal 2MB.";
            fileInput.value = "";
            return;
        }
    });

    // ============ OTOMATIS TANGGAL & SHIFT ============
    const dateInput = document.getElementById("dateInput");

    let now = new Date();
    let yyyy = now.getFullYear();
    let mm = String(now.getMonth() + 1).padStart(2, '0');
    let dd = String(now.getDate()).padStart(2, '0');
    let hh = now.getHours();

    dateInput.value = `${yyyy}-${mm}-${dd}`;
});
</script>
@endpush