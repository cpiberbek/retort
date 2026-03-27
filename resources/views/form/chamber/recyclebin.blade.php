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
                <h3><i class="bi bi-trash"></i> Recycle Bin Verifikasi Timer Chamber</h3>
                <a href="{{ route('chamber.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Pemeriksaan</th>
                            <th>QC (User)</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($chamber as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">
                                @php
                                $chambers = json_decode($item->verifikasi, true);
                                $rentang_menit = [5, 10, 20, 30, 60];
                                @endphp

                                @if(!empty($chambers))
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#chamberModal{{ $item->uuid }}" 
                                 class="text-primary fw-bold text-decoration-none" style="cursor: pointer;">Result</a>

                                 <div class="modal fade" id="chamberModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="chamberModalLabel{{ $item->uuid }}" aria-hidden="true">
                                  <div class="modal-dialog modal-xl modal-dialog-scrollable"> {{-- Pakai scrollable & XL --}}
                                   <div class="modal-content">
                                       {{-- Header Primary seperti Washing --}}
                                       <div class="modal-header bg-primary text-white">
                                           <h5 class="modal-title" id="chamberModalLabel{{ $item->uuid }}">
                                               <i class="bi bi-list-task me-2"></i> Detail Verifikasi Timer Chamber
                                           </h5>
                                           <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                       </div>

                                       <div class="modal-body text-start">
                                           <div class="table-responsive">
                                               <table class="table table-bordered table-striped table-sm text-center align-middle mb-3" style="font-size: 0.8rem;">
                                                   <thead class="table-light">
                                                       <tr class="table-secondary">
                                                           <th rowspan="2" colspan="2" class="align-middle">RENTANG UKUR</th>
                                                           @foreach($chambers as $index => $row)
                                                           <th colspan="6" class="fw-bold">No. Chamber {{ $index + 1 }}</th>
                                                           @endforeach
                                                       </tr>
                                                       <tr>
                                                           @foreach($chambers as $index => $row)
                                                           <th colspan="2">PLC</th>
                                                           <th colspan="2">STOPWATCH</th>
                                                           <th colspan="2">KOREKSI</th>
                                                           @endforeach
                                                       </tr>
                                                       <tr>
                                                           <th>MNT</th>
                                                           <th>DTK</th>
                                                           @foreach($chambers as $index => $row)
                                                           <th>MNT</th>
                                                           <th>DTK</th>
                                                           <th>MNT</th>
                                                           <th>DTK</th>
                                                           <th colspan="2">Factor</th>
                                                           @endforeach
                                                       </tr>
                                                   </thead>
                                                   <tbody>
                                                       @foreach($rentang_menit as $rentang)
                                                       <tr>
                                                           <td class="fw-bold">{{ $rentang }}</td>
                                                           <td>00</td>
                                                           @foreach($chambers as $index => $row)
                                                           <td>{{ $row['plc_menit_'.$rentang] ?? '-' }}</td>
                                                           <td>{{ $row['plc_detik_'.$rentang] ?? '-' }}</td>
                                                           <td>{{ $row['stopwatch_menit_'.$rentang] ?? '-' }}</td>
                                                           <td>{{ $row['stopwatch_detik_'.$rentang] ?? '-' }}</td>
                                                           <td colspan="2" class="fw-bold text-danger">{{ $row['faktor_koreksi_'.$rentang] ?? '-' }}</td>
                                                           @endforeach
                                                       </tr>
                                                       @endforeach
                                                   </tbody>
                                               </table>
                                           </div>

                                           @if($item->catatan)
                                           <h6 class="text-primary fw-bold mt-2"><i class="bi bi-journal-text me-1"></i> Catatan</h6>
                                           <p>{{ $item->catatan }}</p>
                                           @endif
                                       </div>

                                       <div class="modal-footer">
                                           <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
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
                        <form action="{{ route('chamber.restore', $item->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm mb-1">
                                <i class="bi bi-arrow-clockwise"></i> Restore
                            </button>
                        </form>

                        <form action="{{ route('chamber.deletePermanent', $item->uuid) }}" 
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
    {{ $chamber->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
