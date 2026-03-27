@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Recall
            </h4>

            <form id="recallForm" method="POST"
            action="{{ route('recall.update', $recall->uuid) }}">
            @csrf
            @method('PUT')

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
                            value="{{ old('date', $recall->date) }}" required>

                            @error('date')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== TABS ===================== --}}
            <ul class="nav nav-tabs mb-3" id="traceTabs" role="tablist">

                <li class="nav-item">
                    <button class="nav-link active"
                    data-bs-toggle="tab"
                    data-bs-target="#content-penyebab"
                    type="button">
                    Informasi Penyebab Penarikan
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#content-pangan"
                type="button">
                Informasi Pangan
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link"
            data-bs-toggle="tab"
            data-bs-target="#content-distribusi"
            type="button">
            Distribusi / Penjualan / Pengiriman
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
        data-bs-toggle="tab"
        data-bs-target="#content-neraca"
        type="button">
        Neraca Penarikan Produk
    </button>
</li>

<li class="nav-item">
    <button class="nav-link"
    data-bs-toggle="tab"
    data-bs-target="#content-rangkuman"
    type="button">
    Rangkuman Waktu Simulasi
</button>
</li>

<li class="nav-item">
    <button class="nav-link"
    data-bs-toggle="tab"
    data-bs-target="#content-evaluasi"
    type="button">
    Evaluasi Simulasi Penarikan
</button>
</li>
</ul>

