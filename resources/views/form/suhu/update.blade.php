@extends('layouts.app')

@section('content')

@push('styles')
<style>
    /* Styling khusus untuk form input bergaya modern & premium */
    .form-control-solid, .form-select-solid {
        background-color: #f4f6f8;
        border: 1px solid transparent;
        color: #3f4254;
        transition: all 0.2s ease;
        width: 100%; 
        height: 48px; 
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    
    /* Styling khusus untuk input Readonly agar terlihat terkunci namun tetap premium */
    .form-control-solid[readonly], .form-select-solid[readonly], .form-select-solid[disabled] {
        background-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
        border-color: #dee2e6;
        opacity: 0.8;
    }

    .form-control-solid:focus:not([readonly]), .form-select-solid:focus:not([disabled]) {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
        outline: none;
    }
    
    /* Styling tabel untuk desktop */
    .table-modern th {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #a1a5b7;
        font-weight: 600;
        border-bottom: 1px dashed #e4e6ef;
        white-space: nowrap; 
    }
    .table-modern td {
        border-bottom: 1px dashed #e4e6ef;
        vertical-align: middle;
    }
    
    /* Kustomisasi label agar lebih proporsional */
    .label-premium {
        font-size: 0.8rem;
        font-weight: 500;
        color: #7e8299;
        margin-bottom: 0.4rem;
        display: block;
    }

    /* Memperbaiki visibilitas badge batas standar */
    .badge-standar {
        background-color: #f1f5f9; 
        color: #475569; 
        border: 1px solid #e2e8f0;
        padding: 0.4rem 1rem;
        border-radius: 50rem;
        font-weight: 600;
        display: inline-block;
        font-size: 0.85rem;
    }

    /* Efek hover untuk tombol Batalkan / Kembali */
    .btn-batalkan {
        background-color: #f3f4f6;
        color: #6b7280;
        transition: all 0.2s ease;
    }
    .btn-batalkan:hover {
        background-color: #e5e7eb;
        color: #374151;
    }

    /* Efek hover untuk tombol Update (Menggunakan warna Warning/Yellow yang premium) */
    .btn-update {
        background-color: #f59e0b; 
        border-color: #f59e0b;
        color: white;
        transition: all 0.2s ease;
    }
    .btn-update:hover {
        background-color: #d97706;
        border-color: #d97706;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);
    }
    
    /* Tombol disable agar terlihat jelas tidak bisa diklik */
    .btn-update:disabled {
        background-color: #cbd5e1;
        border-color: #cbd5e1;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
        color: #64748b;
    }

    /* Mengatur responsivitas tombol aksi */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem; 
    }
    .action-buttons .btn {
        width: 100%;
    }

    /* Media query layar besar (Desktop & Tablet landscape) */
    @media (min-width: 576px) { 
        .action-buttons {
            flex-direction: row;
            justify-content: flex-end;
            align-items: center;
        }
        .action-buttons .btn {
            width: auto;
        }
    }

    /* =======================================================
       CSS AJAIB: MENGUBAH TABEL MENJADI CARD DI LAYAR HP
       ======================================================= */
    @media (max-width: 767.98px) {
        .table-responsive {
            border: none;
            overflow-x: hidden; 
        }
        .table-modern {
            min-width: 100% !important; 
        }
        .table-modern thead {
            display: none; 
        }
        .table-modern tbody tr {
            display: block;
            border: 1px solid #e4e6ef;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .table-modern tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #e4e6ef;
            padding: 0.75rem 0;
            text-align: right !important; 
        }
        .table-modern tbody td:last-child {
            border-bottom: none;
            flex-direction: column;
            align-items: stretch;
            text-align: left !important;
            padding-bottom: 0.25rem;
        }
        .table-modern tbody td::before {
            content: attr(data-label);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #a1a5b7;
            text-align: left;
        }
        .table-modern tbody td:last-child::before {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

<div class="container-fluid py-4 max-w-7xl mx-auto">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="bi bi-pencil-square text-warning me-2"></i> Update Pemeriksaan Suhu (QC)
            </h4>
            <p class="text-muted small mb-0">Perbarui data form di bawah ini jika diperlukan.</p>
        </div>
        <a href="{{ route('suhu.index') }}" class="btn btn-sm btn-light border shadow-sm fw-medium rounded-pill px-3 align-self-start align-self-md-auto btn-batalkan d-inline-block w-auto">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form id="suhuForm" action="{{ route('suhu.update_qc', $suhu->uuid) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ===================== IDENTITAS DATA ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <h6 class="fw-bold mb-4 text-dark d-flex align-items-center">
                    <i class="bi bi-clock text-primary me-2 fs-5"></i> Identitas Jadwal (Terkunci)
                </h6>
                
                <div class="row g-3 g-md-4">
                    <div class="col-12 col-md-4">
                        <label class="label-premium">Tanggal Pemeriksaan</label>
                        <input type="date" name="date" class="form-control form-control-solid rounded-3" value="{{ $suhu->date }}" readonly>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="label-premium">Shift Kerja</label>
                        {{-- Menggunakan hidden input karena select disabled tidak ikut tersubmit formnya --}}
                        <input type="hidden" name="shift" value="{{ $suhu->shift }}">
                        <select class="form-select form-select-solid rounded-3" disabled>
                            <option value="1" {{ $suhu->shift == 1 ? 'selected' : '' }}>Shift 1 (Pagi)</option>
                            <option value="2" {{ $suhu->shift == 2 ? 'selected' : '' }}>Shift 2 (Sore)</option>
                            <option value="3" {{ $suhu->shift == 3 ? 'selected' : '' }}>Shift 3 (Malam)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="label-premium">Waktu (Pukul)</label>
                        {{-- Hidden input agar nilai tetap tersubmit meskipun select-nya disabled --}}
                        <input type="hidden" name="pukul" value="{{ $suhu->pukul }}">
                        <select class="form-select form-select-solid rounded-3" disabled>
                            @for ($h = 0; $h < 24; $h++)
                                @php $optVal = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                                <option value="{{ $optVal }}" {{ substr($suhu->pukul, 0, 5) === $optVal ? 'selected' : '' }}>
                                    {{ $optVal }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== INPUT SUHU AREA ===================== --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold mb-4 text-dark d-flex align-items-center">
                        <i class="bi bi-thermometer-half text-info me-2 fs-5"></i> Edit Suhu Ruangan
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-borderless table-modern mb-0" style="min-width: 600px;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th style="width: 35%">Area Pengukuran</th>
                                    <th class="text-center" style="width: 25%">Batas Standar (°C)</th>
                                    <th style="width: 35%">Hasil Pengukuran (°C)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($area_suhus as $index => $area)
                                    @php
                                        // Ambil nilai lama berdasarkan nama area sesuai struktur data form Edit
                                        $matched = $suhuData[$area->area] ?? null;
                                        $nilai = $matched['suhu'] ?? '';
                                    @endphp
                                    <tr>
                                        <td data-label="No" class="text-center text-muted fw-bold">{{ $index + 1 }}</td>

                                        <td data-label="Area Pengukuran">
                                            <input type="hidden" name="hasil_suhu[{{ $index }}][area]"
                                                value="{{ $area->area }}">
                                            <span class="fw-medium text-dark">{{ $area->area }}</span>
                                        </td>

                                        <td data-label="Batas Standar" class="text-center">
                                            @if ($area->standar_min !== null && $area->standar_max !== null)
                                                <span class="badge-standar">
                                                    {{ $area->standar_min }}°C - {{ $area->standar_max }}°C
                                                </span>
                                            @else
                                                <span class="text-muted small fw-medium text-danger"><i
                                                        class="bi bi-exclamation-triangle"></i> Standar Kosong</span>
                                            @endif
                                        </td>

                                        <td data-label="Hasil Pengukuran">
                                            <div class="position-relative w-100">
                                                {{-- Diubah ke type text inputmode decimal agar support tanda minus --}}
                                                <input type="text" inputmode="decimal"
                                                    name="hasil_suhu[{{ $index }}][nilai]"
                                                    value="{{ $nilai }}"
                                                    class="form-control form-control-solid suhu-input check-old rounded-3"
                                                    data-min="{{ $area->standar_min }}"
                                                    data-max="{{ $area->standar_max }}"
                                                    placeholder="Masukkan suhu"
                                                    {{ $nilai !== '' && $nilai !== null ? 'readonly' : '' }}>

                                                <div class="text-danger warning-msg d-none mt-1"
                                                    style="font-size: 0.75rem; font-weight: 500;">
                                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Suhu di luar standar!
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        {{-- ===================== INPUT SUHU RH ===================== --}}    
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Edit RH</h5>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Area</th>
                                <th>Standar</th>
                                <th>Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($area_suhus->filter(function ($area) {
                                return $area->rh_min !== null && $area->rh_max !== null;
                            })->values() as $index => $area)
                                @php
                                    $matched = $suhuData[$area->area] ?? null;
                                    $nilai_rh = $matched['rh'] ?? '';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        <input type="hidden" name="hasil_rh[{{ $index }}][area]"
                                            value="{{ $area->area }}">
                                        {{ $area->area }}
                                    </td>

                                    <td>
                                        @if ($area->rh_min !== null && $area->rh_max !== null)
                                            {{ $area->rh_min }} - {{ $area->rh_max }} %
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        <input type="text"
                                            name="hasil_rh[{{ $index }}][nilai]"
                                            value="{{ $nilai_rh }}"
                                            class="form-control rh-input check-old {{ $area->rh_min === null ? 'bg-light text-muted' : '' }}"
                                            data-min="{{ $area->rh_min }}"
                                            data-max="{{ $area->rh_max }}"
                                            {{ $area->rh_min === null ? 'disabled' : '' }}
                                            {{ $nilai_rh !== '' && $nilai_rh !== null ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        {{-- ===================== CATATAN & KETERANGAN ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <h6 class="fw-bold mb-4 text-dark d-flex align-items-center">
                    <i class="bi bi-journal-text text-secondary me-2 fs-5"></i> Informasi Tambahan
                </h6>
                
                <div class="row g-3 g-md-4">
                    <div class="col-12 col-md-6">
                        <label class="label-premium">Keterangan (Opsional)</label>
                        <textarea
                            name="keterangan"
                            class="form-control form-control-solid rounded-3"
                            rows="3"
                            placeholder="Ketik keterangan di sini..."
                            {{ !empty($suhu->keterangan) ? 'readonly' : '' }}>{{ $suhu->keterangan }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="label-premium">Catatan Penting (Opsional)</label>
                        <textarea
                            name="catatan"
                            class="form-control form-control-solid rounded-3"
                            rows="3"
                            placeholder="Ketik catatan di sini..."
                            {{ !empty($suhu->catatan) ? 'readonly' : '' }}>{{ $suhu->catatan }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== TOMBOL AKSI ===================== --}}
        <div class="action-buttons mt-4 mb-5">
            @php
                // Cek apakah ada master data suhu yang belum lengkap batas standarnya
                $hasNullStandard = collect($area_suhus)->contains(function($area) {
                    return $area->standar_min === null || $area->standar_max === null;
                });
            @endphp

            <button type="button" class="btn btn-batalkan rounded-pill px-4 fw-medium" onclick="window.location.href='{{ route('suhu.index') }}'">
                Batalkan
            </button>
            <button type="submit" class="btn btn-update rounded-pill px-5 fw-medium"
                @if($hasNullStandard)
                    disabled
                    title="⚠️ Silahkan cek Master Suhu dan lengkapi Standar Suhu terlebih dahulu"
                @endif
            >
                <i class="bi bi-save me-1"></i> Update Data
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Fungsi Helper Validasi Suhu (Hanya berlaku untuk input yang tidak readonly)
    function validateSuhu(input) {
        
        // 1. FILTER INPUT: Hanya izinkan angka, minus (-), dan koma/titik (.)
        input.value = input.value.replace(/[^0-9.,-]/g, '');

        // Antisipasi jika user mengetik koma pada keyboard mobile, ubah jadi titik
        let rawValue = input.value.replace(',', '.').trim();

        const warningMsg = input.parentElement.querySelector('.warning-msg');

        // Reset state terlebih dahulu
        input.classList.remove('is-invalid', 'border-danger');
        if (warningMsg) warningMsg.classList.add('d-none');

        // Jika input kosong atau HANYA tanda "-", hentikan proses validasi (jangan salahkan user dulu)
        if (rawValue === '' || rawValue === '-') return;

        // Lanjut ke validasi Min/Max
        const val = parseFloat(rawValue);
        const min = parseFloat(input.dataset.min);
        const max = parseFloat(input.dataset.max);

        // Lakukan validasi hanya jika nilai, min, dan max merupakan angka yang valid
        if (!isNaN(val) && !isNaN(min) && !isNaN(max)) {
            if (val < min || val > max) {
                input.classList.add('is-invalid', 'border-danger');
                if (warningMsg) {
                    warningMsg.innerHTML = `<i class="bi bi-exclamation-circle-fill me-1"></i> Di luar standar (${min} – ${max}°C)`;
                    warningMsg.classList.remove('d-none');
                }
            }
        }
    }

    // Eksekusi Pengecekan pada semua input suhu
    const inputs = document.querySelectorAll('.suhu-input');
    
    inputs.forEach(function (input) {
        // 1. Eksekusi pengecekan di awal halaman load untuk menunjukkan data lama yang mungkin salah (merah)
        let rawValueInit = input.value.replace(',', '.').trim();
        if (rawValueInit !== '' && rawValueInit !== '-') {
            validateSuhu(input);
        }

        // 2. Jalankan real-time pengecekan SAAT DIKETIK (Hanya untuk input yang masih bisa diedit QC)
        if (!input.hasAttribute('readonly')) {
            input.addEventListener('input', function () {
                validateSuhu(this);
            });
        }
    });

});
</script>
@endpush
@endsection