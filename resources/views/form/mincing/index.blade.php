@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">

        {{-- ===================== ALERTS ===================== --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ===================== HEADER & ACTION BUTTONS ===================== --}}
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Pemeriksaan Mincing - Emulsifying - Aging</h2>
            <div class="btn-group" role="group">
                @can('can access add button')
                    <a href="{{ route('mincing.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </a>
                @endcan

                @can('can access export')
                    <button type="button" class="btn btn-danger" id="exportPdfBtn">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-success" id="exportExcelBtn">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button>
                @endcan

                @can('can access recycle')
                    <a href="{{ route('mincing.recyclebin') }}" class="btn btn-secondary">
                        <i class="bi bi-trash"></i> Recycle Bin
                    </a>
                @endcan
            </div>
        </div>

        <div class="card shadow-sm mb-3 w-100">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">

                <div class="d-flex align-items-center">
                    <h6 class="mb-0">
                        <span class="text-primary" style="font-weight: 600;">
                            <i class="bi bi-clipboard-check" style="font-size: 1.75rem; margin-right: 0.75rem;"></i>
                            Mode Operasional
                        </span>
                    </h6>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('toggle.mode.audit') }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Masuk ke Mode Audit?')">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-shield-check"></i> Mode Audit
                        </button>
                    </form>

                    @unless(auth()->user()->hasRole('auditor'))
                        @php
                            $bulkStd = $masters[auth()->user()->plant]->standar_ref ?? null;
                            $bulkReady = !empty($bulkStd) && is_array($bulkStd);

                            $bulkDesc = function ($rule) {
                                if (is_array($rule) && isset($rule['min'], $rule['max'])) return "diacak {$rule['min']} s/d {$rule['max']} (maks 2 desimal)";
                                if (is_array($rule) && isset($rule['max'])) return "diacak di bawah {$rule['max']} (strictly < {$rule['max']})";
                                return $rule === 'Oke' ? 'dicentang otomatis' : 'tidak dicentang (uncheck)';
                            };
                        @endphp

                        <button type="button" class="btn btn-dark btn-sm"
                            data-bs-toggle="modal" data-bs-target="#bulkAuditModal"
                            {{ $bulkReady ? '' : 'disabled' }}
                            title="{{ $bulkReady ? 'Duplikasi audit massal' : 'Duplikasi audit massal (Terapkan dahulu di Master List Form)' }}">
                            <i class="bi bi-files"></i> Duplikasi Data audit Massal
                        </button>
                    @endunless
                </div>
            </div>

            @unless(auth()->user()->hasRole('auditor'))
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
        </div>

        {{-- ===================== FILTER & LIVE SEARCH ===================== --}}
        <form id="filterForm" method="GET" action="{{ route('mincing.index') }}"
            class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
            <div class="row w-100">
                <div class="col-md-2">
                    <div class="mb-1 fw-semibold">Pilih Tanggal</div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-calendar-date text-muted"></i>
                            </span>
                        </div>
                        <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                            value="{{ request('date') }}" placeholder="Tanggal Produksi">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="mb-1 fw-semibold">Pilih Shift</div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-hourglass-split text-muted"></i>
                            </span>
                        </div>
                        <select name="shift" id="filter_shift" class="form-select border-start-0 form-control">
                            <option value="">Semua Shift</option>
                            <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                            <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                            <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="mb-1 fw-semibold">Kode Batch</div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-upc-scan text-muted"></i>
                            </span>
                        </div>
                        <input type="text" name="kode_batch" id="filter_kode_batch" class="form-control border-start-0"
                            value="{{ request('kode_batch') }}" placeholder="Kode Batch">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="mb-1 fw-semibold">Cari Data</div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" name="search" id="search" class="form-control border-start-0"
                            value="{{ request('search') }}" placeholder="Cari Nama Produk / Kode Batch...">
                    </div>
                </div>

                <div class="col-md-2 align-self-end">
                    <a href="{{ route('mincing.index') }}" class="btn btn-primary mb-2 w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="modal fade" id="warningModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">(!) Filter Belum Lengkap Untuk Export Data</h5>
                    </div>
                    <div class="modal-body">
                        Silakan pilih <b>Tanggal</b>, <b>Shift</b> & <b>Kode Batch</b> yang spesifik di bagian filter
                        terlebih dahulu sebelum melakukan export.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Scripts --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const search = document.getElementById('search');
                const date = document.getElementById('filter_date');
                const shift = document.getElementById('filter_shift');
                const form = document.getElementById('filterForm');
                const exportPdfBtn = document.getElementById('exportPdfBtn');
                const exportExcelBtn = document.getElementById('exportExcelBtn');
                const kodeBatch = document.getElementById('filter_kode_batch');

                let timer;

                function submitFilter() {
                    clearTimeout(timer);
                    timer = setTimeout(() => form.submit(), 500);
                }

                if (search) {
                    search.addEventListener('input', submitFilter);
                }

                if (date) {
                    date.addEventListener('change', () => form.submit());
                }

                if (shift) {
                    shift.addEventListener('change', () => form.submit());
                }

                if (kodeBatch) {
                    kodeBatch.addEventListener('input', submitFilter);
                }


                function validateExport() {
                    const selectedDate = date.value.trim();
                    const selectedShift = shift.value.trim();
                    const selectedBatch = document.getElementById('filter_kode_batch').value.trim();

                    if (selectedDate === '' || selectedShift === '' || selectedBatch === '') {
                        const modal = new bootstrap.Modal(document.getElementById('warningModal'));
                        modal.show();
                        return false;
                    }

                    return true;
                }

                function handleExport(route, target = '_blank') {
                    if (!validateExport()) return;

                    const formData = new FormData(form);
                    const exportUrl = route + '?' + new URLSearchParams(formData).toString();

                    window.open(exportUrl, target);
                }

                if (exportPdfBtn) {
                    exportPdfBtn.addEventListener('click', () => {
                        handleExport("{{ route('mincing.exportPdf') }}", '_blank');
                    });
                }

                if (exportExcelBtn) {
                    exportExcelBtn.addEventListener('click', () => {
                        handleExport("{{ route('mincing.exportExcel') }}", '_self');
                    });
                }
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
                                @can('can access update button')
                                <th>Aksi</th>
                                @endcan
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
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift:
                                        {{ $dep->shift }}
                                    </td>
                                    <td class="text-center">{{ $dep->nama_produk }}</td>
                                    <td class="text-center">{{ $dep->kode_produksi ?? '-' }}</td>

                                    {{-- Kolom Hasil Pemeriksaan (Modal Detail) --}}
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

                                    {{-- Kolom Status Produksi --}}
                                    <td class="text-center">
                                        @if ($dep->status_produksi == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_produksi == 1)
                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-bs-target="#checkedModal{{ $dep->uuid }}"
                                                class="fw-bold text-success text-decoration-none"
                                                style="cursor: pointer;">Checked</a>

                                            {{-- MODAL CHECKED --}}
                                            <div class="modal fade text-start" id="checkedModal{{ $dep->uuid }}"
                                                tabindex="-1" aria-labelledby="checkedModalLabel{{ $dep->uuid }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title"
                                                                id="checkedModalLabel{{ $dep->uuid }}">Detail Checked
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <ul class="list-unstyled mb-0">
                                                                <li><strong>Status:</strong> Checked</li>
                                                                <li><strong>Nama Produksi:</strong>
                                                                    {{ $dep->nama_produksi ?? '-' }}</li>
                                                            </ul>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($dep->status_produksi == 2)
                                            <span class="fw-bold text-danger">Recheck</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Status SPV --}}
                                    <td class="text-center">
                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                class="text-danger fw-bold text-decoration-none"
                                                style="cursor: pointer;">Revision</a>

                                            {{-- MODAL REVISION --}}
                                            <div class="modal fade text-start" id="revisionModal{{ $dep->uuid }}"
                                                tabindex="-1" aria-labelledby="revisionModalLabel{{ $dep->uuid }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title"
                                                                id="revisionModalLabel{{ $dep->uuid }}">Detail Revisi
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <ul class="list-unstyled mb-0">
                                                                <li><strong>Status:</strong> Revision</li>
                                                                <li><strong>Catatan:</strong>
                                                                    {{ $dep->catatan_spv ?? '-' }}</li>
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

                                    {{-- Kolom Aksi / Verifikasi --}}
                                    @can('can access update button')
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">

                                            {{-- EDIT DATA AUDIT --}}
                                            @unless(auth()->user()->hasRole('auditor'))
                                                @php
                                                    $std = $masters[$dep->plant]->standar_ref ?? null;

                                                    $descRule = function ($rule) {
                                                        if (is_array($rule) && isset($rule['min'], $rule['max'])) {
                                                            return "Di-acak antara {$rule['min']} s/d {$rule['max']} (maks 2 desimal)";
                                                        }
                                                        if (is_array($rule) && isset($rule['max'])) {
                                                            return "diacak di bawah {$rule['max']} ( < {$rule['max']}, maks 2 desimal)";
                                                        }
                                                        return $rule === 'Oke' ? 'dicentang otomatis' : 'tidak dicentang (uncheck) otomatis';
                                                    };

                                                    if ($dep->auditVersion) {
                                                        $msg = "Data audit sudah ada.\nKlik OK untuk membuka dan mengedit data audit.\n(Auto correct tidak dijalankan ulang, edit manual Anda tetap aman.)";
                                                    } elseif (!empty($std) && is_array($std)) {
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
                                                    <button type="submit"
                                                        class="btn {{ $dep->auditVersion ? 'btn-success' : 'btn-dark' }} btn-sm"
                                                        title="{{ $dep->auditVersion ? 'Sudah ada data audit' : 'Belum ada data audit' }}"
                                                        onclick="return confirm({{ \Illuminate\Support\Js::from($msg) }})">
                                                        <i class="bi bi-files"></i> Edit Data Audit
                                                    </button>
                                                </form>
                                            @endunless

                                            @can('can access verification button')
                                                <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm"
                                                    data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                                                    <i class="bi bi-shield-check me-1"></i> Verifikasi
                                                </button>
                                            @endcan

                                            @can('can access edit button')
                                                <a href="{{ route('mincing.edit.form', $dep->uuid) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil-square"></i> Edit Data
                                                </a>
                                            @endcan

                                            @can('can access update button')
                                                <a href="{{ route('mincing.update.form', $dep->uuid) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="bi bi-pencil"></i> Update
                                                </a>
                                            @endcan

                                            @can('can access delete button')
                                                <form action="{{ route('mincing.destroy', $dep->uuid) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>

                                        {{-- MODAL VERIFIKASI SPV --}}
                                        @can('can access verification button')
                                            <div class="modal fade text-start" id="verifyModal{{ $dep->uuid }}"
                                                tabindex="-1" aria-labelledby="verifyModalLabel{{ $dep->uuid }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-md">
                                                    <form action="{{ route('mincing.verification.update', $dep->uuid) }}"
                                                        method="POST" class="w-100">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="page"
                                                            value="{{ request('page', 1) }}">
                                                        <input type="hidden" name="search"
                                                            value="{{ request('search') }}">
                                                        <input type="hidden" name="date" value="{{ request('date') }}">
                                                        <input type="hidden" name="shift" value="{{ request('shift') }}">
                                                        <input type="hidden" name="kode_batch"
                                                            value="{{ request('kode_batch') }}">
                                                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white"
                                                            style="background: linear-gradient(145deg, #7a1f12, #9E3419); box-shadow: 0 15px 40px rgba(0,0,0,0.5);">

                                                            <div class="modal-header border-bottom border-light-subtle p-4"
                                                                style="border-bottom-width: 3px !important;">
                                                                <h5 class="modal-title fw-bolder fs-3 text-uppercase"
                                                                    id="verifyModalLabel{{ $dep->uuid }}"
                                                                    style="color: #00ffc4;">
                                                                    <i class="bi bi-gear-fill me-2"></i> VERIFICATION
                                                                </h5>
                                                                <button type="button"
                                                                    class="btn-close btn-close-white shadow-none"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>

                                                            <div class="modal-body p-5">
                                                                <p class="text-light mb-4 fs-6 text-center">
                                                                    Pastikan data yang akan diverifikasi di-check dengan teliti
                                                                    terlebih dahulu.
                                                                </p>
                                                                <div class="row g-4">
                                                                    <div class="col-md-12">
                                                                        <label for="status_spv_{{ $dep->uuid }}"
                                                                            class="form-label fw-bold mb-2 text-center d-block"
                                                                            style="color: #FFE5DE; font-size: 0.95rem;">
                                                                            Pilih Status Verifikasi
                                                                        </label>
                                                                        <select name="status_spv"
                                                                            id="status_spv_{{ $dep->uuid }}"
                                                                            class="form-select form-select-lg fw-bold text-center mx-auto"
                                                                            style="background: linear-gradient(135deg, #fff1f0, #ffe5de); border: 2px solid #dc3545; border-radius: 12px; color: #dc3545; height: 55px; font-size: 1.1rem; box-shadow: 0 6px 12px rgba(0,0,0,0.1); width: 85%; transition: all 0.3s ease;"
                                                                            required>
                                                                            <option value="1"
                                                                                {{ $dep->status_spv == 1 ? 'selected' : '' }}
                                                                                style="color: #198754; font-weight: 600;">✅
                                                                                Verified (Disetujui)</option>
                                                                            <option value="2"
                                                                                {{ $dep->status_spv == 2 ? 'selected' : '' }}
                                                                                style="color: #dc3545; font-weight: 600;">❌
                                                                                Revision (Perlu Perbaikan)</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-12 mt-3">
                                                                        <label for="catatan_spv_{{ $dep->uuid }}"
                                                                            class="form-label fw-bold text-light mb-2">
                                                                            Catatan Tambahan (Opsional)
                                                                        </label>
                                                                        <textarea name="catatan_spv" id="catatan_spv_{{ $dep->uuid }}" rows="4"
                                                                            class="form-control text-dark border-0 shadow-none" placeholder="Masukkan catatan, misalnya alasan revisi..."
                                                                            style="background-color: #FFE5DE; height: 120px;">{{ $dep->catatan_spv }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer justify-content-end p-4 border-top"
                                                                style="background-color: #9E3419; border-color: #00ffc4 !important;">
                                                                <button type="button"
                                                                    class="btn btn-outline-light fw-bold rounded-pill px-4 me-2"
                                                                    data-bs-dismiss="modal">
                                                                    Batal
                                                                </button>
                                                                <button type="submit" class="btn fw-bolder rounded-pill px-5"
                                                                    style="background-color: #E39581; color: #2c3e50;">
                                                                    <i class="bi bi-save-fill me-1"></i> SUBMIT
                                                                </button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endcan
                                    </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Belum ada data mincing.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Component (Opsional: Jika ada fitur paginasi di controller) --}}
                @if (method_exists($data, 'links'))
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $data->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ===================== ADDITIONAL SCRIPTS & STYLES ===================== --}}
    <script>
        // Auto-hide alert setelah 3 detik
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150);
            }
        }, 3000);
    </script>

    <style>
        .table td,
        .table th {
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .text-danger {
            font-weight: bold;
            color: red !important;
        }

        .text-success {
            font-weight: bold;
            color: green !important;
        }

        .text-muted.fst-italic {
            color: #6c757d !important;
            font-style: italic !important;
        }

        .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
    </style>
@endsection
