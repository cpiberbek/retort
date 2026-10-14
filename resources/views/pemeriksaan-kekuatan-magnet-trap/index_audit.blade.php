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
            justify-content: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            line-height: 1.2;
            color: #fff;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .38);
            transition: background .15s ease;
            cursor: pointer;
        }

        .audit-pill:hover:not(:disabled) {
            color: #fff;
            background: rgba(255, 255, 255, .24);
        }

        .audit-pill:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .audit-modal-label {
            font-size: .85rem;
            font-weight: 600;
            margin-bottom: .4rem;
        }

        .audit-preview-summary {
            border-radius: .65rem;
            padding: 1rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }

        .audit-preview-number {
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .audit-preview-caption {
            font-size: .8rem;
            color: #6c757d;
        }

        .audit-rule-list {
            padding-left: 1.2rem;
            margin-bottom: 0;
        }

        .audit-rule-list li {
            margin-bottom: .35rem;
        }

        .audit-modal-result {
            max-height: 300px;
            overflow-y: auto;
        }

        .audit-pill form {
            margin: 0;
        }
    </style>
@endpush

@section('content')
@php
    $isAuditor = auth()->user()->hasRole('auditor');
    $showAksi = !$isAuditor && auth()->user()->can('can access update button');

    $userPlant = auth()->user()->plant;

    $bulkMaster = \App\Models\List_form::where('plant', $userPlant)
        ->where('laporan', 'Pemeriksaan Kekuatan Magnet Trap')
        ->first();

    $bulkStd = $bulkMaster?->standar_ref;

    if (is_string($bulkStd)) {
        $bulkStd = json_decode($bulkStd, true);
    }

    $bulkReady = is_array($bulkStd) && !empty($bulkStd);

    $bulkWhy = null;

    if (!$bulkReady) {
        if (!$bulkMaster) {
            $bulkWhy = "Master List_form untuk plant '{$userPlant}' tidak ditemukan.";
        } else {
            $bulkWhy = 'Master Audit belum diterapkan untuk plant ini.';
        }
    }

    $msgExisting = "Data audit sudah ada.\nKlik OK untuk membuka dan mengedit data audit.\nAuto correct tidak dijalankan ulang, sehingga perubahan manual tetap aman.";

    $descRule = function ($key, $rule) {
        if (!is_array($rule)) {
            return 'tidak diatur';
        }

        $min = $rule['min'] ?? null;
        $max = $rule['max'] ?? null;

        if ($min !== null && $min !== '' && $max !== null && $max !== '') {
            return "nilai tengah {$key}: " . number_format(((float) $min + (float) $max) / 2, 2, '.', '');
        }

        return 'tidak diubah';
    };

    $bulkDesc = function ($rule) {
        if (is_array($rule)) {
            if (isset($rule['min'], $rule['max'])) {
                return "diacak {$rule['min']} s/d {$rule['max']} Gauss";
            }

            return 'tidak diubah';
        }

        if ((string) $rule === '1') {
            return 'diatur Sesuai (√)';
        }

        if ((string) $rule === '0') {
            return 'diatur Tidak Sesuai (X)';
        }

        return 'tidak diubah';
    };
@endphp

<div class="container-fluid py-0">

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($message = Session::get('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Data Audit Pemeriksaan Kekuatan Magnet Trap</h2>
        </div>
    </div>

    @unless($isAuditor)
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
                        Perubahan di halaman ini hanya berlaku pada <b>salinan data audit</b>
                        untuk keperluan Audit QC System. Data operasional utama tidak terpengaruh.
                    </div>
                </div>
            </div>

            <div class="audit-banner__actions">
                <button
                    type="button"
                    class="audit-pill"
                    data-bs-toggle="modal"
                    data-bs-target="#bulkAuditModal"
                    {{ $bulkReady ? '' : 'disabled' }}
                    title="{{ $bulkReady ? 'Duplikasi audit massal' : 'Nonaktif: ' . $bulkWhy }}"
                >
                    <i class="bi bi-files"></i> Duplikasi Data Audit Massal
                </button>

                <form
                    action="{{ route('toggle.mode.audit') }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Kembali ke mode Operasional?')"
                >
                    @csrf
                    <button type="submit" class="audit-pill">
                        <i class="bi bi-clipboard-check"></i> Mode Operasional
                    </button>
                </form>
            </div>
        </div>

        @if($bulkReady)
            <div class="modal fade" id="bulkAuditModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form
                        method="POST"
                        action="{{ route('pemeriksaan-kekuatan-magnet-trap.auditBulk.execute') }}"
                        class="w-100"
                        onsubmit="return confirm('Data audit akan dibuat dan SEMUA nilai sesuai master akan diterapkan. Lanjutkan?')"
                    >
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
                                        <input
                                            type="date"
                                            name="start_date"
                                            id="bulkStart"
                                            class="form-control"
                                            required
                                        >
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label">Tanggal akhir</label>
                                        <input
                                            type="date"
                                            name="end_date"
                                            id="bulkEnd"
                                            class="form-control"
                                            required
                                        >
                                    </div>
                                </div>

                                <div id="bulkPreview" class="alert alert-secondary py-2 small mb-3">
                                    Pilih tanggal untuk melihat ringkasan.
                                </div>

                                <div class="small">
                                    <strong>Standar yang akan diterapkan (semua nilai ditimpa):</strong>
                                    <ul class="mb-0">
                                        @for($i = 1; $i <= 6; $i++)
                                            <li>
                                                Kekuatan Magnet {{ $i }}:
                                                {{ $bulkDesc($bulkStd['kekuatan_median_' . $i] ?? null) }}
                                            </li>
                                        @endfor
                                        <li>
                                            Parameter Settingan:
                                            {{ $bulkDesc($bulkStd['parameter_sesuai'] ?? null) }}
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                    Batal
                                </button>
                                <button type="submit" id="bulkRun" class="btn btn-success btn-sm" disabled>
                                    Jalankan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endunless

    <form
        id="filterForm"
        method="GET"
        action="{{ route('pemeriksaan-kekuatan-magnet-trap.audit') }}"
        class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm"
    >
        <div class="row w-100">
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
                        placeholder="Cari kondisi magnet, petugas..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>

            <div class="col-md-3 align-self-end">
                <a href="{{ route('pemeriksaan-kekuatan-magnet-trap.audit') }}" class="btn btn-primary mb-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <div class="card card-custom mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th style="width: 4%;">No</th>
                            <th style="width: 9%;">Tanggal</th>
                            <th style="width: 14%;">Kondisi Visual</th>
                            <th style="width: 11%;">Petugas QC</th>
                            <th style="width: 9%;">Parameter</th>
                            <th style="width: 9%;">Status SPV</th>
                            <th style="width: 19%;">Hasil Pemeriksaan</th>
                            @can('can access update button')
                                <th style="width: 25%;">Aksi</th>
                            @endcan
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pemeriksaanKekuatanMagnetTraps as $item)
                            <tr>
                                <td class="text-center fw-bold text-secondary">
                                    {{ $loop->iteration + (($pemeriksaanKekuatanMagnetTraps->currentPage() - 1) * $pemeriksaanKekuatanMagnetTraps->perPage()) }}
                                </td>

                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                </td>

                                <td>{{ $item->kondisi_magnet_trap ?? '-' }}</td>

                                <td>{{ $item->petugas_qc ?? '-' }}</td>

                                <td class="text-center">
                                    @if($item->parameter_sesuai === null)
                                        <span class="badge bg-secondary">Belum Diatur</span>
                                    @elseif((int) $item->parameter_sesuai === 1)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> Sesuai
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i> Tdk Sesuai
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($item->status_spv == 0)
                                        <span class="badge-status status-pending">
                                            <i class="bi bi-hourglass-split me-1"></i> Pending
                                        </span>
                                    @elseif($item->status_spv == 1)
                                        <span class="badge-status status-verified">
                                            <i class="bi bi-check-circle me-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge-status status-revision">
                                            <i class="bi bi-x-circle me-1"></i> Revisi
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a
                                        href="{{ route('pemeriksaan-kekuatan-magnet-trap.show', $item->id) }}"
                                        class="btn btn-outline-primary btn-sm"
                                        title="Detail"
                                    >
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>

                                @can('can access update button')
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            @if($showAksi)
                                                @if($item->is_audit)
                                                    <a
                                                        href="{{ route('pemeriksaan-kekuatan-magnet-trap.edit', $item->id) }}"
                                                        class="btn btn-success btn-sm"
                                                        title="Data audit sudah ada"
                                                        onclick="return confirm({{ \Illuminate\Support\Js::from($msgExisting) }})"
                                                    >
                                                        <i class="bi bi-files"></i> Edit Data Audit Manual
                                                    </a>
                                                @else
                                                    @php
                                                        if ($bulkReady) {
                                                            $msgAuto = "Salinan data audit Magnet Trap akan dibuat dari data asli.\n\n";

                                                            for ($i = 1; $i <= 6; $i++) {
                                                                $key = 'kekuatan_median_' . $i;
                                                                $rule = $bulkStd[$key] ?? null;
                                                                $min = data_get($rule, 'min');
                                                                $max = data_get($rule, 'max');

                                                                if (
                                                                    $min !== null && $min !== '' &&
                                                                    $max !== null && $max !== '' &&
                                                                    is_numeric($min) &&
                                                                    is_numeric($max) &&
                                                                    (float) $min <= (float) $max
                                                                ) {
                                                                    $msgAuto .= "- Kekuatan Magnet {$i}: Nilai diacak dari {$min} sampai {$max} Gauss\n";
                                                                } else {
                                                                    $msgAuto .= "- Kekuatan Magnet {$i}: Tidak diubah\n";
                                                                }
                                                            }

                                                            $parameterRule = $bulkStd['parameter_sesuai'] ?? null;

                                                            if (in_array((string) $parameterRule, ['0', '1'], true)) {
                                                                $msgAuto .= "- Parameter Settingan: " . (
                                                                    (string) $parameterRule === '1'
                                                                        ? 'Sesuai (√)'
                                                                        : 'Tidak Sesuai (X)'
                                                                ) . "\n";
                                                            } else {
                                                                $msgAuto .= "- Parameter Settingan: Tidak diubah\n";
                                                            }

                                                            $msgAuto .= "\nLanjutkan?";
                                                            $msgManual = $msgAuto;
                                                        } else {
                                                            $msgManual = "Master belum diterapkan untuk plant ini.\nAuto correct tidak dijalankan, salinan memakai data asli untuk edit manual.\n\nLanjutkan?";
                                                            $msgAuto = $msgManual;
                                                        }
                                                    @endphp

                                                    <form
                                                        action="{{ route('pemeriksaan-kekuatan-magnet-trap.duplicateToAudit', $item->uuid) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                    >
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="btn btn-dark btn-sm"
                                                            title="Buat salinan audit manual"
                                                            onclick="return confirm({{ \Illuminate\Support\Js::from($msgManual) }})"
                                                        >
                                                            <i class="bi bi-files"></i> Edit Data Audit Manual
                                                        </button>
                                                    </form>

                                                    <button
                                                        type="button"
                                                        class="btn btn-warning btn-sm"
                                                        title="{{ $bulkReady ? 'Buat salinan dan terapkan standar master secara acak' : 'Nonaktif: ' . $bulkWhy }}"
                                                        data-url="{{ route('pemeriksaan-kekuatan-magnet-trap.duplicateToAuditAuto', $item->uuid) }}"
                                                        onclick="magicDuplicate(this, {{ \Illuminate\Support\Js::from($msgAuto) }})"
                                                        {{ $bulkReady ? '' : 'disabled' }}
                                                    >
                                                        <i class="bi bi-magic"></i> Edit Data Audit Otomatis
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="mb-2">
                                        <i class="bi bi-clipboard-x display-4 text-secondary opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold">Data tidak ditemukan</h6>
                                    <p class="small mb-0">Silakan ubah filter pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($pemeriksaanKekuatanMagnetTraps->hasPages())
        <div class="mt-3">
            {!! $pemeriksaanKekuatanMagnetTraps->withQueryString()->links('pagination::bootstrap-5') !!}
        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filterForm');
        const month = document.getElementById('filter_month');
        const date = document.getElementById('filter_date');
        const search = document.getElementById('filter_search');

        let debounceTimer;

        date.addEventListener('change', function () {
            if (date.value) {
                month.value = '';
            }

            form.submit();
        });

        month.addEventListener('change', function () {
            if (month.value) {
                date.value = '';
            }

            form.submit();
        });

        search.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => form.submit(), 600);
        });

        const start = document.getElementById('bulkStart');
        const end = document.getElementById('bulkEnd');
        const box = document.getElementById('bulkPreview');
        const run = document.getElementById('bulkRun');
        const modal = document.getElementById('bulkAuditModal');

        if (start && end && box && run && modal) {
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

                if (!start.value || !end.value) {
                    show('#e9ecef', '#212529', 'Pilih tanggal untuk melihat ringkasan.');
                    return;
                }

                if (end.value < start.value) {
                    showError('Tanggal akhir tidak boleh sebelum tanggal awal.');
                    return;
                }

                show('#e9ecef', '#212529', 'Memuat...');

                try {
                    const res = await fetch(
                        @json(route('pemeriksaan-kekuatan-magnet-trap.auditBulk.preview')),
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token())
                            },
                            body: JSON.stringify({
                                start_date: start.value,
                                end_date: end.value
                            })
                        }
                    );

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

                    show(
                        '#cff4fc',
                        '#055160',
                        'Total entry: <b>' + d.total + '</b><br>' +
                        'Sudah punya audit (dilewati): <b>' + d.sudah_ada + '</b><br>' +
                        'Akan diduplikasi dan dikoreksi: <b>' + d.akan_dibuat + '</b>'
                    );

                    run.disabled = d.akan_dibuat === 0;
                } catch (err) {
                    showError('Error: ' + (err && err.message ? err.message : 'tidak diketahui'));
                }
            }

            start.addEventListener('change', function () {
                end.min = start.value;

                if (end.value && end.value < start.value) {
                    end.value = start.value;
                }

                preview();
            });

            end.addEventListener('change', preview);
            modal.addEventListener('shown.bs.modal', preview);
        }
    });

    @if($showAksi)
        async function magicDuplicate(btn, msg) {
            if (!confirm(msg)) {
                return;
            }

            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const res = await fetch(btn.dataset.url, {
                    method: 'POST',
                    redirect: 'manual',
                    headers: {
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'Accept': 'text/html'
                    }
                });

                if (res.type === 'opaqueredirect' || res.ok) {
                    location.reload();
                    return;
                }

                throw new Error('HTTP ' + res.status);
            } catch (err) {
                alert('Gagal menduplikasi: ' + (err && err.message ? err.message : 'tidak diketahui'));
                btn.disabled = false;
                btn.innerHTML = original;
            }
        }
    @endif
</script>
@endsection
