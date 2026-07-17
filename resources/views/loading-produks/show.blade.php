@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Loading')

@push('styles')
{{-- Menambahkan link untuk Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    :root {
        --color-primary: #007bff;
        --color-success: #28a745;
        --color-danger: #dc3545;
        --color-info: #17a2b8;
        --color-warning: #ffc107;
        --color-light-gray: #f8f9fa;
        --color-gray: #dee2e6;
        --border-radius: 0.375rem;
    }
    .page-container { padding: 20px; max-width: 1200px; margin: 0 auto; }
    
    /* Header Halaman */
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

    /* Tombol */
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
        color: white;
    }
    .btn-custom.btn-primary { background-color: var(--color-warning); color: #212529; }
    .btn-custom.btn-primary:hover { background-color: #ffca2c; }
    .btn-custom.btn-secondary {
        background-color: transparent;
        color: #6c757d;
        border: 1px solid #6c757d;
    }
    .btn-custom.btn-secondary:hover { background-color: rgba(0,0,0, 0.05); }

    /* Detail Card */
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
        /* Sesuaikan grid untuk 2 kolom info */
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
    
    /* Grid untuk PIC */
    .pic-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        text-align: center;
        margin-top: 1rem;
    }
    .pic-item {
        background-color: var(--color-light-gray);
        padding: 1rem;
        border-radius: var(--border-radius);
    }
    .pic-item strong { display: block; margin-bottom: 0.5rem; }
    .pic-item span { color: #555; font-style: italic; }
    
    /* List Kondisi */
    .kondisi-list {
        list-style-type: none;
        padding-left: 0;
        columns: 2;
        -webkit-columns: 2;
        -moz-columns: 2;
    }
    .kondisi-list li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .kondisi-list li::before {
        content: '\F26A'; /* Bootstrap Icon Check */
        font-family: 'bootstrap-icons';
        position: absolute;
        left: 0;
        top: 2px;
        color: var(--color-success);
        font-weight: bold;
    }
    
    /* Tabel Data (untuk item) */
    .items-table-container { overflow-x: auto; width: 100%; }
    .items-table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 10px; 
        min-width: 800px;
        border: 1px solid var(--color-gray);
    }
    .items-table th, .items-table td { 
        border-bottom: 1px solid var(--color-gray); 
        padding: 0.75rem; 
        text-align: left; 
        vertical-align: middle;
    }
    .items-table th { 
        background-color: var(--color-light-gray); 
        white-space: nowrap;
        font-weight: 600;
    }
    .items-table tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }
</style>
@endpush

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1><i class="bi bi-truck-ramp-box" style="color: var(--color-primary);"></i> Detail Pemeriksaan</h1>
        <div class="page-header-actions">
            <a href="{{ route('loading-produks.index') }}" class="btn-custom btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            <a href="{{ route('loading-produks.edit', $loadingProduk->uuid) }}" class="btn-custom btn-primary"><i class="bi bi-pencil"></i> Edit Data</a>
        </div>
    </div>

    {{-- Data Header (Induk) --}}
    <div class="detail-card">
        <h2><i class="bi bi-info-circle-fill"></i> Data Utama</h2>
        <div class="detail-grid">
            <div class="detail-grid-item">
                <strong>Tanggal:</strong> <span>{{ \Carbon\Carbon::parse($loadingProduk->tanggal)->format('d F Y') }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Shift:</strong> <span>{{ $loadingProduk->shift }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Aktivitas:</strong> <span>{{ $loadingProduk->jenis_aktivitas }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Dibuat Oleh:</strong> <span>{{ $loadingProduk->creator->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Jam Mulai:</strong> <span>{{ $loadingProduk->jam_mulai }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Jam Selesai:</strong> <span>{{ $loadingProduk->jam_selesai }}</span>
            </div>
        </div>
    </div>
    
    {{-- Data Kendaraan --}}
    <div class="detail-card">
        <h2><i class="bi bi-truck"></i> Informasi Kendaraan</h2>
        <div class="detail-grid">
            <div class="detail-grid-item">
                <strong>No. Pol Mobil:</strong> <span>{{ $loadingProduk->no_pol_mobil }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Nama Supir:</strong> <span>{{ $loadingProduk->nama_supir }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Ekspedisi:</strong> <span>{{ $loadingProduk->ekspedisi }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Tujuan / Asal:</strong> <span>{{ $loadingProduk->tujuan_asal }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>No. Segel:</strong> <span>{{ $loadingProduk->no_segel ?? '-' }}</span>
            </div>
            <div class="detail-grid-item">
                <strong>Jenis Kendaraan:</strong> <span>{{ $loadingProduk->jenis_kendaraan ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Data Kondisi, Keterangan, & PIC --}}
    <div class="detail-card">
        <h2><i class="bi bi-clipboard2-check-fill"></i> Kondisi, Keterangan & PIC</h2>
        
        {{-- Menggunakan row/col Bootstrap standar di dalam card kustom --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <h4>Kondisi Mobil</h4>
                @if (!empty($loadingProduk->kondisi_mobil))
                    <ul class="kondisi-list">
                        @foreach ($loadingProduk->kondisi_mobil as $kondisi)
                            <li>{{ ucwords(str_replace('_', ' ', $kondisi)) }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted"><em>Tidak ada data checklist.</em></p>
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <h4>Keterangan</h4>
                <div class="detail-grid-item" style="border-bottom: 1px solid #eee;">
                    <strong>Total (Varian & Jumlah):</strong> 
                    <span>{{ $loadingProduk->keterangan_total ?? '-' }}</span>
                </div>
                <div class="detail-grid-item">
                    <strong>Umum (Catatan):</strong>
                    <span>{{ $loadingProduk->keterangan_umum ?? '-' }}</span>
                </div>
            </div>
        </div>

        <hr>
        <h4 class="text-center mb-3">Penanggung Jawab (PIC)</h4>
        <div class="pic-grid">
            <div class="pic-item">
                <strong>Diperiksa (QC)</strong>
                <span>{{ $loadingProduk->pic_qc ?? '(Belum Diperiksa)' }}</span>
            </div>
            <div class="pic-item">
                <strong>Diketahui (Warehouse)</strong>
                <span>{{ $loadingProduk->pic_warehouse ?? '(Belum Diketahui)' }}</span>
            </div>
            <div class="pic-item">
                <strong>Disetujui (QC SPV)</strong>
                <span>{{ $loadingProduk->pic_qc_spv ?? '(Belum Disetujui)' }}</span>
            </div>
        </div>
    </div>

    {{-- Data Detail (Item) --}}
    <div class="detail-card">
        <h2><i class="bi bi-list-nested"></i> Detail Produk yang Di-load</h2>
        <div class="items-table-container">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Nama Produk (Varian)</th>
                        <th>Kode Produksi</th>
                        <th>Kode Expired</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loadingProduk->details as $detail)
                        <tr>
                            <td>{{ $detail->nama_produk }}</td>
                            <td>{{ $detail->kode_produksi }}</td>
                            <td>{{ $detail->kode_expired ? \Carbon\Carbon::parse($detail->kode_expired)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $detail->jumlah }}{{ $detail->satuan ? ' (' . $detail->satuan . ')' : '' }}</td>
                            <td>{{ $detail->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">Tidak ada item detail untuk data ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection