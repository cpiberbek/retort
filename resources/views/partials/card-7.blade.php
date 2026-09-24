<style>
    @keyframes packingTrendHeader {
        0% {
            opacity: 0;
            transform: translateY(12px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes packingTrendArrow {
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

    @keyframes packingTrendLine {
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

    @keyframes packingTrendBody {
        0% {
            opacity: 0;
            transform: translateY(6px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #badProductPackingCard .packing-trend-header {
        animation: packingTrendHeader .45s cubic-bezier(.16, 1, .3, 1) both;
    }

    #badProductPackingCard .packing-trend-arrow {
        animation: packingTrendArrow .55s cubic-bezier(.16, 1, .3, 1) .3s both;
    }

    #badProductPackingCard .packing-trend-line {
        animation: packingTrendLine .7s cubic-bezier(.16, 1, .3, 1) .45s both;
    }

    #badProductPackingCard .packing-trend-body {
        animation: packingTrendBody .4s ease-out .9s both;
    }

    #badProductPackingCard .defect-label {
        font-size: 10px;
        letter-spacing: 1.5px;
        color: #94a3b8;
        font-weight: 600;
    }

    #badProductPackingCard .defect-picker {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    #badProductPackingCard .defect-pill {
        display: inline-flex;
        align-items: center;
        height: 32px;
        padding: 0 10px 0 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        color: #431407;
        font-size: 12px;
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(68, 27, 5, .04);
    }

    #badProductPackingCard .defect-pill-remove {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        margin-left: 7px;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: #fff7ed;
        color: #ea580c;
        font-size: 10px;
        cursor: pointer;
    }

    #badProductPackingCard .defect-pill-remove:hover {
        background: #ea580c;
        color: #fff;
    }

    #badProductPackingCard .defect-add {
        position: relative;
        height: 32px;
        padding: 4px 12px;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
    }

    #badProductPackingCard .defect-add:hover {
        border-color: #ea580c;
        color: #ea580c;
        background: #fff7ed;
    }

    #badProductPackingCard .defect-add.filter-max {
        border-style: solid;
        cursor: not-allowed;
        color: #94a3b8;
        background: #f8fafc;
    }

    #badProductPackingCard .defect-add.filter-max:hover {
        border-color: #cbd5e1;
        color: #64748b;
        background: #f8fafc;
    }

    #badProductPackingCard .defect-add.filter-max:hover::after {
        content: "Hapus kategori lain untuk menambahkan kategori baru";
        position: absolute;
        left: 0;
        bottom: calc(100% + 8px);
        z-index: 200;
        width: 230px;
        padding: 7px 10px;
        border-radius: 6px;
        background: #431407;
        color: #fff;
        font-size: 11px;
        font-weight: 500;
        line-height: 1.4;
        text-align: left;
        letter-spacing: 0;
        box-shadow: 0 6px 16px rgba(68, 27, 5, .15);
        pointer-events: none;
    }

    #badProductPackingCard .defect-dropdown {
        position: relative;
    }

    #badProductPackingCard .defect-options {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        z-index: 100;
        display: none;
        min-width: 190px;
        max-height: 240px;
        overflow-y: auto;
        padding: 5px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(68, 27, 5, .08);
    }

    #badProductPackingCard .defect-dropdown.open .defect-options {
        display: block;
    }

    #badProductPackingCard .defect-option {
        display: block;
        width: 100%;
        padding: 7px 10px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #431407;
        font-size: 12px;
        text-align: left;
        cursor: pointer;
    }

    #badProductPackingCard .defect-option:hover {
        background: #fff7ed;
        color: #ea580c;
    }

    #badProductPackingCard .packing-period {
        height: 32px;
        width: 150px;
        padding: 4px 32px 4px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #fff;
        color: #431407;
        font-size: 12px;
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(67, 20, 7, .04);
        cursor: pointer;
    }

    #badProductPackingCard .packing-trend-frame {
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

    $defectOptions = [
        'jamur'           => 'Jamur',
        'lendir'          => 'Lendir',
        'klip_tajam'      => 'Klip Tajam',
        'pin_hole'        => 'Pin Hole',
        'air_trap_pvdc'   => 'Air Trap PVDC',
        'air_trap_produk' => 'Air Trap Produk',
        'keriput'         => 'Keriput',
        'bengkok'         => 'Bengkok',
        'non_kode'        => 'Non Kode',
        'over_lap'        => 'Over Lap',
        'kecil'           => 'Kecil',
        'terjepit'        => 'Terjepit',
        'double_klip'     => 'Double Klip',
        'seal_halus'      => 'Seal Halus',
        'basah'           => 'Basah',
        'dll'             => 'Dll',
    ];

    $selectedDefects = [
        'jamur',
        'lendir',
        'klip_tajam',
        'pin_hole',
        'air_trap_pvdc',
    ];
