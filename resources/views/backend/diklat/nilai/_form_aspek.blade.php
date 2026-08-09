@php $isEdit = isset($item); @endphp

<div class="form-group">
    <label class="font-w600 font-size-sm">
        Nama Aspek <span class="text-danger">*</span>
    </label>
    <input type="text" name="nama" class="form-control"
           placeholder="cth: Evaluasi Akademik, Sikap &amp; Perilaku"
           value="{{ $isEdit ? $item->nama : old('nama') }}"
           maxlength="200" required>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600 font-size-sm">
                Bobot <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input type="number" name="bobot" class="form-control"
                       step="0.01" min="0.01" max="1"
                       placeholder="0.10"
                       value="{{ $isEdit ? $item->bobot : old('bobot') }}"
                       required>
                <div class="input-group-append">
                    <span class="input-group-text" style="font-size:.75rem">desimal</span>
                </div>
            </div>
            <small class="text-muted">Contoh: 0.10 = 10%, 0.20 = 20%</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600 font-size-sm">Penilai <span class="text-danger">*</span></label>
            <select name="penilai" class="form-control" required>
                <option value="operator"
                    {{ ($isEdit ? $item->penilai : 'operator') === 'operator' ? 'selected' : '' }}>
                    Operator / Panitia
                </option>
                <option value="penguji"
                    {{ ($isEdit && $item->penilai === 'penguji') ? 'selected' : '' }}>
                    Penguji (Seminar)
                </option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600 font-size-sm">Fase Seminar</label>
            <select name="fase" class="form-control">
                <option value="" {{ ($isEdit && !$item->fase) ? 'selected' : '' }}>
                    — Bukan Seminar —
                </option>
                <option value="rancangan"
                    {{ ($isEdit && $item->fase === 'rancangan') ? 'selected' : '' }}>
                    Seminar Rancangan
                </option>
                <option value="akhir"
                    {{ ($isEdit && $item->fase === 'akhir') ? 'selected' : '' }}>
                    Seminar Akhir
                </option>
            </select>
            <small class="text-muted">Isi jika penilai = Penguji</small>
        </div>
    </div>
</div>

<div class="form-group mb-0">
    <label class="font-w600 font-size-sm">Keterangan / Panduan</label>
    <input type="text" name="keterangan" class="form-control"
           placeholder="Panduan singkat penilaian aspek ini (opsional)"
           value="{{ $isEdit ? $item->keterangan : old('keterangan') }}"
           maxlength="500">
</div>
