{{-- Menggunakan layout utama --}}
@extends('layouts.app')

{{-- Mendefinisikan bagian style kustom --}}
@push('styles')
{{-- Menambahkan link untuk Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    /* Mengubah font utama */
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
    }

    /* Kustomisasi label */
    .form-label {
        font-weight: 600;
        color: #495057;
    }

    /* Kustomisasi input, select, dan textarea */
    .form-control,
    .form-select {
        border-radius: 8px;
    }

    /* PERBAIKAN CSS SELECT2 AGAR TINGGI SAMA DENGAN INPUT BOOTSTRAP */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(2.25rem + 2px) !important;
        border-radius: 8px !important;
        align-items: center;
        /* Menengahkan teks secara vertikal */
    }

    /* Memastikan dropdown arrow berada di tengah */
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        margin-top: -2px;
    }

    /* Kustomisasi Radio Button */
    .status-selector {
        display: flex;
        gap: 1rem;
    }

    .status-selector input[type="radio"] {
        opacity: 0;
        position: fixed;
        width: 0;
    }

    .status-selector label {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.75rem;
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .status-selector input[type="radio"]:checked+label {
        color: #fff;
        border-color: transparent;
    }

    .status-selector input#status_v:checked+label {
        background-color: #198754;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }

    .status-selector input#status_x:checked+label {
        background-color: #dc3545;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .status-selector label:hover {
        background-color: #e9ecef;
        border-color: #ced4da;
    }
</style>
@endpush

{{-- Mendefinisikan bagian konten --}}
@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-1"><i class="bi bi-clipboard2-check"></i> Form Input Temuan</h4>
            <p class="text-muted mb-4">Isi detail temuan pada formulir di bawah ini.</p>

            <form method="POST" action="{{ route('checklistmagnettrap.store') }}">
                @csrf

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Detail Produk & Temuan</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label">{{ __('Nama Varian') }}</label>

                            {{-- PERBAIKAN: class "form-select" bisa konflik dengan Select2 di beberapa kasus,
                            tapi dengan theme bootstrap-5 biasanya aman.
                            Kuncinya ada di script javascript di bawah. --}}
                            <select class="form-select select2 @error('nama_produk') is-invalid @enderror"
                                id="nama_produk" name="nama_produk" required>
                                <option></option> {{-- Placeholder for Select2 --}}
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}" {{ old('nama_produk')==$produk->nama_produk ?
                                    'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                @endforeach
                            </select>
                            @error('nama_produk')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kode_batch" class="form-label">{{ __('Kode Batch') }}</label>
                            <select name="kode_batch" id="kode_batch"
                                class="form-control @error('kode_batch') is-invalid @enderror" required disabled>
                                <option value="">Pilih Varian Terlebih Dahulu</option>
                            </select>
                            @error('kode_batch')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pukul" class="form-label">{{ __('Pukul') }}</label>
                                <input id="pukul" type="time" class="form-control @error('pukul') is-invalid @enderror"
                                    name="pukul" value="{{ old('pukul') }}" required>
                                @error('pukul')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_temuan" class="form-label">{{ __('Jumlah Temuan') }}</label>
                                <input id="jumlah_temuan" type="number"
                                    class="form-control @error('jumlah_temuan') is-invalid @enderror"
                                    name="jumlah_temuan" value="{{ old('jumlah_temuan') }}" required
                                    placeholder="Contoh: 0" min="0">
                                @error('jumlah_temuan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Pemeriksaan --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <strong class="text-dark">Status Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <div class="status-selector">
                                <input type="radio" name="status" id="status_v" value="v" {{ old('status', 'v' )=='v'
                                    ? 'checked' : '' }}>
                                <label for="status_v">✓ OK</label>

                                <input type="radio" name="status" id="status_x" value="x" {{ old('status')=='x'
                                    ? 'checked' : '' }}>
                                <label for="status_x">✗ NOT OK</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">{{ __('Keterangan') }}</label>
                            <textarea id="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                name="keterangan" rows="3"
                                placeholder="Tambahkan catatan jika diperlukan...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <strong class="text-dark">Verifikasi</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="produksi" class="form-label">{{ __('Operator Produksi') }}</label>
                                <select class="form-select @error('produksi_id') is-invalid @enderror" id="produksi"
                                    name="produksi_id" required>
                                    <option selected disabled value="">Pilih Operator...</option>

                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}" {{-- Logic Selected: Cek old input dulu, jika
                                        tidak ada cek data dari database (untuk edit), jika cocok beri 'selected' --}}
                                        {{ old('produksi_id', $checklistmagnettrap->produksi_id ?? '') == $operator->id
                                        ? 'selected' : '' }}>

                                        {{ $operator->nama_karyawan }}
                                    </option>
                                    @endforeach

                                </select>

                                @error('produksi_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="engineer" class="form-label">{{ __('Engineer') }}</label>
                                <select class="form-select @error('engineer_id') is-invalid @enderror" id="engineer"
                                    name="engineer_id" required>
                                    <option selected disabled value="">Pilih Engineer...</option>

                                    @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}" {{ old('engineer_id')==$engineer->id ?
                                        'selected' : '' }}>
                                        {{ $engineer->nama_karyawan }}
                                    </option>
                                    @endforeach

                                </select>
                                @error('engineer_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan Data</button>
                    <a href="{{ route('checklistmagnettrap.index') }}" class="btn btn-secondary"><i
                            class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Include jQuery (Select2 depends on it) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Include Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {

        // --- BAGIAN 1: KODE SELECT2 ANDA (TETAP AMAN) ---
        $('.select2').select2({
            theme: "bootstrap-5", 
            width: '100%', 
            placeholder: "Ketik untuk mencari varian...",
            allowClear: true
        });
    
    // const produkSelect = document.querySelector('select[name="nama_produk"]');
    const batchSelect = document.getElementById('kode_batch');

    // Disable batch saat awal load (jika tidak ada old value)
    // if (!produkSelect.value) {
    //     batchSelect.disabled = true;
    // }

    $('select[name="nama_produk"]').on('change', function (e) {
        let namaProduk = $(this).val();
        console.log("Selected Varian:", namaProduk);
        if (!namaProduk) {
            batchSelect.innerHTML = '<option value="">Pilih Varian Terlebih Dahulu</option>';
            batchSelect.disabled = true;
            expDateInput.value = '';
            return;
        }

        const url = "{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}".replace('__PRODUK__', encodeURIComponent(namaProduk));
        fetch(url)
        .then(response => response.json())
        .then(data => {
            batchSelect.disabled = false; 
            batchSelect.innerHTML = ""; // bersihkan dulu

            if (data.length === 0) {
                batchSelect.innerHTML = '<option value="">Batch Tidak Ditemukan</option>';
                batchSelect.disabled = true;
                return;
            }

            // Jika ada data, baru tampilkan default option
            batchSelect.innerHTML = '<option value="">-- Pilih Batch --</option>';

            data.forEach(batch => {
                batchSelect.innerHTML += `<option value="${batch.uuid}">${batch.kode_produksi}</option>`;
            });
        });
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