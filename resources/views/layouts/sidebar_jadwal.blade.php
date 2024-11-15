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
                <a class="nav-main-link{{ request()->is('dashboard') ? ' active' : '' }}" href="{{ route('backend.diklat.jadwal.index') }}">
                    <i class="nav-main-link-icon fa fa-chevron-circle-left"></i>
                    <span class="nav-main-link-name">Kembali</span>
                    {{-- <span class="nav-main-link-badge badge badge-pill badge-success">10</span> --}}
                </a>
            </li>
            <li class="nav-main-heading">Jadwal Detail</li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]) }}">
                    <i class="nav-main-link-icon fa fa-align-justify"></i>
                    <span class="nav-main-link-name">Detail</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'peserta']) }}">
                    <i class="nav-main-link-icon fa fa-users"></i>
                    <span class="nav-main-link-name">Peserta</span>
                </a>
            </li>
            @can('isPKMF')
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'mata-pelatihan']) }}">
                    <i class="nav-main-link-icon fa fa-book"></i>
                    <span class="nav-main-link-name">Mata Pelatihan</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'seminar']) }}">
                    <i class="nav-main-link-icon fa fa-book-open"></i>
                    <span class="nav-main-link-name">Seminar</span>
                </a>
            </li>
            @endcan
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'surat-tugas']) }}">
                    <i class="nav-main-link-icon fa fa-file-alt"></i>
                    <span class="nav-main-link-name">Surat Tugas</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'sertifikat']) }}">
                    <i class="nav-main-link-icon fa fa-certificate"></i>
                    <span class="nav-main-link-name">Lola SiKembangKol</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'cetak']) }}">
                    <i class="nav-main-link-icon fa fa-print"></i>
                    <span class="nav-main-link-name">Cetak</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'page' => 'checklist']) }}">
                    <i class="nav-main-link-icon fa fa-check"></i>
                    <span class="nav-main-link-name">PAKAR</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- END Side Navigation -->
</nav>
