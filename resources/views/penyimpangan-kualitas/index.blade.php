@extends('layouts.app')

@section('title', 'Daftar Penyimpangan Kualitas')

@section('content')
<div class="container-fluid py-0">

    {{-- Alert Section --}}
    @if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Daftar Penyimpangan Kualitas</h2>
        <div class="btn-group" role="group">
            @can('can access add button')
            <a href="{{ route('penyimpangan-kualitas.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            @endcan
            @can('can access export')
            <a href="{{ route('penyimpangan-kualitas.exportPdf', ['date' => request('date')]) }}" target="_blank" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            @endcan
            @can('can access recycle')
            <a href="{{ route('penyimpangan-kualitas.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- Filter dan Live Search --}}
    <form id="filterForm" method="GET" action="{{ route('penyimpangan-kualitas.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-1">Pilih Tanggal</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar4-week"></i>
                        </span>
                    </div>
                    <input type="date" name="date" id="filter_date" class="form-control border-start-0 ps-0 shadow-none filter-input text-muted"
                    value="{{ request('date') }}" style="border-color: #ced4da; font-size: 0.95rem;">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">Cari Data</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                    </div>
                    <input type="text" name="search" id="filter_search" class="form-control border-start-0 ps-0 shadow-none filter-input"
                    placeholder="Cari Nomor, Varian, Lot..." value="{{ request('search') }}"
                    style="border-color: #ced4da; font-size: 0.95rem;">
                </div>
            </div>
            <div class="col-md-4 align-self-end">
                <a href="{{ route('penyimpangan-kualitas.index') }}" class="btn btn-primary mb-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            {{-- Tabel Data --}}
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 10%;">Tanggal</th>
                            <th style="width: 15%;">Nomor</th>
                            <th style="width: 20%;">Nama Varian</th>
                            <th style="width: 15%;">Lot/Kode</th>
                            <th style="width: 10%;">Status Diketahui</th>
                            <th style="width: 10%;">Status Disetujui</th>
                            <th style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penyimpanganKualitasItems as $item)
                        <tr>
                            {{-- Penomoran --}}
                            <td class="text-center fw-bold text-secondary">
                                {{ $loop->iteration + ($penyimpanganKualitasItems->currentPage() - 1) * $penyimpanganKualitasItems->perPage() }}
                            </td>

                            {{-- Tanggal --}}
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                            </td>

                            {{-- Nomor --}}
                            <td class="fw-bold text-center text-primary">{{ $item->nomor }}</td>

                            {{-- Nama Produk --}}
                            <td>{{ $item->nama_produk }}</td>

                            {{-- Lot Kode --}}
                            <td class="text-center">{{ $item->lot_kode }}</td>

                            {{-- Status Diketahui --}}
                            <td class="text-center">
                                @if($item->status_diketahui == 0) 
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                @elseif($item->status_diketahui == 1) 
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Verified</span>
                                @else 
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Revisi</span>
                                @endif
                            </td>

                            {{-- Status Disetujui --}}
                            <td class="text-center">
                                @if($item->status_disetujui == 0) 
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                @elseif($item->status_disetujui == 1) 
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Verified</span>
                                @else 
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Revisi</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center align-middle text-nowrap">
                                <div class="d-flex justify-content-center align-items-center gap-2">

                                    {{-- Verifikasi --}}
                                    <button type="button"
                                    class="btn btn-primary btn-sm fw-bold shadow-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#verifyModal{{ $item->id }}"
                                    title="Verifikasi Penyimpangan Kualitas">
                                    <i class="bi bi-shield-check me-1"></i> Verifikasi
                                </button>

                                {{-- Detail --}}
                                <a href="{{ route('penyimpangan-kualitas.show', $item->id) }}"
                                   class="btn btn-outline-primary btn-sm"
                                   title="Detail">
                                   <i class="bi bi-eye me-1"></i> Detail
                               </a>

                               {{-- Edit --}}
                               <a href="{{ route('penyimpangan-kualitas.edit', $item->id) }}"
                                   class="btn btn-warning btn-sm text-white"
                                   title="Edit">
                                   <i class="bi bi-pencil-square me-1"></i> Edit
                               </a>

                               {{-- Update / Lengkapi --}}
                               <a href="{{ route('penyimpangan-kualitas.update_form', $item->id) }}"
                                   class="btn btn-success btn-sm"
                                   title="Update / Lengkapi Data">
                                   <i class="bi bi-pencil me-1"></i> Update
                               </a>

                               {{-- Hapus --}}
                               <form action="{{ route('penyimpangan-kualitas.destroy', $item->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit"
                                  class="btn btn-danger btn-sm"
                                  title="Hapus">
                                  <i class="bi bi-trash me-1"></i> Hapus
                              </button>
                          </form>

                      </div>
                  </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <div class="mb-2"><i class="bi bi-clipboard-x display-4 text-secondary opacity-50"></i></div>
                    <h6 class="fw-bold">Data tidak ditemukan</h6>
                    <p class="small mb-0">Silakan tambahkan data baru atau ubah kata kunci pencarian.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
