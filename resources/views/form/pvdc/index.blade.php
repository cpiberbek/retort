@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Alert error --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Data No. Lot PVDC</h2>
        <div class="btn-group" role="group">
            @can('can access add button')
            <a href="{{ route('pvdc.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            @endcan
            @can('can access export')
            {{-- Tombol Export PDF --}}
            <button type="button" class="btn btn-danger" id="exportPdfBtn">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </button>
            @endcan
            @can('can access recycle')
            <a href="{{ route('pvdc.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- Filter dan Live Search --}}
    <form id="filterForm" method="GET" action="{{ route('pvdc.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
        <div class="row">
            <div class="col-md-3">
                <div class="mb-1">Pilih Tanggal</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                    </div>
                    <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                    value="{{ request('date') }}" placeholder="Tanggal Batch...">
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-1">Pilih Shift</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-hourglass-split text-muted"></i>
                        </span>
                    </div>
                    <select name="shift" id="filter_shift" class="form-select border-start-0 form-control">
                        <option value="">Semua Shift</option>
                        {{-- Pastikan value ini sama dengan yang tersimpan di database --}}
                        <option value="1" {{ request("shift")=="1" ? "selected" : "" }}>Shift 1</option>
                        <option value="2" {{ request("shift")=="2" ? "selected" : "" }}>Shift 2</option>
                        <option value="3" {{ request("shift")=="3" ? "selected" : "" }}>Shift 3</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-1">Cari Data</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" name="search" id="search" class="form-control border-start-0"
                    value="{{ request('search') }}" placeholder="Cari Varian / Supplier / Catatan...">
                </div>
            </div>
            <div class="col-md-3 align-self-end">
                <a href="{{ route('pvdc.index') }}" class="btn btn-primary mb-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filterForm');
            const searchInput = document.getElementById('search');
            const dateInput = document.getElementById('filter_date');
            const shiftInput = document.getElementById('filter_shift');
            const exportPdfBtn = document.getElementById('exportPdfBtn');

            // Auto-submit saat mengetik di search (debounce)
            let timer;
            searchInput.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });

            // Auto-submit saat date atau shift berubah (Opsional, hilangkan jika ingin manual klik filter)
            dateInput.addEventListener('change', () => form.submit());
            shiftInput.addEventListener('change', () => form.submit());

            // --- LOGIC EXPORT PDF ---
            exportPdfBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah form submit biasa

                // Ambil nilai real-time dari input
                let dateVal = dateInput.value;
                let shiftVal = shiftInput.value;
                let searchVal = searchInput.value;

                // Bangun URL dengan query string
                // encodeURIComponent digunakan agar karakter spesial aman di URL
                let exportUrl = "{{ route('pvdc.exportPdf') }}" +
                "?date=" + encodeURIComponent(dateVal) +
                "&shift=" + encodeURIComponent(shiftVal) +
                "&search=" + encodeURIComponent(searchVal);

                // Buka di tab baru
                window.open(exportUrl, '_blank');
            });
        });
    </script>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            {{-- Table --}}
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Nama Varian</th>
                            <th>Nama Supplier</th>
                            <th>Tanggal Kedatangan</th>
                            <th>Tanggal Expired</th>
                            <th>Data PVDC</th>
                            <th>QC</th>
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
                            <td class="align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>
                            <td class="align-middle">{{ $dep->nama_produk }}</td>
                            <td class="align-middle">{{ $dep->nama_supplier }}</td>
                            <td class="text-center align-middle">{{
                                \Carbon\Carbon::parse($dep->tgl_kedatangan)->format('d-m-Y') }}</td>
                                <td class="text-center align-middle">{{
                                    \Carbon\Carbon::parse($dep->tgl_expired)->format('d-m-Y') }}</td>
                                    <td class="text-center align-middle">
                                        @php
                                        $data_pvdc = json_decode($dep->data_pvdc, true);
                                        @endphp

                                        @if(!empty($data_pvdc))
                                        @php
                                        $batches = $dep->pvdc_detail->flatMap(function ($mesin) {
                                            return $mesin['detail']->pluck('mincing.kode_produksi')->filter();
                                        })->unique()->values()->implode(', ');
                                        @endphp
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#pvdcModal{{ $dep->uuid }}"
                                            style="font-weight: bold; text-decoration: underline;">
                                            Result
                                        </a>

                                        {{-- Modal Detail PVDC --}}
                                        <div class="modal fade" id="pvdcModal{{ $dep->uuid }}" tabindex="-1"
                                            aria-labelledby="pvdcModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title" id="pvdcModalLabel{{ $dep->uuid }}">Detail
                                                        Pemeriksaan PVDC - Batch: {{ $batches ?: 'N/A' }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body table-responsive">
                                                        @foreach($dep->pvdc_detail as $mIndex => $mesin)
                                                        <div class="mb-3 border p-3 rounded bg-light">
                                                            <h6 class="fw-bold mb-2">🧭 Mesin: {{ $mesin['mesin'] ?? '-' }}</h6>
                                                            <table
                                                            class="table table-bordered table-striped table-sm text-center align-middle bg-white">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Batch</th>
                                                                    <th>No. Lot</th>
                                                                    <th>Waktu</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(!empty($mesin['detail']))
                                                                @foreach($mesin['detail'] as $index => $detail)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $detail['mincing']->kode_produksi ?? '-' }}</td>
                                                                    <td>{{ $detail['no_lot'] ?? '-' }}</td>
                                                                    <td>{{ $detail['waktu'] ?? '-' }}</td>
                                                                </tr>
                                                                @endforeach
                                                                @else
                                                                <tr>
                                                                    <td colspan="4">Tidak ada data batch</td>
                                                                </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endforeach
                                                    <div class="mt-3 text-start">
                                                        <strong>Catatan:</strong> {{ $dep->catatan ?? '-' }}
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Tutup</button>
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
                                    <span class="fw-bold text-secondary">Created</span>
                                    @elseif ($dep->status_spv == 1)
                                    <span class="fw-bold text-success">Verified</span>
                                    @elseif ($dep->status_spv == 2)
                                    <a href="javascript:void(0);" data-bs-toggle="modal"
                                    data-bs-target="#revisionModal{{ $dep->uuid }}"
                                    class="text-danger fw-bold text-decoration-none">Revision</a>

                                    {{-- Modal Revisi --}}
                                    <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Detail Revisi</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <ul class="list-unstyled mb-0">
                                                        <li><strong>Status:</strong> Revision</li>
                                                        <li><strong>Catatan:</strong> {{ $dep->catatan_spv ?? '-' }}</li>
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
                                <td class="text-center align-middle">
                                    @can('can access verification button')
                                    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm mb-1"
                                    data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                                    <i class="bi bi-shield-check me-1"></i> Verifikasi
                                </button>
                                @endcan
                                @can('can access edit button')
                                <a href="{{ route('pvdc.edit.form', $dep->uuid) }}"
                                    class="btn btn-warning btn-sm me-1 mb-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                @endcan
                                @can('can access update button')
                                <a href="{{ route('pvdc.update.form', $dep->uuid) }}"
                                    class="btn btn-info btn-sm me-1 mb-1">
                                    <i class="bi bi-pencil"></i> Update
                                </a>
                                @endcan
                                @can('can access delete button')
                                <form action="{{ route('pvdc.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm mb-1"
                                    onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                            @endcan

                            {{-- Modal Verifikasi (Disamakan Style dengan Mincing) --}}
                            <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                    <form action="{{ route('pvdc.verification.update', $dep->uuid) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white"
                                        style="background: linear-gradient(145deg, #7a1f12, #9E3419); box-shadow: 0 15px 40px rgba(0,0,0,0.5);">
                                        <div class="modal-header border-bottom border-light-subtle p-4"
                                        style="border-bottom-width: 3px !important;">
                                        <h5 class="modal-title fw-bolder fs-3 text-uppercase"
                                        style="color: #00ffc4;">
                                        <i class="bi bi-gear-fill me-2"></i> VERIFICATION
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white shadow-none"
                                    data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-5">
                                    <p class="text-light mb-4 fs-6">Pastikan data yang akan diverifikasi
                                    di check dengan teliti terlebih dahulu.</p>
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold mb-2 text-center d-block"
                                            style="color: #FFE5DE; font-size: 0.95rem;">Pilih Status
                                        Verifikasi</label>
                                        <select name="status_spv"
                                        class="form-select form-select-lg fw-bold text-center mx-auto"
                                        style="background: linear-gradient(135deg, #fff1f0, #ffe5de); border: 2px solid #dc3545; border-radius: 12px; color: #dc3545; height: 55px; font-size: 1.1rem; width: 85%;"
                                        required>
                                        <option value="1" {{ $dep->status_spv == 1 ? 'selected'
                                            : '' }} style="color: #198754; font-weight: 600;">✅
                                        Verified (Disetujui)</option>
                                        <option value="2" {{ $dep->status_spv == 2 ? 'selected'
                                            : '' }} style="color: #dc3545; font-weight: 600;">❌
                                        Revision (Perlu Perbaikan)</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label class="form-label fw-bold text-light mb-2">Catatan
                                    Tambahan (Opsional)</label>
                                    <textarea name="catatan_spv" rows="4"
                                    class="form-control text-dark border-0 shadow-none"
                                    placeholder="Masukkan catatan..."
                                    style="background-color: #FFE5DE; height: 120px;">{{ $dep->catatan_spv }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-end p-4 border-top"
                        style="background-color: #9E3419; border-color: #00ffc4 !important;">
                        <button type="button"
                        class="btn btn-outline-light fw-bold rounded-pill px-4 me-2"
                        data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn fw-bolder rounded-pill px-5"
                        style="background-color: #E39581; color: #2c3e50;">
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
    <td colspan="10" class="text-center">Belum ada data PVDC.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>

{{-- Pagination --}}
<div class="mt-3">
    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
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
    .table td,
    .table th {
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .text-success {
        color: green;
        font-weight: bold;
    }

    .text-danger {
        color: red;
        font-weight: bold;
    }

    .container {
        padding-left: 2px !important;
        padding-right: 2px !important;
    }
</style>

@endsection
