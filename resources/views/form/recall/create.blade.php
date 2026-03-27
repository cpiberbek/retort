@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Recall
            </h4>

            <form id="recallForm" method="POST" action="{{ route('recall.store') }}">
                @csrf

                {{-- ===================== MAIN INFO ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Recall</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <label class="col-md-3 col-form-label fw-bold">Tanggal</label>
                            <div class="col-md-9">
                                <input type="date" name="date" id="dateInput"
                                class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date', now()->format('Y-m-d')) }}" required>
                                @error('date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== TABS ===================== --}}
                <ul class="nav nav-tabs mb-3" id="traceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-penyebab"
                        data-bs-toggle="tab" data-bs-target="#content-penyebab"
                        type="button" role="tab">
                        Informasi Penyebab Penarikan
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-pangan"
                    data-bs-toggle="tab" data-bs-target="#content-pangan"
                    type="button" role="tab">
                    Informasi Pangan
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-distribusi"
                data-bs-toggle="tab" data-bs-target="#content-distribusi"
                type="button" role="tab">
                Distribusi / Penjualan / Pengiriman
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-neraca"
            data-bs-toggle="tab" data-bs-target="#content-neraca"
            type="button" role="tab">
            Neraca Penarikan Produk
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-rangkuman"
        data-bs-toggle="tab" data-bs-target="#content-rangkuman"
        type="button" role="tab">
        Rangkuman Waktu Simulasi
    </button>
</li>

<li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-evaluasi"
    data-bs-toggle="tab" data-bs-target="#content-evaluasi"
    type="button" role="tab">
    Evaluasi Simulasi Penarikan
</button>
</li>
</ul>

{{-- ===================== TAB CONTENT ===================== --}}
<div class="tab-content">

    {{-- ===================== TAB 1: Penyebab ===================== --}}
    <div class="tab-pane fade show active" id="content-penyebab" role="tabpanel">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Informasi Penyebab Penarikan</strong>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Penyebab Penarikan</label>
                    <div class="col-md-9">
                        <input type="text" name="penyebab"
                        class="form-control @error('penyebab') is-invalid @enderror"
                        value="{{ old('penyebab') }}">
                        @error('penyebab') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Asal Informasi (Internal/Eksternal)</label>
                    <div class="col-md-9">
                        <input type="text" name="asal_informasi"
                        class="form-control @error('asal_informasi') is-invalid @enderror"
                        value="{{ old('asal_informasi') }}">
                        @error('asal_informasi') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== TAB 2: Informasi Pangan ===================== --}}
    <div class="tab-pane fade" id="content-pangan" role="tabpanel">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Informasi Pangan</strong>
            </div>

            <div class="card-body">

                {{-- field-field pangan --}}
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Jenis Pangan</label>
                    <div class="col-md-9">
                        <input type="text" name="jenis_pangan"
                        class="form-control @error('jenis_pangan') is-invalid @enderror"
                        value="{{ old('jenis_pangan') }}">
                        @error('jenis_pangan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Nama Dagang</label>
                    <div class="col-md-9">
                        <input type="text" name="nama_dagang"
                        class="form-control @error('nama_dagang') is-invalid @enderror"
                        value="{{ old('nama_dagang') }}">
                        @error('nama_dagang') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Berat / Isi Bersih</label>
                    <div class="col-md-9">
                        <input type="number" name="berat_bersih"
                        class="form-control @error('berat_bersih') is-invalid @enderror"
                        value="{{ old('berat_bersih') }}">
                        @error('berat_bersih') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Jenis Kemasan</label>
                    <div class="col-md-9">
                        <input type="text" name="jenis_kemasan"
                        class="form-control @error('jenis_kemasan') is-invalid @enderror"
                        value="{{ old('jenis_kemasan') }}">
                        @error('jenis_kemasan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Kode Produksi</label>
                    <div class="col-md-9">
                        <input type="text" name="kode_produksi"
                        class="form-control @error('kode_produksi') is-invalid @enderror"
                        value="{{ old('kode_produksi') }}">
                        @error('kode_produksi') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Tanggal Produksi</label>
                    <div class="col-md-9">
                        <input type="date" name="tanggal_produksi" id="tanggal_produksi"
                        class="form-control @error('tanggal_produksi') is-invalid @enderror"
                        value="{{ old('tanggal_produksi') }}" required>
                        @error('tanggal_produksi') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Tanggal Kadaluarsa</label>
                    <div class="col-md-9">
                        <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa"
                        class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror"
                        value="{{ old('tanggal_kadaluarsa') }}" required>
                        @error('tanggal_kadaluarsa') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Nomor Pendaftaran Pangan</label>
                    <div class="col-md-9">
                        <input type="text" name="no_pendaftaran"
                        class="form-control @error('no_pendaftaran') is-invalid @enderror"
                        value="{{ old('no_pendaftaran') }}">
                        @error('no_pendaftaran') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Diproduksi Oleh</label>
                    <div class="col-md-9">
                        <input type="text" name="diproduksi_oleh"
                        class="form-control @error('diproduksi_oleh') is-invalid @enderror"
                        value="{{ old('diproduksi_oleh') }}">
                        @error('diproduksi_oleh') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Jumlah Produksi</label>
                    <div class="col-md-9">
                        <input type="number" name="jumlah_produksi"
                        class="form-control @error('jumlah_produksi') is-invalid @enderror"
                        value="{{ old('jumlah_produksi') }}">
                        @error('jumlah_produksi') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Jumlah Pangan Terkirim/Terjual</label>
                    <div class="col-md-9">
                        <input type="number" name="jumlah_terkirim"
                        class="form-control @error('jumlah_terkirim') is-invalid @enderror"
                        value="{{ old('jumlah_terkirim') }}">
                        @error('jumlah_terkirim') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Jumlah Pangan Tersisa di Gudang</label>
                    <div class="col-md-9">
                        <input type="number" name="jumlah_tersisa"
                        class="form-control @error('jumlah_tersisa') is-invalid @enderror"
                        value="{{ old('jumlah_tersisa') }}">
                        @error('jumlah_tersisa') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label fw-bold">Rencana Tindak Lanjut Terhadap Pangan yang Ditarik</label>
                    <div class="col-md-9">
                        <input type="text" name="tindak_lanjut"
                        class="form-control @error('tindak_lanjut') is-invalid @enderror"
                        value="{{ old('tindak_lanjut') }}">
                        @error('tindak_lanjut') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===================== TAB 3: Data Distribusi ===================== --}}
    <div class="tab-pane fade" id="content-distribusi" role="tabpanel">
        <div class="card mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between">
                <strong>Distribusi / Penjualan / Pengiriman</strong>
                <button type="button" id="addRow" class="btn btn-light btn-sm text-primary fw-bold">
                    + Tambah Distribusi
                </button>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal Pengiriman</th>
                            <th>Jumlah Terkirim</th>
                            <th>Distributor / Agen</th>
                            <th>Nomor Invoice / Surat Jalan</th>
                            <th>Nomor Kendaraan</th>
                            <th>Jumlah Tersisa di Distributor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="distribusiForm">
                        <tr>
                            <td>
                                <input type="date" name="distribusi[0][tanggal]" class="form-control">
                            </td>
                            <td>
                                <input type="number" name="distribusi[0][jumlah_terkirim]" class="form-control">
                            </td>
                            <td>
                                <input type="text" name="distribusi[0][distributor]" class="form-control">
                            </td>
                            <td>
                                <input type="text" name="distribusi[0][nomor_invoice]" class="form-control">
                            </td>
                            <td>
                                <input type="text" name="distribusi[0][nomor_kendaraan]" class="form-control">
                            </td>
                            <td>
                                <input type="number" name="distribusi[0][jumlah_tersisa]" class="form-control">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="content-neraca" role="tabpanel">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Neraca Penarikan Produk</strong>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Deskripsi</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>A</td>
                            <td><textarea name="neraca_penarikan[0][deskripsi]" class="form-control" readonly>Jumlah total produk / pangan yang diproduksi</textarea>
                            </td>
                            <td><input type="number" id="A" name="neraca_penarikan[0][jumlah]" class="form-control text-center"></td>
                            <td><input type="text" class="form-control" name="neraca_penarikan[0][satuan]"></td>
                        </tr>

                        <tr>
                            <td>B</td>
                            <td><textarea name="neraca_penarikan[1][deskripsi]" class="form-control" readonly>Jumlah pangan yang masih belum diedarkan / dijual / didistribusikan</textarea></td>
                            <td><input type="number" id="B" name="neraca_penarikan[1][jumlah]" class="form-control text-center"></td>
                            <td><input type="text" class="form-control" name="neraca_penarikan[1][satuan]"></td>
                        </tr>

                        <tr>
                            <td>C</td>
                            <td><textarea name="neraca_penarikan[2][deskripsi]" class="form-control" readonly>Jumlah pangan yang tersisa dari tingkat peritel
                            </textarea></td>
                            <td><input type="number" id="C" name="neraca_penarikan[2][jumlah]" class="form-control text-center"></td>
                            <td><input type="text" class="form-control" name="neraca_penarikan[2][satuan]"></td>
                        </tr>

                        <tr>
                            <td>D</td>
                            <td><textarea name="neraca_penarikan[3][deskripsi]" class="form-control" readonly>Jumlah pangan yang dapat dikembalikan oleh konsumen
                            </textarea></td>
                            <td><input type="number" id="D" name="neraca_penarikan[3][jumlah]" class="form-control text-center"></td>
                            <td><input type="text" class="form-control" name="neraca_penarikan[3][satuan]"></td>
                        </tr>

                        <tr>
                            <td>E</td>
                            <td><textarea name="neraca_penarikan[4][deskripsi]" class="form-control" readonly>Jumlah pangan yang tidak bisa dilacak atau dihitung (E=A-(B+C+D))
                            </textarea></td>
                            <td><input type="number" id="E" name="neraca_penarikan[4][jumlah]" class="form-control text-center" readonly></td>
                            <td><input type="text" class="form-control" name="neraca_penarikan[4][satuan]"></td>
                        </tr>

                        <tr>
                            <td>F</td>
                            <td><textarea name="neraca_penarikan[5][deskripsi]" class="form-control" readonly>Jumlah pangan yang dapat ditarik (F = B+C+D)
                            </textarea></td>
                            <td><input type="number" id="F" name="neraca_penarikan[5][jumlah]" class="form-control text-center" readonly></td>
                            <td><input type="text" class="form-control" name="neraca_penarikan[5][satuan]"></td>
                        </tr>

                        <tr>
                            <td>=</td>
                            <td><textarea name="neraca_penarikan[6][deskripsi]" class="form-control" readonly>Presentasi kemungkinan pangan yang tidak dapat ditarik (E/A) x 100%
                            </textarea></td>
                            <td><input type="number" id="persenE" name="neraca_penarikan[6][jumlah]" class="form-control text-center" readonly></td>
                            <td><input type="text" class="form-control" name="neraca_penarikan[6][satuan]"></td>
                        </tr>

                        <tr>
                            <td>=</td>
                            <td><textarea name="neraca_penarikan[7][deskripsi]" class="form-control" readonly>Presentasi kemungkinan pangan yang dapat ditarik (F/A) x 100%
                            </textarea></td>
                            <td><input type="number" id="persenF" name="neraca_penarikan[7][jumlah]" class="form-control text-center" readonly></td>
                            <td><input type="text" class="form-control" name="neraca_penarikan[7][satuan]"></td>
                        </tr>
                    </tbody>
                </table>

                {{-- ALERT DIPINDAH KE SINI (DALAM CARD-BODY) --}}
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Keterangan:</strong>
                    <ul class="mb-0 mt-2">
                        <li><b>A</b> = Jumlah pangan yang diproduksi / diimpor atau diedarkan</li>
                        <li><b>B</b> = Jumlah pangan yang masih belum diedarkan/dijual/didistribusikan</li>
                        <li><b>C</b> = Jumlah pangan yang tersisa dari tingkat peritel (di gudang agen)</li>
                        <li><b>D</b> = Jumlah pangan yang dikembalikan oleh konsumen, distributor, agen dan rantai distribusi yang lain</li>
                        <li><b>E</b> = Jumlah pangan yang tidak bisa dilacak atau dihitung dari B, C, dan D</li>
                        <li><b>F</b> = Jumlah pangan yang tidak ditarik</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="content-rangkuman" role="tabpanel">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Rangkuman Waktu Simulasi</strong>
            </div>

            <div class="card-body table-responsive">
              <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Aktivitas</th>
                        <th>Tgl/Jam Mulai</th>
                        <th>Tgl/Jam Selesai</th>
                        <th>Total Waktu</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            <textarea name="simulasi[0][aktivitas]" class="form-control" readonly>Mulai Simulasi (Mock Recall Start)</textarea>
                        </td>
                        <td>
                          <input type="datetime-local" name="simulasi[0][mulai]" id="mulai_0" class="form-control">
                      </td>
                      <td>
                       <input type="datetime-local" name="simulasi[0][selesai]" class="form-control" disabled>
                   </td>
                   <td>
                     <input type="text" name="simulasi[0][total_waktu]" disabled>
                 </td>
             </tr>
             <tr>
                <td>
                    <textarea name="simulasi[1][aktivitas]" class="form-control" readonly>Identifikasi asal masalah (Informasi Awal)</textarea>
                </td>
                <td>
                    <input type="datetime-local" name="simulasi[1][mulai]" id="mulai_1" class="form-control">
                </td>
                <td>
                 <input type="datetime-local" name="simulasi[1][selesai]" id="selesai_1" class="form-control">
             </td>
             <td>
                <input type="text" name="simulasi[1][total_waktu]" id="total_1" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[2][aktivitas]" class="form-control" readonly>Telusur Informasi Pangan</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[2][mulai]" id="mulai_2" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[2][selesai]" id="selesai_2" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[2][total_waktu]" id="total_2" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[3][aktivitas]" class="form-control" readonly>Sistem Ketertelusuran mundur (Backward)</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[3][mulai]" id="mulai_3" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[3][selesai]" id="selesai_3" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[3][total_waktu]" id="total_3" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[4][aktivitas]" class="form-control" readonly>Sistem Ketertelusuran maju (Forward)</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[4][mulai]" id="mulai_4" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[4][selesai]" id="selesai_4" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[4][total_waktu]" id="total_4" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[5][aktivitas]" class="form-control" readonly>Penelusuran ke tingkat distribusi / agen / eksport</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[5][mulai]" id="mulai_5" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[5][selesai]" id="selesai_5" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[5][total_waktu]" id="total_5" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[6][aktivitas]" class="form-control" readonly>Informasi ke seluruh saluran distribusi</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[6][mulai]" id="mulai_6" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[6][selesai]" id="selesai_6" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[6][total_waktu]" id="total_6" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[7][aktivitas]" class="form-control" readonly>Konfirmasi sisa stock yang tersisa di distributor / agen / peritel</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[7][mulai]" id="mulai_7" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[7][selesai]" id="selesai_7" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[7][total_waktu]" id="total_7" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[8][aktivitas]" class="form-control" readonly>Penelusuran ke tingkat pengecer / peritel</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[8][mulai]" id="mulai_8" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[8][selesai]" id="selesai_8" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[8][total_waktu]" id="total_8" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[9][aktivitas]" class="form-control" readonly>Informasi ke seluruh pengecer / peritel</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[9][mulai]" id="mulai_9" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[9][selesai]" id="selesai_9" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[9][total_waktu]" id="total_9" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[10][aktivitas]" class="form-control" readonly>Konfirmasi sisa stock yang tersisa di pengecer / peritel</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[10][mulai]" id="mulai_10" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[10][selesai]" id="selesai_10" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[10][total_waktu]" id="total_10" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[11][aktivitas]" class="form-control" readonly>Konfirmasi akhir jumlah panggan yang dapat di tarik</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[11][mulai]" id="mulai_11" class="form-control">
            </td>
            <td>
                <input type="datetime-local" name="simulasi[11][selesai]" id="selesai_11" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[11][total_waktu]" id="total_11" readonly>
            </td>
        </tr>
        <tr>
            <td>
                <textarea name="simulasi[12][aktivitas]" class="form-control" readonly>Akhir Simulasi (Mock Recall Finish)</textarea>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[12][mulai]" class="form-control" disabled>
            </td>
            <td>
                <input type="datetime-local" name="simulasi[12][selesai]" id="selesai_12" class="form-control">
            </td>
            <td>
                <input type="text" name="simulasi[12][total_waktu]" disabled>
            </td>
        </tr>
    </tbody>
</table>

{{-- TOTAL --}}
<tr>
    <td colspan="3" class="text-end fw-bold">Jumlah Total Waktu:</td>
    <td colspan="2">
        <input type="text" id="total_waktu" name="total_waktu" 
        class="form-control form-control-sm" readonly>
    </td>
</tr>

</div>
</div>
</div>


{{-- ===================== TAB 6: Evaluasi ===================== --}}
<div class="tab-pane fade" id="content-evaluasi" role="tabpanel">
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Evaluasi Simulasi Penarikan</strong>
        </div>

        <div class="card-body">

            {{-- PERTANYAAN 1 --}}
            <div class="mb-3">
                <label class="form-label fw-bold">
                    1. Apakah prosedur penarikan pangan dan proses penarikan dapat dilaksanakan sesuai prosedur?
                </label>

                <div class="form-check">
                    <input class="form-check-input" type="radio"
                    name="evaluasi[prosedur_sesuai]" value="YA" id="prosedur_ya">
                    <label class="form-check-label" for="prosedur_ya">YA</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio"
                    name="evaluasi[prosedur_sesuai]" value="TIDAK" id="prosedur_tidak">
                    <label class="form-check-label" for="prosedur_tidak">TIDAK</label>
                </div>
            </div>

            {{-- JIKA TIDAK --}}
            <div class="mb-3">
                <label class="form-label">
                    A. Apakah penyebab ketidaksesuaian?
                </label>
                <textarea name="evaluasi[penyebab_ketidaksesuaian]"
                class="form-control"
                rows="3"
                placeholder="Jelaskan penyebab ketidaksesuaian jika ada"></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">
                    B. Jelaskan langkah yang dilakukan sebagai perbaikan
                </label>
                <textarea name="evaluasi[tindakan_perbaikan]"
                class="form-control"
                rows="3"
                placeholder="Jelaskan tindakan perbaikan yang dilakukan"></textarea>
            </div>

            <hr>

            {{-- PERTANYAAN 2 --}}
            <div class="mb-3">
                <label class="form-label fw-bold">
                    2. Apakah ada perubahan antara prosedur dan pelaksanaan penarikan?
                </label>

                <div class="form-check">
                    <input class="form-check-input" type="radio"
                    name="evaluasi[ada_perubahan]" value="YA" id="perubahan_ya">
                    <label class="form-check-label" for="perubahan_ya">YA</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio"
                    name="evaluasi[ada_perubahan]" value="TIDAK" id="perubahan_tidak">
                    <label class="form-check-label" for="perubahan_tidak">TIDAK</label>
                </div>
            </div>

            {{-- JIKA YA --}}
            <div class="mb-3">
                <label class="form-label">
                    Jika YA, jelaskan hal apa yang berubah
                </label>
                <textarea name="evaluasi[detail_perubahan]"
                class="form-control"
                rows="3"
                placeholder="Jelaskan perubahan yang terjadi"></textarea>
            </div>

        </div>
    </div>
</div>


</div>

{{-- ===================== BUTTONS ===================== --}}
<div class="d-flex justify-content-between mt-3">
    <button type="submit" class="btn btn-success">
        <i class="bi bi-save"></i> Simpan
    </button>

    <a href="{{ route('recall.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

</form>

</div>
</div>
</div>

{{-- ===================== ASSETS ===================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

{{-- ===================== SCRIPT ===================== --}}
<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();
    });

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("dateInput").value = new Date().toISOString().slice(0, 10);
    });

    document.getElementById('tanggal_produksi').addEventListener('change', function () {
        let produksi = new Date(this.value);

        if (!isNaN(produksi.getTime())) {
            let kadaluarsa = new Date(produksi);
            kadaluarsa.setMonth(kadaluarsa.getMonth() + 7);

            let tahun = kadaluarsa.getFullYear();
            let bulan = String(kadaluarsa.getMonth() + 1).padStart(2, '0');
            let hari = String(kadaluarsa.getDate()).padStart(2, '0');

            document.getElementById('tanggal_kadaluarsa').value = `${tahun}-${bulan}-${hari}`;
        }
    });
