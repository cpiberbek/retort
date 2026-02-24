@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">
                <i class="bi bi-plus-circle"></i> Form Input Kontrol Labelisasi PVDC
            </h4>

            <form id="pvdcForm" enctype="multipart/form-data">
                @csrf

                {{-- ===================== IDENTITAS ===================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Data PVDC</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" id="dateInput" class="form-control" required>
                                <span class="invalid-feedback" id="error-date"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" id="shiftInput" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                                <span class="invalid-feedback" id="error-shift"></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select name="nama_produk" id="nama_produk" class="form-control selectpicker"
                                    data-live-search="true" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="error-nama_produk"></span>

                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Operator</label>
                                <select id="nama_operator" name="nama_operator" class="form-control selectpicker"
                                    data-live-search="true" required>
                                    <option value="">-- Pilih Operator --</option>
                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->nama_karyawan }}">{{ $operator->nama_karyawan }}
                                    </option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="error-nama_operator"></span>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Data PVDC</strong>
                        <button type="button" id="addRow" class="btn btn-sm btn-light text-primary fw-bold">
                            + Tambah Mesin
                        </button>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered text-center align-middle" id="pvdcTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Mesin</th>
                                    <th>Kode Batch</th>
                                    <th>Bukti Kode (Upload Gambar)</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pvdcBody">
                                <tr>
                                    <td>
                                        <select name="data_pvdc[0][mesin]" class="form-control form-control-sm"
                                            required>
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach($mesins as $mesin)
                                            <option value="{{ $mesin->nama_mesin }}">{{ $mesin->nama_mesin }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="data_pvdc[0][kode_batch]"
                                            class="form-control form-control-sm batchSelect" required disabled>
                                            <option value="">Pilih Produk Terlebih Dahulu</option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="file" name="data_pvdc[0][kode_produksi]"
                                            class="form-control form-control-sm" accept="image/*" required>
                                        <div class="preview mt-2"></div>
                                    </td>
                                    <td>
                                        <input type="text" name="data_pvdc[0][keterangan]"
                                            class="form-control form-control-sm">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ===================== TOMBOL ===================== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" id="saveBtn" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('labelisasi_pvdc.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>

            <hr>
            <div id="resultArea"></div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');

        dateInput.value = `${yyyy}-${mm}-${dd}`;

        let hour = parseInt(hh);
        if (hour >= 7 && hour < 15) {
            shiftInput.value = "1";
        } else if (hour >= 15 && hour < 23) {
            shiftInput.value = "2";
        } else {
            shiftInput.value = "3"; 
        }
    });
</script>

<script>
    // Script untuk Auto-fill Tanggal dan Shift saat halaman dimuat
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');

        if(dateInput) {
            dateInput.value = `${yyyy}-${mm}-${dd}`;
        }

        if(shiftInput) {
            let hour = parseInt(hh);
            if (hour >= 7 && hour < 15) {
                shiftInput.value = "1";
            } else if (hour >= 15 && hour < 23) {
                shiftInput.value = "2";
            } else {
                shiftInput.value = "3"; 
            }
        }
    });
</script>

