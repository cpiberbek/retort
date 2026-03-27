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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Washing - Drying</h3>
                <a href="{{ route('washing.index') }}" class="btn btn-primary">
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
                            <th>Waktu</th>
                            <th>Hasil Pemeriksaan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($washing as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                            
                            {{-- KOLOM PEMERIKSAAN (Menggunakan Modal Detail Versi Server yang Lengkap) --}}
                            <td class="text-center align-middle">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->uuid }}" 
                                 class="text-primary fw-bold text-decoration-none" style="cursor: pointer;">Result</a>

                                 {{-- Modal Detail (Washing, PC Kleer, Pottasium, Speed) --}}
                                 <div class="modal fade" id="detailModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="detailModalLabel{{ $item->uuid }}">Detail Pemeriksaan Washing - Drying</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">

                                                {{-- IDENTIFIKASI --}}
                                                <h6 class="text-secondary fw-bold mt-2">Identifikasi</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Nama Produk</th><td>{{ $item->nama_produk }}</td></tr>
                                                        <tr><th>Kode Produksi</th><td>{{ $item->kode_produksi }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- PENGECEKAN --}}
                                                <h6 class="text-primary fw-bold mt-2"><i class="bi bi-check2-square me-1"></i> Pengecekan</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Panjang Produk Akhir (Cm)</th><td>{{ $item->panjang_produk ?? '-' }}</td></tr>
                                                        <tr><th>Diameter Produk Akhir (Mm)</th><td>{{ $item->diameter_produk ?? '-' }}</td></tr>
                                                        <tr><th>Airtrap</th><td>{{ $item->airtrap ?? '-' }}</td></tr>
                                                        <tr><th>Lengket</th><td>{{ $item->lengket ?? '-' }}</td></tr>
                                                        <tr><th>Sisa Adonan</th><td>{{ $item->sisa_adonan ?? '-' }}</td></tr>
                                                        <tr><th>Cek Kebocoran / Vacuum</th><td>{{ $item->kebocoran ?? '-' }}</td></tr>
                                                        <tr><th>Kekuatan Seal</th><td>{{ $item->kekuatan_seal ?? '-' }}</td></tr>
                                                        <tr><th>Print Kode Produksi</th><td>{{ $item->print_kode ?? '-' }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- PC KLEER --}}
                                                <h6 class="text-primary fw-bold mt-2"><i class="bi bi-droplet-half me-1"></i> PC Kleer</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Konsentrasi PC Kleer 1 (%)</th><td>{{ $item->konsentrasi_pckleer ?? '-' }}</td></tr>
                                                        <tr><th>Suhu PC Kleer 1 (°C)</th><td>{{ $item->suhu_pckleer_1 ?? '-' }}</td></tr>
                                                        <tr><th>Suhu PC Kleer 2 (°C)</th><td>{{ $item->suhu_pckleer_2 ?? '-' }}</td></tr>
                                                        <tr><th>pH PC Kleer</th><td>{{ $item->ph_pckleer ?? '-' }}</td></tr>
                                                        <tr><th>Kondisi Air PC Kleer</th><td>{{ $item->kondisi_air_pckleer ?? '-' }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- POTTASIUM SORBATE --}}
                                                <h6 class="text-primary fw-bold mt-2"><i class="bi bi-flask me-1"></i> Pottasium Sorbate</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Konsentrasi Pottasium Sorbate (%)</th><td>{{ $item->konsentrasi_pottasium ?? '-' }}</td></tr>
                                                        <tr><th>Suhu Pottasium Sorbate (°C)</th><td>{{ $item->suhu_pottasium ?? '-' }}</td></tr>
                                                        <tr><th>pH Pottasium Sorbate</th><td>{{ $item->ph_pottasium ?? '-' }}</td></tr>
                                                        <tr><th>Kondisi Air Pottasium Sorbate</th><td>{{ $item->kondisi_pottasium ?? '-' }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- SUHU & SPEED --}}
                                                <h6 class="text-primary fw-bold mt-2"><i class="bi bi-speedometer2 me-1"></i> Suhu & Speed Conveyor</h6>
                                                <table class="table table-bordered table-sm mb-3">
                                                    <tbody>
                                                        <tr><th style="width: 50%;">Suhu Heater (°C)</th><td>{{ $item->suhu_heater ?? '-' }}</td></tr>
                                                        <tr><th>Speed Conv. Drying 1</th><td>{{ $item->speed_1 ?? '-' }}</td></tr>
                                                        <tr><th>Speed Conv. Drying 2</th><td>{{ $item->speed_2 ?? '-' }}</td></tr>
                                                        <tr><th>Speed Conv. Drying 3</th><td>{{ $item->speed_3 ?? '-' }}</td></tr>
                                                        <tr><th>Speed Conv. Drying 4</th><td>{{ $item->speed_4 ?? '-' }}</td></tr>
                                                    </tbody>
                                                </table>

                                                {{-- CATATAN --}}
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
                            </td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('washing.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('washing.deletePermanent', $item->uuid) }}" 
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
        {{ $washing->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection
