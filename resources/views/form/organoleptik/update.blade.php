@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-pencil-square"></i> Edit Pemeriksaan Organoleptik
            </h4>

            <form method="POST" action="{{ route('organoleptik.update_qc', $organoleptik->uuid) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        @php
                        $isLocked = !empty($organoleptik->date) || !empty($organoleptik->shift) ||
                        !empty($organoleptik->nama_produk);
                        @endphp
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control"
                                    value="{{ old('date', $organoleptik->date) }}" {{ $isLocked ? 'readonly' : '' }}
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" {{ $isLocked ? 'disabled' : ''
                                    }} required>
                                    <option value="1" {{ old('shift', $organoleptik->shift) == '1' ? 'selected' : ''
                                        }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $organoleptik->shift) == '2' ? 'selected' : ''
                                        }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $organoleptik->shift) == '3' ? 'selected' : ''
                                        }}>Shift 3</option>
                                </select>
                                @if($isLocked)
                                <input type="hidden" name="shift" value="{{ $organoleptik->shift }}">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker"
                                    data-live-search="true" title="Ketik nama produk..." {{ $isLocked ? 'disabled' : ''
                                    }} required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" {{ old('nama_produk', $organoleptik->
                                        nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($isLocked)
                                <input type="hidden" name="nama_produk" value="{{ $organoleptik->nama_produk }}">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== PEMERIKSAAN ===================== --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <strong>Pemeriksaan Sensori</strong>
                        <button type="button" id="addRow" class="btn btn-light btn-sm text-dark">
                            <i class="bi bi-plus-lg"></i> Tambah Pemeriksaan
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="alert alert-warning mt-2 py-3 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong> Keterangan Parameter:</strong>
                            <strong> Penampilan, Aroma, Rasa Keseluruhan:</strong>
                            <ul class="mb-2 mt-2">
                                <li>1. Sangat Tidak</li>
                                <li>2. Biasa</li>
                                <li>3. Sangat</li>
                            </ul>
                            <i class="bi bi-info-circle"></i>
                            <strong> Kekenyalan, Manis, Asin, Gurih, Ayam/BBQ/Ikan:</strong>
                            <ul class="mb-2 mt-2">
                                <li>1. Terlalu</li>
                                <li>2. Kurang</li>
                                <li>3. Pas</li>
                            </ul>
                            <strong>Keterangan Hasil Score:</strong>
                            <ul class="mb-0 mt-2">
                                <li>1 – 1.5 : Tidak Release</li>
                                <li>1.6 – 3 : Release</li>
                            </ul>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center align-middle" id="pemeriksaanTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Produksi</th>
                                        <th>Penampilan</th>
                                        <th>Aroma</th>
                                        <th>Kekenyalan</th>
                                        <th>Rasa Asin</th>
                                        <th>Rasa Gurih</th>
                                        <th>Rasa Manis</th>
                                        <th>Rasa Ayam/BBQ/Ikan</th>
                                        <th>Rasa Keseluruhan</th>
                                        <th>Hasil Score</th>
                                        <th>Release/Tidak Release</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="sensoriTable">
                                    @foreach($organoleptikData as $i => $sensori)
                                    @php
                                    $releaseText = $sensori['release'] ?? '';
                                    $releaseStyle = $releaseText === 'Tidak Release'
                                    ? 'color:red;font-weight:bold;'
                                    : ($releaseText === 'Release'
                                    ? 'color:green;font-weight:bold;'
                                    : '');
                                    @endphp
                                    <tr>
                                        <td>
                                            <select name="sensori[{{ $i }}][kode_produksi]"
                                                class="form-control form-control-sm b kode_produksi_Select" required
                                                data-should-disabled="{{ !empty($sensori['kode_produksi']) ? 'true' : 'false' }}"
                                                {{ !empty($sensori['kode_produksi']) ? 'disabled' : '' }}>
                                                <option value="">Pilih Kode Produksi</option>
                                                @if(!empty($sensori['kode_produksi']))
                                                <option value="{{ $sensori['kode_produksi'] }}" selected>{{
                                                    $sensori['kode_produksi_text'] ?? $sensori['kode_produksi'] }}
                                                </option>
                                                @endif
                                            </select>
                                            @if(!empty($sensori['kode_produksi']))
                                            <input type="hidden" name="sensori[{{ $i }}][kode_produksi]"
                                                value="{{ $sensori['kode_produksi'] }}">
                                            @endif
                                            <small class="kodeError text-danger d-none"></small>
                                        </td>

                                        <td><input type="number" name="sensori[{{ $i }}][penampilan]"
                                                class="form-control form-control-sm sensori"
                                                value="{{ $sensori['penampilan'] ?? '' }}" min="0" max="3" {{
                                                !empty($sensori['penampilan']) ? 'readonly' : '' }}></td>
                                        <td><input type="number" name="sensori[{{ $i }}][aroma]"
                                                class="form-control form-control-sm sensori"
                                                value="{{ $sensori['aroma'] ?? '' }}" min="0" max="3" {{
                                                !empty($sensori['aroma']) ? 'readonly' : '' }}></td>
                                        <td><input type="number" name="sensori[{{ $i }}][kekenyalan]"
                                                class="form-control form-control-sm sensori"
                                                value="{{ $sensori['kekenyalan'] ?? '' }}" min="0" max="3" {{
                                                !empty($sensori['kekenyalan']) ? 'readonly' : '' }}></td>
                                        <td><input type="number" name="sensori[{{ $i }}][rasa_asin]"
                                                class="form-control form-control-sm sensori"
                                                value="{{ $sensori['rasa_asin'] ?? '' }}" min="0" max="3" {{
                                                !empty($sensori['rasa_asin']) ? 'readonly' : '' }}></td>
                                        <td><input type="number" name="sensori[{{ $i }}][rasa_gurih]"
                                                class="form-control form-control-sm sensori"
                                                value="{{ $sensori['rasa_gurih'] ?? '' }}" min="0" max="3" {{
                                                !empty($sensori['rasa_gurih']) ? 'readonly' : '' }}></td>
                                        <td><input type="number" name="sensori[{{ $i }}][rasa_manis]"
                                                class="form-control form-control-sm sensori"
                                                value="{{ $sensori['rasa_manis'] ?? '' }}" min="0" max="3" {{
                                                !empty($sensori['rasa_manis']) ? 'readonly' : '' }}></td>
                                        <td><input type="number" name="sensori[{{ $i }}][rasa_daging]"
                                                class="form-control form-control-sm sensori"
                                                value="{{ $sensori['rasa_daging'] ?? '' }}" min="0" max="3" {{
                                                !empty($sensori['rasa_daging']) ? 'readonly' : '' }}></td>
                                        <td><input type="number" name="sensori[{{ $i }}][rasa_keseluruhan]"
                                                class="form-control form-control-sm sensori"
                                                value="{{ $sensori['rasa_keseluruhan'] ?? '' }}" min="0" max="3" {{
                                                !empty($sensori['rasa_keseluruhan']) ? 'readonly' : '' }}></td>

                                        <td>
                                            <input type="number" step="0.1" name="sensori[{{ $i }}][rata_score]"
                                                class="form-control form-control-sm rata_score"
                                                value="{{ $sensori['rata_score'] ?? '' }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="sensori[{{ $i }}][release]"
                                                class="form-control form-control-sm b release"
                                                value="{{ $releaseText }}" style="{{ $releaseStyle }}" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm removeRow">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                {{-- ===================== TOMBOL UPDATE ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Update</button>
                    <a href="{{ route('organoleptik.index') }}" class="btn btn-secondary w-auto"><i
                            class="bi bi-arrow-left"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .table th,
    .table td {
        padding: 0.5rem;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .form-control-sm {
        min-width: 90px;
        font-size: 0.9rem;
    }

    .b {
        min-width: 150px;
        font-size: 0.9rem;
    }

    .table-bordered th,
    .table-bordered td {
        text-align: center;
    }
</style>
@endsection

{{-- ========== JS & LOGIKA SAMA DENGAN CREATE ========== --}}
@push('scripts')
{{-- Include jQuery (Select2 depends on it) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<script>
    window.organoleptikData = @json($organoleptikData);
</script>

<script>
    // $(document).ready(function(){
    //     $('.selectpicker').selectpicker();
    //     // Ensure nama_produk is set
    //     $("#nama_produk").val("{{ $organoleptik->nama_produk }}");
    //     $('.selectpicker').selectpicker('refresh');
    //     // Inisialisasi Select2 untuk kode_produksi
    //     initializeKodeProduksiSelects();
    // });
</script>

<script>
    // ========== Inisialisasi Select2 untuk kode_produksi ==========
    function initializeKodeProduksiSelects() {
        $('.kode_produksi_Select').each(function() {
            const $select = $(this);
            const shouldBeDisabled = $select.attr('data-should-disabled') === 'true';

            $select.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Ketik kode produksi...',
                allowClear: true,
                ajax: {
                    delay: 300,
                    transport: function (params, success, failure) {
                        const produk = $('#nama_produk').val();
                        if (!produk) {
                            return;
                        }

                        return $.ajax({
                            url: `/lookup/batch-packing/${produk}`,
                            data: { q: params.data.term },
                            success,
                            error: failure
                        });
                    },
                    processResults: function (data) {
                        return { results: data };
                    }
                }
            });

            // Re-apply disabled state after Select2 initialization
            if (shouldBeDisabled) {
                $select.prop('disabled', true).trigger('change');
            }
        });
    }

    // ========== Trigger saat produk diganti ==========
    $('#nama_produk').on('change', function () {
        // Only affect new rows (not disabled ones)
        $('.kode_produksi_Select').each(function() {
            const $select = $(this);
            const shouldBeDisabled = $select.attr('data-should-disabled') === 'true';
            if (!shouldBeDisabled) {
                $select.prop('disabled', !$('#nama_produk').val())
                       .val(null)
                       .trigger('change');
            }
        });
    });

    $(function() {
        function hitungRata(row) {
            const nilai = $(row).find('.sensori').map(function(){ 
                return parseFloat($(this).val()) || 0;
            }).get();
            const isi = nilai.filter(v => v > 0).length;
            if (isi === 0) return;
            const avg = (nilai.reduce((a,b)=>a+b,0) / isi).toFixed(1);
            $(row).find('.rata_score').val(avg);

            const release = $(row).find('.release');
            if (avg >= 1 && avg <= 1.5) {
                release.val('Tidak Release').css({color:'red',fontWeight:'bold'});
            } else if (avg >= 1.6 && avg <= 3) {
                release.val('Release').css({color:'green',fontWeight:'bold'});
            } else {
                release.val('').css({color:'',fontWeight:''});
            }
        }

    // ========== Event input nilai sensori ==========
        $(document).on('input', '.sensori', function(){
            const row = $(this).closest('tr');
            hitungRata(row);
        });

    // ========== Event change kode produksi (select) ==========
        $(document).on('change', '.kode_produksi_Select', function() {
            const $this = $(this);
            const row = $this.closest('tr');
            const val = $this.val();
            const $err = row.find('.kodeError');

            if (!val) {
                $err.text('Kode produksi wajib dipilih.').removeClass('d-none text-success').addClass('text-danger');
                $this.addClass('is-invalid').removeClass('is-valid');
            } else {
                $err.text('').addClass('d-none').removeClass('text-success text-danger');
                $this.addClass('is-valid').removeClass('is-invalid');
            }
        });
// ========== Tambah baris baru (HANYA SATU EVENT) ==========
        $(document).off('click', '#addRow').on('click', '#addRow', function(){
            const table = $('#pemeriksaanTable tbody');
            const index = table.find('tr').length;

            // Create new blank row instead of cloning
            const newRow = `
                <tr>
                    <td>
                        <select name="sensori[${index}][kode_produksi]"
                            class="form-control form-control-sm b kode_produksi_Select" required
                            data-should-disabled="false">
                            <option value="">Pilih Kode Produksi</option>
                        </select>
                        <small class="kodeError text-danger d-none"></small>
                    </td>
                    <td><input type="number" name="sensori[${index}][penampilan]"
                            class="form-control form-control-sm sensori" min="0" max="3" step="1">
                    </td>
                    <td><input type="number" name="sensori[${index}][aroma]"
                            class="form-control form-control-sm sensori" min="0" max="3" step="1">
                    </td>
                    <td><input type="number" name="sensori[${index}][kekenyalan]"
                            class="form-control form-control-sm sensori" min="0" max="3" step="1">
                    </td>
                    <td><input type="number" name="sensori[${index}][rasa_asin]"
                            class="form-control form-control-sm sensori" min="0" max="3" step="1">
                    </td>
                    <td><input type="number" name="sensori[${index}][rasa_gurih]"
                            class="form-control form-control-sm sensori" min="0" max="3" step="1">
                    </td>
                    <td><input type="number" name="sensori[${index}][rasa_manis]"
                            class="form-control form-control-sm sensori" min="0" max="3" step="1">
                    </td>
                    <td><input type="number" name="sensori[${index}][rasa_daging]"
                            class="form-control form-control-sm sensori" min="0" max="3" step="1">
                    </td>
                    <td><input type="number" name="sensori[${index}][rasa_keseluruhan]"
                            class="form-control form-control-sm sensori" min="0" max="3" step="1">
                    </td>
                    <td>
                        <input type="number" step="0.1" name="sensori[${index}][rata_score]"
                            class="form-control form-control-sm rata_score" readonly>
                    </td>
                    <td>
                        <input type="text" name="sensori[${index}][release]"
                            class="form-control form-control-sm b release" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            table.append(newRow);

            // Initialize select2 only on the new row
            const newSelect = table.find('tr:last .kode_produksi_Select');
            newSelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Ketik kode produksi...',
                allowClear: true,
                ajax: {
                    delay: 300,
                    transport: function (params, success, failure) {
                        const produk = $('#nama_produk').val();
                        if (!produk) {
                            return;
                        }

                        return $.ajax({
                            url: "{{ route('lookup.batch_packing', ['nama_produk' => '__PRODUK__']) }}".replace('__PRODUK__', encodeURIComponent(produk)),
                            data: { q: params.data.term },
                            success,
                            error: failure
                        });
                    },
                    processResults: function (data) {
                        return { results: data };
                    }
                }
            });

            // Set disabled state based on product selection
            newSelect.prop('disabled', !$('#nama_produk').val());
        });


    // ========== Hapus baris ==========
        $(document).on('click', '.removeRow', function(){
            if ($('#pemeriksaanTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('Minimal harus ada satu baris pemeriksaan.');
            }
        });

    });
</script>
@endpush()