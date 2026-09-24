<style>
    @keyframes tempLabel {
        0% { opacity: 0; transform: translateY(-12px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes tempTitle {
        0% { opacity: 0; transform: translateY(25px); }
        70% { opacity: 1; transform: translateY(-3px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes tempUnit {
        0% { opacity: 0; transform: scale(.5) rotate(-12deg); }
        60% { opacity: 1; transform: scale(1.08) rotate(3deg); }
        100% { opacity: 1; transform: scale(1) rotate(0); }
    }

    @keyframes tempLine {
        0% { opacity: 0; clip-path: inset(0 100% 0 0); }
        100% { opacity: 1; clip-path: inset(0 0 0 0); }
    }

    @keyframes tempBody {
        0% { opacity: 0; transform: scaleY(0); transform-origin: top; }
        100% { opacity: 1; transform: scaleY(1); transform-origin: top; }
    }

    .temp-label {
        font-size: 10px;
        letter-spacing: 3px;
        color: #ea580c;
        font-weight: 600;
        animation: tempLabel .4s ease-out both;
    }

    .temp-title {
        font-size: 28px;
        line-height: 1;
        font-weight: 600;
        color: #441b05;
        animation: tempTitle .55s cubic-bezier(.16, 1, .3, 1) .12s both;
    }

    .temp-unit {
        font-size: 30px;
        line-height: 1;
        font-weight: 300;
        color: #ea580c;
        animation: tempUnit .5s cubic-bezier(.16, 1, .3, 1) .28s both;
    }

    .temp-line {
        height: 2px;
        background: linear-gradient(to right, #ea580c 100%, #e2e8f0 100%);
        animation: tempLine .65s cubic-bezier(.16, 1, .3, 1) .4s both;
    }

    .temp-body {
        animation: tempBody .4s ease-out .65s both;
    }

    .temp-field-label {
        font-size: 10px;
        letter-spacing: 1.5px;
        color: #94a3b8;
        font-weight: 600;
    }

    .temp-select {
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
    }

    .temp-empty {
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
    }

    .temp-frame {
        width: 100%;
        overflow: hidden;
        border-radius: 8px;
    }
</style>

@php
    // Konfigurasi Grafana per environment. Kunci = url('/') aplikasi Laravel.
    // Live
    $grafanaEnvironments = [
        'http://10.7.10.101/retort' => [              
            'base' => 'http://10.7.10.101:3000',      
            'uid'  => 'ad5x69y',
            'slug' => 'dashboard-suhu-live',
        ],
    ];

    // Local
    $grafana = $grafanaEnvironments[rtrim(url('/'), '/')] ?? [
        'base' => 'http://10.7.10.101:3000',
        'uid'  => 'ad5x69z',
        'slug' => 'dashboard-suhu-local',
    ];

    // Value harus sama persis dengan custom options variabel $periode di Grafana:
    // Hari Ini : now/d, Minggu Ini : now/w, Bulan Ini : now/M
    $periodeOptions = [
        'now/d' => 'Hari Ini',
        'now/w' => 'Minggu Ini',
        'now/M' => 'Bulan Ini',
    ];
    $defaultPeriode = 'now/d';
@endphp

<div class="py-2">

    <div class="temp-label">HOURLY DATA MONITORING</div>

    <div class="d-flex align-items-center justify-content-between mt-1">
        <div class="temp-title">Suhu Ruangan</div>

        <i class="fas fa-thermometer-half"
            style="font-size: 30px; color: #ea580c; padding-right: 8px; transform: translateY(-4px);">
        </i>
    </div>

    <div class="mt-3 temp-line"></div>

    <div class="d-flex align-items-center flex-wrap gap-3 mt-3 mb-2 temp-body">

        @if($areas->isNotEmpty())

            <div class="d-flex align-items-center gap-2">
                <span class="temp-field-label">AREA</span>

                <select id="areaSelector" class="form-select form-select-sm temp-select" style="width: 220px;">
                    @foreach($areas as $area)
                        <option value="{{ $area }}" @selected(request('area') == $area)>
                            {{ $area }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="temp-field-label">PERIODE</span>

                <select id="periodeSelector" class="form-select form-select-sm temp-select" style="width: 150px;">
                    @foreach($periodeOptions as $value => $label)
                        <option value="{{ $value }}" @selected($value === $defaultPeriode)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

        @else

            <span class="temp-field-label">AREA</span>
            <span class="temp-empty">Belum ada area untuk plant ini</span>

        @endif

    </div>

    @if($areas->isNotEmpty())
        <div class="mt-3 temp-frame">
            {{-- src diisi oleh JavaScript di bawah --}}
            <iframe id="grafanaSuhu" width="100%" height="360" frameborder="0"></iframe>
        </div>
    @endif

</div>

@if($areas->isNotEmpty())
<script>
    (function () {
        const iframe = document.getElementById('grafanaSuhu');
        const areaSelector = document.getElementById('areaSelector');
        const periodeSelector = document.getElementById('periodeSelector');

        if (!iframe || !areaSelector || !periodeSelector) {
            return;
        }

        const grafana = @json($grafana);
        const plant = @json($plant);

        function buildUrl(area, periode) {
            const query = new URLSearchParams({
                from: periode,
                to: periode,
                timezone: 'Asia/Jakarta',
                'var-plant': plant,
                'var-area': area,
                'var-periode': periode,
                refresh: '5s',
                orgId: '1',
                theme: 'light',
                panelId: 'panel-1',
            }).toString().replace(/\+/g, '%20');

            return `${grafana.base}/d-solo/${grafana.uid}/${grafana.slug}?${query}`;
        }

        function refreshGrafana() {
            iframe.src = buildUrl(areaSelector.value, periodeSelector.value);
        }

        // Muat pertama kali, lalu refresh otomatis saat area/periode diganti
        refreshGrafana();
        areaSelector.addEventListener('change', refreshGrafana);
        periodeSelector.addEventListener('change', refreshGrafana);
    })();
</script>
@endif