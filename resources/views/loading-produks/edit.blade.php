@extends('layouts.app')

@section('title', 'Edit Pemeriksaan Loading')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    body {
        background-color: #f8f9fa;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-control, .form-select {
        border-radius: 8px;
    }

    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
    }

    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important;
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .dynamic-item-card {
        background-color: #fdfdfd;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            <h4 class="mb-1"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Loading</h4>
            <p class="text-muted mb-4">Perbarui detail formulir pemeriksaan loading di bawah ini.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Ada beberapa masalah dengan input Anda:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('loading-produks.update', $loadingProduk->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Utama & Kendaraan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="tanggal" class="form-label">Hari/Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                       value="{{ old('tanggal', $loadingProduk->tanggal) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="shift" class="form-label">Shift <span class="text-danger">*</span></label>

                                @php
                                    $shift = old('shift', $loadingProduk->shift);
                                @endphp

                                <select class="form-select select2-static" id="shift" name="shift" required>
                                    @if(in_array($shift, ['Pagi', 'Malam']))
                                        <option value="{{ $shift }}" selected>{{ $shift }}</option>
                                    @endif

                                    <option value="Shift 1" @selected($shift == 'Shift 1')>Shift 1</option>
                                    <option value="Shift 2" @selected($shift == 'Shift 2')>Shift 2</option>
                                    <option value="Shift 3" @selected($shift == 'Shift 3')>Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="jenis_aktivitas" class="form-label">Jenis Aktivitas <span class="text-danger">*</span></label>
                                <select class="form-select select2-static" id="jenis_aktivitas" name="jenis_aktivitas" required>
                                    <option value="Loading" @selected(old('jenis_aktivitas', $loadingProduk->jenis_aktivitas) == 'Loading')>Loading</option>
                                    <option value="Unloading" @selected(old('jenis_aktivitas', $loadingProduk->jenis_aktivitas) == 'Unloading')>Unloading</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="jam_mulai" name="jam_mulai"
                                       value="{{ old('jam_mulai', $loadingProduk->jam_mulai) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="jam_selesai" name="jam_selesai"
                                       value="{{ old('jam_selesai', $loadingProduk->jam_selesai) }}" required>
                            </div>

                            <div class="col-12"><hr class="my-2"></div>

                            <div class="col-md-4">
                                <label for="no_pol_mobil" class="form-label">No. Pol Mobil <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="no_pol_mobil" name="no_pol_mobil"
                                       value="{{ old('no_pol_mobil', $loadingProduk->no_pol_mobil) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="nama_supir" class="form-label">Nama Supir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_supir" name="nama_supir"
                                       value="{{ old('nama_supir', $loadingProduk->nama_supir) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="ekspedisi" class="form-label">Ekspedisi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ekspedisi" name="ekspedisi"
                                       value="{{ old('ekspedisi', $loadingProduk->ekspedisi) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="tujuan_asal" class="form-label">Tujuan / Asal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tujuan_asal" name="tujuan_asal"
                                       value="{{ old('tujuan_asal', $loadingProduk->tujuan_asal) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="no_segel" class="form-label">No. Segel</label>
                                <input type="text" class="form-control" id="no_segel" name="no_segel"
                                       value="{{ old('no_segel', $loadingProduk->no_segel) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                                <input type="text" class="form-control" id="jenis_kendaraan" name="jenis_kendaraan"
                                       value="{{ old('jenis_kendaraan', $loadingProduk->jenis_kendaraan) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-clipboard2-check"></i> Kondisi Mobil & Keterangan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label mb-2">Kondisi Mobil (Checklist)</label>
                                <div class="card p-3">
                                    <div class="row">
                                        @php
                                            $kondisiList = [
                                                'bersih' => 'Bersih',
                                                'kering' => 'Kering',
                                                'tidak_bocor' => 'Tidak Bocor',
                                                'tidak_debu' => 'Tidak Berdebu',
                                                'tidak_basah' => 'Tidak Basah',
                                                'bebas_hama' => 'Bebas Hama',
                                                'bebas_noda' => 'Bebas Noda (Karat, cat, tinta)',
                                                'bebas_oli' => 'Bebas Bekas oli di lantai/dinding',
                                                'tidak_ada_non_halal' => 'Tidak ada produk non halal',
                                            ];
                                            $currentKondisi = old('kondisi_mobil', $loadingProduk->kondisi_mobil ?? []);
                                        @endphp

                                        @foreach ($kondisiList as $key => $label)
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="kondisi_mobil[]" value="{{ $key }}" id="kondisi_{{ $key }}"
                                                           @checked(in_array($key, $currentKondisi))>
                                                    <label class="form-check-label" for="kondisi_{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="keterangan_total" class="form-label">Keterangan Total (Varian & Jumlah)</label>
                                    <textarea class="form-control" id="keterangan_total" name="keterangan_total" rows="2">{{ old('keterangan_total', $loadingProduk->keterangan_total) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan_umum" class="form-label">Keterangan Umum (Catatan)</label>
                                    <textarea class="form-control" id="keterangan_umum" name="keterangan_umum" rows="2">{{ old('keterangan_umum', $loadingProduk->keterangan_umum) }}</textarea>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="pic_qc" class="form-label">PIC QC</label>
                                        <input type="text" class="form-control" id="pic_qc" name="pic_qc" value="{{ old('pic_qc', $user->name) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pic_warehouse" class="form-label">PIC Warehouse</label>
                                        <input type="text" class="form-control" id="pic_warehouse" name="pic_warehouse" value="{{ old('pic_warehouse', $loadingProduk->pic_warehouse) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pic_qc_spv" class="form-label">PIC QC SPV</label>
                                        <input type="text" class="form-control" id="pic_qc_spv" name="pic_qc_spv" value="{{ old('pic_qc_spv', $loadingProduk->pic_qc_spv) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><i class="bi bi-list-nested"></i> Detail Item Produk <span class="text-danger">*</span></strong>
                            <button type="button" id="add-detail-btn" class="btn btn-secondary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Item</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="details-container"></div>
                        <div class="card border-secondary mt-3">
                            <div class="card-header bg-success text-white">
                                <strong>Jumlah Total Item Produk</strong><br>
                                <small><strong>*Total akan dihitung berdasarkan Varian dan Satuan</strong></small>
                            </div>
                            <div class="card-body">
                                <h5 class="mb-0 text-start" id="total-item-display">Data Kosong! tidak bisa disubmit</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-warning btn-lg"><i class="bi bi-check-circle"></i> Update Data</button>
                    <a href="{{ route('loading-produks.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Batal</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2-static').select2({
            theme: "bootstrap-5",
            placeholder: "Pilih...",
            allowClear: false,
            dropdownAutoWidth: true
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('details-container');
        const addBtn = document.getElementById('add-detail-btn');
        let detailIndex = 0;

        function updateTotalItem() {
            const totals = {};

            container.querySelectorAll('.dynamic-item-card').forEach(card => {
                const produk = card.querySelector('select[name$="[nama_produk]"]')?.value;
                const jumlah = parseFloat(card.querySelector('input[name$="[jumlah]"]')?.value) || 0;
                const satuan = card.querySelector('select[name$="[satuan]"]')?.value || '';

                if (!produk) return;

                const key = `${produk}|${satuan}`;

                if (!totals[key]) {
                    totals[key] = {
                        produk: produk,
                        jumlah: 0,
                        satuan: satuan
                    };
                }

                totals[key].jumlah += jumlah;
            });

            const display = document.getElementById('total-item-display');

            if (Object.keys(totals).length === 0) {
                display.textContent = 'Belum ada data';
                return;
            }

            display.style.textAlign = 'left';
            display.style.display = 'block';

            display.innerHTML = Object.values(totals)
                .map(data =>
                    `<div class="badge bg-secondary text-white d-inline-block mb-2">• ${data.produk} [ ${data.jumlah} ${data.satuan} ]</div>`
                )
                .join('<br>');
        }

        function hitungExpired(kode) {
            kode = (kode || '').toUpperCase();

            const tahunKode = {
                'O': 2024, 'P': 2025, 'Q': 2026, 'R': 2027, 'S': 2028,
                'T': 2029, 'U': 2030, 'V': 2031, 'W': 2032, 'X': 2033,
                'Y': 2034, 'Z': 2035
            };

            const bulanKode = {
                'A': 1, 'B': 2, 'C': 3, 'D': 4, 'E': 5, 'F': 6,
                'G': 7, 'H': 8, 'I': 9, 'J': 10, 'K': 11, 'L': 12
            };

            const format = kode.substring(0, 4);

            if (!/^[A-Z]{2}\d{2}$/.test(format)) return null;

            const tahun = tahunKode[format[0]];
            const bulan = bulanKode[format[1]];
            const hari = parseInt(format.substring(2, 4));

            if (!tahun || !bulan || !hari) return null;

            let date = new Date(tahun, bulan - 1, hari);
            date.setMonth(date.getMonth() + 7);

            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        }

        function reindexDetails() {
            const cards = container.querySelectorAll('.dynamic-item-card');

            cards.forEach((card, index) => {
                card.querySelector('h5').textContent = `Item Produk #${index + 1}`;

                card.querySelectorAll('[name]').forEach(field => {
                    field.name = field.name.replace(/details\[\d+\]/, `details[${index}]`);
                });
            });

            detailIndex = cards.length;
        }

        function setBatchExpired(kode, expiredInput, warning) {
            const expired = hitungExpired(kode);

            if (expired) {
                expiredInput.val(expired);
                warning.addClass('d-none');
            } else {
                expiredInput.val('');
                warning.removeClass('d-none');
            }
        }

        function renderDetailForm(data = null) {
            const i = detailIndex;
            const uuid = data?.uuid || '';
            const nama_produk = data?.nama_produk || '';
            const kode_produksi = data?.kode_produksi || '';
            const kode_expired = data?.kode_expired || '';
            const jumlah = data?.jumlah || '';
            const keterangan = data?.keterangan || '';
            const satuan = data?.satuan || '';

            const newDetail = document.createElement('div');
            newDetail.classList.add('dynamic-item-card', 'border', 'p-3', 'mb-3', 'rounded');

            newDetail.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Item Produk #${i + 1}</h5>
                    <button type="button" class="btn btn-danger btn-sm remove-detail-btn"><i class="bi bi-trash"></i> Hapus</button>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Nama Produk (Varian) <span class="text-danger">*</span></label>
                        <select name="details[${i}][nama_produk]" class="form-control var-produk-select" required>
                            <option value="">-- Pilih Varian --</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}" ${nama_produk === '{{ $produk->nama_produk }}' ? 'selected' : ''}>
                                    {{ $produk->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Kode Batch <span class="text-danger">*</span></label>

                        <div class="existing-batch-mode">
                            <select name="details[${i}][kode_produksi]" class="form-control var-batch-select" required>
                                <option value="">Pilih Varian Terlebih Dahulu</option>
                            </select>

                            <button type="button" class="btn btn-primary btn-sm mt-1 switch-to-manual">
                                Input Kode Batch Manual
                            </button>
                        </div>

                        <div class="manual-batch-mode d-none">
                            <input type="text"
                                name="details[${i}][kode_produksi]"
                                class="form-control var-batch-manual"
                                placeholder="Ketik Kode Batch"
                                disabled
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')">

                            <button type="button" class="btn btn-info btn-sm mt-1 switch-to-existing text-white">
                                Input Kode Batch Existing
                            </button>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Kode Expired</label>
                        <input type="date" name="details[${i}][kode_expired]" class="form-control expired-date" value="${kode_expired}" required>
                        <small class="text-primary exp-warning d-none">Sesuaikan kode produksi manual</small>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="details[${i}][jumlah]" class="form-control" value="${jumlah}" min="1" required>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Satuan <span class="text-danger">*</span></label>
                        <select name="details[${i}][satuan]" class="form-control" required>
                            <option value="">--Pilih--</option>
                            <option value="kg" ${satuan === 'kg' ? 'selected' : ''}>Kg</option>
                            <option value="pcs" ${satuan === 'pcs' ? 'selected' : ''}>Pcs</option>
                            <option value="roll" ${satuan === 'roll' ? 'selected' : ''}>Roll</option>
                            <option value="box" ${satuan === 'box' ? 'selected' : ''}>Box</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="details[${i}][keterangan]" class="form-control" value="${keterangan}">
                    </div>
                </div>
            `;

            if (uuid) {
                newDetail.innerHTML += `
                    <input type="hidden" name="details[${i}][uuid]" value="${uuid}">
                `;
            }

            container.appendChild(newDetail);

            const produkSelect = $(newDetail).find('.var-produk-select');
            const batchSelect = $(newDetail).find('.var-batch-select');
            const batchManual = $(newDetail).find('.var-batch-manual');
            const expiredInput = $(newDetail).find('.expired-date');
            const warning = $(newDetail).find('.exp-warning');
            const existingMode = $(newDetail).find('.existing-batch-mode');
            const manualMode = $(newDetail).find('.manual-batch-mode');

            produkSelect.select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: "-- Pilih Varian --",
                allowClear: true
            });

            batchSelect.select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: "-- Pilih Kode Batch --",
                allowClear: true,
                ajax: {
                    url: function() {
                        const produkValue = produkSelect.val();

                        if (!produkValue) return '';

                        return "{{ url('/lookup/batch-packing') }}/" + encodeURIComponent(produkValue);
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return { results: data };
                    },
                    cache: true
                }
            });

            function setExistingMode() {
                batchManual.val('');
                batchManual.prop('disabled', true);
                batchManual.prop('required', false);

                existingMode.removeClass('d-none');
                manualMode.addClass('d-none');

                batchSelect.prop('disabled', !produkSelect.val());
                batchSelect.prop('required', true);
            }

            function setManualMode(value = '') {
                batchSelect.val(null).trigger('change');
                batchSelect.prop('disabled', true);
                batchSelect.prop('required', false);

                batchManual.val(value);
                batchManual.prop('disabled', false);
                batchManual.prop('required', true);

                existingMode.addClass('d-none');
                manualMode.removeClass('d-none');
            }

            newDetail.querySelector('.switch-to-manual').addEventListener('click', function() {
                setManualMode();
                updateTotalItem();
            });

            newDetail.querySelector('.switch-to-existing').addEventListener('click', function() {
                setExistingMode();
                updateTotalItem();
            });

            setExistingMode();

            if (kode_produksi) {
                const isUuid = /^[0-9a-f-]{36}$/i.test(kode_produksi);

                if (isUuid) {
                    $.get("{{ url('/lookup/batch-packing-by-uuid') }}/" + kode_produksi, function(res) {
                        const newOption = new Option(res.text, res.id, true, true);
                        batchSelect.append(newOption).trigger('change');
                    }).fail(function() {
                        setManualMode(kode_produksi);
                    });
                } else {
                    setManualMode(kode_produksi);
                }
            }

            produkSelect.on('change', function() {
                const currentVal = $(this).val();

                if (!manualMode.hasClass('d-none')) {
                    batchManual.val('');
                    updateTotalItem();
                    return;
                }

                batchSelect.val(null).trigger('change');
                batchSelect.prop('disabled', !currentVal);

                updateTotalItem();
            });

            batchSelect.on('select2:select', function() {
                const kode = $(this).find(':selected').text();
                setBatchExpired(kode, expiredInput, warning);
            });

            batchManual.on('input', function() {
                setBatchExpired($(this).val(), expiredInput, warning);
            });

            newDetail.querySelector('input[name$="[jumlah]"]').addEventListener('input', updateTotalItem);
            newDetail.querySelector('select[name$="[satuan]"]').addEventListener('change', updateTotalItem);

            detailIndex++;
        }

        if (addBtn) {
            addBtn.addEventListener('click', () => {
                const firstProduk = $('.var-produk-select').first().val();

                renderDetailForm({
                    nama_produk: firstProduk || ''
                });

                reindexDetails();
                updateTotalItem();
            });
        }

        if (container) {
            container.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-detail-btn');

                if (removeBtn) {
                    removeBtn.closest('.dynamic-item-card').remove();
                    reindexDetails();
                    updateTotalItem();
                }
            });
        }

        const existingDetails = @json(old('details', $loadingProduk->details ?? []));

        if (existingDetails.length > 0) {
            existingDetails.forEach(itemData => {
                renderDetailForm(itemData);
            });
        } else {
            renderDetailForm(null);
        }

        reindexDetails();
        updateTotalItem();
    });
</script>
@endpush