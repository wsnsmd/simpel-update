@extends('layouts.simple')

@section('css_before')
    {{-- Font Awesome sudah di-load dashmix, tambahkan title khusus lewat JS --}}
@endsection

@section('content')
    @php $isAktif = $sesi->isTokenValid(); @endphp

    {{-- Fullscreen centered container menggunakan hero Dashmix --}}
    <div class="hero bg-gd-primary">
        <div class="hero-inner w-100 py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-10 col-md-7 col-lg-5">

                        {{-- ── Kartu QR ── --}}
                        <div class="block block-rounded block-fx-pop mb-0">

                            {{-- Header kartu --}}
                            <div class="block-header bg-gd-primary p-4 rounded-top">
                                <div class="w-100 text-center">
                                    <h2 class="font-w700 text-white font-size-h4 mb-1">
                                        {{ $sesi->nama_materi }}
                                    </h2>
                                    <p class="text-white-75 font-size-sm mb-0">
                                        {{ $jadwal->nama }}
                                    </p>
                                    @if ($sesi->widyaiswara)
                                        <p class="text-white-50 font-size-sm mb-0">
                                            <i class="fa fa-chalkboard-teacher mr-1"></i>{{ $sesi->widyaiswara }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="block-content text-center py-4">

                                {{-- QR Code container --}}
                                <div id="qr-container" class="d-inline-block p-3 rounded bg-white mb-3"
                                    style="box-shadow:0 2px 12px rgba(0,0,0,.1)"></div>

                                {{-- Info waktu sesi --}}
                                <div class="font-w700 text-primary mb-1" style="font-size:1.5rem;letter-spacing:.04em">
                                    {{ substr($sesi->jam_mulai, 0, 5) }} – {{ substr($sesi->jam_selesai, 0, 5) }}
                                </div>
                                <div class="text-muted font-size-sm mb-3">
                                    {{ \Carbon\Carbon::parse($sesi->tanggal)->format('l, d F Y') }}
                                    <span class="mx-1">&middot;</span>
                                    <span class="badge badge-secondary">{{ $sesi->jp }} JP</span>
                                    <span class="mx-1">&middot;</span>
                                    Terlambat &gt; {{ $sesi->batas_terlambat }} mnt
                                </div>

                                {{-- Status badge --}}
                                @if ($isAktif)
                                    <div class="mb-1">
                                        <span class="badge badge-success px-3 py-2" style="font-size:.8rem">
                                            <i class="fa fa-circle mr-1" style="font-size:.45rem;vertical-align:middle"></i>
                                            QR Aktif — Scan Sekarang
                                        </span>
                                    </div>
                                    <div class="text-muted font-size-sm mb-3">
                                        Sesi berakhir dalam
                                        <span id="timer" class="font-w700 text-primary">...</span>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <span class="badge badge-danger px-3 py-2" style="font-size:.8rem">
                                            <i class="fa fa-times-circle mr-1"></i>QR Tidak Aktif
                                        </span>
                                    </div>
                                @endif

                                {{-- Instruksi --}}
                                <div class="block block-rounded bg-success-lighter border border-success mb-3 text-left">
                                    <div class="block-content py-3">
                                        <p class="font-w700 font-size-sm text-success mb-2">
                                            <i class="fa fa-info-circle mr-1"></i>Cara presensi:
                                        </p>
                                        <ol class="pl-3 mb-0 text-muted font-size-sm" style="line-height:1.9">
                                            <li>Scan QR code di atas dengan kamera HP</li>
                                            <li>Login menggunakan akun SSO BPSDM</li>
                                            <li>Klik tombol <strong>Konfirmasi Presensi</strong></li>
                                        </ol>
                                    </div>
                                </div>

                                {{-- Tombol aksi --}}
                                <div class="d-flex justify-content-center" style="gap:.5rem">
                                    <form action="{{ route('backend.diklat.presensi.regenerate', $sesi->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                                            <i class="fa fa-sync-alt mr-1"></i>Refresh Token
                                        </button>
                                    </form>
                                    <a href="{{ route('backend.diklat.presensi.index', $sesi->diklat_jadwal_id) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fa fa-arrow-left mr-1"></i>Kembali
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js_after')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.title = 'QR Presensi — {{ addslashes($sesi->nama_materi) }}';

        new QRCode(document.getElementById('qr-container'), {
            text: '{{ url("/presensi/" . $sesi->token) }}',
            width: 220,
            height: 220,
            colorDark: '#1a1a2e',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });

        @if ($isAktif)
            (function () {
                var jamSelesai = new Date('{{ $sesi->tanggal->format("Y-m-d") }} {{ substr($sesi->jam_selesai, 0, 5) }}:00');

                function updateTimer() {
                    var diff = jamSelesai - new Date();
                    var el = document.getElementById('timer');
                    if (!el) return;
                    if (diff <= 0) { el.textContent = '00:00'; return; }
                    var jam = Math.floor(diff / 3600000);
                    var mnt = Math.floor((diff % 3600000) / 60000);
                    var dtk = Math.floor((diff % 60000) / 1000);
                    el.textContent =
                        (jam > 0 ? jam + 'j ' : '') +
                        (mnt < 10 ? '0' : '') + mnt + ':' +
                        (dtk < 10 ? '0' : '') + dtk;
                }
                updateTimer();
                setInterval(updateTimer, 1000);
            })();
        @endif
    </script>
@endsection