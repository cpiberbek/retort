@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-3">

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
        <div class="row w-100">
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
                    value="{{ request('search') }}" placeholder="Cari PIC / Karyawan...">
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
                <table class="table table-bordered table-hover">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th class="align-middle" style="width: 5%">NO.</th>
                            <th class="align-middle" style="width: 12%">Date</th>
                            <th class="align-middle" style="width: 35%">Area Hygiene</th>
                            <th class="align-middle">QC</th>
                            <th class="align-middle">Produksi</th>
                            <th class="align-middle">SPV</th>
                            <th class="align-middle" style="width: 20%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $no = ($data->currentPage() - 1) * $data->perPage() + 1; @endphp

                        @forelse($data as $dep)
                        <tr>
                            <td class="text-center align-middle">{{ $no++ }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}</td>
                            
                            <td class="text-start align-middle">
                                @php
                                    $pemeriksaan = is_array($dep->pemeriksaan) ? $dep->pemeriksaan : (json_decode($dep->pemeriksaan, true) ?: []);
                                    
                                    // Kelompokkan baris pemeriksaan berdasarkan nama area asli dari JSON
                                    $groupedByArea = [];
                                    foreach($pemeriksaan as $row){
                                        $areaName = strtoupper(trim($row['area'] ?? 'Unknown'));
                                        $groupedByArea[$areaName][] = $row;
                                    }
                                @endphp

                                @foreach($groupedByArea as $areaName => $rows)
                                    @php
                                        $scores = [];
                                        $totalAttr = 0;
                                        $countChecked = 0;

                                        foreach($rows as $row){
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

                                        $persen = $totalAttr > 0 ? round(($countChecked / $totalAttr) * 100, 1) : 0;
                                        usort($scores, fn($a, $b) => $b['nilai'] <=> $a['nilai']);
                                        $top = array_slice($scores, 0, 3);
                                    @endphp

                                    {{-- Render Data Berurutan ke Bawah --}}
                                    <div class="p-2 {{ !$loop->last ? 'border-bottom mb-2 pb-2' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $areaName }}</span>
                                            <span class="badge bg-info text-dark fw-bold">{{ $persen }} %</span>
                                        </div>
                                        <div class="text-muted mt-1 ps-3" style="font-size: 0.8rem; line-height: 1.4;">
                                            @foreach($top as $s)
                                                • {{ $s['nama'] }} ({{ $s['nilai'] }})<br>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </td>

                            <td class="text-center align-middle">{{ $dep->username }}</td>
                            <td class="text-center align-middle">{{ $dep->nama_produksi ?? '-' }}</td>
                            <td class="text-center align-middle">
                                @if ($dep->status_spv == 0)
                                    <span>Created</span>
                                @elseif ($dep->status_spv == 1)
                                    <span class="badge bg-success">Verified</span>
                                @elseif ($dep->status_spv == 2)
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revisionModal{{ $dep->uuid }}" 
                                       class="badge bg-danger text-decoration-none" style="cursor: pointer;">Revision</a>
                                     
                                    <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="revisionModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="revisionModalLabel{{ $dep->uuid }}">Detail Revisi</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-start text-dark">
                                                    <ul class="list-unstyled mb-0">
                                                        <li><strong>Status:</strong> <span class="text-danger">Revision</span></li>
                                                        <li class="mt-2"><strong>Catatan SPV:</strong><br> {{ $dep->catatan_spv ?? '-' }}</li>
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
                                {{-- FIX TOMBOL ACTION: Diberi class m-1 agar ada margin di tiap sisinya dan memecah tumpukan --}}
                                <div class="d-flex flex-wrap justify-content-center">
                                    @can('can access verification button')
                                    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm m-1" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                                        <i class="bi bi-shield-check"></i> Verifikasi
                                    </button>
                                    @endcan
                                    @can('can access edit button')
                                    <a href="{{ route('gmp.edit.form', $dep->uuid) }}" class="btn btn-warning btn-sm m-1 text-dark fw-bold">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    @endcan
                                    @can('can access update button')
                                    <a href="{{ route('gmp.update.form', $dep->uuid) }}" class="btn btn-info btn-sm m-1 text-white fw-bold">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>
                                    @endcan
                                    @can('can access delete button')
                                    <form action="{{ route('gmp.destroy', $dep->uuid) }}" method="POST" class="d-inline m-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                    @endcan
                                </div>

                                <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                        <form action="{{ route('gmp.updateVerification', $dep->uuid) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white"
                                            style="background: linear-gradient(145deg, #7a1f12, #9E3419);">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">VERIFICATION</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start text-dark">
                                                    <label class="form-label fw-bold text-white">Status Verifikasi</label>
                                                    <select name="status_spv" class="form-select" required>
                                                        <option value="1">Verified</option>
                                                        <option value="2">Revision</option>
                                                    </select>
                                                    <label class="form-label mt-3 fw-bold text-white">Catatan SPV</label>
                                                    <textarea name="catatan_spv" class="form-control" rows="3" placeholder="Masukkan catatan revisi jika ada...">{{ trim($dep->catatan_spv) }}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data GMP karyawan pada list ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
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
        white-space: normal; /* Biarkan tombol membungkus ke baris baru jika kolom terlalu sempit */
    }
    .text-muted.fst-italic {
        color: #6c757d !important;
        font-style: italic !important;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.6em;
    }
</style>
@endsection