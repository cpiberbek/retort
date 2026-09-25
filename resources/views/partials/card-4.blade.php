<style>
    @keyframes qualityLeft {
        0% {
            opacity: 0;
            transform: translateX(-18px);
        }

        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes qualityDivider {
        0% {
            height: 0;
            opacity: 0;
        }

        100% {
            height: 43px;
            opacity: 1;
        }
    }

    @keyframes qualityRight {
        0% {
            opacity: 0;
            transform: translateX(18px);
            clip-path: inset(0 100% 0 0);
        }

        100% {
            opacity: 1;
            transform: translateX(0);
            clip-path: inset(0 0 0 0);
        }
    }

    @keyframes qualityLine {
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

    @keyframes qualityBody {
        0% {
            opacity: 0;
            transform: translateY(8px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .quality-left {
        animation: qualityLeft .45s cubic-bezier(.16, 1, .3, 1) both;
    }

    .quality-divider {
        animation: qualityDivider .35s ease-out .25s both;
    }

    .quality-right {
        animation: qualityRight .5s cubic-bezier(.16, 1, .3, 1) .4s both;
    }

    .quality-line {
        animation: qualityLine .55s cubic-bezier(.16, 1, .3, 1) .7s both;
    }

    .quality-body {
        animation: qualityBody .5s ease-out .85s both;
    }
</style>

<div class="py-2">

    <div class="d-flex align-items-start justify-content-between" style="white-space: nowrap;">

        <div class="d-flex align-items-start">

            <div class="quality-left" style="width: 105px; flex-shrink: 0; padding-right: 20px;">
                <div style="font-size: 12px; letter-spacing: 2.5px; color: #ea580c; font-weight: 700;">
                    PREVENTIVE
                </div>

                <div class="mt-1"
                    style="font-size: 22px; line-height: 1.1; font-weight: 700; color: #441b05;">
                    QUALITY
                </div>
            </div>

            <div class="quality-divider"
                style="width: 1px; height: 43px; flex-shrink: 0; background: #ea580c;">
            </div>

            <div class="quality-right" style="padding-left: 15px; flex-shrink: 0;">
                <div style="font-size: 26px; line-height: 1; font-weight: 700; color: #441b05;">
                    Hold
                </div>

                <div style="font-size: 17px; color: #ea580c;">
                    & Release
                </div>
            </div>

        </div>

        <div class="d-flex align-items-end status-live">
            {{-- later kalo sudah proper --}}
            {{-- <span class="status-live-dot"
                style="font-size: 10px; color: #eb1010; margin-right: 6px;">
                ●
            </span>

            <span style="font-size: 10px; letter-spacing: 2px; color: #e61616; font-weight: 700;">
                LIVE
            </span> --}}

        </div>

    </div>

    <div class="mt-3 quality-line"
        style="height: 2px; background: linear-gradient(to right, #ea580c 100%, #ea580c 100%, #e2e8f0 43%, #e2e8f0 100%);">
    </div>

    <div class="d-flex align-items-center justify-content-center mt-3 quality-body">
        <div class="card shadow-sm border-0" style="width: 100%; background: #fff7ed;">
            <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">

                <div style="font-size: 50px; color: #f28c28; line-height: 1; margin-bottom: 10px;">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div style="font-family: 'Arial Black', Arial, sans-serif; font-size: 18px; line-height: 1; letter-spacing: 3px; color: #441b05; font-weight: 900;">
                    COMING SOON...
                </div>

            </div>
        </div>
    </div>

</div>