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
                <h3><i class="bi bi-trash"></i> Recycle Bin Peneraan Thermometer</h3>
                <a href="{{ route('thermometer.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Hasil Peneraan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($thermometer as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">
                                @php
                                $peneraan = is_array($item->peneraan) ? $item->peneraan : json_decode($item->peneraan, true);
                                @endphp

                                @if(!empty($peneraan))
                                <a href="javascript:void(0);" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#peneraanModal{{ $item->uuid }}">
                                Lihat Hasil Peneraan
                            </a>
                            <div class="modal fade" id="peneraanModal{{ $item->uuid }}" tabindex="-1"
                                aria-labelledby="peneraanModalLabel{{ $item->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="peneraanModalLabel{{ $item->uuid }}">
                                                Tanggal : 
                                                {{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift:
                                                {{ $item->shift }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @php
                                            $peneraan = is_array($item->peneraan) ? $item->peneraan : json_decode($item->peneraan, true);
                                            @endphp
                                            @if(!empty($peneraan))
                                            <table class="table table-bordered table-sm text-center align-middle mb-0" style="font-size: 12px;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Kode Thermometer</th>
                                                        <th>Area</th>
                                                        <th>Standar (0.0°C)</th>
                                                        <th>Pukul</th>
                                                        <th>Hasil Tera</th>
                                                        <th>Tindakan Perbaikan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($peneraan as $index => $items)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $items['kode_thermometer'] ?? '-' }}</td>
                                                        <td>{{ $items['area'] ?? '-' }}</td>
                                                        <td>{{ $items['standar'] ?? '-' }}</td>
                                                        <td>{{ $items['pukul'] ?? '-' }}</td>
                                                        <td>{{ $items['hasil_tera'] ?? '-' }}</td>
                                                        <td>{{ $items['tindakan_perbaikan'] ?? '-' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @else
                                            <p class="text-muted">Belum ada peneraan timbangan.</p>
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
                            <span>-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">{{ $item->username }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                        <td class="text-center align-middle">
                            <form action="{{ route('thermometer.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('thermometer.deletePermanent', $item->uuid) }}" 
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
    {{ $thermometer->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
