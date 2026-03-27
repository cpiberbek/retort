@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i> Laporan Withdrawl
                </h3>

                <div class="d-flex gap-2">
                    @can('can access add button')
                    <a href="{{ route('withdrawl.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </a>
                    @endcan

                    @can('can access recycle')
                    <a href="{{ route('withdrawl.recyclebin') }}" class="btn btn-secondary">
                        <i class="bi bi-trash"></i> Recycle Bin
                    </a>
                    @endcan
                </div>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('withdrawl.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                <div class="input-group" style="max-width: 220px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>
                    <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                    value="{{ request('date') }}" placeholder="Tanggal Produksi">
                </div>

                <div class="input-group flex-grow-1" style="max-width: 350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" id="search" class="form-control border-start-0"
                    value="{{ request('search') }}" placeholder="Cari sesuatu...">
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

            {{-- Tambahkan table-responsive agar tabel tidak keluar border --}}
            <div class="table-responsive">
             <table class="table table-striped table-bordered align-middle table-hover">
                <thead class="table-primary text-center">
                    <tr>
                        <th>NO.</th>
                        <th>Date | No. Withdrawl</th>
                        <th>Nama Produk</th>
                        <th>Kode Produksi | Expired Date</th>
                        <th>Jumlah Produksi</th>
                        <th>Tgl Akhir Edar | Jumlah Edar</th>
                        <th>Tgl Penarikan | Jumlah Tarik</th>
                        <th>Rincian</th>
                        <th>Pembuat</th>
                        <th>SPV</th>
                        <th>Manager</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                    $no = ($data->currentPage() - 1) * $data->perPage() + 1; 
                    @endphp
                    @forelse ($data as $dep)
                    <tr>
                        <td class="text-center align-middle">{{ $no++ }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | {{ $dep->no_withdrawl }}</td>   
                        <td class="text-center align-middle">{{ $dep->nama_produk }}</td>
                        <td class="text-center align-middle">{{ $dep->kode_produksi }} | {{ \Carbon\Carbon::parse($dep->exp_date)->format('d-m-Y') }}</td>
                        <td class="text-center align-middle">{{ $dep->jumlah_produksi }} Box</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->tanggal_edar)->format('d-m-Y') }} | {{ $dep->jumlah_edar }} Box</td>  
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->tanggal_tarik)->format('d-m-Y') }} | {{ $dep->jumlah_tarik }} Box</td>  
                        <td class="text-center align-middle">
                            @php
                            $withdrawl = json_decode($dep->rincian, true);
                            @endphp

                            @if(!empty($withdrawl))
                            <a href="#" data-bs-toggle="modal" data-bs-target="#withdrawlModal{{ $dep->uuid }}" 
                             style="font-weight: bold; text-decoration: underline;">
                             Detail
                         </a>

                         <div class="modal fade" id="withdrawlModal{{ $dep->uuid }}" tabindex="-1"
                            aria-labelledby="withdrawlModalLabel{{ $dep->uuid }}" aria-hidden="true">
                            <div class="modal-dialog" style="max-width: 60%;">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="withdrawlModalLabel{{ $dep->uuid }}">
                                            Detail rincian sebagai berikut
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-sm text-center align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Supplier</th>
                                                        <th>Alamat</th>
                                                        <th>Jumlah</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($withdrawl as $i => $row)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>{{ $row['nama_supplier'] ?? '-' }}</td>
                                                        <td>{{ $row['alamat'] ?? '-' }}</td>
                                                        <td>{{ $row['jumlah'] ?? '-' }} Box</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
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
                    <td class="text-center align-middle">{{ $dep->username }}</td>
                    <td class="text-center align-middle">
                        @if ($dep->status_spv == 0)
                        <span class="fw-bold text-secondary"><b>Created</b></span>
                        @else
                        <b><a href="#" class="{{ $dep->status_spv == 1 ? 'text-success' : 'text-danger' }} fw-bold"
                           data-bs-toggle="modal" data-bs-target="#modalSpv{{ $dep->uuid }}">
                           {{ $dep->status_spv == 1 ? 'Verified' : 'Revision' }}</a></b>
                           @endif
                           {{-- Modal SPV --}}
                           <div class="modal fade" id="modalSpv{{ $dep->uuid }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header {{ $dep->status_spv == 1 ? 'bg-success' : 'bg-danger' }} text-white">
                                        <h5 class="modal-title">
                                            {{ $dep->status_spv == 1 ? 'Detail Verifikasi SPV' : 'Detail Revisi SPV' }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-unstyled">
                                            <li><strong>Status:</strong> {{ $dep->status_spv == 1 ? 'Verified' : 'Revision' }}</li>
                                            <li><strong>Supervisor:</strong> {{ $dep->nama_spv ?? '-' }}</li>
                                            <li><strong>Catatan:</strong> {{ $dep->catatan_spv ?? '-' }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        @if ($dep->status_manager == 0)
                        <span class="fw-bold text-secondary"><b>Pending</b></span>
                        @else
                        <b><a href="#" class="{{ $dep->status_manager == 1 ? 'text-success' : 'text-danger' }} fw-bold"
                         data-bs-toggle="modal" data-bs-target="#modalManager{{ $dep->uuid }}">
                         {{ $dep->status_manager == 1 ? 'Verified' : 'Revision' }}</a></b>
                         @endif
                         {{-- Modal Manager --}}
                         <div class="modal fade" id="modalManager{{ $dep->uuid }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header {{ $dep->status_manager == 1 ? 'bg-success' : 'bg-danger' }} text-white">
                                        <h5 class="modal-title">
                                            {{ $dep->status_manager == 1 ? 'Detail Verifikasi Manager' : 'Detail Revisi Manager' }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-unstyled">
                                            <li><strong>Status:</strong> {{ $dep->status_manager == 1 ? 'Verified' : 'Revision' }}</li>
                                            <li><strong>Manager:</strong> {{ $dep->nama_manager ?? '-' }}</li>
                                            <li><strong>Catatan:</strong> {{ $dep->catatan_manager ?? '-' }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        @can('can access verification button')
                        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" 
                        data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                        <i class="bi bi-shield-check me-1"></i> Verifikasi SPV
                    </button>
                    @endcan
                    @can('can access verification manager')
                    <button type="button" class="btn btn-info btn-sm fw-bold shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#verifyManagerModal{{ $dep->uuid }}">
                    <i class="bi bi-person-check-fill me-1"></i> Persetujuan Manager
                </button>
                @endcan
                @can('can access edit button')
                <a href="{{ route('withdrawl.edit', $dep->uuid) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                @endcan
                @can('can access delete button')
                <form action="{{ route('withdrawl.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                    onclick="return confirm('Yakin ingin menghapus?')">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>
            @endcan
            <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $dep->uuid }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <form action="{{ route('withdrawl.updateVerification', $dep->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white" 
                        style="background: linear-gradient(145deg, #7a1f12, #9E3419); box-shadow: 0 15px 40px rgba(0,0,0,0.5);">

                        <div class="modal-header border-bottom p-4">
                            <h5 class="modal-title fw-bolder fs-3 text-uppercase" style="color: #00ffc4;">
                                <i class="bi bi-gear-fill me-2"></i> VERIFICATION SPV
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body p-5">
                            <p class="text-light mb-4 fs-6">Pastikan data yang akan diverifikasi dicek dengan teliti terlebih dahulu.</p>

                            <label class="form-label fw-bold mb-2 text-center d-block" style="color: #FFE5DE;">Status Verifikasi SPV</label>
                            <select name="status_spv" class="form-select form-select-lg fw-bold text-center mx-auto"
                            style="background: linear-gradient(135deg, #fff1f0, #ffe5de); border: 2px solid #dc3545; border-radius: 12px; color: #dc3545; height: 55px; font-size: 1.1rem;">
                            <option value="1" {{ $dep->status_spv == 1 ? 'selected' : '' }}>✅ Verified</option>
                            <option value="2" {{ $dep->status_spv == 2 ? 'selected' : '' }}>❌ Revision</option>
                        </select>
                        <br>
                        <label class="form-label fw-bold text-light mt-3">Catatan SPV (Opsional)</label>
                        <textarea name="catatan_spv" rows="4" class="form-control text-dark border-0" style="background-color: #FFE5DE;">
                            {{ $dep->catatan_spv }}</textarea>
                        </div>

                        <div class="modal-footer p-4" style="background-color: #9E3419;">
                            <button type="button" class="btn btn-outline-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn fw-bolder rounded-pill px-5" style="background-color: #E39581; color: #2c3e50;">
                                <i class="bi bi-save-fill me-1"></i> SUBMIT
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="verifyManagerModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="verifyManagerModalLabel{{ $dep->uuid }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <form action="{{ route('withdrawl.updateApproval', $dep->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white"
                    style="background: linear-gradient(145deg, #0a3d62, #1e5f92);">

                    <div class="modal-header p-4 border-bottom border-light">
                        <h5 class="modal-title fw-bolder fs-3 text-uppercase" style="color: #00eaff;">
                            <i class="bi bi-person-check-fill me-2"></i> MANAGER APPROVAL
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-5">
                        <p class="text-light mb-4 fs-6">Pastikan data telah ditinjau sebelum memberikan persetujuan.</p>

                        <label class="form-label fw-bold mb-2 text-light">Status Persetujuan Manager</label>
                        <br>
                        <select name="status_manager"
                        class="form-select form-select-lg fw-bold text-center mx-auto"
                        style="background: linear-gradient(135deg, #e8f6ff, #d1ecff); border: 2px solid #0a3d62; border-radius: 12px; color: #0a3d62; height: 55px;">
                        <option value="1" {{ $dep->status_manager == 1 ? 'selected' : '' }}>✔️ Approved</option>
                        <option value="2" {{ $dep->status_manager == 2 ? 'selected' : '' }}>❌ Revision Required</option>
                    </select>
                    <br>
                    <label class="form-label fw-bold text-light mt-3">Catatan Manager (Opsional)</label>
                    <textarea name="catatan_manager" rows="4" class="form-control text-dark"
                    style="background-color: #d1ecff;">{{ $dep->catatan_manager }}</textarea>
                </div>

                <div class="modal-footer justify-content-end p-4" style="background-color: #1e5f92;">
                    <button type="button" class="btn btn-outline-light fw-bold rounded-pill px-4 me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn fw-bolder rounded-pill px-5" style="background-color: #00eaff; color: #003449;">
                        <i class="bi bi-save-fill me-1"></i> SUBMIT
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
</td>

</tr>
@empty
<tr>
    <td colspan="19" class="text-center">Belum ada data withdrawl.</td>
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
</div>

{{-- Auto-hide alert setelah 3 detik --}}
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if(alert){
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>

{{-- CSS tambahan agar tabel lebih rapi --}}
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
@endsection
