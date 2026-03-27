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
                <h3><i class="bi bi-trash"></i> Recycle Bin Withdrawl</h3>
                <a href="{{ route('withdrawl.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | No. Withdrawl</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi | Expired Date</th>
                            <th>Jumlah Produksi</th>
                            <th>Tgl Akhir Edar | Jumlah Edar</th>
                            <th>Tgl Penarikan | Jumlah Tarik</th>
                            <th>Rincian</th>
                            <th>Pembuat</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($withdrawl as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | {{ $item->no_withdrawl }}</td>   
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }} | {{ \Carbon\Carbon::parse($item->exp_date)->format('d-m-Y') }}</td>
                            <td class="text-center align-middle">{{ $item->jumlah_produksi }} Box</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->tanggal_edar)->format('d-m-Y') }} | {{ $item->jumlah_edar }} Box</td>  
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->tanggal_tarik)->format('d-m-Y') }} | {{ $item->jumlah_tarik }} Box</td>  
                            <td class="text-center align-middle">
                                @php
                                $rincians = json_decode($item->rincian, true);
                                @endphp

                                @if(!empty($rincians))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#withdrawlModal{{ $item->uuid }}" 
                                   style="font-weight: bold; text-decoration: underline;">
                                   Detail
                               </a>

                               <div class="modal fade" id="withdrawlModal{{ $item->uuid }}" tabindex="-1"
                                aria-labelledby="withdrawlModalLabel{{ $item->uuid }}" aria-hidden="true">
                                <div class="modal-dialog" style="max-width: 60%;">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title" id="withdrawlModalLabel{{ $item->uuid }}">
                                                Detail rincian sebagai berikut
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm text-center align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nama Supplier</th>
                                                            <th>Alamat</th>
                                                            <th>Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($rincians as $i => $row)
                                                        <tr>
                                                            <td>{{ $i + 1 }}</td>
                                                            <td>{{ $row['nama_supplier'] ?? '-' }}</td>
                                                            <td>{{ $row['alamat'] ?? '-' }}</td>
                                                            <td>{{ $row['jumlah'] ?? '-' }} Box</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <span>-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">{{ $item->username }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                        <td class="text-center align-middle">
                            <form action="{{ route('withdrawl.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('withdrawl.deletePermanent', $item->uuid) }}" 
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
    {{ $withdrawl->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