{{-- ===================== TAB 1: Penyebab ===================== --}}
<div class="tab-content">

    <div class="tab-pane fade show active"
    id="content-penyebab">

    <div class="card mb-4">

        <div class="card-header bg-info text-white">
            <strong>Informasi Penyebab Penarikan</strong>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">
                    Penyebab Penarikan
                </label>

                <div class="col-md-9">
                    <input type="text"
                    name="penyebab"
                    class="form-control @error('penyebab') is-invalid @enderror"
                    value="{{ old('penyebab', $recall->penyebab) }}">

                    @error('penyebab')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">
                    Asal Informasi (Internal/Eksternal)
                </label>

                <div class="col-md-9">
                    <input type="text"
                    name="asal_informasi"
                    class="form-control @error('asal_informasi') is-invalid @enderror"
                    value="{{ old('asal_informasi', $recall->asal_informasi) }}">

                    @error('asal_informasi')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
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
                    value="{{ old('jenis_pangan', $recall->jenis_pangan) }}">

                    @error('jenis_pangan')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Nama Dagang</label>
                <div class="col-md-9">
                    <input type="text" name="nama_dagang"
                    class="form-control @error('nama_dagang') is-invalid @enderror"
                    value="{{ old('nama_dagang', $recall->nama_dagang) }}">

                    @error('nama_dagang')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Berat / Isi Bersih</label>
                <div class="col-md-9">
                    <input type="number" name="berat_bersih"
                    class="form-control @error('berat_bersih') is-invalid @enderror"
                    value="{{ old('berat_bersih', $recall->berat_bersih) }}">

                    @error('berat_bersih')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Jenis Kemasan</label>
                <div class="col-md-9">
                    <input type="text" name="jenis_kemasan"
                    class="form-control @error('jenis_kemasan') is-invalid @enderror"
                    value="{{ old('jenis_kemasan', $recall->jenis_kemasan) }}">

                    @error('jenis_kemasan')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Kode Produksi</label>
                <div class="col-md-9">
                    <input type="text" name="kode_produksi"
                    class="form-control @error('kode_produksi') is-invalid @enderror"
                    value="{{ old('kode_produksi', $recall->kode_produksi) }}">

                    @error('kode_produksi')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Tanggal Produksi</label>
                <div class="col-md-9">
                    <input type="date" name="tanggal_produksi" id="tanggal_produksi"
                    class="form-control @error('tanggal_produksi') is-invalid @enderror"
                    value="{{ old('tanggal_produksi', $recall->tanggal_produksi) }}" required>

                    @error('tanggal_produksi')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Tanggal Kadaluarsa</label>
                <div class="col-md-9">
                    <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa"
                    class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror"
                    value="{{ old('tanggal_kadaluarsa', $recall->tanggal_kadaluarsa) }}" required>

                    @error('tanggal_kadaluarsa')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Nomor Pendaftaran Pangan</label>
                <div class="col-md-9">
                    <input type="text" name="no_pendaftaran"
                    class="form-control @error('no_pendaftaran') is-invalid @enderror"
                    value="{{ old('no_pendaftaran', $recall->no_pendaftaran) }}">

                    @error('no_pendaftaran')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Diproduksi Oleh</label>
                <div class="col-md-9">
                    <input type="text" name="diproduksi_oleh"
                    class="form-control @error('diproduksi_oleh') is-invalid @enderror"
                    value="{{ old('diproduksi_oleh', $recall->diproduksi_oleh) }}">

                    @error('diproduksi_oleh')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Jumlah Produksi</label>
                <div class="col-md-9">
                    <input type="number" name="jumlah_produksi"
                    class="form-control @error('jumlah_produksi') is-invalid @enderror"
                    value="{{ old('jumlah_produksi', $recall->jumlah_produksi) }}">

                    @error('jumlah_produksi')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Jumlah Pangan Terkirim / Terjual</label>
                <div class="col-md-9">
                    <input type="number" name="jumlah_terkirim"
                    class="form-control @error('jumlah_terkirim') is-invalid @enderror"
                    value="{{ old('jumlah_terkirim', $recall->jumlah_terkirim) }}">

                    @error('jumlah_terkirim')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">Jumlah Pangan Tersisa di Gudang</label>
                <div class="col-md-9">
                    <input type="number" name="jumlah_tersisa"
                    class="form-control @error('jumlah_tersisa') is-invalid @enderror"
                    value="{{ old('jumlah_tersisa', $recall->jumlah_tersisa) }}">

                    @error('jumlah_tersisa')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-3 col-form-label fw-bold">
                    Rencana Tindak Lanjut Terhadap Pangan yang Ditarik
                </label>

                <div class="col-md-9">
                    <input type="text" name="tindak_lanjut"
                    class="form-control @error('tindak_lanjut') is-invalid @enderror"
                    value="{{ old('tindak_lanjut', $recall->tindak_lanjut) }}">

                    @error('tindak_lanjut')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
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

            <button type="button"
            id="addRow"
            class="btn btn-light btn-sm text-primary fw-bold">
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

                {{-- === DATA EXISTING === --}}
                @php
                $oldDistribusi = old('distribusi', $recall->distribusi ?? []);
                @endphp

                @forelse ($oldDistribusi as $i => $dist)
                <tr>
                    <td>
                        <input type="date"
                        name="distribusi[{{ $i }}][tanggal]"
                        class="form-control"
                        value="{{ $dist['tanggal'] ?? $dist->tanggal ?? '' }}">
                    </td>

                    <td>
                        <input type="number"
                        name="distribusi[{{ $i }}][jumlah_terkirim]"
                        class="form-control"
                        value="{{ $dist['jumlah_terkirim'] ?? $dist->jumlah_terkirim ?? '' }}">
                    </td>

                    <td>
                        <input type="text"
                        name="distribusi[{{ $i }}][distributor]"
                        class="form-control"
                        value="{{ $dist['distributor'] ?? $dist->distributor ?? '' }}">
                    </td>

                    <td>
                        <input type="text"
                        name="distribusi[{{ $i }}][nomor_invoice]"
                        class="form-control"
                        value="{{ $dist['nomor_invoice'] ?? $dist->nomor_invoice ?? '' }}">
                    </td>

                    <td>
                        <input type="text"
                        name="distribusi[{{ $i }}][nomor_kendaraan]"
                        class="form-control"
                        value="{{ $dist['nomor_kendaraan'] ?? $dist->nomor_kendaraan ?? '' }}">
                    </td>

                    <td>
                        <input type="number"
                        name="distribusi[{{ $i }}][jumlah_tersisa]"
                        class="form-control"
                        value="{{ $dist['jumlah_tersisa'] ?? $dist->jumlah_tersisa ?? '' }}">
                    </td>

                    <td>
                        <button type="button"
                        class="btn btn-danger btn-sm removeRow">
                        Hapus
                    </button>
                </td>
            </tr>
            @empty
            {{-- jika belum ada data --}}
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
                    <button type="button"
                    class="btn btn-danger btn-sm removeRow">
                    Hapus
                </button>
            </td>
        </tr>
        @endforelse

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

            @php
            $oldNeraca = old('neraca_penarikan', $recall->neraca_penarikan ?? []);
            $deskripsiDefault = [
            'Jumlah total produk / pangan yang diproduksi',
            'Jumlah pangan yang masih belum diedarkan / dijual / didistribusikan',
            'Jumlah pangan yang tersisa dari tingkat peritel',
            'Jumlah pangan yang dapat dikembalikan oleh konsumen',
            'Jumlah pangan yang tidak bisa dilacak atau dihitung (E=A-(B+C+D))',
            'Jumlah pangan yang dapat ditarik (F = B+C+D)',
            'Presentasi kemungkinan pangan yang tidak dapat ditarik (E/A) x 100%',
            'Presentasi kemungkinan pangan yang dapat ditarik (F/A) x 100%',
            ];
            @endphp

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

                    @foreach ($deskripsiDefault as $i => $desk)

                    @php $row = $oldNeraca[$i] ?? null; @endphp

                    <tr>
                        <td>{{ chr(65 + $i) }}</td>

                        <td>
                            <textarea name="neraca_penarikan[{{ $i }}][deskripsi]"
                            class="form-control"
                            readonly>{{ $row['deskripsi'] ?? $row->deskripsi ?? $desk }}</textarea>
                        </td>

                        <td>
                            <input type="number"
                            id="{{ chr(65 + $i) }}"
                            name="neraca_penarikan[{{ $i }}][jumlah]"
                            class="form-control text-center"
                            value="{{ $row['jumlah'] ?? $row->jumlah ?? '' }}"
                            {{ $i >= 4 ? 'readonly' : '' }}>
                        </td>

                        <td>
                            <input type="text"
                            name="neraca_penarikan[{{ $i }}][satuan]"
                            class="form-control"
                            value="{{ $row['satuan'] ?? $row->satuan ?? '' }}">
                        </td>
                    </tr>

                    @endforeach

                </tbody>
            </table>

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

            @php
            $oldSimulasi = old('simulasi', $recall->simulasi ?? []);

            $aktivitasDefault = [
            'Mulai Simulasi (Mock Recall Start)',
            'Identifikasi asal masalah (Informasi Awal)',
            'Telusur Informasi Pangan',
            'Sistem Ketertelusuran mundur (Backward)',
            'Sistem Ketertelusuran maju (Forward)',
            'Penelusuran ke tingkat distribusi / agen / eksport',
            'Informasi ke seluruh saluran distribusi',
            'Konfirmasi sisa stock yang tersisa di distributor / agen / peritel',
            'Penelusuran ke tingkat pengecer / peritel',
            'Informasi ke seluruh pengecer / peritel',
            'Konfirmasi sisa stock yang tersisa di pengecer / peritel',
            'Konfirmasi akhir jumlah pangan yang dapat di tarik',
            'Akhir Simulasi (Mock Recall Finish)',
            ];
            @endphp

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

                    @foreach ($aktivitasDefault as $i => $aktivitas)

                    @php
                    $row = $oldSimulasi[$i] ?? null;
                    @endphp

                    <tr>

                        <td>
                            <textarea
                            name="simulasi[{{ $i }}][aktivitas]"
                            class="form-control"
                            readonly>{{ $row['aktivitas'] ?? $row->aktivitas ?? $aktivitas }}</textarea>
                        </td>

                        <td>
                            <input type="datetime-local"
                            name="simulasi[{{ $i }}][mulai]"
                            id="mulai_{{ $i }}"
                            class="form-control"
                            value="{{ isset($row['mulai']) 
                            ? \Carbon\Carbon::parse($row['mulai'])->format('Y-m-d\TH:i') 
                            : (isset($row->mulai) ? \Carbon\Carbon::parse($row->mulai)->format('Y-m-d\TH:i') : '') }}"

                            {{ in_array($i, [0,12]) ? 'readonly' : '' }}
                            ...
                            {{ $i === 0 ? 'readonly' : '' }}
                            >
                        </td>

                        <td>
                            <input type="datetime-local"
                            name="simulasi[{{ $i }}][selesai]"
                            id="selesai_{{ $i }}"
                            class="form-control"
                            value="{{ $row['selesai'] ?? $row->selesai ?? '' }}"
                            {{ $i === 0 ? 'disabled' : '' }}>
                        </td>

                        <td>
                            <input type="text"
                            name="simulasi[{{ $i }}][total_waktu]"
                            id="total_{{ $i }}"
                            class="form-control"
                            value="{{ $row['total_waktu'] ?? $row->total_waktu ?? '' }}"
                            readonly
                            data-menit="{{ $row['total_menit'] ?? '' }}">
                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

            {{-- TOTAL --}}
            <div class="row mt-3">
                <div class="col-md-9 text-end fw-bold">
                    Jumlah Total Waktu:
                </div>
                <div class="col-md-3">
                    <input type="text"
                    id="total_waktu"
                    name="total_waktu"
                    class="form-control form-control-sm"
                    readonly
                    value="{{ old('total_waktu', $recall->total_waktu) }}">
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ===================== TAB 6: Evaluasi ===================== --}}
<div class="tab-pane fade" id="content-evaluasi" role="tabpanel">

    @php
        $evaluasi = $recall->evaluasi ?? [];
    @endphp

    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Evaluasi Simulasi Penarikan</strong>
        </div>

        <div class="card-body">

            {{-- =================== PERTANYAAN 1 =================== --}}
            <div class="mb-3">
                <label class="form-label fw-bold">
                    1. Apakah prosedur penarikan pangan dan proses penarikan dapat dilaksanakan sesuai prosedur?
                </label>

                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        name="evaluasi[prosedur_sesuai]"
                        value="YA"
                        id="prosedur_ya"
                        {{ old('evaluasi.prosedur_sesuai', $evaluasi['prosedur_sesuai'] ?? '') == 'YA' ? 'checked' : '' }}>
                    <label class="form-check-label" for="prosedur_ya">YA</label>
                </div>

                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        name="evaluasi[prosedur_sesuai]"
                        value="TIDAK"
                        id="prosedur_tidak"
                        {{ old('evaluasi.prosedur_sesuai', $evaluasi['prosedur_sesuai'] ?? '') == 'TIDAK' ? 'checked' : '' }}>
                    <label class="form-check-label" for="prosedur_tidak">TIDAK</label>
                </div>
            </div>

            {{-- =================== JIKA TIDAK =================== --}}
            <div class="mb-3">
                <label class="form-label">
                    A. Apakah penyebab ketidaksesuaian?
                </label>

                <textarea
                    name="evaluasi[penyebab_ketidaksesuaian]"
                    class="form-control"
                    rows="3"
                    placeholder="Jelaskan penyebab ketidaksesuaian jika ada">{{ old('evaluasi.penyebab_ketidaksesuaian', $evaluasi['penyebab_ketidaksesuaian'] ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">
                    B. Jelaskan langkah yang dilakukan sebagai perbaikan
                </label>

                <textarea
                    name="evaluasi[tindakan_perbaikan]"
                    class="form-control"
                    rows="3"
                    placeholder="Jelaskan tindakan perbaikan yang dilakukan">{{ old('evaluasi.tindakan_perbaikan', $evaluasi['tindakan_perbaikan'] ?? '') }}</textarea>
            </div>

            <hr>

            {{-- =================== PERTANYAAN 2 =================== --}}
            <div class="mb-3">
                <label class="form-label fw-bold">
                    2. Apakah ada perubahan antara prosedur dan pelaksanaan penarikan?
                </label>

                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        name="evaluasi[ada_perubahan]"
                        value="YA"
                        id="perubahan_ya"
                        {{ old('evaluasi.ada_perubahan', $evaluasi['ada_perubahan'] ?? '') == 'YA' ? 'checked' : '' }}>
                    <label class="form-check-label" for="perubahan_ya">YA</label>
                </div>

                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        name="evaluasi[ada_perubahan]"
                        value="TIDAK"
                        id="perubahan_tidak"
                        {{ old('evaluasi.ada_perubahan', $evaluasi['ada_perubahan'] ?? '') == 'TIDAK' ? 'checked' : '' }}>
                    <label class="form-check-label" for="perubahan_tidak">TIDAK</label>
                </div>
            </div>

            {{-- =================== JIKA YA =================== --}}
            <div class="mb-3">
                <label class="form-label">
                    Jika YA, jelaskan hal apa yang berubah
                </label>

                <textarea
                    name="evaluasi[detail_perubahan]"
                    class="form-control"
                    rows="3"
                    placeholder="Jelaskan perubahan yang terjadi">{{ old('evaluasi.detail_perubahan', $evaluasi['detail_perubahan'] ?? '') }}</textarea>
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