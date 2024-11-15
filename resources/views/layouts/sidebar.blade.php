<nav id="sidebar" aria-label="Main Navigation">
    <!-- Side Header -->
    <div class="bg-header-dark">
        <div class="content-header bg-white-0 justify-content-lg-center">
            <!-- Logo -->
            <a class="link-fx font-w600 font-size-lg text-white" href="/">
                <span class="smini-hidden">
                    <span class="text-white-75">BPSDM</span><span class="text-white"> Kaltim</span>
                </span>
            </a>
            <!-- END Logo -->
            <!-- Options -->
            <div>
                <!-- Toggle Sidebar Style -->
                <!-- Close Sidebar, Visible only on mobile screens -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <a class="d-lg-none text-white ml-2" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
                    <i class="fa fa-times-circle"></i>
                </a>
                <!-- END Close Sidebar -->
            </div>
            <!-- END Options -->
        </div>
    </div>
    <!-- END Side Header -->

    <!-- User Info -->
    <div class="smini-hidden" style="height: 104px;" >
        <div class="content-side content-side-full bg-gray-lighter d-flex align-items-center" style="height: 100%">
            <a class="img-link d-inline-block" href="javascript:void(0)">
                <img class="img-avatar img-avatar48 img-avatar-thumb" src="{{ is_null(Auth::user()->photo) ? asset('media/avatars/avatar8.jpg') : asset(Storage::url(Auth::user()->photo)) }}" alt="">
            </a>
            <div class="ml-3">
                <a class="font-size-sm text-dual" href="javascript:void(0)">{{ Auth::user()->name }}</a>
                <div class="font-size-sm text-dual text-uppercase">{{ Auth::user()->usergroup }}</div>
            </div>
        </div>
    </div>
    <!-- END User Info -->

    <!-- Side Navigation -->
    <div class="content-side content-side-full">
        <ul class="nav-main">
            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('dashboard') ? ' active' : '' }}" href="{{ route('backend.dashboard') }}">
                    <i class="nav-main-link-icon si si-home"></i>
                    <span class="nav-main-link-name">Beranda</span>
                    {{-- <span class="nav-main-link-badge badge badge-pill badge-success">10</span> --}}
                </a>
            </li>
            @can('isAdmin')
            <li class="nav-main-heading">Master</li>
            <li class="nav-main-item{{ request()->routeIs('backend.master.agama*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fa fa-star-and-crescent"></i>
                    <span class="nav-main-link-name">Agama</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.agama.create') ? ' active' : '' }}" href="{{ route('backend.master.agama.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.agama.index') ? ' active' : '' }}" href="{{ route('backend.master.agama.index') }}">
                            <i class="nav-main-link-icon fa fa-list"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-main-item{{ request()->routeIs('backend.master.instansi*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fa fa-globe"></i>
                    <span class="nav-main-link-name">Instansi</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.instansi.create') ? ' active' : '' }}" href="{{ route('backend.master.instansi.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.instansi.index') ? ' active' : '' }}" href="{{ route('backend.master.instansi.index') }}">
                            <i class="nav-main-link-icon fa fa-list"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-main-item{{ request()->routeIs('backend.master.lokasi*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fa fa-location-arrow"></i>
                    <span class="nav-main-link-name">Lokasi</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.lokasi.create') ? ' active' : '' }}" href="{{ route('backend.master.lokasi.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.lokasi.index') ? ' active' : '' }}" href="{{ route('backend.master.lokasi.index') }}">
                            <i class="nav-main-link-icon fa fa-list"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-main-item{{ request()->routeIs('backend.master.opd*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon far fa-building"></i>
                    <span class="nav-main-link-name">OPD / SKPD</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.opd.create') ? ' active' : '' }}" href="{{ route('backend.master.opd.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.opd.index') ? ' active' : '' }}" href="{{ route('backend.master.opd.index') }}">
                            <i class="nav-main-link-icon fa fa-list"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-main-item{{ request()->routeIs('backend.master.pangkat*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fa fa-chevron-up"></i>
                    <span class="nav-main-link-name">Pangkat</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.pangkat.create') ? ' active' : '' }}" href="{{ route('backend.master.pangkat.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.pangkat.index') ? ' active' : '' }}" href="{{ route('backend.master.pangkat.index') }}">
                            <i class="nav-main-link-icon fa fa-list"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-main-item{{ request()->routeIs('backend.master.tahun*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fa fa-calendar"></i>
                    <span class="nav-main-link-name">Tahun</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.tahun.create') ? ' active' : '' }}" href="{{ route('backend.master.tahun.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.master.tahun.index') ? ' active' : '' }}" href="{{ route('backend.master.tahun.index') }}">
                            <i class="nav-main-link-icon fa fa-list"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan
            <li class="nav-main-heading">Pelatihan</li>
            @can('isUser')
            <li class="nav-main-item{{ request()->routeIs('backend.diklat.fasilitator*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fa fa-chalkboard-teacher"></i>
                    <span class="nav-main-link-name">Fasilitator</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.diklat.fasilitator.create') ? ' active' : '' }}" href="{{ route('backend.diklat.fasilitator.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.diklat.fasilitator.index') ? ' active' : '' }}" href="{{ route('backend.diklat.fasilitator.index') }}">
                            <i class="nav-main-link-icon fa fa-list"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-main-item{{ request()->routeIs('backend.diklat.jenis*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fa fa-tags"></i>
                    <span class="nav-main-link-name">Jenis</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.diklat.jenis.create') ? ' active' : '' }}" href="{{ route('backend.diklat.jenis.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.diklat.jenis.index') ? ' active' : '' }}" href="{{ route('backend.diklat.jenis.index') }}">
                            <i class="nav-main-link-icon fa fa-list-alt"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-main-item{{ request()->routeIs('backend.diklat.kurikulum*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fa fa-book"></i>
                    <span class="nav-main-link-name">Kurikulum</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.diklat.kurikulum.create') ? ' active' : '' }}" href="{{ route('backend.diklat.kurikulum.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.diklat.kurikulum.index') ? ' active' : '' }}" href="{{ route('backend.diklat.kurikulum.index') }}">
                            <i class="nav-main-link-icon fa fa-list"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan
            <li class="nav-main-item{{ request()->routeIs('backend.diklat.jadwal*') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon far fa-calendar-alt"></i>
                    <span class="nav-main-link-name">Jadwal</span>
                </a>
                <ul class="nav-main-submenu">
                    @can('isUser')
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.diklat.jadwal.create') ? ' active' : '' }}" href="{{ route('backend.diklat.jadwal.create') }}">
                            <i class="nav-main-link-icon fa fa-plus"></i>
                            <span class="nav-main-link-name">Tambah</span>
                        </a>
                    </li>
                    @endcan
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('backend.diklat.jadwal.index') ? ' active' : '' }}" href="{{ route('backend.diklat.jadwal.index') }}">
                            <i class="nav-main-link-icon fa fa-list"></i>
                            <span class="nav-main-link-name">Data</span>
                        </a>
                    </li>
                </ul>
            </li>
            @can('isAdmin')
            <li class="nav-main-heading">Pengaturan</li>
            <li class="nav-main-item">
                <a class="nav-main-link {{ request()->routeIs('backend.pengaturan*') ? ' active' : '' }}" href="{{ route('backend.pengaturan.index') }}">
                    <i class="nav-main-link-icon fa fa-cog"></i>
                    <span class="nav-main-link-name">Aplikasi</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link {{ request()->routeIs('backend.pengguna*') ? ' active' : '' }}" href="{{ route('backend.pengguna.index') }}">
                    <i class="nav-main-link-icon fa fa-users"></i>
                    <span class="nav-main-link-name">Pengguna</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link {{ request()->routeIs('backend.aktifitas*') ? ' active' : '' }}" href="{{ route('backend.aktifitas') }}">
                    <i class="nav-main-link-icon fa fa-clipboard-list"></i>
                    <span class="nav-main-link-name">Aktifitas</span>
                </a>
            </li>
            @endcan
        </ul>
    </div>
    <!-- END Side Navigation -->
</nav>
