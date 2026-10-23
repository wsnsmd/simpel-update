@extends('frontend.daftar._index')

@section('content-block')
    <div class="block-header block-header-default">
        <h3 class="block-title">
            <i class="fa fa-check-circle text-success mr-2"></i> Pendaftaran Berhasil
        </h3>
    </div>

    <div class="block-content block-content-full">

        {{-- ── Notifikasi sukses ── --}}
        <div class="alert alert-success border-0 rounded mb-3 py-3">
            <div class="d-flex align-items-center" style="gap:.75rem">
                <i class="fa fa-check-circle fa-2x"></i>
                <div>
                    <p class="font-w700 mb-0">Selamat, {{ $peserta->nama_lengkap }}!</p>
                    <p class="mb-0 font-size-sm">
                        Pendaftaran Anda pada <strong>{{ $jadwal->nama }}</strong> telah diterima.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Peringatan konfirmasi ── --}}
        <div class="alert border-warning mb-3 py-3"
            style="background:#fffbeb;border:2px solid #f59e0b !important;border-radius:10px">
            <div class="d-flex align-items-start" style="gap:.75rem">
                <i class="fa fa-exclamation-triangle fa-lg mt-1" style="color:#d97706;flex-shrink:0"></i>
                <div>
                    <p class="font-w700 mb-1" style="color:#92400e">Langkah penting — jangan dilewati</p>
                    <p class="mb-0 font-size-sm" style="color:#92400e">
                        Klik tombol konfirmasi di bawah <strong>sekarang</strong>.
                        Tanpa konfirmasi, kehadiran Anda tidak tercatat dan sertifikat tidak dapat diterbitkan.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── TOMBOL UTAMA ── --}}
        <a href="{{ $url }}" class="btn btn-success btn-lg btn-block mb-2 py-3"
            style="font-size:1.05rem;border-radius:10px;letter-spacing:.01em">
            <i class="fa fa-hand-pointer mr-2"></i>
            Konfirmasi Kehadiran Saya
        </a>
        <p class="text-center text-muted font-size-sm mb-4">
            <i class="fa fa-info-circle mr-1"></i>
            Atau scan QR code / salin link di bawah jika tidak bisa klik
        </p>

        {{-- ── QR code + link salin (alternatif) ── --}}
        <div class="block block-rounded border mb-3">
            <div class="block-content py-3">
                <div class="d-flex align-items-start" style="gap:1rem">

                    {{-- QR --}}
                    <div class="text-center flex-shrink-0">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($url) }}"
                            alt="QR Code Konfirmasi" class="img-thumbnail p-1" style="width:100px;height:100px">
                        <p class="text-muted mb-0 mt-1" style="font-size:.7rem">Scan QR</p>
                    </div>

                    {{-- Link salin --}}
                    <div class="flex-grow-1" style="min-width:0">
                        <p class="font-w600 font-size-sm mb-1">Link konfirmasi kehadiran</p>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" class="form-control form-control-sm" id="linkKonfirmasi" value="{{ $url }}"
                                readonly style="font-size:.72rem">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard()"
                                    id="btnSalin">
                                    <i class="fa fa-copy mr-1"></i> Salin
                                </button>
                            </div>
                        </div>
                        <p class="text-muted mb-0" style="font-size:.72rem">
                            <i class="fa fa-camera mr-1"></i>
                            Screenshot halaman ini sebagai bukti pendaftaran Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Grup Telegram/WA (jika ada) ── --}}
        @if (!empty($jadwal->var_1))
            <div class="text-center border-top pt-3 mb-3">
                <p class="font-size-sm text-muted mb-2">
                    Bergabunglah dengan grup koordinasi untuk informasi teknis terbaru:
                </p>
                <a href="{{ $jadwal->var_1 }}" class="btn btn-sm btn-info px-4 rounded-pill" target="_blank">
                    <i class="fab fa-telegram-plane mr-1"></i> Gabung Grup Informasi
                </a>
            </div>
        @endif

        <div class="text-center border-top pt-3">
            <p class="text-muted font-size-sm mb-0">Terima Kasih,</p>
            <p class="font-w600 font-size-sm mb-0">BPSDM Provinsi Kalimantan Timur</p>
        </div>

    </div>

    <script>
        function copyToClipboard() {
            var input = document.getElementById('linkKonfirmasi');
            var btn = document.getElementById('btnSalin');

            input.select();
            input.setSelectionRange(0, 99999);

            try {
                navigator.clipboard.writeText(input.value).then(function () {
                    showCopied(btn);
                });
            } catch (e) {
                document.execCommand('copy');
                showCopied(btn);
            }
        }

        function showCopied(btn) {
            btn.innerHTML = '<i class="fa fa-check mr-1"></i> Disalin!';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success');
            setTimeout(function () {
                btn.innerHTML = '<i class="fa fa-copy mr-1"></i> Salin';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 2500);
        }
    </script>
@endsection