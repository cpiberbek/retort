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
                <h3><i class="bi bi-trash"></i> Recycle Bin Data Lot PVDC</h3>
                <a href="{{ route('pvdc.index') }}" class="btn btn-primary">
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
                            <th>Tanggal Kedatangan</th>
                            <th>Tanggal Expired</th>
                            <th>Data PVDC</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pvdc as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="align-middle">{{ $item->nama_produk }}</td>
                            <td class="align-middle">{{ $item->nama_supplier }}</td>
                            <td class="text-center align-middle">{{
                                \Carbon\Carbon::parse($item->tgl_kedatangan)->format('d-m-Y') }}</td>
                                <td class="text-center align-middle">{{
                                    \Carbon\Carbon::parse($item->tgl_expired)->format('d-m-Y') }}</td>
                                    <td class="text-center align-middle">
                                        @php
                                        $data_pvdc = json_decode($item->data_pvdc, true);
                                        @endphp

                                        @if(!empty($data_pvdc))
                                        @php
                                        $batches = $item->pvdc_detail->flatMap(function ($mesin) {
                                            return $mesin['detail']->pluck('mincing.kode_produksi')->filter();
                                        })->unique()->values()->implode(', ');
                                        @endphp
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#pvdcModal{{ $item->uuid }}"
                                            style="font-weight: bold; text-decoration: underline;">
                                            Result
                                        </a>

                                        {{-- Modal Detail PVDC --}}
                                        <div class="modal fade" id="pvdcModal{{ $item->uuid }}" tabindex="-1"
                                            aria-labelledby="pvdcModalLabel{{ $item->uuid }}" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title" id="pvdcModalLabel{{ $item->uuid }}">Detail
                                                        Pemeriksaan PVDC - Batch: {{ $batches ?: 'N/A' }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body table-responsive">
                                                        @foreach($item->pvdc_detail as $mIndex => $mesin)
                                                        <div class="mb-3 border p-3 rounded bg-light">
                                                            <h6 class="fw-bold mb-2">🧭 Mesin: {{ $mesin['mesin'] ?? '-' }}</h6>
                                                            <table
                                                            class="table table-bordered table-striped table-sm text-center align-middle bg-white">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Batch</th>
                                                                    <th>No. Lot</th>
                                                                    <th>Waktu</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(!empty($mesin['detail']))
                                                                @foreach($mesin['detail'] as $index => $detail)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $detail['mincing']->kode_produksi ?? '-' }}</td>
                                                                    <td>{{ $detail['no_lot'] ?? '-' }}</td>
                                                                    <td>{{ $detail['waktu'] ?? '-' }}</td>
                                                                </tr>
                                                                @endforeach
                                                                @else
                                                                <tr>
                                                                    <td colspan="4">Tidak ada data batch</td>
                                                                </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endforeach
                                                    <div class="mt-3 text-start">
                                                        <strong>Catatan:</strong> {{ $item->catatan ?? '-' }}
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Tutup</button>
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
                                    <form action="{{ route('pvdc.restore', $item->uuid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm mb-1">
                                            <i class="bi bi-arrow-clockwise"></i> Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('pvdc.deletePermanent', $item->uuid) }}" 
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
                        <td colspan="10" class="text-center align-middle">Recycle bin kosong.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-2">
            {{ $pvdc->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

</div>
@endsection