@endphp

<div id="badProductPackingCard" class="py-2">

    <div class="d-flex align-items-end">

        <div class="packing-trend-header">

            <div style="font-size: 10px; letter-spacing: 3px; color: #ea580c; font-weight: 700;">
                SORTING ITEMS
            </div>

            <div class="mt-1"
                style="font-size: 28px; line-height: 1; font-weight: 600; letter-spacing: -1.2px; color: #431407;">
                Bad Product [Packing]
            </div>

        </div>

        <div class="text-right packing-trend-header ml-auto"
            style="padding-right: 8px;">

            <div class="packing-trend-arrow"
                style="font-size: 25px; line-height: 1; color: #ea580c;">

                <i class="fas fa-box"></i>

            </div>

        </div>

    </div>

    <div class="mt-3 packing-trend-line"
        style="height: 2px; background: linear-gradient(to right, #ea580c 100%, #ea580c 100%, #e2e8f0 34%, #e2e8f0 100%);">
    </div>

    <div class="mt-3 packing-trend-body">

        <div class="d-flex align-items-center mb-3">

            <div class="defect-label mr-0">
                PERIODE
            </div>

            <select id="badProductPackingPeriode"
                class="packing-period">

                <option value="day">
                    Hari Ini
                </option>

                <option value="week" selected>
                    Minggu Ini
                </option>

                <option value="month">
                    Bulan Ini
                </option>

            </select>

        </div>

        <div class="defect-label mb-2">
            OPSI DEFECT (MAX.5):
        </div>

        <div class="defect-picker"
            id="badProductPackingPicker">

            @foreach($selectedDefects as $defect)

                <div class="defect-pill"
                    data-defect-pill="{{ $defect }}">

                    {{ $defectOptions[$defect] }}

                    <button type="button"
                        class="defect-pill-remove"
                        data-defect="{{ $defect }}">

                        <i class="fas fa-times"></i>

                    </button>

                </div>

            @endforeach

            <div class="defect-dropdown"
                id="badProductPackingDropdown">

                <button type="button"
                    class="defect-add"
                    id="badProductPackingAddButton">

                    <i class="fas fa-plus mr-1"></i>
                    KATEGORI

                </button>

                <div class="defect-options"
                    id="badProductPackingOptions">

                    @foreach($defectOptions as $value => $label)

                        @if(!in_array($value, $selectedDefects))

                            <button type="button"
                                class="defect-option"
                                data-defect="{{ $value }}">

                                {{ $label }}

                            </button>

                        @endif

                    @endforeach

                </div>

            </div>

        </div>

        <div class="mt-3 packing-trend-frame">

            <iframe id="grafanaBadProductPacking"
                width="100%"
                height="300"
                frameborder="0">
            </iframe>

        </div>

    </div>

</div>

