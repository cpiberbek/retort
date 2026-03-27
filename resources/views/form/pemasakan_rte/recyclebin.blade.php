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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemasakan RTE</h3>
                <a href="{{ route('pemasakan_rte.index') }}" class="btn btn-primary">
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
                            <th>No. Chamber</th>
                            <th>Berat Produk (Gram)</th>
                            <th>Suhu Produk (°C)</th>
                            <th>Jumlah Tray</th>
                            <th>Total Reject (Kg)</th>
                            <th>Pengecekan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pemasakan_rte as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->no_chamber }}</td>
                            <td class="text-center align-middle">{{ $item->berat_produk }}</td>
                            <td class="text-center align-middle">{{ $item->suhu_produk }}</td>
                            <td class="text-center align-middle">{{ $item->jumlah_tray }}</td>
                            <td class="text-center align-middle">{{ $item->total_reject }}</td>
                            <td class="text-center align-middle">
                                @php
                                $cooking = $item->cooking ?? null;
                                if (is_string($cooking)) {
                                    $cooking = json_decode($cooking, true);
                                }

                                if (!function_exists('showValue')) {
                                    function showValue($val) {
                                        if (is_array($val)) {
                                            return collect($val)
                                            ->map(fn($v) => "<span class='badge bg-light text-dark border border-secondary me-1 mb-1'>$v</span>")
                                            ->implode('');
                                        }
                                        return e($val ?? '-');
                                    }
                                }

                                $sections = [
                                '1. Persiapan' => [
                                'Tekanan Angin (Kg/cm²)' => 'tekanan_angin',
                                'Tekanan Steam (Kg/cm²)' => 'tekanan_steam',
                                'Tekanan Air (Kg/cm²)' => 'tekanan_air',
                                ],
                                '2. Pemanasan Awal' => [
                                'Suhu Air Awal (°C)' => 'suhu_air_awal',
                                'Tekanan Awal (Mpa)' => 'tekanan_awal',
                                'Waktu Mulai' => 'waktu_mulai_awal',
                                'Waktu Selesai' => 'waktu_selesai_awal',
                                ],
                                '3. Proses Pemanasan' => [
                                'Suhu Air Proses (°C)' => 'suhu_air_proses',
                                'Tekanan Proses (Mpa)' => 'tekanan_proses',
                                'Waktu Mulai' => 'waktu_mulai_proses',
                                'Waktu Selesai' => 'waktu_selesai_proses',
                                ],
                                '4. Sterilisasi' => [
                                'Suhu Air Sterilisasi (°C)' => 'suhu_air_sterilisasi',
                                'Thermometer Retort (°C)' => 'thermometer_retort',
                                'Tekanan Sterilisasi (Mpa)' => 'tekanan_sterilisasi',
                                'Waktu Mulai' => 'waktu_mulai_sterilisasi',
                                'Waktu Pengecekan' => 'waktu_pengecekan_sterilisasi',
                                'Waktu Selesai' => 'waktu_selesai_sterilisasi',
                                ],
                                '5. Pendinginan Awal' => [
                                'Suhu Air (°C)' => 'suhu_air_pendinginan_awal',
                                'Tekanan (Mpa)' => 'tekanan_pendinginan_awal',
                                'Waktu Mulai' => 'waktu_mulai_pendinginan_awal',
                                'Waktu Selesai' => 'waktu_selesai_pendinginan_awal',
                                ],
                                '6. Pendinginan' => [
                                'Suhu Air (°C)' => 'suhu_air_pendinginan',
                                'Tekanan (Mpa)' => 'tekanan_pendinginan',
                                'Waktu Mulai' => 'waktu_mulai_pendinginan',
                                'Waktu Selesai' => 'waktu_selesai_pendinginan',
                                ],
                                '7. Proses Akhir' => [
                                'Suhu Air (°C)' => 'suhu_air_akhir',
                                'Tekanan (Mpa)' => 'tekanan_akhir',
                                'Waktu Mulai' => 'waktu_mulai_akhir',
                                'Waktu Selesai' => 'waktu_selesai_akhir',
                                ],
                                '8. Total Waktu Proses' => [
                                'Waktu Mulai Total (WIB)' => 'waktu_mulai_total',
                                'Waktu Selesai Total (WIB)' => 'waktu_selesai_total',
                                ],
                                '9. Sensori' => [
                                'Suhu Produk Akhir (°C)' => 'suhu_produk_akhir',
                                'Sobek Seal' => 'sobek_seal',
                                ],
                                ];
                                @endphp

                                @if(!empty($cooking))
                                <a href="#" class="fw-bold text-decoration-underline text-primary"
                                data-bs-toggle="modal" data-bs-target="#cookingModal{{ $item->uuid }}">
                                Result
                            </a>

                            <!-- Modal Detail Cooking -->
                            <div class="modal fade" id="cookingModal{{ $item->uuid }}" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <div class="modal-content">

                                        <!-- Header -->
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Detail Proses Pemasakan</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <!-- Body -->
                                        <div class="modal-body">
                                            @foreach($sections as $title => $rows)
                                            <h6 class="fw-bold text-primary mt-3">{{ $title }}</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-3 align-middle text-start">
                                                    <tbody>
                                                        @foreach($rows as $label => $key)
                                                        <tr>
                                                            <td class="fw-semibold w-50">{{ $label }}</td>
                                                            <td>{!! showValue($cooking[$key] ?? '-') !!}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endforeach
                                        </div>

                                        <!-- Footer -->
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
                            <form action="{{ route('pemasakan_rte.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('pemasakan_rte.deletePermanent', $item->uuid) }}" 
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
                <td colspan="20" class="text-center align-middle">Recycle bin kosong.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-2">
    {{ $pemasakan_rte->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
