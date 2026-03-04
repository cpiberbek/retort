@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">
    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-list-check"></i> Data Area Kontrol Sanitasi</h3>
                <a href="{{ route('area_sanitasi.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>

            {{-- Search Form --}}
            <form method="GET" class="mb-3 d-flex justify-content-end">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cari area..." style="width: 250px;">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Search</button>
            </form>
            
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr class="text-center align-middle">
                        <th style="width: 5%;">No</th>
                        <th style="width: 20%;">Date</th>
                        <th>Nama Area</th>
                        <th>Sub Area</th>
                        <th>Bagian</th>
                        <th style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $no = ($area_sanitasi->currentPage() - 1) * $area_sanitasi->perPage() + 1;
                    @endphp
                    @forelse ($area_sanitasi as $dep)
                    <tr>
                        <td class="text-center align-middle">{{ $no++ }}</td>
                        <td class="align-middle">{{ \Carbon\Carbon::parse($dep->created_at)->format('d-m-Y H:i') }}</td>
                        <td class="align-middle">{{ $dep->area }}</td>
                        <td class="align-middle">{{ $dep->sub_area ? $dep->sub_area : 'Tolong lengkapi Sub Area' }}</td>
                        <td class="text-center align-middle">
                            @php
                            $bagianList = json_decode($dep->bagian, true);
                            $collapseId = 'bagianCollapse' . $dep->uuid; // unik per row
                            @endphp

                            @if(count($bagianList) > 0)
                            <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                Lihat Bagian
                            </button>
                            <div class="collapse mt-1" id="{{ $collapseId }}">
                                <ul class="mb-0 text-left">
                                    @foreach($bagianList as $b)
                                    <li>{{ $b }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @else
                            <span>-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <a href="{{ route('area_sanitasi.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('area_sanitasi.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')" title="Hapus">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data area.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $area_sanitasi->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- Auto-hide alert --}}
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if(alert){
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>

<style>
/* Header tabel merah */
.table thead {
    background-color: #dc3545 !important;
    color: #fff;
}

/* Baris stripe merah muda */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f8d7da;
}
.table-striped tbody tr:nth-of-type(even) {
    background-color: #f5c2c7;
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

/* Tombol aksi */
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

/* Pagination */
.pagination {
    justify-content: end;
}
.pagination .page-link {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}
</style>
@endsection
