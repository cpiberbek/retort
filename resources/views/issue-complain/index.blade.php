@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Issue & Komplain</h1>

    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm border-0">

        <div class="card-header py-3">
            <form method="GET"
                action="{{ route('issue-complain.index') }}"
                id="filterForm">

                <div class="row align-items-end">

                    <div class="col-md-3">
                        <label for="date" class="font-weight-bold mb-1">
                            Filter Tanggal
                        </label>

                        <input type="date"
                            name="date"
                            id="date"
                            class="form-control"
                            value="{{ request('date') }}">
                    </div>

                    <div class="col-md-5">
                        <label for="search" class="font-weight-bold mb-1">
                            Cari Issue
                        </label>

                        <input type="text"
                            name="search"
                            id="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Cari judul, jenis, atau detail isu...">
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('issue-complain.index') }}"
                            class="btn btn-primary w-100">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>

                </div>

            </form>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover"
                    width="100%"
                    cellspacing="0">

                        <div class="d-flex justify-content-end mb-3">
                                <a href="{{ route('issue-complain.create-or-update') }}"
                                    class="btn btn-success px-4 py-2">
                                    <i class="fas fa-plus"></i> Tambah Issue & Complain
                                </a>
                        </div>

                    <thead class="text-center bg-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Judul Issue</th>
                            <th>Jenis</th>
                            <th>Detail</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($issueComplains as $index => $data)
                            <tr>

                                <td class="text-center">
                                    {{ ($issueComplains->currentPage() - 1) * $issueComplains->perPage() + $index + 1 }}
                                </td>

                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $data->judul_isu }}
                                </td>

                                <td class="text-center">
                                    @if($data->jenis === 'progress')
                                        <span class="badge badge-info">
                                            Progress
                                        </span>
                                    @elseif($data->jenis === 'penyelesaian')
                                        <span class="badge badge-success">
                                            Penyelesaian
                                        </span>
                                    @else
                                        <span class="badge" style="background: #ea580c; color: #fff;">Update</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button type="button"
                                        class="btn btn-sm btn-primary"
                                        data-toggle="modal"
                                        data-target="#detailModal{{ $data->uuid }}">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>

                                    <div class="modal fade"
                                        id="detailModal{{ $data->uuid }}"
                                        tabindex="-1"
                                        role="dialog"
                                        aria-labelledby="detailModalLabel{{ $data->uuid }}"
                                        aria-hidden="true">

                                        <div class="modal-dialog modal-lg modal-dialog-centered"
                                            role="document">

                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="detailModalLabel{{ $data->uuid }}">
                                                        Detail Issue
                                                    </h5>

                                                    <button type="button"
                                                        class="close"
                                                        data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body text-left">
                                                    {{ $data->detail }}
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button"
                                                        class="btn btn-secondary"
                                                        data-dismiss="modal">
                                                        Tutup
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center">

                                    <a href="{{ route('issue-complain.create-or-update', ['uuid' => $data->uuid]) }}"
                                        class="btn btn-sm btn-warning"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    <form action="{{ route('issue-complain.destroy', ['uuid' => $data->uuid]) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data isu & komplain ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Hapus">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Data tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if(method_exists($issueComplains, 'links'))
                <div class="d-flex justify-content-end mt-3">
                    {{ $issueComplains->links('pagination::bootstrap-4') }}
                </div>
            @endif

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {

        $('#date').on('change', function () {
            $('#filterForm').submit();
        });

        let searchTimer;

        $('#search').on('input', function () {
            clearTimeout(searchTimer);

            searchTimer = setTimeout(function () {
                $('#filterForm').submit();
            }, 500);
        });

    });
</script>
@endpush