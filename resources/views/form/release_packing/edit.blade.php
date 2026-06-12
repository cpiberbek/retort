@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Edit Release Packing
            </h4>

            <form id="releasepackingForm" action="{{ route('release_packing.edit_spv', $release_packing->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- IDENTITAS --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Release Packing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                value="{{ old('date', $release_packing->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kemasan</label>
                                <select name="jenis_kemasan" id="jenis_kemasan" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Kemasan --</option>
                                    <option value="Pouch" {{ old('jenis_kemasan', $release_packing->jenis_kemasan) == 'Pouch' ? 'selected' : '' }}>Pouch</option>
                                    <option value="Toples" {{ old('jenis_kemasan', $release_packing->jenis_kemasan) == 'Toples' ? 'selected' : '' }}>Toples</option>
                                    <option value="Box" {{ old('jenis_kemasan', $release_packing->jenis_kemasan) == 'Box' ? 'selected' : '' }}>Box</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $release_packing->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <input type="text" name="kode_produksi" id="kode_produksi" class="form-control"
                                value="{{ old('kode_produksi', $release_packing->mincing->kode_produksi) }}" maxlength="10" required>
                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="expired_date" id="expired_date" class="form-control"
                                value="{{ old('expired_date', $release_packing->expired_date) }}">
                                <small class="text-muted">Tanggal ini dihitung otomatis 7 bulan dari kode batch</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Palet</label>
                                <input type="text" name="no_palet" id="no_palet" class="form-control"
                                value="{{ old('no_palet', $release_packing->no_palet) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PEMERIKSAAN --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><strong>Jumlah Release</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Release</label>
                                <input type="number" name="release" id="release" class="form-control"
                                value="{{ old('release', $release_packing->release) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KETERANGAN --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $release_packing->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('release_packing.verification') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();
    });

    // Validasi kode produksi dan generate Exp Date otomatis
    const kodeInput = document.getElementById('kode_produksi');
    const expDateInput = document.getElementById('expired_date');
    const kodeError = document.getElementById('kodeError');

    kodeInput.addEventListener('input', function () {
        let value = this.value.toUpperCase().replace(/\s+/g, '');
        this.value = value;
        kodeError.textContent = '';
        kodeError.classList.add('d-none');
        expDateInput.value = '';

        if (value.length !== 10) {
            kodeError.textContent = "Kode batch harus terdiri dari 10 karakter.";
            kodeError.classList.remove('d-none');
            return;
        }

        const format = /^[A-Z0-9]+$/;
        if (!format.test(value)) {
            kodeError.textContent = "Kode batch hanya boleh huruf besar dan angka.";
            kodeError.classList.remove('d-none');
            return;
        }

        const bulanChar = value.charAt(1);
        const validBulan = /^[A-L]$/;
        if (!validBulan.test(bulanChar)) {
            kodeError.textContent = "Karakter ke-2 harus huruf bulan (A–L).";
            kodeError.classList.remove('d-none');
            return;
        }

        const hariStr = value.substr(2, 2);
        const hari = parseInt(hariStr, 10);
        if (isNaN(hari) || hari < 1 || hari > 31) {
            kodeError.textContent = "Karakter ke-3 dan ke-4 harus tanggal valid (01–31).";
            kodeError.classList.remove('d-none');
            return;
        }

        const bulanMap = { A: 0, B: 1, C: 2, D: 3, E: 4, F: 5, G: 6, H: 7, I: 8, J: 9, K: 10, L: 11 };
        const bulanIndex = bulanMap[bulanChar];
        const tahun = new Date().getFullYear();

        let expDate = new Date(tahun, bulanIndex, hari);
        expDate.setMonth(expDate.getMonth() + 7);

        const yyyy = expDate.getFullYear();
        const mm = String(expDate.getMonth() + 1).padStart(2, '0');
        const dd = String(expDate.getDate()).padStart(2, '0');
        expDateInput.value = `${yyyy}-${mm}-${dd}`;
    });
</script>
@endpush
@endsection
