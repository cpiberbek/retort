@php
    $type_user = auth()->user()->type_user;
@endphp

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-hotdog text-white fs-3"></i>
        </div>
        <div class="sidebar-brand-text mx-3">E-Retort</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    @can('can access master data')
        <div class="sidebar-heading">Master Data</div>
        @php
            $masterActive =
                request()->routeIs('departemen.*') ||
                request()->routeIs('plant.*') ||
                request()->routeIs('produk.*') ||
                request()->routeIs('produksi.*') ||
                request()->routeIs('user.*') ||
                request()->routeIs('mesin.*');
            $masterActive =
                request()->routeIs('departemen.*') ||
                request()->routeIs('plant.*') ||
                request()->routeIs('produk.*') ||
                request()->routeIs('produksi.*') ||
                request()->routeIs('user.*') ||
                request()->routeIs('operator.*') ||
                request()->routeIs('engineer.*') ||
                request()->routeIs('mesin.*') ||
                request()->routeIs('supplier.*') ||
                request()->routeIs('supplier_rm.*') ||
                request()->routeIs('koordinator.*') ||
                request()->routeIs('list_chamber.*') ||
                request()->routeIs('area_hygiene.*') ||
                request()->routeIs('area_suhu.*') ||
                request()->routeIs('area_sanitasi.*') ||
                request()->routeIs('raw-material.*') ||
                request()->routeIs('premix.*') ||
                request()->routeIs('list_form.*');
        @endphp
        <li class="nav-item {{ $masterActive ? 'active' : '' }}">
            <a class="nav-link {{ $masterActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseMasterData" aria-expanded="{{ $masterActive ? 'true' : 'false' }}"
                aria-controls="collapseMasterData">
                <i class="fas fa-database"></i>
                <span>Master Data</span>
            </a>
            <div id="collapseMasterData" class="collapse {{ $masterActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('user.*') ? 'active' : '' }}"
                        href="{{ route('user.index') }}">User</a>
                    <a class="collapse-item {{ request()->routeIs('departemen.*') ? 'active' : '' }}"
                        href="{{ route('departemen.index') }}">Departemen</a>
                    <a class="collapse-item {{ request()->routeIs('plant.*') ? 'active' : '' }}"
                        href="{{ route('plant.index') }}">Plant</a>
                    <a class="collapse-item {{ request()->routeIs('produk.*') ? 'active' : '' }}"
                        href="{{ route('produk.index') }}">List Produk</a>
                    <a class="collapse-item {{ request()->routeIs('raw-material.*') ? 'active' : '' }}"
                        href="{{ route('raw-material.index') }}">List Bahan Baku</a>
                    <a class="collapse-item {{ request()->routeIs('premix.*') ? 'active' : '' }}"
                        href="{{ route('premix.index') }}">
                        List Premix
                    </a>
                    <a class="collapse-item {{ request()->routeIs('mesin.*') ? 'active' : '' }}"
                        href="{{ route('mesin.index') }}">List Mesin</a>
                    <a class="collapse-item {{ request()->routeIs('supplier.*') ? 'active' : '' }}"
                        href="{{ route('supplier.index') }}">List Supplier</a>
                    <a class="collapse-item {{ request()->routeIs('list_form.*') ? 'active' : '' }}"
                        href="{{ route('list_form.index') }}">List Form</a>
                    <a class="collapse-item {{ request()->routeIs('area_hygiene.*') ? 'active' : '' }}"
                        href="{{ route('area_hygiene.index') }}">Area GMP</a>
                    <a class="collapse-item {{ request()->routeIs('area_suhu.*') ? 'active' : '' }}"
                        href="{{ route('area_suhu.index') }}">Area Suhu</a>
                    <a class="collapse-item {{ request()->routeIs('area_sanitasi.*') ? 'active' : '' }}"
                        href="{{ route('area_sanitasi.index') }}">Area Sanitasi</a>
                    <a class="collapse-item {{ request()->routeIs('produksi.*') ? 'active' : '' }}"
                        href="{{ route('produksi.index') }}">Karyawan Produksi</a>
                    <a class="collapse-item {{ request()->routeIs('operator.*') ? 'active' : '' }}"
                        href="{{ route('operator.index') }}">Karyawan Pendukung</a>
                </div>
            </div>
        </li>
    @endcan

    @can('can access control')
        <div class="sidebar-heading">Access Control</div>
        @php
            $accessControlActive = request()->routeIs('permissions.*') || request()->routeIs('roles.*');
        @endphp
        <li class="nav-item {{ $accessControlActive ? 'active' : '' }}">
            <a class="nav-link {{ $accessControlActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseAccessControl" aria-expanded="{{ $accessControlActive ? 'true' : 'false' }}"
                aria-controls="collapseAccessControl">
                <i class="fas fa-lock"></i>
                <span>Access Control</span>
            </a>
            <div id="collapseAccessControl" class="collapse {{ $accessControlActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}"
                        href="{{ route('permissions.index') }}">Permissions</a>
                    <a class="collapse-item {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                        href="{{ route('roles.index') }}">Roles</a>
                </div>
            </div>
        </li>
    @endcan

    @can('can access form qc')
        @php

            // jika ada perubahan form, bisa ditambahkan disini
            $meatRoutes = ['checklistmagnettrap.*', 'mincing.*', 'metal.*', 'pemeriksaan-kekuatan-magnet-trap.*',];
            $stuffingRoutes = ['pvdc.*', 'labelisasi_pvdc.*', 'stuffing.*', 'wire.*'];
            $smfgRoutes = ['sampling_fg.*'];
            $retortRoutes = [ 'pemusnahan.*'];
            $packingRoutes = [
                'organoleptik.*',
                'packing.*',
                'prepacking.*',
                'release_packing.*',
                'sampling.*',
                'pemeriksaan_retain.*',
                'karton.*',
            ];
            $warehouseRoutes = ['inspections.*', 'packaging-inspections.*', 'loading-produks.*', 'sampel.*'];
            $cikandeRoutes = [
                'dispositions.*',
                'berita-acara.*',
                'penyimpangan-kualitas.*',
            ];


            $rteRoutes = ['retain_rte.*', 'release_packing_rte.*', 'pemasakan_rte.*'];
            $kebersihanRoutes = ['gmp.*', 'suhu.*', 'sanitasi.*'];
            $umumRoutes = ['klorin.*', 'timbangan.*', 'thermometer.*'];
            $chamberRoutes = ['washing.*','pemasakan.*','chamber.*'];

            // Aktivasi dropdown
            $meatPrepActive = request()->routeIs($meatRoutes);
            $stuffingActive = request()->routeIs($stuffingRoutes);
            $retortActive = request()->routeIs($retortRoutes);
            $packingActive = request()->routeIs($packingRoutes);
            $warehouseActive = request()->routeIs($warehouseRoutes);
            $cikandeActive = request()->routeIs($cikandeRoutes);
            $rteActive = request()->routeIs($rteRoutes);
            $kebersihanActive = request()->routeIs($kebersihanRoutes);
            $umumActive = request()->routeIs($umumRoutes);
            $chamberActive = request()->routeIs($chamberRoutes);
            $smfgActive = request()->routeIs($smfgRoutes);

        @endphp


        <!-- Heading -->
        <div class="sidebar-heading">Form QC</div>

        {{-- MEAT PREPARATION --}}
        <li class="nav-item {{ $meatPrepActive ? 'active' : '' }}">
            <a class="nav-link {{ $meatPrepActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseMeatPrep" aria-expanded="{{ $meatPrepActive ? 'true' : 'false' }}"
                aria-controls="collapseMeatPrep">
                <i class="fas fa-drumstick-bite"></i>
                <span>Meat Preparation</span>
            </a>
            <div id="collapseMeatPrep" class="collapse {{ $meatPrepActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('mincing.*') ? 'active' : '' }}"
                        href="{{ route('mincing.index') }}">Pemeriksaan Mincing - Emulsifying - Aging
                    </a>
                    <a class="collapse-item {{ request()->routeIs('metal.*') ? 'active' : '' }}"
                        href="{{ route('metal.index') }}">
                        Pengecekan Metal Detector
                    </a>
                    <a class="collapse-item {{ request()->routeIs('checklistmagnettrap.*') ? 'active' : '' }}"
                        href="{{ route('checklistmagnettrap.index') }}">
                        Checklist Cleaning Magnet Trap
                    </a>

                    <a class="collapse-item {{ request()->routeIs('pemeriksaan-kekuatan-magnet-trap.*') ? 'active' : '' }}"
                        href="{{ route('pemeriksaan-kekuatan-magnet-trap.index') }}">
                        Pemeriksaan Kekuatan Magnet Trap
                    </a>
                   
                </div>
            </div>
        </li>

        {{-- Stuffing --}}
        <li class="nav-item {{ $stuffingActive ? 'active' : '' }}">
            <a class="nav-link {{ $stuffingActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#UollapseStuffing" aria-expanded="{{ $stuffingActive ? 'true' : 'false' }}"
                aria-controls="UollapseStuffing">

                <i class="fas fa-dolly"></i>
                <span>Stuffing</span>
            </a>
            <div id="UollapseStuffing" class="collapse {{ $stuffingActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('stuffing.*') ? 'active' : '' }}"
                        href="{{ route('stuffing.index') }}">
                        Pemeriksaan Stuffing Sosis Retort
                    </a>
                    <a class="collapse-item {{ request()->routeIs('labelisasi_pvdc.*') ? 'active' : '' }}"
                        href="{{ route('labelisasi_pvdc.index') }}">
                        Kontrol Labelisasi PVDC
                    </a>
                    <a class="collapse-item {{ request()->routeIs('pvdc.*') ? 'active' : '' }}"
                        href="{{ route('pvdc.index') }}">
                        Data No. Lot PVDC
                    </a>
                    <a class="collapse-item {{ request()->routeIs('wire.*') ? 'active' : '' }}"
                        href="{{ route('wire.index') }}">
                        Data No. Lot Wire
                    </a>
                </div>
            </div>
        </li>

        {{-- Chamber --}}
        <li class="nav-item {{ $chamberActive ? 'active' : '' }}">
            <a class="nav-link {{ $chamberActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#UollapseStuffing" aria-expanded="{{ $chamberActive ? 'true' : 'false' }}"
                aria-controls="UollapseStuffing">

                <i class="fas fa-building"></i>
                <span>Chamber</span>
            </a>
            <div id="UollapseStuffing" class="collapse {{ $chamberActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('washing.*') ? 'active' : '' }}"
                        href="{{ route('washing.index') }}">Pemeriksaan Washing - Drying</a>
                    <a class="collapse-item {{ request()->routeIs('pemasakan.*') ? 'active' : '' }}"
                        href="{{ route('pemasakan.index') }}">Pengecekan Pemasakan</a>
                    <a class="collapse-item {{ request()->routeIs('chamber.*') ? 'active' : '' }}"
                        href="{{ route('chamber.index') }}">Verifikasi Timer Chamber</a>
                </div>
            </div>
        </li>


        <li class="nav-item {{ $smfgActive ? 'active' : '' }}">
            <a class="nav-link {{ $smfgActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseSMFG" aria-expanded="{{ $smfgActive ? 'true' : 'false' }}"
                aria-controls="collapseSMFG">

                <i class="fas fa-vials"></i>
                <span>SMFG</span>
            </a>

            <div id="collapseSMFG" class="collapse {{ $smfgActive ? 'show' : '' }}" data-bs-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item {{ request()->routeIs('sampling_fg.*') ? 'active' : '' }}"
                        href="{{ route('sampling_fg.index') }}">
                        Pemeriksaan Proses Sampling Finish Good
                    </a>

                </div>
            </div>
        </li>

        {{-- Retort --}}
        <li class="nav-item {{ $retortActive ? 'active' : '' }}">
            <a class="nav-link {{ $retortActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#rollapseRetort" aria-expanded="{{ $retortActive ? 'true' : 'false' }}"
                aria-controls="rollapseRetort">

                <i class="fas fa-industry"></i>
                <span>Retort</span>
            </a>
            <div id="rollapseRetort" class="collapse {{ $retortActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('pemusnahan.*') ? 'active' : '' }}"
                        href="{{ route('pemusnahan.index') }}">Pemusnahan Barang</a>
                </div>
            </div>
        </li>

        {{-- Packing --}}
        <li class="nav-item {{ $packingActive ? 'active' : '' }}">
            <a class="nav-link {{ $packingActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapsePacking" aria-expanded="{{ $packingActive ? 'true' : 'false' }}"
                aria-controls="collapsePacking">
                <i class="fas fa-box-open"></i>
                <span>Packing</span>
            </a>
            <div id="collapsePacking" class="collapse {{ $packingActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('sampling.*') ? 'active' : '' }}"
                        href="{{ route('sampling.index') }}">Data Sampling Produk</a>
                    <a class="collapse-item {{ request()->routeIs('release_packing.*') ? 'active' : '' }}"
                        href="{{ route('release_packing.index') }}">Data Release Packing</a>
                    <a class="collapse-item {{ request()->routeIs('packing.*') ? 'active' : '' }}"
                        href="{{ route('packing.index') }}">Pemeriksaan Proses Packing</a>
                    <a class="collapse-item {{ request()->routeIs('karton.*') ? 'active' : '' }}"
                        href="{{ route('karton.index') }}">Kontrol Labelisasi Cartoning</a>
                    <a class="collapse-item {{ request()->routeIs('organoleptik.*') ? 'active' : '' }}"
                        href="{{ route('organoleptik.index') }}">Pemeriksaan Organoleptik</a>
                    <a class="collapse-item {{ request()->routeIs('pemeriksaan_retain.*') ? 'active' : '' }}"
                        href="{{ route('pemeriksaan_retain.index') }}">Pemeriksaan Sampel Retain</a>
                    <a class="collapse-item {{ request()->routeIs('prepacking.*') ? 'active' : '' }}"
                        href="{{ route('prepacking.index') }}">Pengecekan Pre Packing</a>
                    
                </div>
            </div>
        </li>

        {{-- Warehouse --}}
        <li class="nav-item {{ $warehouseActive ? 'active' : '' }}">
            <a class="nav-link {{ $warehouseActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseWarehouse" aria-expanded="{{ $warehouseActive ? 'true' : 'false' }}"
                aria-controls="collapseWarehouse">
                <i class="fas fa-warehouse"></i>
                <span>Warehouse</span>
            </a>
            <div id="collapseWarehouse" class="collapse {{ $warehouseActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('inspections.*') ? 'active' : '' }}"
                        href="{{ route('inspections.index') }}">
                        Pemeriksaan  Kedatangan Bahan Baku dan Bahan Kimia
                    </a>
                    <a class="collapse-item {{ request()->routeIs('packaging-inspections.*') ? 'active' : '' }}"
                        href="{{ route('packaging-inspections.index') }}">
                        Pemeriksaan Kedatangan Bahan Kemas
                    </a>
                    <a class="collapse-item {{ request()->routeIs('loading-produks.*') ? 'active' : '' }}"
                        href="{{ route('loading-produks.index') }}">
                        Pemeriksaan Loading-Unloading Produk
                    </a>
                    <a class="collapse-item {{ request()->routeIs('sampel.*') ? 'active' : '' }}"
                        href="{{ route('sampel.index') }}">
                        Pengambilan Sampel</a>

                    
                </div>
            </div>
        </li>

        {{-- Cikande Form --}}
        <li class="nav-item {{ $cikandeActive ? 'active' : '' }}">
            <a class="nav-link {{ $cikandeActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseCikande" aria-expanded="{{ $cikandeActive ? 'true' : 'false' }}"
                aria-controls="collapseCikande">
                <i class="fas fa-clipboard-list"></i>
                <span>Cikande Form</span>
            </a>

            <div id="collapseCikande" class="collapse {{ $cikandeActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    

                    <a class="collapse-item {{ request()->routeIs('dispositions.*') ? 'active' : '' }}"
                        href="{{ route('dispositions.index') }}">
                        Disposisi Produk & Prosedur
                    </a>

                    <a class="collapse-item {{ request()->routeIs('berita-acara.*') ? 'active' : '' }}"
                        href="{{ route('berita-acara.index') }}">
                        Berita Acara
                    </a>

                    

                    <a class="collapse-item {{ request()->routeIs('penyimpangan-kualitas.*') ? 'active' : '' }}"
                        href="{{ route('penyimpangan-kualitas.index') }}">
                        Penyimpangan Kualitas Internal
                    </a>

                </div>
            </div>
        </li>

        {{-- RTE --}}
        <li class="nav-item {{ $rteActive ? 'active' : '' }}">
            <a class="nav-link {{ $rteActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseRTE" aria-expanded="{{ $rteActive ? 'true' : 'false' }}"
                aria-controls="collapseRTE">
                <i class="fas fa-clipboard-check"></i>
                <span>RTE</span>
            </a>
            <div id="collapseRTE" class="collapse {{ $rteActive ? 'show' : '' }}" data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('retain_rte.*') ? 'active' : '' }}"
                        href="{{ route('retain_rte.index') }}">Pemeriksaan Sampel Retain RTE
                    </a>
                    <a class="collapse-item {{ request()->routeIs('release_packing_rte.*') ? 'active' : '' }}"
                        href="{{ route('release_packing_rte.index') }}">
                        Data Release Packing RTE
                    </a>
                    <a class="collapse-item {{ request()->routeIs('pemasakan_rte.*') ? 'active' : '' }}"
                        href="{{ route('pemasakan_rte.index') }}">
                        Pengecekan Pemasakan RTE
                    </a>
                </div>
            </div>
        </li>

        {{-- Suhu & Kebersihan --}}
        <li class="nav-item {{ $kebersihanActive ? 'active' : '' }}">
            <a class="nav-link {{ $kebersihanActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseFormQC" aria-expanded="{{ $kebersihanActive ? 'true' : 'false' }}"
                aria-controls="collapseFormQC">

                <i class="fas fa-clipboard-list"></i>
                <span>Suhu & Kebersihan</span>
            </a>

            <div id="collapseFormQC" class="collapse {{ $kebersihanActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item {{ request()->routeIs('gmp.*') ? 'active' : '' }}"
                        href="{{ route('gmp.index') }}">
                        Pemeriksaan Personal Hygiene & Kesehatan
                    </a>

                    <a class="collapse-item {{ request()->routeIs('suhu.*') ? 'active' : '' }}"
                        href="{{ route('suhu.index') }}">
                        Pemeriksaan Suhu & RH
                    </a>

                    <a class="collapse-item {{ request()->routeIs('sanitasi.*') ? 'active' : '' }}"
                        href="{{ route('sanitasi.index') }}">
                        Kontrol Sanitasi
                    </a>
                </div>
            </div>
        </li>

        {{-- Umum --}}
        <li class="nav-item {{ $umumActive ? 'active' : '' }}">
            <a class="nav-link {{ $umumActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#Uollapseumum" aria-expanded="{{ $umumActive ? 'true' : 'false' }}"
                aria-controls="Uollapseumum">

                <i class="fas fa-cogs"></i>
                <span>Umum</span>
            </a>
            <div id="Uollapseumum" class="collapse {{ $umumActive ? 'show' : '' }}" data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('klorin.*') ? 'active' : '' }}"
                        href="{{ route('klorin.index') }}">Pengecekan Klorin
                    </a>
                    <a class="collapse-item {{ request()->routeIs('timbangan.*') ? 'active' : '' }}"
                        href="{{ route('timbangan.index') }}">Peneraan Timbangan</a>
                    <a class="collapse-item {{ request()->routeIs('thermometer.*') ? 'active' : '' }}"
                        href="{{ route('thermometer.index') }}">Peneraan Thermometer</a>

                </div>
            </div>
        </li>

        @php
            $searchActive = request()->routeIs('retort.*');
        @endphp

        <li class="nav-item {{ $searchActive ? 'active' : '' }}">
            <a class="nav-link {{ $searchActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseSearch" aria-expanded="{{ $searchActive ? 'true' : 'false' }}">

                <i class="fas fa-search"></i>
                <span>Traceability</span>
            </a>

            <div id="collapseSearch" class="collapse {{ $searchActive ? 'show' : '' }}">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('retort.index') ? 'active' : '' }}"
                        href="{{ route('retort.index') }}">
                        Cari Data
                    </a>
                </div>
            </div>
        </li>
    @endcan
    @php
        $traceRoutes = ['traceability.*', 'withdrawl.*', 'recall.*'];
        $traceActive = request()->routeIs($traceRoutes);
    @endphp

    @can('can access form spv')
        <li class="nav-item {{ $traceActive ? 'active' : '' }}">
            <a class="nav-link {{ $traceActive ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse"
                data-bs-target="#Uollapsetrace" aria-expanded="{{ $traceActive ? 'true' : 'false' }}"
                aria-controls="Uollapsetrace">

                <i class="fas fa-link"></i>
                <span>Traceability</span>
            </a>

            <div id="Uollapsetrace" class="collapse {{ $traceActive ? 'show' : '' }}"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item {{ request()->routeIs('traceability.*') ? 'active' : '' }}"
                        href="{{ route('traceability.index') }}">
                        Laporan Traceability
                    </a>

                    <a class="collapse-item {{ request()->routeIs('withdrawl.*') ? 'active' : '' }}"
                        href="{{ route('withdrawl.index') }}">
                        Laporan Withdrawl
                    </a>

                    <a class="collapse-item {{ request()->routeIs('recall.*') ? 'active' : '' }}"
                        href="{{ route('recall.index') }}">
                        Recall
                    </a>

                </div>
            </div>
        </li>
    @endcan


    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>


