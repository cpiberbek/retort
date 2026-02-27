@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Edit Pemeriksaan Washing - Drying
            </h4>

            <form id="washingForm" action="{{ route('washing.update_qc', $washing->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- IDENTIFIKASI --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>IDENTIFIKASI</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date_display" class="form-control" 
                                value="{{ old('date', $washing->date) }}" 
                                @if($washing->date) readonly style="background-color:#e9ecef;cursor:not-allowed;" @endif
                                required>
                                @if($washing->date)
                                <input type="hidden" name="date" value="{{ $washing->date }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift_display" class="form-control" 
                                @if($washing->shift) style="pointer-events:none;background-color:#e9ecef;" @endif>
                                <option value="1" {{ $washing->shift == 1 ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ $washing->shift == 2 ? 'selected' : '' }}>Shift 2</option>
                                <option value="3" {{ $washing->shift == 3 ? 'selected' : '' }}>Shift 3</option>
                            </select>
                            @if($washing->shift)
                            <input type="hidden" name="shift" value="{{ $washing->shift }}">
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                     <div class="col-md-6">
                        <label class="form-label">Nama Varian</label>
                        <input type="text" name="nama_produk" class="form-control" value="{{ $washing->nama_produk }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kode Batch</label>
                        <input type="text" name="kode_produksi" id="kode_produksi" maxlength="10" class="form-control" 
                        value="{{ old('kode_produksi', $washing->kode_produksi) }}"
                        @if($washing->kode_produksi) readonly style="background-color:#e9ecef;cursor:not-allowed;" @endif
                        required>
                        <small id="kodeError" class="text-danger d-none"></small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Pukul</label>
                        <input type="time" name="pukul_display" class="form-control"
                        value="{{ old('pukul', $washing->pukul) }}"
                        @if($washing->pukul) readonly style="background-color:#e9ecef;cursor:not-allowed;" @endif
                        required>
                        @if($washing->pukul)
                        <input type="hidden" name="pukul" value="{{ $washing->pukul }}">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- PENGECEKAN --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white"><strong>Pengecekan</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <tbody>
                            @php
                                $fields = [
                                    // 'nama_kolom_db' => 'Label Tampilan'
                                    'panjang_produk'  => 'Panjang Varian', 
                                    'diameter_produk' => 'Diameter Varian', 
                                    'airtrap'         => 'Airtrap', 
                                    'lengket'         => 'Lengket', 
                                    'sisa_adonan'     => 'Sisa Adonan', 
                                    'kebocoran'       => 'Kebocoran', 
                                    'kekuatan_seal'   => 'Kekuatan Seal', 
                                    'print_kode'      => 'Print Kode'
                                ];
                            @endphp
                            @foreach($fields as $column => $label)
                            <tr>
                                {{-- Tampilan untuk User menggunakan $label --}}
                                <td class="text-left align-middle">{{ $label }}</td>
                                
                                <td>
                                    {{-- Logika Backend tetap menggunakan $column --}}
                                    @if(in_array($column, ['airtrap','lengket','sisa_adonan','kebocoran','kekuatan_seal','print_kode']))
                                        <select name="{{ $column }}_display" class="form-control form-control-sm text-center" 
                                            @if($washing->$column) style="pointer-events:none;background-color:#e9ecef;" @endif>
                                            
                                            @php
                                            $options = (in_array($column, ['kebocoran','kekuatan_seal','print_kode'])) 
                                                        ? ['Ok','Tidak Ok'] 
                                                        : ['Ada','Tidak Ada'];
                                            @endphp

                                            @foreach($options as $opt)
                                                <option value="{{ $opt }}" {{ $washing->$column == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                            @endforeach
                                        </select>

                                        @if($washing->$column)
                                            <input type="hidden" name="{{ $column }}" value="{{ $washing->$column }}">
                                        @endif
                                    @else
                                        <input type="number" name="{{ $column }}" class="form-control form-control-sm text-center" step="0.01" min="0"
                                            value="{{ old($column, $washing->$column) }}"
                                            @if($washing->$column) readonly style="background-color:#e9ecef;cursor:not-allowed;" @endif>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PC KLEER --}}
    <div class="card mb-4">
        <div class="card-header bg-info text-white"><strong>PC Kleer</strong></div>
        <div class="card-body">
           <div class="alert alert-danger mt-2 py-3 px-3" style="font-size: 0.9rem;">
            <i class="bi bi-info-circle"></i>
            <strong> Standar Pemeriksaan:</strong>
            <ul class="mb-2 mt-2">
                <li>Suhu PC Kleer : 46 ± 3 °C</li>
                <li>Kons. PC Kleer : 0.7% (ayam); 1% (sapi dan RTE); 0.8% (cuci ulang)</li>
            </ul>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <tbody>
                    @php
                    $pckleer_fields = ['konsentrasi_pckleer','suhu_pckleer_1','suhu_pckleer_2','ph_pckleer','kondisi_air_pckleer'];
                    @endphp
                    @foreach($pckleer_fields as $field)
                    <tr>
                        <td class="text-left align-middle">{{ ucwords(str_replace('_',' ',$field)) }}</td>
                        <td>
                            @if($field=='kondisi_air_pckleer')
                            <select name="{{ $field }}_display" class="form-control form-control-sm text-center" 
                            @if($washing->$field) style="pointer-events:none;background-color:#e9ecef;" @endif>
                            <option value="OK" {{ $washing->$field=='OK' ? 'selected' : '' }}>OK</option>
                            <option value="Tidak OK" {{ $washing->$field=='Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                        </select>
                        @if($washing->$field)
                        <input type="hidden" name="{{ $field }}" value="{{ $washing->$field }}">
                        @endif
                        @else
                        <input type="number" name="{{ $field }}" class="form-control form-control-sm text-center" step="0.01" min="0"
                        value="{{ old($field, $washing->$field) }}"
                        @if($washing->$field) readonly style="background-color:#e9ecef;cursor:not-allowed;" @endif>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

{{-- Pottasium Sorbate --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white"><strong>Pottasium Sorbate</strong></div>
    <div class="card-body">
        <div class="alert alert-danger mt-2 py-3 px-3" style="font-size: 0.9rem;">
            <i class="bi bi-info-circle"></i>
            <strong> Standar Pemeriksaan:</strong>
            <ul class="mb-2 mt-2">
                <li>Kons. Pottasium Sorbate : 0.15%</li>
            </ul>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <tbody>
                    @php
                    $pottasium_fields = ['konsentrasi_pottasium','suhu_pottasium','ph_pottasium','kondisi_pottasium'];
                    @endphp
                    @foreach($pottasium_fields as $field)
                    <tr>
                        <td class="text-left align-middle">{{ ucwords(str_replace('_',' ',$field)) }}</td>
                        <td>
                            @if($field=='kondisi_pottasium')
                            <select name="{{ $field }}_display" class="form-control form-control-sm text-center" 
                            @if($washing->$field) style="pointer-events:none;background-color:#e9ecef;" @endif>
                            <option value="OK" {{ $washing->$field=='OK' ? 'selected' : '' }}>OK</option>
                            <option value="Tidak OK" {{ $washing->$field=='Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                        </select>
                        @if($washing->$field)
                        <input type="hidden" name="{{ $field }}" value="{{ $washing->$field }}">
                        @endif
                        @else
                        <input type="number" name="{{ $field }}" class="form-control form-control-sm text-center" step="0.01" min="0"
                        value="{{ old($field, $washing->$field) }}"
                        @if($washing->$field) readonly style="background-color:#e9ecef;cursor:not-allowed;" @endif>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

{{-- Suhu & Speed --}}
<div class="card mb-4">
    <div class="card-header bg-info text-white"><strong>Suhu & Speed Conveyor</strong></div>
    <div class="card-body">
        <div class="alert alert-danger mt-2 py-3 px-3" style="font-size: 0.9rem;">
            <i class="bi bi-info-circle"></i>
            <strong> Standar Pemeriksaan:</strong>
            <ul class="mb-2 mt-2">
                <li>Suhu Heater   : 125 - 135 °C</li>                           
            </ul>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <tbody>
                    @php $suhu_fields = ['suhu_heater','speed_1','speed_2','speed_3','speed_4']; @endphp
                    @foreach($suhu_fields as $field)
                    <tr>
                        <td class="text-left align-middle">{{ ucwords(str_replace('_',' ',$field)) }}</td>
                        <td>
                            <input type="number" name="{{ $field }}" class="form-control form-control-sm text-center" step="0.01" min="0"
                            value="{{ old($field, $washing->$field) }}"
                            @if($washing->$field) readonly style="background-color:#e9ecef;cursor:not-allowed;" @endif>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Catatan --}}
<div class="card mb-4">
    <div class="card-header bg-light"><strong>Catatan</strong></div>
    <div class="card-body">
        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $washing->catatan ?? '') }}</textarea>
    </div>
</div>

{{-- Tombol --}}
<div class="d-flex justify-content-between mt-3">
    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('washing.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
</form>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>

    // Validasi kode produksi hanya jika tidak readonly
    $(document).ready(function(){ 
        const kodeInput = $('#kode_produksi');
        const kodeError = $('#kodeError');
        const form = $('#washingForm');

        function validateKode() {
            if(kodeInput.is('[readonly]')) return true;

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
@endsection
