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
        <h2 class="h4">Data No. Lot Wire</h2>
        <div class="btn-group" role="group">
            @can('can access add button')
            <a href="{{ route('wire.create') }}" class="btn btn-success">
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
            <a href="{{ route('wire.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- Filter Form --}}
    <form id="filterForm" method="GET" action="{{ route('wire.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
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
                    value="{{ request('date') }}" placeholder="Tanggal">
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
                        <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                        <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
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
                    value="{{ request('search') }}" placeholder="Cari Nama Varian / Supplier...">
                </div>
            </div>
            <div class="col-md-3 align-self-end">
                <a href="{{ route('wire.index') }}" class="btn btn-primary mb-2">
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
            const form = document.getElementById('filterForm');
            const exportPdfBtn = document.getElementById('exportPdfBtn');

            let timer;

            // Auto submit saat mengetik search (debounce)
            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });

            // Auto submit saat tanggal / shift berubah
            date.addEventListener('change', () => form.submit());
            if(shift) shift.addEventListener('change', () => form.submit());

            // Handle Export PDF
            if(exportPdfBtn){
                exportPdfBtn.addEventListener('click', function() {
                    // Ambil semua data dari form filter (Date, Shift, Search)
                    const formData = new FormData(form);
                    // Buat URL export dengan query string
                    const exportUrl = "{{ route('wire.exportPdf') }}?" + new URLSearchParams(formData).toString();
                    // Buka di tab baru
                    window.open(exportUrl, '_blank');
                });
            }
        });
    </script>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            {{-- Tabel Data --}}
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Nama Varian</th>
                            <th>Nama Supplier</th>
                            <th>Data Wire</th>
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
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | {{ $dep->shift }}</td>   
                            <td>{{ $dep->nama_produk }}</td>
                            <td>{{ $dep->nama_supplier }}</td>
                            <td class="text-center">
                                @php
                                $data_wire = json_decode($dep->data_wire, true);
                                @endphp

                                @if(!empty($data_wire))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#wireModal{{ $dep->uuid }}" class="fw-bold text-decoration-underline">
                                    Result
                                </a>

                                <div class="modal fade" id="wireModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog" style="max-width: 70%;">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title">Detail Pemeriksaan Wire</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                @foreach($data_wire as $mIndex => $mesin)
                                                <div class="mb-3 border-bottom pb-3">
                                                    <h6 class="fw-bold text-primary">🧭 Mesin: {{ $mesin['mesin'] ?? '-' }}</h6>
                                                    <table class="table table-bordered table-sm text-center">
                                                        <thead class="table-light">
                                                            <tr><th>No</th><th>Start - End</th><th>No. Lot</th></tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(!empty($mesin['detail']))
                                                            @foreach($mesin['detail'] as $idx => $dtl)
                                                            <tr>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td>{{ $dtl['start'] ?? '' }} - {{ $dtl['end'] ?? '' }}</td>
                                                                <td>{{ $dtl['no_lot'] ?? '' }}</td>
                                                            </tr>
                                                            @endforeach
                                                            @else
                                                            <tr><td colspan="3">Tidak ada data</td></tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @endforeach
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
                            <td class="text-center">{{ $dep->username }}</td>
                            <td class="text-center">
                                @if ($dep->status_spv == 1) <span class="fw-bold text-success">Verified</span>
                                @elseif ($dep->status_spv == 2) <span class="fw-bold text-danger">Revisi</span>
                                @else <span class="fw-bold text-secondary">Created</span> @endif
                            </td>
                            <td class="text-center">
                                @can('can access verification button')
                                <button class="btn btn-primary btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}"><i class="bi bi-shield-check"></i> Verif</button>
                                @endcan
                                @can('can access edit button')
                                <a href="{{ route('wire.edit.form', $dep->uuid) }}" class="btn btn-warning btn-sm mb-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                @endcan
                                @can('can access update button')
                                <a href="{{ route('wire.update.form', $dep->uuid) }}" class="btn btn-info btn-sm me-1 mb-1">
                                    <i class="bi bi-pencil"></i> Update
                                </a>
                                @endcan
                                @can('can access delete button')
                                <form action="{{ route('wire.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm mb-1" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i> Hapus</button>
                                </form>
                                @endcan

                                {{-- Modal Verify --}}
                                <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('wire.verification.update', $dep->uuid) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-content text-white" style="background: linear-gradient(145deg, #7a1f12, #9E3419);">
                                                <div class="modal-header border-bottom border-light-subtle">
                                                    <h5 class="modal-title">VERIFICATION</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-center">
                                                    <select name="status_spv" class="form-select form-select-lg mb-3 fw-bold text-center text-danger">
                                                        <option value="1" {{ $dep->status_spv==1?'selected':'' }}>Verified</option>
                                                        <option value="2" {{ $dep->status_spv==2?'selected':'' }}>Revision</option>
                                                    </select>
                                                    <textarea name="catatan_spv" class="form-control" rows="3" placeholder="Catatan...">{{ $dep->catatan_spv }}</textarea>
                                                </div>
                                                <div class="modal-footer border-top border-light-subtle">
                                                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning fw-bold">SUBMIT</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center">Belum ada data.</td></tr>
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
@endsection
