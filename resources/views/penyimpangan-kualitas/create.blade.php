@extends('layouts.app')

@section('title', 'Buat Laporan Penyimpangan Kualitas')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    /* Style kustom dari UI/UX sebelumnya */
    body { background-color: #f8f9fa; }
    .form-label { font-weight: 600; color: #495057; }
    .form-control, .form-select { border-radius: 8px; }
    .card-header { border-radius: 8px 8px 0 0 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            {{-- Header --}}
            <h4 class="mb-1"><i class="bi bi-file-earmark-plus-fill"></i> Buat Laporan Penyimpangan Baru</h4>
            <p class="text-muted mb-4">Isi semua detail yang diperlukan di bawah ini.</p>

            {{-- Tampilkan error jika ada --}}
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">Terjadi Kesalahan!</h4>
                    <p>Silakan periksa kembali input Anda:</p>
                    <hr>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('penyimpangan-kualitas.store') }}" method="POST">
                @csrf

                {{-- CARD 1: INFORMASI HEADER --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Laporan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="nomor" class="form-label">Nomor <span class="text-danger">*</span></label>
                                <input type="text" name="nomor" id="nomor" class="form-control" 
                                       value="{{ old('nomor') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal" class="form-label">Hari/Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control"
                                       value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="ditujukan_untuk" class="form-label">Ditujukan Untuk <span class="text-danger">*</span></label>
                                <input type="text" name="ditujukan_untuk" id="ditujukan_untuk" class="form-control"
                                       value="{{ old('ditujukan_untuk') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: DETAIL PENYIMPANGAN (DARI TABEL) --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-box-seam-fill"></i> Detail Varian Penyimpangan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                             <div class="col-md-6">
                                <label for="nama_produk" class="form-label">Nama Varian <span class="text-danger">*</span></label>
                                <input type="text" name="nama_produk" id="nama_produk" class="form-control" 
                                       placeholder="Nama varian yang menyimpang"
                                       value="{{ old('nama_produk') }}" required>
                            </div>
                             <div class="col-md-3">
                                <label for="lot_kode" class="form-label">Lot/Kode</label>
                                <input type="text" name="lot_kode" id="lot_kode" class="form-control" 
                                       value="{{ old('lot_kode') }}">
                            </div>
                             <div class="col-md-3">
                                <label for="jumlah" class="form-label">Jumlah (Karton)</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" value="{{ old('jumlah') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 3: HASIL & PENYELESAIAN (TEXTAREA) --}}
                <div class="card mb-4"> 
                    <div class="card-header">
                        <strong><i class="bi bi-card-text"></i> Keterangan dan Penyelesaian</strong>
                    </div>
                    <div class="card-body">
                         <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Jelaskan hasil penyimpangan...">{{ old('keterangan') }}</textarea>
                        </div>
                         <div class="mb-3">
                            <label for="penyelesaian" class="form-label">Penyelesaian</label>
                            <textarea name="penyelesaian" id="penyelesaian" class="form-control" rows="3" placeholder="Tindakan penyelesaian yang diambil...">{{ old('penyelesaian') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Simpan Data</button>
                    <a href="{{ route('penyimpangan-kualitas.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Batal</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection