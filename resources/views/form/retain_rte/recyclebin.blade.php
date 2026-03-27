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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Retain RTE</h3>
                <a href="{{ route('retain_rte.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date</th>
                            <th>Nama Produk</th>
                            <th>Kode Batch</th>
                            <th>Analisa</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($retain_rte as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                            <td class="text-left align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">
                                @php
                                $analisa = is_string($item->analisa) ? json_decode($item->analisa, true) : ($item->analisa ?? []);
                                if (!$analisa) $analisa = [];
                                @endphp

                                @if(!empty($analisa))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#analisaModal{{ $item->uuid }}" 
                                    style="font-weight: bold; text-decoration: underline;">
                                    Lihat Analisa
                                </a>

                                <div class="modal fade" id="analisaModal{{ $item->uuid }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">Detail Analisa Sampel Retain</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm mb-0 text-center align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Bulan</th>
                                                                <th>Fisik/Tekstur</th>
                                                                <th>Aroma</th>
                                                                <th>Rasa</th>
                                                                <th>Average Score</th>
                                                                <th>Cemaran</th>
                                                                <th>Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($analisa as $index => $items)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    @if(!empty($items['bulan']))
                                                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $items['bulan'])->translatedFormat('F Y') }}
                                                                    @else
                                                                    -
                                                                    @endif
                                                                </td>
                                                                <td>{{ $items['fisik'] ?? '-' }}</td>
                                                                <td>{{ $items['aroma'] ?? '-' }}</td>
                                                                <td>{{ $items['rasa'] ?? '-' }}</td>
                                                                <td>{{ $items['rata_score'] ?? '-' }}</td>
                                                                <td>{{ $items['cemaran'] ?? '-' }}</td>
                                                                <td>
                                                                    @if(isset($items['release']))
                                                                    @if($items['release'] === 'Release')
                                                                    <span class="fw-bold text-success">{{ $items['release'] }}</span>
                                                                    @elseif($items['release'] === 'Tidak Release')
                                                                    <span class="fw-bold text-danger">{{ $items['release'] }}</span>
                                                                    @else
                                                                    {{ $items['release'] }}
                                                                    @endif
                                                                    @else
                                                                    -
                                                                    @endif
                                                                </td>
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
                                <form action="{{ route('retain_rte.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('retain_rte.deletePermanent', $item->uuid) }}" 
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
        {{ $retain_rte->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
