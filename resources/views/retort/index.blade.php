@extends('layouts.app')

@section('content')
    <div class="container-fluid py-3">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">🔍 Pencarian Data</h4>
        </div>

        {{-- CARD SEARCH --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">

                <form id="form">
                    @csrf

                    <div class="input-group">
                        <input type="text" name="txt_cari" id="txt_cari" class="form-control"
                            placeholder="Ketik kata kunci...">

                        <button type="button" id="btn_cari" class="btn btn-danger">
                            Cari
                        </button>
                    </div>

                </form>

            </div>
        </div>

        {{-- HASIL --}}
        <div id="div_data"></div>

    </div>

    {{-- JQUERY --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $("#btn_cari").click(function() {
            $.ajax({
                url: "{{ url('/retort/cari') }}",
                method: "POST",
                data: $("#form").serialize(),
                beforeSend: function() {
                    $("#div_data").html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-danger"></div>
                        <p class="mt-2">Mencari data...</p>
                    </div>
                `);
                },
                success: function(res) {
                    $("#div_data").html(res);
                }
            });
        });
    </script>
@endsection