</div>

{{-- Pagination --}}
@if ($penyimpanganKualitasItems->hasPages())
<div class="mt-3">
    {!! $penyimpanganKualitasItems->withQueryString()->links('pagination::bootstrap-5') !!}
</div>
@endif
</div>

{{-- 
=======================================================
MODAL VERIFIKASI (LOOPING)
=======================================================
--}}
@foreach ($penyimpanganKualitasItems as $item)
<div class="modal fade modal-verification" id="verifyModal{{ $item->id }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Mengarah ke Route Verify SPV --}}
            <form action="{{ route('penyimpangan-kualitas.verify.disetujui', $item->id) }}" method="POST">
                @csrf
                @method('POST') 
                
                <div class="modal-header">
                    <h5 class="modal-title fw-bolder fs-4 text-uppercase" id="verifyModalLabel{{ $item->id }}">
                        <i class="bi bi-gear-fill me-2"></i>Verification
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <p class="text-light mb-4 text-center">
                        Verifikasi Data Nomor BA: <br>
                        <span class="fs-3 fw-bold">{{ $item->nomor }}</span>
                    </p>

                    <div class="mb-4">
                        <label for="status_spv_{{ $item->id }}" class="form-label fw-bold d-block text-center mb-2">Pilih Status Verifikasi</label>
                        <select name="status_spv" id="status_spv_{{ $item->id }}" class="form-select form-select-lg fw-bold text-center mx-auto form-select-custom" style="width: 85%;" required>
                            @php $current = $item->status_spv ?? 0; @endphp
                            <option value="1" {{ $current != 2 ? 'selected' : '' }} style="color: #198754;">✅ Verified (Disetujui)</option>
                            <option value="2" {{ $current == 2 ? 'selected' : '' }} style="color: #dc3545;">❌ Revision (Perlu Perbaikan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan_spv_{{ $item->id }}" class="form-label fw-bold">Catatan Verifikasi (Wajib jika 'Revision')</label>
                        <textarea name="catatan_spv" id="catatan_spv_{{ $item->id }}" rows="4"
                            class="form-control shadow-none textarea-custom"
                            placeholder="Tuliskan catatan perbaikan...">{{ old('catatan_spv', $item->catatan_spv) }}</textarea>
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

    {{-- JAVASCRIPT SECTION --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        // 1. Logic untuk Auto Submit Filter
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

        // 2. Logic untuk Auto Hide Alert
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

    {{-- CUSTOM STYLES --}}
    @push('styles')
    <style>
        /* Styling Card */
        .card-custom {
            border: none;
            border-radius: 0.8rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        /* Styling Font Tabel */
        .table td, .table th {
            font-size: 0.9rem;
            padding: 0.75rem 0.5rem;
        }

        /* Styling Input Group agar menyatu (Seamless) */
        .input-group-text {
            background-color: #fff;
        }

        /* Efek Focus */
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #86b7fe;
            box-shadow: none; 
        }

        /* Import Bootstrap Icons */
        @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css");
        /* --- Style Global & Tabel --- */

        /* Status Badge Style */
        .badge-status { padding: 0.5em 0.75em; font-size: 0.75rem; font-weight: 600; border-radius: 50rem; display: inline-block; }
        .status-pending { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
        .status-verified { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .status-revision { background-color: rgba(220, 53, 69, 0.1); color: #DC3545; }

        /* --- Style Khusus Modal Verifikasi (Gradient Merah/Coklat) --- */
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

    @endsection
