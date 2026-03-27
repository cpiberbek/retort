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
                <h3><i class="bi bi-trash"></i> Recycle Bin Recall</h3>
                <a href="{{ route('recall.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th rowspan="2">NO.</th>
                            <th rowspan="2">Date</th>
                            <th colspan="2">Informasi Telusur</th>
                            <th colspan="10">Informasi Pangan</th>
                            <th rowspan="2">Pembuat</th>
                            <th rowspan="2">Dihapus pada</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>Penyebab Telusur</th>
                            <th>Asal Informasi</th>
                            <th>Jenis Pangan</th>
                            <th>Nama Dagang</th>
                            <th>Berat / Isi Bersih</th>
                            <th>Jenis Kemasan</th>
                            <th>Kode Produksi</th>
                            <th>Tgl Produksi</th>
                            <th>Kadaluarsa</th>
                            <th>No. Daftar Pangan</th>
                            <th>Jumlah Produksi</th>
                            <th>Tindak Lanjut</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($recall as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>   
                            <td class="text-center align-middle">{{ $item->penyebab }}</td>
                            <td class="text-center align-middle">{{ $item->asal_informasi }}</td>
                            <td class="text-center align-middle">{{ $item->jenis_pangan }}</td>
                            <td class="text-center align-middle">{{ $item->nama_dagang }}</td>
                            <td class="text-center align-middle">{{ $item->berat_bersih }}</td>
                            <td class="text-center align-middle">{{ $item->jenis_kemasan }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->tanggal_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->tanggal_kadaluarsa }}</td>
                            <td class="text-center align-middle">{{ $item->no_pendaftaran }}</td>
                            <td class="text-center align-middle">{{ $item->jumlah_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->tindak_lanjut }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('recall.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('recall.deletePermanent', $item->uuid) }}" 
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
                    <td colspan="20" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $recall->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