<script>
(function () {

    const card = document.getElementById('badProductPackingCard');

    if (!card) {
        return;
    }

    const iframe = card.querySelector('#grafanaBadProductPacking');
    const periodeSelector = card.querySelector('#badProductPackingPeriode');
    const addButton = card.querySelector('#badProductPackingAddButton');
    const dropdown = card.querySelector('#badProductPackingDropdown');
    const optionsContainer = card.querySelector('#badProductPackingOptions');
    const picker = card.querySelector('#badProductPackingPicker');

    if (!iframe || !periodeSelector || !picker) {
        return;
    }

    const grafana = @json($grafana);
    const plant = @json($plant);
    const defectLabels = @json($defectOptions);

    let periode = periodeSelector.value || 'week';

    let selectedDefects = @json($selectedDefects);

    function buildUrl(defects) {

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

            refresh: '5s',

            orgId: '1',

            theme: 'light',

            panelId: 'panel-3',

        });

        defects.forEach(function (defect) {

            query.append(
                'var-defect_packing',
                defect
            );

        });

        return `${grafana.base}/d-solo/${grafana.uid}/${grafana.slug}?${query.toString().replace(/\+/g, '%20')}`;
    }

    function refreshGrafana() {

        iframe.src = buildUrl(selectedDefects);

    }

    function updateCategoryButton() {

        if (!addButton) {
            return;
        }

        if (selectedDefects.length >= 5) {

            addButton.classList.add('filter-max');

            addButton.setAttribute(
                'aria-disabled',
                'true'
            );

            addButton.innerHTML = `
                <i class="fas fa-filter mr-1"></i>
                FILTER MAX
            `;

        } else {

            addButton.classList.remove('filter-max');

            addButton.removeAttribute(
                'aria-disabled'
            );

            addButton.innerHTML = `
                <i class="fas fa-plus mr-1"></i>
                KATEGORI
            `;

        }

    }

    function renderPills() {

        picker
            .querySelectorAll('.defect-pill')
            .forEach(function (pill) {

                pill.remove();

            });

        selectedDefects.forEach(function (defect) {

            const pill = document.createElement('div');

            pill.className = 'defect-pill';

            pill.setAttribute(
                'data-defect-pill',
                defect
            );

            pill.innerHTML = `
                ${defectLabels[defect] ?? defect}
                <button type="button"
                    class="defect-pill-remove"
                    data-defect="${defect}">
                    <i class="fas fa-times"></i>
                </button>
            `;

            picker.insertBefore(
                pill,
                dropdown
            );

        });

        renderOptions();

        bindRemoveButtons();

        updateCategoryButton();

    }

    function renderOptions() {

        if (!optionsContainer) {
            return;
        }

        optionsContainer.innerHTML = '';

        Object.keys(defectLabels)
            .forEach(function (value) {

                if (selectedDefects.includes(value)) {
                    return;
                }

                const button = document.createElement('button');

                button.type = 'button';

                button.className = 'defect-option';

                button.dataset.defect = value;

                button.textContent = defectLabels[value];

                optionsContainer.appendChild(button);

            });

        bindOptionButtons();

    }

    function bindOptionButtons() {

        if (!optionsContainer) {
            return;
        }

        optionsContainer
            .querySelectorAll('.defect-option')
            .forEach(function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        const defect = this.dataset.defect;

                        if (!defect) {
                            return;
                        }

                        if (selectedDefects.includes(defect)) {
                            return;
                        }
                        
                        if (selectedDefects.length >= 5) {

                            selectedDefects.pop();

                        }

                        selectedDefects.push(defect);

                        renderPills();

                        refreshGrafana();

                        dropdown.classList.remove(
                            'open'
                        );

                    }
                );

            });

    }

    function bindRemoveButtons() {

        picker
            .querySelectorAll('.defect-pill-remove')
            .forEach(function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        if (selectedDefects.length <= 1) {
                            return;
                        }

                        const defect =
                            this.dataset.defect;

                        selectedDefects =
                            selectedDefects.filter(
                                function (item) {
                                    return item !== defect;
                                }
                            );

                        renderPills();

                        refreshGrafana();

                    }
                );

            });

    }

    if (periodeSelector) {

        periodeSelector.addEventListener(
            'change',
            function () {

                periode = this.value;

                refreshGrafana();

            }
        );

    }

    if (addButton && dropdown) {

        addButton.addEventListener(
            'click',
            function (event) {

                event.stopPropagation();

                if (selectedDefects.length >= 5) {

                    dropdown.classList.remove(
                        'open'
                    );

                    return;

                }

                dropdown.classList.toggle(
                    'open'
                );

            }
        );

    }

    document.addEventListener(
        'click',
        function (event) {

            if (
                dropdown &&
                !dropdown.contains(event.target) &&
                event.target !== addButton
            ) {

                dropdown.classList.remove(
                    'open'
                );

            }

        }
    );

    renderPills();

    updateCategoryButton();

    refreshGrafana();

})();
</script>