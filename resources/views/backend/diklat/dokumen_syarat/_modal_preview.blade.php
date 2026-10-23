{{--
    Partial: _modal_preview.blade.php
    Preview dokumen: PDF/gambar dibuka langsung via window.open() di tab baru
    sebagai fallback paling reliable, dengan modal sebagai wrapper info.
--}}

<div class="modal fade" id="modalPreviewDokumen" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <div>
                    <h5 class="modal-title font-w700 mb-0">
                        <i class="fa fa-file mr-2 text-primary"></i>
                        <span id="previewNamaDokumen">—</span>
                    </h5>
                    <small class="text-muted" id="previewInfoPeserta"></small>
                </div>
                <div class="d-flex align-items-center" style="gap:.5rem">
                    <a id="previewDownloadBtn" href="#" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-download mr-1"></i> Download
                    </a>
                    <button type="button" class="close ml-2" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body p-0" style="height:80vh;background:#525659;position:relative">

                {{-- Loading --}}
                <div id="previewLoading"
                     style="position:absolute;top:0;left:0;right:0;bottom:0;
                            display:flex;align-items:center;justify-content:center;
                            background:#525659;z-index:10">
                    <div class="text-center text-white">
                        <i class="fa fa-spinner fa-spin fa-3x mb-3"></i>
                        <p class="mb-0">Memuat dokumen...</p>
                    </div>
                </div>

                {{-- Error --}}
                <div id="previewError"
                     style="display:none;position:absolute;top:0;left:0;right:0;bottom:0;
                            align-items:center;justify-content:center;
                            background:#f4f6f8;z-index:9">
                    <div class="text-center text-muted p-4">
                        <i class="fa fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                        <p id="previewErrorMsg" class="mb-3">Gagal memuat dokumen.</p>
                        <a id="previewOpenNewTab" href="#" target="_blank"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-external-link-alt mr-1"></i> Buka di Tab Baru
                        </a>
                    </div>
                </div>

                {{-- PDF / Gambar via iframe --}}
                <iframe id="previewFrame"
                        src="about:blank"
                        style="width:100%;height:100%;border:none;display:none">
                </iframe>

                {{-- DOC fallback --}}
                <div id="previewDocFallback"
                     style="display:none;position:absolute;top:0;left:0;right:0;bottom:0;
                            align-items:center;justify-content:center;background:#f4f6f8">
                    <div class="text-center p-4">
                        <i class="fa fa-file-word fa-4x mb-3" style="color:#2b5797"></i>
                        <p class="text-muted mb-3">File Word tidak dapat ditampilkan secara inline.</p>
                        <a id="previewDocDownload" href="#" target="_blank" class="btn btn-primary">
                            <i class="fa fa-download mr-1"></i> Download File
                        </a>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-2 justify-content-between">
                <small class="text-muted" id="previewUploadedAt"></small>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
var _previewBaseUrl = '{{ rtrim(url('/'), '/') }}/backend/diklat/dokumen-peserta/';

function previewDokumen(dokumenId) {
    var infoUrl  = _previewBaseUrl + dokumenId + '/info';
    var fileUrl  = _previewBaseUrl + dokumenId + '/file';
    var dlUrl    = fileUrl + '?download=1';

    // Reset
    ['previewFrame','previewError','previewDocFallback'].forEach(function(id) {
        var el = document.getElementById(id);
        el.style.display = 'none';
    });
    document.getElementById('previewLoading').style.display    = 'flex';
    document.getElementById('previewFrame').src                = 'about:blank';
    document.getElementById('previewNamaDokumen').textContent  = 'Memuat...';
    document.getElementById('previewInfoPeserta').textContent  = '';
    document.getElementById('previewUploadedAt').textContent   = '';

    $('#modalPreviewDokumen').modal('show');

    // Ambil info
    fetch(infoUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(d) {
            if (d.error) { showPreviewError(d.error, fileUrl); return; }

            document.getElementById('previewNamaDokumen').textContent = d.nama_dokumen;
            document.getElementById('previewInfoPeserta').textContent = d.nama_peserta + ' — ' + d.nip;
            document.getElementById('previewUploadedAt').textContent  = 'Diupload: ' + d.uploaded_at;
            document.getElementById('previewDownloadBtn').href        = dlUrl;
            document.getElementById('previewOpenNewTab').href         = fileUrl;

            if (d.is_doc) {
                document.getElementById('previewLoading').style.display     = 'none';
                document.getElementById('previewDocFallback').style.display = 'flex';
                document.getElementById('previewDocDownload').href          = dlUrl;
                return;
            }

            // PDF dan gambar — coba load di iframe
            var frame = document.getElementById('previewFrame');

            // Timeout: jika 8 detik belum ada response, tampilkan error dengan tombol tab baru
            var timeout = setTimeout(function() {
                if (document.getElementById('previewLoading').style.display !== 'none') {
                    showPreviewError(
                        'Dokumen memakan waktu terlalu lama untuk dimuat. ' +
                        'Klik tombol di bawah untuk membuka di tab baru.',
                        fileUrl
                    );
                }
            }, 8000);

            frame.onload = function() {
                clearTimeout(timeout);
                // Cek apakah iframe berhasil load konten (bukan halaman error)
                try {
                    // Jika bisa akses contentDocument, iframe berhasil load
                    var doc = frame.contentDocument || frame.contentWindow.document;
                    // PDF reader tidak expose contentDocument — anggap berhasil
                } catch(e) { /* cross-origin, normal untuk PDF */ }

                document.getElementById('previewLoading').style.display = 'none';
                frame.style.display = 'block';
            };

            frame.onerror = function() {
                clearTimeout(timeout);
                showPreviewError('Gagal memuat dokumen.', fileUrl);
            };

            frame.src = fileUrl;
        })
        .catch(function(err) {
            console.error('previewDokumen:', err);
            showPreviewError('Gagal mengambil informasi file.', fileUrl);
        });
}

function showPreviewError(msg, fallbackUrl) {
    document.getElementById('previewLoading').style.display = 'none';
    document.getElementById('previewFrame').style.display   = 'none';
    document.getElementById('previewErrorMsg').textContent  = msg || 'Gagal memuat dokumen.';
    if (fallbackUrl) {
        document.getElementById('previewOpenNewTab').href = fallbackUrl;
    }
    document.getElementById('previewError').style.display = 'flex';
}

$('#modalPreviewDokumen').on('hidden.bs.modal', function() {
    document.getElementById('previewFrame').src = 'about:blank';
});
</script>
