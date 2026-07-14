@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Form Update Pemeriksaan Stuffing Sosis Retort (QC)
            </h4>

            @if ($errors->any())
                <div class="alert alert-danger mb-4 shadow-sm" role="alert">
                    <strong class="d-block mb-2"><i class="bi bi-exclamation-triangle-fill"></i> Terjadi Kesalahan!</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="stuffingForm" action="{{ route('stuffing.update_qc', $stuffing->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS (READONLY) ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data Stuffing</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" value="{{ old('date', $stuffing->date) }}" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <input type="text" class="form-control" value="Shift {{ $stuffing->shift }}" readonly>
                                <input type="hidden" name="shift" id="shiftInput" value="{{ $stuffing->shift }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <input type="text" class="form-control" value="{{ $stuffing->nama_produk }}" readonly>
                                <input type="hidden" name="nama_produk" id="nama_produk" value="{{ $stuffing->nama_produk }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Batch</label>
                                <input type="text" class="form-control" value="{{ $stuffing->mincing->kode_produksi ?? '-' }}" readonly>
                                <input type="hidden" name="kode_produksi" id="kode_produksi" value="{{ $stuffing->kode_produksi }}">
                            </div>

                            <div class="col-md-6 mt-3">
                                <label class="form-label">Exp. Date</label>
                                <input type="date" name="exp_date" id="exp_date" class="form-control" value="{{ old('exp_date', $stuffing->exp_date) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== DATA STUFFING ACCORDION ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Data Stuffing</strong>
                        <button type="button" class="btn btn-dark btn-sm btnTambah" id="btnTambahStuffing">
                            <i class="bi bi-plus-circle"></i> Tambah Data Baru
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="accordionStuffing">
                            
                            @php
                                // Ambil old input jika ada error validasi, jika tidak ambil dari array JSON database
                                $oldStuffing = old('stuffing', is_array($stuffing->data_stuffing) ? $stuffing->data_stuffing : [[]]);
                                $originalCount = is_array($stuffing->data_stuffing) ? count($stuffing->data_stuffing) : 0;
                            @endphp

                            @foreach ($oldStuffing as $index => $item)
                                @php 
                                    // Hitung index untuk menentukan apakah ini data lama yang harus di-readonly
                                    $isReadonly = $index < $originalCount; 
                                @endphp
                                
                                <div class="accordion-item stuffing-item">
                                    <h5 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                                            <i class="bi bi-clipboard-check me-2 text-warning"></i>
                                            Stuffing {{ $index + 1 }} {!! $isReadonly ? '<span class="badge bg-secondary ms-2">Data Existing (Readonly)</span>' : '<span class="badge bg-success ms-2">Baru</span>' !!}
                                        </button>
                                    </h5>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#accordionStuffing">
                                        <div class="accordion-body">
                                            
                                            @if(!$isReadonly)
                                                <div class="text-end mb-3">
                                                    <button type="button" class="btn btn-outline-danger btn-sm btnHapus"><i class="bi bi-trash"></i></button>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nama Mesin</label>
                                                @if($isReadonly)
                                                    <input type="text" class="form-control" value="{{ $item['kode_mesin'] ?? '' }}" readonly>
                                                    <input type="hidden" name="stuffing[{{ $index }}][kode_mesin]" value="{{ $item['kode_mesin'] ?? '' }}">
                                                @else
                                                    <select name="stuffing[{{ $index }}][kode_mesin]" class="form-control" required>
                                                        <option value="">-- Pilih Mesin --</option>
                                                        @foreach($mesins as $m)
                                                            <option value="{{ $m->nama_mesin }}" {{ ($item['kode_mesin'] ?? '') == $m->nama_mesin ? 'selected' : '' }}>{{ $m->nama_mesin }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Jam Mulai</label>
                                                <input type="time" name="stuffing[{{ $index }}][jam_mulai]" class="form-control" value="{{ $item['jam_mulai'] ?? '' }}" {{ $isReadonly ? 'readonly' : '' }} required>
                                            </div>

                                            <hr><h6 class="fw-bold text-primary">Parameter Adonan</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Suhu (°C)</label>
                                                <input type="number" step="0.01" name="stuffing[{{ $index }}][suhu]" class="form-control" value="{{ $item['suhu'] ?? '' }}" {{ $isReadonly ? 'readonly' : '' }}>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Sensori</label>
                                                @if($isReadonly)
                                                    <input type="text" class="form-control" value="{{ $item['sensori'] ?? '-' }}" readonly>
                                                    <input type="hidden" name="stuffing[{{ $index }}][sensori]" value="{{ $item['sensori'] ?? '' }}">
                                                @else
                                                    <select name="stuffing[{{ $index }}][sensori]" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="OK" {{ ($item['sensori'] ?? '') == 'OK' ? 'selected' : '' }}>OK</option>
                                                        <option value="Tidak OK" {{ ($item['sensori'] ?? '') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                    </select>
                                                @endif
                                            </div>

                                            <hr><h6 class="fw-bold text-primary">Parameter Stuffing</h6>
                                            @php
                                                $fields = ['kecepatan_stuffing' => 'Kecepatan Stuffing (/mnt)', 'panjang_pcs' => 'Panjang per pcs (cm)', 'berat_pcs' => 'Berat per pcs (gr)', 'diameter_klip' => 'Diameter Klip (mm)', 'lebar_cassing' => 'Lebar Cassing (mm)'];
                                                $selects = ['kebersihan_seal' => 'Kebersihan Ujung Seal', 'kekuatan_seal' => 'Kekuatan Seal', 'print_kode' => 'Print Kode Production'];
                                            @endphp

                                            @foreach($fields as $key => $label)
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $label }}</label>
                                                    <input type="number" step="0.01" name="stuffing[{{ $index }}][{{ $key }}]" class="form-control" value="{{ $item[$key] ?? '' }}" {{ $isReadonly ? 'readonly' : '' }}>
                                                </div>
                                            @endforeach

                                            @foreach($selects as $key => $label)
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $label }}</label>
                                                    @if($isReadonly)
                                                        <input type="text" class="form-control" value="{{ $item[$key] ?? '-' }}" readonly>
                                                        <input type="hidden" name="stuffing[{{ $index }}][{{ $key }}]" value="{{ $item[$key] ?? '' }}">
                                                    @else
                                                        <select name="stuffing[{{ $index }}][{{ $key }}]" class="form-control">
                                                            <option value="">-- Pilih --</option>
                                                            <option value="OK" {{ ($item[$key] ?? '') == 'OK' ? 'selected' : '' }}>OK</option>
                                                            <option value="Tidak OK" {{ ($item[$key] ?? '') == 'Tidak OK' ? 'selected' : '' }}>Tidak OK</option>
                                                        </select>
                                                    @endif
                                                </div>
                                            @endforeach

                                            <div class="card mt-4">
                                                <div class="card-header bg-light"><strong>Catatan</strong></div>
                                                <div class="card-body">
                                                    <textarea name="stuffing[{{ $index }}][catatan]" class="form-control" rows="3">{{ $item['catatan'] ?? '' }}</textarea>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Update & Simpan</button>
                    <a href="{{ route('stuffing.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== BLANK TEMPLATE UNTUK DINAMIS DATA BARU ===================== --}}
