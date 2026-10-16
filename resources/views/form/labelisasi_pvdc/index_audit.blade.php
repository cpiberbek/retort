@extends('layouts.app')

@section('content')
@php
    $isAuditor = auth()->user()->hasRole('auditor');
    $showAksi = !$isAuditor && auth()->user()->can('can access update button');
@endphp

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
        <h2 class="h4">Data Kontrol Labelisasi PVDC</h2>
    </div>

    {{-- ===================== MODE AUDIT ===================== --}}
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

    {{-- Filter Bar --}}
    <form id="filterForm" method="GET" action="{{ route('labelisasi_pvdc.index') }}"
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
                        value="{{ request('date') }}">
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
                        placeholder="Cari Varian / Operator...">
                </div>
            </div>

            <div class="col-md-3 align-self-end">
                <a href="{{ route('labelisasi_pvdc.index') }}" class="btn btn-primary mb-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Modal Warning Export --}}
    <div class="modal fade" id="warningModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">(!) Filter Belum Lengkap Untuk Export Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Silakan pilih <b>Tanggal</b> &amp; <b>Shift</b> yang spesifik di bagian filter terlebih dahulu sebelum melakukan export.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

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
                            <th>Hasil Pemeriksaan</th>
                            <th>QC</th>
                            <th>Operator</th>
                            <th>SPV</th>
                            @if($showAksi)
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $index => $dep)
                            <tr>
                                <td class="text-center">
                                    {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                                </td>

                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}
                                </td>

                                <td class="text-center">{{ $dep->nama_produk }}</td>

                                <td class="text-center">
                                    <a href="#"
                                        class="fw-bold text-decoration-underline btn-result"
                                        data-uuid="{{ $dep->uuid }}">
                                        Result
                                    </a>
                                </td>

                                <td class="text-center">
                                    {{ \Illuminate\Support\Facades\DB::table('users')->where('username', $dep->username)->value('name') }}
                                </td>

                                <td class="text-center">{{ $dep->nama_operator }}</td>

                                <td class="text-center">
                                    @if ($dep->status_spv == 0)
                                        <span class="fw-bold text-secondary">Created</span>
                                    @elseif ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                    @elseif ($dep->status_spv == 2)
                                        <a href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#revModal{{ $dep->uuid }}"
                                            class="text-danger fw-bold">
                                            Revision
                                        </a>

                                        <div class="modal fade"
                                            id="revModal{{ $dep->uuid }}"
                                            tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Detail Revisi</h5>
                                                        <button type="button"
                                                            class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <strong>Catatan:</strong> {{ $dep->catatan_spv }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                {{-- Aksi Audit --}}
                                @if($showAksi)
                                    <td class="text-center">
                                        @unless(auth()->user()->hasRole('auditor'))
                                            <form
                                                action="{{ route('labelisasi_pvdc.duplicateToAudit', $dep->uuid) }}"
                                                method="POST"
                                                class="d-inline"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="btn {{ $dep->is_audit ? 'btn-success' : 'btn-warning' }} btn-sm me-1 mb-1"
                                                    title="{{ $dep->is_audit ? 'Data Audit Sudah Tersedia : Duplikasi entry ini saja' : '' }}"
                                                    onclick="return confirm('Edit data ini sebagai data Audit?')"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                    Edit Data Audit
                                                </button>
                                            </form>
                                        @endunless
                                    </td>
                                @endif
                            </tr>

                            {{-- Modal Revision --}}
                            @if ($dep->status_spv == 2)
                                <div class="modal fade"
                                    id="revModal{{ $dep->uuid }}"
                                    tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Detail Revisi</h5>
                                                <button type="button"
                                                    class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <strong>Catatan:</strong> {{ $dep->catatan_spv }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const date = document.getElementById('filter_date');
    const shift = document.getElementById('filter_shift');
    const search = document.getElementById('search');

    let timer;

    [date, shift].forEach(function (el) {
        el.addEventListener('change', function () {
            form.submit();
        });
    });

    search.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            form.submit();
        }, 500);
    });

    document.querySelectorAll('.btn-result').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const uuid = this.dataset.uuid;

            fetch("{{ url('/labelisasi-pvdc') }}/" + uuid + "/result")
                .then(response => response.text())
                .then(html => {
                    document.body.insertAdjacentHTML('beforeend', html);

                    const modalElement = document.getElementById('resultModal' + uuid);
                    if (!modalElement) return;

                    const modal = new bootstrap.Modal(modalElement);

                    modalElement.addEventListener('hidden.bs.modal', function () {
                        this.remove();
                    });

                    modal.show();
                })
                .catch(error => {
                    console.error('Gagal mengambil hasil pemeriksaan:', error);
                });
        });
    });

    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (alert) {
            alert.classList.remove('show');
        });
    }, 3000);
});
</script>

<style>
    .table td,
    .table th {
        font-size: 0.85rem;
        white-space: nowrap;
    }
</style>
@endsection