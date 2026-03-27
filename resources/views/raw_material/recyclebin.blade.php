@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Kedatangan Bahan Baku</h3>
                <a href="{{ route('raw_material.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tgl Datang</th>
                            <th>Bahan Baku</th>
                            <th>Supplier</th>
                            <th>No. DO / PO</th>
                            <th>Nopol Mobil</th>
                            <th>Dibuat Oleh</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($inspection as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">
                                {{ $item->setup_kedatangan ? \Carbon\Carbon::parse($item->setup_kedatangan)->format('d-m-Y') : '-' }} <br>
                                <span class="text-muted small">
                                    {{ $item->setup_kedatangan ? \Carbon\Carbon::parse($item->setup_kedatangan)->format('H:i') : '' }}
                                </span>
                            </td>

                            <td class="text-center align-middle fw-bold">{{ $item->bahan_baku }}</td>
                            <td class="text-center align-middle">{{ Str::limit($item->supplier, 25) }}</td>
                            <td class="text-center align-middle">{{ $item->do_po }}</td>
                            <td class="text-center align-middle">{{ $item->nopol_mobil }}</td>
                            <td class="text-center align-middle">{{ $item->creator->name ?? '-' }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('raw_material.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('raw_material.deletePermanent', $item->uuid) }}" 
                                  method="POST" class="d-inline">
                                  @csrf
                                  @method('DELETE')
                                  <button class="btn btn-danger btn-sm mb-1"
                                  onclick="return confirm('Hapus permanen?')">
                                  <i class="bi bi-x-circle"></i> Delete
                              </button>
                          </form>
                      </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $inspection->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
