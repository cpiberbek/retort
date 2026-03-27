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
                <h3><i class="bi bi-trash"></i> Recycle Bin Metal Detector</h3>
                <a href="{{ route('metal.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Pukul</th>
                            <th>FE 1.0 mm</th>
                            <th>NFE 1.5 mm</th>
                            <th>
                                SUS
                                @if(Auth::user()->plant == '2debd595-89c4-4a7e-bf94-e623cc220ca6')
                                2.5 mm
                                @elseif(Auth::user()->plant == 'fdaca613-7ab2-4997-8f33-686e886c867d')
                                2.0 mm
                                @else
                                - mm
                                @endif
                            </th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>Engineer</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($metal as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | {{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                            <td class="text-center">
                                {!! $item->fe == 'Terdeteksi'
                                ? '<span class="text-success fw-bold">✓</span>'
                                : '<span class="text-danger fw-bold">x</span>' !!}
                            </td>
                            <td class="text-center">
                                {!! $item->nfe == 'Terdeteksi'
                                ? '<span class="text-success fw-bold">✓</span>'
                                : '<span class="text-danger fw-bold">x</span>' !!}
                            </td>
                            <td class="text-center">
                                {!! $item->sus == 'Terdeteksi'
                                ? '<span class="text-success fw-bold">✓</span>'
                                : '<span class="text-danger fw-bold">x</span>' !!}
                            </td>

                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->nama_enginer }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('metal.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('metal.deletePermanent', $item->uuid) }}" 
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
        {{ $metal->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
