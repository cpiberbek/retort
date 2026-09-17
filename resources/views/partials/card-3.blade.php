<style>
    @keyframes tempLabel {
        0% {
            opacity: 0;
            transform: translateY(-12px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes tempTitle {
        0% {
            opacity: 0;
            transform: translateY(25px);
        }

        70% {
            opacity: 1;
            transform: translateY(-3px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes tempUnit {
        0% {
            opacity: 0;
            transform: scale(.5) rotate(-12deg);
        }

        60% {
            opacity: 1;
            transform: scale(1.08) rotate(3deg);
        }

        100% {
            opacity: 1;
            transform: scale(1) rotate(0);
        }
    }

    @keyframes tempLine {
        0% {
            opacity: 0;
            clip-path: inset(0 100% 0 0);
        }

        100% {
            opacity: 1;
            clip-path: inset(0 0 0 0);
        }
    }

    @keyframes tempBody {
        0% {
            opacity: 0;
            transform: scaleY(0);
            transform-origin: top;
        }

        100% {
            opacity: 1;
            transform: scaleY(1);
            transform-origin: top;
        }
    }

    .temp-label {
        animation: tempLabel .4s ease-out both;
    }

    .temp-title {
        animation: tempTitle .55s cubic-bezier(.16, 1, .3, 1) .12s both;
    }

    .temp-unit {
        animation: tempUnit .5s cubic-bezier(.16, 1, .3, 1) .28s both;
    }

    .temp-line {
        animation: tempLine .65s cubic-bezier(.16, 1, .3, 1) .4s both;
    }

    .temp-body {
        animation: tempBody .4s ease-out .65s both;
    }
</style>

<div class="py-2">

    <div class="temp-label"
        style="font-size: 10px; letter-spacing: 3px; color: #ea580c; font-weight: 600;">
        HOURLY DATA MONITORING
    </div>

    <div class="d-flex align-items-center justify-content-between mt-1">

        <div class="temp-title"
            style="font-size: 28px; line-height: 1; font-weight: 600; color: #441b05;">
            Suhu Ruangan
        </div>

        <div class="temp-unit"
            style="font-size: 30px; line-height: 1; color: #ea580c; font-weight: 300;">
            °C
        </div>

    </div>

    <div class="mt-3 temp-line"
        style="height: 2px; background: linear-gradient(to right, #ea580c 43%, #ea580c 43%, #e2e8f0 43%, #e2e8f0 100%);">
    </div>

    <div class="d-flex align-items-center justify-content-between mt-3 temp-body">

    </div>

</div>