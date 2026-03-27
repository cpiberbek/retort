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
                <h3><i class="bi bi-trash"></i> Recycle Bin Data No. Lot Wire</h3>
                <a href="{{ route('wire.index') }}" class="btn btn-primary">
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
                            <th>Nama Supplier</th>
                            <th>Data Wire</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($wire as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td>{{ $item->nama_produk }}</td>
                            <td>{{ $item->nama_supplier }}</td>
                            <td class="text-center">
                                @php
                                $data_wire = json_decode($item->data_wire, true);
                                @endphp

                                @if(!empty($data_wire))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#wireModal{{ $item->uuid }}" class="fw-bold text-decoration-underline">
                                    Result
                                </a>

                                <div class="modal fade" id="wireModal{{ $item->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog" style="max-width: 70%;">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title">Detail Pemeriksaan Wire</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                @foreach($data_wire as $mIndex => $mesin)
                                                <div class="mb-3 border-bottom pb-3">
                                                    <h6 class="fw-bold text-primary">🧭 Mesin: {{ $mesin['mesin'] ?? '-' }}</h6>
                                                    <table class="table table-bordered table-sm text-center">
                                                        <thead class="table-light">
                                                            <tr><th>No</th><th>Start - End</th><th>No. Lot</th></tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(!empty($mesin['detail']))
                                                            @foreach($mesin['detail'] as $idx => $dtl)
                                                            <tr>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td>{{ $dtl['start'] ?? '' }} - {{ $dtl['end'] ?? '' }}</td>
                                                                <td>{{ $dtl['no_lot'] ?? '' }}</td>
                                                            </tr>
                                                            @endforeach
                                                            @else
                                                            <tr><td colspan="3">Tidak ada data</td></tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @endforeach
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
                                <form action="{{ route('wire.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('wire.deletePermanent', $item->uuid) }}" 
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
        {{ $wire->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
