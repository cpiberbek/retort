<nav class="navbar navbar-expand navbar-light topbar mb-4 static-top" style="background: linear-gradient(90deg, #A60000 0%, #850000 100%); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <!-- Sidebar Toggle (Topbar) -->
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

    <div class="card border-0 shadow-sm mb-2 mt-2 w-60" style="border-radius: 12px;">
        <div class="card-body py-2 px-4">
            <div class="d-flex align-items-center">

                <div class="d-flex align-items-center" style="gap: 30px; font-weight: 700;">

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

                    {{-- hide if plant opsi null --}}
                    @if (!empty($plantOpsi))
                        <span class="text-muted" style="font-size: 20px; font-weight: 300;">│</span>

                        <div>
                            <div class="text-muted" style="font-size: 11px; font-weight: 700;">
                                Ganti Data Plant yang Ingin Diakses:
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

            </div>
        </div>
    </div>


    
    <ul class="navbar-nav ml-auto">


        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
            <!-- Dropdown - User Information -->
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
const plantSelect = document.getElementById('plantActiveSelect');
const btnPindahPlant = document.getElementById('btnPindahPlant');

if (plantSelect && btnPindahPlant) {
    plantSelect.addEventListener('change', function () {
        btnPindahPlant.disabled = !this.value;
    });

    btnPindahPlant.addEventListener('click', function () {
        if (!plantSelect.value) return;

        this.disabled = true;

        fetch('{{ route('user.change-plant') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                plant_active: plantSelect.value
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
</script>

<style>
/* Gunakan font modern Poppins */
body, .navbar, .dropdown-menu, .navbar-text {
    font-family: 'Poppins', sans-serif;
}

/* --- Kustomisasi Topbar Menjadi Merah Cerah --- */
/* Menggunakan #d32f2f agar menyatu sempurna dengan ujung atas sidebar */
.topbar {
    background: #d32f2f !important; 
    /* Jika ingin sedikit gradasi cerah, gunakan baris di bawah ini dan hapus baris di atas: */
    /* background: linear-gradient(90deg, #d32f2f 0%, #c62828 100%) !important; */
    box-shadow: 0 4px 10px rgba(0,0,0,0.15) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.navbar-text h5 {
    margin: 0;
    font-weight: 600;
}

/* Profile image styling */
.img-profile {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 2px solid rgba(255,255,255,0.9);
}
.img-profile:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(255,255,255,0.4);
}

/* Dropdown menu (Logout) dibuat putih bersih & modern */
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

/* Text Logout Dropdown */
.topbar .dropdown-menu .dropdown-item {
    color: #444 !important;
    font-weight: 500;
    transition: 0.2s;
    padding: 0.6rem 1.5rem;
}
.topbar .dropdown-menu .dropdown-item i {
    color: #888 !important;
}

/* Efek saat Logout disorot kursor */
.topbar .dropdown-menu .dropdown-item:hover {
    background-color: #fdf2f2 !important;
    color: #d32f2f !important;
    transform: translateX(3px);
}
.topbar .dropdown-menu .dropdown-item:hover i {
    color: #d32f2f !important;
}

.navbar, .topbar {
    overflow: visible !important;
    position: relative;
    z-index: 1000;
}

@media(max-width:768px){
    .sidebar-dark .nav-item .nav-link{
        width: 100%;
    }
}
</style>