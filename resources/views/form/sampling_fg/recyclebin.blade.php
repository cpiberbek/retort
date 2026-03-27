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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Proses Sampling Finish Good</h3>
                <a href="{{ route('sampling_fg.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                       <tr>
                        <th rowspan="2" style="width: 3%;">NO.</th>
                        <th rowspan="2" style="width: 8%;">Tanggal | Shift</th>
                        <th rowspan="2" style="width: 4%;">Palet</th>
                        <th rowspan="2" style="width: 12%;">Nama Produk</th>
                        <th rowspan="2" style="width: 6%;">Kode Prod</th>
                        <th rowspan="2" style="width: 6%;">Exp. Date</th>
                        <th colspan="4">Pemeriksaan Proses Cartoning</th>
                        <th rowspan="2" style="width: 5%;">Isi<br>/Box</th>
                        <th rowspan="2" style="width: 4%;">Jml<br>Box</th>
                        <th colspan="3">Status Produk</th>
                        <th rowspan="2" style="width: 5%;">Item<br>Mutu</th>
                        <th rowspan="2" style="width: 8%;">Catatan</th>
                        <th rowspan="2" style="width: 4%;">QC</th>
                        <th rowspan="2" style="width: 4%;">Koord</th>
                        <th rowspan="2" style="width: 4%;">Dihapus pada</th>
                        <th rowspan="2" style="width: 5%;">Action</th>
                    </tr>
                    <tr>
                        <th style="width: 4%;">Jam</th>
                        <th style="width: 4%;">Kalib</th>
                        <th style="width: 4%;">Berat</th>
                        <th style="width: 5%;">Ket</th>
                        <th style="width: 3%;">Rls</th>
                        <th style="width: 3%;">Rjc</th>
                        <th style="width: 3%;">Hld</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($sampling_fg as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                        <td class="text-center">{{ $item->palet }}</td>
                        <td class="text-center">{{ $item->nama_produk }}</td>
                        <td class="text-center">{{ $item->kode_produksi }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->exp_date)->format('d-m-Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                        <td class="text-center">
                            @if ($item->kalibrasi == 'Sesuai') <span class="text-success fw-bold">✔</span>
                            @else <span class="text-danger fw-bold">✘</span> @endif
                        </td>
                        <td class="text-center">{{ $item->berat_produk }}</td>
                        <td class="text-center small">{{ $item->keterangan }}</td>
                        <td class="text-center">{{ $item->isi_per_box }}</td>
                        <td class="text-center">{{ $item->jumlah_box }}</td>
                        <td class="text-center">{{ $item->release }}</td>
                        <td class="text-center">{{ $item->reject }}</td>
                        <td class="text-center">{{ $item->hold }}</td>
                        <td class="text-center small">{{ $item->item_mutu }}</td>
                        <td class="text-start small">{{ $item->catatan }}</td>
                        <td class="text-center">{{ $item->username }}</td>
                        <td class="text-center">{{ $item->nama_koordinator }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                        <td class="text-center align-middle">
                            <form action="{{ route('sampling_fg.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('sampling_fg.deletePermanent', $item->uuid) }}" 
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
                <td colspan="21" class="text-center align-middle">Recycle bin kosong.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-2">
    {{ $sampling_fg->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
