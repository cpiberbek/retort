@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
        <h2 class="h4"> Pemeriksaan Washing - Drying</h2>
        <div class="btn-group">
            @can('can access add button')
            <a href="{{ route('washing.create') }}" class="btn btn-success">
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
            <a href="{{ route('washing.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- FILTER: Menggunakan Versi Anda (Ada Shift) --}}
    <form id="filterForm" method="GET" action="{{ route('washing.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
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
                <a href="{{ route('washing.index') }}" class="btn btn-primary mb-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </div>  
    </form>

    {{-- SCRIPT: Versi Anda (PDF & Shift Handler) --}}
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
                    const exportUrl = "{{ route('washing.exportPdf') }}?" + new URLSearchParams(formData).toString();
                    window.open(exportUrl, '_blank');
                });
            }
        });
    </script>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- TABEL DATA --}}
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Nama Varian</th>
                            <th>Kode Batch</th>
                            <th>Waktu</th>
                            <th>Pemeriksaan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
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
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>   
                            <td class="text-center align-middle">{{ $dep->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $dep->kode_produksi }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}</td>
                            
                            {{-- KOLOM PEMERIKSAAN (Menggunakan Modal Detail Versi Server yang Lengkap) --}}
                            <td class="text-center align-middle">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#detailModal{{ $dep->uuid }}" 
                                 class="text-primary fw-bold text-decoration-none" style="cursor: pointer;">Result</a>

                                 {{-- Modal Detail (Washing, PC Kleer, Pottasium, Speed) --}}
                                 <div class="modal fade" id="detailModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="detailModalLabel{{ $dep->uuid }}">Detail Pemeriksaan Washing - Drying</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">

                                                {{-- IDENTIFIKASI --}}
                                                <h6 class="text-secondary fw-bold mt-2">Identifikasi</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Nama Varian</th><td>{{ $dep->nama_produk }}</td></tr>
                                                        <tr><th>Kode Batch</th><td>{{ $dep->kode_produksi }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- PENGECEKAN --}}
                                                <h6 class="text-primary fw-bold mt-2"><i class="bi bi-check2-square me-1"></i> Pengecekan</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Panjang Varian Akhir (Cm)</th><td>{{ $dep->panjang_produk ?? '-' }}</td></tr>
                                                        <tr><th>Diameter Varian Akhir (Mm)</th><td>{{ $dep->diameter_produk ?? '-' }}</td></tr>
                                                        <tr><th>Airtrap</th><td>{{ $dep->airtrap ?? '-' }}</td></tr>
                                                        <tr><th>Lengket</th><td>{{ $dep->lengket ?? '-' }}</td></tr>
                                                        <tr><th>Sisa Adonan</th><td>{{ $dep->sisa_adonan ?? '-' }}</td></tr>
                                                        <tr><th>Kekuatan Seal</th><td>{{ $dep->kekuatan_seal ?? '-' }}</td></tr>
                                                        <tr><th>Print Kode Batch</th><td>{{ $dep->print_kode ?? '-' }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- PC KLEER --}}
                                                <h6 class="text-primary fw-bold mt-2"><i class="bi bi-droplet-half me-1"></i> PC Kleer</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Konsentrasi PC Kleer 1 (%)</th><td>{{ $dep->konsentrasi_pckleer ?? '-' }}</td></tr>
                                                        <tr><th>Suhu PC Kleer 1 (°C)</th><td>{{ $dep->suhu_pckleer_1 ?? '-' }}</td></tr>
                                                        <tr><th>Suhu PC Kleer 2 (°C)</th><td>{{ $dep->suhu_pckleer_2 ?? '-' }}</td></tr>
                                                        <tr><th>pH PC Kleer</th><td>{{ $dep->ph_pckleer ?? '-' }}</td></tr>
                                                        <tr><th>Kondisi Air PC Kleer</th><td>{{ $dep->kondisi_air_pckleer ?? '-' }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- POTTASIUM SORBATE --}}
                                                <h6 class="text-primary fw-bold mt-2"><i class="bi bi-flask me-1"></i> Pottasium Sorbate</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Konsentrasi Pottasium Sorbate (%)</th><td>{{ $dep->konsentrasi_pottasium ?? '-' }}</td></tr>
                                                        <tr><th>Suhu Pottasium Sorbate (°C)</th><td>{{ $dep->suhu_pottasium ?? '-' }}</td></tr>
                                                        <tr><th>pH Pottasium Sorbate</th><td>{{ $dep->ph_pottasium ?? '-' }}</td></tr>
                                                        <tr><th>Kondisi Air Pottasium Sorbate</th><td>{{ $dep->kondisi_pottasium ?? '-' }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- SUHU & SPEED --}}
                                                <h6 class="text-primary fw-bold mt-2"><i class="bi bi-speedometer2 me-1"></i> Suhu & Speed Conveyor</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Suhu Heater (°C)</th><td>{{ $dep->suhu_heater ?? '-' }}</td></tr>
                                                        <tr><th>Speed Conv. Drying 1</th><td>{{ $dep->speed_1 ?? '-' }}</td></tr>
                                                        <tr><th>Speed Conv. Drying 2</th><td>{{ $dep->speed_2 ?? '-' }}</td></tr>
                                                        <tr><th>Speed Conv. Drying 3</th><td>{{ $dep->speed_3 ?? '-' }}</td></tr>
                                                        <tr><th>Speed Conv. Drying 4</th><td>{{ $dep->speed_4 ?? '-' }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- CATATAN --}}
                                                @if($dep->catatan)
                                                <h6 class="text-primary fw-bold mt-2"><i class="bi bi-journal-text me-1"></i> Catatan</h6>
                                                <p>{{ $dep->catatan }}</p>
                                                @endif

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center align-middle">{{ $dep->username }}</td>
                            <td class="text-center align-middle">{{ $dep->nama_produksi }}</td>
                            
                            {{-- STATUS SPV --}}
                            <td class="text-center align-middle">
                                @if ($dep->status_spv == 0)
                                <span class="fw-bold text-secondary">Created</span>
                                @elseif ($dep->status_spv == 1)
                                <span class="fw-bold text-success">Verified</span>
                                @elseif ($dep->status_spv == 2)
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revisionModal{{ $dep->uuid }}" 
                                 class="text-danger fw-bold text-decoration-none" style="cursor: pointer;">Revision</a>

                                 {{-- Modal Revisi --}}
                                 <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Detail Revisi</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
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

                            {{-- ACTION & MODAL VERIFIKASI (Versi Server Gradient) --}}
                            <td class="text-center align-middle">
                                @can('can access verification button')
                                <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm mb-1" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                                    <i class="bi bi-shield-check me-1"></i> Verifikasi
                                </button>
                                @endcan

                                @can('can access edit button')
                                <a href="{{ route('washing.edit.form', $dep->uuid) }}" class="btn btn-warning btn-sm me-1 mb-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                @endcan

                                @can('can access update button')
                                <a href="{{ route('washing.update.form', $dep->uuid) }}" class="btn btn-info btn-sm me-1 mb-1">
                                    <i class="bi bi-pencil"></i> Update
                                </a>
                                @endcan

                                @can('can access delete button')
                                <form action="{{ route('washing.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                                @endcan

                                {{-- MODAL VERIFIKASI (Style Server: Gradient) --}}
                                <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                        <form action="{{ route('washing.verification.update', $dep->uuid) }}" method="POST">
                                            @csrf @method('PUT')
                                            
                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white" 
                                            style="background: linear-gradient(145deg, #7a1f12, #9E3419); box-shadow: 0 15px 40px rgba(0,0,0,0.5);">

                                            <div class="modal-header border-bottom border-light-subtle p-4" style="border-bottom-width: 3px !important;">
                                                <h5 class="modal-title fw-bolder fs-3 text-uppercase" id="verifyModalLabel{{ $dep->uuid }}" style="color: #00ffc4;">
                                                    <i class="bi bi-gear-fill me-2"></i> VERIFICATION
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body p-5">
                                                <p class="text-light mb-4 fs-6">
                                                    Pastikan data yang akan diverifikasi di check dengan teliti terlebih dahulu.
                                                </p>
                                                <div class="row g-4">
                                                    <div class="col-md-12">
                                                        <label for="status_spv_{{ $dep->uuid }}" class="form-label fw-bold mb-2 text-center d-block" style="color: #FFE5DE; font-size: 0.95rem;">
                                                            Pilih Status Verifikasi
                                                        </label>

                                                        <select name="status_spv" id="status_spv_{{ $dep->uuid }}" class="form-select form-select-lg fw-bold text-center mx-auto" 
                                                            style="background: linear-gradient(135deg, #fff1f0, #ffe5de); border: 2px solid #dc3545; border-radius: 12px; color: #dc3545; height: 55px; font-size: 1.1rem; box-shadow: 0 6px 12px rgba(0,0,0,0.1); width: 85%; transition: all 0.3s ease;" required>
                                                            <option value="1" {{ $dep->status_spv == 1 ? 'selected' : '' }} style="color: #198754; font-weight: 600;">✅ Verified (Disetujui)</option>
                                                            <option value="2" {{ $dep->status_spv == 2 ? 'selected' : '' }} style="color: #dc3545; font-weight: 600;">❌ Revision (Perlu Perbaikan)</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12 mt-3">
                                                        <label for="catatan_spv_{{ $dep->uuid }}" class="form-label fw-bold text-light mb-2">
                                                            Catatan Tambahan (Opsional)
                                                        </label>
                                                        <textarea name="catatan_spv" id="catatan_spv_{{ $dep->uuid }}" rows="4" class="form-control text-dark border-0 shadow-none" 
                                                            placeholder="Masukkan catatan, misalnya alasan revisi..." style="background-color: #FFE5DE; height: 120px;">{{ $dep->catatan_spv }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer justify-content-end p-4 border-top" style="background-color: #9E3419; border-color: #00ffc4 !important;">
                                                    <button type="button" class="btn btn-outline-light fw-bold rounded-pill px-4 me-2" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn fw-bolder rounded-pill px-5" style="background-color: #E39581; color: #2c3e50;">
                                                        <i class="bi bi-save-fill me-1"></i> SUBMIT
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- END MODAL VERIFY SERVER --}}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="19" class="text-center">Belum ada data pengecekan washing.</td></tr>
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
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>

<style>
    .table td, .table th { font-size: 0.85rem; white-space: nowrap; }
    .table td.text-start { text-align: left !important; }
    .container { padding-left: 2px !important; padding-right: 2px !important; }
</style>
@endsection