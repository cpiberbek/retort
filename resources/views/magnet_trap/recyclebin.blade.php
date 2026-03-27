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
                <h3><i class="bi bi-trash"></i> Recycle Bin Cleaning Magnet Trap</h3>
                <a href="{{ route('checklistmagnettrap.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Nama Produk</th>
                            <th>Kode Batch</th>
                            <th>Tanggal | Pukul</th>
                            <th>Jml Temuan</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Produksi</th>
                            <th>Engineer</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($magnettrap as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk ?? '-' }}</td>
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
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('checklistmagnettrap.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('checklistmagnettrap.deletePermanent', $item->uuid) }}" 
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
        {{ $magnettrap->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
