@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Sampel Retain</h4>
            <form id="retainForm" method="POST" action="{{ route('retain_rte.store') }}">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label class="form-label">Nama Varian</label>
                            <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama varian..." required>
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}"
                                    {{ old('nama_produk', $data->nama_produk ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                                    {{ $produk->nama_produk }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Batch</label>
                            <input type="text" name="kode_produksi" id="kode_produksi" class="form-control" maxlength="10" required>
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
                    {{-- Note Petunjuk Checkbox --}}
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
                            <tr>
                                <td>
                                    <input type="month" name="analisa[{{ $i }}][bulan]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="number" name="analisa[{{ $i }}][fisik]" class="form-control form-control-sm fisik" min="1">
                                </td>
                                <td>
                                    <input type="number" name="analisa[{{ $i }}][aroma]" class="form-control form-control-sm aroma" min="1">
                                </td>
                                <td>
                                    <input type="number" name="analisa[{{ $i }}][rasa]" class="form-control form-control-sm rasa" min="1">
                                </td>
                                <td>
                                    <input type="number" name="analisa[{{ $i }}][rata_score]" class="form-control form-control-sm rata_score" step="0.1" readonly>
                                </td>
                                <td>
                                    <input type="text" name="analisa[{{ $i }}][cemaran]" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="text" name="analisa[{{ $i }}][release]" class="form-control form-control-sm" readonly>
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
            <button class="btn btn-success w-auto">
                <i class="bi bi-save"></i> Simpan
            </button>
            <a href="{{ route('retain_rte.index') }}" class="btn btn-secondary w-auto">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
</div>
</div>

<!-- jQuery dulu (wajib) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap-Select CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");

        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');

        dateInput.value = `${yyyy}-${mm}-${dd}`;
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('table tbody tr').forEach(function(row) {
            const fisik  = row.querySelector('.fisik');
            const aroma  = row.querySelector('.aroma');
            const rasa   = row.querySelector('.rasa');
            const rata   = row.querySelector('.rata_score');
            const release = row.querySelector('input[name*="[release]"]');

            function hitungRataDanRelease() {
                const vFisik = parseFloat(fisik.value) || 0;
                const vAroma = parseFloat(aroma.value) || 0;
                const vRasa  = parseFloat(rasa.value) || 0;

                if (fisik.value || aroma.value || rasa.value) {
                    const avg = ((vFisik + vAroma + vRasa) / 3).toFixed(1);
                    rata.value = avg;

              // Set Keterangan berdasarkan rata
                    if (avg >= 1 && avg <= 1.5) {
                        release.value = "Tidak Release";
                        release.style.color = "red";
                        release.style.fontWeight = "bold";
                    } else if (avg >= 1.6 && avg <= 3) {
                        release.value = "Release";
                        release.style.color = "green";
                        release.style.fontWeight = "bold";
                    } else {
                        release.value = "";
                        release.style.color = "";
                        release.style.fontWeight = "";
                    }

                } else {
                    rata.value = '';
                    release.value = '';
                }
            }

            [fisik, aroma, rasa].forEach(el => el.addEventListener('input', hitungRataDanRelease));
        });
    });
</script>
<script>
    $(document).ready(function(){ 
    // Validasi kode produksi
        const kodeInput = $('#kode_produksi');
        const kodeError = $('#kodeError');
        const form = $('#retainForm');

        function validateKode() {
            let value = kodeInput.val().toUpperCase().replace(/\s+/g, '');
            kodeInput.val(value);
            kodeError.text('').addClass('d-none');

            if(value.length !== 10) {
                kodeError.text('Kode batch harus terdiri dari 10 karakter').removeClass('d-none');
                return false;
            }

            if(!/^[A-Z0-9]+$/.test(value)) {
                kodeError.text('Kode batch hanya boleh huruf besar dan angka').removeClass('d-none');
                return false;
            }

            if(!/^[A-L]$/.test(value.charAt(1))) {
                kodeError.text('Karakter ke-2 harus huruf bulan (A-L)').removeClass('d-none');
                return false;
            }

            let hari = parseInt(value.substr(2,2),10);
            if(isNaN(hari) || hari < 1 || hari > 31) {
                kodeError.text('Karakter ke-3 dan ke-4 harus tanggal valid (01-31)').removeClass('d-none');
                return false;
            }

            return true;
        }

        kodeInput.on('input', validateKode);

        form.on('submit', function(e){
            if(!validateKode()){
                e.preventDefault();
                alert('Kode batch tidak valid! Periksa kembali sebelum menyimpan.');
                kodeInput.focus();
            }
        });
    });
</script>

<style>
/* Supaya tabel lebih lebar */
.table {
  width: 100%;
  table-layout: auto; /* kolom auto */
}

.table th,
.table td {
  padding: 0.75rem 0.75rem; /* Lebih besar dari sebelumnya */
  vertical-align: middle;
  font-size: 0.9rem;
}

/* Kolom input kecil tapi fleksibel */
.table input.form-control-sm {
  width: 100%;
  min-width: 80px;
  font-size: 0.9rem;
}

/* Input group supaya nggak mepet */
.input-group-sm > .form-control,
.input-group-sm > .input-group-text {
  height: calc(2em + 0.5rem + 2px);
  font-size: 0.9rem;
}

/* Tabel kecil (nama bahan + suhu) supaya nyaman */
.table td table {
  width: 100%;
}

.table td table th,
.table td table td {
  padding: 0.5rem;
  font-size: 0.85rem;
}

/* Buat judul header lebih bold dan jelas */
.table thead th {
  background-color: #f8f9fa;
  font-weight: 600;
  text-align: center;
}
.table-sm th, .table-sm td {
  padding: 0.5rem;
  vertical-align: middle;
}
.input-group-sm>.form-control,
.input-group-sm>.input-group-text {
  height: calc(1.5em + 0.5rem + 2px);
  font-size: 0.875rem;
}
</style>

@endsection
