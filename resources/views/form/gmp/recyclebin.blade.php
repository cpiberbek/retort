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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Personal Hygiene dan Kesehatan Karyawan</h3>
                <a href="{{ route('gmp.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date</th>

                            @php
                            // Normalisasi & hilangkan duplikasi area
                            $allAreas = [];

                            foreach($gmp as $d){
                                foreach($d->areas as $a){

                                    // Key normalisasi: lowercase + trim
                                    $key = strtolower(trim($a));

                                    // Simpan tampilan asli hanya jika belum ada
                                    if(!isset($allAreas[$key])){
                                        $allAreas[$key] = strtoupper(trim($a));
                                    }
                                }
                            }
                            @endphp

                            {{-- Header area --}}
                            @foreach($allAreas as $area)
                            <th>{{ $area }}</th>
                            @endforeach
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($gmp as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                            {{-- Kolom area --}}
                            @foreach($allAreas as $key => $areaLabel)
                            <td class="text-center align-middle">

                                @php
                                $targetArea = $key; // lowercase normalized
                                $scores = [];
                                $totalAttr = 0;
                                $countChecked = 0;

                                foreach($item->pemeriksaan as $row){

                                    if(strtolower(trim($row['area'] ?? '')) === $targetArea){

                                        $attrKeys = array_diff(
                                        array_keys($row),
                                        ['nama_karyawan','pukul','keterangan','area']
                                        );

                                        $rowTotal = count($attrKeys);
                                        $rowCount = 0;

                                        foreach($attrKeys as $keyAttr){
                                            if((int)($row[$keyAttr] ?? 0) === 1){
                                                $rowCount++;
                                            }
                                        }

                                        $scores[] = [
                                        'nama' => $row['nama_karyawan'],
                                        'nilai' => $rowCount
                                        ];

                                        $totalAttr += $rowTotal;
                                        $countChecked += $rowCount;
                                    }
                                }

                                $persen = $totalAttr > 0 
                                ? round(($countChecked/$totalAttr)*100, 1)
                                : 0;

                                usort($scores, fn($a,$b) => $b['nilai'] <=> $a['nilai']);
                                    $top = array_slice($scores, 0, 3);
                                    @endphp

                                    {{ $persen }} %
                                    <br>
                                    <small>
                                        @foreach($top as $s)
                                        • {{ $s['nama'] }} ({{ $s['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>
                                @endforeach
                                <td class="text-center align-middle">{{ $item->username }}</td>
                                <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                                <td class="text-center align-middle">
                                    <form action="{{ route('gmp.restore', $item->uuid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm mb-1">
                                            <i class="bi bi-arrow-clockwise"></i> Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('gmp.deletePermanent', $item->uuid) }}" 
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
            {{ $gmp->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

</div>
@endsection
