@extends('layouts.app')

@section('content')
@php
    $isAuditor = auth()->user()->hasRole('auditor');
    $showAksi = !$isAuditor && auth()->user()->can('can access update button');
@endphp

<div class="container-fluid py-0">
    {{-- Alert sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alert error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Data No. Lot Wire</h2>
    </div>

    {{-- Mode Audit --}}
    @unless($isAuditor)
        <style>
            .audit-banner {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 12px 20px;
                padding: 14px 20px;
                border-radius: 14px;
                color: #fff;
                background: linear-gradient(90deg, #6f3fbd 0%, #5d37a4 55%, #512f8d 100%);
                box-shadow: 0 6px 18px rgba(81, 47, 141, .25);
            }

            .audit-banner__left {
                display: flex;
                align-items: center;
                gap: 14px;
                min-width: 0;
                flex: 1 1 420px;
            }

            .audit-banner__icon {
                position: relative;
                flex: 0 0 44px;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: #fff;
                color: #6f3fbd;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.35rem;
            }

            .audit-banner__icon-badge {
                position: absolute;
                right: 7px;
                bottom: 7px;
                font-size: .7rem;
                color: #512f8d;
            }

            .audit-banner__title {
                font-weight: 800;
                font-size: 1.05rem;
                letter-spacing: .02em;
                text-transform: uppercase;
                line-height: 1.2;
            }

            .audit-banner__desc {
                font-size: .85rem;
                opacity: .92;
                margin-top: 2px;
            }

            .audit-banner__actions {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px;
            }

            .audit-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 999px;
                font-size: .8rem;
                font-weight: 600;
                line-height: 1.2;
                color: #fff;
                background: rgba(255, 255, 255, .12);
                border: 1px solid rgba(255, 255, 255, .38);
                transition: background .15s ease;
            }

            .audit-pill:hover:not(:disabled) {
                background: rgba(255, 255, 255, .24);
            }

            .audit-pill:disabled {
                opacity: .55;
                cursor: not-allowed;
            }
        </style>

        <div class="audit-banner mb-3 w-100">
            <div class="audit-banner__left">
                <div class="audit-banner__icon">
                    <i class="bi bi-person-fill"></i>
                    <i class="bi bi-shield-fill-check audit-banner__icon-badge"></i>
                </div>

                <div>
                    <div class="audit-banner__title">
                        <i class="bi bi-clipboard-check-fill"></i> Mode Data Audit
                    </div>

                    <div class="audit-banner__desc">
                        Perubahan di halaman ini hanya berlaku pada <b>salinan data audit</b> untuk keperluan Audit QC System.
                        Data operasional utama tidak terpengaruh.
                    </div>
                </div>
            </div>

            <div class="audit-banner__actions">
                <form action="{{ route('toggle.mode.audit') }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Kembali ke mode Operasional?')">
                    @csrf
                    <button type="submit" class="audit-pill">
                        <i class="bi bi-clipboard-check"></i> Mode Operasional
                    </button>
                </form>
            </div>
        </div>
    @endunless

    {{-- Filter Form --}}
    <form id="filterForm" method="GET" action="{{ route('wire.index') }}"
        class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
        <div class="row w-100">
            <div class="col-md-3">
                <div class="mb-1">Pilih Tanggal</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                    </div>
                    <input type="date" name="date" id="filter_date"
                        class="form-control border-start-0"
                        value="{{ request('date') }}" placeholder="Tanggal">
                </div>
            </div>

            <div class="col-md-3">
                <div class="mb-1">Pilih Shift</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-hourglass-split text-muted"></i>
                        </span>
                    </div>
                    <select name="shift" id="filter_shift"
                        class="form-select border-start-0 form-control">
                        <option value="">Semua Shift</option>
                        <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                        <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="mb-1">Cari Data</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" name="search" id="search"
                        class="form-control border-start-0"
                        value="{{ request('search') }}"
                        placeholder="Cari Nama Varian / Supplier...">
                </div>
            </div>

            <div class="col-md-3 align-self-end">
                <a href="{{ route('wire.index') }}" class="btn btn-primary mb-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Warning Modal --}}
    <div class="modal fade" id="warningModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">(!) Filter Belum Lengkap Untuk Export Data</h5>
                </div>
                <div class="modal-body">
                    Silakan pilih <b>Tanggal</b> yang spesifik di bagian filter terlebih dahulu sebelum melakukan
                    export.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('search');
            const date = document.getElementById('filter_date');
            const shift = document.getElementById('filter_shift');
            const kodeBatch = document.getElementById('filter_kode_batch');
            const form = document.getElementById('filterForm');
            const exportPdfBtn = document.getElementById('exportPdfBtn');

            function validateExport() {
                if (!date.value) {
                    new bootstrap.Modal(document.getElementById('warningModal')).show();
                    return false;
                }

                return true;
            }

            let timer;

            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });

            date.addEventListener('change', () => form.submit());

            if (shift) {
                shift.addEventListener('change', () => form.submit());
            }

            if (kodeBatch) {
                kodeBatch.addEventListener('change', () => form.submit());
            }

            if (exportPdfBtn) {
                exportPdfBtn.addEventListener('click', function() {
                    if (!validateExport()) return;

                    const formData = new FormData(form);
                    const exportUrl = "{{ route('wire.exportPdf') }}?" +
                        new URLSearchParams(formData).toString();

                    window.open(exportUrl, '_blank');
                });
            }
        });
    </script>

    {{-- Table --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Nama Varian</th>
                            <th>Nama Supplier</th>
                            <th>Data Wire</th>
                            <th>QC</th>
                            <th>SPV</th>
                            @if($showAksi)
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                        @endphp

                        @forelse ($data as $dep)
                            <tr>
                                <td class="text-center align-middle">{{ $no++ }}</td>

                                <td class="text-center align-middle">
                                    {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | {{ $dep->shift }}
                                </td>

                                <td class="align-middle">{{ $dep->nama_produk }}</td>

                                <td class="align-middle">{{ $dep->nama_supplier }}</td>

                                <td class="text-center align-middle">
                                    @php
                                        $data_wire = json_decode($dep->data_wire, true);
                                    @endphp

                                    @if (!empty($data_wire))
                                        <a href="#" data-bs-toggle="modal"
                                            data-bs-target="#wireModal{{ $dep->uuid }}"
                                            class="fw-bold text-decoration-underline">
                                            Result
                                        </a>

                                        <div class="modal fade" id="wireModal{{ $dep->uuid }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog" style="max-width: 70%;">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title">Detail Pemeriksaan Wire</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body text-start">
                                                        @foreach ($data_wire as $mIndex => $mesin)
                                                            <div class="mb-3 border-bottom pb-3">
                                                                <h6 class="fw-bold text-primary">
                                                                    🧭 Mesin: {{ $mesin['mesin'] ?? '-' }}
                                                                </h6>

                                                                <table class="table table-bordered table-sm text-center">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Start - End</th>
                                                                            <th>No. Lot</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @if (!empty($mesin['detail']))
                                                                            @foreach ($mesin['detail'] as $idx => $dtl)
                                                                                <tr>
                                                                                    <td>{{ $idx + 1 }}</td>
                                                                                    <td>
                                                                                        {{ $dtl['start'] ?? '' }} -
                                                                                        {{ $dtl['end'] ?? '' }}
                                                                                    </td>
                                                                                    <td>{{ $dtl['no_lot'] ?? '' }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="3">Tidak ada data</td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>

                                <td class="text-center align-middle">
                                    {{ \Illuminate\Support\Facades\DB::table('users')->where('username', $dep->username)->value('name') }}
                                </td>

                                <td class="text-center align-middle">
                                    @if ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                    @elseif ($dep->status_spv == 2)
                                        <span class="fw-bold text-danger">Revisi</span>
                                    @else
                                        <span class="fw-bold text-secondary">Created</span>
                                    @endif
                                </td>

                                @if($showAksi)
                                    <td class="text-center align-middle">
                                        @unless($isAuditor)
                                            <form action="{{ route('wire.duplicateToAudit', $dep->uuid) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn {{ $dep->is_audit ? 'btn-success' : 'btn-warning' }} btn-sm me-1 mb-1"
                                                    @if($dep->is_audit)
                                                        title="Data Audit Sudah Tersedia : Duplikasi entry ini saja"
                                                    @endif
                                                    onclick="return confirm('Edit data ini sebagai data Audit?')">
                                                    <i class="bi bi-pencil"></i>
                                                    Edit Data Audit
                                                </button>
                                            </form>
                                        @endunless
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showAksi ? 8 : 7 }}" class="text-center">
                                    Belum ada data.
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
@endsection