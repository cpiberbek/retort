@extends('layouts.app')

@push('styles')
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

    {{-- 1. ALERT SECTION --}}
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
        <h2 class="h4">Daftar Pemeriksaan Packaging</h2>
        <div class="btn-group" role="group">
            @can('can access add button')
            <a href="{{ route('packaging-inspections.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            @endcan
            @can('can access export')
            <a href="{{ route('packaging-inspections.exportPdf', ['start_date' => request('start_date'), 'shift' => request('shift')]) }}" target="_blank" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            @endcan
            @can('can access recycle')
            <a href="{{ route('packaging-inspections.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- 2. FILTER SECTION --}}
    <form id="filterForm" method="GET" action="{{ route('packaging-inspections.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
        <div class="row w-100">
            <div class="col-md-4">
                <div class="mb-1">Pilih Tanggal</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                    </div>
                    <input type="date" name="start_date" id="start_date" class="form-control border-start-0 ps-1"
                    value="{{ request('start_date') }}" placeholder="dd/mm/yyyy">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">Pilih Shift</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-clock text-muted"></i>
                        </span>
                    </div>
                    <select name="shift" id="shift" class="form-select form-control border-start-0 ps-1">
                        <option value="">Semua Shift</option>
                        <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                        <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">Cari Data</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" name="search" id="search" class="form-control border-start-0 ps-1"
                    value="{{ request('search') }}" placeholder="Cari Data..">
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            {{-- 3. TABEL DATA --}}
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-secondary text-center"> 
                        <tr>
                            <th width="5%">NO.</th>
                            <th>Tanggal Inspeksi</th>
                            <th>Shift</th>
                            <th>Status SPV</th> {{-- Tambahan Kolom Status --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inspections as $inspection)
                        <tr>
                            <td class="text-center">{{ $loop->iteration + ($inspections->currentPage() - 1) * $inspections->perPage() }}</td>
                            
                            {{-- Tanggal --}}
                            <td class="text-center align-middle">
                                {{ $inspection->inspection_date ? \Carbon\Carbon::parse($inspection->inspection_date)->format('d-m-Y') : '-' }}
                            </td>

                            {{-- Shift --}}
                            <td class="text-center align-middle">
                                <span class="badge bg-light text-dark border">
                                    Shift {{ $inspection->shift }}
                                </span>
                            </td>

                            {{-- Status SPV --}}
                            <td class="text-center align-middle">
                                @if($inspection->status_spv == 1)
                                <span class="badge-status status-verified"><i class="fas fa-check-circle me-1"></i>Verified</span>
                                @elseif($inspection->status_spv == 2)
                                <span class="badge-status status-revision"><i class="fas fa-exclamation-circle me-1"></i>Revision</span>
                                @else
                                <span class="badge-status status-pending"><i class="fas fa-clock me-1"></i>Pending</span>
                                @endif
                            </td>
                            
                            {{-- Aksi --}}
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center align-items-center">

                                    {{-- 1. Tombol Verifikasi (Memicu Modal) --}}
                                    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm mx-1" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $inspection->uuid }}">
                                        <i class="bi bi-shield-check me-1"></i> Verifikasi
                                    </button>

                                    {{-- 2. Detail --}}
                                    <a href="{{ route('packaging-inspections.show', $inspection) }}" class="btn btn-outline-primary btn-sm mx-1" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- 3. Edit (Warning) --}}
                                    <a href="{{ route('packaging-inspections.edit', $inspection) }}" class="btn btn-warning btn-sm mx-1" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i> Edit Data
                                    </a>

                                    {{-- 4. Update/Lengkapi (Info) --}}
                                    <a href="{{ route('packaging-inspections.edit-for-update', $inspection) }}" class="btn btn-info btn-sm mx-1 text-white" title="Lengkapi Data">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>

                                    {{-- 5. Hapus --}}
                                    <form action="{{ route('packaging-inspections.destroy', $inspection) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data pemeriksaan packaging.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {!! $inspections->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>
</div>

{{-- 
=======================================================
MODAL VERIFIKASI (LOOPING DI BAGIAN BAWAH HALAMAN)
=======================================================
--}}
@foreach ($inspections as $inspection)
<div class="modal fade modal-verification" id="verifyModal{{ $inspection->uuid }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $inspection->uuid }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Form Mengarah ke Route 'packaging-inspections.verify' --}}
            <form action="{{ route('packaging-inspections.verify', $inspection->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bolder fs-4 text-uppercase" id="verifyModalLabel{{ $inspection->uuid }}"><i class="bi bi-gear-fill me-2"></i>Verification</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-light mb-4">Pastikan data yang akan diverifikasi di-check dengan teliti terlebih dahulu.</p>

                    <div class="mb-4">
                        <label for="status_spv_{{ $inspection->uuid }}" class="form-label fw-bold d-block text-center mb-2">Pilih Status Verifikasi</label>
                        <select name="status_spv" id="status_spv_{{ $inspection->uuid }}" class="form-select form-select-lg fw-bold text-center mx-auto form-select-custom" style="width: 85%;" required>
                            <option value="1" {{ old('status_spv', $inspection->status_spv) != 2 ? 'selected' : '' }} style="color: #198754;">✅ Verified (Disetujui)</option>
                            <option value="2" {{ old('status_spv', $inspection->status_spv) == 2 ? 'selected' : '' }} style="color: #dc3545;">❌ Revision (Perlu Perbaikan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan_spv_{{ $inspection->uuid }}" class="form-label fw-bold">Catatan Verifikasi (Wajib jika 'Revision')</label>
                        <textarea name="catatan_spv" id="catatan_spv_{{ $inspection->uuid }}" rows="4"
                            class="form-control shadow-none textarea-custom"
                            placeholder="Tuliskan catatan perbaikan...">{{ old('catatan_spv', $inspection->catatan_spv) }}</textarea>
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

    {{-- Auto-hide alert & Search Script --}}
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

        // Filter Logic
            const startInfo = document.getElementById('start_date');
            const shiftInfo = document.getElementById('shift');
            const searchInfo = document.getElementById('search');
            const form = document.getElementById('filterForm');
            let timer;

            const autoSubmit = () => form.submit();

            startInfo.addEventListener('change', autoSubmit);
            shiftInfo.addEventListener('change', autoSubmit);

            searchInfo.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(autoSubmit, 500);
            });
        });
    </script>
    @endsection