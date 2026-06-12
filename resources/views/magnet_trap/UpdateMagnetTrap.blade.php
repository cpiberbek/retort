{{-- Menggunakan layout utama --}}
@extends('layouts.app')

{{-- Mendefinisikan bagian style kustom --}}
@push('styles')
{{-- Menambahkan link untuk Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
{{-- Include Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- Theme for Select2 to match Bootstrap 5 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    /* Style dasar */
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    .form-control, .form-select {
        border-radius: 8px;
    }

    /* === PERBAIKAN CSS SELECT2 DI SINI === */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(2.25rem + 2px) !important;
        border-radius: 8px !important;
        align-items: center;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        margin-top: -2px;
    }

    /* CSS Khusus untuk Readonly agar terlihat jelas terkunci */
    .form-control[readonly], .form-select[disabled], .select2-container--disabled .select2-selection {
        background-color: #e9ecef !important; /* Abu-abu bootstrap */
        cursor: not-allowed;
    }
    /* ===================================== */

    .status-selector { display: flex; gap: 1rem; }
    .status-selector input[type="radio"] { opacity: 0; position: fixed; width: 0; }
    .status-selector label {
        display: flex; align-items: center; justify-content: center;
        width: 100%; padding: 0.75rem; background-color: #f8f9fa;
        border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer;
        transition: all 0.3s ease; font-weight: 600;
    }
    /* Disable style for radio labels */
    .status-selector input[type="radio"]:disabled + label {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .status-selector input[type="radio"]:checked + label { color: #fff; border-color: transparent; }
    .status-selector input#status_v:checked + label {
        background-color: #198754; box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }
    .status-selector input#status_x:checked + label {
        background-color: #dc3545; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }
    .status-selector label:hover { background-color: #e9ecef; border-color: #ced4da; }
</style>
@endpush

{{-- Mendefinisikan bagian konten --}}
@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-1"><i class="bi bi-pencil-square"></i> Form Edit Temuan</h4>
            <p class="text-muted mb-4">Ubah detail temuan. <span class="text-danger">*Data yang sudah terisi tidak dapat diubah.</span></p>

            <form method="POST" action="{{ route('checklistmagnettrap.update', $checklistmagnettrap->id) }}">
                @csrf
                @method('PUT')

                {{-- ===================== Bagian Detail Produk ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Detail Produk & Temuan</strong>
                    </div>
                    <div class="card-body">

                        {{-- Field: Nama Produk --}}
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label">{{ __('Nama Varian') }}</label>

                            {{-- Cek apakah data sudah ada --}}
                            @php $isProdukFilled = !empty($checklistmagnettrap->nama_produk); @endphp

                            <select class="form-select select2 @error('nama_produk') is-invalid @enderror"
                                    id="nama_produk"
                                    name="nama_produk"
                                    {{ $isProdukFilled ? 'disabled' : '' }}
                                    required>
                                <option></option>
                                @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ (old('nama_produk', $checklistmagnettrap->nama_produk) == $produk->nama_produk) ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Trik: Jika disabled, kirim value via hidden input agar controller tidak error --}}
                            @if($isProdukFilled)
                                <input type="hidden" name="nama_produk" value="{{ $checklistmagnettrap->nama_produk }}">
                            @endif

                            @error('nama_produk')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        {{-- Field: Kode Batch --}}
                        <div class="mb-3">
                            <label for="kode_batch" class="form-label">{{ __('Kode Batch') }}</label>

                            <input
                                id="kode_batch"
                                type="text"
                                class="form-control @error('kode_batch') is-invalid @enderror {{ !empty($checklistmagnettrap->kode_batch) ? 'bg-body-secondary' : '' }}"
                                name="kode_batch"
                                value="{{ old('kode_batch', $checklistmagnettrap->mincing->kode_produksi) }}"
                                required
                                autocomplete="off"
                                placeholder="Sesuai data mincing"
                                maxlength="10"
                                list="batch_suggestions"
                                {{ !empty($checklistmagnettrap->kode_batch) ? 'readonly' : '' }}
                            >

                            <datalist id="batch_suggestions"></datalist>

                            @error('kode_batch')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror

                            @if(empty($checklistmagnettrap->kode_batch))
                                <small class="text-muted d-block mt-1" style="font-size: 0.8em;">
                                    *Auto uppercase, max 10 digit, dilarang spasi/simbol.
                                </small>
                            @endif
                        </div>

                        <div class="row">
                            {{-- Field: Pukul --}}
                            <div class="col-md-6 mb-3">
                                <label for="pukul" class="form-label">{{ __('Pukul') }}</label>
                                <input id="pukul" type="time"
                                       class="form-control @error('pukul') is-invalid @enderror {{ !empty($checklistmagnettrap->pukul) ? 'bg-body-secondary' : '' }}"
                                       name="pukul"
                                       value="{{ old('pukul', $checklistmagnettrap->pukul) }}"
                                       required
                                       {{ !empty($checklistmagnettrap->pukul) ? 'readonly' : '' }}>
                                @error('pukul')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- Field: Jumlah Temuan --}}
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_temuan" class="form-label">{{ __('Jumlah Temuan') }}</label>
                                {{-- Catatan: Biasanya '0' dianggap ada isi, tapi jika null baru boleh diedit.
                                     Jika logika Anda 0 boleh diedit, ganti !empty dengan check null --}}
                                <input id="jumlah_temuan" type="number"
                                       class="form-control @error('jumlah_temuan') is-invalid @enderror {{ ($checklistmagnettrap->jumlah_temuan !== null) ? 'bg-body-secondary' : '' }}"
                                       name="jumlah_temuan"
                                       value="{{ old('jumlah_temuan', $checklistmagnettrap->jumlah_temuan) }}"
                                       required
                                       min="0"
                                       placeholder="Contoh: 0"
                                       {{ ($checklistmagnettrap->jumlah_temuan !== null) ? 'readonly' : '' }}>
                                @error('jumlah_temuan')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== Bagian Status & Keterangan ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <strong class="text-dark">Status Pemeriksaan</strong>
                    </div>
                    <div class="card-body">

                        {{-- Field: Status --}}
                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            @php $isStatusFilled = !empty($checklistmagnettrap->status); @endphp

                            <div class="status-selector">
                                <input type="radio" name="status" id="status_v" value="v"
                                    {{ (old('status', $checklistmagnettrap->status) == 'v') ? 'checked' : '' }}
                                    {{ $isStatusFilled ? 'disabled' : '' }}>
                                <label for="status_v">✓ OK</label>

                                <input type="radio" name="status" id="status_x" value="x"
                                    {{ (old('status', $checklistmagnettrap->status) == 'x') ? 'checked' : '' }}
                                    {{ $isStatusFilled ? 'disabled' : '' }}>
                                <label for="status_x">✗ NOT OK</label>
                            </div>

                            {{-- Hidden input untuk status jika disabled --}}
                            @if($isStatusFilled)
                                <input type="hidden" name="status" value="{{ $checklistmagnettrap->status }}">
                            @endif
                        </div>

                        {{-- Field: Keterangan --}}
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">{{ __('Keterangan') }}</label>
                            <textarea id="keterangan"
                                      class="form-control @error('keterangan') is-invalid @enderror {{ !empty($checklistmagnettrap->keterangan) ? 'bg-body-secondary' : '' }}"
                                      name="keterangan"
                                      rows="3"
                                      placeholder="Tambahkan catatan jika diperlukan..."
                                      {{ !empty($checklistmagnettrap->keterangan) ? 'readonly' : '' }}
                                      >{{ old('keterangan', $checklistmagnettrap->keterangan) }}</textarea>
                            @error('keterangan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ===================== Bagian Verifikasi ===================== --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong class="text-dark">Verifikasi</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            {{-- Field: Operator Produksi --}}
                    <div class="col-md-6 mb-3">
                        <label for="produksi" class="form-label">{{ __('Operator Produksi') }}</label>

                        {{-- Cek apakah data sudah terisi sebelumnya --}}
                        @php $isProduksiFilled = !empty($checklistmagnettrap->produksi_id); @endphp

                        <select class="form-select @error('produksi_id') is-invalid @enderror"
                                id="produksi"
                                name="produksi_id"
                                required
                                {{ $isProduksiFilled ? 'disabled' : '' }}>

                            <option value="" disabled {{ is_null($checklistmagnettrap->produksi_id) ? 'selected' : '' }}>Pilih Operator...</option>

                            @foreach($operators as $operator)
                                <option value="{{ $operator->id }}"
                                    {{ (old('produksi_id', $checklistmagnettrap->produksi_id) == $operator->id) ? 'selected' : '' }}>
                                    {{ $operator->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>

                        {{-- PENTING: Jika select disabled, value tidak terkirim via form post.
                            Maka kita butuh input hidden agar controller tetap menerima ID-nya --}}
                        @if($isProduksiFilled)
                            <input type="hidden" name="produksi_id" value="{{ $checklistmagnettrap->produksi_id }}">
                        @endif

                        @error('produksi_id')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                            {{-- Field: Engineer --}}
                            <div class="col-md-6 mb-3">
                                <label for="engineer" class="form-label">{{ __('Engineer') }}</label>

                                {{-- Cek apakah data Engineer sudah terisi --}}
                                @php $isEngineerFilled = !empty($checklistmagnettrap->engineer_id); @endphp

                                <select class="form-select @error('engineer_id') is-invalid @enderror"
                                        id="engineer"
                                        name="engineer_id"
                                        required
                                        {{ $isEngineerFilled ? 'disabled' : '' }}>

                                    <option disabled value="" {{ is_null($checklistmagnettrap->engineer_id) ? 'selected' : '' }}>Pilih Engineer...</option>

                                    @foreach($engineers as $engineer)
                                        <option value="{{ $engineer->id }}"
                                            {{ (old('engineer_id', $checklistmagnettrap->engineer_id) == $engineer->id) ? 'selected' : '' }}>
                                            {{ $engineer->nama_karyawan }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Input hidden agar data tetap terkirim saat disabled --}}
                                @if($isEngineerFilled)
                                    <input type="hidden" name="engineer_id" value="{{ $checklistmagnettrap->engineer_id }}">
                                @endif

                                @error('engineer_id')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== Tombol Aksi ===================== --}}
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Data</button>
                    <a href="{{ route('checklistmagnettrap.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Include jQuery and Select2 JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {

        // --- 1. CONFIG SELECT2 (BAWAAN ANDA) ---
        $('.select2').select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: "Ketik untuk mencari varian...",
        });

        // --- 2. LOGIC KODE BATCH ---
        // Cek apakah input readonly? Jika readonly, script tidak perlu jalan
        if ($('#kode_batch').prop('readonly') === false) {

            $('#kode_batch').on('input', function() {
                let input = $(this);
                let value = input.val();

                // A. Auto Uppercase
                value = value.toUpperCase();

                // B. Hapus Karakter Terlarang (Spasi, $, %, #, *)
                value = value.replace(/[\s$#%*]/g, '');

                // C. Safety Net Max 10 Karakter (selain maxlength di HTML)
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }

                // Update value di input
                input.val(value);

                // D. Auto Suggestion (AJAX)
                if (value.length >= 2) {
                    $.ajax({
                        url: "{{ route('ajax.search.batch') }}", // Pastikan route ini ada
                        type: "GET",
                        data: { q: value },
                        success: function(data) {
                            let dataList = $('#batch_suggestions');
                            dataList.empty();

                            $.each(data, function(key, item) {
                                dataList.append('<option value="' + item + '">');
                            });
                        }
                    });
                }
            });

            // E. Validasi saat user pindah kursor (Blur)
            $('#kode_batch').on('blur', function() {
                let value = $(this).val();
                // Jika terisi tapi kurang dari 10 karakter, beri peringatan
                if(value.length > 0 && value.length < 10) {
                    alert('Format Salah: Kode Batch harus tepat 10 karakter!');
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        }
    });
</script>
@endpush
