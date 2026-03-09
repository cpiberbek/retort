@extends('layouts.app') {{-- Menggunakan layout utama Anda --}}

{{-- Mendefinisikan bagian style kustom --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
    }

    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
        min-height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .dynamic-item-card {
        background-color: #fdfdfd;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .temuan-group .form-check-inline {
        margin-right: 1.5rem;
    }
</style>
@endpush

{{-- Mendefinisikan bagian konten --}}
@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            <h4 class="mb-1"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Retain</h4>
            <p class="text-muted mb-4">Perbarui detail formulir pemeriksaan retain di bawah ini.</p>

            {{-- Tampilkan error jika ada --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops! Ada beberapa masalah dengan input Anda:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('pemeriksaan_retain.update', $pemeriksaanRetain->uuid) }}" method="POST">
                @csrf
                @method('PUT') {{-- Method untuk update --}}

                {{-- CARD INFORMASI UMUM --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Inspeksi</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $pemeriksaanRetain->tanggal) }}" required>
                                @error('tanggal') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hari" class="form-label">Hari</label>
                                <input type="text" class="form-control @error('hari') is-invalid @enderror" id="hari" name="hari" value="{{ old('hari', $pemeriksaanRetain->hari) }}" required readonly>
                                @error('hari') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DETAIL ITEM (DINAMIS) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><i class="bi bi-list-nested"></i> Detail Pemeriksaan Retain</strong>
                            <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Item</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="details-container">
                            {{-- Item dinamis akan ditambahkan di sini oleh JS --}}
                        </div>
                    </div>
                </div>

                {{-- CARD KETERANGAN --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-chat-left-text"></i> Keterangan Tambahan</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $pemeriksaanRetain->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle"></i> Update Data</button>
                    {{-- Pastikan Anda memiliki route 'index' untuk kembali --}}
                    <a href="{{ route('pemeriksaan_retain.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- 1. Include jQuery (Select2 depends on it) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- 2. Include Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- LOGIKA UNTUK HARI & TANGGAL (Dari form asli Anda) ---
        const tanggalInput = document.getElementById('tanggal');
        const hariInput = document.getElementById('hari');

        function updateHari() {
            const tanggalValue = tanggalInput.value;
            if (tanggalValue) {
                const parts = tanggalValue.split('-');
                const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                const dayName = dateObj.toLocaleDateString('id-ID', {
                    weekday: 'long'
                });
                hariInput.value = dayName;
            } else {
                hariInput.value = '';
            }
        }
        tanggalInput.addEventListener('change', updateHari);
        updateHari(); // Panggil saat dimuat
        // --- AKHIR LOGIKA HARI & TANGGAL ---


        // --- LOGIKA FORM DINAMIS (Gaya "Packaging") ---
        const container = document.getElementById('details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0;

        /**
         * Fungsi untuk merender form detail item "Retain"
         * (Fungsi ini sama persis dengan yang ada di create.blade.php)
         */
        function renderDetailForm(data = null) {
            const i = detailIndex;

            // Siapkan nilai default atau dari 'old' data
            const kode_produksi = data?.kode_produksi || '';
            const exp_date = data?.exp_date || '';
            const varian = data?.varian || '';
            const panjang = data?.panjang || '';
            const diameter = data?.diameter || '';

            const rasa_val = data?.sensori_rasa || '';
            const warna_val = data?.sensori_warna || '';
            const aroma_val = data?.sensori_aroma || '';
            const texture_val = data?.sensori_texture || '';

            const jamur_val = data?.temuan_jamur || false;
            const lendir_val = data?.temuan_lendir || false;
            const pinehole_val = data?.temuan_pinehole || false;
            const kejepit_val = data?.temuan_kejepit || false;
            const seal_val = data?.temuan_seal || false;

            const garam_val = data?.lab_garam || '';
            const air_val = data?.lab_air || '';
            const mikro_val = data?.lab_mikro || '';

            const newDetail = document.createElement('div');
            newDetail.classList.add('dynamic-item-card', 'border', 'p-3', 'mb-3', 'rounded');

            newDetail.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Item #${i + 1}</h5>
                    <button type="button" class="btn btn-danger btn-sm remove-detail-btn"><i class="bi bi-trash"></i> Hapus</button>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Kode Batch</label>
                        <input type="text" name="items[${i}][kode_produksi]" id="kode_produksi" maxlength="10" class="form-control" value="${kode_produksi}">
                        <small id="kodeError" class="text-danger d-none"></small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Exp Date</label>
                        <input type="date" name="items[${i}][exp_date]" class="form-control" value="${exp_date}">
                    </div>
                  <div class="col-md-2">
                        <label class="form-label">Varian</label>
                       <select
                            name="items[${i}][varian]"
                            class="form-select select2-dynamic"
                            data-placeholder="Pilih varian...">

                            <option value=""></option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}"
                                    ${varian === '{{ $produk->nama_produk }}' ? 'selected' : ''}>
                                    {{ $produk->nama_produk }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Panjang (cm)</label>
                        <input type="number" step="0.01" name="items[${i}][panjang]" class="form-control" value="${panjang}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Diameter (cm)</label>
                        <input type="number" step="0.01" name="items[${i}][diameter]" class="form-control" value="${diameter}">
                    </div>
                </div>

                <hr>
                
                             <h6>Sensori</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Rasa</label>
                        <select name="items[${i}][sensori_rasa]" class="form-select select2-dynamic">
                            <option value="">Pilih...</option>
                            <option value="1" ${rasa_val == '1' ? 'selected' : ''}>1</option>
                            <option value="2" ${rasa_val == '2' ? 'selected' : ''}>2</option>
                            <option value="3" ${rasa_val == '3' ? 'selected' : ''}>3</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Warna</label>
                        <select name="items[${i}][sensori_warna]" class="form-select select2-dynamic">
                            <option value="">Pilih...</option>
                            <option value="1" ${warna_val == '1' ? 'selected' : ''}>1</option>
                            <option value="2" ${warna_val == '2' ? 'selected' : ''}>2</option>
                            <option value="3" ${warna_val == '3' ? 'selected' : ''}>3</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Aroma</label>
                        <select name="items[${i}][sensori_aroma]" class="form-select select2-dynamic">
                            <option value="">Pilih...</option>
                            <option value="1" ${aroma_val == '1' ? 'selected' : ''}>1</option>
                            <option value="2" ${aroma_val == '2' ? 'selected' : ''}>2</option>
                            <option value="3" ${aroma_val == '3' ? 'selected' : ''}>3</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Texture</label>
                        <select name="items[${i}][sensori_texture]" class="form-select select2-dynamic">
                            <option value="">Pilih...</option>
                            <option value="1" ${texture_val == '1' ? 'selected' : ''}>1</option>
                            <option value="2" ${texture_val == '2' ? 'selected' : ''}>2</option>
                            <option value="3" ${texture_val == '3' ? 'selected' : ''}>3</option>
                        </select>
                    </div>
                </div>

                <hr>

                <h6>Temuan</h6>
                <div class="row">
                    <div class="col temuan-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="items[${i}][temuan_jamur]" ${jamur_val ? 'checked' : ''} id="jamur_${i}">
                            <label class="form-check-label" for="jamur_${i}">Jamur</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="items[${i}][temuan_lendir]" ${lendir_val ? 'checked' : ''} id="lendir_${i}">
                            <label class="form-check-label" for="lendir_${i}">Lendir</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="items[${i}][temuan_pinehole]" ${pinehole_val ? 'checked' : ''} id="pinehole_${i}">
                            <label class="form-check-label" for="pinehole_${i}">Pinehole</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="items[${i}][temuan_kejepit]" ${kejepit_val ? 'checked' : ''} id="kejepit_${i}">
                            <label class="form-check-label" for="kejepit_${i}">Kejepit</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="items[${i}][temuan_seal]" ${seal_val ? 'checked' : ''} id="seal_${i}">
                            <label class="form-check-label" for="seal_${i}">Seal Halus/Lepas</label>
                        </div>
                    </div>
                </div>

                <hr>

                <h6>Parameter Lab</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kadar Garam (%)</label>
                        <input type="number" step="0.1" name="items[${i}][lab_garam]" class="form-control" value="${garam_val}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kadar Air (%)</label>
                        <input type="number" step="0.1" name="items[${i}][lab_air]" class="form-control" value="${air_val}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mikro (<10 cfu)</label>
                        <input type="number" step="0.1" name="items[${i}][lab_mikro]" class="form-control" value="${mikro_val}">
                    </div>
                </div>
            `;

            container.appendChild(newDetail);

            // Inisialisasi Select2 pada elemen yang baru ditambahkan
            // $(newDetail).find('.select2-dynamic').select2({
            //     theme: "bootstrap-5",
            //     placeholder: "Pilih...",
            //     allowClear: true,
            //     dropdownAutoWidth: true
            // });

            $(newDetail).find('.select2-dynamic').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih...",
                allowClear: true,
                width: '100%'
            });

            detailIndex++;
        }

        // --- Event Listener untuk Tombol "Tambah Item" ---
        addBtn.addEventListener('click', () => renderDetailForm(null));

        // --- Event Listener untuk Tombol Hapus ---
        container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-detail-btn');
            if (removeBtn) {
                if (container.querySelectorAll('.dynamic-item-card').length > 1) {
                    removeBtn.closest('.dynamic-item-card').remove();
                } else {
                    alert('Minimal harus ada 1 item pemeriksaan.');
                }
            }
        });

        // --- Render data 'old' atau data dari $pemeriksaanRetain saat halaman dimuat ---
        // *** INI PERBEDAAN UTAMA DARI CREATE ***
        const existingDetails = @json(old('items', $pemeriksaanRetain->items ?? []));

        if (existingDetails.length > 0) {
            existingDetails.forEach(itemData => {
                renderDetailForm(itemData);
            });
        } else {
            // Tambah satu form kosong jika tidak ada data sama sekali
            renderDetailForm(null);
        }

    });
</script>
<script>
    $(document).ready(function() {
        // Validasi kode produksi
        const kodeInput = $('#kode_produksi');
        const kodeError = $('#kodeError');
        const form = $('#retainForm');

        function validateKode() {
            let value = kodeInput.val().toUpperCase().replace(/\s+/g, '');
            kodeInput.val(value);
            kodeError.text('').addClass('d-none');

            if (value.length !== 10) {
                kodeError.text('Kode batch harus terdiri dari 10 karakter').removeClass('d-none');
                return false;
            }

            if (!/^[A-Z0-9]+$/.test(value)) {
                kodeError.text('Kode batch hanya boleh huruf besar dan angka').removeClass('d-none');
                return false;
            }

            if (!/^[A-L]$/.test(value.charAt(1))) {
                kodeError.text('Karakter ke-2 harus huruf bulan (A-L)').removeClass('d-none');
                return false;
            }

            let hari = parseInt(value.substr(2, 2), 10);
            if (isNaN(hari) || hari < 1 || hari > 31) {
                kodeError.text('Karakter ke-3 dan ke-4 harus tanggal valid (01-31)').removeClass('d-none');
                return false;
            }

            return true;
        }

        kodeInput.on('input', validateKode);

        form.on('submit', function(e) {
            if (!validateKode()) {
                e.preventDefault();
                alert('Kode batch tidak valid! Periksa kembali sebelum menyimpan.');
                kodeInput.focus();
            }
        });
    });
</script>
@endpush