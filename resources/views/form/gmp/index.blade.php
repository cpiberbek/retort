@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    {{-- ================= Alert ================= --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> {{ trim(session('error')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Data Pemeriksaan Personal Hygiene dan Kesehatan Karyawan</h2>
        <div class="btn-group" role="group">
            @can('can access add button')
            <a href="{{ route('gmp.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            @endcan
            @can('can access recycle')
            <a href="{{ route('gmp.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- Filter Tanggal --}}
    <form id="filterForm" method="GET" action="{{ route('gmp.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-1">Pilih Tanggal</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                    </div>
                    <input type="date" name="date" id="filter_date" class="form-control border-start-0" value="{{ request('date') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-1">Cari Data</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" name="search" id="search" class="form-control border-start-0"
                    value="{{ request('search') }}" placeholder="Cari Area...">
                </div>
            </div>
            <div class="col-md-4 align-self-end">
                <a href="{{ route('gmp.index') }}" class="btn btn-primary mb-2">
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

            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });

            date.addEventListener('change', () => form.submit());
        });
    </script>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date</th>

                            @php
                            // Normalisasi & hilangkan duplikasi area
                            $allAreas = [];

                            foreach($data as $d){
                                foreach($d->areas as $a){

                                    // Key normalisasi: lowercase + trim
                                    $key = strtolower(trim($a));

                                    // Simpan tampilan asli hanya jika belum ada
                                    if(!isset($allAreas[$key])){
                                        $allAreas[$key] = strtoupper(trim($a));
                                    }
                                }
                            }
                            @endphp

                            {{-- Header area --}}
                            @foreach($allAreas as $area)
                            <th>{{ $area }}</th>
                            @endforeach
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $no = ($data->currentPage() - 1) * $data->perPage() + 1; @endphp

                        @forelse($data as $dep)
                        <tr>
                            <td class="text-center align-middle">{{ $no++ }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}</td>
                            {{-- Kolom area --}}
                            @foreach($allAreas as $key => $areaLabel)
                            <td class="text-center align-middle">

                                @php
                                $targetArea = $key; // lowercase normalized
                                $scores = [];
                                $totalAttr = 0;
                                $countChecked = 0;

                                foreach($dep->pemeriksaan as $row){

                                    if(strtolower(trim($row['area'] ?? '')) === $targetArea){

                                        $attrKeys = array_diff(
                                        array_keys($row),
                                        ['nama_karyawan','pukul','keterangan','area']
                                        );

                                        $rowTotal = count($attrKeys);
                                        $rowCount = 0;

                                        foreach($attrKeys as $keyAttr){
                                            if((int)($row[$keyAttr] ?? 0) === 1){
                                                $rowCount++;
                                            }
                                        }

                                        $scores[] = [
                                        'nama' => $row['nama_karyawan'],
                                        'nilai' => $rowCount
                                        ];

                                        $totalAttr += $rowTotal;
                                        $countChecked += $rowCount;
                                    }
                                }

                                $persen = $totalAttr > 0 
                                ? round(($countChecked/$totalAttr)*100, 1)
                                : 0;

                                usort($scores, fn($a,$b) => $b['nilai'] <=> $a['nilai']);
                                    $top = array_slice($scores, 0, 3);
                                    @endphp

                                    {{ $persen }} %
                                    <br>
                                    <small>
                                        @foreach($top as $s)
                                        • {{ $s['nama'] }} ({{ $s['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>
                                @endforeach
                                <td class="text-center align-middle">{{ $dep->username }}</td>
                                <td class="text-center align-middle">{{ $dep->nama_produksi }}</td>
                                <td class="text-center align-middle">
                                    @if ($dep->status_spv == 0)
                                    <span class="fw-bold text-secondary">Created</span>
                                    @elseif ($dep->status_spv == 1)
                                    <span class="fw-bold text-success">Verified</span>
                                    @elseif ($dep->status_spv == 2)
                                    <!-- Link buka modal -->
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revisionModal{{ $dep->uuid }}" 
                                     class="text-danger fw-bold text-decoration-none" style="cursor: pointer;">Revision</a>
                                     <!-- Modal -->
                                     <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="revisionModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="revisionModalLabel{{ $dep->uuid }}">Detail Revisi</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <ul class="list-unstyled mb-0">
                                                        <li><strong>Status:</strong> Revision</li>
                                                        <li><strong>Catatan:</strong> {{ $dep->catatan_spv ?? '-' }}</li>
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @can('can access verification button')
                                    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm mb-1" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                                        <i class="bi bi-shield-check me-1"></i> Verifikasi
                                    </button>
                                    @endcan
                                    @can('can access edit button')
                                    <a href="{{ route('gmp.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1 mb-1">
                                        <i class="bi bi-pencil-square"></i> Edit Data
                                    </a>
                                    @endcan
                                    @can('can access update button')
                                    <a href="{{ route('gmp.edit', $dep->uuid) }}" class="btn btn-info btn-sm me-1 mb-1">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>
                                    @endcan
                                    @can('can access delete button')
                                    <form action="{{ route('gmp.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm mb-1"
                                        onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                                @endcan
                                <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-md">

                                        <form action="{{ route('gmp.updateVerification', $dep->uuid) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white"
                                            style="background: linear-gradient(145deg, #7a1f12, #9E3419);">

                                            <!-- HEADER -->
                                            <div class="modal-header">
                                                <h5 class="modal-title">VERIFICATION</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>

                                            <!-- BODY -->
                                            <div class="modal-body">
                                                <select name="status_spv" class="form-select" required>
                                                    <option value="1">Verified</option>
                                                    <option value="2">Revision</option>
                                                </select>

                                                <textarea name="catatan_spv" class="form-control mt-3">
                                                    {{ $dep->catatan_spv }}
                                                </textarea>
                                            </div>

                                            <!-- FOOTER -->
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Submit</button>
                                            </div>

                                        </div>
                                    </form>

                                </div>
                            </div>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 6 + count($allAreas) }}" class="text-center">Belum ada data GMP karyawan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @can('can access export')
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form action="{{ route('gmp.export') }}" method="GET" class="row g-2 align-items-center">

                    {{-- Pilih Tanggal --}}
                    <div class="col-auto">
                        <label for="date" class="col-form-label fw-semibold">Pilih Tanggal</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" id="date" name="date"
                        class="form-control form-control-sm"
                        value="{{ request('date') }}" required>
                    </div>

                    {{-- Pilih Area --}}
                    <div class="col-auto">
                        <label for="atribut" class="col-form-label fw-semibold">Area</label>
                    </div>

                    <div class="col-auto">
                        <select id="atribut" name="atribut" class="form-control form-control-sm" required>
                            <option value="">-- Pilih Area --</option>

                            @foreach ($areas as $area)
                            <option value="{{ $area->area }}"
                                {{ request('atribut') == $area->area ? 'selected' : '' }}>
                                {{ strtoupper($area->area) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Button --}}
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </button>
                    </div>

                </form>
            </div>
        </div>
        @endcan

    </div>
</div>

{{-- Auto-hide alert --}}
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if(alert){
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>
<style>
    .table td, .table th {
        font-size: 0.85rem;
        white-space: nowrap; 
    }
    .text-danger {
        font-weight: bold;
    }
    .text-muted.fst-italic {
        color: #6c757d !important;
        font-style: italic !important;
    }
    .container {
        padding-left: 2px !important;
        padding-right: 2px !important;
    }
</style>
</div>
@endsection
