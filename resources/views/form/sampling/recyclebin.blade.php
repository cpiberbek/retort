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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Sampling</h3>
                <a href="{{ route('sampling.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Jenis Sampling</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Jumlah</th>
                            <th>Jamur</th>
                            <th>Lendir</th>
                            <th>Klip Tajam</th>
                            <th>Pin Hole</th>
                            <th>Air Trap PVDC</th>
                            <th>Air Trap Produk</th>
                            <th>Keriput</th>
                            <th>Bengkok</th>
                            <th>Non Kode</th>
                            <th>Over Lap</th>
                            <th>Kecil</th>
                            <th>Terjepit</th>
                            <th>Double Klip</th>
                            <th>Seal Halus</th>
                            <th>Basah</th>
                            <th>Dll</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sampling as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ $item->jenis_sampel }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->jumlah }} {{ $item->jenis_kemasan }}</td>
                            <td class="text-center align-middle">{{ $item->jamur }}</td>
                            <td class="text-center align-middle">{{ $item->lendir }}</td>
                            <td class="text-center align-middle">{{ $item->klip_tajam }}</td>
                            <td class="text-center align-middle">{{ $item->pin_hole }}</td>
                            <td class="text-center align-middle">{{ $item->air_trap_pvdc }}</td>
                            <td class="text-center align-middle">{{ $item->air_trap_produk }}</td>
                            <td class="text-center align-middle">{{ $item->keriput }}</td>
                            <td class="text-center align-middle">{{ $item->bengkok }}</td>
                            <td class="text-center align-middle">{{ $item->non_kode }}</td>
                            <td class="text-center align-middle">{{ $item->over_lap }}</td>
                            <td class="text-center align-middle">{{ $item->kecil }}</td>
                            <td class="text-center align-middle">{{ $item->terjepit }}</td>
                            <td class="text-center align-middle">{{ $item->double_klip }}</td>
                            <td class="text-center align-middle">{{ $item->seal_halus }}</td>
                            <td class="text-center align-middle">{{ $item->basah }}</td>
                            <td class="text-center align-middle">{{ $item->dll }}</td>
                            <td class="text-center align-middle">
                                @if($item->catatan)
                                <a href="#" 
                                class="text-primary text-decoration-underline" 
                                data-bs-toggle="modal" 
                                data-bs-target="#catatanModal{{ $item->id }}">
                                Lihat Catatan
                            </a>

                            <!-- Modal -->
                            <div class="modal fade" id="catatanModal{{ $item->id }}" tabindex="-1" aria-labelledby="catatanLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="catatanLabel{{ $item->id }}">Catatan</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            {{ $item->catatan }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">{{ $item->username }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                        <td class="text-center align-middle">
                            <form action="{{ route('sampling.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('sampling.deletePermanent', $item->uuid) }}" 
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
                <td colspan="30" class="text-center align-middle">Recycle bin kosong.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-2">
    {{ $sampling->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
