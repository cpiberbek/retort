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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pengecekan Klorin</h3>
                <a href="{{ route('klorin.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Foot Basin</th>
                            <th>Hand Basin</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($klorin as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | {{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                            <td class="text-center align-middle">
                                @if($item->footbasin)
                                <a href="{{ asset('storage/' . str_replace('public/', '', $item->footbasin)) }}" target="_blank">
                                    <img src="{{ asset('storage/' . str_replace('public/', '', $item->footbasin)) }}" 
                                    alt="Footbasin" 
                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                </a>
                                @else
                                <span class="text-muted">Tidak ada gambar</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                @if($item->handbasin)
                                <a href="{{ asset('storage/' . str_replace('public/', '', $item->handbasin)) }}" target="_blank">
                                    <img src="{{ asset('storage/' . str_replace('public/', '', $item->handbasin)) }}" 
                                    alt="Handbasin" 
                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                </a>
                                @else
                                <span class="text-muted">Tidak ada gambar</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">{{ $item->catatan }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('klorin.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('klorin.deletePermanent', $item->uuid) }}" 
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
        {{ $klorin->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
