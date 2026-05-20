@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">
                    <i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Proses Sampling Finish Good
                </h4>

                <form id="samplingForm" action="{{ route('sampling_fg.store') }}" method="POST">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="bi bi-exclamation-triangle"></i> Terjadi Kesalahan!</strong>
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
                                    <input type="date" name="date" id="dateInput" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Shift</label>
                                    <select name="shift" id="shiftInput" class="form-control" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="1">Shift 1</option>
                                        <option value="2">Shift 2</option>
                                        <option value="3">Shift 3</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama_produk" class="form-label fw-semibold">
                                        Nama Varian <span class="text-danger">*</span>
                                    </label>
                                    <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">-- Pilih Varian --</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}">
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Pilih varian produk terlebih dahulu</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="kode_batch" class="form-label fw-semibold">
                                        Kode Batch <span class="text-danger">*</span>
                                    </label>
                                    <select id="kode_batch" name="kode_produksi" class="form-control" disabled required>
                                        <option value="">Pilih Varian terlebih dahulu</option>
                                    </select>
                                    <small id="kodeError" class="text-danger d-none"></small>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Palet <span class="text-danger">*</span></label>
                                    <select name="palet" id="palet" class="form-control" disabled required>
                                        <option value="">Pilih Batch terlebih dahulu</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Exp. Date</label>
                                    <input type="date" name="exp_date" id="exp_date" class="form-control">
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
                                    <input type="time" id="timeInput" name="pukul" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Kalibrasi</label>
                                    <select class="form-control" id="kalibrasi" name="kalibrasi">
                                        <option value="Sesuai">Sesuai</option>
                                        <option value="Tidak Sesuai">Tidak Sesuai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Berat Varian per Box (gr)</label>
                                    <input type="number" name="berat_produk" id="berat_produk" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" name="keterangan" id="keterangan" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Isi Varian per Box</label>
                                    <input type="number" name="isi_per_box" id="isi_per_box" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kemasan</label>
                                    <select name="kemasan" id="kemasan" class="form-control selectpicker">
                                        <option value="">-- Pilih Jenis Kemasan --</option>
                                        <option value="Jar">Toples</option>
                                        <option value="Pouch">Pouch</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah (Box)</label>
                                    <input type="number" name="jumlah_box" id="jumlah_box" class="form-control"
                                        readonly placeholder="Otomatis dari Release Packing">
                                </div>
                            </div>

                            <hr>

                            <label class="form-label"><b>Status Varian</b></label>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Release (Box)</label>
                                    <input type="number" name="release" id="release" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Reject (Box)</label>
                                    <input type="number" name="reject" id="reject" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hold (Box)</label>
                                    <input type="number" name="hold" id="hold" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <strong>Koordinator</strong>
                        </div>
                        <div class="card-body">
                            <div class="col-md-6">
                                <label class="form-label">Nama KR</label>
                                <select id="nama_koordinator" name="nama_koordinator" class="form-control selectpicker"
                                    data-live-search="true" required>
                                    <option value="">-- Pilih Koordinator --</option>
                                    @foreach ($koordinators as $koordinator)
                                        <option value="{{ $koordinator->nama_karyawan }}">{{ $koordinator->nama_karyawan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    {{-- ===================== CATATAN ===================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light"><strong>Item Mutu</strong></div>
                        <div class="card-body">
                            <textarea name="item_mutu" class="form-control" rows="3" placeholder="Tambahkan Item Mutu bila ada">{{ old('item_mutu') }}</textarea>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header bg-light"><strong>Catatan</strong></div>
                        <div class="card-body">
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan') }}</textarea>
                        </div>
                    </div>

                    {{-- ===================== TOMBOL ===================== --}}
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan
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
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

        <script>
            $(document).ready(function() {
                $('.selectpicker').selectpicker();

                // Set tanggal, waktu, dan shift otomatis
                const dateInput = document.getElementById("dateInput");
                const shiftInput = document.getElementById("shiftInput");
                const timeInput = document.getElementById("timeInput");

                const now = new Date();
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const hh = String(now.getHours()).padStart(2, '0');
                const min = String(now.getMinutes()).padStart(2, '0');

                if(dateInput) dateInput.value = `${yyyy}-${mm}-${dd}`;
                if(timeInput) timeInput.value = `${hh}:${min}`;

                const hour = parseInt(hh);
                if (hour >= 7 && hour < 15) shiftInput.value = "1";
                else if (hour >= 15 && hour < 23) shiftInput.value = "2";
                else shiftInput.value = "3";

                // Variabel Elemen Form
                const namaProdukSelect = $('#nama_produk');
                const kodeBatchSelect = $('#kode_batch');
                const paletSelect = $('#palet');
                const expDateInput = $('#exp_date');
                const jumlahBoxInput = $('#jumlah_box');

                // Kita butuh penampung untuk menyimpan UUID sementara (dibutuhkan untuk panggil relasi palet di controller jika controller membutuhkannya)
                // Namun, karena `getPalet` controller sekarang mencari berdasarkan `kode_produksi` (di controller anda UUID, tapi sebentar lagi kita akan mengirim teksnya), 
                // Mari kita pastikan Controller "getPalet" memproses Teks Batch/Kode Produksi, bukan UUID.
                
                // 1. AJAX LOAD BATCH BERDASARKAN VARIAN
                namaProdukSelect.on('change', function() {
                    let namaProduk = $(this).val();
                    
                    kodeBatchSelect.html('<option value="">Pilih Varian terlebih dahulu</option>').prop('disabled', true);
                    paletSelect.html('<option value="">Pilih Batch terlebih dahulu</option>').prop('disabled', true);
                    jumlahBoxInput.val('');
                    expDateInput.val('');

                    if (!namaProduk) return;

                    $.ajax({
                        url: '/lookup/batch/' + encodeURIComponent(namaProduk),
                        type: 'GET',
                        success: function(data) {
                            kodeBatchSelect.prop('disabled', false);
                            kodeBatchSelect.html('<option value="">-- Pilih Batch --</option>');
                            
                            if(data.length === 0){
                                kodeBatchSelect.html('<option value="">Batch Kosong</option>');
                                kodeBatchSelect.prop('disabled', true);
                                return;
                            }

                            data.forEach(function(item) {
                                // VALUE DIUBAH KE TEKS KODE PRODUKSI (bukan item.uuid)
                                kodeBatchSelect.append(`<option value="${item.kode_produksi}" data-uuid="${item.uuid}">${item.kode_produksi}</option>`);
                            });
                        }
                    });
                });

                // 2. AJAX LOAD PALET & HITUNG EXPIRED & LOAD BOX BERDASARKAN BATCH
                kodeBatchSelect.on('change', function() {
                    let kode_produksi = $(this).val(); // INI SEKARANG TEKS KODE BATCH (Bukan UUID)
                    let uuid_produksi = $(this).find("option:selected").data("uuid"); // Ambil UUID dari data-attribute untuk fungsi AJAX jika dibutuhkan
                    
                    paletSelect.html('<option value="">Pilih Batch terlebih dahulu</option>').prop('disabled', true);
                    jumlahBoxInput.val('');
                    expDateInput.val('');
                    
                    if (!kode_produksi) return;

                    // A. Hitung Expired Date
                    if (kode_produksi && !kode_produksi.includes('--')) {
                        const bulanChar = kode_produksi.charAt(1);
                        const hari = parseInt(kode_produksi.substr(2, 2));
                        const bulanMap = { A: 0, B: 1, C: 2, D: 3, E: 4, F: 5, G: 6, H: 7, I: 8, J: 9, K: 10, L: 11 };
                        let kodeBulan = bulanMap[bulanChar];
                        
                        if (kodeBulan !== undefined) {
                            let today = new Date();
                            let tahun = today.getFullYear();
                            if (kodeBulan < today.getMonth()) tahun++;
                            
                            let expDate = new Date(tahun, kodeBulan, hari);
                            expDate.setMonth(expDate.getMonth() + 7);
                            let localExp = new Date(expDate.getTime() - (expDate.getTimezoneOffset() * 60000));
                            expDateInput.val(localExp.toISOString().slice(0, 10));
                        }
                    }

                    // B. Load Palet dari Release Packing (Menggunakan UUID karena Controller mengharapkan uuid mincing)
                    $.ajax({
                        url: "{{ route('get.palet') }}",
                        type: 'GET',
                        data: { kode_produksi: uuid_produksi }, // Kirim UUID ke controller
                        success: function(data) {
                            paletSelect.prop('disabled', false);
                            paletSelect.html('<option value="">-- Pilih Palet --</option>');
                            
                            if(data.length === 0){
                                paletSelect.html('<option value="">Palet Tidak Ditemukan</option>');
                                return;
                            }

                            data.forEach(function(item) {
                                paletSelect.append(`<option value="${item.no_palet}">${item.no_palet}</option>`);
                            });
                        }
                    });

                    // C. Load Jumlah Box
                    let nama_produk = namaProdukSelect.val();
                    $.ajax({
                        url: "{{ route('get.jumlah.box') }}",
                        method: 'GET',
                        // Parameter kode_produksi pada Controller getJumlahBox mengharapkan TEKS
                        data: { nama_produk: nama_produk, kode_produksi: kode_produksi }, 
                        success: function (response) {
                            jumlahBoxInput.val(response.total_box || 0);
                        },
                        error: function () {
                            jumlahBoxInput.val(0);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection