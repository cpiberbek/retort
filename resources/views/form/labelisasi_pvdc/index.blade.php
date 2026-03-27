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
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filterForm');
            const inputs = [document.getElementById('filter_date'), document.getElementById('filter_shift')];
            const search = document.getElementById('search');
            let timer;

            inputs.forEach(el => el.addEventListener('change', () => form.submit()));
            search.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => form.submit(), 500); });

            document.getElementById('exportPdfBtn').addEventListener('click', () => {
                window.open("{{ route('labelisasi_pvdc.exportPdf') }}?" + new URLSearchParams(new FormData(form)).toString(), '_blank');
            });
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
                            <th>Nama Varian</th>
                            <th>Hasil Pemeriksaan</th>
                            <th>QC</th>
                            <th>Operator</th>
                            <th>SPV</th>
                            <th>Verification</th>
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
                                <a href="#" data-bs-toggle="modal" data-bs-target="#resModal{{ $dep->uuid }}"
                                    class="fw-bold text-decoration-underline">Result</a>
                                    <div class="modal fade" id="resModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title">Detail Labelisasi PVDC</h5>
                                                    <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body table-responsive">
                                                    <table class="table table-bordered table-sm text-center align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Mesin</th>
                                                                <th>Kode Batch</th>
                                                                <th>Gambar</th>
                                                                <th>Ket</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($dep->labelisasi_detail as $item)
                                                            <tr>
                                                                <td>{{ $item['mesin'] ?? '-' }}</td>

                                                                <td>{{ $item['mincing']->kode_produksi ?? 'Batch tidak
                                                                ditemukan' }}</td>

                                                                <td>
                                                                    @if(!empty($item['file']))
                                                                    <a href="{{ asset(stripslashes($item['file'])) }}"
                                                                    target="_blank">
                                                                    <img src="{{ asset(stripslashes($item['file'])) }}"
                                                                    width="50" class="img-thumbnail">
                                                                </a>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>

                                                            <td>{{ $item['keterangan'] ?? '-' }}</td>
                                                        </tr>
                                                        @endforeach

                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center">{{ $dep->username }}</td>
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

                                {{-- Action & Verification --}}
                                <td class="text-center">
                                    @can('can access verification button')
                                    <button class="btn btn-primary btn-sm fw-bold shadow-sm mb-1" data-bs-toggle="modal"
                                    data-bs-target="#verifyModal{{ $dep->uuid }}"><i class="bi bi-shield-check"></i>
                                Verifikasi</button>
                                @endcan
                                @can('can access edit button')
                                <a href="{{ route('labelisasi_pvdc.edit.form', $dep->uuid) }}" class="btn btn-warning btn-sm me-1 mb-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                @endcan
                                @can('can access update button')
                                <a href="{{ route('labelisasi_pvdc.update.form', $dep->uuid) }}"
                                    class="btn btn-info btn-sm me-1 mb-1"><i class="bi bi-pencil"></i> Update</a>
                                    @endcan
                                    @can('can access delete button')
                                    <form action="{{ route('labelisasi_pvdc.destroy', $dep->uuid) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus data?')">@csrf
                                        @method('DELETE')<button class="btn btn-danger btn-sm mb-1"><i
                                            class="bi bi-trash"></i> Hapus</button></form>
                                            @endcan

                                            {{-- Modal Verification (Gradient Style) --}}
                                            <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">

                                                {{-- PERBAIKAN UTAMA: --}}
                                                {{-- 1. Tambahkan 'modal-dialog-centered' agar di tengah vertikal --}}
                                                {{-- 2. Tambahkan style="max-width: 700px;" untuk MEMAKSA modal menjadi lebar --}}
                                                <div class="modal-dialog modal-dialog-centered" style="max-width: 700px;"> 

                                                    <form action="{{ route('labelisasi_pvdc.verification.update', $dep->uuid) }}" method="POST">
                                                        @csrf @method('PUT')

                                                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white" style="background: linear-gradient(145deg, #7a1f12, #9E3419);">

                                                            {{-- HEADER --}}
                                                            <div class="modal-header border-bottom border-light-subtle p-3 position-relative">
                                                                <h5 class="modal-title fw-bolder text-uppercase w-100 text-center" style="color: #00ffc4; letter-spacing: 1px;">
                                                                    <i class="bi bi-gear-fill me-2"></i> VERIFICATION
                                                                </h5>
                                                                {{-- Tombol Close --}}
                                                                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal"></button>
                                                            </div>

                                                            {{-- BODY --}}
                                                            <div class="modal-body p-4 text-center">

                                                                <p class="mb-4 text-light opacity-75" style="font-size: 0.95rem;">
                                                                    Pastikan data yang akan diverifikasi di check dengan teliti terlebih dahulu.
                                                                </p>

                                                                <div class="row justify-content-center">
                                                                    <div class="col-10"> {{-- col-10 memastikan konten tidak terlalu mepet pinggir --}}

                                                                        {{-- Pilih Status --}}
                                                                        <div class="mb-4">
                                                                            <label class="fw-bold mb-2 d-block text-white">Pilih Status Verifikasi</label>
                                                                            <select name="status_spv" class="form-select form-select-lg fw-bold text-center w-100 shadow-sm" 
                                                                            style="background: #fff; color: #dc3545; border-radius: 10px; border: none; cursor: pointer;">
                                                                            <option value="1" {{ $dep->status_spv==1?'selected':'' }}>✅ Verified (Disetujui)</option>
                                                                            <option value="2" {{ $dep->status_spv==2?'selected':'' }}>❌ Revision (Perlu Perbaikan)</option>
                                                                        </select>
                                                                    </div>

                                                                    {{-- Catatan --}}
                                                                    <div class="mb-2">
                                                                        <label class="fw-bold mb-2 d-block text-white">Catatan Tambahan (Opsional)</label>
                                                                        <textarea name="catatan_spv" rows="4" class="form-control w-100 shadow-sm" 
                                                                        style="background-color: #FFE5DE; border-radius: 10px; border: none;" 
                                                                        placeholder="Masukkan catatan...">{{ $dep->catatan_spv }}</textarea>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- FOOTER --}}
                                                        <div class="modal-footer justify-content-center border-top p-3" style="background-color: #9E3419; border-color: rgba(255,255,255,0.2) !important;">
                                                            <button type="button" class="btn btn-outline-light rounded-pill px-4 me-2 fw-bold" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn fw-bolder rounded-pill px-5 shadow" style="background-color: #E39581; color: #2c3e50;">
                                                                <i class="bi bi-box-arrow-in-down me-1"></i> SUBMIT
                                                            </button>
                                                        </div>

                                                    </div> {{-- End Modal Content --}}
                                                </form>
                                            </div> {{-- End Modal Dialog --}}
                                        </div> 
                                        {{-- End Modal --}}
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
