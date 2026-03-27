<nav class="navbar navbar-expand navbar-light topbar mb-4 static-top" style="background: linear-gradient(90deg, #A60000 0%, #850000 100%); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3 text-white">
        <i class="fa fa-bars"></i>
    </button>

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