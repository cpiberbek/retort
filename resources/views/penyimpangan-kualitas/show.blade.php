@extends('layouts.app')

@section('title', 'Detail Laporan Penyimpangan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    /* Style kustom dari UI/UX sebelumnya */
    :root {
        --color-primary: #007bff;
        --color-success: #28a745;
        --color-danger: #dc3545;
        --color-warning: #ffc107;
        --color-light-gray: #f8f9fa;
        --color-gray: #dee2e6;
        --border-radius: 0.375rem;
    }
    .page-container { padding: 20px; max-width: 1200px; margin: 0 auto; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-gray); }
    .page-header h1 { margin: 0; color: #333; font-size: 1.75rem; }
    .page-header-actions { display: flex; gap: 10px; }
    .btn-custom { padding: 0.6rem 1rem; border: none; border-radius: var(--border-radius); cursor: pointer; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s ease; }
    .btn-custom.btn-warning { background-color: var(--color-warning); color: #212529; }
    .btn-custom.btn-warning:hover { background-color: #ffca2c; }
    .btn-custom.btn-secondary { background-color: transparent; color: #6c757d; border: 1px solid #6c757d; }
    .btn-custom.btn-secondary:hover { background-color: rgba(0,0,0, 0.05); }
    .detail-card { border: 1px solid var(--color-gray); padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--border-radius); background-color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .detail-card h2 { margin-top: 0; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--color-gray); color: var(--color-primary); display: flex; align-items: center; gap: 0.5rem; }
    .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem 2rem; }
    .detail-grid-item { display: grid; grid-template-columns: 140px 1fr; gap: 5px; padding: 5px 0; border-bottom: 1px solid #f0f0f0; }
    .detail-grid-item strong { font-weight: 600; color: #333; }
    .detail-grid-item span { color: #555; }
    .text-block-item { margin-bottom: 1.5rem; }
    .text-block-item h4 { font-size: 1.1rem; font-weight: 600; color: #333; margin-bottom: 0.5rem; }
    .text-block-item-content { background-color: var(--color-light-gray, #f8f9fa); padding: 1rem; border-radius: var(--border-radius, 0.375rem); border: 1px solid var(--color-gray, #dee2e6); white-space: pre-wrap; }
</style>
@endpush

@section('content')
<div class="page-container">
    {{-- HEADER HALAMAN BARU --}}
    <div class="page-header">
        <h1><i class="bi bi-file-earmark-text" style="color: var(--color-primary);"></i> Detail Laporan Penyimpangan</h1>
        <div class="page-header-actions">
            <a href="{{ route('penyimpangan-kualitas.index') }}" class="btn-custom btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            <a href="{{ route('penyimpangan-kualitas.edit', $penyimpanganKualitas->id) }}" class="btn-custom btn-warning"><i class="bi bi-pencil"></i> Edit Data</a>
        </div>
    </div>

    {{-- CARD 1: Data Utama --}}
    <div class="detail-card">
        <h2><i class="bi bi-info-circle-fill"></i> Data Utama</h2>
        <div class="detail-grid">
            <div class="detail-grid-item">
                <strong>Nomor:</strong> <span>{{ $penyimpanganKualitas->nomor }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Tanggal:</strong> <span>{{ $penyimpanganKualitas->tanggal->format('d F Y') }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Ditujukan Untuk:</strong> <span>{{ $penyimpanganKualitas->ditujukan_untuk }}</span>
            </div>
        </div>
    </div>
    
    {{-- CARD 2: Detail Produk --}}
    <div class="detail-card">
        <h2><i class="bi bi-box-seam-fill"></i> Detail Varian</h2>
        <div class="detail-grid">
            <div class="detail-grid-item">
                <strong>Nama Varian:</strong> <span>{{ $penyimpanganKualitas->nama_produk }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Lot/Kode:</strong> <span>{{ $penyimpanganKualitas->lot_kode ?? '-' }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Jumlah:</strong> <span>{{ $penyimpanganKualitas->jumlah ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- CARD 3: Keterangan & Penyelesaian --}}
    <div class="detail-card">
        <h2><i class="bi bi-card-text"></i> Keterangan & Penyelesaian</h2>
        <div class="text-block-item">
            <h4>Keterangan</h4>
            <div class="text-block-item-content">{{ $penyimpanganKualitas->keterangan ?? '-' }}</div>
        </div>
        <div class="text-block-item mb-0">
            <h4>Penyelesaian</h4>
            <div class="text-block-item-content">{{ $penyimpanganKualitas->penyelesaian ?? '-' }}</div>
        </div>
    </div>
    
    {{-- CARD 4: Status Verifikasi --}}
    <div class="detail-card">
        <h2><i class="bi bi-check-all"></i> Status Verifikasi</h2>
        <div class="row">
            {{-- Verifikasi Tahap 1 --}}
            <div class="col-md-6">
                <h4><i class="bi bi-person-workspace"></i> Diketahui Oleh</h4>
                <div class="detail-grid-item">
                    <strong>Status:</strong> 
                    <span>
                        @if($penyimpanganKualitas->status_diketahui == 0) <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($penyimpanganKualitas->status_diketahui == 1) <span class="badge bg-success">Verified</span>
                        @else <span class="badge bg-danger">Revision</span>
                        @endif
                    </span>
                </div>
                <div class="detail-grid-item">
                    <strong>Oleh:</strong> <span>{{ $penyimpanganKualitas->verifierDiketahui->name ?? '-' }}</span>
                </div>
                <div class="detail-grid-item">
                    <strong>Tanggal:</strong> <span>{{ $penyimpanganKualitas->diketahui_at ? $penyimpanganKualitas->diketahui_at->format('d M Y, H:i') : '-' }}</span>
                </div>
                <div class="text-block-item mt-3">
                    <h4>Catatan "Diketahui"</h4>
                    <div class="text-block-item-content">{{ $penyimpanganKualitas->catatan_diketahui ?? '-' }}</div>
                </div>
            </div>
            {{-- Verifikasi Tahap 2 --}}
            <div class="col-md-6 mt-4 mt-md-0">
                <h4><i class="bi bi-person-video3"></i> Disetujui Oleh</h4>
                <div class="detail-grid-item">
                    <strong>Status:</strong> 
                    <span>
                        @if($penyimpanganKualitas->status_disetujui == 0) <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($penyimpanganKualitas->status_disetujui == 1) <span class="badge bg-success">Verified</span>
                        @else <span class="badge bg-danger">Revision</span>
                        @endif
                    </span>
                </div>
                <div class="detail-grid-item">
                    <strong>Oleh:</strong> <span>{{ $penyimpanganKualitas->verifierDisetujui->name ?? '-' }}</span>
                </div>
                <div class="detail-grid-item">
                    <strong>Tanggal:</strong> <span>{{ $penyimpanganKualitas->disetujui_at ? $penyimpanganKualitas->disetujui_at->format('d M Y, H:i') : '-' }}</span>
                </div>
                 <div class="text-block-item mt-3">
                    <h4>Catatan "Disetujui"</h4>
                    <div class="text-block-item-content">{{ $penyimpanganKualitas->catatan_disetujui ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- CARD 5: Audit Trail --}}
    <div class="detail-card">
        <h2><i class="bi bi-clock-history"></i> Catatan Audit</h2>
        {{-- Footer Timestamp --}}
        <div class="card-footer bg-light text-muted" style="margin: -1.5rem; border-top: 1px solid var(--color-gray);">
            <div class="d-flex justify-content-between flex-wrap small p-3">
                <span><strong>Dibuat Oleh:</strong> {{ $penyimpanganKualitas->creator->name ?? 'N/A' }}</span>
                <span><strong>Dibuat Pada:</strong> {{ $penyimpanganKualitas->created_at->format('d M Y, H:i') }}</span>
                <span><strong>Diperbarui:</strong> {{ $penyimpanganKualitas->updated_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection