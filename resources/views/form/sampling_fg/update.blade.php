@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pemeriksaan Proses Sampling Finish Good
            </h4>

            <form id="samplingForm" action="{{ route('sampling_fg.update_qc', $sampling_fg->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            {{-- TANGGAL --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                value="{{ old('date', $sampling_fg->date) }}"
                                {{ $sampling_fg->date ? 'readonly' : '' }} required>

                                @if($sampling_fg->date)
                                <input type="hidden" name="date" value="{{ $sampling_fg->date }}">
                                @endif
                            </div>

                            {{-- SHIFT --}}
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control"
                                {{ $sampling_fg->shift ? 'disabled' : '' }} required>
                                <option value="">-- Pilih Shift --</option>
                                <option value="1" {{ $sampling_fg->shift == '1' ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ $sampling_fg->shift == '2' ? 'selected' : '' }}>Shift 2</option>
                                <option value="3" {{ $sampling_fg->shift == '3' ? 'selected' : '' }}>Shift 3</option>
                            </select>

                            @if($sampling_fg->shift)
                            <input type="hidden" name="shift" value="{{ $sampling_fg->shift }}">
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                     <div class="col-md-6">
                        <label class="form-label">Palet</label>
                        <input type="number" name="palet" id="palet" class="form-control"
                        value="{{ old('palet', $sampling_fg->palet) }}"
                        {{ $sampling_fg->palet ? 'readonly' : '' }}>
                    </div>

                    {{-- NAMA PRODUK --}}
                    <div class="col-md-6">
                        <label class="form-label">Nama Varian</label>
                        <select name="nama_produk" class="form-control selectpicker"
                        data-live-search="true" {{ $sampling_fg->nama_produk ? 'disabled' : '' }} required>
                        <option value="">-- Pilih Varian --</option>
                        @foreach($produks as $produk)
                        <option value="{{ $produk->nama_produk }}"
                            {{ $sampling_fg->nama_produk == $produk->nama_produk ? 'selected' : '' }}>
                            {{ $produk->nama_produk }}
                        </option>
                        @endforeach
                    </select>

                    @if($sampling_fg->nama_produk)
                    <input type="hidden" name="nama_produk" value="{{ $sampling_fg->nama_produk }}">
                    @endif
                </div>

            </div>
            <div class="row mb-3">
               <div class="col-md-6">
                <label class="form-label">Kode Batch</label>
                <input type="text" name="kode_produksi" id="kode_produksi"
                class="form-control" maxlength="10"
                value="{{ old('kode_produksi', $sampling_fg->kode_produksi) }}"
                {{ $sampling_fg->kode_produksi ? 'readonly' : '' }} required>
                <small id="kodeError" class="text-danger d-none"></small>

                @if($sampling_fg->kode_produksi)
                <input type="hidden" name="kode_produksi" value="{{ $sampling_fg->kode_produksi }}">
                @endif
            </div>

            <div class="col-md-6">
                <label class="form-label">Exp. Date</label>
                <input type="date" name="exp_date" id="exp_date" class="form-control"
                value="{{ old('exp_date', $sampling_fg->exp_date) }}"
                {{ $sampling_fg->exp_date ? 'readonly' : '' }}>
                <small class="text-muted">Tanggal ini dihitung otomatis 7 bulan dari kode batch</small>
            </div>
        </div>
    </div>
</div>

