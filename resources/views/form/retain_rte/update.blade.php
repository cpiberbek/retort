@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4><i class="bi bi-pencil-square"></i> Form Edit Pemeriksaan Sampel Retain</h4>
            <form id="retainForm" method="POST" action="{{ route('retain_rte.update_qc', $retain_rte->uuid) }}">
                @csrf
                @method('PUT')

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control"
                                value="{{ old('date', $retain_rte->date) }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <input type="text" name="nama_produk" id="nama_produk" class="form-control" value="{{ old('nama_produk', $retain_rte->nama_produk) }}" readonly>
                            </div>
                            @php
                            $kode = \App\Models\Mincing::where('uuid', $retain_rte->kode_produksi)->value('kode_produksi');
                        @endphp

                        <div class="col-md-6">
                            <label class="form-label">Kode Batch</label>
                            <input type="text" class="form-control" value="{{ $kode }}" readonly>
                            <input type="hidden" name="kode_produksi" value="{{ $retain_rte->kode_produksi }}">
                            <small id="kodeError" class="text-danger d-none"></small>
                        </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3"> 
                    <div class="card-header bg-info text-white">
                        <strong>Pemeriksaan</strong>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">

                            {{-- Note Petunjuk --}}
                            <div class="alert alert-warning mt-2 py-3 px-3" style="font-size: 0.9rem;">
                                <i class="bi bi-info-circle"></i>
                                <strong> Keterangan Score Orlep:</strong>
                                <ul class="mb-2 mt-2">
                                    <li>1. Sangat Tidak</li>
                                    <li>2. Biasa</li>
                                    <li>3. Sangat</li>
                                </ul>
                                <i class="bi bi-info-circle"></i>
                                <strong>Keterangan Hasil Score:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>1 – 1.5 : Tidak Release</li>
                                    <li>1.6 – 3 : Release</li>
                                </ul>
                            </div>

                            <table class="table table-bordered table-sm mb-0 text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th colspan="7">Analisa Sampel Retain</th>
                                    </tr>
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Fisik/Tekstur</th>
                                        <th>Aroma</th>
                                        <th>Rasa</th>
                                        <th>Average Score</th>
                                        <th>Cemaran</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @for ($i = 0; $i < 12; $i++)
                                    @php
                                    $analisa = $retainData[$i] ?? null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="month" name="analisa[{{ $i }}][bulan]"
                                            class="form-control form-control-sm"
                                            value="{{ old("analisa.$i.bulan", $analisa['bulan'] ?? '') }}"
                                            {{ !empty($analisa['bulan']) ? 'readonly' : '' }}>
                                        </td>
                                        <td>
                                            <input type="number" name="analisa[{{ $i }}][fisik]"
                                            class="form-control form-control-sm fisik"
                                            min="1" 
                                            value="{{ old("analisa.$i.fisik", $analisa['fisik'] ?? '') }}"
                                            {{ !empty($analisa['fisik']) ? 'readonly' : '' }}>
                                        </td>
                                        <td>
                                            <input type="number" name="analisa[{{ $i }}][aroma]"
                                            class="form-control form-control-sm aroma"
                                            min="1" 
                                            value="{{ old("analisa.$i.aroma", $analisa['aroma'] ?? '') }}"
                                            {{ !empty($analisa['aroma']) ? 'readonly' : '' }}>
                                        </td>
                                        <td>
                                            <input type="number" name="analisa[{{ $i }}][rasa]"
                                            class="form-control form-control-sm rasa"
                                            min="1" 
                                            value="{{ old("analisa.$i.rasa", $analisa['rasa'] ?? '') }}"
                                            {{ !empty($analisa['rasa']) ? 'readonly' : '' }}>
                                        </td>
                                        <td>
                                            <input type="number" name="analisa[{{ $i }}][rata_score]"
                                            class="form-control form-control-sm rata_score"
                                            step="0.1" readonly
                                            value="{{ old("analisa.$i.rata_score", $analisa['rata_score'] ?? '') }}">
                                        </td>
                                        <td>
                                            <input type="text" name="analisa[{{ $i }}][cemaran]"
                                            class="form-control form-control-sm"
                                            value="{{ old("analisa.$i.cemaran", $analisa['cemaran'] ?? '') }}"
                                            {{ !empty($analisa['cemaran']) ? 'readonly' : '' }}>
                                        </td>
                                        <td>
                                            <input type="text" name="analisa[{{ $i }}][release]"
                                            class="form-control form-control-sm" readonly
                                            value="{{ old("analisa.$i.release", $analisa['release'] ?? '') }}">
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-primary w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('retain_rte.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Select -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('table tbody tr').each(function() {
            const fisik  = $(this).find('.fisik');
            const aroma  = $(this).find('.aroma');
            const rasa   = $(this).find('.rasa');
            const rata   = $(this).find('.rata_score');
            const release = $(this).find('input[name*="[release]"]');

            function hitungRataDanRelease() {
                const vFisik = parseFloat(fisik.val()) || 0;
                const vAroma = parseFloat(aroma.val()) || 0;
                const vRasa  = parseFloat(rasa.val()) || 0;

                if (fisik.val() || aroma.val() || rasa.val()) {
                    const avg = ((vFisik + vAroma + vRasa) / 3).toFixed(1);
                    rata.val(avg);

                    if (avg >= 1 && avg <= 1.5) {
                        release.val("Tidak Release").css({"color": "red", "font-weight": "bold"});
                    } else if (avg >= 1.6 && avg <= 3) {
                        release.val("Release").css({"color": "green", "font-weight": "bold"});
                    } else {
                        release.val("").css({"color": "", "font-weight": ""});
                    }
                } else {
                    rata.val('');
                    release.val('');
                }
            }

            if (!fisik.prop('readonly')) {
                fisik.on('input', hitungRataDanRelease);
                aroma.on('input', hitungRataDanRelease);
                rasa.on('input', hitungRataDanRelease);
            }

            hitungRataDanRelease();
        });
    });
</script>

<style>
    .table {
      width: 100%;
      table-layout: auto;
  }
  .table th, .table td {
      padding: 0.75rem;
      vertical-align: middle;
      font-size: 0.9rem;
  }
  .table input.form-control-sm {
      width: 100%;
      min-width: 80px;
      font-size: 0.9rem;
  }
  .table thead th {
      background-color: #f8f9fa;
      font-weight: 600;
      text-align: center;
  }
  .table-sm th, .table-sm td {
      padding: 0.5rem;
      vertical-align: middle;
  }
</style>
@endsection
