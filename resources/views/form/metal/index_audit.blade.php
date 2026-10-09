@extends('layouts.app')

@section('content')

<div class="container-fluid py-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ trim(session('success')) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">
            Data Audit Metal Detector
        </h2>
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
        action="{{ route('metal.audit') }}"
        class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm"
    >
        <div class="row w-100">

            <div class="col-md-4">
                <div class="mb-1">Pilih Tanggal</div>

                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>

                    <input
                        type="date"
                        name="date"
                        id="filter_date"
                        class="form-control border-start-0"
                        value="{{ request('date') }}"
                    >
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-1">Cari Data</div>

                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>

                    <input
                        type="text"
                        name="search"
                        id="search"
                        class="form-control border-start-0"
                        value="{{ request('search') }}"
                        placeholder="Cari sesuatu..."
                    >
                </div>
            </div>

            <div class="col-md-4 align-self-end">
                <a
                    href="{{ route('metal.audit') }}"
                    class="btn btn-primary mb-2"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reset
                </a>
            </div>

        </div>
    </form>

    {{-- Table --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="table-responsive">

                <table class="table">

                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Pukul</th>
                            <th>FE 1.0 mm</th>
                            <th>NFE 1.5 mm</th>
                            <th>
                                SUS
                                @if(Auth::user()->plant == '2debd595-89c4-4a7e-bf94-e623cc220ca6')
                                    2.5 mm
                                @elseif(Auth::user()->plant == 'fdaca613-7ab2-4997-8f33-686e886c867d')
                                    2.0 mm
                                @else
                                    - mm
                                @endif
                            </th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>Engineer</th>
                            <th>SPV</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php
                            $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                        @endphp

                        @forelse ($data as $dep)

                            <tr>

                                <td class="text-center align-middle">
                                    {{ $no++ }}
                                </td>

                                <td class="align-middle">
                                    {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                    |
                                    {{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $dep->fe == 'Terdeteksi'
                                        ? '<span class="text-success fw-bold">✓</span>'
                                        : '<span class="text-danger fw-bold">x</span>' !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $dep->nfe == 'Terdeteksi'
                                        ? '<span class="text-success fw-bold">✓</span>'
                                        : '<span class="text-danger fw-bold">x</span>' !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $dep->sus == 'Terdeteksi'
                                        ? '<span class="text-success fw-bold">✓</span>'
                                        : '<span class="text-danger fw-bold">x</span>' !!}
                                </td>

                                <td class="text-center align-middle">
                                    {{ \Illuminate\Support\Facades\DB::table('users')
                                        ->where('username', $dep->username)
                                        ->value('name') ?? '-' }}
                                </td>

                                <td class="text-center align-middle">

                                    @if ($dep->status_produksi == 0)
                                        <span class="fw-bold text-secondary">
                                            Created
                                        </span>
                                    @elseif ($dep->status_produksi == 1)
                                        <span class="fw-bold text-success">
                                            Checked
                                        </span>
                                    @elseif ($dep->status_produksi == 2)
                                        <span class="fw-bold text-danger">
                                            Recheck
                                        </span>
                                    @else
                                        <span class="text-secondary">
                                            -
                                        </span>
                                    @endif

                                </td>

                                <td class="text-center align-middle">

                                    @if ($dep->status_engineer == 0)
                                        <span class="fw-bold text-secondary">
                                            Created
                                        </span>
                                    @elseif ($dep->status_engineer == 1)
                                        <span class="fw-bold text-success">
                                            Checked
                                        </span>
                                    @elseif ($dep->status_engineer == 2)
                                        <span class="fw-bold text-danger">
                                            Recheck
                                        </span>
                                    @else
                                        <span class="text-secondary">
                                            -
                                        </span>
                                    @endif

                                </td>

                                <td class="text-center align-middle">

                                    @if ($dep->status_spv == 0)
                                        <span class="fw-bold text-secondary">
                                            Created
                                        </span>
                                    @elseif ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">
                                            Verified
                                        </span>
                                    @elseif ($dep->status_spv == 2)
                                        <span class="fw-bold text-danger">
                                            Revision
                                        </span>
                                    @else
                                        <span class="text-secondary">
                                            -
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    Belum ada data audit Metal Detector.
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
        {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

</div>

<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');

        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);

    document.addEventListener('DOMContentLoaded', () => {

        const search = document.getElementById('search');
        const date = document.getElementById('filter_date');
        const form = document.getElementById('filterForm');

        let timer;

        if (search) {
            search.addEventListener('input', () => {
                clearTimeout(timer);

                timer = setTimeout(() => {
                    form.submit();
                }, 500);
            });
        }

        if (date) {
            date.addEventListener('change', () => {
                form.submit();
            });
        }

    });
</script>

<style>
    .table td,
    .table th {
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .container {
        padding-left: 2px !important;
        padding-right: 2px !important;
    }
</style>

@endsection