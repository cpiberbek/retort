@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Kontrol Labelisasi PVDC</h2>
        <div class="btn-group" role="group">
            @can('can access add button')
            <a href="{{ route('labelisasi_pvdc.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            @endcan
            @can('can access export')
            <button type="button" class="btn btn-danger" id="exportPdfBtn">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </button>
            @endcan
            @can('can access recycle')
            <a href="{{ route('labelisasi_pvdc.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- Filter Bar --}}
    <form id="filterForm" method="GET" action="{{ route('labelisasi_pvdc.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
        <div class="row w-100">
            <div class="col-md-3">
                <div class="mb-1">Pilih Tanggal</div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                    </div>
                    <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                    value="{{ request('date') }}">
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
                    value="{{ request('search') }}" placeholder="Cari Varian / Operator...">
                </div>
            </div>
            <div class="col-md-3 align-self-end">
                <a href="{{ route('labelisasi_pvdc.index') }}" class="btn btn-primary mb-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const date = document.getElementById('filter_date');
    const shift = document.getElementById('filter_shift');
    const search = document.getElementById('search');
    const exportBtn = document.getElementById('exportPdfBtn');

    let timer;

    [date, shift].forEach(function (el) {
        el.addEventListener('change', function () {
            form.submit();
        });
    });

    search.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            form.submit();
        }, 500);
    });

    if (exportBtn) {
        exportBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (date.value.trim() === '' || shift.value.trim() === '') {
                const modal = new bootstrap.Modal(document.getElementById('warningModal'));
                modal.show();
                return false;
            }

            const params = new URLSearchParams(new FormData(form));

            window.open(
                "{{ route('labelisasi_pvdc.exportPdf') }}?" + params.toString(),
                "_blank"
            );

            return false;
        });
    }
});
</script>

     <div class="modal fade" id="warningModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">(!) Filter Belum Lengkap Untuk Export Data</h5>
                </div>
                <div class="modal-body">
                    Silakan pilih <b>Tanggal</b> & <b>Shift</b> yang spesifik di bagian filter terlebih dahulu sebelum melakukan export.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Nama Varian</th>
                            <th>Hasil Pemeriksaan</th>
                            <th>QC</th>
                            <th>Operator</th>
                            <th>SPV</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $index => $dep)
                        <tr>
                            <td class="text-center">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage()
                            }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{$dep->shift }}</td>
                            <td class="text-center">{{ $dep->nama_produk }}</td>

                            {{-- Modal Result --}}
                            <td class="text-center">
                                <a href="#" class="fw-bold text-decoration-underline btn-result"
                                    data-uuid="{{ $dep->uuid }}">Result</a>
                            </td>

                            <td class="text-center">
                                {{ \Illuminate\Support\Facades\DB::table('users')->where('username', $dep->username)->value('name') }}
                            </td>
                            <td class="text-center">{{ $dep->nama_operator }}</td>

                            {{-- Status SPV --}}
                            <td class="text-center">
                                @if ($dep->status_spv == 0)
                                <span class="fw-bold text-secondary">Created</span>
                                @elseif ($dep->status_spv == 1)
                                <span class="fw-bold text-success">Verified</span>
                                @elseif ($dep->status_spv == 2)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#revModal{{ $dep->uuid }}"
                                    class="text-danger fw-bold">Revision</a>
                                    {{-- Modal Revision Simple --}}
                                    <div class="modal fade" id="revModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5>Detail Revisi</h5><button class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body"><strong>Catatan:</strong> {{ $dep->catatan_spv }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>

                                
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">{{ $data->withQueryString()->links('pagination::bootstrap-5') }}</div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.btn-result').forEach(function (button) {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();

                        const uuid = this.dataset.uuid;

                        fetch("{{ url('/labelisasi-pvdc') }}/" + uuid + "/result")
                            .then(response => response.text())
                            .then(html => {
                                document.body.insertAdjacentHTML('beforeend', html);

                                const modalElement = document.getElementById('resultModal' + uuid);

                                const modal = new bootstrap.Modal(modalElement);

                                modalElement.addEventListener('hidden.bs.modal', function () {
                                    this.remove();
                                });

                                modal.show();
                            });
                    });
                });
            });

            setTimeout(() => { document.querySelector('.alert')?.classList.remove('show'); }, 3000);
            
        </script>
        <style>
            .table td,
            .table th {
                font-size: 0.85rem;
                white-space: nowrap;
            }
        </style>
        @endsection
