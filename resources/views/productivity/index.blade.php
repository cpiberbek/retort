@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Productivity</h1>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-header py-3">
            <form method="GET"
                action="{{ route('productivity.index') }}"
                id="filterForm">

                <div class="row align-items-end">

                    <div class="col-md-3">
                        <label for="month_year" class="font-weight-bold mb-1">
                            Filter Bulan & Tahun
                        </label>

                        <input type="month"
                            name="month_year"
                            id="month_year"
                            class="form-control"
                            value="{{ request('month_year') }}">
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('productivity.index') }}"
                            class="btn btn-primary w-100">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>

                </div>

            </form>
        </div>

        <div class="card-body">

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-success px-4 py-2"
                    data-bs-toggle="modal" data-bs-target="#modalProductivity">
                    <i class="fas fa-plus"></i> Tambah / Update Laporan Bulan ini
                </button>
            </div>

            <div class="modal fade" id="modalProductivity" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Laporan Produktivitas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="modalProductivityBody">
                            <div class="text-center py-4">
                                <div class="spinner-border" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script>
            const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const currentYear = new Date().getFullYear();

            document.getElementById('modalProductivity').addEventListener('show.bs.modal', function () {
                const body = document.getElementById('modalProductivityBody');
                body.innerHTML = `<div class="text-center py-4"><div class="spinner-border"></div></div>`;

                fetch("{{ route('productivity.status') }}")
                    .then(res => res.json())
                    .then(data => {
                        if (!data.has_current_month) {
                            window.location.href = "{{ route('productivity.create-or-update') }}";
                            return;
                        }

                        if (data.all_filled) {
                            const options = data.existing_months
                                .map(item => `<option value="${item.uuid}">${monthNames[item.month - 1]} ${currentYear}</option>`)
                                .join('');

                            body.innerHTML = `
                                <p>Semua Entry Productivity di Tahun ini sudah lengkap. Pilih bulan yang mau diedit:</p>
                                <select class="form-select form-select-lg w-100 mb-3" id="editMonthSelect" style="padding: 0.75rem 1rem;">${options}</select>
                                <button class="btn btn-primary w-100" id="btnGoEdit">Edit Laporan</button>
                            `;

                            document.getElementById('btnGoEdit').addEventListener('click', function () {
                                const uuid = document.getElementById('editMonthSelect').value;
                                window.location.href = "{{ url('productivity/create-or-update') }}/" + uuid;
                            });
                        } else {
                            const options = data.available_months
                                .map(m => `<option value="${m}">${monthNames[m - 1]} ${currentYear}</option>`)
                                .join('');

                            body.innerHTML = `
                                <p>Laporan bulan ini sudah ada. Apakah anda hendak melakukan:</p>
                                <button class="btn btn-primary w-100 mb-3" id="btnEditCurrent">
                                    Edit Laporan Bulan Ini (${monthNames[new Date().getMonth()]} ${currentYear})
                                </button>
                                <hr>
                                <p class="mb-2">Atau tambah laporan di bulan lain (tahun ini) yang belum ada:</p>
                                <select class="form-select form-select-lg w-100 mb-3" id="addMonthSelect" style="padding: 0.75rem 1rem;">${options}</select>
                                <button class="btn btn-outline-success w-100" id="btnGoAdd">Tambah Laporan</button>
                            `;

                            document.getElementById('btnEditCurrent').addEventListener('click', function () {
                                window.location.href = "{{ url('productivity/create-or-update') }}/" + data.current_uuid;
                            });

                            document.getElementById('btnGoAdd').addEventListener('click', function () {
                                const month = document.getElementById('addMonthSelect').value;
                                window.location.href = "{{ route('productivity.create-or-update') }}?month=" + month;
                            });
                        }
                    });
            });
            </script>
            @endpush

            <div class="table-responsive">
                <table class="table table-bordered table-hover"
                    width="100%"
                    cellspacing="0">

                    <thead class="text-center bg-light">
                        <tr>
                            <th>No</th>
                            <th>Bulan</th>
                            <th>Tonase Bulanan</th>
                            <th>Total Manpower</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($productivities as $index => $data)
                            <tr>

                                <td class="text-center">
                                    {{ ($productivities->currentPage() - 1) * $productivities->perPage() + $index + 1 }}
                                </td>

                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($data->date)->translatedFormat('F Y') }}
                                </td>

                                <td class="text-center">
                                    {{ rtrim(rtrim(number_format($data->tonase_bulanan, 2, '.', ''), '0'), '.') }}
                                </td>

                                <td class="text-center">
                                    {{ $data->total_manpower }}
                                </td>

                                <td class="text-center">

                                    <a href="{{ route('productivity.create-or-update', ['uuid' => $data->uuid]) }}"
                                        class="btn btn-sm btn-warning"
                                        title="Update">
                                        <i class="bi bi-pencil-square"></i> Edit Data
                                    </a>

                                    <form action="{{ route('productivity.destroy', ['uuid' => $data->uuid]) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data productivity ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Hapus">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Data tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if(method_exists($productivities, 'links'))
                <div class="d-flex justify-content-end mt-3">
                    {{ $productivities->links('pagination::bootstrap-4') }}
                </div>
            @endif

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {

        $('#month_year').on('change', function () {
            $('#filterForm').submit();
        });

        let searchTimer;

        $('#search').on('input', function () {
            clearTimeout(searchTimer);

            searchTimer = setTimeout(function () {
                $('#filterForm').submit();
            }, 500);
        });

    });
</script>
@endpush