@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">

        {{-- ===================== ALERTS ===================== --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ===================== HEADER ===================== --}}
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <h4></i>Data Audit Pemeriksaan Mincing - Emulsifying - Aging</h4>
        </div>

        @unless(auth()->user()->hasRole('auditor'))
            <div class="card shadow-sm mb-3 w-100">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-check text-primary" style="font-size: 1.75rem; margin-right: 1rem;"></i>
                        <h6 class="mb-0">
                            <span class="text-primary" style="font-weight: 600;">Mode Audit</span>
                        </h6>
                    </div>

                    <form action="{{ route('toggle.mode.audit') }}" method="POST"
                        onsubmit="return confirm('Kembali ke mode Operasional?')">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-shield-check"></i> Mode Operasional
                        </button>
                    </form>
                </div>
            </div>
        @endunless

        {{-- ===================== FILTER ===================== --}}
        <form id="filterForm" method="GET" action="{{ route('mincing.audit') }}"
            class="mb-3 p-3 border rounded bg-white shadow-sm">
            <div class="row align-items-end">

                <div class="col-md-2 mb-2 mb-md-0">
                    <label for="filter_date" class="mb-1" style="font-weight: 600;">Pilih Tanggal</label>
                    <input type="date" name="date" id="filter_date" class="form-control"
                        value="{{ request('date') }}">
                </div>

                <div class="col-md-2 mb-2 mb-md-0">
                    <label for="filter_shift" class="mb-1" style="font-weight: 600;">Pilih Shift</label>
                    <select name="shift" id="filter_shift" class="form-control">
                        <option value="">Semua Shift</option>
                        <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                        <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                    </select>
                </div>

                <div class="col-md-2 mb-2 mb-md-0">
                    <label for="filter_kode_batch" class="mb-1" style="font-weight: 600;">Kode Batch</label>
                    <input type="text" name="kode_batch" id="filter_kode_batch" class="form-control"
                        value="{{ request('kode_batch') }}" placeholder="Kode Batch">
                </div>

                <div class="col-md-4 mb-2 mb-md-0">
                    <label for="search" class="mb-1" style="font-weight: 600;">Cari Data</label>
                    <input type="text" name="search" id="search" class="form-control"
                        value="{{ request('search') }}" placeholder="Cari Nama Produk / Kode Batch...">
                </div>

                <div class="col-md-2">
                    <a href="{{ route('mincing.audit') }}" class="btn btn-primary btn-block w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>

            </div>
        </form>

        {{-- Filter Scripts --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const search = document.getElementById('search');
                const date = document.getElementById('filter_date');
                const shift = document.getElementById('filter_shift');
                const kodeBatch = document.getElementById('filter_kode_batch');
                const form = document.getElementById('filterForm');

                let timer;
                function submitFilter() {
                    clearTimeout(timer);
                    timer = setTimeout(() => form.submit(), 500);
                }

                if (search) search.addEventListener('input', submitFilter);
                if (date) date.addEventListener('change', () => form.submit());
                if (shift) shift.addEventListener('change', () => form.submit());
                if (kodeBatch) kodeBatch.addEventListener('input', submitFilter);
            });
        </script>

        {{-- ===================== MAIN TABLE (READ-ONLY) ===================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Nama Varian</th>
                                <th>Kode Batch</th>
                                <th>Hasil Pemeriksaan</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>SPV</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                            @endphp
                            @forelse ($data as $dep)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}
                                    </td>
                                    <td class="text-center">{{ $dep->nama_produk }}</td>
                                    <td class="text-center">{{ $dep->kode_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        <a href="#" data-bs-toggle="modal"
                                            data-bs-target="#mincingModal{{ $dep->uuid }}"
                                            style="font-weight: bold; text-decoration: underline;">
                                            Result
                                        </a>

                                        @php
                                            $nonPremixItems = $dep->non_premix ?? [];
                                            $premixItems = $dep->premix ?? [];
                                            if (is_string($nonPremixItems)) $nonPremixItems = json_decode($nonPremixItems, true);
                                            if (is_string($premixItems)) $premixItems = json_decode($premixItems, true);
                                        @endphp

                                        <div class="modal fade text-start" id="mincingModal{{ $dep->uuid }}" tabindex="-1">
                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning text-dark">
                                                        <h5 class="modal-title fw-bold">Detail Pemeriksaan Mincing (Audit)</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body table-responsive">
                                                        <table class="table table-bordered table-striped table-sm text-center align-middle">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-start fw-bold w-25">Kode Batch</td>
                                                                    <td colspan="5" class="text-start">{{ $dep->kode_produksi ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start fw-bold">Preparation</td>
                                                                    <td colspan="2">{{ $dep->waktu_mulai ?? '-' }}</td>
                                                                    <td class="text-center">s/d</td>
                                                                    <td colspan="2">{{ $dep->waktu_selesai ?? '-' }}</td>
                                                                </tr>
                                                                <tr class="section-header bg-light fw-bold text-center">
                                                                    <td class="text-start">Bahan Baku & Tambahan (Non-Premix)</td>
                                                                    <td>Kode</td><td>(°C)</td><td>*pH</td><td>Kg</td><td>Sens</td>
                                                                </tr>
                                                                @if (!empty($nonPremixItems) && count($nonPremixItems) > 0)
                                                                    @foreach ($nonPremixItems as $bahan)
                                                                        <tr>
                                                                            <td class="text-start">{{ $bahan['nama_bahan'] ?? '-' }}</td>
                                                                            <td>{{ \App\Models\InspectionProductDetail::where('uuid', $bahan['inspection_uuid'] ?? null)->value('kode_batch') ?? '-' }}</td>
                                                                            <td>{{ $bahan['suhu_bahan'] ?? '-' }}</td>
                                                                            <td>{{ $bahan['ph_bahan'] ?? '-' }}</td>
                                                                            <td>{{ $bahan['berat_bahan'] ?? '-' }}</td>
                                                                            <td>{{ $bahan['sensori'] ?? '-' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr><td colspan="6" class="text-center text-muted">Belum ada data Non-Premix</td></tr>
                                                                @endif
                                                                <tr class="section-header bg-light fw-bold text-center">
                                                                    <td class="text-start">Premix</td>
                                                                    <td colspan="2">Kode</td><td colspan="2">Kg</td><td>Sens</td>
                                                                </tr>
                                                                @if (!empty($premixItems) && count($premixItems) > 0)
                                                                    @foreach ($premixItems as $p)
                                                                        <tr>
                                                                            <td class="text-start">{{ $p['nama_premix'] ?? '-' }}</td>
                                                                            <td colspan="2">{{ $p['kode_premix'] ?? '-' }}</td>
                                                                            <td colspan="2">{{ $p['berat_premix'] ?? '-' }}</td>
                                                                            <td>{{ $p['sensori_premix'] ?? '-' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr><td colspan="6" class="text-center text-muted">Belum ada data Premix</td></tr>
                                                                @endif
                                                                <tr>
                                                                    <td class="text-start fw-bold">Catatan</td>
                                                                    <td colspan="5" class="text-start">{{ $dep->catatan ?? '-' }}</td>
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
                                    </td>

                                    <td class="text-center">
                                        {{ \Illuminate\Support\Facades\DB::table('users')->where('username', $dep->username)->value('name') }}
                                    </td>

                                    <td class="text-center">
                                        @if ($dep->status_produksi == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_produksi == 1)
                                            <span class="fw-bold text-success">Checked</span>
                                        @elseif ($dep->status_produksi == 2)
                                            <span class="fw-bold text-danger">Recheck</span>
                                        @else
                                            <span class="text-muted">Belum Ditinjau</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <span class="fw-bold text-danger">Revision</span>
                                        @else
                                            <span class="text-muted">Belum Ditinjau</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada data audit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($data, 'links'))
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $data->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection