@extends('frontend.daftar._index')

@section('content-block')
    <div class="block-header block-header-default">
        <h3 class="block-title">
            <i class="fa fa-check-circle text-success mr-2"></i> Pendaftaran Berhasil
        </h3>
    </div>

    <div class="block-content block-content-full">
        <div class="text-center py-3">
            <p class="font-size-base font-w700 mb-0">Selamat!</p>
            <p class="font-size-sm text-muted">Pendaftaran Anda pada kegiatan <strong>{{ $jadwal->nama }}</strong> telah
                berhasil diterima.</p>
        </div>

        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="d-flex">
                <i class="fa fa-exclamation-triangle fa-2x mr-3"></i>
                <div>
                    <p class="font-w700 font-size-sm mb-1">PENTING: Jangan Tutup/Refresh Halaman Ini!</p>
                    <p class="font-size-sm mb-0">
                        Kami tidak mengirimkan email konfirmasi guna menjaga efisiensi sistem.
                        Silakan <strong>screenshot</strong> halaman ini atau <strong>salin link</strong> di bawah sebagai
                        konfirmasi kehadiran anda.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-body-light p-3 rounded text-center border">
            <p class="font-w600 font-size-sm text-uppercase text-muted mb-2">Akses Konfirmasi Kehadiran</p>

            <div class="mb-3">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($url) }}"
                    alt="QR Code Konfirmasi" class="img-thumbnail shadow-sm" style="width: 120px;">
                <p class="font-size-sm text-muted mt-2 mb-0 italic">*Scan QR di atas untuk akses cepat</p>
            </div>

            <div class="input-group mb-3">
                <input type="text" class="form-control form-control-sm text-center font-w600" id="linkKonfirmasi"
                    value="{{ $url }}" readonly>
                <div class="input-group-append">
                    <button type="button" class="btn btn-sm btn-primary" onclick="copyToClipboard()">
                        <i class="fa fa-copy mr-1"></i> Salin
                    </button>
                </div>
            </div>

            <div class="alert alert-info border-0 rounded-0 py-2 mb-3">
                <p class="font-size-sm mb-0">
                    <i class="fa fa-info-circle mr-1"></i>
                    <strong>Catatan:</strong> Konfirmasi kehadiran ini hanya dapat digunakan saat <strong>sesi kehadiran
                        telah dibuka oleh Panitia</strong> sesuai jadwal kegiatan dan sebagai salah satu syarat penerbitan
                    sertifikat.
                </p>
            </div>

            <div class="">
                <a href="{{ $url }}" class="btn btn-sm btn-alt-success px-4 shadow-sm rounded-pill">
                    <i class="fa fa-hand-pointer mr-1"></i> Konfirmasi Kehadiran
                </a>
            </div>
        </div>

        @if(!empty($jadwal->var_1))
            <div class="text-center font-size-sm mt-4 border-top pt-3">
                <p class="mb-2">
                    Bergabunglah dengan grup koordinasi untuk informasi teknis terbaru:
                </p>
                <a href="{{ $jadwal->var_1 }}" class="btn btn-sm btn-info px-4 py-2" target="_blank">
                    <i class="fab fa-telegram-plane mr-2"></i> Gabung Grup Informasi
                </a>
            </div>
        @endif

        <div class="text-center mt-4 border-top pt-3">
            <p class="text-muted font-size-sm mb-0">Terima Kasih,</p>
            <p class="font-w600 font-size-sm">BPSDM Provinsi Kalimantan Timur</p>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            var copyText = document.getElementById("linkKonfirmasi");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);

            if (typeof jQuery.notify === 'function') {
                jQuery.notify({
                    icon: 'fa fa-check mr-1',
                    message: 'Link berhasil disalin ke clipboard!'
                }, {
                    type: 'success',
                    placement: { from: 'top', align: 'center' }
                });
            } else {
                alert("Link berhasil disalin!");
            }
        }
    </script>
@endsection