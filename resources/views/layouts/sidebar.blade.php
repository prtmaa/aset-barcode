<aside class="main-sidebar sidebar-dark-primary elevation-4">


    <!-- Sidebar -->
    <div class="sidebar">

        <div class="user-panel mt-3 pb-2 mb-3 d-flex align-items-center">
            <div class="image">
                {{-- <img src="{{ asset('favicon.png') }}" class="img-circle elevation-2"
                    style="width:30px; height:30px; object-fit:cover;" alt="Logo"> --}}
            </div>
            <div class="info ml-2">
                <a href="#" class="d-block font-weight-bold">Stok MTC</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Item -->
                <li class="nav-item">
                    <a href="{{ route('asset.index') }}"
                        class="nav-link {{ request()->routeIs('asset.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Aset</p>
                    </a>
                </li>

                <!-- Scan -->
                <li class="nav-item">
                    <a href="{{ route('scan.index') }}"
                        class="nav-link {{ request()->routeIs('scan.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-qrcode"></i>
                        <p>Scan</p>
                    </a>
                </li>

                <!-- Data -->
                <li
                    class="nav-item has-treeview {{ request()->is('kategori*') || request()->is('lokasi*') || request()->is('employe*') || request()->is('assetattribute*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('kategori*') || request()->is('lokasi*') || request()->is('employe*') || request()->is('assetattribute*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>Master <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('kategori') }}"
                                class="nav-link {{ request()->is('kategori*') ? 'active' : '' }}">
                                <i class="fas fa-tags nav-icon"></i>
                                <p>Kategori</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('lokasi') }}"
                                class="nav-link {{ request()->is('lokasi*') ? 'active' : '' }}">
                                <i class="fas fa-map-marker-alt nav-icon"></i>
                                <p>Lokasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('employe') }}"
                                class="nav-link {{ request()->is('employe*') ? 'active' : '' }}">
                                <i class="fas fa-user-tie nav-icon"></i>
                                <p>PIC</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('assetattribute.index') }}"
                                class="nav-link {{ request()->routeIs('assetattribute.*') ? 'active' : '' }}">
                                <i class="fas fa-cogs nav-icon"></i>
                                <p>Spesifikasi</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
