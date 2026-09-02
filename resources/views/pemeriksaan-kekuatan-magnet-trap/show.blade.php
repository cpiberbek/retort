@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Kekuatan Magnet Trap')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    /* Style kustom dari CRUD sebelumnya */
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
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--color-gray);
    }
    .page-header h1 { margin: 0; color: #333; font-size: 1.75rem; }
    .page-header-actions { display: flex; gap: 10px; }
    .btn-custom {
        padding: 0.6rem 1rem;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    .btn-custom.btn-warning { background-color: var(--color-warning); color: #212529; }
    .btn-custom.btn-warning:hover { background-color: #ffca2c; }
    .btn-custom.btn-secondary {
        background-color: transparent;
        color: #6c757d;
        border: 1px solid #6c757d;
    }
    .btn-custom.btn-secondary:hover { background-color: rgba(0,0,0, 0.05); }
    .detail-card {
        border: 1px solid var(--color-gray);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-radius: var(--border-radius);
        background-color: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .detail-card h2 {
        margin-top: 0;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--color-gray);
        color: var(--color-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1rem 2rem;
    }
    .detail-grid-item {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 5px;
        padding: 5px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .detail-grid-item strong {
        font-weight: 600;
        color: #333;
    }
    .detail-grid-item span { color: #555; }
    .text-block-item { margin-bottom: 1.5rem; }
    .text-block-item h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .text-block-item-content {
        background-color: var(--color-light-gray, #f8f9fa);
        padding: 1rem;
        border-radius: var(--border-radius, 0.375rem);
        border: 1px solid var(--color-gray, #dee2e6);
        white-space: pre-wrap;
    }
</style>
@endpush

@section('content')
<div class="page-container">
    {{-- HEADER HALAMAN BARU --}}
    <div class="page-header">
        <h1><i class="bi bi-magnet-fill" style="color: var(--color-primary);"></i> Detail Pemeriksaan Kekuatan Magnet Trap</h1>
        <div class="page-header-actions">
            {{-- Route name diubah --}}
            <a href="{{ route('pemeriksaan-kekuatan-magnet-trap.index') }}" class="btn-custom btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            {{-- Route name & variabel diubah --}}
            <a href="{{ route('pemeriksaan-kekuatan-magnet-trap.edit', $pemeriksaanKekuatanMagnetTrap->id) }}" class="btn-custom btn-warning"><i class="bi bi-pencil"></i> Edit Data</a>
        </div>
    </div>

    {{-- CARD 1: Data Utama --}}
    <div class="detail-card">
        <h2><i class="bi bi-info-circle-fill"></i> Data Utama</h2>
        <div class="detail-grid">
            {{-- Variabel diubah --}}
            <div class="detail-grid-item">
                <strong>Tanggal:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->tanggal->format('d F Y') }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Magnet Ke:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->magnet_ke }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Kondisi Visual:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->kondisi_magnet_trap }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Petugas QC:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->petugas_qc ?? '-' }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Petugas ENG:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->petugas_eng ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- CARD 2: Hasil Pengecekan --}}
    <div class="detail-card">
        <h2><i class="bi bi-check2-circle"></i> Hasil Pengecekan</h2>

        <h4 style="font-size: 1.1rem; font-weight: 600;">Kekuatan Medan Magnet (Gauss)</h4>
        <div class="detail-grid mb-3">
             <div class="detail-grid-item">
                {{-- Variabel diubah --}}
                <strong>Magnet 1:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->kekuatan_median_1 ? number_format($pemeriksaanKekuatanMagnetTrap->kekuatan_median_1, 2) . ' G' : '-' }}</span>
            </div>
            <div class="detail-grid-item">
                {{-- Variabel diubah --}}
                <strong>Magnet 2:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->kekuatan_median_2 ? number_format($pemeriksaanKekuatanMagnetTrap->kekuatan_median_2, 2) . ' G' : '-' }}</span>
            </div>
            <div class="detail-grid-item">
                {{-- Variabel diubah --}}
                <strong>Magnet 3:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->kekuatan_median_3 ? number_format($pemeriksaanKekuatanMagnetTrap->kekuatan_median_3, 2) . ' G' : '-' }}</span>
            </div>
        </div>

        <h4 style="margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 600; font-size: 1.1rem;">Parameter Setingan:</h4>
        <div>
            {{-- Variabel diubah --}}
            @if($pemeriksaanKekuatanMagnetTrap->parameter_sesuai)
                <span class="badge bg-success fs-6"><i class="bi bi-check-lg"></i> Sesuai (√)</span>
            @else
                <span class="badge bg-danger fs-6"><i class="bi bi-x-lg"></i> Tidak Sesuai (X)</span>
            @endif
        </div>
    </div>

    {{-- CARD 3: Status Verifikasi --}}
    <div class="detail-card">
        <h2><i class="bi bi-check-all"></i> Status Verifikasi (SPV QC)</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="detail-grid-item">
                    <strong>Status:</strong>
                    <span>
                        {{-- Variabel diubah --}}
                        @if($pemeriksaanKekuatanMagnetTrap->status_spv == 0) <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($pemeriksaanKekuatanMagnetTrap->status_spv == 1) <span class="badge bg-success">Verified</span>
                        @else <span class="badge bg-danger">Revision</span>
                        @endif
                    </span>
                </div>
                <div class="detail-grid-item">
                    {{-- Variabel diubah --}}
                    <strong>Oleh:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->verifier->name ?? '-' }}</span>
                </div>
                <div class="detail-grid-item">
                    {{-- Variabel diubah --}}
                    <strong>Tanggal:</strong> <span>{{ $pemeriksaanKekuatanMagnetTrap->verified_at ? $pemeriksaanKekuatanMagnetTrap->verified_at->format('d M Y, H:i') : '-' }}</span>
                </div>
                <div class="text-block-item mt-3">
                    <h4>Catatan Verifikasi</h4>
                    {{-- Variabel diubah --}}
                    <div class="text-block-item-content">{{ $pemeriksaanKekuatanMagnetTrap->catatan_spv ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 4: Keterangan & Audit Trail --}}
    <div class="detail-card">
        <h2><i class="bi bi-paperclip"></i> Catatan Audit</h2>

        <div class="text-block-item mb-0">
            <h4>Keterangan</h4>
            {{-- Variabel diubah --}}
            <div class="text-block-item-content">{{ $pemeriksaanKekuatanMagnetTrap->keterangan ?? '-' }}</div>
        </div>

        {{-- Footer Timestamp --}}
        <div class="card-footer bg-light text-muted" style="margin: 1.5rem -1.5rem -1.5rem; border-top: 1px solid var(--color-gray);">
            <div class="d-flex justify-content-between flex-wrap small p-3">
                {{-- Variabel diubah --}}
                <span><strong>Dibuat Oleh:</strong> {{ $pemeriksaanKekuatanMagnetTrap->creator->name ?? 'N/A' }}</span>
                <span><strong>Dibuat Pada:</strong> {{ $pemeriksaanKekuatanMagnetTrap->created_at->format('d M Y, H:i') }}</span>
                <span><strong>Diperbarui:</strong> {{ $pemeriksaanKekuatanMagnetTrap->updated_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
