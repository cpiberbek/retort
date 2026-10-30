@extends('layouts.app')

@section('content')
    @php
        $isAuditor = auth()->user()->hasRole('auditor');
        $showAksi = !$isAuditor && auth()->user()->can('can access update button');
        $userPlant = auth()->user()->plant;

        $bulkMaster = \App\Models\List_form::where('plant', $userPlant)
            ->where('laporan', 'Verifikasi Timer Chamber')
            ->first();

        $bulkStd = $bulkMaster?->standar_ref;
        if (is_string($bulkStd)) {
            $bulkStd = json_decode($bulkStd, true);
        }

        $bulkReady = !empty($bulkStd) && is_array($bulkStd);
        $bulkWhy = null;

        if (!$bulkReady) {
            if (!$bulkMaster) {
                $bulkWhy = "tidak ada baris List_form untuk plant '{$userPlant}'";
            } elseif (empty($bulkStd)) {
                $bulkWhy = 'Master Audit belum diterapkan untuk plant ini.';
            } else {
                $bulkWhy = 'standar_ref bukan array yang valid.';
            }
        }

        $rentangMenit = [5, 10, 20, 30, 60];
        $timerFields = [
            'plc_menit' => 'PLC Menit',
            'plc_detik' => 'PLC Detik',
            'stopwatch_menit' => 'Stopwatch Menit',
            'stopwatch_detik' => 'Stopwatch Detik',
            'faktor_koreksi' => 'Faktor Koreksi',
        ];

        $descRule = function ($rule, $key = '') {
            if (is_array($rule) && isset($rule['min'], $rule['max'])) {
                $integer = str_contains($key, 'menit') || str_contains($key, 'detik');

                return $integer
                    ? "diacak bilangan bulat {$rule['min']} s/d {$rule['max']}"
                    : "diacak {$rule['min']} s/d {$rule['max']} (maks. 2 desimal)";
            }

            if (is_array($rule) && isset($rule['max'])) {
                return "diacak di bawah {$rule['max']}";
            }

            if ($rule === null || $rule === '') {
                return 'tidak diubah';
            }

            return "nilai tetap {$rule}";
        };

        $bulkDesc = fn ($key) => $descRule(data_get($bulkStd, $key), $key);
    @endphp

    <div class="container-fluid py-0">
        {{-- ALERTS --}}
        @foreach (['success' => 'success', 'warning' => 'warning', 'info' => 'info', 'error' => 'danger'] as $sessionKey => $alertType)
            @if (session($sessionKey))
                <div class="alert alert-{{ $alertType }} alert-dismissible fade show" role="alert">
                    {{ trim(session($sessionKey)) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach

        {{-- HEADER --}}
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h4>Data Verifikasi Timer Chamber</h4>
        </div>

        {{-- MODE AUDIT --}}
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

                .table td,
                .table th {
                    font-size: .85rem;
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
                            <i class="bi bi-clipboard-check-fill"></i> Mode Data Audit
                        </div>
                        <div class="audit-banner__desc">
                            Perubahan di halaman ini hanya berlaku pada <b>salinan data audit</b> untuk keperluan Audit QC System.
                            Data operasional utama tidak terpengaruh.
                        </div>
                    </div>
                </div>

                <div class="audit-banner__actions">
                    <span title="{{ $bulkReady ? 'Duplikasi audit massal' : 'Nonaktif: ' . $bulkWhy }}">
                        <button type="button" class="audit-pill"
                            data-bs-toggle="modal" data-bs-target="#bulkAuditModal"
                            {{ $bulkReady ? '' : 'disabled' }}>
                            <i class="bi bi-files"></i> Duplikasi Data Audit Massal
                        </button>
                    </span>

                    <form action="{{ route('toggle.mode.audit') }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Kembali ke mode Operasional?')">
                        @csrf
                        <button type="submit" class="audit-pill">
                            <i class="bi bi-clipboard-check"></i> Mode Operasional
                        </button>
                    </form>
                </div>
            </div>

            {{-- MODAL DUPLIKASI MASSAL --}}
            @if ($bulkReady)
                <div class="modal fade" id="bulkAuditModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <form method="POST" action="{{ route('chamber.auditBulk.execute') }}" class="w-100"
                            onsubmit="return confirm('Data audit akan dibuat dan nilai yang memiliki standar akan dikoreksi otomatis. Lanjutkan?')">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Duplikasi Audit Massal - Verifikasi Timer Chamber</h5>
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

                                    <div class="small">
                                        <strong>Standar yang akan diterapkan:</strong>
                                        <ul class="mb-0">
                                            @foreach ($rentangMenit as $menit)
                                                <li>
                                                    Rentang {{ $menit }} menit:
                                                    @foreach ($timerFields as $field => $label)
                                                        {{ $label }} ({{ $bulkDesc($field . '_' . $menit) }})@if (!$loop->last); @endif
                                                    @endforeach
                                                </li>
                                            @endforeach
                                        </ul>
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
                                const res = await fetch(@json(route('chamber.auditBulk.preview')), {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': @json(csrf_token()),
                                    },
                                    body: JSON.stringify({
                                        start_date: s.value,
                                        end_date: e.value
                                    }),
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
                                    'Akan diduplikasi dan dikoreksi: <b>' + d.akan_dibuat + '</b>');

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
            @endif
        @endunless

        {{-- FILTER --}}
        <form id="filterForm" method="GET" action="{{ route('chamber.audit') }}"
            class="mb-3 p-3 border rounded bg-white shadow-sm">
            <div class="row align-items-end">
                <div class="col-md-2 mb-2 mb-md-0">
                    <label for="filter_date" class="mb-1" style="font-weight: 600;">Pilih Tanggal</label>
                    <input type="date" name="date" id="filter_date" class="form-control" value="{{ request('date') }}">
                </div>

                <div class="col-md-2 mb-2 mb-md-0">
                    <label for="filter_shift" class="mb-1" style="font-weight: 600;">Pilih Shift</label>
                    <select name="shift" id="filter_shift" class="form-control">
                        <option value="">Semua Shift</option>
                        <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                        <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                    </select>
                </div>

                <div class="col-md-4 mb-2 mb-md-0">
                    <label for="search" class="mb-1" style="font-weight: 600;">Cari Data</label>
                    <input type="text" name="search" id="search" class="form-control"
                        value="{{ request('search') }}" placeholder="Cari Operator / No Chamber...">
                </div>

                <div class="col-md-2">
                    <a href="{{ route('chamber.audit') }}" class="btn btn-primary btn-block w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const search = document.getElementById('search');
                const date = document.getElementById('filter_date');
                const shift = document.getElementById('filter_shift');
                const form = document.getElementById('filterForm');

                let timer;

                function submitFilter() {
                    clearTimeout(timer);
                    timer = setTimeout(() => form.submit(), 500);
                }

                if (search) search.addEventListener('input', submitFilter);
                if (date) date.addEventListener('change', () => form.submit());
                if (shift) shift.addEventListener('change', () => form.submit());
            });
        </script>

        {{-- MAIN TABLE --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Result</th>
                                <th>QC</th>
                                <th>Operator</th>
                                <th>SPV</th>
                                @if ($showAksi)
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                            @endphp

                            @forelse ($data as $dep)
                                @php
                                    $chambers = $dep->verifikasi ?? [];
                                    if (is_string($chambers)) {
                                        $chambers = json_decode($chambers, true) ?? [];
                                    }
                                    $chambers = is_array($chambers) ? $chambers : [];
                                @endphp

                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}
                                    </td>

                                    <td class="text-center">
                                        @if (!empty($chambers))
                                            <a href="javascript:void(0);" class="fw-bold text-decoration-underline text-primary"
                                                data-bs-toggle="modal" data-bs-target="#chamberModal{{ $dep->uuid }}">
                                                Result
                                            </a>

                                            <div class="modal fade" id="chamberModal{{ $dep->uuid }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                                        <div class="modal-header bg-primary bg-gradient text-white">
                                                            <h5 class="modal-title fw-semibold">
                                                                <i class="bi bi-stopwatch me-2"></i> Detail Verifikasi Timer Chamber
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body p-4 bg-light-subtle">
                                                            <div class="table-responsive shadow-sm rounded">
                                                                <table class="table table-bordered table-sm align-middle text-center mb-0 bg-white">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th rowspan="3" class="align-middle">RENTANG UKUR</th>
                                                                            @foreach ($chambers as $index => $row)
                                                                                <th colspan="6">No. Chamber {{ $index + 1 }}</th>
                                                                            @endforeach
                                                                        </tr>
                                                                        <tr>
                                                                            @foreach ($chambers as $row)
                                                                                <th colspan="2">PLC</th>
                                                                                <th colspan="2">STOPWATCH</th>
                                                                                <th colspan="2">KOREKSI</th>
                                                                            @endforeach
                                                                        </tr>
                                                                        <tr>
                                                                            @foreach ($chambers as $row)
                                                                                <th>MNT</th>
                                                                                <th>DTK</th>
                                                                                <th>MNT</th>
                                                                                <th>DTK</th>
                                                                                <th colspan="2">Factor</th>
                                                                            @endforeach
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($rentangMenit as $rentang)
                                                                            <tr>
                                                                                <td class="fw-bold">{{ $rentang }} menit</td>
                                                                                @foreach ($chambers as $row)
                                                                                    <td>{{ $row['plc_menit_' . $rentang] ?? '-' }}</td>
                                                                                    <td>{{ $row['plc_detik_' . $rentang] ?? '-' }}</td>
                                                                                    <td>{{ $row['stopwatch_menit_' . $rentang] ?? '-' }}</td>
                                                                                    <td>{{ $row['stopwatch_detik_' . $rentang] ?? '-' }}</td>
                                                                                    <td colspan="2" class="fw-bold text-danger">
                                                                                        {{ $row['faktor_koreksi_' . $rentang] ?? '-' }}
                                                                                    </td>
                                                                                @endforeach
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="mt-3 p-3 bg-white border rounded text-start">
                                                                <strong>Catatan:</strong>
                                                                {{ $dep->catatan ?? '-' }}
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

                                    <td class="text-center">
                                        {{ \Illuminate\Support\Facades\DB::table('users')->where('username', $dep->username)->value('name') ?? $dep->username ?? '-' }}
                                    </td>

                                    <td class="text-center">{{ $dep->nama_operator ?? '-' }}</td>

                                    <td class="text-center">
                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <span class="fw-bold text-danger">Revision</span>
                                        @else
                                            <span class="text-muted">Belum Ditinjau</span>
                                        @endif
                                    </td>

                                    @if ($showAksi)
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                @if ($dep->is_audit)
                                                    <a href="{{ route('chamber.edit.form', $dep->uuid) }}"
                                                        class="btn btn-success btn-sm" title="Data audit sudah tersedia">
                                                        <i class="bi bi-pencil"></i> Edit Data Audit Manual
                                                    </a>
                                                @else
                                                    @php
                                                        if ($bulkReady) {
                                                            $msg = "Edit data audit dengan acuan Master Verifikasi Timer Chamber (sesuai plant):\n";

                                                            foreach ($rentangMenit as $menit) {
                                                                $msg .= "\n- Rentang {$menit} menit:";
                                                                foreach ($timerFields as $field => $label) {
                                                                    $key = $field . '_' . $menit;
                                                                    $msg .= "\n  {$label}: " . $descRule(data_get($bulkStd, $key), $key);
                                                                }
                                                            }

                                                            $msg .= "\n\nSalinan audit akan dibuat dan nilai yang memiliki standar diterapkan otomatis. Lanjutkan?";
                                                        } else {
                                                            $msg = "Master belum diterapkan untuk plant ini.\nAuto correct tidak dijalankan, salinan memakai data asli untuk edit manual.\nLanjutkan?";
                                                        }
                                                    @endphp

                                                    <button type="button" class="btn btn-warning btn-sm text-dark"
                                                        title="{{ $bulkReady ? 'Duplikasi & auto correct entry ini saja' : 'Nonaktif: ' . $bulkWhy }}"
                                                        data-url="{{ route('chamber.duplicateToAudit', $dep->uuid) }}"
                                                        onclick="magicDuplicate(this, {{ \Illuminate\Support\Js::from($msg) }})"
                                                        {{ $bulkReady ? '' : 'disabled' }}>
                                                        <i class="bi bi-magic"></i> Edit Data Audit Otomatis
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $showAksi ? 8 : 7 }}" class="text-center text-muted">
                                        Belum ada data audit.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($data, 'links'))
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- DUPLIKASI SINGULAR --}}
    @if ($showAksi)
        <script>
            async function magicDuplicate(btn, msg) {
                const original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                try {
                    const res = await fetch(btn.dataset.url, {
                        method: 'POST',
                        redirect: 'manual',
                        headers: {
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json',
                        },
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
        </script>
    @endif
@endsection