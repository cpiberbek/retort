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
                <h3><i class="bi bi-trash"></i> Recycle Bin Kontrol Labelisasi Karton</h3>
                <a href="{{ route('karton.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Start - Finish</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Bukti Kode</th>
                            <th>Tgl Kedatangan</th>
                            <th>Jumlah/Tambahan</th>
                            <th>Nama Supplier</th>
                            <th>No. Lot Karton</th>
                            <th>Keterangan</th>
                            <th>Operator</th>
                            <th>KR</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($karton as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y')
                            }}</td>
                            <td class="text-center align-middle">{{\Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} - {{\Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->mincing->kode_produksi ?? '-'}}</td>
                            <td class="text-center align-middle">
                                @if($item->kode_karton)
                                <a href="{{ asset('storage/' . str_replace('public/', '', $item->kode_karton)) }}"
                                    target="_blank">
                                    <img src="{{ asset('storage/' . str_replace('public/', '', $item->kode_karton)) }}"
                                    alt="Karton"
                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                </a>
                                @else
                                <span class="text-muted">Tidak ada gambar</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">{{\Carbon\Carbon::parse($item->tgl_kedatangan)->format('d-m-Y') }}</td>
                            <td class="text-center align-middle">{{ $item->jumlah }}</td>
                            <td class="text-center align-middle">{{ $item->nama_supplier }}</td>
                            <td class="text-center align-middle">{{ $item->no_lot }}</td>
                            <td class="text-center align-middle">{{ $item->keterangan }}</td>
                            <td class="text-center align-middle">{{ $item->nama_operator }}</td>
                            <td class="text-center align-middle">{{ $item->nama_koordinator }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('karton.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('karton.deletePermanent', $item->uuid) }}" 
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
        {{ $karton->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
