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
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Data Cleaning Magnet Trap</h2>
        <div>
            @can('can access add button')
            <a href="{{ route('checklistmagnettrap.create') }}" class="btn btn-success me-2">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            @endcan
            @can('can access export')
            <a href="{{ route('checklistmagnettrap.exportPdf', ['date' => request('date')]) }}" target="_blank" class="btn btn-primary">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            @endcan
            @can('can access recycle')
            <a href="{{ route('checklistmagnettrap.recyclebin') }}" class="btn btn-secondary">
                <i class="bi bi-trash"></i> Recycle Bin
            </a>
            @endcan
        </div>
    </div>

    {{-- Filter dan Live Search --}}
    <form id="filterForm" method="GET" action="{{ route('checklistmagnettrap.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-3 border rounded bg-white shadow-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-1">Pilih Tanggal</div>
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>
                    <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                    value="{{ request('date') }}" placeholder="Tanggal">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-1">Cari Nama Varian</div>
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" id="search" class="form-control border-start-0"
                    value="{{ request('search') }}" placeholder="Cari Nama Produk / Kode Batch...">
                </div>
            </div>
        </div>
    </form>

    {{-- Script Auto Submit --}}
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

    {{-- Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            {{-- Tabel Data --}}
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Nama Varian</th>
                            <th>Kode Batch</th>
                            <th>Tanggal | Pukul</th>
                            <th>Jml Temuan</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Produksi</th>
                            <th>Engineer</th>
                            <th>Status SPV</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage()
                            }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->mincing->kode_produksi ?? '-' }}</td>
                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }} <br>
                                <span class="text-muted small">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i')
                                }}</span>
                            </td>
                            <td class="text-center align-middle">{{ $item->jumlah_temuan }}</td>
                            <td class="text-center align-middle">
                                @if($item->status == 'v')
                                <span class="fw-bold text-success">
                                    <i class="bi bi-check-circle-fill"></i> OK
                                </span>
                                @else
                                <span class="fw-bold text-danger">
                                    <i class="bi bi-x-circle-fill"></i> NOT OK
                                </span>
                                @endif
                            </td>
                            <td class="text-center align-middle">{{ Str::limit($item->keterangan ?? '-', 35) }}</td>
                            <td class="text-center align-middle">{{ $item->produksi->name ?? $item->produksi_id }}</td>
                            <td class="text-center align-middle">{{ $item->engineer->name ?? $item->engineer_id }}</td>
                            <td class="text-center">
                                @if($item->status_spv == 1)
                                <span class="badge-status status-verified"><i
                                    class="fas fa-check-circle me-1"></i>Verified</span>
                                    @elseif($item->status_spv == 2)
                                    <span class="badge-status status-revision"><i
                                        class="fas fa-exclamation-circle me-1"></i>Revision</span>
                                        @else
                                        <span class="badge-status status-pending"><i
                                            class="fas fa-clock me-1"></i>Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                                @can('can access verification button')
                                                <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm mx-1"
                                                data-bs-toggle="modal" data-bs-target="#verifyModal{{ $item->uuid }}">
                                                <i class="bi bi-shield-check me-1"></i> Verifikasi
                                            </button>
                                            @endcan
                                            @can('can access edit button')
                                            <a href="{{ route('checklistmagnettrap.edit', $item->id) }}"
                                                class="btn btn-warning btn-sm mx-1">
                                                <i class="bi bi-pencil-square"></i> Edit Data
                                            </a>
                                            @endcan
                                            @can('can access update button')
                                            <a href="{{ route('checklistmagnettrap.showUpdateForm', $item) }}"
                                            class="btn btn-info btn-sm mx-1">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                        @endcan
                                        @can('can access delete button')
                                        <form action="{{ route('checklistmagnettrap.destroy', $item->uuid) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm mx-1">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">Belum ada data cleaning magnet trap.</td>
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
    @foreach ($data as $item)
    <div class="modal fade modal-verification" id="verifyModal{{ $item->uuid }}" tabindex="-1"
        aria-labelledby="verifyModalLabel{{ $item->uuid }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('checklistmagnettrap.verify', $item->uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bolder fs-4 text-uppercase" id="verifyModalLabel{{ $item->uuid }}"><i
                            class="bi bi-gear-fill me-2"></i>Verification</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-light mb-4">Pastikan data yang akan diverifikasi di-check dengan teliti terlebih
                            dahulu.</p>

                            <div class="mb-4">
                                <label for="status_spv_{{ $item->uuid }}"
                                    class="form-label fw-bold d-block text-center mb-2">Pilih Status Verifikasi</label>
                                    <select name="status_spv" id="status_spv_{{ $item->uuid }}"
                                        class="form-select form-select-lg fw-bold text-center mx-auto form-select-custom"
                                        style="width: 85%;" required>
                                        <option value="1" {{ old('status_spv', $item->status_spv) != 2 ? 'selected' : '' }}
                                            style="color: #198754;">✅ Verified (Disetujui)</option>
                                            <option value="2" {{ old('status_spv', $item->status_spv) == 2 ? 'selected' : '' }}
                                                style="color: #dc3545;">❌ Revision (Perlu Perbaikan)</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="catatan_spv_{{ $item->uuid }}" class="form-label fw-bold">Catatan Verifikasi
                                            (Opsional)</label>
                                            <textarea name="catatan_spv" id="catatan_spv_{{ $item->uuid }}" rows="4"
                                                class="form-control shadow-none textarea-custom"
                                                placeholder="Tuliskan catatan jika diperlukan...">{{ old('catatan_spv', $item->catatan_spv) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-end">
                                            <button type="button" class="btn btn-outline-light rounded-pill px-4 fw-bold"
                                            data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn rounded-pill px-5 fw-bolder"
                                            style="background-color: var(--accent-color-1); color: var(--text-on-light);"><i
                                            class="bi bi-save-fill me-1"></i> SUBMIT</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
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

                        {{-- Style Tambahan --}}
                        <style>
                            /* Styling Font Tabel */
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

                            /* Styling Card Custom */
                            .card-custom {
                                border: none;
                                border-radius: 0.75rem;
                                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                            }

                            :root {
                                --primary-brand-dark: #7a1f12;
                                --primary-brand-light: #9E3419;
                                --accent-color-1: #E39581;
                                --accent-color-2: #FFE5DE;
                                --text-on-dark: #FFFFFF;
                                --text-on-light: #2c3e50;
                            }

                            body {
                                background-color: #f8f9fa;
                            }

                            .badge-status {
                                padding: 0.5em 0.75em;
                                font-size: 0.75rem;
                                font-weight: 600;
                                border-radius: 50rem;
                            }

                            .status-pending {
                                background-color: rgba(108, 117, 125, 0.1);
                                color: #6c757d;
                            }

                            .status-verified {
                                background-color: rgba(25, 135, 84, 0.1);
                                color: #198754;
                            }

                            .status-revision {
                                background-color: rgba(220, 53, 69, 0.1);
                                color: #DC3545;
                            }

                            .modal-verification .modal-content {
                                border: none;
                                border-radius: 1rem;
                                background: linear-gradient(145deg, var(--primary-brand-dark), var(--primary-brand-light));
                                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
                                color: var(--text-on-dark);
                                overflow: hidden;
                            }

                            .modal-verification .modal-header {
                                border-bottom: 2px solid rgba(255, 255, 255, 0.2);
                                padding: 1.5rem;
                            }

                            .modal-verification .modal-body {
                                padding: 2rem;
                            }

                            .modal-verification .modal-footer {
                                background-color: var(--primary-brand-light);
                                border-top: 1px solid rgba(255, 255, 255, 0.2);
                                padding: 1rem 1.5rem;
                            }

                            .modal-verification .form-select-custom {
                                background: linear-gradient(135deg, #fff1f0, var(--accent-color-2));
                                border: 2px solid #dc3545;
                                border-radius: 12px;
                                color: #7a1f12;
                                font-size: 1.1rem;
                                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
                                transition: all 0.3s ease;
                            }

                            .modal-verification .textarea-custom {
                                background-color: var(--accent-color-2);
                                color: var(--text-on-light);
                                border: none;
                                border-radius: 0.5rem;
                                height: 120px;
                            }

                            /* PERUBAHAN UTAMA: Style padding .container-fluid dihapus agar kembali ke default (memiliki jarak) */
                        </style>
                        @endsection