<template id="stuffingTemplate">
    <div class="accordion-item stuffing-item">
        <h5 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{INDEX}">
                <i class="bi bi-clipboard-check me-2 text-warning"></i> Stuffing {NOMOR} <span class="badge bg-success ms-2">Baru</span>
            </button>
        </h5>
        <div id="collapse{INDEX}" class="accordion-collapse collapse show" data-bs-parent="#accordionStuffing">
            <div class="accordion-body">
                <div class="text-end mb-3">
                    <button type="button" class="btn btn-outline-danger btn-sm btnHapus"><i class="bi bi-trash"></i></button>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Mesin</label>
                    <select name="stuffing[{INDEX}][kode_mesin]" class="form-control" required>
                        <option value="">-- Pilih Mesin --</option>
                        @foreach($mesins as $m)
                            <option value="{{ $m->nama_mesin }}">{{ $m->nama_mesin }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="stuffing[{INDEX}][jam_mulai]" class="form-control" required>
                </div>

                <hr><h6 class="fw-bold text-primary">Parameter Adonan</h6>
                <div class="mb-3">
                    <label class="form-label">Suhu (°C)</label>
                    <input type="number" step="0.01" name="stuffing[{INDEX}][suhu]" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sensori</label>
                    <select name="stuffing[{INDEX}][sensori]" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="OK">OK</option>
                        <option value="Tidak OK">Tidak OK</option>
                    </select>
                </div>

                <hr><h6 class="fw-bold text-primary">Parameter Stuffing</h6>
                @foreach($fields as $key => $label)
                    <div class="mb-3">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" step="0.01" name="stuffing[{INDEX}][{{ $key }}]" class="form-control">
                    </div>
                @endforeach

                @foreach($selects as $key => $label)
                    <div class="mb-3">
                        <label class="form-label">{{ $label }}</label>
                        <select name="stuffing[{INDEX}][{{ $key }}]" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="OK">OK</option>
                            <option value="Tidak OK">Tidak OK</option>
                        </select>
                    </div>
                @endforeach

                <div class="card mt-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="stuffing[{INDEX}][catatan]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let stuffingIndex = {{ count($oldStuffing) }};

    document.getElementById('btnTambahStuffing').addEventListener('click', function() {
        let template = document.getElementById('stuffingTemplate').innerHTML;
        let html = template.replace(/{INDEX}/g, stuffingIndex).replace(/{NOMOR}/g, stuffingIndex + 1);
        
        let container = document.createElement('div');
        container.innerHTML = html;
        
        document.getElementById('accordionStuffing').appendChild(container.firstElementChild);
        stuffingIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btnHapus')) {
            e.target.closest('.stuffing-item').remove();
        }
    });

    document.addEventListener('invalid', function(e) {
        let invalidInput = e.target;
        let accordionPane = invalidInput.closest('.accordion-collapse');
        if (accordionPane && !accordionPane.classList.contains('show')) {
            let accordionButton = document.querySelector(`[data-bs-target="#${accordionPane.id}"]`);
            if(accordionButton) accordionButton.classList.remove('collapsed');
            let bsCollapse = new bootstrap.Collapse(accordionPane, { toggle: false });
            bsCollapse.show();
            setTimeout(() => { invalidInput.focus(); invalidInput.classList.add('is-invalid'); }, 350); 
        }
    }, true);
</script>

<style>
    .accordion-item { border-radius: 12px !important; overflow: hidden; margin-bottom: 12px; border: 1px solid #e9ecef; }
    .accordion-button { font-weight: 600; background: #f8f9fa; box-shadow: none !important; }
    .accordion-button:not(.collapsed) { background: #fff3cd; color: #000; }
    .accordion-body { background: #fff; padding: 20px; }
    .form-label { font-weight: 600; margin-bottom: 6px; }
    .form-control { border-radius: 10px; min-height: 42px; }
    .card { border-radius: 14px; overflow: hidden; }
</style>
@endsection