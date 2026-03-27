@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 5px solid #198754;">
        <i class="bi bi-check-circle-fill me-2 text-success"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        
        {{-- Header Card --}}
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <h4 class="mb-0 fw-bold text-dark">
                <i class="bi bi-boxes me-2" style="color: #d32f2f;"></i> List Bahan Baku
            </h4>
            {{-- Tombol Tambah Gelap Netral --}}
            <a href="{{ route('raw-material.create') }}" class="btn btn-sm btn-dark shadow-sm px-3 py-2 btn-tambah" style="border-radius: 6px;">
                <i class="bi bi-plus-circle me-1"></i> Tambah Data
            </a>
        </div>

        <div class="card-body pt-2 pb-4 px-4 table-responsive">
            
            {{-- Search Form --}}
            <form method="GET" class="mb-4 d-flex justify-content-end">
                <div class="input-group input-group-sm shadow-sm custom-search-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0 bg-light ps-3" placeholder="Cari nama material...">
                    <button class="btn btn-dark border-0 px-3" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
            
            {{-- Tabel Data --}}
            <table class="table custom-table align-middle">
                <thead>
                    <tr class="text-center text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Date</th>
                        <th>Kode Internal</th>
                        <th class="text-start">Nama Bahan Baku</th>
                        <th style="width: 10%;">Satuan</th>
                        <th>Plant</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                    @endphp
                    
                    @forelse ($data as $item)
                    <tr class="text-center">
                        <td class="text-muted">{{ $no++ }}</td>
                        <td class="text-muted small">
                            <i class="bi bi-calendar2-event me-1"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 shadow-sm">{{ $item->kode_internal ?? '-' }}</span>
                        </td>
                        <td class="text-start fw-semibold text-dark">{{ $item->nama_bahan_baku }}</td>
                        <td>
                            {{-- Pewarnaan Dinamis untuk Satuan (Netral) --}}
                            @if(strtolower($item->satuan) == 'kg')
                                <span class="badge bg-light text-dark border px-2 py-1 shadow-sm">KG</span>
                            @elseif(strtolower($item->satuan) == 'gr')
                                <span class="badge bg-light text-dark border px-2 py-1 shadow-sm">GR</span>
                            @elseif(strtolower($item->satuan) == 'liter')
                                <span class="badge bg-light text-dark border px-2 py-1 shadow-sm">LITER</span>
                            @elseif(strtolower($item->satuan) == 'sak')
                                <span class="badge bg-light text-dark border px-2 py-1 shadow-sm">SAK</span>
                            @else
                                <span class="badge bg-light text-dark border px-2 py-1">-</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $item->dataPlant->plant ?? 'N/A' }}</td>
                        <td class="align-middle">
                            <div class="d-flex justify-content-center">
                                
                                {{-- Tombol Edit (Gunakan mr-2 untuk Bootstrap 4) --}}
                                <a href="{{ route('raw-material.edit', $item->uuid) }}" class=" btn btn-outline-warning btn-sm action-btn mr-2" title="Edit">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('raw-material.destroy', $item->uuid) }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm action-btn btn-delete" title="Hapus">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                                
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada data raw material.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-4 custom-pagination">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- Auto-hide alert script --}}
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if(alert){
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 150);
        }
    }, 3000);
</script>

{{-- Panggil SweetAlert2 dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); 
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data bahan baku ini tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d32f2f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

<style>
/* --- PERBAIKAN SEARCH BOX --- */
.custom-search-group {
    width: 300px;
    border-radius: 6px;
    overflow: hidden;
}
/* Menghilangkan glow biru default bootstrap saat input diklik */
.custom-search-group .form-control:focus {
    box-shadow: none !important;
    background-color: #f8f9fa !important; /* Tetap abu terang saat fokus */
}

/* --- TABLE STYLES --- */
.custom-table { margin-bottom: 0; }
.custom-table thead th {
    border-bottom: 2px solid #e9ecef;
    background-color: #fcfcfc;
    color: #6c757d;
    padding-top: 1rem; padding-bottom: 1rem;
}
.custom-table tbody td {
    border-bottom: 1px solid #f0f1f5;
    padding-top: 1rem; padding-bottom: 1rem;
    vertical-align: middle;
}
.custom-table tbody tr { transition: all 0.2s ease; }
.custom-table tbody tr:hover {
    background-color: #f8f9fa !important;
    transform: translateY(-1px);
}

/* --- PERBAIKAN ACTION BUTTONS HOVER --- */
.action-btn {
    border-radius: 6px;
    padding: 0.25rem 0.6rem;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

/* 1. Hover Edit: Jadi abu-abu terang lembut, bukan hitam pekat */
.btn-outline-dark:hover {
    background-color: #e9ecef !important; /* Abu terang */
    color: #212529 !important; /* Teks tetap gelap */
    border-color: #adb5bd !important;
}

/* 2. Hover Hapus: Jadi MERAH SOLID tegas, bukan samar */
.btn-outline-danger:hover {
    background-color: #dc3545 !important; /* Merah solid */
    color: #ffffff !important; /* Teks putih */
    border-color: #dc3545 !important;
}

/* Tombol Tambah Header Hover */
.btn-tambah { transition: all 0.2s; background-color: #212529 !important; border-color: #212529 !important; }
.btn-tambah:hover { transform: translateY(-2px); background-color: #424649 !important; box-shadow: 0 4px 10px rgba(33, 37, 41, 0.3) !important; }

/* Pagination */
.custom-pagination .page-item.active .page-link { background-color: #212529; border-color: #212529; color: white; font-weight: bold; border-radius: 4px; }
.custom-pagination .page-link { color: #212529; border: 1px solid #dee2e6; margin: 0 2px; border-radius: 4px; }
.custom-pagination .page-link:hover { background-color: #e9ecef; }
</style>
@endsection