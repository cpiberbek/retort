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

    .trend-field-label {
        font-size: 10px;
        letter-spacing: 1.5px;
        color: #94a3b8;
        font-weight: 600;
    }

    .trend-select {
        height: 34px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #fff;
        color: #431407;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 32px 4px 12px;
        box-shadow: 0 2px 6px rgba(67, 20, 7, .04);
        cursor: pointer;
    }

    .trend-frame {
        width: 100%;
        overflow: hidden;
        border-radius: 8px;
    }
</style>

@php
    $grafanaEnvironments = [
        'http://10.7.10.101/retort' => [
            'base' => 'http://10.7.10.101:3000',
            'uid'  => 'ad5x69y',
            'slug' => 'dashboard-suhu-live',
        ],
    ];

    $grafana = $grafanaEnvironments[rtrim(url('/'), '/')] ?? [
        'base' => 'http://10.7.10.101:3000',
        'uid'  => 'ad5x69z',
        'slug' => 'dashboard-suhu-local',
    ];

    $periodeOptions = [
        'day'   => 'Hari Ini',
        'week'  => 'Minggu Ini',
        'month' => 'Bulan Ini',
    ];

    $defaultPeriode = 'week';
@endphp

<div class="py-2">

    <div class="d-flex align-items-end">

        <div class="trend-header">
            <div style="font-size: 10px; letter-spacing: 3px; color: #ea580c; font-weight: 700;">
                REJECT TREND
            </div>

            <div class="mt-1"
                style="font-size: 28px; line-height: 1; font-weight: 600; letter-spacing: -1.2px; color: #431407;">
                Bad Product [Chamber]
            </div>
        </div>

        <div class="text-right trend-header ml-auto"
            style="padding-right: 8px;">

            <div class="trend-arrow"
                style="font-size: 25px; line-height: 1; color: #ea580c;">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>

    </div>

    <div class="mt-3 trend-line"
        style="height: 2px; background: linear-gradient(to right, #ea580c 100%, #ea580c 100%, #e2e8f0 34%, #e2e8f0 100%);">
    </div>

    <div class="d-flex align-items-center justify-content-between mt-3 trend-body">

        <div class="d-flex align-items-center gap-2">
            <span class="trend-field-label">PERIODE</span>

            <select id="badProductPeriode" class="form-select form-select-sm trend-select"
                style="width: 150px;">
                @foreach($periodeOptions as $value => $label)
                    <option value="{{ $value }}" @selected($value === $defaultPeriode)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

    <div class="mt-3 trend-frame">
        <iframe id="grafanaBadProduct"
            width="100%"
            height="368"
            frameborder="0">
        </iframe>
    </div>

</div>

<script>
    (function () {
        const iframe = document.getElementById('grafanaBadProduct');
        const periodeSelector = document.getElementById('badProductPeriode');

        if (!iframe || !periodeSelector) {
            return;
        }

        const grafana = @json($grafana);
        const plant = @json($plant);

        function buildUrl(periode) {
            const query = new URLSearchParams({
                from: periode === 'day'
                    ? 'now/d'
                    : periode === 'week'
                        ? 'now/w'
                        : 'now/M',

                to: 'now',

                timezone: 'Asia/Jakarta',
                'var-plant': plant,
                'var-periode': periode,
                'var-area': 'Chill Room (Meat)',
                refresh: '5s',
                orgId: '1',
                theme: 'light',
                panelId: 'panel-4',
                showPanelMenu: 'false',
            }).toString().replace(/\+/g, '%20');

            return `${grafana.base}/d-solo/${grafana.uid}/${grafana.slug}?${query}`;
        }

        function refreshGrafana() {
            iframe.src = buildUrl(periodeSelector.value);
        }

        refreshGrafana();

        periodeSelector.addEventListener('change', refreshGrafana);
    })();
</script>