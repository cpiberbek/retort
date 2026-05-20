@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Update Pemeriksaan Proses Sampling Finish Good
            </h4>
            <form id="samplingForm" action="{{ route('sampling_fg.update_qc', $sampling_fg->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-exclamation-triangle"></i> Terjadi Kesalahan!</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- ===================== IDENTITAS DATA ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Sampling</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ old('date', $sampling_fg->date) }}" readonly>
                                <input type="hidden" name="date" value="{{ $sampling_fg->date }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" disabled>
                                    <option value="1" {{ $sampling_fg->shift == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ $sampling_fg->shift == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ $sampling_fg->shift == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                                <input type="hidden" name="shift" value="{{ $sampling_fg->shift }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <input type="text" class="form-control" value="{{ $sampling_fg->nama_produk }}" readonly>
                                <input type="hidden" name="nama_produk" id="nama_produk" value="{{ $sampling_fg->nama_produk }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <input type="text" class="form-control" value="{{ $sampling_fg->kode_produksi }}" readonly>
                                <input type="hidden" name="kode_produksi" id="kode_produksi" value="{{ $sampling_fg->kode_produksi }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Palet</label>
                                <input type="text" name="palet" id="palet" class="form-control" value="{{ old('palet', $sampling_fg->palet) }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="exp_date" id="exp_date" class="form-control" value="{{ old('exp_date', $sampling_fg->exp_date) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== PEMERIKSAAN PROSES CARTONING ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Pemeriksaan Proses Cartoning</strong>
                    </div>
                    <div class="card-body p-3">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Waktu</label>
                                <input type="time" name="pukul" id="timeInput" class="form-control" value="{{ old('pukul', $sampling_fg->pukul) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">Kalibrasi</label>
                                @php $sudahAda = !empty($sampling_fg->kalibrasi); @endphp
                                <select class="form-control" id="kalibrasi" name="kalibrasi" {{ $sudahAda ? 'disabled' : '' }}>
                                    <option value="Sesuai" {{ $sampling_fg->kalibrasi == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
                                    <option value="Tidak Sesuai" {{ $sampling_fg->kalibrasi == 'Tidak Sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                </select>
                                @if($sudahAda)
                                <input type="hidden" name="kalibrasi" value="{{ $sampling_fg->kalibrasi }}">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Berat Varian per Box (gr)</label>
                                <input type="number" name="berat_produk" id="berat_produk" class="form-control" value="{{ old('berat_produk', $sampling_fg->berat_produk) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" id="keterangan" class="form-control" value="{{ old('keterangan', $sampling_fg->keterangan) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Isi Varian per Box</label>
                                <input type="number" name="isi_per_box" id="isi_per_box" class="form-control" value="{{ old('isi_per_box', $sampling_fg->isi_per_box) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kemasan</label>
                                <select name="kemasan" id="kemasan" class="form-control selectpicker" {{ $sampling_fg->kemasan ? 'disabled' : '' }}>
                                    <option value="Jar" {{ $sampling_fg->kemasan == 'Jar' ? 'selected' : '' }}>Jar</option>
                                    <option value="Pouch" {{ $sampling_fg->kemasan == 'Pouch' ? 'selected' : '' }}>Pouch</option>
                                </select>
                                @if($sampling_fg->kemasan)
                                <input type="hidden" name="kemasan" value="{{ $sampling_fg->kemasan }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah (Box)</label>
                                <input type="number" name="jumlah_box" id="jumlah_box" class="form-control" value="{{ $sampling_fg->jumlah_box }}" readonly>
                            </div>
                        </div>
                        <hr>
                        <label class="form-label"><b>Status Varian</b></label>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Release (Box)</label>
                                <input type="number" name="release" id="release" class="form-control" value="{{ old('release', $sampling_fg->release) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reject (Box)</label>
                                <input type="number" name="reject" id="reject" class="form-control" value="{{ old('reject', $sampling_fg->reject) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hold (Box)</label>
                                <input type="number" name="hold" id="hold" class="form-control" value="{{ old('hold', $sampling_fg->hold) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== CATATAN ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Koordinator</strong>
                    </div>
                    <div class="card-body">
                        <div class="col-md-6">
                            <label class="form-label">Nama KR</label>
                            <select id="nama_koordinator" name="nama_koordinator" class="form-control selectpicker" data-live-search="true" required>
                                <option value="">-- Pilih Koordinator --</option>
                                @foreach($koordinators as $koordinator)
                                <option value="{{ $koordinator->nama_karyawan }}" {{ old('nama_koordinator', $sampling_fg->nama_koordinator) == $koordinator->nama_karyawan ? 'selected' : '' }}>
                                    {{ $koordinator->nama_karyawan }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Item Mutu</strong></div>
                    <div class="card-body">
                        <textarea name="item_mutu" class="form-control" rows="3">{{ old('item_mutu', $sampling_fg->item_mutu) }}</textarea>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $sampling_fg->catatan) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('sampling_fg.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();

        const nama_produk = $('#nama_produk').val();
        const kode_produksi = $('#kode_produksi').val();
        
        // Panggil saat page dimuat untuk mengisi jumlah_box jika kosong
        if (nama_produk && kode_produksi) {
            $.ajax({
                url: "{{ route('get.jumlah.box') }}",
                method: 'GET',
                data: { nama_produk: nama_produk, kode_produksi: kode_produksi },
                success: function (response) {
                    if(!$('#jumlah_box').val() || $('#jumlah_box').val() == 0){
                        $('#jumlah_box').val(response.total_box || 0);
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection