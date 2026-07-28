{{--
    Partial: _foto_upload_handler.blade.php
    Diinclude di semua varian step-2: 2group1, 2group1s, 2group2, 2group2s, 2group3, 2group3s
    Letakkan @include di dalam @section('js_sub') atau sebelum @endsection di bawah form.
--}}
<script>
(function () {
    var MAX_SIZE_KB  = 512;
    var MAX_SIZE_B   = MAX_SIZE_KB * 1024;
    var ACCEPT_TYPES = ['image/jpeg', 'image/png', 'image/gif'];

    var fotoInput    = document.getElementById('foto');
    var submitBtn    = document.querySelector('button[type="submit"]');
    var form         = document.querySelector('form[enctype="multipart/form-data"]');

    // =========================================================================
    // 1. Validasi foto di sisi client sebelum submit
    // =========================================================================
    if (fotoInput) {
        fotoInput.addEventListener('change', function () {
            var file = this.files[0];

            // Hapus pesan error lama
            var oldError = document.getElementById('foto-client-error');
            if (oldError) oldError.parentNode.removeChild(oldError);

            if (!file) return;

            var errorMsg = null;

            // Cek tipe file
            if (ACCEPT_TYPES.indexOf(file.type) === -1) {
                errorMsg = 'Format foto tidak valid. Gunakan file JPG, PNG, atau GIF.';
            }

            // Cek ukuran
            if (!errorMsg && file.size > MAX_SIZE_B) {
                var ukuranKB = Math.round(file.size / 1024);
                errorMsg = 'Ukuran foto terlalu besar (' + ukuranKB + ' KB). Maksimal ' + MAX_SIZE_KB + ' KB.';
            }

            if (errorMsg) {
                // Tampilkan error di bawah input foto
                var div = document.createElement('div');
                div.id        = 'foto-client-error';
                div.className = 'invalid-feedback d-block mt-1';
                div.innerHTML = '<i class="fa fa-exclamation-triangle mr-1"></i>' + errorMsg;

                // Insert setelah elemen fileinput container
                var container = fotoInput.closest('.fileinput') || fotoInput.parentNode;
                container.parentNode.insertBefore(div, container.nextSibling);

                // Highlight border input
                fotoInput.closest('.fileinput').style.border = '1px solid #dc3545';
                fotoInput.closest('.fileinput').style.borderRadius = '4px';
                fotoInput.closest('.fileinput').style.padding = '4px';

                // Reset input supaya file tidak ikut terkirim
                fotoInput.value = '';

                // Trigger jasny fileinput reset jika ada
                if (typeof $ !== 'undefined' && $(fotoInput).closest('.fileinput').length) {
                    $(fotoInput).closest('.fileinput').fileinput('clear');
                }
            } else {
                // Hapus highlight error jika file valid
                var container = fotoInput.closest('.fileinput');
                if (container) {
                    container.style.border = '';
                    container.style.borderRadius = '';
                    container.style.padding = '';
                }
            }
        });
    }

    // =========================================================================
    // 2. Loading indicator saat form disubmit
    // =========================================================================
    if (form && submitBtn) {
        form.addEventListener('submit', function (e) {
            // Jangan proses jika masih ada error foto client-side
            var clientError = document.getElementById('foto-client-error');
            if (clientError) {
                e.preventDefault();
                clientError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            // Jika ada file foto yang akan diupload, tampilkan progress info
            var file = fotoInput ? fotoInput.files[0] : null;

            // Ubah tombol submit
            submitBtn.disabled  = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Memproses...';

            // Tampilkan banner info di atas form
            var banner = document.getElementById('upload-progress-banner');
            if (!banner) {
                banner = document.createElement('div');
                banner.id        = 'upload-progress-banner';
                banner.className = 'alert alert-info';
                banner.style.marginBottom = '1rem';

                var pesanUpload = file
                    ? '<i class="fa fa-cloud-upload-alt mr-2"></i>Mengunggah foto dan menyimpan data, mohon tunggu...'
                    : '<i class="fa fa-spinner fa-spin mr-2"></i>Menyimpan data, mohon tunggu...';

                banner.innerHTML = pesanUpload;

                var blockContent = form.querySelector('.block-content');
                if (blockContent) {
                    form.insertBefore(banner, blockContent);
                } else {
                    form.insertBefore(banner, form.firstChild);
                }
            }

            // Scroll ke atas agar banner terlihat
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Safety: aktifkan kembali tombol setelah 30 detik
            // (antisipasi jika ada error jaringan dan halaman tidak reload)
            setTimeout(function () {
                submitBtn.disabled  = false;
                submitBtn.innerHTML = 'Lanjut <i class="fa fa-angle-right ml-1"></i>';
                var b = document.getElementById('upload-progress-banner');
                if (b) {
                    b.className = 'alert alert-warning';
                    b.innerHTML = '<i class="fa fa-exclamation-triangle mr-2"></i>'
                        + 'Proses memakan waktu lebih lama dari biasanya. '
                        + 'Silakan coba klik Lanjut kembali, atau periksa koneksi internet Anda.';
                }
            }, 30000);
        });
    }

    // =========================================================================
    // 3. Scroll otomatis ke pesan error Laravel jika ada
    // =========================================================================
    var alertDanger = document.querySelector('.alert-danger');
    if (alertDanger) {
        setTimeout(function () {
            alertDanger.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 200);
    }

})();
</script>
