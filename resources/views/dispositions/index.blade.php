@extends('layouts.app')

@section('title', 'Daftar Disposisi')

{{-- 
=======================================================
1. STYLING TAMBAHAN (Copied & Adapted from Reference)
=======================================================
--}}
@push('styles')
{{-- Library Icons --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    /* --- Style Global & Tabel --- */
    .card-custom { border: none; border-radius: 0.8rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table td, .table th { font-size: 0.9rem; padding: 0.75rem 0.5rem; vertical-align: middle; }

    /* Input Group Seamless */
    .input-group-text { background-color: #fff; }
    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control { border-color: #86b7fe; box-shadow: none; }

    /* Status Badge Style */
    .badge-status { padding: 0.5em 0.75em; font-size: 0.75rem; font-weight: 600; border-radius: 50rem; display: inline-block; }
    .status-pending { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
    .status-verified { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .status-revision { background-color: rgba(220, 53, 69, 0.1); color: #DC3545; }

    /* --- Style Khusus Modal Verifikasi --- */
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
    @if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Header Page --}}
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Daftar Disposisi</h2>
        <div class="btn-group" role="group">
         @can('can access add button')
         <a href="{{ route('dispositions.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
        @endcan
        @can('can access export')
        <a href="{{ route('dispositions.exportPdf', ['date' => request('date')]) }}" target="_blank" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
        @endcan
        @can('can access recycle')
        <a href="{{ route('dispositions.recyclebin') }}" class="btn btn-secondary">
            <i class="bi bi-trash"></i> Recycle Bin
        </a>
        @endcan
    </div>
</div>

{{-- Filter dan Live Search --}}
<form id="filterForm" method="GET" action="{{ route('dispositions.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
    <div class="row w-100">
        <div class="col-md-4">
            <div class="mb-1">Pilih Tanggal</div>
            <div class="input-group mb-2">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar4-week"></i></span>
                <input type="date" name="date" id="filter_date" class="form-control border-start-0 ps-0 shadow-none filter-input text-muted"
                value="{{ request('date') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-1">Cari Data</div>
            <div class="input-group mb-2">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" id="filter_search" class="form-control border-start-0 ps-0 shadow-none filter-input"
                placeholder="Cari Nomor / Kepada..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4 align-self-end">
            <a href="{{ route('dispositions.index') }}" class="btn btn-primary mb-2">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </div>
    </div>
</form>

{{-- Card Table --}}
<div class="card card-custom mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-secondary text-center">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Nomor</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 18%;">Kepada</th>
                        <th>Tipe Disposisi</th>
                        <th style="width: 12%;">Status SPV</th> {{-- Kolom Baru --}}
                        <th style="width: 18%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dispositions as $disp)
                    <tr>
                        <td class="text-center fw-bold text-secondary">
                            {{ $loop->iteration + ($dispositions->currentPage() - 1) * $dispositions->perPage() }}
                        </td>

                        <td class="fw-bold text-center text-primary">{{ $disp->nomor }}</td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($disp->tanggal)->format('d-m-Y') }}
                        </td>

                        <td>{{ $disp->kepada }}</td>

                        <td class="text-center">
                            @if($disp->disposisi_produk) <span class="badge bg-info">Produk</span> @endif
                            @if($disp->disposisi_material) <span class="badge bg-warning text-dark">Material</span> @endif
                            @if($disp->disposisi_prosedur) <span class="badge bg-info text-dark">Prosedur</span> @endif
                        </td>

                        {{-- Kolom Status SPV --}}
                        <td class="text-center">
                            @if(isset($disp->status_spv))
                            @if($disp->status_spv == 1)
                            <span class="badge-status status-verified"><i class="fas fa-check-circle me-1"></i>Verified</span>
                            @elseif($disp->status_spv == 2)
                            <span class="badge-status status-revision"><i class="fas fa-exclamation-circle me-1"></i>Revision</span>
                            @else
                            <span class="badge-status status-pending"><i class="fas fa-clock me-1"></i>Pending</span>
                            @endif
                            @else
                            <span class="badge-status status-pending">-</span>
                            @endif
                        </td>

                        <td class="text-center align-middle text-nowrap">

                            <div class="d-flex justify-content-center align-items-center">

                                {{-- 0. Tombol Verifikasi --}}
                                <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm mx-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#verifyModal{{ $disp->uuid }}"
                                title="Verifikasi SPV">
                                <i class="bi bi-shield-check me-1"></i> Verifikasi
                            </button>

                            {{-- 1. Detail --}}
                            <a href="{{ route('dispositions.show', $disp->uuid) }}" class="btn btn-outline-primary btn-sm mx-1" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>

                            {{-- 2. Edit --}}
                            <a href="{{ route('dispositions.edit', $disp->uuid) }}" class="btn btn-warning btn-sm mx-1" title="Edit">
                                <i class="bi bi-pencil-square"></i> Edit Data
                            </a>

                            {{-- 3. Update --}}
                            <a href="{{ route('dispositions.update_form', $disp->uuid) }}" class="btn btn-info btn-sm mx-1 text-white" title="Update Form">
                                <i class="bi bi-pencil"></i> Update
                            </a>

                            {{-- 4. Hapus --}}
                            <form action="{{ route('dispositions.destroy', $disp->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="mb-2"><i class="bi bi-clipboard-x display-4 text-secondary opacity-50"></i></div>
                        <h6 class="fw-bold">Data tidak ditemukan</h6>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

{{-- Pagination --}}
@if ($dispositions->hasPages())
<div class="mt-3">
    {!! $dispositions->withQueryString()->links('pagination::bootstrap-5') !!}
</div>
@endif
</div>

{{-- 
=======================================================
2. MODAL VERIFIKASI (LOOPING)
=======================================================
--}}
@foreach ($dispositions as $disp)
<div class="modal fade modal-verification" id="verifyModal{{ $disp->uuid }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $disp->uuid }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Form Mengarah ke Route 'dispositions.verify' --}}
            <form action="{{ route('dispositions.verify', $disp->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bolder fs-4 text-uppercase" id="verifyModalLabel{{ $disp->uuid }}">
                        <i class="bi bi-gear-fill me-2"></i>Verification
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-light mb-4 text-center">
                        Verifikasi Disposisi Nomor: <br>
                        <span class="fs-3 fw-bold">{{ $disp->nomor }}</span><br>
                        <span class="small">Kepada: {{ $disp->kepada }}</span>
                    </p>

                    <div class="mb-4">
                        <label for="status_spv_{{ $disp->uuid }}" class="form-label fw-bold d-block text-center mb-2">Pilih Status Verifikasi</label>
                        <select name="status_spv" id="status_spv_{{ $disp->uuid }}" class="form-select form-select-lg fw-bold text-center mx-auto form-select-custom" style="width: 85%;" required>
                            @php $currentStatus = $disp->status_spv ?? null; @endphp
                            <option value="1" {{ old('status_spv', $currentStatus) != 2 ? 'selected' : '' }} style="color: #198754;">✅ Verified (Disetujui)</option>
                            <option value="2" {{ old('status_spv', $currentStatus) == 2 ? 'selected' : '' }} style="color: #dc3545;">❌ Revision (Perlu Perbaikan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan_spv_{{ $disp->uuid }}" class="form-label fw-bold">Catatan Verifikasi (Wajib jika 'Revision')</label>
                        <textarea name="catatan_spv" id="catatan_spv_{{ $disp->uuid }}" rows="4"
                            class="form-control shadow-none textarea-custom"
                            placeholder="Tuliskan catatan perbaikan...">{{ old('catatan_spv', $disp->catatan_spv ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-outline-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn rounded-pill px-5 fw-bolder" style="background-color: var(--accent-color-1); color: var(--text-on-light);">
                            <i class="bi bi-save-fill me-1"></i> SUBMIT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Javascript --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        // Auto Submit Filter
            const form = document.getElementById('filterForm');
            const inputs = document.querySelectorAll('.filter-input');
            let debounceTimer;

            inputs.forEach(input => {
                if(input.type === 'date') {
                    input.addEventListener('change', () => form.submit());
                } else {
                    input.addEventListener('input', () => {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            form.submit();
                        }, 600);
                    });
                }
            });

        // Auto Hide Alert
            const alertBox = document.querySelector('.alert');
            if(alertBox){
                setTimeout(() => {
                    alertBox.classList.remove('show');
                    alertBox.classList.add('fade');
                    setTimeout(() => alertBox.remove(), 500); 
                }, 3000);
            }
        });
    </script>

    @endsection