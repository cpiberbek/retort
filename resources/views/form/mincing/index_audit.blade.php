@extends('layouts.app')

@section('content')
    @php
        $isAuditor = auth()->user()->hasRole('auditor');
        $showAksi = !$isAuditor && auth()->user()->can('can access update button');
        $masters = $masters ?? \App\Models\List_form::where('laporan', 'Pemeriksaan Mincing - Emulsifying - Aging')
            ->get()
            ->keyBy('plant');

        $userPlant = auth()->user()->plant;
        $bulkMaster = \App\Models\List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Mincing - Emulsifying - Aging')
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
                $bulkWhy = 'standar_ref bertipe ' . gettype($bulkStd) . ' (bukan array, cek $casts di model List_form)';
            }
        }

        $bulkDesc = function ($rule) {
            if (is_array($rule) && isset($rule['min'], $rule['max'])) return "diacak {$rule['min']} s/d {$rule['max']} (maks 2 desimal)";
            if (is_array($rule) && isset($rule['max'])) return "diacak di bawah {$rule['max']} (strictly < {$rule['max']})";
            return $rule === 'Oke' ? 'dicentang otomatis' : 'tidak dicentang (uncheck)';
        };

        $descRule = function ($rule) {
            if (is_array($rule) && isset($rule['min'], $rule['max'])) {
                return "Di-acak antara {$rule['min']} s/d {$rule['max']} (maks 2 desimal)";
            }
            if (is_array($rule) && isset($rule['max'])) {
                return "diacak di bawah {$rule['max']} ( < {$rule['max']}, maks 2 desimal)";
            }
            return $rule === 'Oke' ? 'dicentang otomatis' : 'tidak dicentang (uncheck) otomatis';
        };

        $msgExisting = "Data audit sudah ada.\nKlik OK untuk membuka dan mengedit data audit.\n(Auto correct tidak dijalankan ulang, edit manual Anda tetap aman.)";
    @endphp

    <div class="container-fluid py-0">

        {{-- ===================== ALERTS ===================== --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ===================== HEADER ===================== --}}
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h4>Data Audit Pemeriksaan Mincing - Emulsifying - Aging</h4>
        </div>

        {{-- ===================== MODE AUDIT (disembunyikan untuk auditor) ===================== --}}
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
                    display: inline-flex; align-items: center; gap: 6px;
                    padding: 6px 14px; border-radius: 999px;
                    font-size: .8rem; font-weight: 600; line-height: 1.2; color: #fff;
                    background: rgba(255, 255, 255, .12);
                    border: 1px solid rgba(255, 255, 255, .38);
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
                    {{-- Duplikasi massal (dipindah dari index operasional) --}}
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
                        <form method="POST" action="{{ route('mincing.auditBulk.execute') }}" class="w-100"
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
                                        <strong>Standar yang akan diterapkan (semua nilai ditimpa):</strong>
                                        <ul class="mb-0">
                                            <li>Suhu Akhir Emulsi Gel: {{ $bulkDesc($bulkStd['suhu_akhir_emulsi_gel'] ?? null) }}</li>
                                            <li>Suhu Akhir Mixing: {{ $bulkDesc($bulkStd['suhu_akhir_mixing'] ?? null) }}</li>
                                            <li>Suhu Akhir Emulsi: {{ $bulkDesc($bulkStd['suhu_akhir_emulsi'] ?? null) }}</li>
                                            <li>Suhu Sebelum Grinding: {{ $bulkDesc($bulkStd['suhu_sebelum_grinding'] ?? null) }}</li>
                                            <li>Non-Premix, Suhu Bahan: {{ $bulkDesc($bulkStd['non_premix']['suhu_bahan'] ?? null) }}</li>
                                            <li>Non-Premix, pH Bahan: {{ $bulkDesc($bulkStd['non_premix']['ph_bahan'] ?? null) }}</li>
                                            <li>Non-Premix, Sensori: {{ $bulkDesc($bulkStd['non_premix']['sensori'] ?? null) }}</li>
                                            <li>Premix, Sensori: {{ $bulkDesc($bulkStd['premix']['sensori_premix'] ?? null) }}</li>
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
                                const res = await fetch(@json(route('mincing.auditBulk.preview')), {
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
        <form id="filterForm" method="GET" action="{{ route('mincing.audit') }}"
            class="mb-3 p-3 border rounded bg-white shadow-sm">
            <div class="row align-items-end">

                <div class="col-md-2 mb-2 mb-md-0">
                    <label for="filter_date" class="mb-1" style="font-weight: 600;">Pilih Tanggal</label>
                    <input type="date" name="date" id="filter_date" class="form-control"
                        value="{{ request('date') }}">
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

                <div class="col-md-2 mb-2 mb-md-0">
                    <label for="filter_kode_batch" class="mb-1" style="font-weight: 600;">Kode Batch</label>
                    <input type="text" name="kode_batch" id="filter_kode_batch" class="form-control"
                        value="{{ request('kode_batch') }}" placeholder="Kode Batch">
                </div>

                <div class="col-md-4 mb-2 mb-md-0">
                    <label for="search" class="mb-1" style="font-weight: 600;">Cari Data</label>
                    <input type="text" name="search" id="search" class="form-control"
                        value="{{ request('search') }}" placeholder="Cari Nama Produk / Kode Batch...">
                </div>

                <div class="col-md-2">
                    <a href="{{ route('mincing.audit') }}" class="btn btn-primary btn-block w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>

            </div>
        </form>

        {{-- Filter Scripts --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const search = document.getElementById('search');
                const date = document.getElementById('filter_date');
                const shift = document.getElementById('filter_shift');
                const kodeBatch = document.getElementById('filter_kode_batch');
                const form = document.getElementById('filterForm');

                let timer;
                function submitFilter() {
                    clearTimeout(timer);
                    timer = setTimeout(() => form.submit(), 500);
                }

                if (search) search.addEventListener('input', submitFilter);
                if (date) date.addEventListener('change', () => form.submit());
                if (shift) shift.addEventListener('change', () => form.submit());
                if (kodeBatch) kodeBatch.addEventListener('input', submitFilter);
            });
        </script>

        {{-- ===================== MAIN TABLE ===================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Nama Varian</th>
                                <th>Kode Batch</th>
                                <th>Hasil Pemeriksaan</th>
                                <th>QC</th>
                                <th>Produksi</th>
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
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}
                                    </td>
                                    <td class="text-center">{{ $dep->nama_produk }}</td>
                                    <td class="text-center">{{ $dep->kode_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        @if ($dep)
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#mincingModal{{ $dep->uuid }}"
                                                style="font-weight: bold; text-decoration: underline;">
                                                Result
                                            </a>

                                            @php
                                                $nonPremixItems = $dep->non_premix ?? [];
                                                $premixItems = $dep->premix ?? [];

                                                if (is_string($nonPremixItems)) $nonPremixItems = json_decode($nonPremixItems, true);
                                                if (is_string($premixItems)) $premixItems = json_decode($premixItems, true);
                                                if (!is_array($nonPremixItems)) $nonPremixItems = [];
                                                if (!is_array($premixItems)) $premixItems = [];

                                                // rowspan hanya untuk baris nama_bahan yang BERURUTAN
                                                $nonPremixItems = array_values($nonPremixItems);
                                                $spans = [];
                                                $n = count($nonPremixItems);
                                                for ($i = 0; $i < $n; $i++) {
                                                    $nama = $nonPremixItems[$i]['nama_bahan'] ?? '-';
                                                    if ($i > 0 && ($nonPremixItems[$i - 1]['nama_bahan'] ?? '-') === $nama) {
                                                        $spans[$i] = 0;
                                                        continue;
                                                    }
                                                    $c = 1;
                                                    while ($i + $c < $n && ($nonPremixItems[$i + $c]['nama_bahan'] ?? '-') === $nama) {
                                                        $c++;
                                                    }
                                                    $spans[$i] = $c;
                                                }

                                                // suhu sebelum grinding
                                                $rawSuhu = $dep->suhu_sebelum_grinding;
                                                if (is_string($rawSuhu)) $rawSuhu = json_decode($rawSuhu, true);
                                            @endphp

                                            {{-- MODAL DETAIL HASIL --}}
                                            <div class="modal fade text-start" id="mincingModal{{ $dep->uuid }}"
                                                tabindex="-1" aria-labelledby="mincingModalLabel{{ $dep->uuid }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning text-dark">
                                                            <h5 class="modal-title fw-bold" id="mincingModalLabel{{ $dep->uuid }}">
                                                                Detail Pemeriksaan Mincing
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body table-responsive">
                                                            <table class="table table-bordered table-striped table-sm text-center align-middle">
                                                                <tbody>
                                                                    {{-- KODE BATCH --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold w-25">Kode Batch</td>
                                                                        <td colspan="5" class="text-start">{{ $dep->kode_produksi ?? '-' }}</td>
                                                                    </tr>

                                                                    {{-- PREPARATION --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Preparation</td>
                                                                        <td colspan="2">{{ $dep->waktu_mulai ?? '-' }}</td>
                                                                        <td class="text-center">s/d</td>
                                                                        <td colspan="2">{{ $dep->waktu_selesai ?? '-' }}</td>
                                                                    </tr>

                                                                    {{-- NON-PREMIX --}}
                                                                    <tr class="section-header bg-light fw-bold text-center">
                                                                        <td class="text-start">Bahan Baku & Tambahan (Non-Premix)</td>
                                                                        <td>Kode</td>
                                                                        <td>(°C)</td>
                                                                        <td>*pH</td>
                                                                        <td>Kg</td>
                                                                        <td>Sens</td>
                                                                    </tr>
                                                                    @if (count($nonPremixItems) > 0)
                                                                        @foreach ($nonPremixItems as $index => $bahan)
                                                                            <tr>
                                                                                @if ($spans[$index] > 0)
                                                                                    <td class="text-start" rowspan="{{ $spans[$index] }}"
                                                                                        style="text-align: center; vertical-align: middle;">
                                                                                        {{ $bahan['nama_bahan'] ?? '-' }}
                                                                                    </td>
                                                                                @endif
                                                                                <td>
                                                                                    {{ \App\Models\InspectionProductDetail::where('uuid', $bahan['inspection_uuid'] ?? null)->value('kode_batch') ?? '-' }}
                                                                                </td>
                                                                                <td>{{ $bahan['suhu_bahan'] ?? '-' }}</td>
                                                                                <td>{{ $bahan['ph_bahan'] ?? '-' }}</td>
                                                                                <td>{{ $bahan['berat_bahan'] ?? '-' }}</td>
                                                                                <td>{{ $bahan['sensori'] ?? '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="6" class="text-center text-muted">Belum ada data Non-Premix</td>
                                                                        </tr>
                                                                    @endif

                                                                    {{-- PREMIX --}}
                                                                    <tr class="section-header bg-light fw-bold text-center">
                                                                        <td class="text-start">Premix</td>
                                                                        <td colspan="2">Kode</td>
                                                                        <td colspan="2">Kg</td>
                                                                        <td>Sens</td>
                                                                    </tr>
                                                                    @if (count($premixItems) > 0)
                                                                        @foreach ($premixItems as $p)
                                                                            <tr>
                                                                                <td class="text-start">{{ $p['nama_premix'] ?? '-' }}</td>
                                                                                <td colspan="2">{{ $p['kode_premix'] ?? '-' }}</td>
                                                                                <td colspan="2">{{ $p['berat_premix'] ?? '-' }}</td>
                                                                                <td>{{ $p['sensori_premix'] ?? '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="6" class="text-center text-muted">Belum ada data Premix</td>
                                                                        </tr>
                                                                    @endif

                                                                    {{-- SUHU SEBELUM GRINDING --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Suhu (Sebelum Grinding)</td>
                                                                        <td colspan="5" class="text-start">
                                                                            @if (!empty($rawSuhu) && is_array($rawSuhu))
                                                                                <div class="d-flex flex-wrap gap-2">
                                                                                    @foreach ($rawSuhu as $s)
                                                                                        <span class="badge bg-secondary text-white border">
                                                                                            {{ $s['daging'] ?? '?' }}: {{ $s['suhu'] ?? '-' }}°C
                                                                                        </span>
                                                                                    @endforeach
                                                                                </div>
                                                                            @elseif (!empty($dep->daging))
                                                                                {{-- Fallback Tampilan Lama --}}
                                                                                {{ $dep->daging }}: {{ $dep->suhu_sebelum_grinding }}°C
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                    </tr>

                                                                    {{-- WAKTU MIXING PREMIX --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Waktu Mixing Premix</td>
                                                                        <td colspan="5" class="text-start">
                                                                            {{ $dep->waktu_mixing_premix_start ? \Carbon\Carbon::parse($dep->waktu_mixing_premix_start)->format('H:i') : '' }}
                                                                            -
                                                                            {{ $dep->waktu_mixing_premix_end ? \Carbon\Carbon::parse($dep->waktu_mixing_premix_end)->format('H:i') : '' }}
                                                                            <span class="text-muted ms-2">({{ $dep->waktu_mixing_premix ?? 0 }} menit)</span>
                                                                        </td>
                                                                    </tr>

                                                                    {{-- WAKTU BOWL CUTTER --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Waktu Bowl Cutter</td>
                                                                        <td colspan="5" class="text-start">
                                                                            {{ $dep->waktu_bowl_cutter_start ? \Carbon\Carbon::parse($dep->waktu_bowl_cutter_start)->format('H:i') : '' }}
                                                                            -
                                                                            {{ $dep->waktu_bowl_cutter_end ? \Carbon\Carbon::parse($dep->waktu_bowl_cutter_end)->format('H:i') : '' }}
                                                                            <span class="text-muted ms-2">({{ $dep->waktu_bowl_cutter ?? 0 }} menit)</span>
                                                                        </td>
                                                                    </tr>

                                                                    {{-- WAKTU AGING EMULSI --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Waktu Aging Emulsi</td>
                                                                        <td colspan="2">{{ $dep->waktu_aging_emulsi_awal ?? '-' }}</td>
                                                                        <td class="text-center">s/d</td>
                                                                        <td colspan="2">{{ $dep->waktu_aging_emulsi_akhir ?? '-' }}</td>
                                                                    </tr>

                                                                    {{-- SUHU AKHIR EMULSI GEL --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Suhu Akhir Emulsi Gel</td>
                                                                        <td colspan="5" class="text-start">
                                                                            {{ $dep->suhu_akhir_emulsi_gel !== null ? rtrim(rtrim($dep->suhu_akhir_emulsi_gel, '0'), '.') : '-' }}
                                                                        </td>
                                                                    </tr>

                                                                    {{-- WAKTU MIXING --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Waktu Mixing</td>
                                                                        <td colspan="5" class="text-start">
                                                                            {{ $dep->waktu_mixing_start ? \Carbon\Carbon::parse($dep->waktu_mixing_start)->format('H:i') : '' }}
                                                                            -
                                                                            {{ $dep->waktu_mixing_end ? \Carbon\Carbon::parse($dep->waktu_mixing_end)->format('H:i') : '' }}
                                                                            <span class="text-muted ms-2">({{ $dep->waktu_mixing ?? 0 }} menit)</span>
                                                                        </td>
                                                                    </tr>

                                                                    {{-- SUHU AKHIR MIXING --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Suhu Akhir Mixing</td>
                                                                        <td colspan="5" class="text-start">
                                                                            {{ $dep->suhu_akhir_mixing !== null ? rtrim(rtrim($dep->suhu_akhir_mixing, '0'), '.') : '-' }}
                                                                        </td>
                                                                    </tr>

                                                                    {{-- SUHU AKHIR EMULSIFYING --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Suhu Akhir Emulsifying</td>
                                                                        <td colspan="5" class="text-start">
                                                                            {{ $dep->suhu_akhir_emulsi !== null ? rtrim(rtrim($dep->suhu_akhir_emulsi, '0'), '.') : '-' }}
                                                                        </td>
                                                                    </tr>

                                                                    {{-- CATATAN --}}
                                                                    <tr>
                                                                        <td class="text-start fw-bold">Catatan</td>
                                                                        <td colspan="5" class="text-start">{{ $dep->catatan ?? '-' }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{ \Illuminate\Support\Facades\DB::table('users')->where('username', $dep->username)->value('name') }}
                                    </td>

                                    <td class="text-center">
                                        @if ($dep->status_produksi == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_produksi == 1)
                                            <span class="fw-bold text-success">Checked</span>
                                        @elseif ($dep->status_produksi == 2)
                                            <span class="fw-bold text-danger">Recheck</span>
                                        @else
                                            <span class="text-muted">Belum Ditinjau</span>
                                        @endif
                                    </td>

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

                                    {{-- ===================== KOLOM AKSI ===================== --}}
                                    @if ($showAksi)
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">

                                                @if ($dep->is_audit)
                                                    {{-- HIJAU: baris sudah berupa salinan audit -> langsung buka edit salinan --}}
                                                    <a href="{{ route('mincing.edit.form', $dep->uuid) }}"
                                                        class="btn btn-success btn-sm"
                                                        title="Sudah ada data audit"
                                                        onclick="return confirm({{ \Illuminate\Support\Js::from($msgExisting) }})">
                                                        <i class="bi bi-files"></i> Edit Data Audit Manual
                                                    </a>
                                                @else
                                                    {{-- HITAM: data asli belum pernah diedit -> buat salinan + autocorrect master --}}
                                                    @php
                                                        $std = $bulkStd; // baris sudah difilter per plant user di controller

                                                        if (!empty($std) && is_array($std)) {
                                                            $msg = "Edit data audit dengan acuan Master (sesuai plant):\n";
                                                            $msg .= "\n- Suhu Akhir Emulsi Gel: " . $descRule($std['suhu_akhir_emulsi_gel'] ?? null);
                                                            $msg .= "\n- Suhu Akhir Mixing: " . $descRule($std['suhu_akhir_mixing'] ?? null);
                                                            $msg .= "\n- Suhu Akhir Emulsi: " . $descRule($std['suhu_akhir_emulsi'] ?? null);
                                                            $msg .= "\n- Suhu Sebelum Grinding: " . $descRule($std['suhu_sebelum_grinding'] ?? null);
                                                            $msg .= "\n- Non-Premix, Suhu Bahan: " . $descRule($std['non_premix']['suhu_bahan'] ?? null);
                                                            $msg .= "\n- Non-Premix, pH Bahan: " . $descRule($std['non_premix']['ph_bahan'] ?? null);
                                                            $msg .= "\n- Non-Premix, Sensori: " . $descRule($std['non_premix']['sensori'] ?? null);
                                                            $msg .= "\n- Premix, Sensori: " . $descRule($std['premix']['sensori_premix'] ?? null);
                                                            $msg .= "\n\nSalinan audit akan dibuat dan nilai di atas diterapkan otomatis. Lanjutkan?";
                                                        } else {
                                                            $msg = "Master belum diterapkan untuk plant ini.\nAuto correct tidak dijalankan, salinan memakai data asli untuk edit manual.\nLanjutkan?";
                                                        }
                                                    @endphp

                                                    <form action="{{ route('mincing.duplicateToAudit', $dep->uuid) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-dark btn-sm"
                                                            title="Belum ada data audit"
                                                            onclick="return confirm({{ \Illuminate\Support\Js::from($msg) }})">
                                                            <i class="bi bi-files"></i> Edit Data Audit Manual
                                                        </button>
                                                    </form>

                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        title="{{ $bulkReady ? 'Duplikasi & auto correct entry ini saja' : 'Nonaktif: ' . $bulkWhy }}"
                                                        data-url="{{ route('mincing.duplicateToAudit', $dep->uuid) }}"
                                                        onclick="magicDuplicate(this, {{ \Illuminate\Support\Js::from($msg) }})"
                                                        {{ $bulkReady ? '' : 'disabled' }}>
                                                        <i class="bi bi-magic"></i> Edit Data Audit Otomatis
                                                    </button>
                                                @endif

                                                {{-- Aksi lain menyusul di sini --}}
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

                @if (method_exists($data, 'links'))
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $data->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($showAksi)
        <script>
            async function magicDuplicate(btn, msg) {
                if (!confirm(msg)) return;

                const original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                try {
                    // redirect:'manual' -> salinan dibuat di request POST ini, form edit tidak ikut dimuat
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

@endsection