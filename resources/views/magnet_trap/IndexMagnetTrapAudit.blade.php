@extends('layouts.app')

@section('content')
    @php
        $isAuditor = auth()->user()->hasRole('auditor');
        $showAksi = !$isAuditor && auth()->user()->can('can access update button');

        $userPlant = auth()->user()->plant;

        $bulkMaster = \App\Models\List_form::where('plant', $userPlant)
            ->where('laporan', 'Checklist Cleaning Magnet Trap')
            ->first();

        $bulkStd = $bulkMaster?->standar_ref;
        $bulkReady = !empty($bulkStd) && is_array($bulkStd);

        $bulkWhy = null;

        if (!$bulkReady) {
            if (!$bulkMaster) {
                $bulkWhy = "tidak ada baris List_form untuk plant '{$userPlant}'";
            } elseif (empty($bulkStd)) {
                $bulkWhy = 'Master Audit belum diterapkan untuk plant ini.';
            } else {
                $bulkWhy = 'standar_ref bertipe ' . gettype($bulkStd)
                    . ' (bukan array, cek $casts di model List_form)';
            }
        }

        $masterStatus = is_array($bulkStd)
            ? ($bulkStd['status'] ?? null)
            : null;

        $masterStatusDesc = match ($masterStatus) {
            'v' => 'ditandai Terdeteksi (OK)',
            'x' => 'ditandai Tidak Terdeteksi (NOT OK)',
            default => 'tidak diubah otomatis oleh Master',
        };

        $msgExisting = "Data audit sudah ada.\n"
            . "Klik OK untuk membuka dan mengedit data audit.\n"
            . "(Penerapan Master tidak dijalankan ulang; edit manual tetap aman.)";

        if ($bulkReady) {
            $msg = "Edit data audit dengan acuan Master Magnet Trap:\n\n"
                . "- Status Magnet Trap: {$masterStatusDesc}\n\n"
                . "Lanjutkan?";
        } else {
            $msg = "Master Audit belum diterapkan untuk plant ini.\n"
                . "Auto correct tidak dijalankan.\n"
                . "Salinan audit menggunakan data asli untuk diedit manual.\n\n"
                . "Lanjutkan?";
        }
    @endphp

    <div class="container-fluid py-0">

        {{-- ===================== ALERTS ===================== --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ trim(session('success')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ===================== HEADER ===================== --}}
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Data Audit Magnet Trap</h2>
        </div>

        {{-- ===================== MODE AUDIT ===================== --}}
        @unless ($isAuditor)
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

                .audit-action-group {
                    display: flex;
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 5px;
                }

                .audit-action-group .btn {
                    white-space: nowrap;
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
                            <i class="bi bi-clipboard-check-fill"></i>
                            Mode Data Audit
                        </div>

                        <div class="audit-banner__desc">
                            Perubahan di halaman ini hanya berlaku pada
                            <b>salinan data audit</b> untuk keperluan Audit QC System.
                            Data operasional utama tidak terpengaruh.
                        </div>
                    </div>
                </div>

                <div class="audit-banner__actions">
                    <span title="{{ $bulkReady ? 'Duplikasi audit massal' : 'Nonaktif: ' . $bulkWhy }}">
                        <button
                            type="button"
                            class="audit-pill"
                            data-bs-toggle="modal"
                            data-bs-target="#bulkAuditModal"
                            {{ $bulkReady ? '' : 'disabled' }}
                        >
                            <i class="bi bi-files"></i>
                            Duplikasi Data Audit Massal
                        </button>
                    </span>

                    <form
                        action="{{ route('toggle.mode.audit') }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Kembali ke mode Operasional?')"
                    >
                        @csrf

                        <button type="submit" class="audit-pill">
                            <i class="bi bi-clipboard-check"></i>
                            Mode Operasional
                        </button>
                    </form>
                </div>
            </div>

            {{-- ===================== MODAL DUPLIKASI MASSAL ===================== --}}
            @if ($bulkReady)
                <div class="modal fade" id="bulkAuditModal" tabindex="-1"
                    aria-labelledby="bulkAuditModalLabel" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered">
                        <form
                            method="POST"
                            action="{{ route('checklistmagnettrap.auditBulkExecute') }}"
                            class="w-100"
                            onsubmit="return confirm('Salinan audit akan dibuat untuk data yang belum memiliki audit dan status Master Magnet Trap diterapkan otomatis. Lanjutkan?')"
                        >
                            @csrf

                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="bulkAuditModalLabel">
                                        Duplikasi Audit Massal Magnet Trap
                                    </h5>

                                    <button type="button" class="btn-close"
                                        data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label for="bulkStart" class="form-label">
                                                Tanggal awal
                                            </label>

                                            <input type="date" name="start_date"
                                                id="bulkStart" class="form-control" required>
                                        </div>

                                        <div class="col-6">
                                            <label for="bulkEnd" class="form-label">
                                                Tanggal akhir
                                            </label>

                                            <input type="date" name="end_date"
                                                id="bulkEnd" class="form-control" required>
                                        </div>
                                    </div>

                                    <div id="bulkPreview"
                                        class="alert alert-secondary py-2 small mb-3">
                                        Pilih tanggal untuk melihat ringkasan.
                                    </div>

                                    <div class="small">
                                        <strong>Standar Master Magnet Trap yang akan diterapkan:</strong>

                                        <ul class="mb-0">
                                            <li>Status Magnet Trap: {{ $masterStatusDesc }}</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">
                                        Batal
                                    </button>

                                    <button type="submit" id="bulkRun"
                                        class="btn btn-success btn-sm" disabled>
                                        Jalankan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const start = document.getElementById('bulkStart');
                        const end = document.getElementById('bulkEnd');
                        const box = document.getElementById('bulkPreview');
                        const run = document.getElementById('bulkRun');
                        const modal = document.getElementById('bulkAuditModal');

                        function show(bg, color, html) {
                            box.style.cssText =
                                'display:block;padding:8px 12px;margin-bottom:1rem;' +
                                'border-radius:6px;font-size:.85rem;background:' +
                                bg + ';color:' + color + ';';

                            box.innerHTML = html;
                        }

                        function showError(html) {
                            show('#f8d7da', '#842029', html);
                        }

                        async function preview() {
                            run.disabled = true;

                            if (!start.value || !end.value) {
                                show('#e9ecef', '#212529',
                                    'Pilih tanggal untuk melihat ringkasan.');
                                return;
                            }

                            if (end.value < start.value) {
                                showError('Tanggal akhir tidak boleh sebelum tanggal awal.');
                                return;
                            }

                            show('#e9ecef', '#212529', 'Memuat...');

                            try {
                                const response = await fetch(
                                    @json(route('checklistmagnettrap.auditBulkPreview')),
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': @json(csrf_token()),
                                        },
                                        body: JSON.stringify({
                                            start_date: start.value,
                                            end_date: end.value
                                        }),
                                    }
                                );

                                if (!response.ok) {
                                    let message = 'Gagal memuat ringkasan (HTTP ' +
                                        response.status + ').';

                                    try {
                                        const error = await response.json();

                                        if (error.errors) {
                                            message = Object.values(error.errors)
                                                .flat().join('<br>');
                                        } else if (error.message) {
                                            message = error.message;
                                        }
                                    } catch (e) {}

                                    showError(message);
                                    return;
                                }

                                const data = await response.json();

                                if (data.total === 0) {
                                    show('#fff3cd', '#664d03',
                                        'Tidak ada entry di tanggal ini.');
                                    return;
                                }

                                show('#cff4fc', '#055160',
                                    'Total entry: <b>' + data.total + '</b><br>' +
                                    'Sudah punya audit (dilewati): <b>' +
                                    data.sudah_ada + '</b><br>' +
                                    'Akan diduplikasi dan disesuaikan: <b>' +
                                    data.akan_dibuat + '</b>');

                                run.disabled = data.akan_dibuat === 0;
                            } catch (error) {
                                showError('Gagal memuat ringkasan: ' +
                                    (error.message || 'tidak diketahui'));
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
                    });
                </script>
            @endif
        @endunless

        {{-- ===================== FILTER ===================== --}}
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
                    <a href="{{ route('checklistmagnettrap.audit') }}"
                        class="btn btn-primary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- ===================== TABEL DATA ===================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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

                                @if ($showAksi)
                                    <th>Aksi</th>
                                @endif
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
                                        @if ($item->status == 'v')
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
                                        {{ \Illuminate\Support\Str::limit($item->keterangan ?? '-', 35) }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ optional($item->produksi)->nama_karyawan ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ optional($item->engineer)->nama_karyawan ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        @if ($item->status_spv == 1)
                                            <span class="badge-status status-verified">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Verified
                                            </span>
                                        @elseif ($item->status_spv == 2)
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

                                    {{-- ===================== KOLOM AKSI ===================== --}}
                                    @if ($showAksi)
                                        <td class="text-center align-middle">
                                            <div class="audit-action-group">
                                                @if ($item->is_audit)
                                                    <a
                                                        href="{{ route('checklistmagnettrap.edit', $item->id) }}"
                                                        class="btn btn-success btn-sm"
                                                        title="Edit data audit manual"
                                                        onclick="return confirm({{ \Illuminate\Support\Js::from($msgExisting) }})"
                                                    >
                                                        <i class="bi bi-pencil-square"></i>
                                                        Edit Data Audit Manual
                                                    </a>
                                                @else
                                                    <form
                                                        action="{{ route('checklistmagnettrap.duplicateToAudit', $item->uuid) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                    >
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="btn btn-dark btn-sm"
                                                            title="Buat salinan untuk diedit manual"
                                                            onclick="return confirm({{ \Illuminate\Support\Js::from($msg) }})"
                                                        >
                                                            <i class="bi bi-files"></i>
                                                            Edit Data Audit Manual
                                                        </button>
                                                    </form>

                                                    <button
                                                        type="button"
                                                        class="btn btn-warning btn-sm"
                                                        title="{{ $bulkReady ? 'Buat salinan audit dengan acuan Master Magnet Trap' : 'Nonaktif: ' . $bulkWhy }}"
                                                        data-url="{{ route('checklistmagnettrap.duplicateToAuditAuto', $item->uuid) }}"
                                                        onclick="magicDuplicate(this, {{ \Illuminate\Support\Js::from($msg) }})"
                                                        {{ $bulkReady ? '' : 'disabled' }}
                                                    >
                                                        <i class="bi bi-magic"></i>
                                                        Edit Data Audit Otomatis
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $showAksi ? 11 : 10 }}"
                                        class="text-center py-4">
                                        Belum ada data audit magnet trap.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ===================== PAGINATION ===================== --}}
                <div class="mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== DUPLIKASI OTOMATIS ===================== --}}
    @if ($showAksi)
        <script>
            async function magicDuplicate(btn, msg) {
                if (!confirm(msg)) {
                    return;
                }

                const original = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                try {
                    const response = await fetch(btn.dataset.url, {
                        method: 'POST',
                        redirect: 'manual',
                        headers: {
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json',
                        },
                    });

                    if (response.type === 'opaqueredirect' || response.ok) {
                        location.reload();
                        return;
                    }

                    throw new Error('HTTP ' + response.status);
                } catch (error) {
                    alert(
                        'Gagal membuat salinan audit: ' +
                        (error && error.message ? error.message : 'tidak diketahui')
                    );

                    btn.disabled = false;
                    btn.innerHTML = original;
                }
            }
        </script>
    @endif

    {{-- ===================== AUTO SUBMIT FILTER ===================== --}}
    <script>
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

    {{-- ===================== AUTO-HIDE ALERT ===================== --}}
    <script>
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');

                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    </script>

    {{-- ===================== STYLE ===================== --}}
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
            color: #dc3545;
        }
    </style>
@endsection