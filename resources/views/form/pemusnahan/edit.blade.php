@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Edit Pemusnahan Barang
            </h4>

            <form id="pemusnahanForm" action="{{ route('pemusnahan.edit_spv', $pemusnahan->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date"
                                    name="date"
                                    id="dateInput"
                                    class="form-control"
                                    value="{{ old('date', $pemusnahan->date) }}"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk"
                                    id="nama_produk"
                                    class="form-control selectpicker"
                                    data-live-search="true"
                                    required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->nama_produk }}"
                                            {{ old('nama_produk', $pemusnahan->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                            {{ $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>

                                <select name="kode_produksi"
                                    id="kode_produksi"
                                    class="form-control batchSelect"
                                    required
                                    disabled>
                                    <option value="">Pilih Varian Terlebih Dahulu</option>
                                </select>

                                <small id="kodeError" class="text-danger d-none"></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Exp. Date</label>
                                <input type="date"
                                    name="expired_date"
                                    id="expired_date"
                                    class="form-control"
                                    value="{{ old('expired_date', $pemusnahan->expired_date) }}">

                                <small class="text-muted">
                                    Tanggal ini dihitung otomatis 7 bulan dari kode batch
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Analisa Masalah</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="analisa"
                            class="form-control"
                            rows="3"
                            placeholder="Tambahkan analisa bila ada">{{ old('analisa', $pemusnahan->analisa) }}</textarea>
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
                            placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $pemusnahan->keterangan) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update
                    </button>

                    <a href="{{ route('pemusnahan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker();

        const produkSelect = $('#nama_produk');
        const batchSelect = $('#kode_produksi');
        const expiredInput = $('#expired_date');
        const kodeError = $('#kodeError');

        const currentBatchUuid = @json(old('kode_produksi', $pemusnahan->kode_produksi));
        const currentExpiredDate = @json(old('expired_date', $pemusnahan->expired_date));

        function hitungExpired(kode) {
            kode = kode.toUpperCase();

            const tahunKode = {
                'O': 2024,
                'P': 2025,
                'Q': 2026,
                'R': 2027,
                'S': 2028,
                'T': 2029,
                'U': 2030,
                'V': 2031,
                'W': 2032,
                'X': 2033,
                'Y': 2034,
                'Z': 2035
            };

            const bulanKode = {
                'A': 1,
                'B': 2,
                'C': 3,
                'D': 4,
                'E': 5,
                'F': 6,
                'G': 7,
                'H': 8,
                'I': 9,
                'J': 10,
                'K': 11,
                'L': 12
            };

            const format = kode.substring(0, 4);

            if (!/^[A-Z]{2}\d{2}$/.test(format)) {
                return null;
            }

            const tahun = tahunKode[format[0]];
            const bulan = bulanKode[format[1]];
            const hari = parseInt(format.substring(2, 4));

            if (!tahun || !bulan || !hari) {
                return null;
            }

            let date = new Date(tahun, bulan - 1, hari);

            date.setMonth(date.getMonth() + 7);

            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        }

        function loadBatch(selectedUuid = null) {
            const produk = produkSelect.val();

            batchSelect.html('');
            batchSelect.prop('disabled', true);
            expiredInput.val('');

            if (!produk) {
                batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>');
                return;
            }

            fetch("{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}"
                    .replace('__PRODUK__', encodeURIComponent(produk)))
                .then(res => res.json())
                .then(data => {
                    batchSelect.html('<option value="">-- Pilih Batch --</option>');

                    if (data.length === 0) {
                        batchSelect.html('<option value="">Batch Tidak Ditemukan</option>');
                        return;
                    }

                    data.forEach(batch => {
                        const selected = selectedUuid && String(batch.uuid) === String(selectedUuid)
                            ? 'selected'
                            : '';

                        batchSelect.append(`
                            <option value="${batch.uuid}" data-kode="${batch.kode_produksi}" ${selected}>
                                ${batch.kode_produksi}
                            </option>
                        `);
                    });

                    batchSelect.prop('disabled', false);

                    if (selectedUuid) {
                        const selectedOption = batchSelect.find(':selected');
                        const kode = selectedOption.data('kode');

                        const expired = hitungExpired(kode);

                        if (expired) {
                            expiredInput.val(expired);
                        } else {
                            expiredInput.val(currentExpiredDate || '');
                        }
                    }
                })
                .catch(() => {
                    batchSelect.html('<option value="">Gagal Memuat Batch</option>');
                });
        }

        produkSelect.on('change', function() {
            loadBatch();
        });

        batchSelect.on('change', function() {
            const selectedOption = $(this).find(':selected');
            const kode = selectedOption.data('kode');

            kodeError.text('');
            kodeError.addClass('d-none');

            if (!kode) {
                expiredInput.val('');
                return;
            }

            const expired = hitungExpired(kode);

            if (expired) {
                expiredInput.val(expired);
            } else {
                expiredInput.val('');

                kodeError.text('Kode batch tidak memiliki format yang valid.');
                kodeError.removeClass('d-none');
            }
        });

        if (produkSelect.val()) {
            loadBatch(currentBatchUuid);
        }
    });
</script>
@endpush
@endsection