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
                    <i class="bi bi-list-check"></i> Recall
                </h3>

                <div class="d-flex gap-2">
                    @can('can access add button')
                    <a href="{{ route('recall.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </a>
                    @endcan

                    @can('can access recycle')
                    <a href="{{ route('recall.recyclebin') }}" class="btn btn-secondary">
                        <i class="bi bi-trash"></i> Recycle Bin
                    </a>
                    @endcan
                </div>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('recall.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                        <th rowspan="2">NO.</th>
                        <th rowspan="2">Date</th>
                        <th colspan="2">Informasi Telusur</th>
                        <th colspan="10">Informasi Pangan</th>
                        <th rowspan="2">Pembuat</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>Penyebab Telusur</th>
                        <th>Asal Informasi</th>
                        <th>Jenis Pangan</th>
                        <th>Nama Dagang</th>
                        <th>Berat / Isi Bersih</th>
                        <th>Jenis Kemasan</th>
                        <th>Kode Produksi</th>
                        <th>Tgl Produksi</th>
                        <th>Kadaluarsa</th>
                        <th>No. Daftar Pangan</th>
                        <th>Jumlah Produksi</th>
                        <th>Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                    $no = ($data->currentPage() - 1) * $data->perPage() + 1; 
                    @endphp
                    @forelse ($data as $dep)
                    <tr>
                        <td class="text-center align-middle">{{ $no++ }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}</td>   
                        <td class="text-center align-middle">{{ $dep->penyebab }}</td>
                        <td class="text-center align-middle">{{ $dep->asal_informasi }}</td>
                        <td class="text-center align-middle">{{ $dep->jenis_pangan }}</td>
                        <td class="text-center align-middle">{{ $dep->nama_dagang }}</td>
                        <td class="text-center align-middle">{{ $dep->berat_bersih }}</td>
                        <td class="text-center align-middle">{{ $dep->jenis_kemasan }}</td>
                        <td class="text-center align-middle">{{ $dep->kode_produksi }}</td>
                        <td class="text-center align-middle">{{ $dep->tanggal_produksi }}</td>
                        <td class="text-center align-middle">{{ $dep->tanggal_kadaluarsa }}</td>
                        <td class="text-center align-middle">{{ $dep->no_pendaftaran }}</td>
                        <td class="text-center align-middle">{{ $dep->jumlah_produksi }}</td>
                        <td class="text-center align-middle">{{ $dep->tindak_lanjut }}</td>
                        <td class="text-center align-middle">{{ $dep->username }}</td>
                        <td class="text-center align-middle">
                         @can('can access edit button')
                         <a href="{{ route('recall.edit', $dep->uuid) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @endcan
                        @can('can access delete button')
                        <form action="{{ route('recall.destroy', $dep->uuid) }}" method="POST" class="d-inline">
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
                            <form action="{{ route('recall.updateVerification', $dep->uuid) }}" method="POST">
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
                        <form action="{{ route('recall.updateApproval', $dep->uuid) }}" method="POST">
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
    <td colspan="20" class="text-center">Belum ada data recall.</td>
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
