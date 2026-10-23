@php $isEdit = isset($itemSyarat); @endphp

<div class="form-group">
    <label class="font-w600">Nama Dokumen <span class="text-danger">*</span></label>
    <input type="text" name="nama" class="form-control" placeholder="cth: Surat Tugas, Lembar Komitmen, Pas Foto 3x4"
        value="{{ $isEdit ? $itemSyarat->nama : old('nama') }}" maxlength="100" required>
    <small class="form-text text-muted">Nama ini akan ditampilkan ke peserta di dashboard mereka.</small>
</div>

<div class="form-group">
    <label class="font-w600">Keterangan / Petunjuk</label>
    <textarea name="keterangan" class="form-control" rows="2"
        placeholder="cth: Surat tugas dari instansi asal, ditandatangani atasan langsung"
        maxlength="500">{{ $isEdit ? $itemSyarat->keterangan : old('keterangan') }}</textarea>
    <small class="form-text text-muted">Opsional. Ditampilkan ke peserta sebagai panduan upload.</small>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="form-group">
            <label class="font-w600">Format File yang Diizinkan <span class="text-danger">*</span></label>
            @php
                $formatAda = $isEdit ? array_map('trim', explode(',', $itemSyarat->format_izin)) : ['pdf'];
                $opsiFormat = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                $uid = $isEdit ? $itemSyarat->id : 'baru';
            @endphp
            <div>
                @foreach ($opsiFormat as $fmt)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input fmt-check-{{ $uid }}"
                            id="fmt_{{ $uid }}_{{ $fmt }}" value="{{ $fmt }}" {{ in_array($fmt, $formatAda) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="fmt_{{ $uid }}_{{ $fmt }}">
                            {{ strtoupper($fmt) }}
                        </label>
                    </div>
                @endforeach
            </div>
            <input type="hidden" name="format_izin" id="format_izin_{{ $uid }}"
                value="{{ $isEdit ? $itemSyarat->format_izin : 'pdf' }}">
            <small class="form-text text-muted">Pilih minimal satu format.</small>
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label class="font-w600">Ukuran Maksimal <span class="text-danger">*</span></label>
            <select name="max_size_kb" class="form-control">
                @php $maxAda = $isEdit ? $itemSyarat->max_size_kb : 2048; @endphp
                @foreach ([512 => '512 KB', 1024 => '1 MB', 2048 => '2 MB', 5120 => '5 MB', 10240 => '10 MB'] as $kb => $label)
                    <option value="{{ $kb }}" {{ $maxAda == $kb ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-w600">Status <span class="text-danger">*</span></label>
            @php $wajibAda = $isEdit ? $itemSyarat->wajib : true; @endphp
            <select name="wajib" class="form-control">
                <option value="1" {{ $wajibAda ? 'selected' : '' }}>Wajib — harus diupload peserta</option>
                <option value="0" {{ !$wajibAda ? 'selected' : '' }}>Opsional — boleh tidak diupload</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-w600">Urutan Tampil <span class="text-danger">*</span></label>
            <input type="number" name="urutan" class="form-control" min="1" max="99"
                value="{{ $isEdit ? $itemSyarat->urutan : 1 }}" required>
            <small class="form-text text-muted">Angka kecil tampil lebih dahulu.</small>
        </div>
    </div>
</div>

<script>
    (function () {
        var uid = '{{ $uid }}';
        function updateFormatIzin() {
            var checked = [];
            document.querySelectorAll('.fmt-check-' + uid).forEach(function (cb) {
                if (cb.checked) checked.push(cb.value);
            });
            var hidden = document.getElementById('format_izin_' + uid);
            if (hidden) hidden.value = checked.length ? checked.join(',') : 'pdf';
        }
        document.querySelectorAll('.fmt-check-' + uid).forEach(function (cb) {
            cb.addEventListener('change', updateFormatIzin);
        });
    })();
</script>