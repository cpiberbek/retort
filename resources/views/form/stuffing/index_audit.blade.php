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
        <h2 class="h4">Pemeriksaan Stuffing Sosis Retort - Audit</h2>
    </div>

    {{-- Filter --}}
    <form id="filterForm" method="GET" action="{{ route('stuffing.audit') }}"
        class="mb-3 p-3 border rounded bg-white shadow-sm">

        <div class="row g-3 align-items-end">

            <div class="col-lg-2 col-md-6">
                <label class="form-label mb-1">Pilih Tanggal</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                    </div>

                    <input type="date"
                        name="date"
                        id="filter_date"
                        class="form-control border-start-0"
                        value="{{ request('date') }}">
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label mb-1">Pilih Shift</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-hourglass-split text-muted"></i>
                        </span>
                    </div>

                    <select name="shift" id="filter_shift" class="form-select border-start-0">
                        <option value="">Semua Shift</option>
                        <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                        <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label mb-1">Cari Kode Batch</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>

                    <input type="text"
                        name="kode_batch"
                        id="kode_batch"
                        class="form-control border-start-0"
                        value="{{ request('kode_batch') }}"
                        placeholder="Cari Kode Batch...">
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label mb-1">Cari Data</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>

                    <input type="text"
                        name="search"
                        id="search"
                        class="form-control border-start-0"
                        value="{{ request('search') }}"
                        placeholder="Cari Area / Varian / Mesin...">
                </div>
            </div>

            <div class="col-lg-2 col-md-12">
                <a href="{{ route('stuffing.audit') }}" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>

        </div>
    </form>

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
                            <th>Exp. Date</th>
                            <th>Kode Mesin</th>
                            <th>Jam Mulai</th>
                            <th>Pemeriksaan</th>
                            <th>QC</th>
                            <th>SPV</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php
                            $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                        @endphp

                        @forelse ($data as $dep)

                        <tr>

                            <td class="text-center">
                                {{ $no++ }}
                            </td>

                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                | Shift: {{ $dep->shift }}
                            </td>

                            <td class="text-center align-middle">
                                {{ $dep->nama_produk }}
                            </td>

                            <td class="text-center align-middle">
                                {{ $dep->mincing->kode_produksi ?? '-' }}
                            </td>

                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($dep->exp_date)->format('d-m-Y') }}
                            </td>


                            {{-- Modal Kode Mesin --}}
                            <td class="text-center align-middle">

                                @if(is_array($dep->data_stuffing) && count($dep->data_stuffing) > 0)

                                    <button
                                        class="btn btn-secondary btn-sm mb-2 shadow-sm"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#mesinModal{{ $dep->uuid }}">
                                        Mesin
                                    </button>

                                    <div
                                        class="modal fade"
                                        id="mesinModal{{ $dep->uuid }}"
                                        tabindex="-1"
                                        aria-hidden="true">

                                        <div class="modal-dialog modal-sm modal-dialog-centered">

                                            <div class="modal-content">

                                                <div class="modal-header bg-secondary text-white p-3">

                                                    <h5
                                                        class="modal-title"
                                                        style="font-size: 1rem;">
                                                        Daftar Kode Mesin
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                    </button>

                                                </div>

                                                <div class="modal-body p-0">

                                                    <ul class="list-group list-group-flush text-start">

                                                        @foreach($dep->data_stuffing as $index => $m)

                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center p-3"
                                                                style="font-size: 0.9rem;">

                                                                <span class="text-muted fw-semibold">
                                                                    Stuffing #{{ $index + 1 }}
                                                                </span>

                                                                <span
                                                                    class="badge bg-primary text-white px-3 py-2 rounded"
                                                                    style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                                                    {{ $m['kode_mesin'] ?? '-' }}
                                                                </span>

                                                            </li>

                                                        @endforeach

                                                    </ul>

                                                </div>

                                                <div class="modal-footer p-2">

                                                    <button
                                                        type="button"
                                                        class="btn btn-light btn-sm border"
                                                        data-bs-dismiss="modal">
                                                        Tutup
                                                    </button>

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

                                    <button
                                        class="btn btn-light border btn-sm mb-2"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#jamModal{{ $dep->uuid }}">
                                        Waktu
                                    </button>

                                    <div
                                        class="modal fade"
                                        id="jamModal{{ $dep->uuid }}"
                                        tabindex="-1"
                                        aria-hidden="true">

                                        <div class="modal-dialog modal-sm modal-dialog-centered">

                                            <div class="modal-content">

                                                <div class="modal-header">

                                                    <h5
                                                        class="modal-title"
                                                        style="font-size: 1rem;">
                                                        Daftar Jam Mulai
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                    </button>

                                                </div>

                                                <div class="modal-body p-2">

                                                    <ul class="list-group list-group-flush text-start">

                                                        @foreach($dep->data_stuffing as $index => $m)

                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center"
                                                                style="font-size: 0.85rem;">

                                                                Stuffing #{{ $index + 1 }}

                                                                <span class="badge bg-light text-dark border rounded-pill">
                                                                    {{ $m['jam_mulai'] ?? '-' }}
                                                                </span>

                                                            </li>

                                                        @endforeach

                                                    </ul>

                                                </div>

                                                <div class="modal-footer p-1">

                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">
                                                        Tutup
                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @else

                                    <span>-</span>

                                @endif

                            </td>


                            {{-- Modal Detail Pemeriksaan --}}
                            <td class="text-center align-middle">

                                @if($dep->id)

                                    <button
                                        class="btn btn-info btn-sm mb-2"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#stuffingModal{{ $dep->uuid }}">
                                        Details
                                    </button>

                                    <div
                                        class="modal fade"
                                        id="stuffingModal{{ $dep->uuid }}"
                                        tabindex="-1"
                                        aria-hidden="true">

                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">

                                            <div class="modal-content">

                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        Detail Pemeriksaan
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                    </button>

                                                </div>

                                                <div class="modal-body">

                                                    <div class="table-responsive p-2">

                                                        @if(is_array($dep->data_stuffing))

                                                            @foreach($dep->data_stuffing as $index => $item)

                                                                <div
                                                                    class="border p-2 mb-2 bg-light rounded shadow-sm">

                                                                    <strong
                                                                        class="text-primary d-block mb-1 text-start"
                                                                        style="font-size: 0.8rem;">
                                                                        Stuffing #{{ $index + 1 }}
                                                                        (Mesin: {{ $item['kode_mesin'] ?? '-' }})
                                                                    </strong>

                                                                    <table
                                                                        class="table table-bordered table-striped table-sm text-center align-middle mb-0">

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

                                                                                        <td
                                                                                            class="text-start fw-bold"
                                                                                            colspan="2"
                                                                                            style="font-size: 0.75rem;">
                                                                                            {{ $f['label'] }}
                                                                                        </td>

                                                                                    </tr>

                                                                                @else

                                                                                    <tr>

                                                                                        <td
                                                                                            class="text-start"
                                                                                            style="font-size: 0.75rem;">
                                                                                            {{ $f['label'] }}
                                                                                        </td>

                                                                                        @php

                                                                                            $value = $item[$f['key']] ?? null;

                                                                                            $display = in_array(
                                                                                                $f['key'],
                                                                                                [
                                                                                                    'sensori',
                                                                                                    'kebersihan_seal',
                                                                                                    'kekuatan_seal',
                                                                                                    'print_kode'
                                                                                                ]
                                                                                            )
                                                                                                ? (
                                                                                                    !empty($value) && $value === 'OK'
                                                                                                        ? '✔'
                                                                                                        : (
                                                                                                            $value === 'Tidak OK'
                                                                                                                ? '❌'
                                                                                                                : '-'
                                                                                                        )
                                                                                                )
                                                                                                : ($value ?? '-');

                                                                                        @endphp

                                                                                        <td style="font-size: 0.75rem;">
                                                                                            {{ $display }}
                                                                                        </td>

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

                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">
                                                        Tutup
                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @else

                                    <span>-</span>

                                @endif

                            </td>


                            {{-- QC --}}
                            <td class="text-center">
                                {{ \Illuminate\Support\Facades\DB::table('users')->where('username', $dep->username)->value('name') }}
                            </td>


                            {{-- SPV --}}
                            <td class="text-center align-middle">

                                @if ($dep->status_spv == 0)

                                    <span class="fw-bold text-secondary">
                                        Created
                                    </span>

                                @elseif ($dep->status_spv == 1)

                                    <span class="fw-bold text-success">
                                        Verified
                                    </span>

                                @elseif ($dep->status_spv == 2)

                                    <a
                                        href="javascript:void(0);"
                                        data-bs-toggle="modal"
                                        data-bs-target="#revisionModal{{ $dep->uuid }}"
                                        class="text-danger fw-bold text-decoration-none"
                                        style="cursor: pointer;">
                                        Revision
                                    </a>

                                    {{-- Modal Revision --}}
                                    <div
                                        class="modal fade"
                                        id="revisionModal{{ $dep->uuid }}"
                                        tabindex="-1"
                                        aria-hidden="true">

                                        <div class="modal-dialog modal-dialog-centered">

                                            <div class="modal-content">

                                                <div class="modal-header bg-danger text-white">

                                                    <h5 class="modal-title">
                                                        Detail Revisi
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal">
                                                    </button>

                                                </div>

                                                <div class="modal-body text-start">

                                                    <ul class="list-unstyled mb-0">

                                                        <li>
                                                            <strong>Status:</strong>
                                                            Revision
                                                        </li>

                                                        <li>
                                                            <strong>Catatan:</strong>
                                                            {{ $dep->catatan_spv ?? '-' }}
                                                        </li>

                                                    </ul>

                                                </div>

                                                <div class="modal-footer">

                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">
                                                        Tutup
                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="10" class="text-center align-middle">
                                Belum ada data stuffing.
                            </td>
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

        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>


<script>
    document.addEventListener('DOMContentLoaded', () => {

        const search = document.getElementById('search');
        const kodeBatch = document.getElementById('kode_batch');
        const date = document.getElementById('filter_date');
        const shift = document.getElementById('filter_shift');
        const form = document.getElementById('filterForm');

        let timer;

        function submitFilter() {
            clearTimeout(timer);

            timer = setTimeout(() => form.submit(), 500);
        }

        if (search) {
            search.addEventListener('input', submitFilter);
        }

        if (kodeBatch) {
            kodeBatch.addEventListener('input', submitFilter);
        }

        if (date) {
            date.addEventListener('change', () => form.submit());
        }

        if (shift) {
            shift.addEventListener('change', () => form.submit());
        }

    });
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