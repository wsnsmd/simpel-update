<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>{!! $jadwal->nama !!} | {{ setting()->get('app_nama', 'SIMPel') }} BPSDM Kaltim</title>

    <meta name="description" content="Tautan Informasi dan Registrasi Pelatihan {{ $jadwal->nama }} - BPSDM Provinsi Kalimantan Timur">
    <meta name="keywords" content="Pelatihan, SIMPel, BPSDM, Kaltim, Linktree, Informasi, Jadwal">
    <meta name="author" content="BPSDM Prov. Kaltim">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('media/favicons/favicon-32x32.png') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/android-icon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-icon-precomposed.png') }}">

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --accent: #0ea5e9;
            --emerald: #10b981;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-600: #475569;
            --slate-100: #f1f5f9;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #0b132b;
            background-image: 
                radial-gradient(at 0% 0%, rgba(14, 165, 233, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.15) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(2, 132, 199, 0.1) 0px, transparent 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px 12px;
            color: #ffffff;
        }

        .container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            position: relative;
        }

        /* Glass Card Responsive Container */
        .glass-card {
            background: rgba(30, 41, 59, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 24px 18px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0ea5e9, #38bdf8, #10b981);
        }

        /* Brand Logo */
        .brand-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 10px;
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.3);
            margin-bottom: 16px;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-decoration: none;
        }

        .brand-wrapper:hover {
            transform: scale(1.05) rotate(2deg);
        }

        .brand-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Category Badge */
        .badge-category {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #38bdf8;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 5px 12px;
            border-radius: 9999px;
            margin-bottom: 12px;
        }

        .badge-category .dot {
            width: 6px;
            height: 6px;
            background-color: #38bdf8;
            border-radius: 50%;
            box-shadow: 0 0 8px #38bdf8;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }

        /* Event Title */
        .event-title {
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.45;
            color: #ffffff;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
            word-break: break-word;
        }

        /* Metadata Responsive Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 16px;
            text-align: left;
        }

        .meta-item {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .meta-full {
            grid-column: span 2;
        }

        .meta-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(14, 165, 233, 0.12);
            color: #38bdf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .meta-text {
            min-width: 0;
            flex: 1;
        }

        .meta-text label {
            display: block;
            font-size: 0.62rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #94a3b8;
            margin-bottom: 1px;
            white-space: nowrap;
        }

        .meta-text span {
            font-size: 0.78rem;
            font-weight: 700;
            color: #f8fafc;
            line-height: 1.25;
            display: block;
            word-break: break-word;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent);
            margin: 16px 0 20px 0;
        }

        /* Action Links / Buttons */
        .links-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .link-card {
            position: relative;
            display: flex;
            align-items: center;
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            text-decoration: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(8px);
            overflow: hidden;
        }

        .link-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0));
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .link-card:hover, .link-card:active {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(56, 189, 248, 0.4);
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.3), 0 0 12px rgba(14, 165, 233, 0.2);
            color: #ffffff;
        }

        .link-card:hover::before {
            transform: translateX(100%);
        }

        /* Highlight Button (Primary Action) */
        .link-primary {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: 1px solid rgba(56, 189, 248, 0.4);
            box-shadow: 0 8px 20px -4px rgba(2, 132, 199, 0.4);
        }

        .link-primary:hover, .link-primary:active {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
            box-shadow: 0 12px 24px -4px rgba(2, 132, 199, 0.6), 0 0 16px rgba(56, 189, 248, 0.4);
        }

        /* WhatsApp / Success Variant */
        .link-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .link-success:hover, .link-success:active {
            border-color: #34d399;
            box-shadow: 0 8px 16px -4px rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .link-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            margin-right: 12px;
            background: rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }

        .link-primary .link-icon-box {
            background: rgba(255, 255, 255, 0.2);
        }

        .link-card:hover .link-icon-box {
            transform: scale(1.08);
        }

        .link-title {
            flex-grow: 1;
            text-align: left;
            min-width: 0;
        }

        .link-title div {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .link-arrow {
            font-size: 0.8rem;
            opacity: 0.5;
            transition: all 0.25s ease;
            margin-left: 8px;
            flex-shrink: 0;
        }

        .link-card:hover .link-arrow {
            opacity: 1;
            transform: translateX(3px);
        }

        /* Panitia Contact Section */
        .contact-box {
            margin-top: 20px;
            background: rgba(15, 23, 42, 0.4);
            border: 1px dashed rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .contact-info {
            text-align: left;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .contact-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .contact-details {
            min-width: 0;
        }

        .contact-details h4 {
            font-size: 0.82rem;
            font-weight: 700;
            color: #f1f5f9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .contact-details p {
            font-size: 0.68rem;
            color: #94a3b8;
        }

        .contact-action-btn {
            background: #25d366;
            color: #ffffff;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 0.95rem;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.35);
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }

        .contact-action-btn:hover {
            transform: scale(1.1);
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.7rem;
            color: #64748b;
        }

        .footer a {
            color: #94a3b8;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: #38bdf8;
        }

        /* Quick Share Button */
        .share-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.8rem;
        }

        .share-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            transform: scale(1.05);
        }

        .toast-copy {
            display: none;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #10b981;
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 9999px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            z-index: 999;
            white-space: nowrap;
        }

        /* Specific Mobile Tweaks (Small Screens <= 375px) */
        @media (max-width: 375px) {
            body {
                padding: 12px 8px;
            }

            .glass-card {
                padding: 20px 14px;
                border-radius: 20px;
            }

            .event-title {
                font-size: 0.98rem;
            }

            .meta-grid {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .meta-full {
                grid-column: span 1;
            }

            .link-card {
                padding: 10px 12px;
                font-size: 0.85rem;
            }

            .link-icon-box {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
                margin-right: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="glass-card">
            <!-- Share Icon -->
            <button class="share-btn" onclick="copyLink()" title="Salin Tautan">
                <i class="fa-solid fa-share-nodes"></i>
            </button>

            <!-- Brand Logo -->
            <a class="brand-wrapper" href="{{ route('index') }}">
                <img src="{{ asset('media/images/bpsdm-logo.png') }}" alt="BPSDM Kaltim Logo">
            </a>

            <!-- Category & Type Tag -->
            <div>
                <span class="badge-category">
                    <span class="dot"></span>
                    {{ $jadwal->tipe ?? 'Pelatihan' }} · {{ $jadwal->jenis ?? 'BPSDM' }}
                </span>
            </div>

            <!-- Title -->
            <h1 class="event-title">
                {!! $jadwal->nama !!}
            </h1>

            <!-- Meta Information Pills -->
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-icon">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <div class="meta-text">
                        <label>Pelaksanaan</label>
                        <span>
                            @if(!empty($jadwal->tgl_awal) && !empty($jadwal->tgl_akhir))
                                @if($jadwal->tgl_awal === $jadwal->tgl_akhir)
                                    {{ formatTanggal($jadwal->tgl_awal) }}
                                @else
                                    {{ formatTgl2($jadwal->tgl_awal) }} - {{ formatTanggal($jadwal->tgl_akhir) }}
                                @endif
                            @else
                                {{ $jadwal->tahun ?? date('Y') }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="meta-text">
                        <label>Bobot JP & Kuota</label>
                        <span>{{ !empty($jadwal->total_jp) ? $jadwal->total_jp . ' JP' : ($jadwal->jum_hari ?? '1') . ' Hari' }} ({{ !empty($jadwal->kuota) ? $jadwal->kuota . ' Peserta' : 'Terbuka' }})</span>
                    </div>
                </div>
                <div class="meta-item meta-full">
                    <div class="meta-icon">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div class="meta-text">
                        <label>Sasaran Peserta</label>
                        <span>{{ $jadwal->kelas ?? 'Terbuka untuk ASN dan Masyarakat Umum' }}</span>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Dynamic Link Hub -->
            <div class="links-group">
                @if($tautan->isNotEmpty())
                    @foreach ($tautan as $t)
                        @php
                            $urlLower = strtolower($t->url);
                            $titleLower = strtolower($t->title);
                            $isWa = str_contains($urlLower, 'wa.me') || str_contains($urlLower, 'chat.whatsapp.com');
                            $isDrive = str_contains($urlLower, 'drive.google.com') || str_contains($titleLower, 'materi');
                            $isForm = str_contains($urlLower, 'forms') || str_contains($titleLower, 'absen') || str_contains($titleLower, 'hadir');
                        @endphp

                        @if($t->url === '{daftar}')
                            <form action="{{ route('jadwal.daftar') }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                <button type="submit" class="link-card link-primary w-100" style="border: none; cursor: pointer; width: 100%;">
                                    <div class="link-icon-box">
                                        <i class="fa-solid fa-user-plus"></i>
                                    </div>
                                    <div class="link-title">
                                        <div>{{ $t->title }}</div>
                                        <small style="font-size: 0.7rem; opacity: 0.85; font-weight: 400;">Registrasi pendaftaran peserta</small>
                                    </div>
                                    <div class="link-arrow">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </div>
                                </button>
                            </form>
                        @else
                            <a href="{!! $t->url !!}" class="link-card {{ $isWa ? 'link-success' : '' }}" target="_blank">
                                <div class="link-icon-box" @if($isWa) style="background: rgba(16, 185, 129, 0.2);" @endif>
                                    @if($isWa)
                                        <i class="fa-brands fa-whatsapp"></i>
                                    @elseif($isDrive)
                                        <i class="fa-solid fa-folder-open"></i>
                                    @elseif($isForm)
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    @else
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    @endif
                                </div>
                                <div class="link-title">
                                    <div @if($isWa) style="color: #ffffff;" @endif>{{ $t->title }}</div>
                                    <small style="font-size: 0.7rem; color: {{ $isWa ? '#6ee7b7' : '#94a3b8' }}; font-weight: 400;">
                                        @if($isWa)
                                            Grup koordinasi WhatsApp
                                        @elseif($isDrive)
                                            Unduh berkas materi / paparan
                                        @elseif($isForm)
                                            Formulir presensi / administrasi
                                        @else
                                            Buka tautan eksternal
                                        @endif
                                    </small>
                                </div>
                                <div class="link-arrow">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </div>
                            </a>
                        @endif
                    @endforeach
                @else
                    {{-- Default bila belum ada tautan custom --}}
                    @if($jadwal->registrasi)
                        <form action="{{ route('jadwal.daftar') }}" method="POST" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                            <button type="submit" class="link-card link-primary w-100" style="border: none; cursor: pointer; width: 100%;">
                                <div class="link-icon-box">
                                    <i class="fa-solid fa-user-plus"></i>
                                </div>
                                <div class="link-title">
                                    <div>Daftar Sekarang</div>
                                    <small style="font-size: 0.7rem; opacity: 0.85; font-weight: 400;">Registrasi pendaftaran peserta</small>
                                </div>
                                <div class="link-arrow">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </div>
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]) }}" class="link-card" target="_blank">
                        <div class="link-icon-box">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div class="link-title">
                            <div>Informasi Lengkap & Kurikulum</div>
                            <small style="font-size: 0.7rem; color: #94a3b8; font-weight: 400;">Cek detail kurikulum & deskripsi kegiatan</small>
                        </div>
                        <div class="link-arrow">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </div>
                    </a>
                @endif
            </div>

            <!-- Contact Section (jika ada narahubung panitia) -->
            @if(!empty($jadwal->panitia_telp) || !empty($jadwal->panitia_nama))
                @php
                    $waClean = preg_replace('/[^0-9]/', '', $jadwal->panitia_telp);
                    if (str_starts_with($waClean, '08')) {
                        $waClean = '62' . substr($waClean, 1);
                    }
                @endphp
                <div class="contact-box">
                    <div class="contact-info">
                        <div class="contact-avatar">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div class="contact-details">
                            <h4>{{ $jadwal->panitia_nama ?? 'Panitia Pelaksana' }}</h4>
                            <p>Narahubung / PIC Kegiatan</p>
                        </div>
                    </div>
                    @if(!empty($waClean))
                        <a href="https://wa.me/{{ $waClean }}?text=Halo%20Panitia%20{{ rawurlencode($jadwal->nama) }}" 
                           class="contact-action-btn" target="_blank" title="Hubungi via WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                    @endif
                </div>
            @endif

            <!-- Footer -->
            <div class="footer">
                <p>© {{ date('Y') }} <a href="{{ route('index') }}">BPSDM Provinsi Kalimantan Timur</a></p>
                <p style="margin-top: 4px; opacity: 0.7;">SIMPel · Smart Training Management</p>
            </div>
        </div>
    </div>

    <div id="toast" class="toast-copy">Tautan berhasil disalin!</div>

    <script>
        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const toast = document.getElementById('toast');
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 2500);
            }).catch(err => {
                console.error('Gagal menyalin:', err);
            });
        }
    </script>
</body>

</html>