<script>
    $(document).ready(function(){

    // =================== VALIDASI KODE BATCH ===================
    const produkSelect = $("#nama_produk");

    // Trigger saat produk diganti
    produkSelect.on("change", function () {
        loadBatchForAllRows();
    });

    function loadBatchForAllRows() {
        let produk = produkSelect.val();
        let batchSelects = $(".batchSelect");

        // Store current selections before clearing
        let currentSelections = {};
        batchSelects.each(function (index) {
            let select = $(this);
            currentSelections[index] = select.val();
        });

        batchSelects.each(function (index) {
            let select = $(this);
            select.html("");
            select.prop("disabled", true);

            if (!produk) {
                select.html('<option value="">Pilih Produk Terlebih Dahulu</option>');
                return;
            }

            const url = "{{ route('lookup.batch', ['nama_produk' => '__PRODUK__']) }}".replace('__PRODUK__', encodeURIComponent(produk));
                fetch(url)
                .then(res => res.json())
                .then(data => {

                    if (data.length === 0) {
                        select.html('<option value="">Batch Tidak Ditemukan</option>');
                        select.prop("disabled", true);
                        return;
                    }

                    select.prop("disabled", false);
                    select.html('<option value="">-- Pilih Batch --</option>');

                    data.forEach(batch => {
                        select.append(`
                            <option value="${batch.uuid}" kode_batch="${batch.kode_produksi}">
                                ${batch.kode_produksi}
                            </option>
                        `);
                    });

                    // Restore previous selection if it still exists
                    if (currentSelections[index] && select.find(`option[value="${currentSelections[index]}"]`).length > 0) {
                        select.val(currentSelections[index]);
                    }
                });
        });
    }

    // Trigger saat batch dipilih → isi kode batch pada input text (jika dipakai)
    $(document).on("change", ".batchSelect", function () {
        let kode = $(this).find(":selected").data("kode") ?? "";
        $(this).closest("tr").find(".kodeBatchText").val(kode);
    });

        function showError(input, message) {
            input.addClass("is-invalid");
            // Cek agar tidak duplikat pesan error
            if(input.next(".invalid-feedback").length === 0){
                input.after(`<div class="invalid-feedback">${message}</div>`);
            }
        }

    // Jalankan validasi saat input berubah

    // =================== TAMBAH BARIS ===================
        let index = 0;
        // Variabel mesinOptions diambil dari Blade
        const mesinOptions = `{!! collect($mesins)->map(fn($m) => "<option value='{$m->nama_mesin}'>{$m->nama_mesin}</option>")->implode('') !!}`;

        $('#addRow').on('click', function () {
            index++;
            $('#pvdcBody').append(`
        <tr>
            <td>
                <select name="data_pvdc[${index}][mesin]" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Mesin --</option>${mesinOptions}
                </select>
            </td>
            <td>
                <select name="data_pvdc[${index}][kode_batch]"
                    class="form-control form-control-sm batchSelect" required disabled>
                    <option value="">Pilih Produk Terlebih Dahulu</option>
                </select>
            </td>
            <td>
                <input type="file" name="data_pvdc[${index}][kode_produksi]" class="form-control form-control-sm" accept="image/*" required>
            </td>
            <td>
                <input type="text" name="data_pvdc[${index}][keterangan]" class="form-control form-control-sm">
                <div class="preview mt-2"></div>
            </td>
            <td>
                <button type="button" class="btn btn-primary btn-sm saveRow">Simpan</button>
                <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
            </td>
        </tr>
            `);
            loadBatchForAllRows();
        });

    // =================== HAPUS BARIS ===================
        $('#pvdcBody').on('click', '.removeRow', function () {
            // Cek sisa baris, jangan hapus jika tinggal satu (opsional)
            // if($('#pvdcBody tr').length > 1) { 
                $(this).closest('tr').remove();
            // }
        });

    // =================== SIMPAN DATA (AJAX) ===================
        $('#saveBtn').click(function () {
            const btn = $(this);
            const form = $('#pvdcForm')[0]; // Ambil elemen DOM form
            
            // Validasi HTML5 Native sebelum kirim AJAX (Cek required field)
            if (!form.checkValidity()) {
                form.reportValidity(); // Tampilkan popup error browser standard
                return;
            }

            const formData = new FormData(form);

            // Validasi manual tambahan (opsional, karena checkValidity sudah menangani required)
            let hasData = false;
            $('#pvdcBody tr').each(function(){
                const mesin = $(this).find('select[name$="[mesin]"]').val();
                if(mesin) hasData = true;
            });

            if(!hasData){
                alert('Belum ada data PVDC yang diinputkan!');
                return;
            }

            // Ubah tombol jadi loading
            const originalText = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

            $.ajax({
                url: "{{ route('labelisasi_pvdc.storeFinal') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res){
                    if(res.success) {
                        window.location.href = res.redirect_url;
                    } else {
                        alert(res.message);
                        btn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr) {
                    // PERBAIKAN: Handle error agar tombol kembali aktif
                    btn.prop('disabled', false).html(originalText);

                    let errorMessage = 'Terjadi kesalahan sistem.';

                    if (xhr.status === 422) { // Error Validasi Laravel
                        let errors = xhr.responseJSON.errors;
                        errorMessage = 'Mohon periksa inputan Anda:\n';
                        $.each(errors, function(key, value) {
                            errorMessage += '- ' + value[0] + '\n';
                        });
                    } else if (xhr.status === 413) {
                        errorMessage = 'File gambar terlalu besar. Maksimum upload server terlampaui.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    alert(errorMessage);
                    console.error(xhr.responseText);
                }
            });
        });

    });
</script>
@endsection