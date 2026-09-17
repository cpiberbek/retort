<style>
    @keyframes statusLabelAssemble {
        0% {
            opacity: 0;
            transform: scaleY(0);
            transform-origin: bottom;
        }

        100% {
            opacity: 1;
            transform: scaleY(1);
            transform-origin: bottom;
        }
    }

    @keyframes statusTitleAssemble {
        0% {
            opacity: 0;
            transform: translateY(18px);
            filter: blur(4px);
        }

        55% {
            opacity: 1;
            filter: blur(0);
        }

        100% {
            transform: translateY(0);
        }
    }

    @keyframes statusLiveAssemble {
        0% {
            opacity: 0;
            transform: translateX(12px);
        }

        50% {
            opacity: 1;
        }

        70% {
            transform: translateX(-3px);
        }

        100% {
            transform: translateX(0);
        }
    }

    @keyframes statusLineAssemble {
        0% {
            opacity: 0;
            transform: scaleX(0);
            transform-origin: center;
        }

        40% {
            opacity: 1;
        }

        100% {
            transform: scaleX(1);
            transform-origin: center;
        }
    }

    @keyframes statusBodyAssemble {
        0% {
            opacity: 0;
            clip-path: inset(0 50% 0 50%);
        }

        100% {
            opacity: 1;
            clip-path: inset(0 0 0 0);
        }
    }

    @keyframes livePulse {
        0%,
        100% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.7);
            opacity: .35;
        }
    }

    .status-label {
        animation: statusLabelAssemble .3s ease-out both;
    }

    .status-title {
        animation: statusTitleAssemble .55s cubic-bezier(.2, .8, .2, 1) .15s both;
    }

    .status-live {
        animation: statusLiveAssemble .4s cubic-bezier(.2, .8, .2, 1) .35s both;
    }

    .status-line {
        animation: statusLineAssemble .7s cubic-bezier(.16, 1, .3, 1) .35s both;
    }

    .status-body {
        animation: statusBodyAssemble .45s ease-out .65s both;
    }

    .status-live-dot {
        animation: livePulse 1.4s ease-in-out 1s infinite;
    }
</style>

<div class="py-2">

    <div class="status-label"
        style="font-size: 10px; letter-spacing: 3px; color: #ea580c; font-weight: 600;">
        REAL TIME MONITORING
    </div>

    <div class="d-flex align-items-center justify-content-between mt-1">

        <div class="status-title"
            style="font-size: 28px; line-height: 1; font-weight: 600; color: #441b05;">
            Status Produksi
        </div>

        <div class="d-flex align-items-center status-live">

            <span class="status-live-dot"
                style="font-size: 10px; color: #eb1010; margin-right: 6px;">
                ●
            </span>

            <span style="font-size: 10px; letter-spacing: 2px; color: #e61616; font-weight: 700;">
                LIVE
            </span>

        </div>

    </div>

    <div class="mt-3 status-line"
        style="height: 2px; background: linear-gradient(to right, #ea580c 45%, #ea580c 45%, #e2e8f0 43%, #e2e8f0 100%);">
    </div>

    <div class="d-flex align-items-center justify-content-between mt-3 status-body">

    </div>

</div>