<style>
    /* --- Kustomisasi Sidebar Menjadi Merah Cerah & Elegan --- */
    #accordionSidebar {
        /* Menggunakan !important untuk menimpa warna bawaan template tanpa mengubah HTML */
        background: linear-gradient(180deg, #d32f2f 0%, #9e0202 100%) !important;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2) !important;
    }

    /* Efek Hover bergeser pada menu utama */
    #accordionSidebar .nav-item .nav-link {
        transition: all 0.3s ease;
    }

    #accordionSidebar .nav-item .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        transform: translateX(5px);
    }

    /* Penanda menu utama sedang aktif */
    #accordionSidebar .nav-item.active .nav-link {
        font-weight: bold;
        background-color: rgba(255, 255, 255, 0.2) !important;
        border-left: 4px solid #fff;
    }

    /* Styling Box Dropdown Submenu */
    #accordionSidebar .collapse-inner {
        background-color: #ffffff !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
        padding: 0.5rem !important;
    }

    /* Styling text Submenu */
    #accordionSidebar .collapse-inner .collapse-item {
        white-space: unset !important;
        transition: all 0.2s ease;
        border-radius: 4px;
        margin-bottom: 2px;
        color: #444 !important;
        /* Warna text abu gelap agar jelas */
    }

    /* Hover submenu (Warna merah muda) */
    #accordionSidebar .collapse-inner .collapse-item:hover {
        background-color: #f8d7da !important;
        color: #9e0202 !important;
        transform: translateX(3px);
    }

    /* Submenu aktif (Warna merah tegas) */
    #accordionSidebar .collapse-inner .collapse-item.active {
        background-color: #ffe6e6 !important;
        color: #d32f2f !important;
        font-weight: bold;
        border-left: 3px solid #d32f2f;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle sidebar
    const sidebar = document.getElementById('accordionSidebar');
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        sidebar.classList.toggle('minimized');
    });

    // Collapse dropdown fix saat minimized
    document.querySelectorAll('#accordionSidebar .nav-link[data-bs-toggle="collapse"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (sidebar.classList.contains('minimized')) {
                const targetId = link.getAttribute('data-bs-target');
                const collapseEl = document.querySelector(targetId);
                collapseEl.classList.toggle('show');
            }
        });
    });
</script>
