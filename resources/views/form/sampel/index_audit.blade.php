@extends('layouts.app')

@section('content')
    @php
        $isAuditor = auth()->user()->hasRole('auditor');
        $showAksi  = !$isAuditor && auth()->user()->can('can access update button');
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
            <h4>Data Pengambilan Sampel</h4>
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
                    <button type="button" class="audit-pill"
                        data-bs-toggle="modal" data-bs-target="#bulkAuditModal">
                        <i class="bi bi-files"></i> Duplikasi Data audit Massal
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
                    <form method="POST" action="{{ route('sampel.auditBulk.execute') }}" class="w-100"
                        onsubmit="return confirm('Data audit akan dibuat sebagai salinan dari data asli. Lanjutkan?')">
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
                                    <em>Menu ini tidak memiliki standar master. Data disalin apa adanya, tanpa koreksi otomatis.</em>
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
                            const res = await fetch(@json(route('sampel.auditBulk.preview')), {
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

        {{-- ===================== FILTER ===================== --}}
        <form id="filterForm" method="GET" action="{{ route('sampel.audit') }}"
            class="mb-3 p-3 border rounded bg-white shadow-sm">
            <div class="row align-items-end">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label for="filter_date" class="mb-1" style="font-weight: 600;">Pilih Tanggal</label>
                    <input type="date" name="date" id="filter_date" class="form-control" value="{{ request('date') }}">
                </div>

                <div class="col-md-4 mb-2 mb-md-0">
                    <label for="search" class="mb-1" style="font-weight: 600;">Cari Data</label>
                    <input type="text" name="search" id="search" class="form-control"
                        value="{{ request('search') }}" placeholder="Cari Nama Varian / Kode Batch...">
                </div>

                <div class="col-md-4">
                    <a href="{{ route('sampel.audit') }}" class="btn btn-primary btn-block w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const search = document.getElementById('search');
                const date = document.getElementById('filter_date');
                const form = document.getElementById('filterForm');

                let timer;
                function submitFilter() {
                    clearTimeout(timer);
                    timer = setTimeout(() => form.submit(), 500);
                }

                if (search) search.addEventListener('input', submitFilter);
                if (date) date.addEventListener('change', () => form.submit());
            });
        </script>

        {{-- ===================== MAIN TABLE ===================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Tanggal Pengambilan</th>
                                <th>Dibuat Oleh</th>
                                <th>Jenis Sampel</th>
                                <th>Nama Varian</th>
                                <th>Kode Batch</th>
                                <th>Keterangan</th>
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
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}</td>
                                    <td>{{ $dep->username_updated ?? $dep->username }}</td>
                                    <td>{{ $dep->jenis_sampel }}</td>
                                    <td>{{ $dep->nama_produk }}</td>
                                    <td>{{ $kodeMap[$dep->kode_produksi] ?? $dep->kode_produksi }}</td>
                                    <td>{{ $dep->keterangan }}</td>

                                    <td class="text-center align-middle">
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

                                    {{-- ===================== KOLOM AKSI ===================== --}}
                                    @if ($showAksi)
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                @if ($dep->is_audit)
                                                    <a href="{{ route('sampel.edit.form', $dep->uuid) }}"
                                                        class="btn btn-success btn-sm"
                                                        title="Data audit sudah tersedia">
                                                        <i class="bi bi-pencil"></i> Edit Data Audit Manual
                                                    </a>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-warning btn-sm text-dark"
                                                        title="Duplikasi entry ini saja"
                                                        data-url="{{ route('sampel.duplicateToAudit', $dep->uuid) }}"
                                                        onclick="magicDuplicate(this)">
                                                        <i class="bi bi-magic"></i> Duplikasi Data Audit Otomatis
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $showAksi ? 9 : 8 }}" class="text-center text-muted">Belum ada data audit.</td>
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
        .table td, .table th { font-size: 0.85rem; white-space: nowrap; }
        .text-danger { font-weight: bold; }
        .container-fluid { padding-left: 15px !important; padding-right: 15px !important; }
    </style>
@endsection