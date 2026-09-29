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

    {{-- Alert error --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif


    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-list-check"></i> Data Pemeriksaan Mincing - Emulsifying - Aging</h3>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('mincing.verification') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                    value="{{ request('search') }}" placeholder="Cari Nama Varian / Kode Batch...">
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
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Nama Varian</th>
                            <th>Kode Batch</th>
                            <th>Hasil Pemeriksaan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Verification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                        $no = ($data->currentPage() - 1) * $data->perPage() + 1; 
                        @endphp

                        @forelse ($data as $dep)
                        @php
                        $cooking = json_decode($dep->cooking, true);
                        @endphp
                        <tr>
                            <td class="text-center align-middle">{{ $no++ }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>
                            <td class="text-center align-middle">{{ $dep->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $dep->kode_produksi }}</td>
                            <td class="text-center align-middle">
                                @if($dep)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#mincingModal{{ $dep->uuid }}" style="font-weight: bold; text-decoration: underline;">
                                    Result
                                </a>

                                @php
                                $nonPremixItems = json_decode($dep->non_premix ?? '[]', true);
                                $premixItems    = json_decode($dep->premix ?? '[]', true);
                                @endphp

                                <div class="modal fade" id="mincingModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="mincingModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title" id="mincingModalLabel{{ $dep->uuid }}">Detail Pemeriksaan Mincing</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body table-responsive">
                                                <table class="table table-bordered table-striped table-sm text-center align-middle">
                                                    <tbody>
                                                        {{-- KODE PRODUKSI --}}
                                                        <tr>
                                                            <td class="text-left">Kode Batch</td>
                                                            <td colspan="5">{{ $dep->kode_produksi ?? '-' }}</td>
                                                        </tr>

                                                        {{-- PREPARATION --}}
                                                        <tr>
                                                            <td class="text-left">Preparation</td>
                                                            <td colspan="2">{{ $dep->waktu_mulai ?? '-' }}</td>
                                                            <td class="text-center">-</td>
                                                            <td colspan="2">{{ $dep->waktu_selesai ?? '-' }}</td>
                                                        </tr>

                                                        {{-- NON-PREMIX --}}
                                                        <tr class="section-header bg-light fw-bold">
                                                            <td class="text-left">Bahan Baku dan Bahan Tambahan (Non-Premix)</td>
                                                            <td>Kode</td>
                                                            <td>(°C)</td>
                                                            <td>*pH</td>
                                                            <td>Kg</td>
                                                            <td>Sens</td>
                                                        </tr>
                                                        @if(count($nonPremixItems) > 0)
                                                        @foreach($nonPremixItems as $bahan)
                                                        <tr>
                                                            <td>{{ $bahan['nama_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['kode_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['suhu_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['ph_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['berat_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['sensori'] ?? '-' }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @else
                                                        <tr><td colspan="6">-</td></tr>
                                                        @endif

                                                        {{-- PREMIX --}}
                                                        <tr class="section-header bg-light fw-bold">
                                                            <td class="text-left">Premix</td>
                                                            <td colspan="2">Kode</td>
                                                            <td colspan="2">Kg</td>
                                                            <td>Sens</td>
                                                        </tr>
                                                        @if(count($premixItems) > 0)
                                                        @foreach($premixItems as $p)
                                                        <tr>
                                                            <td>{{ $p['nama_premix'] ?? '-' }}</td>
                                                            <td colspan="2">{{ $p['kode_premix'] ?? '-' }}</td>
                                                            <td colspan="2">{{ $p['berat_premix'] ?? '-' }}</td>
                                                            <td>{{ $p['sensori_premix'] ?? '-' }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @else
                                                        <tr><td colspan="6">-</td></tr>
                                                        @endif

                                                        {{-- SUHU & WAKTU --}}
                                                        <tr>
                                                            <td class="text-left">Suhu (Sebelum Grinding)</td>
                                                            <td colspan="5">{{ $dep->suhu_sebelum_grinding ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Waktu Mixing Premix</td>
                                                            <td colspan="2">{{ $dep->waktu_mixing_premix_awal ?? '-' }}</td>
                                                            <td class="text-center">-</td>
                                                            <td colspan="2">{{ $dep->waktu_mixing_premix_akhir ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Waktu Bowl Cutter</td>
                                                            <td colspan="2">{{ $dep->waktu_bowl_cutter_awal ?? '-' }}</td>
                                                            <td class="text-center">-</td>
                                                            <td colspan="2">{{ $dep->waktu_bowl_cutter_akhir ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Waktu Aging Emulsi</td>
                                                            <td colspan="2">{{ $dep->waktu_aging_emulsi_awal ?? '-' }}</td>
                                                            <td class="text-center">-</td>
                                                            <td colspan="2">{{ $dep->waktu_aging_emulsi_akhir ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Suhu Akhir Emulsi Gel</td>
                                                            <td colspan="5">{{ $dep->suhu_akhir_emulsi_gel ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Waktu Mixing</td>
                                                            <td colspan="5">{{ $dep->waktu_mixing ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Suhu Akhir Mixing</td>
                                                            <td colspan="5">{{ $dep->suhu_akhir_mixing ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Suhu Akhir Emulsifying</td>
                                                            <td colspan="5">{{ $dep->suhu_akhir_emulsi ?? '-' }}</td>
                                                        </tr>

                                                        {{-- CATATAN --}}
                                                        <tr>
                                                            <td class="text-left">Catatan</td>
                                                            <td colspan="5">{{ $dep->catatan ?? '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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
                            <td class="text-center">
                                {{ \Illuminate\Support\Facades\DB::table('users')->where('username', $dep->username)->value('name') }}
                            </td>
                            <td class="text-center align-middle">{{ $dep->nama_produksi }}</td>
                            <td class="text-center align-middle">
                                @if ($dep->status_spv == 0)
                                <span class="fw-bold text-secondary">Created</span>
                                @elseif ($dep->status_spv == 1)
                                <span class="fw-bold text-success">Verified</span>
                                @elseif ($dep->status_spv == 2)
                                <!-- Link buka modal -->
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revisionModal{{ $dep->uuid }}" 
                                    class="text-danger fw-bold text-decoration-none" style="cursor: pointer;">Revision</a>
                                    <!-- Modal -->
                                    <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="revisionModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="revisionModalLabel{{ $dep->uuid }}">Detail Revisi</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
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
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                                        <i class="bi bi-shield-check me-1"></i> Verifikasi
                                    </button>
                                    <a href="{{ route('mincing.edit.form', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('mincing.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                                <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                        <form action="{{ route('mincing.verification.update', $dep->uuid) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white" 
                                            style="background: linear-gradient(145deg, #7a1f12, #9E3419); 
                                            box-shadow: 0 15px 40px rgba(0,0,0,0.5);">
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
                                                        <label for="status_spv_{{ $dep->uuid }}" class="form-label fw-bold mb-2 text-center d-block" 
                                                            style="color: #FFE5DE; font-size: 0.95rem;">
                                                            Pilih Status Verifikasi
                                                        </label>

                                                        <select 
                                                        name="status_spv" 
                                                        id="status_spv_{{ $dep->uuid }}" 
                                                        class="form-select form-select-lg fw-bold text-center mx-auto"
                                                        style="
                                                        background: linear-gradient(135deg, #fff1f0, #ffe5de);
                                                        border: 2px solid #dc3545;
                                                        border-radius: 12px;
                                                        color: #dc3545;
                                                        height: 55px;
                                                        font-size: 1.1rem;
                                                        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
                                                        width: 85%;
                                                        transition: all 0.3s ease;
                                                        "
                                                        required
                                                        >
                                                        <option value="1" {{ $dep->status_spv == 1 ? 'selected' : '' }} 
                                                            style="color: #198754; font-weight: 600;">✅ Verified (Disetujui)</option>
                                                            <option value="2" {{ $dep->status_spv == 2 ? 'selected' : '' }} 
                                                                style="color: #dc3545; font-weight: 600;">❌ Revision (Perlu Perbaikan)</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-12 mt-3">
                                                            <label for="catatan_spv_{{ $dep->uuid }}" class="form-label fw-bold text-light mb-2">
                                                                Catatan Tambahan (Opsional)
                                                            </label>
                                                            <textarea name="catatan_spv" id="catatan_spv_{{ $dep->uuid }}" rows="4" 
                                                                class="form-control text-dark border-0 shadow-none" 
                                                                placeholder="Masukkan catatan, misalnya alasan revisi..." 
                                                                style="background-color: #FFE5DE; height: 120px;">{{ $dep->catatan_spv }}</textarea>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer justify-content-end p-4 border-top" style="background-color: #9E3419; border-color: #00ffc4 !important;">
                                                        <button type="button" class="btn btn-outline-light fw-bold rounded-pill px-4 me-2" data-bs-dismiss="modal">
                                                            Batal
                                                        </button>
                                                        <button type="submit" class="btn fw-bolder rounded-pill px-5" style="background-color: #E39581; color: #2c3e50;">
                                                            <i class="bi bi-save-fill me-1"></i> SUBMIT
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="19" class="text-center">Belum ada data pengecekan mincing.</td>
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
    .text-success { color: green; font-weight: bold; }
    .text-danger { color: red; font-weight: bold; }
</style>
@endsection

