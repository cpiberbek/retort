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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Proses Packing</h3>
                <a href="{{ route('packing.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Nama Produk</th>
                            <th>Waktu</th>
                            <th>Pemeriksaan Packing</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($packing as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}</td>
                            <td class="text-center align-middle">
                                @if($item)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#packingModal{{ $item->uuid }}"
                                    class="fw-bold text-decoration-underline text-primary">Result</a>
                                    {{-- Modal Pemeriksaan Packing --}}
                                    <div class="modal fade" id="packingModal{{ $item->uuid }}" tabindex="-1"
                                        aria-labelledby="packingModalLabel{{ $item->uuid }}" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="packingModalLabel{{ $item->uuid }}">
                                                        Detail Pemeriksaan Proses Packing
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm text-center align-middle">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th colspan="16">{{ $item->nama_produk }}</th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Waktu</th>
                                                                    <th>Kalibrasi</th>
                                                                    <th>QR Code</th>
                                                                    <th>Kode Printing</th>
                                                                    <th>Kode Toples</th>
                                                                    <th>Kode Karton</th>
                                                                    <th>Suhu</th>
                                                                    <th>Speed</th>
                                                                    <th>Kondisi Segel</th>
                                                                    <th>Berat Toples (gr)</th>
                                                                    <th>Berat Pouch (gr)</th>
                                                                    <th>No Lot</th>
                                                                    <th>Tgl Kedatangan</th>
                                                                    <th>Supplier</th>
                                                                    <th>Keterangan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{ $item->waktu ?? '-' }}</td>
                                                                    <td>{{ $item->kalibrasi ?? '-' }}</td>
                                                                    <td>{{ $item->qrcode ?? '-' }}</td>

                                                                    <td>
                                                                        @if(!empty($item->kode_printing))
                                                                        <a href="{{ asset($item->kode_printing) }}" target="_blank">
                                                                            <img src="{{ asset($item->kode_printing) }}" width="80"
                                                                            class="img-thumbnail">
                                                                        </a>
                                                                        @else
                                                                        -
                                                                        @endif
                                                                    </td>

                                                                    <td>{{ $item->kode_toples ?? '-' }}</td>
                                                                    <td>{{ $item->kode_karton ?? '-' }}</td>
                                                                    <td>{{ $item->suhu ?? '-' }}</td>
                                                                    <td>{{ $item->speed ?? '-' }}</td>
                                                                    <td>{{ $item->kondisi_segel ?? '-' }}</td>
                                                                    <td>{{ $item->berat_toples ?? '-' }}</td>
                                                                    <td>{{ $item->berat_pouch ?? '-' }}</td>
                                                                    <td>{{ $item->no_lot ?? '-' }}</td>
                                                                    <td>{{ $item->tgl_kedatangan ?? '-' }}</td>
                                                                    <td>{{ $item->nama_supplier ?? '-' }}</td>
                                                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                                        Tutup
                                                    </button>
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
                                    <form action="{{ route('packing.restore', $item->uuid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm mb-1">
                                            <i class="bi bi-arrow-clockwise"></i> Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('packing.deletePermanent', $item->uuid) }}" 
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
                        <td colspan="12" class="text-center align-middle">Recycle bin kosong.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-2">
            {{ $packing->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

</div>
@endsection
