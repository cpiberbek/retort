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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Mincing - Emulsifying - Aging</h3>
                <a href="{{ route('mincing.index') }}" class="btn btn-primary">
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
                            <th>Kode Produksi</th>
                            <th>Hasil Pemeriksaan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($mincing as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">
                                @if($item)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#mincingModal{{ $item->uuid }}" style="font-weight: bold; text-decoration: underline;">
                                    Result
                                </a>

                                @php
                                $nonPremixItems = json_decode($item->non_premix ?? '[]', true);
                                $premixItems    = json_decode($item->premix ?? '[]', true);
                                @endphp

                                <div class="modal fade" id="mincingModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="mincingModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title" id="mincingModalLabel{{ $item->uuid }}">Detail Pemeriksaan Mincing</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body table-responsive">
                                                <table class="table table-bordered table-striped table-sm text-center align-middle">
                                                    <tbody>
                                                        {{-- KODE PRODUKSI --}}
                                                        <tr>
                                                            <td class="text-left">Kode Produksi</td>
                                                            <td colspan="5">{{ $item->kode_produksi ?? '-' }}</td>
                                                        </tr>

                                                        {{-- PREPARATION --}}
                                                        <tr>
                                                            <td class="text-left">Preparation</td>
                                                            <td colspan="2">{{ $item->waktu_mulai ?? '-' }}</td>
                                                            <td class="text-center">-</td>
                                                            <td colspan="2">{{ $item->waktu_selesai ?? '-' }}</td>
                                                        </tr>

                                                        {{-- NON-PREMIX --}}
                                                        <tr class="section-header bg-light fw-bold">
                                                            <td class="text-left">Bahan Baku dan Bahan Tambahan (Non-Premix)</td>
                                                            <td>Kode</td>
                                                            <td>(°C)</td>
                                                            <td>*pH</td>
                                                            <td>Kg</td>
                                                            <td>Sens</td>
                                                        </tr>
                                                        @if(count($nonPremixItems) > 0)
                                                        @foreach($nonPremixItems as $bahan)
                                                        <tr>
                                                            <td>{{ $bahan['nama_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['kode_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['suhu_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['ph_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['berat_bahan'] ?? '-' }}</td>
                                                            <td>{{ $bahan['sensori'] ?? '-' }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @else
                                                        <tr><td colspan="6">-</td></tr>
                                                        @endif

                                                        {{-- PREMIX --}}
                                                        <tr class="section-header bg-light fw-bold">
                                                            <td class="text-left">Premix</td>
                                                            <td colspan="2">Kode</td>
                                                            <td colspan="2">Kg</td>
                                                            <td>Sens</td>
                                                        </tr>
                                                        @if(count($premixItems) > 0)
                                                        @foreach($premixItems as $p)
                                                        <tr>
                                                            <td>{{ $p['nama_premix'] ?? '-' }}</td>
                                                            <td colspan="2">{{ $p['kode_premix'] ?? '-' }}</td>
                                                            <td colspan="2">{{ $p['berat_premix'] ?? '-' }}</td>
                                                            <td>{{ $p['sensori_premix'] ?? '-' }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @else
                                                        <tr><td colspan="6">-</td></tr>
                                                        @endif

                                                        {{-- SUHU & WAKTU --}}
                                                        <tr>
                                                            <td class="text-left">Suhu (Sebelum Grinding)</td>
                                                            <td colspan="5">{{ $item->suhu_sebelum_grinding ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Waktu Mixing Premix</td>
                                                            <td colspan="2">{{ $item->waktu_mixing_premix_awal ?? '-' }}</td>
                                                            <td class="text-center">-</td>
                                                            <td colspan="2">{{ $item->waktu_mixing_premix_akhir ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Waktu Bowl Cutter</td>
                                                            <td colspan="2">{{ $item->waktu_bowl_cutter_awal ?? '-' }}</td>
                                                            <td class="text-center">-</td>
                                                            <td colspan="2">{{ $item->waktu_bowl_cutter_akhir ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Waktu Aging Emulsi</td>
                                                            <td colspan="2">{{ $item->waktu_aging_emulsi_awal ?? '-' }}</td>
                                                            <td class="text-center">-</td>
                                                            <td colspan="2">{{ $item->waktu_aging_emulsi_akhir ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Suhu Akhir Emulsi Gel</td>
                                                            <td colspan="5">{{ $item->suhu_akhir_emulsi_gel ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Waktu Mixing</td>
                                                            <td colspan="5">{{ $item->waktu_mixing ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Suhu Akhir Mixing</td>
                                                            <td colspan="5">{{ $item->suhu_akhir_mixing ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">Suhu Akhir Emulsifying</td>
                                                            <td colspan="5">{{ $item->suhu_akhir_emulsi ?? '-' }}</td>
                                                        </tr>

                                                        {{-- CATATAN --}}
                                                        <tr>
                                                            <td class="text-left">Catatan</td>
                                                            <td colspan="5">{{ $item->catatan ?? '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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
                                <form action="{{ route('mincing.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('mincing.deletePermanent', $item->uuid) }}" 
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
        {{ $mincing->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
