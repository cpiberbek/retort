@extends('layouts.app')

{{-- Style ini idealnya ada di file CSS terpusat --}}
@push('styles')
<style>
    :root {
        --color-primary: #007bff;
        --color-success: #28a745;
        --color-danger: #dc3545;
        --color-info: #17a2b8;
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
    .page-header h1 { margin: 0; color: #333; }
    .page-header-actions { display: flex; gap: 10px; }

    /* Tombol */
    .btn {
        padding: 0.6rem 1rem;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s ease;
        color: white;
    }
    .btn-primary { background-color: var(--color-primary); }
    .btn-primary:hover { background-color: #0069d9; }
    .btn-secondary {
        background-color: transparent;
        color: var(--color-primary);
        border: 1px solid var(--color-primary);
    }
    .btn-secondary:hover { background-color: rgba(0,123,255, 0.05); }

    /* Detail Card */
    .detail-card { 
        border: 1px solid var(--color-gray); 
        padding: 1.5rem; 
        margin-bottom: 1.5rem; 
        border-radius: var(--border-radius); 
        background-color: #fff;
    }
    .detail-card h2 { 
        margin-top: 0; 
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--color-gray);
        color: var(--color-primary);
    }
    .detail-grid { 
        display: grid; 
        grid-template-columns: 180px 1fr; 
        gap: 12px; 
    }
    .detail-grid strong { 
        font-weight: 600; 
        color: #333;
    }
    .detail-grid span { color: #555; }
    
    /* Tabel Data (untuk item) */
    .items-table-container { overflow-x: auto; width: 100%; }
    .items-table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 10px; 
        min-width: 1200px;
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
    
    .status-ok {
        color: var(--color-success);
        font-weight: 700;
        background-color: #d4edda;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .status-tolak {
        color: var(--color-danger);
        font-weight: 700;
        background-color: #f8d7da;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1>Detail Inspeksi #{{ $packagingInspection->id }}</h1>
        <div class="page-header-actions">
            <a href="{{ route('packaging-inspections.index') }}" class="btn btn-secondary">&larr; Kembali ke Daftar</a>
            @can('can access edit button')
            <a href="{{ route('packaging-inspections.edit', $packagingInspection) }}" class="btn btn-primary">Edit Data</a>
            @endcan
        </div>
    </div>

    {{-- Data Header (Induk) --}}
    <div class="detail-card">
        <h2>Data Inspeksi</h2>
        <div class="detail-grid">
            <strong>Tanggal Inspeksi:</strong> <span>{{ $packagingInspection->inspection_date }}</span>
            <strong>Shift:</strong>           <span>{{ $packagingInspection->shift }}</span>
        </div>
    </div>

    {{-- Data Detail (Item) --}}
    <div class="detail-card">
        <h2>Detail Item</h2>
        <div class="items-table-container">
            <table class="items-table">
                <thead>
                    <tr>
                        {{-- URUTAN BARU --}}
                        <th>Jenis Packaging</th>
                        <th>Supplier</th>
                        <th>Lot Batch</th>
                        <th>Design</th>
                        <th>Sealing</th>
                        <th>Warna</th>
                        <th>Dimensi</th>
                        <th>Berat</th>
                        <th>Qty Barang</th>
                        <th>Qty Sampel</th>
                        <th>Qty Reject</th>
                        <th>Penerimaan</th>
                        <th>No. Polisi</th>
                        <th>Kondisi Kendaraan</th>
                        <th>PBB / OP</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packagingInspection->items as $item)
                        <tr>
                            {{-- URUTAN BARU --}}
                            <td>{{ $item->packaging_type }}</td>
                            <td>{{ $item->supplier }}</td>
                            <td>{{ $item->lot_batch }}</td>
                            <td>
                                {{-- PERUBAHAN: Tambah span status --}}
                                <span class="{{ $item->condition_design == 'OK' ? 'status-ok' : 'status-tolak' }}">
                                    {{ $item->condition_design }}
                                </span>
                            </td>
                            <td>
                                {{-- PERUBAHAN: Tambah span status --}}
                                <span class="{{ $item->condition_sealing == 'OK' ? 'status-ok' : 'status-tolak' }}">
                                    {{ $item->condition_sealing }}
                                </span>
                            </td>
                            <td>
                                {{-- PERUBAHAN: Tambah span status --}}
                                <span class="{{ $item->condition_color == 'OK' ? 'status-ok' : 'status-tolak' }}">
                                    {{ $item->condition_color }}
                                </span>
                            </td>
                            <td>{{ $item->condition_dimension ?? '-' }}</td>
                            <td>{{ $item->condition_weight ?? '-' }} gr</td>
                            <td>{{ $item->quantity_goods }}</td>
                            <td>{{ $item->quantity_sample }}</td>
                            <td>{{ $item->quantity_reject }}</td>
                            <td>
                                <span class="{{ $item->acceptance_status == 'OK' ? 'status-ok' : 'status-tolak' }}">
                                    {{ $item->acceptance_status }}
                                </span>
                            </td>
                            <td>{{ $item->no_pol }}</td>
                            <td>{{ $item->vehicle_condition }}</td>
                            <td>{{ $item->pbb_op ?? '-' }}</td>
                            <td>{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" style="text-align: center; padding: 2rem;">Tidak ada item untuk inspeksi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

