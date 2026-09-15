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

                            <div style="
                                width:330px;
                                height:100px;
                                margin:auto;
                                position:relative;
                                overflow:hidden;
                            ">

                                <div id="searchViewport" style="
                                    width:100%;
                                    height:100%;
                                    position:relative;
                                    overflow:hidden;
                                ">

                                    <div id="searchTrack" style="
                                        position:absolute;
                                        top:12px;
                                        left:50%;
                                        display:flex;
                                        align-items:center;
                                        gap:14px;
                                        width:max-content;
                                    ">

                                        <div class="search-card">
                                            <i class="fas fa-file-alt"></i>
                                        </div>

                                        <div class="search-card">
                                            <i class="fas fa-user"></i>
                                        </div>

                                        <div class="search-card active">
                                            <i class="fas fa-image"></i>
                                        </div>

                                        <div class="search-card">
                                            <i class="fas fa-file-alt"></i>
                                        </div>

                                        <div class="search-card">
                                            <i class="fas fa-user"></i>
                                        </div>

                                        <div class="search-card">
                                            <i class="fas fa-image"></i>
                                        </div>

                                        <div class="search-card">
                                            <i class="fas fa-file-alt"></i>
                                        </div>

                                    </div>

                                </div>

                                <div class="search-glass">
                                    <i class="fas fa-search"></i>
                                </div>

                                <style>
                                    .search-card {
                                        width:90px;
                                        height:70px;
                                        flex:0 0 90px;
                                        border:1px solid #dee2e6;
                                        border-radius:10px;
                                        background:#fff;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        font-size:28px;
                                        color:#adb5bd;
                                        opacity:.35;
                                        transition:
                                            opacity .7s ease,
                                            transform .7s ease,
                                            box-shadow .7s ease;
                                    }

                                    .search-card.active {
                                        color:#dc3545;
                                        opacity:1;
                                        transform:scale(1);
                                        box-shadow:0 4px 14px rgba(0,0,0,.12);
                                    }

                                    .search-glass {
                                        position:absolute;
                                        left:68%;
                                        top:50%;
                                        width:46px;
                                        height:46px;
                                        border-radius:50%;
                                        background:#fff;
                                        border:1px solid #dee2e6;
                                        box-shadow:0 2px 10px rgba(0,0,0,.15);
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        font-size:20px;
                                        z-index:10;

                                        animation:searchFloat 2.2s ease-in-out infinite;
                                    }

                                    .search-glass i {
                                        color:#dc3545;
                                    }

                                    @keyframes searchFloat {
                                        0% {
                                            transform:translate(-50%, -50%) translate(0, 0);
                                        }

                                        25% {
                                            transform:translate(-50%, -50%) translate(4px, -5px);
                                        }

                                        50% {
                                            transform:translate(-50%, -50%) translate(0, -8px);
                                        }

                                        75% {
                                            transform:translate(-50%, -50%) translate(-4px, -5px);
                                        }

                                        100% {
                                            transform:translate(-50%, -50%) translate(0, 0);
                                        }
                                    }
                                </style>

                            </div>

                            <p class="mt-3 mb-0 text-muted">
                                Data sedang dicari, mohon ditunggu...
                            </p>

                        </div>
                    `);

                    const track = document.getElementById("searchTrack");
                    const cards = document.querySelectorAll(".search-card");

                    const cardWidth = 104;
                    const centerPosition = 120;

                    let current = 2;
                    let currentLeft = centerPosition - (current * cardWidth);

                    track.style.left = currentLeft + "px";

                    function setActive() {
                        cards.forEach((card, index) => {
                            if (index === current) {
                                card.classList.add("active");
                            } else {
                                card.classList.remove("active");
                            }
                        });
                    }

                    function slide() {
                        current++;

                        if (current >= cards.length - 2) {
                            current = 2;

                            track.style.transition = "none";

                            currentLeft =
                                centerPosition - (current * cardWidth);

                            track.style.left = currentLeft + "px";

                            setActive();

                            setTimeout(function() {
                                track.style.transition =
                                    "left .8s cubic-bezier(.25,.8,.25,1)";
                            }, 50);

                        } else {
                            currentLeft =
                                centerPosition - (current * cardWidth);

                            track.style.transition =
                                "left .8s cubic-bezier(.25,.8,.25,1)";

                            track.style.left = currentLeft + "px";

                            setActive();
                        }

                        setTimeout(slide, 1000);
                    }

                    setActive();

                    setTimeout(slide, 200);
                },

                success: function(res) {
                    $("#div_data").html(res);
                },

                error: function(xhr) {
                    console.log(xhr);

                    $("#div_data").html(`
                        <div class="alert alert-danger">
                            <strong>Gagal mengambil data.</strong><br>
                            Status: ${xhr.status}<br>
                            ${xhr.responseText
                                ? xhr.responseText.substring(0, 500)
                                : 'Tidak ada response dari server.'
                            }
                        </div>
                    `);
                }
            });
        });
    </script>
@endsection
