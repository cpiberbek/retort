@extends('layouts.app')

@section('title', 'Daftar Pemeriksaan Loading')

@push('styles')
{{-- Library Icons --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    /* --- 1. Style Global & Tabel --- */
    body { background-color: #f8f9fa; }
    .table td, .table th { font-size: 0.85rem; white-space: nowrap; vertical-align: middle; }
    .card-custom { border: none; border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
    .input-group:focus-within { box-shadow: none; }
    .form-control:focus, .input-group-text:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); border-color: #86b7fe; }

    /* Status Badge Style */
    .badge-status { padding: 0.5em 0.75em; font-size: 0.75rem; font-weight: 600; border-radius: 50rem; }
    .status-pending { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
    .status-verified { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .status-revision { background-color: rgba(220, 53, 69, 0.1); color: #DC3545; }

    /* --- 2. Style Khusus Modal Verifikasi --- */
    :root {
        --primary-brand-dark: #7a1f12;
        --primary-brand-light: #9E3419;
        --accent-color-1: #E39581;
        --accent-color-2: #FFE5DE;
        --text-on-dark: #FFFFFF;
        --text-on-light: #2c3e50;
    }
    .modal-verification .modal-content { 
        border: none; border-radius: 1rem; 
        background: linear-gradient(145deg, var(--primary-brand-dark), var(--primary-brand-light)); 
        box-shadow: 0 15px 40px rgba(0,0,0,0.5); 
        color: var(--text-on-dark); overflow: hidden; 
    }
    .modal-verification .modal-header { border-bottom: 2px solid rgba(255, 255, 255, 0.2); padding: 1.5rem; }
    .modal-verification .modal-body { padding: 2rem; }
    .modal-verification .modal-footer { background-color: var(--primary-brand-light); border-top: 1px solid rgba(255, 255, 255, 0.2); padding: 1rem 1.5rem; }
    .modal-verification .form-select-custom { 
        background: linear-gradient(135deg, #fff1f0, var(--accent-color-2)); 
        border: 2px solid #dc3545; border-radius: 12px; color: #7a1f12; font-size: 1.1rem; 
        box-shadow: 0 6px 12px rgba(0,0,0,0.1); transition: all 0.3s ease; 
    }
    .modal-verification .textarea-custom { background-color: var(--accent-color-2); color: var(--text-on-light); border: none; border-radius: 0.5rem; height: 120px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-0">

    {{-- Alert Section --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Daftar Pemeriksaan Loading</h2>
        <div class="btn-group" role="group">
            @can('can access add button')
            <a href="{{ route('loading-produks.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            @endcan
            @can('can access export')
            <a href="{{ route('loading-produks.exportPdf', ['date' => request('date'), 'shift' => request('shift')]) }}" target="_blank" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            @endcan
            @can('can access recycle')
            <a href="{{ route('loading-produks.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- Filter dan Live Search --}}
    <form id="filterForm" method="GET" action="{{ route('loading-produks.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
        <div class="row w-100">
            <div class="col-md-4">
                <div class="mb-1">Pilih Tanggal</div>
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>
                    <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                    value="{{ request('date') ?? request('start_date') }}" placeholder="Tanggal">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">Pilih Shift</div>
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-clock text-muted"></i>
                    </span>
                    <select name="shift" id="shift" class="form-select form-control border-start-0">
                        <option value="">Semua Shift</option>
                        <option value="Pagi" {{ request('shift') == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                        <option value="Malam" {{ request('shift') == 'Malam' ? 'selected' : '' }}>Malam</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">Cari Data</div>
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" id="search" class="form-control border-start-0"
                    value="{{ request('search') }}" placeholder="Cari No. Pol / Supir / Ekspedisi...">
                </div>
            </div>
        </div>
    </form>

    <div class="card card-custom mb-4">
        <div class="card-body">
            {{-- Tabel Data --}}
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Aktivitas</th>
                            <th>No. Pol Mobil</th>
                            <th>Nama Supir</th>
                            <th>Ekspedisi</th>
                            <th>Status SPV</th> {{-- Kolom Status Baru --}}
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produks as $produk)
                        <tr>
                            {{-- Penomoran --}}
                            <td class="text-center">{{ $loop->iteration + ($produks->currentPage() - 1) * $produks->perPage() }}</td>
                            
                            {{-- Tanggal --}}
                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($produk->tanggal)->format('d-m-Y') }}
                            </td>

                            {{-- Data Columns --}}
                            <td class="text-center align-middle">{{ $produk->shift }}</td>
                            <td class="align-middle">{{ $produk->jenis_aktivitas }}</td>
                            <td class="text-center align-middle fw-bold">{{ $produk->no_pol_mobil }}</td>
                            <td class="align-middle">{{ $produk->nama_supir }}</td>
                            <td class="align-middle">{{ $produk->ekspedisi }}</td>

                            {{-- Kolom Status SPV --}}
                            <td class="text-center align-middle">
                                @if(isset($produk->status_spv))
                                @if($produk->status_spv == 1)
                                <span class="badge-status status-verified"><i class="fas fa-check-circle me-1"></i>Verified</span>
                                @elseif($produk->status_spv == 2)
                                <span class="badge-status status-revision"><i class="fas fa-exclamation-circle me-1"></i>Revision</span>
                                @else
                                <span class="badge-status status-pending"><i class="fas fa-clock me-1"></i>Pending</span>
                                @endif
                                @else
                                <span class="badge-status status-pending">-</span>
                                @endif
                            </td>
                            
                            {{-- Creator --}}
                            <td class="text-center align-middle">{{ $produk->creator->name ?? 'N/A' }}</td>
                            
                            {{-- Aksi --}}
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center align-items-center">

                                    {{-- 0. Tombol Verifikasi (New) --}}
                                    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm mx-1" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $produk->uuid }}">
                                        <i class="bi bi-shield-check me-1"></i> Verifikasi
                                    </button>

                                    {{-- 1. Detail --}}
                                    <a href="{{ route('loading-produks.show', $produk->uuid) }}" class="btn btn-outline-primary btn-sm mx-1" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    {{-- 2. Edit --}}
                                    <a href="{{ route('loading-produks.edit', $produk->uuid) }}" class="btn btn-warning btn-sm mx-1" title="Edit">
                                        <i class="bi bi-pencil-square"></i> Edit Data
                                    </a>

                                    {{-- 3. Update (Lengkapi Data) --}}
                                    <a href="{{ route('loading-produks.edit-details', $produk->uuid) }}" class="btn btn-info btn-sm mx-1 text-white" title="Lengkapi Data">
                                        <i class="bi bi-pencil"></i> Update Data
                                    </a>

                                    {{-- 4. Hapus --}}
                                    <form action="{{ route('loading-produks.destroy', $produk->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm mx-1" title="Hapus">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                Data tidak ditemukan. Silakan tambahkan data baru atau ubah filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($produks->hasPages())
    <div class="mt-3">
        {{ $produks->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- 
=======================================================
MODAL VERIFIKASI (LOOPING)
=======================================================
--}}
@foreach ($produks as $produk)
<div class="modal fade modal-verification" id="verifyModal{{ $produk->uuid }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $produk->uuid }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Form Mengarah ke Route 'loading-produks.verify' (Pastikan route ini ada) --}}
            <form action="{{ route('loading-produks.verify', $produk->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bolder fs-4 text-uppercase" id="verifyModalLabel{{ $produk->uuid }}"><i class="bi bi-gear-fill me-2"></i>Verification</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-light mb-4">
                        Verifikasi Loading untuk:<br>
                        <strong>{{ $produk->no_pol_mobil }}</strong> ({{ $produk->nama_supir }})<br>
                        Ekspedisi: {{ $produk->ekspedisi }}
                    </p>

                    <div class="mb-4">
                        <label for="status_spv_{{ $produk->uuid }}" class="form-label fw-bold d-block text-center mb-2">Pilih Status Verifikasi</label>
                        <select name="status_spv" id="status_spv_{{ $produk->uuid }}" class="form-select form-select-lg fw-bold text-center mx-auto form-select-custom" style="width: 85%;" required>
                            @php $currentStatus = $produk->status_spv ?? null; @endphp
                            <option value="1" {{ old('status_spv', $currentStatus) != 2 ? 'selected' : '' }} style="color: #198754;">✅ Verified (Disetujui)</option>
                            <option value="2" {{ old('status_spv', $currentStatus) == 2 ? 'selected' : '' }} style="color: #dc3545;">❌ Revision (Perlu Perbaikan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan_spv_{{ $produk->uuid }}" class="form-label fw-bold">Catatan Verifikasi (Wajib jika 'Revision')</label>
                        <textarea name="catatan_spv" id="catatan_spv_{{ $produk->uuid }}" rows="4"
                            class="form-control shadow-none textarea-custom"
                            placeholder="Tuliskan catatan perbaikan...">{{ old('catatan_spv', $produk->catatan_spv ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-outline-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn rounded-pill px-5 fw-bolder" style="background-color: var(--accent-color-1); color: var(--text-on-light);"><i class="bi bi-save-fill me-1"></i> SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        // Auto hide alert
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if(alert){
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                }
            }, 3000);

        // Search & Filter
            const search = document.getElementById('search');
            const date = document.getElementById('filter_date');
            const shift = document.getElementById('shift');
            const form = document.getElementById('filterForm');
            let timer;

            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });

            date.addEventListener('change', () => form.submit());
            shift.addEventListener('change', () => form.submit());
        });
    </script>

    @endsection