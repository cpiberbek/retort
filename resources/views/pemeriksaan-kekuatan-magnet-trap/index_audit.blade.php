@extends('layouts.app')

@section('title', 'Audit Pemeriksaan Kekuatan Magnet Trap')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .card-custom {
            border: none;
            border-radius: 0.8rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .table td,
        .table th {
            font-size: 0.9rem;
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
        }

        .input-group-text {
            background-color: #fff;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #86b7fe;
            box-shadow: none;
        }

        .badge-status {
            padding: 0.5em 0.75em;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 50rem;
            display: inline-block;
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
            color: #dc3545;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-0">

    {{-- Alert --}}
    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ $message }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">
                Audit Pemeriksaan Kekuatan Magnet Trap
            </h2>

            <small class="text-muted">
                Daftar data untuk proses audit
            </small>
        </div>
    </div>

    {{-- Filter --}}
    <form
        id="filterForm"
        method="GET"
        action="{{ route('pemeriksaan-kekuatan-magnet-trap.audit') }}"
        class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm"
    >
        <div class="row w-100">

            {{-- Bulan --}}
            <div class="col-md-3">
                <div class="mb-1">Pilih Bulan</div>

                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-month"></i>
                    </span>

                    <input
                        type="month"
                        name="month"
                        id="filter_month"
                        class="form-control border-start-0 ps-0 shadow-none filter-input text-muted"
                        value="{{ request('month') }}"
                    >
                </div>
            </div>

            {{-- Tanggal --}}
            <div class="col-md-3">
                <div class="mb-1">Pilih Tanggal</div>

                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar4-week"></i>
                    </span>

                    <input
                        type="date"
                        name="date"
                        id="filter_date"
                        class="form-control border-start-0 ps-0 shadow-none filter-input text-muted"
                        value="{{ request('date') }}"
                    >
                </div>
            </div>

            {{-- Search --}}
            <div class="col-md-3">
                <div class="mb-1">Cari Data</div>

                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="text"
                        name="search"
                        id="filter_search"
                        class="form-control border-start-0 ps-0 shadow-none filter-input"
                        placeholder="Cari Magnet Ke, Petugas..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>

            {{-- Reset --}}
            <div class="col-md-3 align-self-end">
                <a
                    href="{{ route('pemeriksaan-kekuatan-magnet-trap.audit') }}"
                    class="btn btn-primary mb-2"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reset
                </a>
            </div>

        </div>
    </form>

    {{-- Table --}}
    <div class="card card-custom mb-4">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover">

                    <thead class="table-secondary text-center">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 10%;">Tanggal</th>
                            <th style="width: 25%;">Kondisi Visual</th>
                            <th style="width: 20%;">Petugas QC</th>
                            <th style="width: 20%;">Parameter</th>
                            <th style="width: 20%;">Status SPV</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($pemeriksaanKekuatanMagnetTraps as $item)

                            <tr>

                                {{-- No --}}
                                <td class="text-center fw-bold text-secondary">
                                    {{
                                        $loop->iteration +
                                        (
                                            ($pemeriksaanKekuatanMagnetTraps->currentPage() - 1)
                                            *
                                            $pemeriksaanKekuatanMagnetTraps->perPage()
                                        )
                                    }}
                                </td>

                                {{-- Tanggal --}}
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                </td>

                                {{-- Kondisi --}}
                                <td>
                                    {{ $item->kondisi_magnet_trap }}
                                </td>

                                {{-- Petugas QC --}}
                                <td>
                                    {{ $item->petugas_qc }}
                                </td>

                                {{-- Parameter --}}
                                <td class="text-center">
                                    @if($item->parameter_sesuai)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Sesuai
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Tdk Sesuai
                                        </span>
                                    @endif
                                </td>

                                {{-- Status SPV --}}
                                <td class="text-center">
                                    @if($item->status_spv == 0)
                                        <span class="badge-status status-pending">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            Pending
                                        </span>
                                    @elseif($item->status_spv == 1)
                                        <span class="badge-status status-verified">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Verified
                                        </span>
                                    @else
                                        <span class="badge-status status-revision">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Revisi
                                        </span>
                                    @endif
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">

                                    <div class="mb-2">
                                        <i class="bi bi-clipboard-x display-4 text-secondary opacity-50"></i>
                                    </div>

                                    <h6 class="fw-bold">
                                        Data tidak ditemukan
                                    </h6>

                                    <p class="small mb-0">
                                        Silakan ubah filter pencarian.
                                    </p>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>
    </div>

    {{-- Pagination --}}
    @if ($pemeriksaanKekuatanMagnetTraps->hasPages())
        <div class="mt-3">
            {!! $pemeriksaanKekuatanMagnetTraps
                ->withQueryString()
                ->links('pagination::bootstrap-5')
            !!}
        </div>
    @endif

</div>

{{-- Auto Filter --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('filterForm');
        const month = document.getElementById('filter_month');
        const date = document.getElementById('filter_date');
        const search = document.getElementById('filter_search');

        let debounceTimer;

        function submitFilter() {
            form.submit();
        }

        date.addEventListener('change', function () {
            if (date.value) {
                month.value = '';
            }

            submitFilter();
        });

        month.addEventListener('change', function () {
            if (month.value) {
                date.value = '';
            }

            submitFilter();
        });

        search.addEventListener('input', function () {
            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(
                submitFilter,
                600
            );
        });

    });
</script>

@endsection