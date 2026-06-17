@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">
        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Pemeriksaan Suhu dan RH</h2>
            <div class="btn-group" role="group">
                @can('can access add button')
                    <a href="{{ route('suhu.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </a>
                @endcan
                @can('can access export')
                    <a href="{{ route('suhu.exportPdf', ['date' => request('date'), 'shift' => request('shift')]) }}"
                        target="_blank" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                @endcan
                @can('can access recycle')
                    <a href="{{ route('suhu.recyclebin') }}" class="btn btn-secondary">
                        <i class="bi bi-trash"></i> Recycle Bin
                    </a>
                @endcan
            </div>
        </div>

        {{-- Filter dan Live Search --}}
        <form id="filterForm" method="GET" action="{{ route('suhu.index') }}"
            class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
            <div class="row w-100">
                <div class="col-md-4">
                    <div class="mb-1">Pilih Tanggal</div>
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
                <div class="col-md-4">
                    <div class="mb-1">Pilih Shift</div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-clock text-muted"></i>
                            </span>
                        </div>
                        <select name="shift" id="filter_shift" class="form-select form-control border-start-0">
                            <option value="">Semua Shift</option>
                            <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                            <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                            <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 align-self-end">
                    <a href="{{ route('suhu.index') }}" class="btn btn-primary mb-2">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const date = document.getElementById('filter_date');
                const shift = document.getElementById('filter_shift');
                const form = document.getElementById('filterForm');

                date.addEventListener('change', () => form.submit());
                shift.addEventListener('change', () => form.submit());
            });
        </script>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Pukul</th>
                                <th>Pemeriksaan</th>
                                <th>Keterangan</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>SPV</th>
                                <th>Verification</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                            @endphp
                            @forelse ($data as $dep)
                                <tr>
                                    <td class="text-center align-middle">{{ $no++ }}</td>
                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift:
                                        {{ $dep->shift }}</td>
                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}</td>
                                    <td class="text-center align-middle">
                                        @php
                                            $hasilSuhu = is_string($dep->hasil_suhu)
                                                ? json_decode($dep->hasil_suhu, true)
                                                : $dep->hasil_suhu ?? [];
                                            if (!$hasilSuhu) {
                                                $hasilSuhu = [];
                                            }
                                            $areaList = $area_suhus ?? [];
                                        @endphp

                                        @if (!empty($hasilSuhu))
                                            <a href="javascript:void(0);" class="btn btn-info btn-sm text-white"
                                                data-bs-toggle="modal" data-bs-target="#peneraanModal{{ $dep->uuid }}">
                                                <i class="bi bi-eye"></i> Lihat Hasil
                                            </a>

                                            {{-- Modal Hasil Pemeriksaan --}}
                                            <div class="modal fade" id="peneraanModal{{ $dep->uuid }}" tabindex="-1"
                                                aria-labelledby="peneraanModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                                <div
                                                    class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title"
                                                                id="peneraanModalLabel{{ $dep->uuid }}">
                                                                Tanggal :
                                                                {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} |
                                                                Shift: {{ $dep->shift }}
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-0">
                                                            <div class="table-responsive">
                                                                <table
                                                                    class="table table-bordered table-sm mb-0 text-center align-middle">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th style="width: 20%" class="text-start ps-3">
                                                                                Detail</th>
                                                                            @foreach ($areaList as $area)
                                                                                <th>{{ $area->area }}</th>
                                                                            @endforeach
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        {{-- Baris Standar Suhu --}}
                                                                        <tr>
                                                                            <td class="fw-bold text-start ps-3 bg-light">
                                                                                Standar (°C)</td>
                                                                            @foreach ($areaList as $area)
                                                                                <td class="text-center bg-light"
                                                                                    style="font-weight: 600; font-size: 0.8rem;">
                                                                                    @if ($area->standar_min !== null && $area->standar_max !== null)
                                                                                        ({{ $area->standar_min }})
                                                                                        - ({{ $area->standar_max }})
                                                                                    @else
                                                                                        <span class="text-muted">-</span>
                                                                                    @endif
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>

                                                                        {{-- Baris Aktual (Hasil Pemeriksaan) --}}
                                                                        <tr>
                                                                            <td class="fw-bold text-start ps-3">Aktual (°C)
                                                                            </td>
                                                                            @foreach ($areaList as $area)
                                                                                @php
                                                                                    $matched = collect(
                                                                                        $hasilSuhu,
                                                                                    )->firstWhere('area', $area->area);

                                                                                    $nilai = isset($matched['suhu'])
                                                                                        ? floatval($matched['suhu'])
                                                                                        : null;
                                                                                    $min = $area->standar_min;
                                                                                    $max = $area->standar_max;

                                                                                    // Logika Penentuan Warna
                                                                                    if (
                                                                                        $nilai === null ||
                                                                                        is_nan($nilai)
                                                                                    ) {
                                                                                        $colorClass = 'text-dark';
                                                                                        $displayNilai = '-';
                                                                                    } elseif (
                                                                                        $min !== null &&
                                                                                        $max !== null
                                                                                    ) {
                                                                                        // Asumsikan standar_min bisa lebih besar dari max (seperti case dev sebelumnya)
                                                                                        $actualMin = min($min, $max);
                                                                                        $actualMax = max($min, $max);

                                                                                        if (
                                                                                            $nilai >= $actualMin &&
                                                                                            $nilai <= $actualMax
                                                                                        ) {
                                                                                            $colorClass =
                                                                                                'text-success'; // Masuk standar
                                                                                        } else {
                                                                                            $colorClass = 'text-danger'; // Keluar standar
                                                                                        }
                                                                                        $displayNilai = $nilai;
                                                                                    } else {
                                                                                        $colorClass = 'text-dark';
                                                                                        $displayNilai = $nilai;
                                                                                    }
                                                                                @endphp
                                                                                <td class="fw-bold text-center {{ $colorClass }}"
                                                                                    style="font-size: 0.9rem;">
                                                                                    {{ $displayNilai }}
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                        {{-- ================= RH ================= --}}
                                                                        <tr>
                                                                            <td class="fw-bold text-start ps-3 bg-light">
                                                                                Standar RH (%)</td>
                                                                            @foreach ($areaList as $area)
                                                                                <td class="bg-light">
                                                                                    @if ($area->rh_min !== null && $area->rh_max !== null)
                                                                                        ({{ $area->rh_min }})
                                                                                        - ({{ $area->rh_max }})
                                                                                    @else
                                                                                        -
                                                                                    @endif
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>

                                                                        <tr>
                                                                            <td class="fw-bold text-start ps-3">Aktual RH
                                                                                (%)</td>
                                                                            @foreach ($areaList as $area)
                                                                                @php
                                                                                    $matched = collect(
                                                                                        $hasilSuhu,
                                                                                    )->firstWhere('area', $area->area);

                                                                                    $nilai = $matched['rh'] ?? null;
                                                                                    $min = $area->rh_min;
                                                                                    $max = $area->rh_max;

                                                                                    if (
                                                                                        $nilai === '-' ||
                                                                                        $nilai === null
                                                                                    ) {
                                                                                        $color = 'text-dark';
                                                                                        $show = $nilai ?? '-';
                                                                                    } elseif (
                                                                                        $min !== null &&
                                                                                        $max !== null
                                                                                    ) {
                                                                                        $color =
                                                                                            $nilai >= min($min, $max) &&
                                                                                            $nilai <= max($min, $max)
                                                                                                ? 'text-success'
                                                                                                : 'text-danger';
                                                                                        $show = $nilai;
                                                                                    } else {
                                                                                        $color = 'text-dark';
                                                                                        $show = $nilai;
                                                                                    }
                                                                                @endphp

                                                                                <td class="fw-bold {{ $color }}">
                                                                                    {{ $show }}</td>
                                                                            @endforeach
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button"
                                                                class="btn btn-secondary btn-sm px-4 rounded-pill"
                                                                data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">Belum ada data</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ !empty($dep->keterangan) ? $dep->keterangan : '-' }}</td>
                                    <td class="text-center align-middle fw-medium">{{ $dep->username }}</td>
                                    <td class="text-center align-middle">{{ $dep->nama_produksi }}</td>
                                    <td class="text-center align-middle">
                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                class="text-danger fw-bold text-decoration-none"
                                                style="cursor: pointer;">Revision</a>
                                        @endif
                                    </td>

                                    <td class="text-center align-middle">
                                        @can('can access verification button')
                                            <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                                                <i class="bi bi-shield-check me-1"></i> Verifikasi
                                            </button>
                                        @endcan
                                        @can('can access edit button')
                                            <a href="{{ route('suhu.edit.form', $dep->uuid) }}"
                                                class="btn btn-warning btn-sm me-1">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                        @endcan
                                        @can('can access update button')
                                            <a href="{{ route('suhu.update.form', $dep->uuid) }}"
                                                class="btn btn-info btn-sm me-1">
                                                <i class="bi bi-pencil"></i> Update
                                            </a>
                                        @endcan
                                        @can('can access delete button')
                                            <form action="{{ route('suhu.destroy', $dep->uuid) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @endcan
                                        <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1"
                                            aria-labelledby="verifyModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-md">
                                                <form action="{{ route('suhu.verification.update', $dep->uuid) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white"
                                                        style="background: linear-gradient(145deg, #7a1f12, #9E3419);
                                            box-shadow: 0 15px 40px rgba(0,0,0,0.5);">
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
                                                            <p class="text-light mb-4 fs-6">
                                                                Pastikan data yang akan diverifikasi di check dengan teliti
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
                                                                        style="
                                                        background: linear-gradient(135deg, #fff1f0, #ffe5de);
                                                        border: 2px solid #dc3545;
                                                        border-radius: 12px;
                                                        color: #dc3545;
                                                        height: 55px;
                                                        font-size: 1.1rem;
                                                        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
                                                        width: 85%;
                                                        transition: all 0.3s ease;
                                                        "
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
                                            </div>
                                            </form>
                                        </div>
                </div>
                </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="text-muted d-flex flex-column align-items-center justify-content-center">
                            <i class="bi bi-inbox fs-1 mb-2"></i>
                            <span>Belum ada data pemeriksaan suhu.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    </div>

    <script>
        // Auto-hide alert setelah 3 detik
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500); // Hapus elemen dari DOM
            });
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
        }

        .text-success {
            font-weight: bold;
        }

        .text-muted.fst-italic {
            color: #6c757d !important;
            font-style: italic !important;
        }

        .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        /* Perbaikan tampilan tombol action agar rapi sejajar */
        .btn-group .btn {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Mengubah modal tabel agar mengisi penuh modal body */
        .modal-body.p-0 .table {
            margin-bottom: 0;
            border-radius: 0;
        }

        .modal-body.p-0 .table th:first-child,
        .modal-body.p-0 .table td:first-child {
            border-left: none;
        }

        .modal-body.p-0 .table th:last-child,
        .modal-body.p-0 .table td:last-child {
            border-right: none;
        }
    </style>
@endsection
