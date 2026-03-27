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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pengecekan Pemasakan</h3>
                <a href="{{ route('pemasakan.index') }}" class="btn btn-primary">
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
                            <th>Jumlah Tray</th>
                            <th>No. Chamber</th>
                            <th>Berat Produk (Gram)</th>
                            <th>Suhu Produk (°C)</th>
                            <th>Total Reject (Kg)</th>
                            <th>Pengecekan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pemasakan as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} || Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">
                                @if(is_array($item->kode_produksi))
                                @foreach ($item->kode_produksi as $uuid)
                                {{ $stuffingData[$uuid]->kode_produksi ?? 'Tidak ditemukan' }}
                                <br>
                                @endforeach
                                @else
                                -
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                @if(is_array($item->jumlah_tray))
                                @foreach ($item->jumlah_tray as $tray)
                                {{ $tray }} <br>
                                @endforeach
                                @else
                                -
                                @endif
                            </td>

                            <td class="text-center align-middle">{{ $item->no_chamber }}</td>
                            <td class="text-center align-middle">{{ $item->berat_produk }}</td>
                            <td class="text-center align-middle">{{ $item->suhu_produk }}</td>
                            <td class="text-center align-middle">{{ $item->total_reject }}</td>

                            <td class="text-center align-middle">
                                @if(!empty($cooking))
                                <a href="#" class="fw-bold text-decoration-underline text-primary"
                                data-bs-toggle="modal" data-bs-target="#cookingModal{{ $item->uuid }}">
                                Result
                            </a>

                            {{-- Modal Detail Cooking (Server Version) --}}
                            <div class="modal fade" id="cookingModal{{ $item->uuid }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                        <div class="modal-header bg-primary bg-gradient text-white">
                                            <h5 class="modal-title fw-semibold"><i class="bi bi-fire me-2"></i> Detail Proses Pemasakan</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-light-subtle">
                                            @php
                                            $sections = [
                                            '2. Tekanan & Suhu Awal' => ['Tekanan Angin' => 'tekanan_angin', 'Tekanan Steam' => 'tekanan_steam', 'Tekanan Air' => 'tekanan_air'],
                                            '3. Pemanasan Awal' => ['Suhu Air Awal' => 'suhu_air_awal', 'Tekanan Awal' => 'tekanan_awal', 'Waktu Mulai' => 'waktu_mulai_awal', 'Waktu Selesai' => 'waktu_selesai_awal'],
                                            '4. Proses Pemanasan' => ['Suhu Air Proses' => 'suhu_air_proses', 'Tekanan Proses' => 'tekanan_proses', 'Waktu Mulai' => 'waktu_mulai_proses', 'Waktu Selesai' => 'waktu_selesai_proses'],
                                            '5. Sterilisasi' => ['Suhu Air Sterilisasi' => 'suhu_air_sterilisasi', 'Thermometer Retort' => 'thermometer_retort', 'Tekanan Sterilisasi' => 'tekanan_sterilisasi'],
                                            '10. Hasil Pemasakan' => ['Suhu Produk Akhir' => 'suhu_produk_akhir', 'Panjang' => 'panjang', 'Diameter' => 'diameter', 'Rasa' => 'rasa', 'Warna' => 'warna', 'Texture' => 'texture']
                                            ];
                                            @endphp

                                            @foreach($sections as $title => $rows)
                                            <div class="mb-3">
                                                <h6 class="fw-bold text-primary">{{ $title }}</h6>
                                                <div class="table-responsive shadow-sm rounded">
                                                    <table class="table table-bordered table-sm align-middle text-center mb-0 bg-white">
                                                        <tbody>
                                                            @foreach($rows as $label => $key)
                                                            <tr>
                                                                <td class="fw-semibold text-start ps-3 w-50">{{ $label }}</td>
                                                                <td class="text-start ps-3">{!! showValue($cooking[$key] ?? '-') !!}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            @endforeach
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
                            <form action="{{ route('pemasakan.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form action="{{ route('pemasakan.deletePermanent', $item->uuid) }}" 
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
    {{ $pemasakan->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
