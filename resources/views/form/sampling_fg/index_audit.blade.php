@extends('layouts.app')

@section('content')
    @php
        $isAuditor = auth()->user()->hasRole('auditor');
        $showAksi  = !$isAuditor && auth()->user()->can('can access update button');

        $userPlant  = auth()->user()->plant;
        $bulkMaster = \App\Models\List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Proses Sampling Finish Good')
            ->first();
        $bulkStd   = $bulkMaster?->standar_ref;
        if (is_string($bulkStd)) $bulkStd = json_decode($bulkStd, true);
        $bulkReady = !empty($bulkStd) && is_array($bulkStd);
        $bulkWhy   = null;
        if (!$bulkReady) {
            $bulkWhy = !$bulkMaster
                ? "tidak ada baris List_form untuk plant '{$userPlant}'"
                : 'Master Audit belum diterapkan untuk plant ini.';
        }

        $bulkDesc = function ($rule) {
            if (is_array($rule)) {
                $min = $rule['min'] ?? null;
                $max = $rule['max'] ?? null;
                if ($min === null || $min === '' || $max === null || $max === '') return 'tidak diubah';
                return $min == $max ? "diisi tepat {$min}" : "diacak {$min} s/d {$max} (bilangan bulat)";
            }
            if (is_string($rule) && trim($rule) !== '') return "diisi \"{$rule}\"";
            return 'tidak diubah';
        };

        $bulkFields = [
            'kalibrasi'    => 'Kalibrasi',
            'berat_produk' => 'Berat Produk',
            'release'      => 'Release',
            'reject'       => 'Reject',
            'hold'         => 'Hold',
            'item_mutu'    => 'Item Mutu',
            'keterangan'   => 'Keterangan',
            'catatan'      => 'Catatan',
        ];
    @endphp

    <div class="container-fluid py-0">

        {{-- ===================== ALERTS ===================== --}}
        @foreach (['success' => ['success', 'bi-check-circle'], 'warning' => ['warning', null], 'info' => ['info', null], 'error' => ['danger', null]] as $key => [$cls, $icon])
            @if (session($key))
                <div class="alert alert-{{ $cls }} alert-dismissible fade show" role="alert">
                    @if ($icon)<i class="bi {{ $icon }} me-2"></i>@endif {{ trim(session($key)) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endforeach

        {{-- ===================== HEADER ===================== --}}
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h4>Data Pemeriksaan Proses Sampling Finish Good</h4>
        </div>

        {{-- ===================== MODE AUDIT (disembunyikan untuk auditor) ===================== --}}
        @unless($isAuditor)
            <style>
                .audit-banner {
                    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
                    gap: 12px 20px; padding: 14px 20px; border-radius: 14px; color: #fff;
                    background: linear-gradient(90deg, #6f3fbd 0%, #5d37a4 55%, #512f8d 100%);
                    box-shadow: 0 6px 18px rgba(81, 47, 141, .25);
                }
                .audit-banner__left { display: flex; align-items: center; gap: 14px; min-width: 0; flex: 1 1 420px; }
                .audit-banner__icon {
                    position: relative; flex: 0 0 44px; width: 44px; height: 44px;
                    border-radius: 50%; background: #fff; color: #6f3fbd;
                    display: flex; align-items: center; justify-content: center; font-size: 1.35rem;
                }
                .audit-banner__icon-badge { position: absolute; right: 7px; bottom: 7px; font-size: .7rem; color: #512f8d; }
                .audit-banner__title { font-weight: 800; font-size: 1.05rem; letter-spacing: .02em; text-transform: uppercase; line-height: 1.2; }
                .audit-banner__desc { font-size: .85rem; opacity: .92; margin-top: 2px; }
                .audit-banner__actions { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
                .audit-pill {
                    display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 999px;
                    font-size: .8rem; font-weight: 600; line-height: 1.2; color: #fff;
                    background: rgba(255, 255, 255, .12); border: 1px solid rgba(255, 255, 255, .38);
                    transition: background .15s ease;
                }
                .audit-pill:hover:not(:disabled) { background: rgba(255, 255, 255, .24); }
                .audit-pill:disabled { opacity: .55; cursor: not-allowed; }
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
                            <i class="bi bi-files"></i> Duplikasi Data audit Massal
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

            {{-- MODAL DUPLIKASI AUDIT MASSAL --}}
            @if ($bulkReady)
                <div class="modal fade" id="bulkAuditModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" action="{{ route('sampling_fg.auditBulk.execute') }}" class="w-100"
                            onsubmit="return confirm('Data audit akan dibuat dan SEMUA nilai sesuai master akan ditimpa. Lanjutkan?')">
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

                                    <div class="small">
                                        <strong>Standar yang akan diterapkan:</strong>
                                        <ul class="mb-0">
                                            @foreach ($bulkFields as $key => $label)
                                                <li>{{ $label }}: {{ $bulkDesc($bulkStd[$key] ?? null) }}</li>
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
                        function showError(html) { show('#f8d7da', '#842029', html); }

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
                                const res = await fetch(@json(route('sampling_fg.auditBulk.preview')), {
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
                                        if (err.errors) msg = Object.values(err.errors).flat().join('<br>');
                                        else if (err.message) msg = err.message;
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

        {{-- ===================== FILTER ===================== --}}
        <form id="filterForm" method="GET" action="{{ route('sampling_fg.audit') }}"
            class="mb-3 p-3 border rounded bg-white shadow-sm">
            <div class="row align-items-end">
                <div class="col-md-3 mb-2 mb-md-0">
                    <label for="filter_date" class="mb-1" style="font-weight: 600;">Pilih Tanggal</label>
                    <input type="date" name="date" id="filter_date" class="form-control" value="{{ request('date') }}">
                </div>

                <div class="col-md-3 mb-2 mb-md-0">
                    <label for="search" class="mb-1" style="font-weight: 600;">Cari Kode Batch</label>
                    <input type="text" name="search" id="search" class="form-control"
                        value="{{ request('search') }}" placeholder="Cari Kode Batch...">
                </div>

                <div class="col-md-3 mb-2 mb-md-0">
                    <label for="filter_shift" class="mb-1" style="font-weight: 600;">Pilih Shift</label>
                    <select name="shift" id="filter_shift" class="form-control">
                        <option value="">Semua Shift</option>
                        <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                        <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('sampling_fg.audit') }}" class="btn btn-primary btn-block w-100">
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

        {{-- ===================== MAIN TABLE ===================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th rowspan="2" style="width: 3%;">NO.</th>
                                <th rowspan="2" style="width: 8%;">Tanggal | Shift</th>
                                <th rowspan="2" style="width: 4%;">Palet</th>
                                <th rowspan="2" style="width: 12%;">Nama Varian</th>
                                <th rowspan="2" style="width: 6%;">Kode Batch</th>
                                <th rowspan="2" style="width: 6%;">Exp. Date</th>
                                <th colspan="4">Pemeriksaan Proses Cartoning</th>
                                <th rowspan="2" style="width: 5%;">Isi<br>/Box</th>
                                <th rowspan="2" style="width: 4%;">Jml<br>Box</th>
                                <th colspan="3">Status Varian</th>
                                <th rowspan="2" style="width: 5%;">Item<br>Mutu</th>
                                <th rowspan="2" style="width: 8%;">Catatan</th>
                                <th rowspan="2" style="width: 4%;">QC</th>
                                <th rowspan="2" style="width: 4%;">Koord</th>
                                <th rowspan="2" style="width: 4%;">SPV</th>
                                @if ($showAksi)
                                    <th rowspan="2" style="width: 5%;">Aksi</th>
                                @endif
                            </tr>
                            <tr>
                                <th style="width: 4%;">Jam</th>
                                <th style="width: 4%;">Kalib</th>
                                <th style="width: 4%;">Berat</th>
                                <th style="width: 5%;">Ket</th>
                                <th style="width: 3%;">Rls</th>
                                <th style="width: 3%;">Rjc</th>
                                <th style="width: 3%;">Hld</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                            @endphp
                            @forelse ($data as $dep)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | {{ $dep->shift }}</td>
                                    <td class="text-center">{{ $dep->palet }}</td>
                                    <td class="text-center">{{ $dep->nama_produk }}</td>
                                    <td class="text-center">
                                        {{ Str::isUuid($dep->kode_produksi) ? \App\Models\Mincing::where('uuid', $dep->kode_produksi)->value('kode_produksi') ?? $dep->kode_produksi : $dep->kode_produksi }}
                                    </td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($dep->exp_date)->format('d-m-Y') }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}</td>
                                    <td class="text-center">
                                        @if ($dep->kalibrasi == 'Sesuai')
                                            <span class="text-success fw-bold">✔</span>
                                        @else
                                            <span class="text-danger fw-bold">✘</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $dep->berat_produk }}</td>
                                    <td class="text-center small">{{ $dep->keterangan }}</td>
                                    <td class="text-center">{{ $dep->isi_per_box }}</td>
                                    <td class="text-center">{{ $dep->jumlah_box }}</td>
                                    <td class="text-center">{{ $dep->release }}</td>
                                    <td class="text-center">{{ $dep->reject }}</td>
                                    <td class="text-center">{{ $dep->hold }}</td>
                                    <td class="text-center small">{{ $dep->item_mutu }}</td>
                                    <td class="text-start small">{{ $dep->catatan }}</td>
                                    <td class="text-center">
                                        {{ \Illuminate\Support\Facades\DB::table('users')->where('username', $dep->username)->value('name') }}
                                    </td>
                                    <td class="text-center">{{ $dep->nama_koordinator }}</td>

                                    <td class="text-center align-middle">
                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <span class="fw-bold text-danger">Revisi</span>
                                        @else
                                            <span class="text-muted">Belum Ditinjau</span>
                                        @endif
                                    </td>

                                    {{-- ===================== KOLOM AKSI ===================== --}}
                                    @if ($showAksi)
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                @if ($dep->is_audit)
                                                    <a href="{{ route('sampling_fg.edit.form', $dep->uuid) }}"
                                                        class="btn btn-success btn-sm"
                                                        title="Data audit sudah tersedia">
                                                        <i class="bi bi-pencil"></i> Edit Data Audit Manual
                                                    </a>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-warning btn-sm text-dark"
                                                        title="{{ $bulkReady ? 'Duplikasi & auto correct entry ini saja' : 'Nonaktif: ' . $bulkWhy }}"
                                                        data-url="{{ route('sampling_fg.duplicateToAudit', $dep->uuid) }}"
                                                        onclick="magicDuplicate(this)"
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
                                    <td colspan="{{ $showAksi ? 21 : 20 }}" class="text-center py-3">Belum ada data audit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    @if ($showAksi)
        <script>
            async function magicDuplicate(btn) {
                const original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                try {
                    const res = await fetch(btn.dataset.url, {
                        method: 'POST',
                        redirect: 'manual',
                        headers: { 'X-CSRF-TOKEN': @json(csrf_token()) },
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

    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.classList.remove('show');
        }, 3000);
    </script>

    <style>
        .table th { font-size: 0.8rem; white-space: nowrap; padding: 8px 4px; }
        .table td { font-size: 0.8rem; padding: 6px 4px; white-space: nowrap; }
        .table td.text-start { text-align: left !important; }
        .table td.small { font-size: 0.75rem; white-space: normal; max-width: 150px; }
    </style>
@endsection