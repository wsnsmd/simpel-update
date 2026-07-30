<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Presensi — {{ $sesi->nama_materi }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            background: #1a1a2e;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: sans-serif;
            color: #fff;
        }

        .qr-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
        }

        .qr-header {
            background: #1d4ed8;
            color: #fff;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .qr-header h2 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .qr-header p {
            margin: .3rem 0 0;
            font-size: .85rem;
            opacity: .85;
        }

        #qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 1.5rem;
        }

        #qr-container canvas,
        #qr-container img {
            border-radius: 8px;
            display: block;
        }

        .qr-info {
            color: #374151;
        }

        .qr-info .waktu {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1d4ed8;
            letter-spacing: .05em;
        }

        .qr-info .label {
            font-size: .8rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .qr-info .instruksi {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: .75rem;
            font-size: .82rem;
            color: #15803d;
            margin-top: 1rem;
        }

        .status-badge {
            display: inline-block;
            padding: .35rem 1rem;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 600;
            margin-top: .75rem;
        }

        .status-aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .status-nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        .countdown {
            font-size: .85rem;
            color: #6b7280;
            margin-top: .5rem;
        }

        #timer {
            font-weight: 700;
            color: #1d4ed8;
        }

        .btn-actions {
            margin-top: 1.5rem;
            display: flex;
            gap: .5rem;
            justify-content: center;
        }

        .btn-regen {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: .4rem 1rem;
            font-size: .82rem;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-regen:hover {
            background: #e5e7eb;
            color: #111827;
            text-decoration: none;
        }

        .btn-close-qr {
            background: #1d4ed8;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .4rem 1rem;
            font-size: .82rem;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-close-qr:hover {
            background: #1e40af;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="qr-card">
        <div class="qr-header">
            <h2>{{ $sesi->nama_materi }}</h2>
            <p>
                {{ $jadwal->nama }}<br>
                @if ($sesi->widyaiswara)
                    <span style="opacity:.7">{{ $sesi->widyaiswara }}</span>
                @endif
            </p>
        </div>

        <div id="qr-container"></div>

        <div class="qr-info">
            <div class="label">Waktu Sesi</div>
            <div class="waktu">
                {{ substr($sesi->jam_mulai, 0, 5) }} – {{ substr($sesi->jam_selesai, 0, 5) }}
            </div>
            <div class="label mt-1">
                {{ \Carbon\Carbon::parse($sesi->tanggal)->format('l, d F Y') }}
                &nbsp;·&nbsp; {{ $sesi->jp }} JP
                &nbsp;·&nbsp; Terlambat &gt; {{ $sesi->batas_terlambat }} menit
            </div>

            @php $isAktif = $sesi->isTokenValid(); @endphp
            <div class="status-badge {{ $isAktif ? 'status-aktif' : 'status-nonaktif' }}">
                <i class="fa fa-circle" style="font-size:.5rem;vertical-align:middle;margin-right:4px"></i>
                {{ $isAktif ? 'QR Aktif — Scan Sekarang' : 'QR Tidak Aktif' }}
            </div>

            @if ($isAktif)
                <div class="countdown">
                    Sesi berakhir dalam <span id="timer">...</span>
                </div>
            @endif

            <div class="instruksi">
                <strong>Cara presensi:</strong><br>
                1. Scan QR code di atas dengan kamera HP<br>
                2. Login menggunakan akun SSO BPSDM<br>
                3. Klik tombol <strong>Konfirmasi Presensi</strong>
            </div>
        </div>

        <div class="btn-actions">
            <form action="{{ route('backend.diklat.presensi.regenerate', $sesi->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-regen">
                    <i class="fa fa-sync-alt mr-1"></i> Refresh Token
                </button>
            </form>
            <a href="{{ route('backend.diklat.presensi.index', $sesi->diklat_jadwal_id) }}" class="btn-close-qr">
                <i class="fa fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Library QR Code --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        var presensiUrl = '{{ url("/presensi/" . $sesi->token) }}';
        var jamSelesai = '{{ $sesi->tanggal->format("Y-m-d") }} {{ substr($sesi->jam_selesai, 0, 5) }}:00';

        new QRCode(document.getElementById('qr-container'), {
            text: presensiUrl,
            width: 240,
            height: 240,
            colorDark: '#1a1a2e',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });

        // Countdown timer
        function updateTimer() {
            var now = new Date();
            var selesai = new Date(jamSelesai);
            var diff = selesai - now;
            if (diff <= 0) {
                document.getElementById('timer') && (document.getElementById('timer').textContent = '00:00');
                return;
            }
            var totalMnt = Math.floor(diff / 60000);
            var mnt = totalMnt % 60;
            var jam = Math.floor(totalMnt / 60);
            var dtk = Math.floor((diff % 60000) / 1000);
            var el = document.getElementById('timer');
            if (el) el.textContent =
                (jam > 0 ? jam + 'j ' : '') +
                (mnt < 10 ? '0' : '') + mnt + ':' +
                (dtk < 10 ? '0' : '') + dtk;
        }
        updateTimer();
        setInterval(updateTimer, 1000);
    </script>
</body>

</html>