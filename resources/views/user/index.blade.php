@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-people"></i> Daftar User</h3>
                {{-- <a href="{{ route('user.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah User
                </a> --}}
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('user.index') }}" class="mb-3 d-flex justify-content-end">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari user..."
                    value="{{ request('search') }}" style="width: 250px;"> {{-- <=== dibatasi 250px --}} <button
                    class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Cari
                    </button>
                    @if(request('search'))
                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary ms-2">Reset</a>
                    @endif
            </form>

            {{-- Table User --}}
           <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Plant</th>
                            <th>Department</th>
                            <th>Type</th>
                            <th>Akses Multiplant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                            <tr>
                                <td class="text-center align-middle">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td class="text-center align-middle">{{ $user->name }}</td>
                                <td class="text-center align-middle">{{ $user->username }}</td>
                                <td class="text-left align-middle">{{ $user->email }}</td>
                                <td class="text-center align-middle">{{ $user->plantRelasi->plant ?? '-' }}</td>
                                <td class="text-center align-middle">{{ $user->departmentRelasi->nama ?? '-' }}</td>
                                <td class="text-center align-middle">
                                    {{ ucfirst(\Spatie\Permission\Models\Role::whereIn('id', function ($query) use ($user) {
                                        $query->select('role_id')
                                            ->from('model_has_roles')
                                            ->where('model_id', $user->uuid);
                                    })->value('name') ?? '-') }}
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button"
                                        class="btn btn-sm btn-primary btn-plant-option"
                                        data-bs-toggle="modal"
                                        data-bs-target="#plantOptionModal"
                                        data-uuid="{{ $user->uuid }}"
                                        data-plant-option='@json($user->plant_option ?? [])'>
                                        Buka / Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="d-flex justify-content-end">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="plantOptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="plantOptionForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Akses Multiplant</h5>
                    <button type="button" class="btn btn-sm" data-bs-dismiss="modal">
                        X
                    </button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Plant yang dapat diakses akun ini:</label>

                    <div id="plantOptionList">
                        @foreach ($plants as $plant)
                            <div class="form-check">
                                <input class="form-check-input plant-option-check"
                                    type="checkbox"
                                    name="plant_option[]"
                                    value="{{ $plant->uuid }}"
                                    id="plant_{{ $plant->uuid }}">

                                <label class="form-check-label" for="plant_{{ $plant->uuid }}">
                                    {{ $plant->plant }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-plant-option').forEach(button => {
        button.addEventListener('click', function () {
            const uuid = this.dataset.uuid;
            const options = JSON.parse(this.dataset.plantOption || '[]');

            document.getElementById('plantOptionForm').action =
                `{{ url('users') }}/${uuid}/plant-option`;

            document.querySelectorAll('.plant-option-check').forEach(checkbox => {
                checkbox.checked = options.includes(checkbox.value);
            });
        });
    });

    const select = document.getElementById('plantActiveSelect');

    if (!select) {
        return;
    }

    select.addEventListener('change', function () {
        fetch('{{ route('user.change-plant') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                plant_active: this.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message ?? 'Gagal mengubah plant.');
            }
        })
        .catch(error => {
            console.error(error);
            alert('Terjadi error saat mengubah plant.');
        });
    });
});
</script>

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

{{-- Styling pagination --}}
<style>
    .pagination {
        justify-content: end;
    }

    .pagination .page-link {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }

    /* Header tabel merah */
    .table thead {
        background-color: #dc3545 !important;
        /* merah gelap */
        color: #fff;
    }

    /* Baris tabel stripe merah muda */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f8d7da;
        /* merah muda terang */
    }

    .table-striped tbody tr:nth-of-type(even) {
        background-color: #f5c2c7;
        /* merah muda agak gelap */
    }

    /* Hover baris merah gelap */
    .table tbody tr:hover {
        background-color: #e4606d !important;
        color: #fff;
    }

    /* Border tabel merah */
    .table-bordered th,
    .table-bordered td {
        border-color: #dc3545;
    }

    /* Tombol aksi tetap jelas */
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
</style>
@endsection