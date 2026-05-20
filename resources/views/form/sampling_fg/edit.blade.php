@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pemeriksaan Proses Sampling Finish Good
            </h4>
            <form id="samplingForm" action="{{ route('sampling_fg.edit_spv', $sampling_fg->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-exclamation-triangle"></i> Terjadi Kesalahan!</strong>
                        Terdapat isian yang tidak valid. Silakan periksa kembali form di bawah.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" value="{{ old('date', $sampling_fg->date) }}" class="form-control @error('date') is-invalid @enderror" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control @error('shift') is-invalid @enderror" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift', $sampling_fg->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $sampling_fg->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $sampling_fg->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker @error('nama_produk') is-invalid @enderror" data-live-search="true" required>
                                    <option value="">-- Pilih Varian --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $sampling_fg->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <select name="kode_produksi" id="kode_produksi" class="form-control @error('kode_produksi') is-invalid @enderror" required>
                                    @if($sampling_fg->kode_produksi)
                                        <option value="{{ $sampling_fg->kode_produksi }}" selected>Sedang memuat batch...</option>
                                    @else
                                        <option value="">Pilih Varian Terlebih Dahulu</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Palet</label>
                                <select name="palet" id="palet" class="form-control @error('palet') is-invalid @enderror" required>
                                    @if($sampling_fg->palet)
                                        <option value="{{ $sampling_fg->palet }}" selected>Sedang memuat palet...</option>
                                    @else
                                        <option value="">Pilih Batch Terlebih Dahulu</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="exp_date" id="exp_date" value="{{ old('exp_date', $sampling_fg->exp_date) }}" class="form-control">
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
                                <input type="time" id="timeInput" name="pukul" class="form-control" value="{{ old('pukul', $sampling_fg->pukul) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">Kalibrasi</label>
                                <select class="form-control" id="kalibrasi" name="kalibrasi">
                                    <option value="Sesuai" {{ old('kalibrasi', $sampling_fg->kalibrasi) == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
                                    <option value="Tidak Sesuai" {{ old('kalibrasi', $sampling_fg->kalibrasi) == 'Tidak Sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Berat Varian per Box (gr)</label>
                                <input type="number" name="berat_produk" id="berat_produk" class="form-control" value="{{ old('berat_produk', $sampling_fg->berat_produk) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" id="keterangan" class="form-control" value="{{ old('keterangan', $sampling_fg->keterangan) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Isi Varian per Box</label>
                                <input type="number" name="isi_per_box" id="isi_per_box" class="form-control" value="{{ old('isi_per_box', $sampling_fg->isi_per_box) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kemasan</label>
                                <select name="kemasan" id="kemasan" class="form-control selectpicker">
                                    <option value="">-- Pilih Jenis Kemasan --</option>
                                    <option value="Jar" {{ old('kemasan', $sampling_fg->kemasan) == 'Jar' ? 'selected' : '' }}>Jar</option>
                                    <option value="Pouch" {{ old('kemasan', $sampling_fg->kemasan) == 'Pouch' ? 'selected' : '' }}>Pouch</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah (Box)</label>
                                <input type="number" name="jumlah_box" id="jumlah_box" class="form-control" value="{{ old('jumlah_box', $sampling_fg->jumlah_box) }}" readonly>
                            </div>
                        </div>

                        <hr>
                        <label class="form-label"><b>Status Varian</b></label>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Release (Box)</label>
                                <input type="number" name="release" id="release" class="form-control" value="{{ old('release', $sampling_fg->release) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reject (Box)</label>
                                <input type="number" name="reject" id="reject" class="form-control" value="{{ old('reject', $sampling_fg->reject) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hold (Box)</label>
                                <input type="number" name="hold" id="hold" class="form-control" value="{{ old('hold', $sampling_fg->hold) }}">
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
                            <select id="nama_koordinator" name="nama_koordinator" class="form-control selectpicker" data-live-search="true" required>
                                <option value="">-- Pilih Koordinator --</option>
                                @foreach($koordinators as $koordinator)
                                <option value="{{ $koordinator->nama_karyawan }}" {{ old('nama_koordinator', $sampling_fg->nama_koordinator) == $koordinator->nama_karyawan ? 'selected' : '' }}>
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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();

        const namaProdukSelect = $('#nama_produk');
        const batchSelect = $('#kode_produksi');
        const paletSelect = $('#palet');
        const expDateInput = $('#exp_date');
        const jumlahBoxInput = $('#jumlah_box');

        // Fungsi Load Batches
        function loadBatches(namaProduk, oldBatch = '') {
            if (!batchSelect.is('select')) return;

            if (!namaProduk) {
                batchSelect.html('<option value="">Pilih Varian Terlebih Dahulu</option>').prop('disabled', true);
                expDateInput.val('');
                return;
            }

            batchSelect.prop('disabled', false);
            if(batchSelect.find('option').length <= 1) {
                batchSelect.html('<option value="">Mencari Batch...</option>');
            }

            let url = "{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}";
            url = url.replace('__PRODUK__', encodeURIComponent(namaProduk));

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    batchSelect.html('<option value="">-- Pilih Batch --</option>');
                    if (!data || data.length === 0) {
                        batchSelect.html('<option value="">Batch Tidak Ditemukan</option>').prop('disabled', true);
                        return;
                    }

                    data.forEach(function(batch) {
                        let isSelected = (oldBatch === batch.kode_produksi) ? 'selected' : '';
                        
                        // Set teks sebagai value, dan uuid di dalam data-uuid
                        batchSelect.append(`<option value="${batch.kode_produksi}" data-uuid="${batch.uuid}" ${isSelected}>${batch.kode_produksi}</option>`);
                    });

                    // JIKA SEDANG MODE EDIT (oldBatch terisi)
                    if (oldBatch) {
                        // Ambil data-uuid dari opsi yang baru saja di-render dan diset 'selected'
                        let uuid_produksi = batchSelect.find('option:selected').data('uuid');
                        
                        // Jalankan loadPalet dengan memberikan uuid tersebut
                        let oldPalet = "{{ old('palet', $sampling_fg->palet ?? '') }}";
                        if(uuid_produksi) {
                            loadPalet(uuid_produksi, oldPalet);
                        }
                    }
                }
            });
        }

        // Fungsi Load Palet
        function loadPalet(uuid_produksi, oldPalet = '') {
            if (!uuid_produksi) {
                paletSelect.html('<option value="">Pilih Batch Terlebih Dahulu</option>').prop('disabled', true);
                return;
            }

            paletSelect.prop('disabled', false);
            if(paletSelect.find('option').length <= 1) {
                paletSelect.html('<option value="">Mencari Palet...</option>');
            }

            $.ajax({
                url: "{{ route('get.palet') }}",
                type: 'GET',
                data: { kode_produksi: uuid_produksi }, // get.palet di controller mengharapkan UUID
                success: function(data) {
                    paletSelect.html('<option value="">-- Pilih Palet --</option>');
                    if (data.length === 0) {
                        paletSelect.html('<option value="">Palet Tidak Ditemukan</option>');
                        return;
                    }

                    data.forEach(function(item) {
                        // Jika oldPalet sesuai dengan data item, tandai selected
                        let isSelected = (oldPalet == item.no_palet) ? 'selected' : '';
                        paletSelect.append(`<option value="${item.no_palet}" ${isSelected}>${item.no_palet}</option>`);
                    });
                },
                error: function() {
                    paletSelect.html('<option value="">Gagal Terhubung</option>');
                }
            });
        }

        // Trigger Change Varian (Jika user manual mengganti varian)
        namaProdukSelect.on('change', function() {
            loadBatches($(this).val());
            paletSelect.html('<option value="">Pilih Batch Terlebih Dahulu</option>').prop('disabled', true);
            jumlahBoxInput.val('');
            expDateInput.val('');
        });

        // Trigger Change Batch (Jika user manual mengganti batch)
        batchSelect.on('change', function() {
            let kode_produksi = $(this).val(); // Berupa Teks Kode (P3012..)
            let uuid_produksi = $(this).find("option:selected").data("uuid"); // Ambil UUID
            
            paletSelect.html('<option value="">Pilih Batch Terlebih Dahulu</option>').prop('disabled', true);
            jumlahBoxInput.val('');

            // Panggil Load Palet menggunakan UUID
            if(uuid_produksi) {
                loadPalet(uuid_produksi);
            }

            // Coba Load Jml Box
            if (namaProdukSelect.val() && kode_produksi && !kode_produksi.includes('Pilih')) {
                $.ajax({
                    url: "{{ route('get.jumlah.box') }}",
                    method: 'GET',
                    data: { nama_produk: namaProdukSelect.val(), kode_produksi: kode_produksi }, // Controller minta text kode
                    success: function (response) { jumlahBoxInput.val(response.total_box || 0); },
                    error: function () { jumlahBoxInput.val(0); }
                });
            }

            // Set Exp Date
            if (!kode_produksi || kode_produksi.includes('-- Pilih Batch') || kode_produksi.includes('Tidak Ditemukan')) {
                expDateInput.val('');
                return;
            }

            const bulanChar = kode_produksi.charAt(1);
            const hari = parseInt(kode_produksi.substr(2, 2));
            const bulanMap = { A: 0, B: 1, C: 2, D: 3, E: 4, F: 5, G: 6, H: 7, I: 8, J: 9, K: 10, L: 11 };
            
            let kodeBulan = bulanMap[bulanChar];
            if (kodeBulan !== undefined) {
                let now = new Date();
                let tahun = now.getFullYear();
                if (kodeBulan < now.getMonth()) tahun++;
                
                let expDate = new Date(tahun, kodeBulan, hari);
                expDate.setMonth(expDate.getMonth() + 7);
                let localExp = new Date(expDate.getTime() - (expDate.getTimezoneOffset() * 60000));
                expDateInput.val(localExp.toISOString().slice(0, 10));
            }
        });

        // Init Saat Halaman Dimuat (Edit Case)
        if (namaProdukSelect.val() && batchSelect.is('select')) {
            let oldBatch = "{{ old('kode_produksi', $sampling_fg->kode_produksi ?? '') }}";
            loadBatches(namaProdukSelect.val(), oldBatch);
        }
    });
</script>
@endpush
@endsection