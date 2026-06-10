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
        <h2 class="h4">Pemeriksaan Stuffing Sosis Retort</h2>
        <div class="btn-group" role="group">
            @can('can access add button')
            <a href="{{ route('stuffing.create') }}" class="btn btn-success">
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
            <a href="{{ route('stuffing.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- Filter dan Live Search --}}
    <form id="filterForm" method="GET" action="{{ route('stuffing.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
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
                    value="{{ request('date') }}" placeholder="Tanggal Produksi">
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
                    value="{{ request('search') }}" placeholder="Cari Area / Varian / Mesin...">
                </div>
            </div>
            <div class="col-md-3 align-self-end">
                <a href="{{ route('stuffing.index') }}" class="btn btn-primary mb-2">
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
            const exportPdfBtn = document.getElementById('exportPdfBtn'); // Get Export Button

            let timer;

            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });

            date.addEventListener('change', () => form.submit());
            if(shift) shift.addEventListener('change', () => form.submit());

            // Handle PDF export button click
            if(exportPdfBtn){
                exportPdfBtn.addEventListener('click', function() {
                    const formData = new FormData(form);
                    // Pastikan route exportPdf sudah dibuat di web.php
                    const exportUrl = "{{ route('stuffing.exportPdf') }}?" + new URLSearchParams(formData).toString();
                    window.open(exportUrl, '_blank');
                });
            }
        });
    </script>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            {{-- Tambahkan table-responsive agar tabel tidak keluar border --}}
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Nama Varian</th>
                            <th>Kode Batch</th>
                            <th>Exp. Date</th>
                            <th>Kode Mesin</th>
                            <th>Jam Mulai</th>
                            <th>Pemeriksaan</th>
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
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>
                            <td class="text-center align-middle">{{ $dep->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $dep->mincing->kode_produksi ?? '-'}}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->exp_date)->format('d-m-Y') }}</td>
                            
                            {{-- Modal Kode Mesin --}}
                            <td class="text-center align-middle">
                                @if(is_array($dep->data_stuffing) && count($dep->data_stuffing) > 0)
                                <button class="btn btn-secondary btn-sm mb-2 shadow-sm" type="button" data-bs-toggle="modal" data-bs-target="#mesinModal{{ $dep->uuid }}">
                                    Mesin
                                </button>

                                <div class="modal fade" id="mesinModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-secondary text-white p-3">
                                                <h5 class="modal-title" style="font-size: 1rem;">Daftar Kode Mesin</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <ul class="list-group list-group-flush text-start">
                                                    @foreach($dep->data_stuffing as $index => $m)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center p-3" style="font-size: 0.9rem;">
                                                            <span class="text-muted fw-semibold">Stuffing #{{ $index + 1 }}</span>
                                                            {{-- Perbaikan Badge di sini --}}
                                                            <span class="badge bg-primary text-white px-3 py-2 rounded" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                                                {{ $m['kode_mesin'] ?? '-' }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="modal-footer p-2">
                                                <button type="button" class="btn btn-light btn-sm border" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Modal Jam Mulai --}}
                            <td class="text-center align-middle">
                                @if(is_array($dep->data_stuffing) && count($dep->data_stuffing) > 0)
                                <button class="btn btn-light border btn-sm mb-2" type="button" data-bs-toggle="modal" data-bs-target="#jamModal{{ $dep->uuid }}">
                                    Waktu
                                </button>

                                <div class="modal fade" id="jamModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="font-size: 1rem;">Daftar Jam Mulai</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-2">
                                                <ul class="list-group list-group-flush text-start">
                                                    @foreach($dep->data_stuffing as $index => $m)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                                                            Stuffing #{{ $index + 1 }}
                                                            <span class="badge bg-light text-dark border rounded-pill">{{ $m['jam_mulai'] ?? '-' }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="modal-footer p-1">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span>-</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                @if($dep->id)
                                <button class="btn btn-info btn-sm mb-2" type="button"
                                data-bs-toggle="modal" data-bs-target="#stuffingModal{{ $dep->uuid }}">
                                    Details
                                </button>

                                {{-- Modal Detail Pemeriksaan --}}
                                <div class="modal fade" id="stuffingModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Pemeriksaan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive p-2">
                                                    @if(is_array($dep->data_stuffing))
                                                        @foreach($dep->data_stuffing as $index => $item)
                                                            <div class="border p-2 mb-2 bg-light rounded shadow-sm">
                                                                <strong class="text-primary d-block mb-1 text-start" style="font-size: 0.8rem;">
                                                                    Stuffing #{{ $index + 1 }} (Mesin: {{ $item['kode_mesin'] ?? '-' }})
                                                                </strong>
                                                                <table class="table table-bordered table-striped table-sm text-center align-middle mb-0">
                                                                    <tbody>
                                                                        @php
                                                                        $fields = [
                                                                            ['type'=>'title', 'label'=>'Parameter Adonan'],
                                                                            ['type'=>'field', 'label'=>'Suhu (°C)', 'key'=>'suhu'],
                                                                            ['type'=>'field', 'label'=>'Sensori', 'key'=>'sensori'],
                                                                            ['type'=>'title', 'label'=>'Parameter Stuffing'],
                                                                            ['type'=>'field', 'label'=>'Kecepatan Stuffing', 'key'=>'kecepatan_stuffing'],
                                                                            ['type'=>'field', 'label'=>'Panjang/pcs (cm)', 'key'=>'panjang_pcs'],
                                                                            ['type'=>'field', 'label'=>'Berat/pcs (gr)', 'key'=>'berat_pcs'],
                                                                            ['type'=>'field', 'label'=>'Kebersihan Seal', 'key'=>'kebersihan_seal'],
                                                                            ['type'=>'field', 'label'=>'Kekuatan Seal', 'key'=>'kekuatan_seal'],
                                                                            ['type'=>'field', 'label'=>'Diameter Klip (mm)', 'key'=>'diameter_klip'],
                                                                            ['type'=>'field', 'label'=>'Print Kode', 'key'=>'print_kode'],
                                                                            ['type'=>'field', 'label'=>'Lebar Cassing (mm)', 'key'=>'lebar_cassing'],
                                                                            ['type'=>'field', 'label'=>'Catatan', 'key'=>'catatan'],
                                                                        ];
                                                                        @endphp

                                                                        @foreach($fields as $f)
                                                                            @if($f['type'] === 'title')
                                                                                <tr class="table-secondary">
                                                                                    <td class="text-start fw-bold" colspan="2" style="font-size: 0.75rem;">{{ $f['label'] }}</td>
                                                                                </tr>
                                                                            @else
                                                                                <tr>
                                                                                    <td class="text-start" style="font-size: 0.75rem;">{{ $f['label'] }}</td>
                                                                                    @php
                                                                                    $value = $item[$f['key']] ?? null;
                                                                                    $display = in_array($f['key'], ['sensori','kebersihan_seal','kekuatan_seal','print_kode'])
                                                                                        ? (!empty($value) && $value === 'OK' ? '✔' : ($value === 'Tidak OK' ? '❌' : '-'))
                                                                                        : ($value ?? '-');
                                                                                    @endphp
                                                                                    <td style="font-size: 0.75rem;">{{ $display }}</td>
                                                                                </tr>
                                                                            @endif
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
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
                                <span class="fw-bold text-secondary">Created</span>
                                @elseif ($dep->status_spv == 1)
                                <span class="fw-bold text-success">Verified</span>
                                @elseif ($dep->status_spv == 2)
                                <a href="javascript:void(0);" data-bs-toggle="modal"
                                data-bs-target="#revisionModal{{ $dep->uuid }}"
                                class="text-danger fw-bold text-decoration-none"
                                style="cursor: pointer;">Revision</a>

                                {{-- Modal Revision --}}
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
                            <a href="{{ route('stuffing.edit.form', $dep->uuid) }}"
                                class="btn btn-warning btn-sm me-1 mb-1">
                                <i class="bi bi-pencil-square"></i> Edit Data
                            </a>
                            @endcan
                            @can('can access update button')
                            <a href="{{ route('stuffing.update.form', $dep->uuid) }}"
                                class="btn btn-info btn-sm me-1 mb-1">
                                <i class="bi bi-pencil"></i> Update
                            </a>
                            @endcan
                            @can('can access delete button')
                            <form action="{{ route('stuffing.destroy', $dep->uuid) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mb-1"
                                onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                        @endcan
                        {{-- Modal Verify (sama seperti mincing) --}}
                        <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-md">
                                <form action="{{ route('stuffing.verification.update', $dep->uuid) }}"
                                    method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white"
                                    style="background: linear-gradient(145deg, #7a1f12, #9E3419);">
                                    <div class="modal-header border-bottom border-light-subtle p-4">
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
                                            style="color: #FFE5DE;">Pilih Status Verifikasi</label>
                                            <select name="status_spv"
                                            class="form-select form-select-lg fw-bold text-center mx-auto"
                                            required
                                            style="background: linear-gradient(135deg, #fff1f0, #ffe5de); border: 2px solid #dc3545; color: #dc3545; height: 55px;">
                                            <option value="1" {{ $dep->status_spv == 1 ? 'selected'
                                                : '' }} style="color: #198754;">✅ Verified
                                            (Disetujui)</option>
                                            <option value="2" {{ $dep->status_spv == 2 ? 'selected'
                                                : '' }} style="color: #dc3545;">❌ Revision (Perlu
                                            Perbaikan)</option>
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
                            style="background-color: #E39581; color: #2c3e50;"><i
                            class="bi bi-save-fill me-1"></i> SUBMIT</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="11" class="text-center align-middle">Belum ada data stuffing.</td>
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