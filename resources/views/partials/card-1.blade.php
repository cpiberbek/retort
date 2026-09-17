<style>
    @keyframes assembleLabel {
        0% {
            opacity: 0;
            transform: translateY(-10px);
            letter-spacing: 7px;
        }

        100% {
            opacity: 1;
            transform: translateY(0);
            letter-spacing: 3px;
        }
    }

    @keyframes assembleTitle {
        0% {
            opacity: 0;
            transform: translateX(-20px);
        }

        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes assembleUnit {
        0% {
            opacity: 0;
            transform: translateX(20px) scale(.85);
        }

        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes assembleLine {
        0% {
            width: 0;
            opacity: 0;
        }

        100% {
            width: 100%;
            opacity: 1;
        }
    }

    @keyframes assembleBody {
        0% {
            opacity: 0;
            transform: translateY(8px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .productivity-label {
        animation: assembleLabel .35s cubic-bezier(.22, 1, .36, 1) both;
    }

    .productivity-title {
        animation: assembleTitle .45s cubic-bezier(.22, 1, .36, 1) .15s both;
    }

    .productivity-unit {
        animation: assembleUnit .4s cubic-bezier(.22, 1, .36, 1) .25s both;
    }

    .productivity-line {
        width: 100%;
        animation: assembleLine .5s cubic-bezier(.22, 1, .36, 1) .35s both;
    }

    .productivity-body {
        animation: assembleBody .4s cubic-bezier(.22, 1, .36, 1) .5s both;
    }
</style>

<div class="py-2">
    <div class="productivity-label"
        style="font-size: 10px; letter-spacing: 3px; color: #9a3412; font-weight: 600;">
        OPERATIONAL MONITORING
    </div>

    <div class="d-flex align-items-center justify-content-between mt-1">
        <div class="productivity-title"
            style="font-size: 28px; line-height: 1; font-weight: 600; color: #441b05;">
            Productivity Record
        </div>

        <div class="productivity-unit"
            style="
                font-size: 22px;
                color: #ea580c;
                font-weight: 300;
                transform: scaleY(0.9);
                transform-origin: center;
            ">
            [Kg/MP]
        </div>
    </div>

    <div class="mt-3 productivity-line"
        style="height: 2px; background: linear-gradient(to right, #ea580c 58%, #ea580c 58%, #e2e8f0 43%, #e2e8f0 100%);">
    </div>

    <div class="d-flex align-items-center justify-content-between mt-3 productivity-body">
        
    </div>
</div>