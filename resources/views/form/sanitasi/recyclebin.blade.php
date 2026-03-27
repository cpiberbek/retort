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
                <h3><i class="bi bi-trash"></i> Recycle Bin Kontrol Sanitasi</h3>
                <a href="{{ route('sanitasi.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Area</th>
                            <th>Pemeriksaan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sanitasi as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ $item->area }}</td>
                            <td class="text-center align-middle">
                                @if(!empty($item->pemeriksaan))
                                <a href="javascript:void(0);" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#pemeriksaanModal{{ $item->uuid }}">
                                Lihat Pemeriksaan
                            </a>

                            {{-- Modal --}}
                            <div class="modal fade" id="pemeriksaanModal{{ $item->uuid }}" tabindex="-1"
                                aria-labelledby="pemeriksaanModalLabel{{ $item->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="pemeriksaanModalLabel{{ $item->uuid }}">
                                                Pemeriksaan Area: {{ $item->area }} |
                                                {{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} Shift:
                                                {{ $item->shift }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @php
                                            $pemeriksaan = json_decode($item->pemeriksaan, true);
                                            @endphp
                                            @if(!empty($pemeriksaan))
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-secondary text-center">
                                                    <tr>
                                                        <th>Bagian</th>
                                                        <th>Waktu</th>
                                                        <th>Kondisi</th>
                                                        <th>Keterangan</th>
                                                        <th>Rencana Tindakan</th>
                                                        <th>Waktu Pengerjaan</th>
                                                        <th>Dikerjakan Oleh</th>
                                                        <th>Waktu Verifikasi</th>
                                                    </tr>
                                                </thead>
                                                @php
                                                // Mapping kondisi tanpa angka
                                                $kondisiMapping = [
                                                '✔' => 'OK (Bersih)',
                                                '1' => 'Basah',
                                                '2' => 'Berdebu',
                                                '3' => 'Kerak',
                                                '4' => 'Noda',
                                                '5' => 'Karat',
                                                '6' => 'Sampah',
                                                '7' => 'Retak/Pecah',
                                                '8' => 'Sisa Produk',
                                                '9' => 'Sisa Adonan',
                                                '10'=> 'Berjamur',
                                                '11'=> 'Lain-lain'
                                                ];
                                                @endphp

                                                <tbody>
                                                    @foreach($pemeriksaan as $bagian => $items)
                                                    <tr>
                                                        <td>{{ $bagian }}</td>
                                                        <td>{{ $items['waktu'] ?? '-' }}</td>
                                                        <td>{{ $kondisiMapping[$items['kondisi']] ?? '-' }}</td>
                                                        <td>{{ $items['keterangan'] ?? '-' }}</td>
                                                        <td>{{ $items['tindakan'] ?? '-' }}</td>
                                                        <td>{{ $items['waktu_koreksi'] ?? '-' }}</td>
                                                        <td>{{ $items['dikerjakan_oleh'] ?? '-' }}</td>
                                                        <td>{{ $items['waktu_verifikasi'] ?? '-' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                            @else
                                            <p class="text-muted">Belum ada pemeriksaan.</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                            data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">Belum ada pemeriksaan</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">{{ $item->username }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                        <td class="text-center align-middle">
                            <form action="{{ route('sanitasi.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('sanitasi.deletePermanent', $item->uuid) }}" 
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
    {{ $sanitasi->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
