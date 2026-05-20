@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    {{-- Helper Show Array --}}
    @once
    @php
    function showValue($val) {
        if (is_array($val)) {
            return collect($val)
            ->map(fn($v) => "<span class='badge bg-gradient bg-light text-dark border border-secondary me-1 mb-1'>$v</span>")
            ->implode('');
        }
        return e($val ?? '-');
    }
    @endphp
    @endonce

    {{-- Alert --}}
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

    {{-- HEADER --}}
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4"> Pengecekan Pemasakan</h2>
        <div class="btn-group">
            @can('can access add button')
            <a href="{{ route('pemasakan.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            @endcan
            @can('can access export')
            <button type="button" class="btn btn-danger" id="exportPdfBtn">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </button>
            @endcan
            @can('can access recycle')
            <a href="{{ route('pemasakan.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- FILTER --}}
    <form id="filterForm" method="GET" action="{{ route('pemasakan.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
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
                <a href="{{ route('pemasakan.index') }}" class="btn btn-primary mb-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
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
            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });

            date.addEventListener('change', () => form.submit());
            if(shift) shift.addEventListener('change', () => form.submit());

            if(exportPdfBtn){
                exportPdfBtn.addEventListener('click', function() {
                    const formData = new FormData(form);
                    const exportUrl = "{{ route('pemasakan.exportPdf') }}?" + new URLSearchParams(formData).toString();
                    window.open(exportUrl, '_blank');
                });
            }
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
                            <th>Kode Batch</th>
                            <th>Jumlah Tray</th>
                            <th>No. Chamber</th>
                            <th>Berat Varian (Gram)</th>
                            <th>Suhu Varian (°C)</th>
                            <th>Total Reject (Kg)</th>
                            <th>Pengecekan</th>
                            <th>QC</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                        @endphp

                        @forelse ($data as $dep)
                        @php
                        // FIX: Tidak perlu json_decode karena $casts di model sudah handle menjadi array
                        $cooking = $dep->cooking ?? [];
                        @endphp

                        <tr>
                            <td class="text-center align-middle">{{ $no++ }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | {{ $dep->shift }}</td>   
                            <td class="text-center align-middle">{{ $dep->nama_produk }}</td>
                            <td class="text-center align-middle">
                                @if(is_array($dep->kode_produksi))
                                @foreach ($dep->kode_produksi as $uuid)
                                {{ $stuffingData[$uuid]->kode_produksi ?? 'Tidak ditemukan' }}
                                <br>
                                @endforeach
                                @else
                                -
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                @if(is_array($dep->jumlah_tray))
                                @foreach ($dep->jumlah_tray as $tray)
                                {{ $tray }} <br>
                                @endforeach
                                @else
                                -
                                @endif
                            </td>

                            <td class="text-center align-middle">{{ $dep->no_chamber }}</td>
                            <td class="text-center align-middle">{{ $dep->berat_produk }}</td>
                            <td class="text-center align-middle">{{ $dep->suhu_produk }}</td>
                            <td class="text-center align-middle">{{ $dep->total_reject }}</td>

                            <td class="text-center align-middle">
                                @if(!empty($cooking))
                                <a href="#" class="fw-bold text-decoration-underline text-primary"
                                data-bs-toggle="modal" data-bs-target="#cookingModal{{ $dep->uuid }}">
                                Result
                                </a>

                                <div class="modal fade" id="cookingModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                            <div class="modal-header bg-primary bg-gradient text-white">
                                                <h5 class="modal-title fw-semibold"><i class="bi bi-fire me-2"></i> Detail Proses Pemasakan</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 bg-light-subtle">
                                                @php
                                                $sections = [
                                                '2. Tekanan & Suhu Awal' => ['Tekanan Angin' => 'tekanan_angin', 'Tekanan Steam' => 'tekanan_steam', 'Tekanan Air' => 'tekanan_air'],
                                                '3. Pemanasan Awal' => ['Suhu Air Awal' => 'suhu_air_awal', 'Tekanan Awal' => 'tekanan_awal', 'Waktu Mulai' => 'waktu_mulai_awal', 'Waktu Selesai' => 'waktu_selesai_awal'],
                                                '4. Proses Pemanasan' => ['Suhu Air Proses' => 'suhu_air_proses', 'Tekanan Proses' => 'tekanan_proses', 'Waktu Mulai' => 'waktu_mulai_proses', 'Waktu Selesai' => 'waktu_selesai_proses'],
                                                '5. Sterilisasi' => ['Suhu Air Sterilisasi' => 'suhu_air_sterilisasi', 'Thermometer Retort' => 'thermometer_retort', 'Tekanan Sterilisasi' => 'tekanan_sterilisasi'],
                                                '10. Hasil Pemasakan' => ['Suhu Varian Akhir' => 'suhu_produk_akhir', 'Panjang' => 'panjang', 'Diameter' => 'diameter', 'Rasa' => 'rasa', 'Warna' => 'warna', 'Texture' => 'texture']
                                                ];
                                                @endphp

                                                @foreach($sections as $title => $rows)
                                                <div class="mb-3">
                                                    <h6 class="fw-bold text-primary">{{ $title }}</h6>
                                                    <div class="table-responsive shadow-sm rounded">
                                                        <table class="table table-bordered table-sm align-middle text-center mb-0 bg-white">
                                                            <tbody>
                                                                @foreach($rows as $label => $key)
                                                                <tr>
                                                                    <td class="fw-semibold text-start ps-3 w-50">{{ $label }}</td>
                                                                    <td class="text-start ps-3">{!! showValue($cooking[$key] ?? '-') !!}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
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

                            <td class="text-center align-middle">{{ $dep->username }}</td>
                            <td class="text-center align-middle">
                                @if ($dep->status_spv == 0) <span class="fw-bold text-secondary">Created</span>
                                @elseif ($dep->status_spv == 1) <span class="fw-bold text-success">Verified</span>
                                @elseif ($dep->status_spv == 2) <span class="fw-bold text-danger">Revisi</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                @can('can access verification button')
                                <button class="btn btn-primary btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}"><i class="bi bi-shield-check"></i> Verifikasi</button>
                                @endcan

                                @can('can access edit button')
                                <a href="{{ route('pemasakan.edit.form', $dep->uuid) }}" class="btn btn-warning btn-sm mb-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                @endcan

                                @can('can access update button')
                                <a href="{{ route('pemasakan.update.form', $dep->uuid) }}" class="btn btn-info btn-sm mb-1"><i class="bi bi-pencil"></i> Update</a>
                                @endcan

                                @can('can access delete button')
                                <form action="{{ route('pemasakan.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm mb-1" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan

                                {{-- MODAL VERIFIKASI --}}
                                <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                        <form action="{{ route('pemasakan.verification.update', $dep->uuid) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white" style="background: linear-gradient(145deg, #7a1f12, #9E3419);">
                                                <div class="modal-header border-bottom border-light-subtle p-4">
                                                    <h5 class="modal-title fw-bolder fs-3 text-uppercase" style="color: #00ffc4;"><i class="bi bi-gear-fill me-2"></i> VERIFICATION</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-5">
                                                    <p class="text-light mb-4 fs-6">Pastikan data yang akan diverifikasi di check dengan teliti terlebih dahulu.</p>
                                                    <div class="row g-4">
                                                        <div class="col-md-12">
                                                            <label class="form-label fw-bold mb-2 text-center d-block" style="color: #FFE5DE; font-size: 0.95rem;">Pilih Status Verifikasi</label>
                                                            <select name="status_spv" class="form-select form-select-lg fw-bold text-center mx-auto" style="background: linear-gradient(135deg, #fff1f0, #ffe5de); border: 2px solid #dc3545; border-radius: 12px; color: #dc3545; height: 55px; width: 85%;" required>
                                                                <option value="1" {{ $dep->status_spv == 1 ? 'selected' : '' }}>✅ Verified (Disetujui)</option>
                                                                <option value="2" {{ $dep->status_spv == 2 ? 'selected' : '' }}>❌ Revision (Perlu Perbaikan)</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <label class="form-label fw-bold text-light mb-2">Catatan Tambahan (Opsional)</label>
                                                            <textarea name="catatan_spv" rows="4" class="form-control text-dark border-0 shadow-none" style="background-color: #FFE5DE; height: 120px;">{{ $dep->catatan_spv }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer justify-content-end p-4 border-top" style="background-color: #9E3419;">
                                                    <button type="button" class="btn btn-outline-light fw-bold rounded-pill px-4 me-2" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn fw-bolder rounded-pill px-5" style="background-color: #E39581; color: #2c3e50;">SUBMIT</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="14" class="text-center">Belum ada data.</td></tr>
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
    .container { padding-left: 2px !important; padding-right: 2px !important; }
</style>
@endsection