@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-list-check"></i> Daftar Form QC</h3>
                <a href="{{ route('list_form.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>

            {{-- Search Form di kanan --}}
            <form method="GET" class="mb-3 d-flex justify-content-end">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cari nama list_form..." style="width: 250px;">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Search</button>
            </form>

            <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>No</th>
                        <th style="width: 20%;">Date</th>
                        <th>Nama Laporan</th>
                        <th>No. Dokumen</th>
                        <th style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                 @php $no = ($list_form->currentPage() - 1) * $list_form->perPage() + 1; @endphp
                 @forelse ($list_form as $dep)
                 <tr>
                    <td class="text-center align-middle">{{ $no++ }}</td>
                    <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->created_at)->format('d-m-Y H:i') }}</td>
                    <td class="text-center align-middle">{{ $dep->laporan }}</td>
                    <td class="text-center align-middle">{{ $dep->no_dokumen }}</td>
                    <td class="text-center align-middle">
                        <a href="{{ route('list_form.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        {{-- <form action="{{ route('list_form.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus?')">
                            <i class="bi bi-trash"></i> Hapus
                        </button> --}}
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data laporan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{-- Pagination pakai Bootstrap 5 --}}
    <div class="d-flex justify-content-end">
        {{ $list_form->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>
</div>

{{-- Auto-hide alert setelah 3 detik --}}
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if(alert){
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>

{{-- Styling kecil pagination biar rapih --}}
<style>
    .pagination {
        justify-content: end;
    }
    .pagination .page-link {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
    /* Header tabel merah */
    .table thead {
        background-color: #dc3545 !important; /* merah gelap */
        color: #fff;
    }

/* Baris tabel stripe merah muda */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f8d7da; /* merah muda terang */
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: #f5c2c7; /* merah muda agak gelap */
}

/* Hover baris merah gelap */
.table tbody tr:hover {
    background-color: #e4606d !important;
    color: #fff;
}

/* Border tabel merah */
.table-bordered th, .table-bordered td {
    border-color: #dc3545;
}

/* Tombol aksi tetap jelas */
.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #b02a37;
    border-color: #a52834;
}
</style>

@endsection
