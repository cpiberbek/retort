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
                <h3><i class="bi bi-trash"></i> Recycle Bin Labelisasi PVDC</h3>
                <a href="{{ route('labelisasi_pvdc.index') }}" class="btn btn-primary">
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
                            <th>Hasil Pemeriksaan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($labelisasi_pvdc as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="text-center">{{ $item->nama_produk }}</td>

                            {{-- Modal Result --}}
                            <td class="text-center">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#resModal{{ $item->uuid }}"
                                    class="fw-bold text-decoration-underline">Result</a>
                                    <div class="modal fade" id="resModal{{ $item->uuid }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title">Detail Labelisasi PVDC</h5>
                                                    <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body table-responsive">
                                                    <table class="table table-bordered table-sm text-center align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Mesin</th>
                                                                <th>Kode Batch</th>
                                                                <th>Gambar</th>
                                                                <th>Ket</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($item->labelisasi_detail as $items)
                                                            <tr>
                                                                <td>{{ $items['mesin'] ?? '-' }}</td>

                                                                <td>{{ $items['mincing']->kode_produksi ?? 'Batch tidak
                                                                ditemukan' }}</td>

                                                                <td>
                                                                    @if(!empty($items['file']))
                                                                    <a href="{{ asset(stripslashes($items['file'])) }}"
                                                                    target="_blank">
                                                                    <img src="{{ asset(stripslashes($items['file'])) }}"
                                                                    width="50" class="img-thumbnail">
                                                                </a>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>

                                                            <td>{{ $items['keterangan'] ?? '-' }}</td>
                                                        </tr>
                                                        @endforeach

                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>
                            <td class="text-center align-middle">
                                <form action="{{ route('labelisasi_pvdc.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('labelisasi_pvdc.deletePermanent', $item->uuid) }}" 
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
        {{ $labelisasi_pvdc->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
