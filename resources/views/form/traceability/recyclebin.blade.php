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
                <h3><i class="bi bi-trash"></i> Recycle Bin Traceability</h3>
                <a href="{{ route('traceability.index') }}" class="btn btn-primary">
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
                            <th rowspan="2">Kelengkapan</th>
                            <th rowspan="2">Kesimpulan</th>
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
                        @forelse ($traceability as $item)
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

                            <td class="text-center align-middle">
                                @php
                                $kelengkapan = json_decode($item->kelengkapan_form, true);
                                @endphp

                                @if(!empty($kelengkapan))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#traceModal{{ $item->uuid }}" 
                                   style="font-weight: bold; text-decoration: underline;">
                                   Detail
                               </a>

                               <div class="modal fade" id="traceModal{{ $item->uuid }}" tabindex="-1"
                                aria-labelledby="traceModalLabel{{ $item->uuid }}" aria-hidden="true">
                                <div class="modal-dialog" style="max-width: 60%;">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title" id="traceModalLabel{{ $item->uuid }}">
                                                Detail Kelengkapan Form
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm text-center align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Laporan</th>
                                                            <th>No. Dokumen</th>
                                                            <th>Kelengkapan Laporan</th>
                                                            <th>Total Waktu Telusur</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($kelengkapan as $i => $row)
                                                        <tr>
                                                            <td>{{ $i + 1 }}</td>
                                                            <td>{{ $row['laporan'] ?? '-' }}</td>
                                                            <td>{{ $row['no_dokumen'] ?? '-' }}</td>
                                                            <td>{{ $row['kelengkapan'] ?? '-' }}</td>
                                                            <td>{{ $row['waktu_telusur'] ?? '-' }}</td>
                                                        </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td colspan="4">Total Waktu Traceability</td>
                                                            <td>{{ $item->total_waktu }}</td>
                                                        </tr>
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
                        <td class="text-center align-middle">
                            <a href="#" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalKesimpulan{{ $item->id }}">
                            Lihat Kesimpulan
                        </a>
                        <!-- Modal Kesimpulan -->
                        <div class="modal fade" id="modalKesimpulan{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Dari hasil Traceability yang telah dilakukan, dapat disimpulkan:</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <p style="white-space: pre-wrap;">{{ $item->kesimpulan }}</p>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center align-middle">{{ $item->username }}</td>
                    <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                    <td class="text-center align-middle">
                        <form action="{{ route('traceability.restore', $item->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm mb-1">
                                <i class="bi bi-arrow-clockwise"></i> Restore
                            </button>
                        </form>

                        <form action="{{ route('traceability.deletePermanent', $item->uuid) }}" 
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
            <td colspan="20 class="text-center align-middle">Recycle bin kosong.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

<div class="d-flex justify-content-end mt-2">
    {{ $traceability->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
