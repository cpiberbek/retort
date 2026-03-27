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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pengecekan Pre Packing</h3>
                <a href="{{ route('prepacking.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th rowspan="2">NO.</th>
                            <th rowspan="2">Date</th>
                            <th rowspan="2">Nama Produk</th>
                            <th rowspan="2">Kode Produksi</th>
                            <th rowspan="2">No. Conveyor</th>
                            <th rowspan="2">Suhu Produk (°C)</th>
                            <th colspan="2">Air (%)</th>
                            <th colspan="2">Minyak (%)</th>
                            <th colspan="2">Berat Produk per</th>
                            <th rowspan="2">Catatan</th>
                            <th rowspan="2">QC</th>
                            <th rowspan="2">Dihapus Pada</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>Basah</th>
                            <th>Kering</th>
                            <th>Basah</th>
                            <th>Kering</th>
                            <th>Pcs</th>
                            <th>Toples (berat kotor)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($prepacking as $item)
                        @php
                        // Suhu Produk
                        $suhuArray = json_decode($item->suhu_produk, true) ?? [];
                        $suhuText = implode(' | ', $suhuArray);

                        // Kondisi Produk
                        $kondisi = json_decode($item->kondisi_produk, true) ?? [];
                        $airBasah = ($kondisi['basah_air_ujung'] ?? 0) + ($kondisi['basah_air_seal'] ?? 0);
                        $airKering = ($kondisi['kering_air_ujung'] ?? 0) + ($kondisi['kering_air_seal'] ?? 0);
                        $minyakBasah = ($kondisi['basah_minyak_ujung'] ?? 0) + ($kondisi['basah_minyak_seal'] ?? 0);
                        $minyakKering = ($kondisi['kering_minyak_ujung'] ?? 0) + ($kondisi['kering_minyak_seal'] ?? 0);

                        // Berat Produk
                        $berat = json_decode($item->berat_produk, true) ?? [];
                        $pcsText = implode(' | ', [
                        $berat['pcs_1'] ?? 0,
                        $berat['pcs_2'] ?? 0,
                        $berat['pcs_3'] ?? 0,
                        ]);
                        $toplesText = implode(' | ', [
                        $berat['toples_1'] ?? 0,
                        $berat['toples_2'] ?? 0,
                        $berat['toples_3'] ?? 0,
                        ]);
                        @endphp
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->conveyor }}</td>
                            <td class="text-center align-middle">{{ $suhuText }}</td>
                            <td class="text-center align-middle">{{ $airBasah }}</td>
                            <td class="text-center align-middle">{{ $airKering }}</td>
                            <td class="text-center align-middle">{{ $minyakBasah }}</td>
                            <td class="text-center align-middle">{{ $minyakKering }}</td>
                            <td class="text-center align-middle">{{ $pcsText }}</td>
                            <td class="text-center align-middle">{{ $toplesText }}</td>
                            <td class="text-center align-middle">{{ $item->catatan }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('prepacking.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('prepacking.deletePermanent', $item->uuid) }}" 
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
        {{ $prepacking->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
