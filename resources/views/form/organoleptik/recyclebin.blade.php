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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Organoleptik</h3>
                <a href="{{ route('organoleptik.index') }}" class="btn btn-primary">
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
                            <th>Hasil Sensori</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($organoleptik as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">
                                @php
                                $sensori = json_decode($item->sensori, true);
                                @endphp

                                @if(!empty($sensori))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#pemeriksaanModal{{ $item->uuid }}"
                                    style="font-weight: bold; text-decoration: underline;">
                                    Result
                                </a>
                                <div class="modal fade" id="pemeriksaanModal{{ $item->uuid }}" tabindex="-1"
                                    aria-labelledby="pemeriksaanModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog" style="max-width: 70%;">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title text-start"
                                                id="pemeriksaanModalLabel{{ $item->uuid }}">Detail Pemeriksaan
                                            Organoleptik</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table
                                                class="table table-bordered table-striped table-sm text-center align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Kode Produksi</th>
                                                        <th>Penampilan</th>
                                                        <th>Aroma</th>
                                                        <th>Kekenyalan</th>
                                                        <th>Rasa Asin</th>
                                                        <th>Rasa Gurih</th>
                                                        <th>Rasa Manis</th>
                                                        <th>Rasa Ayam/BBQ/Ikan</th>
                                                        <th>Rasa Keseluruhan</th>
                                                        <th>Hasil Score</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($item->organoleptik_detail as $index => $items)
                                                    {{-- @dd($items); --}}
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $items['organoleptik']->kode_produksi ?? '-' }}</td>
                                                        <td>{{ $items['penampilan'] ?? '-' }}</td>
                                                        <td>{{ $items['aroma'] ?? '-' }}</td>
                                                        <td>{{ $items['kekenyalan'] ?? '-' }}</td>
                                                        <td>{{ $items['rasa_asin'] ?? '-' }}</td>
                                                        <td>{{ $items['rasa_gurih'] ?? '-' }}</td>
                                                        <td>{{ $items['rasa_manis'] ?? '-' }}</td>
                                                        <td>{{ $items['rasa_daging'] ?? '-' }}</td>
                                                        <td>{{ $items['rasa_keseluruhan'] ?? '-' }}</td>
                                                        <td>{{ $items['rata_score'] ?? '-' }}</td>
                                                        <td>
                                                            @php
                                                            $release = $items['release'] ?? '-';
                                                            $color = '';
                                                            $weight = 'font-weight:bold;';

                                                            if ($release === 'Release') {
                                                                $color = 'color:green;';
                                                            } elseif ($release === 'Tidak Release') {
                                                                $color = 'color:red;';
                                                            } else {
                                                                $weight = '';
                                                            }
                                                            @endphp

                                                            <span style="{{ $color }} {{ $weight }}">{{ $release
                                                            }}</span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Pagination --}}
                                    <div class="mt-3">
                                        {{ $organoleptik->withQueryString()->links('pagination::bootstrap-5') }}
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
                            <form action="{{ route('organoleptik.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('organoleptik.deletePermanent', $item->uuid) }}" 
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
    {{ $organoleptik->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
