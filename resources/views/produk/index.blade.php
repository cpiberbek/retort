@extends('layouts.app')

@section('content')
    <div class="container-fluid py-2">

        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ trim(session('success')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>
                        <i class="bi bi-list-check"></i>
                        Data List Produk Retort
                    </h3>

                    <a href="{{ route('produk.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i>
                        Tambah
                    </a>
                </div>

                {{-- Search Form --}}
                <form method="GET" class="mb-3 d-flex justify-content-end">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control me-2"
                        placeholder="Cari nama produk..."
                        style="width: 250px;"
                    >

                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                        Search
                    </button>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">

                        <thead>
                            <tr class="text-center align-middle">
                                <th style="width: 5%;">No</th>
                                <th style="width: 20%;">Date</th>
                                <th>Nama Produk</th>
                                <th>Plant</th>
                                <th style="width: 30%;">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @php
                                $no = ($produk->currentPage() - 1) * $produk->perPage() + 1;
                            @endphp

                            @forelse ($produk as $dep)

                                <tr>

                                    <td class="text-center align-middle">
                                        {{ $no++ }}
                                    </td>

                                    <td class="align-middle">
                                        {{ \Carbon\Carbon::parse($dep->created_at)->format('d-m-Y H:i') }}
                                    </td>

                                    <td class="align-middle">
                                        {{ $dep->nama_produk }}
                                    </td>

                                    <td class="align-middle">
                                        {{ $dep->dataPlant->plant ?? 'Nama Plant Tidak Ditemukan' }}
                                    </td>

                                    <td class="text-center align-middle">

                                        {{-- Tombol Bahan Baku --}}
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalBahanBaku{{ $dep->uuid }}"
                                            title="Atur Bahan Baku"
                                        >
                                            <i class="bi bi-box-seam"></i>
                                            Bahan Baku
                                        </button>

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('produk.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm me-1"
                                            title="Edit"
                                        >
                                            <i class="bi bi-pencil"></i>
                                            Edit
                                        </a>

                                        {{-- Hapus --}}
                                        <form
                                            action="{{ route('produk.destroy', $dep->uuid) }}"
                                            method="POST"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus?')"
                                                title="Hapus"
                                            >
                                                <i class="bi bi-trash"></i>
                                                Hapus
                                            </button>
                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center">
                                        Belum ada data produk.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>


                {{-- ========================================================= --}}
                {{-- MODAL BAHAN BAKU --}}
                {{-- MODAL DILETAKKAN DI LUAR TABLE / TBODY --}}
                {{-- ========================================================= --}}

                @foreach ($produk as $dep)

                    <div
                        class="modal fade"
                        id="modalBahanBaku{{ $dep->uuid }}"
                        tabindex="-1"
                        aria-hidden="true"
                    >

                        <div class="modal-dialog modal-lg modal-dialog-centered">

                            <div class="modal-content">

                                <form
                                    action="{{ route('produk.updateBahanBaku', $dep->uuid) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('PUT')

                                    {{-- Modal Header --}}
                                    <div class="modal-header bg-primary text-white">

                                        <h5 class="modal-title">
                                            <i class="bi bi-box-seam me-2"></i>
                                            Bahan Baku Produk
                                        </h5>

                                        <button
                                            type="button"
                                            class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"
                                        ></button>

                                    </div>


                                    {{-- Modal Body --}}
                                    <div class="modal-body">

                                        {{-- Nama Produk --}}
                                        <div class="mb-3">

                                            <label class="form-label fw-bold">
                                                Nama Produk
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="{{ $dep->nama_produk }}"
                                                readonly
                                            >

                                        </div>


                                        <hr>


                                        {{-- Header Daftar Bahan --}}
                                        <div class="d-flex justify-content-between align-items-center mb-3">

                                            <h6 class="fw-bold mb-0">
                                                Daftar Bahan Baku
                                            </h6>

                                            <button
                                                type="button"
                                                class="btn btn-success btn-sm btnTambahBahan"
                                                data-target="listBahan{{ $dep->uuid }}"
                                            >
                                                <i class="bi bi-plus-circle"></i>
                                                Tambah Bahan
                                            </button>

                                        </div>


                                        {{-- List Bahan Baku --}}
                                        <div
                                            id="listBahan{{ $dep->uuid }}"
                                            class="list-bahan"
                                        >

                                            @php
                                                $bahanProduk = $dep->bahan_baku ?? [];
                                            @endphp


                                            {{-- Jika sudah ada bahan baku --}}
                                            @forelse ($bahanProduk as $index => $bahan)

                                                <div class="row mb-2 bahan-row">

                                                    <div class="col-md-10">

                                                        <select
                                                            name="bahan_baku[]"
                                                            class="form-select"
                                                        >

                                                            <option value="">
                                                                -- Pilih Bahan Baku --
                                                            </option>

                                                            @foreach ($rawMaterials as $rm)

                                                                <option
                                                                    value="{{ $rm->nama_bahan_baku }}"
                                                                    {{ $rm->nama_bahan_baku == $bahan ? 'selected' : '' }}
                                                                >
                                                                    {{ $rm->nama_bahan_baku }}
                                                                </option>

                                                            @endforeach

                                                        </select>

                                                    </div>


                                                    <div class="col-md-2">

                                                        <button
                                                            type="button"
                                                            class="btn btn-danger btn-sm w-100 btnHapusBahan"
                                                        >
                                                            <i class="bi bi-trash"></i>
                                                        </button>

                                                    </div>

                                                </div>

                                            @empty

                                                {{-- Baris awal jika belum ada bahan --}}
                                                <div class="row mb-2 bahan-row">

                                                    <div class="col-md-10">

                                                        <select
                                                            name="bahan_baku[]"
                                                            class="form-select"
                                                        >

                                                            <option value="" selected>
                                                                -- Pilih Bahan Baku --
                                                            </option>

                                                            @foreach ($rawMaterials as $rm)

                                                                <option value="{{ $rm->nama_bahan_baku }}">
                                                                    {{ $rm->nama_bahan_baku }}
                                                                </option>

                                                            @endforeach

                                                        </select>

                                                    </div>


                                                    <div class="col-md-2">

                                                        <button
                                                            type="button"
                                                            class="btn btn-danger btn-sm w-100 btnHapusBahan"
                                                        >
                                                            <i class="bi bi-trash"></i>
                                                        </button>

                                                    </div>

                                                </div>

                                            @endforelse

                                        </div>


                                        {{-- Info --}}
                                        <div class="alert alert-info mt-3 mb-0">

                                            <i class="bi bi-info-circle me-2"></i>

                                            Bahan baku yang disimpan di sini akan
                                            otomatis digunakan ketika produk dipilih
                                            pada form Mincing.

                                        </div>

                                    </div>


                                    {{-- Modal Footer --}}
                                    <div class="modal-footer">

                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-bs-dismiss="modal"
                                        >
                                            <i class="bi bi-x-circle"></i>
                                            Batal
                                        </button>

                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                        >
                                            <i class="bi bi-save"></i>
                                            Simpan Bahan Baku
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                @endforeach


                {{-- Pagination --}}
                <div class="d-flex justify-content-end mt-3">

                    {{ $produk->withQueryString()->links('pagination::bootstrap-5') }}

                </div>

            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>

        document.addEventListener('DOMContentLoaded', function() {


            /* =========================================================
             * TAMBAH BAHAN BAKU
             * ========================================================= */

            document.querySelectorAll('.btnTambahBahan').forEach(function(button) {

                button.addEventListener('click', function() {

                    const targetId = this.dataset.target;

                    const container = document.getElementById(targetId);

                    if (!container) {
                        return;
                    }


                    const row = document.createElement('div');

                    row.className = 'row mb-2 bahan-row';


                    row.innerHTML = `
                        <div class="col-md-10">

                            <select
                                name="bahan_baku[]"
                                class="form-select"
                            >

                                <option value="" selected>
                                    -- Pilih Bahan Baku --
                                </option>

                                @foreach ($rawMaterials as $rm)

                                    <option value="{{ $rm->nama_bahan_baku }}">
                                        {{ $rm->nama_bahan_baku }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-2">

                            <button
                                type="button"
                                class="btn btn-danger btn-sm w-100 btnHapusBahan"
                            >
                                <i class="bi bi-trash"></i>
                            </button>

                        </div>
                    `;


                    container.appendChild(row);

                });

            });



            /* =========================================================
             * HAPUS BAHAN BAKU
             * ========================================================= */

            document.addEventListener('click', function(event) {

                const button = event.target.closest('.btnHapusBahan');

                if (!button) {
                    return;
                }


                const row = button.closest('.bahan-row');

                if (!row) {
                    return;
                }


                const container = row.parentElement;

                const rows = container.querySelectorAll('.bahan-row');


                if (rows.length > 1) {

                    row.remove();

                } else {

                    const select = row.querySelector('select');

                    if (select) {
                        select.value = '';
                    }

                }

            });



            /* =========================================================
             * CEGAH BAHAN BAKU DUPLIKAT
             * ========================================================= */

            document.addEventListener('change', function(event) {

                if (!event.target.matches('.list-bahan select')) {
                    return;
                }


                const select = event.target;

                const value = select.value;


                if (!value) {
                    return;
                }


                const container = select.closest('.list-bahan');

                if (!container) {
                    return;
                }


                const allSelects = container.querySelectorAll('select');

                let duplicate = false;


                allSelects.forEach(function(otherSelect) {

                    if (
                        otherSelect !== select &&
                        otherSelect.value === value
                    ) {
                        duplicate = true;
                    }

                });


                if (duplicate) {

                    alert('Bahan baku tersebut sudah dipilih.');

                    select.value = '';

                }

            });

        });



        /* =========================================================
         * AUTO HIDE ALERT
         * ========================================================= */

        setTimeout(() => {

            const alert = document.querySelector('.alert');

            if (alert) {

                alert.classList.remove('show');

                alert.classList.add('fade');

            }

        }, 3000);

    </script>


    {{-- ========================================================= --}}
    {{-- CSS --}}
    {{-- ========================================================= --}}

    <style>

        .table thead {
            background-color: #dc3545 !important;
            color: #fff;
        }


        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8d7da;
        }


        .table-striped tbody tr:nth-of-type(even) {
            background-color: #f5c2c7;
        }


        .table tbody tr:hover {
            background-color: #e4606d !important;
            color: #fff;
        }


        .table-bordered th,
        .table-bordered td {
            border-color: #dc3545;
        }


        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }


        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }


        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }


        .btn-danger:hover {
            background-color: #b02a37;
            border-color: #a52834;
        }


        .pagination {
            justify-content: end;
        }


        .pagination .page-link {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }


        .modal-header.bg-primary {
            background-color: #0d6efd !important;
        }


        .bahan-row select {
            min-height: 38px;
        }

    </style>

@endsection
