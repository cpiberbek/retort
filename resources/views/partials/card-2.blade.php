<style>
    @keyframes statusLabel {
        0% {
            opacity: 0;
            letter-spacing: 8px;
        }

        100% {
            opacity: 1;
            letter-spacing: 3px;
        }
    }

    @keyframes statusTitle {
        0% {
            opacity: 0;
            transform: translateX(25px);
        }

        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes statusLine {
        0% {
            opacity: 0;
            transform: scaleX(0);
            transform-origin: left;
        }

        100% {
            opacity: 1;
            transform: scaleX(1);
            transform-origin: left;
        }
    }

    @keyframes statusBody {
        0% {
            opacity: 0;
            transform: translateY(15px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .status-label {
        font-size: 10px;
        letter-spacing: 3px;
        color: #ea580c;
        font-weight: 600;
        animation: statusLabel .45s ease-out both;
    }

    .status-title {
        font-size: 28px;
        line-height: 1;
        font-weight: 600;
        color: #441b05;
        animation: statusTitle .5s cubic-bezier(.16, 1, .3, 1) .2s both;
    }

    .status-live-dot {
        font-size: 10px;
        color: #eb1010;
        margin-right: 6px;
    }

    .status-live-text {
        font-size: 10px;
        letter-spacing: 2px;
        color: #e61616;
        font-weight: 700;
    }

    .status-line {
        height: 2px;
        background: linear-gradient(
            to right,
            #ea580c 45%,
            #ea580c 45%,
            #e2e8f0 43%,
            #e2e8f0 100%
        );
        animation: statusLine .65s cubic-bezier(.16, 1, .3, 1) .55s both;
    }

    .status-body {
        animation: statusBody .45s ease-out .9s both;
    }
</style>

    <div class="py-2">

        <div class="status-label">
            REAL TIME MONITORING
        </div>

        <div class="d-flex align-items-center justify-content-between mt-1">

        <div class="status-title">
            Status Produksi
        </div>

        {{-- later kalau sudah proper --}}
        {{-- <div style="
            width: 30px;
            height: 30px;
            border: 1.5px solid #ea580c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ea580c;
            font-size: 16px;
        ">
            <i class="bi bi-search"></i>
        </div> --}}

    </div>

    <div class="mt-3 status-line"></div>

    <div class="d-flex align-items-center justify-content-center mt-4 status-body">

        <div class="card shadow-sm border-0" style="width: 150%; max-width: 360px; background: #fff7ed;">
            <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">

                <div style="font-size: 50px; color: #f28c28; line-height: 1; margin-bottom: 10px;">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div style="font-family: 'Arial Black', Arial, sans-serif; font-size: 18px; line-height: 1; letter-spacing: 3px; color: #441b05; font-weight: 900;">
                    COMING SOON...
                </div>

            </div>
        </div>

    </div>

</div>