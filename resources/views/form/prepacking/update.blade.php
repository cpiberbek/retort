@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Data Pengecekan Pre Packing
            </h4>

            <form id="prepackingForm" action="{{ route('prepacking.update_qc', $prepacking->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- IDENTIFIKASI --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>IDENTIFIKASI</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ $prepacking->date }}" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                           <div class="col-md-6">
                            <label class="form-label">Nama Varian</label>
                            <input type="text" name="nama_produk" class="form-control" value="{{ $prepacking->nama_produk }}" readonly>
                        </div>
                        @php
                            $kode = \App\Models\Mincing::where('uuid', $prepacking->kode_produksi)->value('kode_produksi');
                        @endphp

                        <div class="col-md-6">
                            <label class="form-label">Kode Batch</label>
                            <input type="text" class="form-control" value="{{ $kode }}" readonly>
                            <input type="hidden" name="kode_produksi" value="{{ $prepacking->kode_produksi }}">
                            <small id="kodeError" class="text-danger d-none"></small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PENGECEKAN SUHU --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white"><strong>Pengecekan Suhu</strong></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <tbody>
                                <tr>
                                    <td class="text-start">No. Conveyor</td>
                                    <td>
                                        <input type="text" name="conveyor" value="{{ $prepacking->conveyor }}" class="form-control form-control-sm text-center" {{ $prepacking->conveyor ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                @for($i=1; $i<=3; $i++)
                                @php
                                $value = $suhuData['suhu_'.$i] ?? '';
                                @endphp
                                <tr>
                                    @if($i==1)
                                    <td rowspan="3" class="text-center align-middle">Suhu Varian (°C)</td>
                                    @endif
                                    <td>
                                        <input type="number" name="suhu_produk[suhu_{{ $i }}]" value="{{ $value }}" id="suhu_{{ $i }}" class="form-control form-control-sm text-center" step="0.01" min="0" {{ $value != '' ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- KONDISI PRODUK --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white"><strong>Kondisi Varian</strong></div>
                <div class="card-body">

                    {{-- Air --}}
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">Bagian</th>
                                    <th colspan="2">Air (%)</th>
                                </tr>
                                <tr>
                                    <th>Basah</th>
                                    <th>Kering</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['ujung','seal'] as $bagian)
                                <tr>
                                    <td class="text-start">{{ ucfirst($bagian) }}</td>
                                    <td>
                                        <input type="number" name="kondisi_produk[basah_air_{{ $bagian }}]" id="basah_air_{{ $bagian }}" value="{{ $kondisiData['basah_air_'.$bagian] ?? '' }}" class="form-control form-control-sm text-center" min="0" {{ ($kondisiData['basah_air_'.$bagian] ?? '') != '' ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <input type="number" name="kondisi_produk[kering_air_{{ $bagian }}]" id="kering_air_{{ $bagian }}" value="{{ $kondisiData['kering_air_'.$bagian] ?? '' }}" class="form-control form-control-sm text-center"  min="0" {{ ($kondisiData['kering_air_'.$bagian] ?? '') != '' ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="text-start">Total</td>
                                    <td><input type="number" id="basah_air_total" value="0" class="form-control form-control-sm text-center"  readonly></td>
                                    <td><input type="number" id="kering_air_total" value="0" class="form-control form-control-sm text-center" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Minyak --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">Bagian</th>
                                    <th colspan="2">Minyak (%)</th>
                                </tr>
                                <tr>
                                    <th>Basah</th>
                                    <th>Kering</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['ujung','seal'] as $bagian)
                                <tr>
                                    <td class="text-start">{{ ucfirst($bagian) }}</td>
                                    <td>
                                        <input type="number" name="kondisi_produk[basah_minyak_{{ $bagian }}]" id="basah_minyak_{{ $bagian }}" value="{{ $kondisiData['basah_minyak_'.$bagian] ?? '' }}" class="form-control form-control-sm text-center" min="0" {{ ($kondisiData['basah_minyak_'.$bagian] ?? '') != '' ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <input type="number" name="kondisi_produk[kering_minyak_{{ $bagian }}]" id="kering_minyak_{{ $bagian }}" value="{{ $kondisiData['kering_minyak_'.$bagian] ?? '' }}" class="form-control form-control-sm text-center"   min="0" {{ ($kondisiData['kering_minyak_'.$bagian] ?? '') != '' ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="text-start">Total</td>
                                    <td><input type="number" id="basah_minyak_total" value="0" class="form-control form-control-sm text-center" readonly></td>
                                    <td><input type="number" id="kering_minyak_total" value="0" class="form-control form-control-sm text-center" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- BERAT PRODUK --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white"><strong>Berat Varian per Pcs/Toples</strong></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead>
                                <tr>
                                    <th>Pcs</th>
                                    <th>Toples (berat kotor)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i=1; $i<=3; $i++)
                                <tr>
                                    <td>
                                        <input type="number" name="berat_produk[pcs_{{ $i }}]" id="pcs_{{ $i }}" value="{{ $beratData['pcs_'.$i] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"  min="0" {{ ($beratData['pcs_'.$i] ?? '') != '' ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <input type="number" name="berat_produk[toples_{{ $i }}]" id="toples_{{ $i }}" value="{{ $beratData['toples_'.$i] ?? '' }}" class="form-control form-control-sm text-center" step="0.01"   min="0" {{ ($beratData['toples_'.$i] ?? '') != '' ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- CATATAN --}}
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>Catatan</strong></div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $prepacking->catatan ?? '') }}</textarea>
                </div>
            </div>

            {{-- TOMBOL --}}
            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Update</button>
                <a href="{{ route('prepacking.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){

    // ===== Fungsi hitung total =====
        function hitungTotal(type){
            let ujung = parseFloat($(`#${type}_ujung`).val()) || 0;
            let seal  = parseFloat($(`#${type}_seal`).val()) || 0;
            $(`#${type}_total`).val(ujung + seal);
        }

    // Hitung total kondisi produk
        ['basah_air','kering_air','basah_minyak','kering_minyak'].forEach(type => {
            hitungTotal(type);
            $(`#${type}_ujung, #${type}_seal`).on('input', function(){ hitungTotal(type); });
        });

    // Hitung total berat produk
        function hitungTotalBerat(col){
            let total = 0;
            for(let i=1;i<=3;i++){
                total += parseFloat($(`#${col}_${i}`).val()) || 0;
            }
            $(`#${col}_total`).val(total);
        }
        ['pcs','toples'].forEach(col => {
            hitungTotalBerat(col);
            for(let i=1;i<=3;i++){
                $(`#${col}_${i}`).on('input', function(){ hitungTotalBerat(col); });
            }
        });

    });
</script>
@endsection
