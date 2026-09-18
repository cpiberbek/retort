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

@php
    $baseUrl = rtrim(url('/'), '/');

    if ($baseUrl === 'http://127.0.0.1:8000') {
        $grafanaBaseUrl = 'http://10.7.10.101:3000';
        $grafanaDashboard = 'dashboard-suhu-local';
    } elseif ($baseUrl === 'http://10.7.10.101') {
        $grafanaBaseUrl = 'GRAFANA_LIVE_NANTI';
        $grafanaDashboard = 'dashboard-suhu-live';
    } else {
        $grafanaBaseUrl = 'http://10.7.10.101:3000';
        $grafanaDashboard = 'dashboard-suhu-local';
    }
@endphp

<div class="py-2">

    <div class="temp-label"
        style="
            font-size: 10px;
            letter-spacing: 3px;
            color: #ea580c;
            font-weight: 600;
        ">
        HOURLY DATA MONITORING
    </div>

    <div class="d-flex align-items-center justify-content-between mt-1">

        <div class="temp-title"
            style="
                font-size: 28px;
                line-height: 1;
                font-weight: 600;
                color: #441b05;
            ">
            Suhu Ruangan
        </div>

        <div class="temp-unit"
            style="
                font-size: 30px;
                line-height: 1;
                color: #ea580c;
                font-weight: 300;
            ">
            °C
        </div>

    </div>

    <div class="mt-3 temp-line"
        style="
            height: 2px;
            background: linear-gradient(
                to right,
                #ea580c 43%,
                #ea580c 43%,
                #e2e8f0 43%,
                #e2e8f0 100%
            );
        ">
    </div>

    <div class="d-flex align-items-center justify-content-between mt-3 temp-body">

        <div class="d-flex align-items-center gap-2 mt-2 mb-2">

            <span style="
                font-size: 10px;
                letter-spacing: 1.5px;
                color: #94a3b8;
                font-weight: 600;
            ">
                AREA
            </span>

            @if($areas->isNotEmpty())

                <form id="suhuAreaForm"
                    class="d-flex align-items-center gap-2 m-0">

                    <select
                        name="area"
                        id="areaSelector"
                        class="form-select form-select-sm"
                        style="
                            width: 220px;
                            height: 34px;
                            border: 1px solid #e2e8f0;
                            border-radius: 8px;
                            background-color: #fff;
                            color: #441b05;
                            font-size: 12px;
                            font-weight: 500;
                            padding: 4px 32px 4px 12px;
                            box-shadow: 0 2px 6px rgba(68, 27, 5, .04);
                            cursor: pointer;
                        "
                    >

                        @foreach($areas as $area)

                            <option
                                value="{{ $area }}"
                                {{ request('area') == $area ? 'selected' : '' }}
                            >
                                {{ $area }}
                            </option>

                        @endforeach

                    </select>

                    <button
                        type="submit"
                        style="
                            height: 34px;
                            padding: 0 14px;
                            border: 0;
                            border-radius: 8px;
                            background: #ea580c;
                            color: #fff;
                            font-size: 12px;
                            font-weight: 600;
                            cursor: pointer;
                            box-shadow: 0 2px 6px rgba(234, 88, 12, .18);
                            white-space: nowrap;
                        "
                    >
                        Tampilkan
                    </button>

                </form>

            @else

                <span style="
                    display: inline-flex;
                    align-items: center;
                    min-height: 34px;
                    padding: 0 12px;
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    background: #f8fafc;
                    color: #94a3b8;
                    font-size: 12px;
                    font-weight: 500;
                ">
                    Belum ada area untuk plant ini
                </span>

            @endif

        </div>

    </div>


    @if($areas->isNotEmpty())

        @php
            $grafanaArea = request('area', $areas->first());

            $grafanaFrom = now()->startOfDay()->timestamp * 1000;
            $grafanaTo = now()->endOfDay()->timestamp * 1000;
        @endphp

        <div
            class="mt-3"
            style="
                width: 100%;
                overflow: hidden;
                border-radius: 8px;
            "
        >

            <iframe
                id="grafanaSuhu"
                src="{{ $grafanaBaseUrl }}/d-solo/ad5x69z/{{ $grafanaDashboard }}?from={{ $grafanaFrom }}&to={{ $grafanaTo }}&timezone=browser&var-plant={{ urlencode($plant) }}&var-area={{ urlencode($grafanaArea) }}&refresh=5s&orgId=1&theme=light&panelId=panel-1"
                width="100%"
                height="200"
                frameborder="0"
            >
            </iframe>

        </div>

    @endif

</div>


<script>
    document.getElementById('suhuAreaForm')?.addEventListener('submit', function(e) {

        e.preventDefault();

        const area = document.getElementById('areaSelector').value;
        const iframe = document.getElementById('grafanaSuhu');

        if (!iframe) {
            return;
        }

        const from = {{ $grafanaFrom ?? 0 }};
        const to = {{ $grafanaTo ?? 0 }};
        const plant = @json($plant);

        const grafanaBaseUrl = @json($grafanaBaseUrl);
        const grafanaDashboard = @json($grafanaDashboard);

        const url =
            grafanaBaseUrl +
            '/d-solo/ad5x69z/' +
            grafanaDashboard +
            '?from=' + from +
            '&to=' + to +
            '&timezone=browser' +
            '&var-plant=' + encodeURIComponent(plant) +
            '&var-area=' + encodeURIComponent(area) +
            '&refresh=5s' +
            '&orgId=1' +
            '&theme=light' +
            '&panelId=panel-1';

        iframe.src = url;
    });
</script>