</script>
<script>
    let rowIndex = 1;

    document.getElementById('addRow').addEventListener('click', function () {
        const tbody = document.getElementById('distribusiForm');

        const tr = document.createElement('tr');

        tr.innerHTML = `
        <td><input type="date" name="distribusi[${rowIndex}][tanggal]" class="form-control"></td>
        <td><input type="number" name="distribusi[${rowIndex}][jumlah_terkirim]" class="form-control"></td>
        <td><input type="text" name="distribusi[${rowIndex}][distributor]" class="form-control"></td>
        <td><input type="text" name="distribusi[${rowIndex}][nomor_invoice]" class="form-control"></td>
        <td><input type="text" name="distribusi[${rowIndex}][nomor_kendaraan]" class="form-control"></td>
        <td><input type="number" name="distribusi[${rowIndex}][jumlah_tersisa]" class="form-control"></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
        </td>
        `;

        tbody.appendChild(tr);
        rowIndex++;
    });

    document.getElementById('distribusiForm').addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        function hitungNeraca() {
            let A = parseFloat(document.getElementById('A').value) || 0;
            let B = parseFloat(document.getElementById('B').value) || 0;
            let C = parseFloat(document.getElementById('C').value) || 0;
            let D = parseFloat(document.getElementById('D').value) || 0;

        // F = B + C + D
            let F = B + C + D;
            document.getElementById('F').value = F;

        // E = A - (B + C + D)
            let E = A - (B + C + D);
            document.getElementById('E').value = E >= 0 ? E : 0;

        // Persentase
            document.getElementById('persenE').value =
            A > 0 ? ((E / A) * 100).toFixed(2) : 0;

            document.getElementById('persenF').value =
            A > 0 ? ((F / A) * 100).toFixed(2) : 0;
        }

    // Trigger hitung saat A B C D berubah
        ['A','B','C','D'].forEach(id => {
            document.getElementById(id).addEventListener('input', hitungNeraca);
        });

    });
