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
                <h3><i class="bi bi-trash"></i> Recycle Bin Penyimpangan Kualitas</h3>
                <a href="{{ route('penyimpangan-kualitas.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th style="width: 10%;">Tanggal</th>
                            <th style="width: 15%;">Nomor</th>
                            <th style="width: 20%;">Nama Produk</th>
                            <th style="width: 15%;">Lot/Kode</th>
                            <th style="width: 12%;">Dihapus pada</th>
                            <th style="width: 18%;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($penyimpanganKualitas as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                            </td>

                            {{-- Nomor --}}
                            <td class="fw-bold text-center text-primary">{{ $item->nomor }}</td>

                            {{-- Nama Produk --}}
                            <td>{{ $item->nama_produk }}</td>

                            {{-- Lot Kode --}}
                            <td class="text-center">{{ $item->lot_kode }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('penyimpangan-kualitas.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('penyimpangan-kualitas.deletePermanent', $item->uuid) }}" 
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
        {{ $penyimpanganKualitas->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
