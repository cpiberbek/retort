@extends('layouts.app')

@section('content')
    @php
        $isAuditor = auth()->user()->hasRole('auditor');
        $showAksi = !$isAuditor && auth()->user()->can('can access update button');

        $laporan = 'Pemeriksaan Washing - Drying';

        $masters = $masters ?? \App\Models\List_form::where('laporan', $laporan)
            ->get()
            ->keyBy('plant');

        $userPlant = auth()->user()->plant;

        $bulkMaster = \App\Models\List_form::where('plant', $userPlant)
            ->where('laporan', $laporan)
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

        $washingRangeFields = [
            'panjang_produk' => 'Panjang Produk (cm)',
            'diameter_produk' => 'Diameter Produk (mm)',
            'konsentrasi_pckleer' => 'Konsentrasi PC Kleer (%)',
            'suhu_pckleer_1' => 'Suhu PC Kleer 1 (°C)',
            'suhu_pckleer_2' => 'Suhu PC Kleer 2 (°C)',
            'ph_pckleer' => 'pH PC Kleer',
            'konsentrasi_pottasium' => 'Konsentrasi Pottasium Sorbate (%)',
            'suhu_pottasium' => 'Suhu Pottasium Sorbate (°C)',
            'ph_pottasium' => 'pH Pottasium Sorbate',
            'suhu_heater' => 'Suhu Heater (°C)',
            'speed_1' => 'Speed Conveyor Drying 1',
            'speed_2' => 'Speed Conveyor Drying 2',
            'speed_3' => 'Speed Conveyor Drying 3',
            'speed_4' => 'Speed Conveyor Drying 4',
        ];

        $washingOptionFields = [
            'airtrap' => 'Air Trap',
            'lengket' => 'Lengket',
            'sisa_adonan' => 'Sisa Adonan',
            'kebocoran' => 'Kebocoran',
            'kekuatan_seal' => 'Kekuatan Seal',
            'print_kode' => 'Print Kode Batch',
            'kondisi_air_pckleer' => 'Kondisi Air PC Kleer',
            'kondisi_pottasium' => 'Kondisi Air Pottasium Sorbate',
        ];

        $washingTextFields = [
            'catatan' => 'Catatan',
        ];

        $bulkDesc = function ($rule) {
            if (is_array($rule) && isset($rule['min'], $rule['max'])) {
                return "diacak {$rule['min']} s/d {$rule['max']} (maks. 2 desimal)";
            }

            if (is_array($rule) && isset($rule['max'])) {
                return "diacak di bawah {$rule['max']} (maks. 2 desimal)";
            }

            if (in_array($rule, ['Ada', 'Tidak Ada', 'OK', 'Tidak OK'], true)) {
                return "diatur menjadi {$rule}";
            }

            return 'tidak diubah';
        };

        $descRule = function ($rule) {
            if (is_array($rule) && isset($rule['min'], $rule['max'])) {
                return "Diacak antara {$rule['min']} s/d {$rule['max']} (maks. 2 desimal)";
            }

            if (is_array($rule) && isset($rule['max'])) {
                return "Diacak di bawah {$rule['max']} (maks. 2 desimal)";
            }

            if (in_array($rule, ['Ada', 'Tidak Ada', 'OK', 'Tidak OK'], true)) {
                return "diatur menjadi {$rule}";
            }

            return 'tidak diubah';
        };

        $msgExisting = "Data audit sudah ada.\nKlik OK untuk membuka data audit yang sudah tersedia.\nAuto correct tidak dijalankan ulang.";
    @endphp

    <div class="container-fluid py-0">

        {{-- ALERTS --}}
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

        {{-- HEADER --}}
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h4>Data Audit Pemeriksaan Washing - Drying</h4>
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
                            Perubahan di halaman ini hanya berlaku pada <b>salinan data audit</b>.
                            Data operasional utama tidak terpengaruh.
                        </div>
                    </div>
                </div>

                <div class="audit-banner__actions">
                    <span title="{{ $bulkReady ? 'Duplikasi audit massal' : 'Nonaktif: ' . $bulkWhy }}">
                        <button type="button"
                            class="audit-pill"
                            data-bs-toggle="modal"
                            data-bs-target="#bulkAuditModal"
                            {{ $bulkReady ? '' : 'disabled' }}>
                            <i class="bi bi-files"></i> Duplikasi Data Audit Massal
                        </button>
                    </span>

                    <form action="{{ route('toggle.mode.audit') }}"
                        method="POST"
                        class="d-inline"
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
                        <form method="POST"
                            action="{{ route('washing.auditBulk.execute') }}"
                            class="w-100"
                            onsubmit="return confirm('Data audit akan dibuat dan standar master akan diterapkan. Lanjutkan?')">
                            @csrf

                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Duplikasi Audit Massal Washing - Drying</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label">Tanggal awal</label>
                                            <input type="date" name="start_date" id="bulkStart"
                                                class="form-control" required>
                                        </div>

                                        <div class="col-6">
                                            <label class="form-label">Tanggal akhir</label>
                                            <input type="date" name="end_date" id="bulkEnd"
                                                class="form-control" required>
                                        </div>
                                    </div>

                                    <div id="bulkPreview" class="alert alert-secondary py-2 small mb-3">
                                        Pilih tanggal untuk melihat ringkasan.
                                    </div>

                                    <div class="small">
                                        <strong>Standar yang akan diterapkan:</strong>

                                        <ul class="mb-0">
                                            @foreach ($washingRangeFields as $key => $label)
                                                <li>{{ $label }}: {{ $bulkDesc($bulkStd[$key] ?? null) }}</li>
                                            @endforeach

                                            @foreach ($washingOptionFields as $key => $label)
                                                <li>{{ $label }}: {{ $bulkDesc($bulkStd[$key] ?? null) }}</li>
                                            @endforeach

                                            @foreach ($washingTextFields as $key => $label)
                                                <li>{{ $label }}: {{ !empty($bulkStd[$key]) ? $bulkStd[$key] : 'tidak diubah' }}</li>
                                            @endforeach
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

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const s = document.getElementById('bulkStart');
                        const e = document.getElementById('bulkEnd');
                        const box = document.getElementById('bulkPreview');
                        const run = document.getElementById('bulkRun');
                        const modal = document.getElementById('bulkAuditModal');

                        function show(bg, color, html) {
                            box.removeAttribute('class');
                            box.style.cssText =
                                'display:block;padding:8px 12px;margin-bottom:1rem;border-radius:6px;font-size:.85rem;background:' +
                                bg + ';color:' + color + ';';
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
                                const res = await fetch(@json(route('washing.auditBulk.preview')), {
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

                            if (e.value && e.value < s.value) {
                                e.value = s.value;
                            }

                            preview();
                        });

                        e.addEventListener('change', preview);
                        modal.addEventListener('shown.bs.modal', preview);
                    });
                </script>
            @endif
        @endunless

        {{-- FILTER --}}
        <form id="filterForm"
            method="GET"
            action="{{ route('washing.audit') }}"
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
                        value="{{ request('search') }}" placeholder="Cari Nama Varian / Kode Batch...">
                </div>

                <div class="col-md-2">
                    <a href="{{ route('washing.audit') }}" class="btn btn-primary btn-block w-100">
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

        {{-- TABEL DATA --}}
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
                                @php
                                    $kodeBatch = $dep->mincing->kode_produksi ?? $dep->kode_produksi ?? '-';
                                    $qcName = \Illuminate\Support\Facades\DB::table('users')
                                        ->where('username', $dep->username)
                                        ->value('name');
                                @endphp

                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>

                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                        | Shift: {{ $dep->shift }}
                                    </td>

                                    <td class="text-center">{{ $dep->nama_produk }}</td>
                                    <td class="text-center">{{ $kodeBatch }}</td>

                                    <td class="text-center">
                                        <a href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#washingModal{{ $dep->uuid }}"
                                            style="font-weight: bold; text-decoration: underline;">
                                            Result
                                        </a>

                                        {{-- MODAL DETAIL WASHING --}}
                                        <div class="modal fade text-start"
                                            id="washingModal{{ $dep->uuid }}"
                                            tabindex="-1"
                                            aria-labelledby="washingModalLabel{{ $dep->uuid }}"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title fw-bold"
                                                            id="washingModalLabel{{ $dep->uuid }}">
                                                            Detail Pemeriksaan Washing - Drying
                                                        </h5>
                                                        <button type="button"
                                                            class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <h6 class="text-secondary fw-bold mt-2">Identifikasi</h6>

                                                        <table class="table table-bordered table-sm mb-3">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 50%;">Nama Varian</th>
                                                                    <td>{{ $dep->nama_produk ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Kode Batch</th>
                                                                    <td>{{ $kodeBatch }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Tanggal</th>
                                                                    <td>{{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Shift</th>
                                                                    <td>{{ $dep->shift ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Waktu</th>
                                                                    <td>{{ $dep->pukul ? \Carbon\Carbon::parse($dep->pukul)->format('H:i') : '-' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <h6 class="text-primary fw-bold mt-2">
                                                            <i class="bi bi-check2-square me-1"></i> Pengecekan Produk
                                                        </h6>

                                                        <table class="table table-bordered table-sm mb-3">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 50%;">Panjang Produk (cm)</th>
                                                                    <td>{{ $dep->panjang_produk ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Diameter Produk (mm)</th>
                                                                    <td>{{ $dep->diameter_produk ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Air Trap</th>
                                                                    <td>{{ $dep->airtrap ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Lengket</th>
                                                                    <td>{{ $dep->lengket ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Sisa Adonan</th>
                                                                    <td>{{ $dep->sisa_adonan ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Kebocoran</th>
                                                                    <td>{{ $dep->kebocoran ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Kekuatan Seal</th>
                                                                    <td>{{ $dep->kekuatan_seal ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Print Kode Batch</th>
                                                                    <td>{{ $dep->print_kode ?? '-' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <h6 class="text-primary fw-bold mt-2">
                                                            <i class="bi bi-droplet-half me-1"></i> PC Kleer
                                                        </h6>

                                                        <table class="table table-bordered table-sm mb-3">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 50%;">Konsentrasi PC Kleer (%)</th>
                                                                    <td>{{ $dep->konsentrasi_pckleer ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Suhu PC Kleer 1 (°C)</th>
                                                                    <td>{{ $dep->suhu_pckleer_1 ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Suhu PC Kleer 2 (°C)</th>
                                                                    <td>{{ $dep->suhu_pckleer_2 ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>pH PC Kleer</th>
                                                                    <td>{{ $dep->ph_pckleer ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Kondisi Air PC Kleer</th>
                                                                    <td>{{ $dep->kondisi_air_pckleer ?? '-' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <h6 class="text-primary fw-bold mt-2">
                                                            <i class="bi bi-flask me-1"></i> Pottasium Sorbate
                                                        </h6>

                                                        <table class="table table-bordered table-sm mb-3">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 50%;">Konsentrasi Pottasium Sorbate (%)</th>
                                                                    <td>{{ $dep->konsentrasi_pottasium ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Suhu Pottasium Sorbate (°C)</th>
                                                                    <td>{{ $dep->suhu_pottasium ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>pH Pottasium Sorbate</th>
                                                                    <td>{{ $dep->ph_pottasium ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Kondisi Air Pottasium Sorbate</th>
                                                                    <td>{{ $dep->kondisi_pottasium ?? '-' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <h6 class="text-primary fw-bold mt-2">
                                                            <i class="bi bi-speedometer2 me-1"></i> Suhu & Speed Conveyor
                                                        </h6>

                                                        <table class="table table-bordered table-sm mb-3">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 50%;">Suhu Heater (°C)</th>
                                                                    <td>{{ $dep->suhu_heater ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Speed Conveyor Drying 1</th>
                                                                    <td>{{ $dep->speed_1 ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Speed Conveyor Drying 2</th>
                                                                    <td>{{ $dep->speed_2 ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Speed Conveyor Drying 3</th>
                                                                    <td>{{ $dep->speed_3 ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Speed Conveyor Drying 4</th>
                                                                    <td>{{ $dep->speed_4 ?? '-' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <h6 class="text-primary fw-bold mt-2">
                                                            <i class="bi bi-journal-text me-1"></i> Catatan
                                                        </h6>
                                                        <p class="mb-0">{{ $dep->catatan ?? '-' }}</p>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button"
                                                            class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">
                                                            Tutup
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">{{ $qcName ?? '-' }}</td>

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
                                            <a href="javascript:void(0);"
                                                data-bs-toggle="modal"
                                                data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                class="text-danger fw-bold text-decoration-none">
                                                Revision
                                            </a>

                                            <div class="modal fade"
                                                id="revisionModal{{ $dep->uuid }}"
                                                tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">Detail Revisi</h5>
                                                            <button type="button"
                                                                class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body text-start">
                                                            <ul class="list-unstyled mb-0">
                                                                <li><strong>Status:</strong> Revision</li>
                                                                <li><strong>Catatan:</strong> {{ $dep->catatan_spv ?? '-' }}</li>
                                                            </ul>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                class="btn btn-secondary btn-sm"
                                                                data-bs-dismiss="modal">
                                                                Tutup
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Belum Ditinjau</span>
                                        @endif
                                    </td>

                                    @if ($showAksi)
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                @if ($dep->is_audit)
                                                    <a href="{{ route('washing.edit.form', $dep->uuid) }}"
                                                        class="btn btn-success btn-sm"
                                                        title="Data audit sudah tersedia">
                                                        <i class="bi bi-pencil"></i> Edit Data Audit Manual
                                                    </a>
                                                @else
                                                    @php
                                                        $std = $bulkStd;

                                                        if (!empty($std) && is_array($std)) {
                                                            $msg = "Edit data audit dengan acuan Master (sesuai plant):\n";

                                                            foreach ($washingRangeFields as $key => $label) {
                                                                $msg .= "\n- {$label}: " . $descRule($std[$key] ?? null);
                                                            }

                                                            foreach ($washingOptionFields as $key => $label) {
                                                                $msg .= "\n- {$label}: " . $descRule($std[$key] ?? null);
                                                            }

                                                            foreach ($washingTextFields as $key => $label) {
                                                                $msg .= "\n- {$label}: " . (!empty($std[$key]) ? $std[$key] : 'tidak diubah');
                                                            }

                                                            $msg .= "\n\nSalinan audit akan dibuat dan standar di atas diterapkan otomatis. Lanjutkan?";
                                                        } else {
                                                            $msg = "Master belum diterapkan untuk plant ini.\nAuto correct tidak dijalankan, salinan memakai data asli untuk edit manual.\nLanjutkan?";
                                                        }
                                                    @endphp

                                                    <button type="button"
                                                        class="btn btn-warning btn-sm text-dark"
                                                        title="{{ $bulkReady ? 'Duplikasi dan auto correct entry ini' : 'Nonaktif: ' . $bulkWhy }}"
                                                        data-url="{{ route('washing.duplicateToAudit', $dep->uuid) }}"
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
                                    <td colspan="{{ $showAksi ? 9 : 8 }}" class="text-center text-muted">
                                        Belum ada data audit.
                                    </td>
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
                const original = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                try {
                    const res = await fetch(btn.dataset.url, {
                        method: 'POST',
                        redirect: 'manual',
                        headers: {
                            'X-CSRF-TOKEN': @json(csrf_token()),
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

    <style>
        .table td,
        .table th {
            font-size: .85rem;
            white-space: nowrap;
        }
    </style>
@endsection
