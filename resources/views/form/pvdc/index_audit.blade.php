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

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
        <h2 class="h4">Data No. Lot PVDC</h2>
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
                <button type="button" class="audit-pill" data-bs-toggle="modal" data-bs-target="#bulkAuditModal">
                    <i class="bi bi-files"></i> Duplikasi Data Audit Massal
                </button>

                <form action="{{ route('toggle.mode.audit') }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Kembali ke mode Operasional?')">
                    @csrf
                    <button type="submit" class="audit-pill">
                        <i class="bi bi-clipboard-check"></i> Mode Operasional
                    </button>
                </form>
            </div>
        </div>

        {{-- MODAL DUPLIKASI AUDIT MASSAL --}}
        <div class="modal fade" id="bulkAuditModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('pvdc.auditBulk.execute') }}" class="w-100"
                    onsubmit="return confirm('Salinan data audit akan dibuat untuk semua data pada rentang tanggal ini. Lanjutkan?')">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Duplikasi Audit Massal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label">Tanggal awal</label>
                                    <input type="date" name="start_date" id="bulkStart" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Tanggal akhir</label>
                                    <input type="date" name="end_date" id="bulkEnd" class="form-control" required>
                                </div>
                            </div>

                            <div id="bulkPreview" class="alert alert-secondary py-2 small mb-3">
                                Pilih tanggal untuk melihat ringkasan.
                            </div>

                            <div class="small text-muted">
                                Data disalin apa adanya, tanpa penyesuaian nilai.
                                Entry yang sudah punya salinan audit dilewati.
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="bulkRun" class="btn btn-success btn-sm" disabled>Jalankan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const s = document.getElementById('bulkStart');
                const e = document.getElementById('bulkEnd');
                const box = document.getElementById('bulkPreview');
                const run = document.getElementById('bulkRun');
                const modal = document.getElementById('bulkAuditModal');

                function show(bg, color, html) {
                    box.removeAttribute('class');
                    box.style.cssText = 'display:block;padding:8px 12px;margin-bottom:1rem;border-radius:6px;font-size:.85rem;background:' + bg + ';color:' + color + ';';
                    box.innerHTML = html;
                }

                function showError(html) {
                    show('#f8d7da', '#842029', html);
                }

                async function preview() {
                    run.disabled = true;

                    if (!s.value || !e.value) {
                        show('#e9ecef', '#212529', 'Pilih tanggal untuk melihat ringkasan.');
                        return;
                    }

                    if (e.value < s.value) {
                        showError('Tanggal akhir tidak boleh sebelum tanggal awal.');
                        return;
                    }

                    show('#e9ecef', '#212529', 'Memuat...');

                    try {
                        const res = await fetch(@json(route('pvdc.auditBulk.preview')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                            },
                            body: JSON.stringify({ start_date: s.value, end_date: e.value }),
                        });

                        if (!res.ok) {
                            let msg = 'Gagal memuat ringkasan (HTTP ' + res.status + ').';
                            try {
                                const err = await res.json();
                                if (err.errors) {
                                    msg = Object.values(err.errors).flat().join('<br>');
                                } else if (err.message) {
                                    msg = err.message;
                                }
                            } catch (x) {}
                            showError(msg);
                            return;
                        }

                        const d = await res.json();

                        if (d.total === 0) {
                            show('#fff3cd', '#664d03', 'Tidak ada entry di tanggal ini.');
                            return;
                        }

                        show('#cff4fc', '#055160',
                            'Total entry: <b>' + d.total + '</b><br>' +
                            'Sudah punya audit (dilewati): <b>' + d.sudah_ada + '</b><br>' +
                            'Akan diduplikasi: <b>' + d.akan_dibuat + '</b>');

                        run.disabled = d.akan_dibuat === 0;
                    } catch (err) {
                        showError('Error: ' + (err && err.message ? err.message : 'tidak diketahui'));
                    }
                }

                s.addEventListener('change', function () {
                    e.min = s.value;
                    if (e.value && e.value < s.value) e.value = s.value;
                    preview();
                });
                e.addEventListener('change', preview);
                modal.addEventListener('shown.bs.modal', preview);
            });
        </script>
    @endunless

    {{-- Filter Bar --}}
    <form id="filterForm" method="GET" action="{{ route('pvdc.audit') }}"
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
                <div class="mb-1">Cari Varian</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" name="search" id="search"
                        class="form-control border-start-0"
                        value="{{ request('search') }}"
                        placeholder="Cari Varian">
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

            <div class="col-md-3 align-self-end">
                <a href="{{ route('pvdc.audit') }}" class="btn btn-primary mb-2">
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
                    Silakan pilih <b>Tanggal</b> dan <b>Nama Varian</b> yang spesifik di bagian filter terlebih dahulu
                    sebelum melakukan export.

                    <br><br>
                    *Copy Nama Varian (bisa klik tombol <i class="bi bi-copy text-muted"></i> disebelah tiap
                    produk/varian) setelah Tanggal diterapkan agar tidak perlu mengetik manual
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
                            <th>Nama Supplier</th>
                            <th>Tanggal Kedatangan</th>
                            <th>Tanggal Expired</th>
                            <th>Data PVDC</th>
                            <th>QC</th>
                            <th>SPV</th>
                            @if($showAksi)
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $dep)
                            <tr>
                                <td class="text-center align-middle">
                                    {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}
                                </td>

                                <td class="align-middle">
                                    {{ $dep->nama_produk }}
                                    <span class="badge bg-light text-dark border ms-1 copy-badge"
                                        style="cursor:pointer"
                                        data-text="{{ $dep->nama_produk }}"
                                        title="Copy">
                                        <i class="bi bi-copy"></i>
                                    </span>
                                </td>

                                <td class="align-middle">{{ $dep->nama_supplier }}</td>

                                <td class="text-center align-middle">
                                    {{ \Carbon\Carbon::parse($dep->tgl_kedatangan)->format('d-m-Y') }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ \Carbon\Carbon::parse($dep->tgl_expired)->format('d-m-Y') }}
                                </td>

                                <td class="text-center align-middle">
                                    @php
                                        $data_pvdc = json_decode($dep->data_pvdc, true);
                                    @endphp

                                    @if (!empty($data_pvdc))
                                        @php
                                            $batches = $dep->pvdc_detail
                                                ->flatMap(function ($mesin) {
                                                    return $mesin['detail']
                                                        ->pluck('mincing.kode_produksi')
                                                        ->filter();
                                                })
                                                ->unique()
                                                ->values()
                                                ->implode(', ');
                                        @endphp

                                        <a href="#" data-bs-toggle="modal"
                                            data-bs-target="#pvdcModal{{ $dep->uuid }}"
                                            style="font-weight: bold; text-decoration: underline;">
                                            Result
                                        </a>

                                        <div class="modal fade" id="pvdcModal{{ $dep->uuid }}" tabindex="-1"
                                            aria-labelledby="pvdcModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title"
                                                            id="pvdcModalLabel{{ $dep->uuid }}">
                                                            Detail Pemeriksaan PVDC - Batch: {{ $batches ?: 'N/A' }}
                                                        </h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body table-responsive">
                                                        @foreach ($dep->pvdc_detail as $mesin)
                                                            <div class="mb-3 border p-3 rounded bg-light">
                                                                <h6 class="fw-bold mb-2">
                                                                    🧭 Mesin: {{ $mesin['mesin'] ?? '-' }}
                                                                </h6>

                                                                <table class="table table-bordered table-striped table-sm text-center align-middle bg-white">
                                                                    <thead class="table-secondary">
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Batch</th>
                                                                            <th>No. Lot</th>
                                                                            <th>Waktu</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @if (!empty($mesin['detail']))
                                                                            @foreach ($mesin['detail'] as $detail)
                                                                                <tr>
                                                                                    <td>{{ $loop->iteration }}</td>
                                                                                    <td>{{ $detail['mincing']->kode_produksi ?? '-' }}</td>
                                                                                    <td>{{ $detail['no_lot'] ?? '-' }}</td>
                                                                                    <td>{{ $detail['waktu'] ?? '-' }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="4">Tidak ada data batch</td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endforeach

                                                        <div class="mt-3 text-start">
                                                            <strong>Catatan:</strong> {{ $dep->catatan ?? '-' }}
                                                        </div>
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
                                    @if ($dep->status_spv == 0)
                                        <span class="fw-bold text-secondary">Created</span>
                                    @elseif ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                    @elseif ($dep->status_spv == 2)
                                        <a href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#revisionModal{{ $dep->uuid }}"
                                            class="text-danger fw-bold text-decoration-none">
                                            Revision
                                        </a>

                                        <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Detail Revisi</h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <ul class="list-unstyled mb-0">
                                                            <li><strong>Status:</strong> Revision</li>
                                                            <li><strong>Catatan:</strong> {{ $dep->catatan_spv ?? '-' }}</li>
                                                        </ul>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                @if($showAksi)
                                    <td class="text-center">
                                        @if($dep->is_audit)
                                            {{-- Ini salinan audit: edit manual --}}
                                            <a href="{{ route('pvdc.edit.form', $dep->uuid) }}"
                                                class="btn btn-success btn-sm me-1 mb-1"
                                                title="Edit manual data Audit">
                                                <i class="bi bi-pencil"></i> Edit Data Audit Manual
                                            </a>
                                        @else
                                            {{-- Data asli yang belum punya salinan: duplikat otomatis --}}
                                            <form action="{{ route('pvdc.autoDuplicateToAudit', $dep->uuid) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Duplikat data ini menjadi data Audit?')">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm me-1 mb-1"
                                                    title="Duplikat otomatis menjadi data Audit">
                                                    <i class="bi bi-magic text-dark"></i> <span class="text-dark">Duplikat Data Audit Otomatis</span>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showAksi ? 10 : 9 }}" class="text-center">
                                    Belum ada data PVDC.
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

    document.querySelectorAll('.copy-badge').forEach(function (badge) {
        badge.addEventListener('click', async function () {
            await navigator.clipboard.writeText(this.dataset.text);

            const oldHtml = this.innerHTML;
            const oldTitle = this.title;

            this.innerHTML = 'Ter-Copy';
            this.title = 'Ter-Copy';

            setTimeout(() => {
                this.innerHTML = oldHtml;
                this.title = oldTitle;
            }, 1500);
        });
    });

    setTimeout(function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
        });
    }, 3000);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const exportPdfBtn = document.getElementById('exportPdfBtn');

    if (!exportPdfBtn) return;

    const dateInput = document.getElementById('filter_date');
    const searchInput = document.getElementById('search');
    const shiftInput = document.getElementById('filter_shift');

    exportPdfBtn.addEventListener('click', function (e) {
        e.preventDefault();

        let dateVal = dateInput.value.trim();
        let searchVal = searchInput.value.trim();
        let shiftVal = shiftInput.value.trim();

        if (!dateVal || !searchVal) {
            $('#warningModal').modal('show');
            return;
        }

        let exportUrl = "{{ route('pvdc.exportPdf') }}" +
            "?date=" + encodeURIComponent(dateVal) +
            "&shift=" + encodeURIComponent(shiftVal) +
            "&search=" + encodeURIComponent(searchVal);

        window.open(exportUrl, '_blank');
    });
});
</script>

<style>
    .table td,
    .table th {
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .text-success {
        color: green;
        font-weight: bold;
    }

    .text-danger {
        color: red;
        font-weight: bold;
    }

    .container {
        padding-left: 2px !important;
        padding-right: 2px !important;
    }
</style>
@endsection