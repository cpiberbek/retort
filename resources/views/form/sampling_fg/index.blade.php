@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- HEADER: Menggunakan Versi Anda (Ada Export PDF) --}}
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Pemeriksaan Proses Sampling Finish Good</h2>
        <div class="btn-group">
            @can('can access add button')
            <a href="{{ route('sampling_fg.create') }}" class="btn btn-success">
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
            <a href="{{ route('sampling_fg.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- FILTER: Menggunakan Versi Anda (Ada Shift) --}}
    <form id="filterForm" method="GET" action="{{ route('sampling_fg.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
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
                    value="{{ request('date') }}" placeholder="Tanggal Batch">
                </div>
            </div>
            <div class="col-md-3">
                {{-- Filter Shift --}}
                <div class="mb-1">Pilih Shift</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-hourglass-split text-muted"></i>
                        </span>
                    </div>
                    <select name="shift" id="filter_shift" class="form-select border-start-0 form-control">
                        <option value="">Semua Shift</option>
                        <option value="1" {{ request("shift") == "1" ? "selected" : "" }}>Shift 1</option>
                        <option value="2" {{ request("shift") == "2" ? "selected" : "" }}>Shift 2</option>
                        <option value="3" {{ request("shift") == "3" ? "selected" : "" }}>Shift 3</option>
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
                    value="{{ request('search') }}" placeholder="Cari Nama Varian / Kode Batch...">
                </div>
            </div>
            <div class="col-md-3 align-self-end">
                <!-- <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button> -->
                <a href="{{ route('sampling_fg.index') }}" class="btn btn-primary mb-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </div>
    </form>

    {{-- SCRIPT: Menggunakan Versi Anda (Handler PDF & Shift) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('search');
            const date = document.getElementById('filter_date');
            const shift = document.getElementById('filter_shift');
            const form = document.getElementById('filterForm');
            const exportPdfBtn = document.getElementById('exportPdfBtn');

            let timer;

            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });



            date.addEventListener('change', () => form.submit());
            if(shift) shift.addEventListener('change', () => form.submit());

            // Handle Export PDF
            if(exportPdfBtn){
                exportPdfBtn.addEventListener('click', function() {
                    const formData = new FormData(form);
                    const exportUrl = "{{ route('sampling_fg.exportPdf') }}?" + new URLSearchParams(formData).toString();
                    window.open(exportUrl, '_blank');
                });
            }
        });
    </script>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            {{-- TABEL DATA --}}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th rowspan="2" style="width: 3%;">NO.</th>
                            <th rowspan="2" style="width: 8%;">Tanggal | Shift</th>
                            <th rowspan="2" style="width: 4%;">Palet</th>
                            <th rowspan="2" style="width: 12%;">Nama Varian</th>
                            <th rowspan="2" style="width: 6%;">Kode Batch</th>
                            <th rowspan="2" style="width: 6%;">Exp. Date</th>
                            <th colspan="4">Pemeriksaan Proses Cartoning</th>
                            <th rowspan="2" style="width: 5%;">Isi<br>/Box</th>
                            <th rowspan="2" style="width: 4%;">Jml<br>Box</th>
                            <th colspan="3">Status Varian</th>
                            <th rowspan="2" style="width: 5%;">Item<br>Mutu</th>
                            <th rowspan="2" style="width: 8%;">Catatan</th>
                            <th rowspan="2" style="width: 4%;">QC</th>
                            <th rowspan="2" style="width: 4%;">Koord</th>
                            <th rowspan="2" style="width: 4%;">SPV</th>
                            <th rowspan="2" style="width: 5%;">Action</th>
                        </tr>
                        <tr>
                            <th style="width: 4%;">Jam</th>
                            <th style="width: 4%;">Kalib</th>
                            <th style="width: 4%;">Berat</th>
                            <th style="width: 5%;">Ket</th>
                            <th style="width: 3%;">Rls</th>
                            <th style="width: 3%;">Rjc</th>
                            <th style="width: 3%;">Hld</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                        $no = ($data->currentPage() - 1) * $data->perPage() + 1; 
                        @endphp
                        @forelse ($data as $dep)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | {{ $dep->shift }}</td>
                            <td class="text-center">{{ $dep->palet }}</td>
                            <td class="text-center">{{ $dep->nama_produk }}</td>
                            <td class="text-center">{{ $dep->kode_produksi }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($dep->exp_date)->format('d-m-Y') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}</td>
                            <td class="text-center">
                                @if ($dep->kalibrasi == 'Sesuai') <span class="text-success fw-bold">✔</span>
                                @else <span class="text-danger fw-bold">✘</span> @endif
                            </td>
                            <td class="text-center">{{ $dep->berat_produk }}</td>
                            <td class="text-center small">{{ $dep->keterangan }}</td>
                            <td class="text-center">{{ $dep->isi_per_box }}</td>
                            <td class="text-center">{{ $dep->jumlah_box }}</td>
                            <td class="text-center">{{ $dep->release }}</td>
                            <td class="text-center">{{ $dep->reject }}</td>
                            <td class="text-center">{{ $dep->hold }}</td>
                            <td class="text-center small">{{ $dep->item_mutu }}</td>
                            <td class="text-start small">{{ $dep->catatan }}</td>
                            <td class="text-center">{{ $dep->username }}</td>
                            <td class="text-center">{{ $dep->nama_koordinator }}</td>
                            
                            {{-- STATUS SPV --}}
                            <td class="text-center align-middle">
                                @if ($dep->status_spv == 0)
                                <span class="fw-bold text-secondary">Created</span>
                                @elseif ($dep->status_spv == 1)
                                <span class="fw-bold text-success">Verified</span>
                                @elseif ($dep->status_spv == 2)
                                <span class="fw-bold text-danger">Revisi</span>
                                @endif
                            </td>

                            {{-- ACTION BUTTONS --}}
                            <td class="text-center align-middle">
                                @can('can access verification button')
                                <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm mb-1" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                                    <i class="bi bi-shield-check me-1"></i> Verifikasi
                                </button>
                                @endcan

                                @can('can access edit button')
                                <a href="{{ route('sampling_fg.edit.form', $dep->uuid) }}" class="btn btn-warning btn-sm me-1 mb-1">
                                    <i class="bi bi-pencil-square"></i> Edit Data
                                </a>
                                @endcan

                                @can('can access update button')
                                <a href="{{ route('sampling_fg.update.form', $dep->uuid) }}" class="btn btn-info btn-sm me-1 mb-1">
                                    <i class="bi bi-pencil"></i> Update
                                </a>
                                @endcan

                                @can('can access delete button')
                                <form action="{{ route('sampling_fg.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                                @endcan

                                {{-- MODAL VERIFY (Gradient Style - Versi Anda) --}}
                                <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('sampling_fg.verification.update', $dep->uuid) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-content text-white" style="background: linear-gradient(145deg, #7a1f12, #9E3419);">
                                                <div class="modal-header border-bottom border-light-subtle">
                                                    <h5 class="modal-title">VERIFICATION</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-center">
                                                    <select name="status_spv" class="form-select form-select-lg mb-3 fw-bold text-center text-danger">
                                                        <option value="1" {{ $dep->status_spv==1?'selected':'' }}>Verified (OK)</option>
                                                        <option value="2" {{ $dep->status_spv==2?'selected':'' }}>Revision</option>
                                                    </select>
                                                    <textarea name="catatan_spv" class="form-control" rows="3" placeholder="Catatan (Opsional)">{{ $dep->catatan_spv }}</textarea>
                                                </div>
                                                <div class="modal-footer border-top border-light-subtle">
                                                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning fw-bold">SUBMIT</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- END MODAL --}}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="21" class="text-center py-3">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if(alert) alert.classList.remove('show');
    }, 3000);
</script>

<style>
    /* Styling Agar Tabel Rapi & Compact */
    .table th { font-size: 0.8rem; white-space: nowrap; padding: 8px 4px; }
    .table td { font-size: 0.8rem; padding: 6px 4px; white-space: nowrap; }
    .table td.text-start { text-align: left !important; }
    .table td.small { font-size: 0.75rem; white-space: normal; max-width: 150px; } /* Untuk catatan panjang */
    .container { padding: 0 5px; }
</style>
@endsection