{{-- ===================== PEMERIKSAAN PROSES CARTONING ===================== --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <strong>Pemeriksaan Proses Cartoning</strong>
    </div>
    <div class="card-body p-3">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Waktu</label>
                <input type="time" name="pukul" id="timeInput" class="form-control"
                value="{{ old('pukul', $sampling_fg->pukul) }}"
                {{ $sampling_fg->pukul ? 'readonly' : '' }}>
            </div>

            <div class="col-md-6">
                <label class="form-label d-block">Kalibrasi</label>
                @php
                $sudahAda = !empty($sampling_fg->kalibrasi);
                @endphp
                <select class="form-control"
                id="kalibrasi"
                name="kalibrasi"
                {{ $sudahAda ? 'disabled' : '' }}>
                <option value="Sesuai" {{ $sampling_fg->kalibrasi == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
                <option value="Tidak Sesuai" {{ $sampling_fg->kalibrasi == 'Tidak Sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
            </select>
            @if($sudahAda)
            <input type="hidden" name="kalibrasi" value="{{ $sampling_fg->kalibrasi }}">
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Berat Varian per Box (gr)</label>
            <input type="number" name="berat_produk" id="berat_produk" class="form-control"
            value="{{ old('berat_produk', $sampling_fg->berat_produk) }}"
            {{ $sampling_fg->berat_produk ? 'readonly' : '' }}>
        </div>
        <div class="col-md-6">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" class="form-control"
            value="{{ old('keterangan', $sampling_fg->keterangan) }}"
            {{ $sampling_fg->keterangan ? 'readonly' : '' }}>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label class="form-label">Isi Varian per Box</label>
            <input type="number" name="isi_per_box" id="isi_per_box" class="form-control"
            value="{{ old('isi_per_box', $sampling_fg->isi_per_box) }}"
            {{ $sampling_fg->isi_per_box ? 'readonly' : '' }}>
        </div>
        <div class="col-md-3">
            <label class="form-label">Kemasan</label>
            <select name="kemasan" id="kemasan" class="form-control selectpicker"
            {{ $sampling_fg->kemasan ? 'disabled' : '' }}>
            <option value="">--Pilih--</option>
            <option value="Jar" {{ $sampling_fg->kemasan == 'Jar' ? 'selected' : '' }}>Jar</option>
            <option value="Pouch" {{ $sampling_fg->kemasan == 'Pouch' ? 'selected' : '' }}>Pouch</option>
        </select>

        @if($sampling_fg->kemasan)
        <input type="hidden" name="kemasan" value="{{ $sampling_fg->kemasan }}">
        @endif
    </div>

    <div class="col-md-6">
        <label class="form-label">Jumlah (Box)</label>
        <input type="number" name="jumlah_box" id="jumlah_box" class="form-control" value="{{ $sampling_fg->jumlah_box }}" readonly>
    </div>
</div>

<hr>

<label class="form-label"><b>Status Varian</b></label>
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Release (Box)</label>
        <input type="number" name="release" id="release" class="form-control"
        value="{{ old('release', $sampling_fg->release) }}"
        {{ $sampling_fg->release ? 'readonly' : '' }}>
    </div>
    <div class="col-md-4">
        <label class="form-label">Reject (Box)</label>
        <input type="number" name="reject" id="reject" class="form-control"
        value="{{ old('reject', $sampling_fg->reject) }}"
        {{ $sampling_fg->reject ? 'readonly' : '' }}>
    </div>
    <div class="col-md-4">
        <label class="form-label">Hold (Box)</label>
        <input type="number" name="hold" id="hold" class="form-control"
        value="{{ old('hold', $sampling_fg->hold) }}"
        {{ $sampling_fg->hold ? 'readonly' : '' }}>
    </div>
</div>
</div>
</div>

{{-- ===================== CATATAN ===================== --}}

<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <strong>Koordinator</strong>
    </div>
    <div class="card-body">
        <div class="col-md-6">
            <label class="form-label">Nama KR</label>
            <select id="nama_koordinator" name="nama_koordinator"
            class="form-control selectpicker"
            data-live-search="true" required>
            <option value="">-- Pilih Koordinator --</option>
            @foreach($koordinators as $koordinator)
            <option value="{{ $koordinator->nama_karyawan }}"
                {{ $sampling_fg->nama_koordinator == $koordinator->nama_karyawan ? 'selected' : '' }}>
                {{ $koordinator->nama_karyawan }}
            </option>
            @endforeach
        </select>
    </div>
</div>
</div>

<div class="card mb-4">
    <div class="card-header bg-light"><strong>Item Mutu</strong></div>
    <div class="card-body">
        <textarea name="item_mutu" class="form-control" rows="3" placeholder="Tambahkan Item Mutu bila ada">{{ old('item_mutu', $sampling_fg->item_mutu) }}</textarea>
    </div>
</div>
<div class="card mb-4">
    <div class="card-header bg-light"><strong>Catatan</strong></div>
    <div class="card-body">
        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $sampling_fg->catatan) }}</textarea>
    </div>
</div>

{{-- ===================== TOMBOL ===================== --}}
<div class="d-flex justify-content-between mt-3">
    <button type="submit" class="btn btn-success">
        <i class="bi bi-save"></i> Update
    </button>
    <a href="{{ route('sampling_fg.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
</form>
</div>
</div>
</div>

{{-- ===================== SCRIPT ===================== --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();
    });

    const kodeInput = document.getElementById('kode_produksi');
    const expDateInput = document.getElementById('exp_date');
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

    // Kalibrasi checkbox: sesuai/tidak sesuai
    const kalibrasi = document.getElementById('kalibrasi');
    const kalibrasiLabel = document.getElementById('kalibrasiLabel');

    kalibrasi.addEventListener('change', function () {
        if (this.checked) {
            kalibrasiLabel.textContent = 'Sesuai';
            this.value = 'Sesuai';
        } else {
            kalibrasiLabel.textContent = 'Tidak Sesuai';
            this.value = 'Tidak Sesuai';
        }
    });

    // Set nilai awal kalibrasi saat edit
    kalibrasi.value = kalibrasi.checked ? 'Sesuai' : 'Tidak Sesuai';

    <script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();

    // === FUNGSI AUTO AMBIL JUMLAH BOX DARI RELEASE PACKING ===
        function loadJumlahBox() {
            const nama_produk = $('select[name="nama_produk"]').val();
            const kode_produksi = $('#kode_produksi').val();

            if (nama_produk && kode_produksi.length > 0) {
                $.ajax({
                    url: "{{ route('get.jumlah.box') }}",
                    method: 'GET',
                    data: { nama_produk: nama_produk, kode_produksi: kode_produksi },
                    beforeSend: function() {
                        $('#jumlah_box').val('...');
                    },
                    success: function (response) {
                        $('#jumlah_box').val(response.total_box || 0);
                    },
                    error: function () {
                        $('#jumlah_box').val(0);
                    }
                });
            }
        }

    // Trigger ambil jumlah_box ketika nama_produk atau kode_produksi berubah
        $('select[name="nama_produk"]').on('change', loadJumlahBox);
        $('#kode_produksi').on('keyup change', loadJumlahBox);

    // Jalankan sekali saat halaman pertama kali dibuka (untuk edit)
        loadJumlahBox();
    });
</script>

</script>
@endpush
@endsection
