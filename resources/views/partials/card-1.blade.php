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

    .productivity-select {
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

    .productivity-field-label {
        font-size: 10px;
        letter-spacing: 1.5px;
        color: #94a3b8;
        font-weight: 600;
    }

    .productivity-frame {
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
            'slug' => 'dashboard-productivity-live',
        ],
    ];

    // Local (sementara dipakai sampai Grafana live tersedia)
    $grafanaProductivity = $grafanaEnvironments[rtrim(url('/'), '/')] ?? [
        'base' => 'http://10.7.10.101:3000',
        'uid'  => 'ad5x69z',
        'slug' => 'dashboard-productivity-local',
    ];

    $panelIdProductivity = 'panel-2';

    $currentYear = now()->year;
    $tahunOptions = [$currentYear, $currentYear - 1, $currentYear - 2];
    $defaultTahun = $currentYear;
@endphp

<div class="py-2">
    <div class="productivity-label"
        style="font-size: 10px; letter-spacing: 3px; color: #ea580c; font-weight: 600;">
        OPERATIONAL MONITORING
    </div>

    <div class="d-flex align-items-center justify-content-between mt-1">
        <div class="productivity-title"
            style="font-size: 28px; line-height: 1; font-weight: 600; color: #441b05;">
            Rekap Produktivitas
        </div>

        <div style="font-size: 27px; line-height: 1; color: #ea580c; padding-right: 8px; transform: translateY(-3px);">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    
    <div class="mt-3 productivity-line"
        style="height: 2px; background: linear-gradient(to right, #ea580c 100%, #ea580c 100%, #e2e8f0 43%, #e2e8f0 100%);">
    </div>

    <div class="d-flex align-items-center flex-wrap gap-3 mt-3 mb-2 productivity-body">

        <div class="d-flex align-items-center gap-2">
            <span class="productivity-field-label">TAHUN</span>

            <select id="tahunSelector" class="form-select form-select-sm productivity-select" style="width: 120px;">
                @foreach($tahunOptions as $tahun)
                    <option value="{{ $tahun }}" @selected($tahun == $defaultTahun)>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

    <div class="mt-3 productivity-frame">
        <iframe id="grafanaProductivity" width="100%" height="300" frameborder="0"></iframe>
    </div>
</div>

<script>
    (function () {
        const iframe = document.getElementById('grafanaProductivity');
        const tahunSelector = document.getElementById('tahunSelector');

        if (!iframe || !tahunSelector) {
            return;
        }

        const grafana = @json($grafanaProductivity);
        const panelId = @json($panelIdProductivity);
        const plant = @json($plant);

        function buildUrl(tahun) {
            const query = new URLSearchParams({
                timezone: 'Asia/Jakarta',
                'var-plant': plant,
                'var-tahun': tahun,
                orgId: '1',
                theme: 'light',
                panelId: panelId,
                kiosk: 'tv',
            }).toString().replace(/\+/g, '%20');

            return `${grafana.base}/d-solo/${grafana.uid}/${grafana.slug}?${query}`;
        }

        function refreshGrafana() {
            iframe.src = buildUrl(tahunSelector.value);
        }

        refreshGrafana();
        tahunSelector.addEventListener('change', refreshGrafana);
    })();
</script>