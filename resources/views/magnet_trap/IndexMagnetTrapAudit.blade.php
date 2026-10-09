@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    {{-- Alert --}}
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

    {{-- Header --}}
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Data Audit Magnet Trap</h2>

        <a href="{{ route('checklistmagnettrap.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

     @unless(auth()->user()->hasRole('auditor'))
            <div class="card shadow-sm mb-3 w-100">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-check text-primary" style="font-size: 1.75rem; margin-right: 1rem;"></i>
                        <h6 class="mb-0">
                            <span class="text-primary" style="font-weight: 600;">Mode Audit</span>
                        </h6>
                    </div>

                    <form action="{{ route('toggle.mode.audit') }}" method="POST"
                        onsubmit="return confirm('Kembali ke mode Operasional?')">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-clipboard-check"></i> Mode Operasional
                        </button>
                    </form>
                </div>
            </div>
        @endunless

    {{-- Filter --}}
    <form
    id="filterForm"
    method="GET"
    action="{{ route('checklistmagnettrap.audit') }}"
    class="mb-3 p-3 border rounded bg-white shadow-sm"
>
    <div class="row align-items-end">

        <div class="col-md-5 mb-2 mb-md-0">
            <div class="mb-1">Pilih Tanggal</div>

            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-calendar-date text-muted"></i>
                </span>

                <input
                    type="date"
                    name="date"
                    id="filter_date"
                    class="form-control border-start-0"
                    value="{{ request('date') }}"
                    placeholder="Tanggal"
                >
            </div>
        </div>

        <div class="col-md-5 mb-2 mb-md-0">
            <div class="mb-1">Cari Nama Varian</div>

            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>

                <input
                    type="text"
                    name="search"
                    id="search"
                    class="form-control border-start-0"
                    value="{{ request('search') }}"
                    placeholder="Cari Nama Produk / Kode Batch..."
                >
            </div>
        </div>

        <div class="col-md-2">
            <a
                href="{{ route('checklistmagnettrap.audit') }}"
                class="btn btn-primary w-100"
            >
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </div>

    </div>
</form>

    {{-- Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table">

                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Nama Varian</th>
                            <th>Kode Batch</th>
                            <th>Tanggal | Pukul</th>
                            <th>Jml Temuan</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Produksi</th>
                            <th>Engineer</th>
                            <th>Status SPV</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($data as $item)

                            <tr>

                                <td class="text-center align-middle">
                                    {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $item->nama_produk ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $item->mincing->kode_produksi ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ \Carbon\Carbon::parse($item->tanggal ?? $item->created_at)->format('d-m-Y') }}
                                    <br>

                                    <span class="text-muted small">
                                        {{ $item->pukul ? \Carbon\Carbon::parse($item->pukul)->format('H:i') : '-' }}
                                    </span>
                                </td>

                                <td class="text-center align-middle">
                                    {{ $item->jumlah_temuan ?? '-' }}
                                </td>

                                <td class="text-center align-middle">

                                    @if($item->status == 'v')

                                        <span class="fw-bold text-success">
                                            <i class="bi bi-check-circle-fill"></i>
                                            OK
                                        </span>

                                    @else

                                        <span class="fw-bold text-danger">
                                            <i class="bi bi-x-circle-fill"></i>
                                            NOT OK
                                        </span>

                                    @endif

                                </td>

                                <td class="text-center align-middle">
                                    {{ Str::limit($item->keterangan ?? '-', 35) }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ optional($item->produksi)->nama_karyawan ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ optional($item->engineer)->nama_karyawan ?? '-' }}
                                </td>

                                <td class="text-center align-middle">

                                    @if($item->status_spv == 1)

                                        <span class="badge-status status-verified">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Verified
                                        </span>

                                    @elseif($item->status_spv == 2)

                                        <span class="badge-status status-revision">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            Revision
                                        </span>

                                    @else

                                        <span class="badge-status status-pending">
                                            <i class="fas fa-clock me-1"></i>
                                            Pending
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    Belum ada data audit magnet trap.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

{{-- Auto Submit --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    const search = document.getElementById('search');
    const date = document.getElementById('filter_date');
    const form = document.getElementById('filterForm');

    let timer;

    search.addEventListener('input', () => {
        clearTimeout(timer);

        timer = setTimeout(() => {
            form.submit();
        }, 500);
    });

    date.addEventListener('change', () => {
        form.submit();
    });

});
</script>

{{-- Auto-hide Alert --}}
<script>
setTimeout(() => {

    const alert = document.querySelector('.alert');

    if (alert) {
        alert.classList.remove('show');
        alert.classList.add('fade');
    }

}, 3000);
</script>

<style>

.table td,
.table th {
    font-size: 0.85rem;
    white-space: nowrap;
}

.text-danger {
    font-weight: bold;
}

.badge-status {
    padding: 0.5em 0.75em;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 50rem;
}

.status-pending {
    background-color: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

.status-verified {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754;
}

.status-revision {
    background-color: rgba(220, 53, 69, 0.1);
    color: #DC3545;
}

</style>

@endsection