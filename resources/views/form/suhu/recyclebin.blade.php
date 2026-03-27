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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Suhu Ruang</h3>
                <a href="{{ route('suhu.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Pukul</th>
                            <th>Pemeriksaan</th>
                            <th>Keterangan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($suhu as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td> <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                            <td class="text-center align-middle">
                                @php
                                // Decode JSON hasil suhu dari database
                                $hasilSuhu = is_string($item->hasil_suhu)
                                ? json_decode($item->hasil_suhu, true)
                                : ($item->hasil_suhu ?? []);

                                if (!$hasilSuhu) $hasilSuhu = [];

                                // Ambil daftar area & standar dari tabel area_suhu
                                $areaList = $area_suhus ?? [];
                                @endphp

                                @if(!empty($hasilSuhu))
                                <a href="javascript:void(0);" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#peneraanModal{{ $item->uuid }}">
                                Lihat Hasil Pemeriksaan
                            </a>
                            {{-- Modal --}}
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
                                            @if(!empty($hasilSuhu))
                                            <table class="table table-bordered table-sm mb-0 text-center align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 50%" class="text-left">Area</th>
                                                        @foreach($areaList as $area)
                                                        <th>{{ $area->area }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- Baris Standar --}}
                                                    <tr>
                                                        <td class="fw-bold text-left"><b>Standar (°C)</b></td>
                                                        @foreach($areaList as $area)
                                                        <td class="text-center" style="font-weight: 700;">{{ $area->standar ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>

                                                    {{-- Baris Aktual --}}
                                                    <tr>
                                                        <td class="fw-bold text-left"><b>Aktual (°C)</b></td>
                                                        @foreach($areaList as $area)
                                                        @php
                                                        // Cocokkan nilai aktual berdasarkan area
                                                        $matched = collect($hasilSuhu)->firstWhere('area', $area->area);
                                                        $nilai = floatval($matched['nilai'] ?? 0);
                                                        $standarStr = trim($area->standar ?? '');
                                                        $outOfRange = false;

                                                        if ($standarStr !== '') {
                                                            if (preg_match('/^<\s*(\d+(\.\d+)?)/', $standarStr, $m)) {
                                                                $max = floatval($m[1]);
                                                                $outOfRange = $nilai >= $max;
                                                            } elseif (preg_match('/^>\s*(\d+(\.\d+)?)/', $standarStr, $m)) {
                                                                $min = floatval($m[1]);
                                                                $outOfRange = $nilai <= $min;
                                                            } elseif (preg_match('/^(\d+(\.\d+)?)\s*-\s*(\d+(\.\d+)?)/', $standarStr, $m)) {
                                                                $min = floatval($m[1]);
                                                                $max = floatval($m[3]);
                                                                $outOfRange = $nilai < $min || $nilai > $max;
                                                            }
                                                        }
                                                        @endphp

                                                        <td class="fw-bold text-center {{ $outOfRange ? 'text-danger' : 'text-success' }}">
                                                            {{ $matched['nilai'] ?? '-' }}
                                                        </td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>
                                            </table>
                                            @else
                                            <p class="text-muted">Belum ada pemeriksaan</p>
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
                        <td class="text-center align-middle">{{ !empty($item->keterangan) ? $item->keterangan : '-' }}</td>
                        <td class="text-center align-middle">{{ $item->username }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                        <td class="text-center align-middle">
                            <form action="{{ route('suhu.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('suhu.deletePermanent', $item->uuid) }}" 
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
    {{ $suhu->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
