<nav class="navbar navbar-expand navbar-light topbar mb-4 static-top" style="background: linear-gradient(90deg, #A60000 0%, #850000 100%); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3 text-white">
        <i class="fa fa-bars"></i>
    </button>

    @php
        $user = auth()->user();

        $plantAkun = \App\Models\Plant::where(
            'uuid',
            $user->getRawOriginal('plant')
        )->value('plant');

        $plantActive = \App\Models\Plant::where(
            'uuid',
            $user->plant_active
        )->value('plant') ?? $plantAkun;

        $roleAkun = ucfirst(
            \Spatie\Permission\Models\Role::whereIn('id', function ($query) use ($user) {
                $query->select('role_id')
                    ->from('model_has_roles')
                    ->where('model_id', $user->uuid);
            })->value('name') ?? '-'
        );

        $plantOpsiRaw = \Illuminate\Support\Facades\DB::table('users')
            ->where('uuid', $user->uuid)
            ->value('plant_option');

        $plantOpsi = json_decode($plantOpsiRaw, true) ?? [];

        $plants = \App\Models\Plant::whereIn('uuid', $plantOpsi)->get();
    @endphp

    <div class="card border-0 shadow-sm mb-2 mt-2 w-60 plant-info-card" style="border-radius: 12px;">
        <div class="card-body py-2 px-4">

            <div class="plant-desktop-info d-flex align-items-center" style="gap: 30px; font-weight: 700;">

                <div>
                    <div class="text-muted" style="font-size: 11px; font-weight: 700;">
                        Role Akun:
                    </div>
                    <div style="font-size: 13px; font-weight: 700;">
                        {{ $roleAkun }}
                    </div>
                </div>

                <span class="text-muted" style="font-size: 20px; font-weight: 300;">│</span>

                <div>
                    <div class="text-muted" style="font-size: 11px; font-weight: 700;">
                        Plant Akun Asal:
                    </div>
                    <div style="font-size: 13px; font-weight: 700;">
                        Plant {{ $plantAkun ?? '-' }}
                    </div>
                </div>

                <span class="text-muted" style="font-size: 20px; font-weight: 300;">│</span>

                <div>
                    <div class="text-muted" style="font-size: 11px; font-weight: 700;">
                        Data Plant yang Sedang Diakses:
                    </div>
                    <div class="text-primary" style="font-size: 13px; font-weight: 700;">
                        Plant {{ $plantActive ?? '-' }}
                    </div>
                </div>

                @if (!empty($plantOpsi))
                    <span class="text-muted" style="font-size: 20px; font-weight: 300;">│</span>

                    <div>
                        <div class="text-muted" style="font-size: 11px; font-weight: 700;">
                            Ganti Data Akses Plant:
                        </div>

                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <select id="plantActiveSelect" class="form-select form-select-sm" style="min-width: 180px;">
                                <option value="" disabled selected>Pilih Data Plant</option>

                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->uuid }}">
                                        Plant {{ $plant->plant }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" id="btnPindahPlant" class="btn btn-sm btn-primary" disabled>
                                Pindah
                            </button>
                        </div>
                    </div>
                @endif

            </div>

            @if (!empty($plantOpsi))
                <div class="plant-mobile-info">

                    <div class="text-muted plant-mobile-label">
                         Ganti Data Akses Plant:
                    </div>

                    <div class="d-flex align-items-center plant-mobile-controls">

                        <select id="plantActiveSelectMobile" class="form-select form-select-sm">
                            <option value="" disabled selected>
                                Pilih Data Plant
                            </option>

                            @foreach ($plants as $plant)
                                <option value="{{ $plant->uuid }}">
                                    Plant {{ $plant->plant }}
                                </option>
                            @endforeach
                        </select>

                        <button type="button"
                            id="btnPindahPlantMobile"
                            class="btn btn-sm btn-primary"
                            disabled>
                            Pindah
                        </button>

                        <div class="plant-info-wrapper">
                            <button type="button"
                                id="plantInfoButton"
                                class="btn btn-sm btn-light rounded-circle plant-info-button"
                                aria-label="Informasi plant"
                                aria-expanded="false">
                                <i class="fas fa-info"></i>
                            </button>

                            <div id="plantInfoPopup" class="plant-info-popup">

                                <div class="plant-info-row">
                                    <div class="text-muted">
                                        Role Akun:
                                    </div>
                                    <div>
                                        {{ $roleAkun }}
                                    </div>
                                </div>

                                <div class="plant-info-row">
                                    <div class="text-muted">
                                        Plant Akun Asal:
                                    </div>
                                    <div>
                                        Plant {{ $plantAkun ?? '-' }}
                                    </div>
                                </div>

                                <div class="plant-info-row">
                                    <div class="text-muted">
                                        Data Plant yang Sedang Diakses:
                                    </div>
                                    <div class="text-primary">
                                        Plant {{ $plantActive ?? '-' }}
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            @endif

        </div>
    </div>

    <ul class="navbar-nav ml-auto">

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle"
                href="#"
                id="userDropdown"
                role="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">

                <span class="mr-2 d-none d-lg-inline text-white small">
                    @if(auth()->check())
                        Hallo, {{ auth()->user()->name }}
                    @else
                        Hallo, Guest
                    @endif
                </span>

                <img class="img-profile rounded-circle"
                    src="{{ auth()->user()->photo ? asset('assets/' . auth()->user()->photo) : asset('assets/profil.jpg') }}">
            </a>

            <div class="bg-white dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="dropdown-item d-flex align-items-center" type="submit">
                        <i class="fas fa-sign-out-alt fa-fw me-2 text-dark"></i>&nbsp;
                        Logout
                    </button>
                </form>

            </div>
        </li>

    </ul>
</nav>

<script>
    function changePlant(select, button) {
        if (!select || !button) return;

        select.addEventListener('change', function () {
            button.disabled = !this.value;
        });

        button.addEventListener('click', function () {
            if (!select.value) return;

            this.disabled = true;

            fetch('{{ route('user.change-plant') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    plant_active: select.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    this.disabled = false;
                }
            })
            .catch(() => {
                this.disabled = false;
            });
        });
    }

    changePlant(
        document.getElementById('plantActiveSelect'),
        document.getElementById('btnPindahPlant')
    );

    changePlant(
        document.getElementById('plantActiveSelectMobile'),
        document.getElementById('btnPindahPlantMobile')
    );

    const plantInfoButton = document.getElementById('plantInfoButton');
    const plantInfoPopup = document.getElementById('plantInfoPopup');

    if (plantInfoButton && plantInfoPopup) {
        plantInfoButton.addEventListener('click', function (event) {
            event.stopPropagation();

            const isOpen = plantInfoPopup.classList.contains('show');

            plantInfoPopup.classList.toggle('show');

            this.setAttribute('aria-expanded', !isOpen);
        });

        document.addEventListener('click', function (event) {
            if (!plantInfoPopup.contains(event.target) &&
                !plantInfoButton.contains(event.target)) {
                plantInfoPopup.classList.remove('show');
                plantInfoButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
</script>

<style>
    body,
    .navbar,
    .dropdown-menu,
    .navbar-text {
        font-family: 'Poppins', sans-serif;
    }

    .topbar {
        background: #d32f2f !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .navbar-text h5 {
        margin: 0;
        font-weight: 600;
    }

    .img-profile {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 2px solid rgba(255,255,255,0.9);
    }

    .img-profile:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(255,255,255,0.4);
    }

    .topbar .dropdown-menu {
        top: calc(100% + 0.5rem);
        right: 0;
        left: auto !important;
        background: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
        z-index: 5000 !important;
        padding: 0.5rem 0;
    }

    .topbar .dropdown-menu .dropdown-item {
        color: #444 !important;
        font-weight: 500;
        transition: 0.2s;
        padding: 0.6rem 1.5rem;
    }

    .topbar .dropdown-menu .dropdown-item i {
        color: #888 !important;
    }

    .topbar .dropdown-menu .dropdown-item:hover {
        background-color: #fdf2f2 !important;
        color: #d32f2f !important;
        transform: translateX(3px);
    }

    .topbar .dropdown-menu .dropdown-item:hover i {
        color: #d32f2f !important;
    }

    .navbar,
    .topbar {
        overflow: visible !important;
        position: relative;
        z-index: 1000;
    }

    .plant-mobile-info {
        display: none;
    }

    .plant-mobile-controls {
        gap: 8px;
    }

    .plant-mobile-controls select {
        min-width: 0;
        flex: 1;
    }

    .plant-info-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .plant-info-button {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .plant-info-button i {
        font-size: 12px;
    }

    .plant-info-popup {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 10px);
        width: 240px;
        padding: 14px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.18);
        z-index: 9999;
    }

    .plant-info-popup.show {
        display: block;
    }

    .plant-info-row {
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .plant-info-row:last-child {
        margin-bottom: 0;
    }

    .plant-info-row .text-muted {
        font-size: 10px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    @media (max-width: 768px) and (orientation: portrait) {
        .plant-desktop-info {
            display: none !important;
        }

        .plant-mobile-info {
            display: block !important;
        }

        .plant-info-card {
            width: 100% !important;
        }
    }

    @media (max-width: 768px) {
        .sidebar-dark .nav-item .nav-link {
            width: 100%;
        }
    }
</style>