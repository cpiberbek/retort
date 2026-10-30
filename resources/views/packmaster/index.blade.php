 @extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Alert gagal --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Alert validasi --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Data Packaging</h3>

                <button type="button" class="btn btn-success"
                    data-bs-toggle="modal" data-bs-target="#createPackagingModal">
                    <i class="bi bi-plus-circle"></i> Tambah Packaging
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr class="text-center align-middle">
                            <th style="width: 5%">No</th>
                            <th>Nama Packaging</th>
                            <th style="width: 15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($packagings as $index => $packaging)
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">{{ $packaging->nama_packaging }}</td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-warning btn-sm"
                                    title="Edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editPackagingModal{{ $packaging->uuid }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">
                                Belum ada data packaging.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Create --}}
<div class="modal fade" id="createPackagingModal" tabindex="-1"
    aria-labelledby="createPackagingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('packmaster.store') }}" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPackagingModalLabel">
                        Tambah Packaging
                    </h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label for="nama_packaging_create" class="form-label">
                        Nama Packaging
                    </label>
                    <input type="text" name="nama_packaging"
                        id="nama_packaging_create"
                        class="form-control"
                        placeholder="Masukkan nama packaging"
                        maxlength="255" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
@foreach($packagings as $packaging)
<div class="modal fade"
    id="editPackagingModal{{ $packaging->uuid }}"
    tabindex="-1"
    aria-labelledby="editPackagingModalLabel{{ $packaging->uuid }}"
    aria-hidden="true">

    <div class="modal-dialog">
        <form action="{{ route('packmaster.update', $packaging->uuid) }}"
            method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="editPackagingModalLabel{{ $packaging->uuid }}">
                        Edit Packaging
                    </h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label for="nama_packaging_{{ $packaging->uuid }}"
                        class="form-label">
                        Nama Packaging
                    </label>
                    <input type="text" name="nama_packaging"
                        id="nama_packaging_{{ $packaging->uuid }}"
                        class="form-control"
                        value="{{ $packaging->nama_packaging }}"
                        maxlength="255" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<style>
    .table thead {
        background-color: #dc3545;
        color: #fff;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f8d7da;
    }

    .table-striped tbody tr:nth-of-type(even) {
        background-color: #f5c2c7;
    }

    .table tbody tr:hover {
        background-color: #e4606d;
        color: #fff;
    }

    .table-bordered th,
    .table-bordered td {
        border-color: #dc3545;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }
</style>
@endsection