<style>
    @keyframes trendHeader {
        0% {
            opacity: 0;
            transform: translateY(12px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes trendArrow {
        0% {
            opacity: 0;
            transform: translate(-8px, 8px);
        }

        70% {
            opacity: 1;
            transform: translate(3px, -3px);
        }

        100% {
            transform: translate(0, 0);
        }
    }

    @keyframes trendLine {
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

    @keyframes trendBody {
        0% {
            opacity: 0;
            transform: translateY(6px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .trend-header {
        animation: trendHeader .45s cubic-bezier(.16, 1, .3, 1) both;
    }

    .trend-arrow {
        animation: trendArrow .55s cubic-bezier(.16, 1, .3, 1) .3s both;
    }

    .trend-line {
        animation: trendLine .7s cubic-bezier(.16, 1, .3, 1) .45s both;
    }

    .trend-body {
        animation: trendBody .4s ease-out .9s both;
    }
</style>

<div class="py-2">

    <div class="d-flex align-items-end">

        <div class="trend-header">
            <div style="font-size: 10px; letter-spacing: 3px; color: #ea580c; font-weight: 700;">
                WEEKLY TREND
            </div>

            <div class="mt-1"
                style="font-size: 28px; line-height: 1; font-weight: 600; letter-spacing: -1.2px; color: #431407;">
                Bad Product
            </div>
        </div>

        <div class="text-right trend-header ml-auto"
            style="padding-right: 8px;">
            <div style="font-size: 11px; color: #9a3412; letter-spacing: 1px;">
                TREND
            </div>

            <div class="trend-arrow"
                style="font-size: 25px; line-height: 1; color: #ea580c;">
                ↗
            </div>
        </div>

    </div>

    <div class="mt-3 trend-line"
        style="height: 2px; background: linear-gradient(to right, #ea580c 0%, #ea580c 34%, #e2e8f0 34%, #e2e8f0 100%);">
    </div>

    <div class="d-flex align-items-center justify-content-between mt-3 trend-body">
        
    </div>

</div>