</script>

<script>
    function hitungTotalWaktu(index) {
        let mulaiEl = document.getElementById('mulai_' + index);
        let selesaiEl = document.getElementById('selesai_' + index);
        let totalEl = document.getElementById('total_' + index);

        if (!mulaiEl || !selesaiEl || !totalEl) return;

        let mulai = mulaiEl.value;
        let selesai = selesaiEl.value;

        if (!mulai || !selesai) return;

        let start = new Date(mulai);
        let end = new Date(selesai);

        if (end < start) {
            totalEl.value = 'Invalid';
            totalEl.dataset.menit = 0;
            hitungGrandTotal();
            return;
        }

        let diffMs = end - start;
        let diffMenit = Math.floor(diffMs / 60000);

        let jam = Math.floor(diffMenit / 60);
        let menit = diffMenit % 60;

        totalEl.value = jam + ' jam ' + menit + ' menit';
        totalEl.dataset.menit = diffMenit;

        hitungGrandTotal();
    }

    function hitungGrandTotal() {
        let totalMenit = 0;

        document.querySelectorAll('[id^="total_"]').forEach(el => {
            totalMenit += parseInt(el.dataset.menit || 0);
        });

        let jam = Math.floor(totalMenit / 60);
        let menit = totalMenit % 60;

        document.getElementById('total_waktu').value =
        jam + ' jam ' + menit + ' menit';
    }

// auto attach listener untuk semua index 0–12
    for (let i = 0; i <= 12; i++) {
        let mulai = document.getElementById('mulai_' + i);
        let selesai = document.getElementById('selesai_' + i);

        if (mulai) mulai.addEventListener('change', () => hitungTotalWaktu(i));
        if (selesai) selesai.addEventListener('change', () => hitungTotalWaktu(i));
    }
</script>

@endsection
