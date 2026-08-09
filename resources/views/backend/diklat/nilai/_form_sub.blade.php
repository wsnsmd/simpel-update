@php $isEdit = isset($item); @endphp

<div class="form-group">
    <label class="font-w600 font-size-sm">
        Nama Sub-komponen <span class="text-danger">*</span>
    </label>
    <input type="text" name="nama" class="form-control"
           placeholder="cth: Analisis Kepemimpinan Kinerja, Laporan Kelompok"
           value="{{ $isEdit ? $item->nama : old('nama') }}"
           maxlength="200" required>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-w600 font-size-sm">
                Bobot <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input type="number" name="bobot" class="form-control"
                       step="0.01" min="0.01" max="1"
                       placeholder="0.04"
                       value="{{ $isEdit ? $item->bobot : old('bobot') }}"
                       required>
                <div class="input-group-append">
                    <span class="input-group-text" style="font-size:.75rem">desimal</span>
                </div>
            </div>
            <small class="text-muted">Contoh: 0.04 = 4%, 0.10 = 10%</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-0">
            <label class="font-w600 font-size-sm">Keterangan / Rubrik Singkat</label>
            <input type="text" name="keterangan" class="form-control"
                   placeholder="Panduan penilaian sub-komponen (opsional)"
                   value="{{ $isEdit ? $item->keterangan : old('keterangan') }}"
                   maxlength="500">
        </div>
    </div>
</div>

<div class="alert alert-info font-size-sm mt-2 mb-0">
    <i class="fa fa-info-circle mr-1"></i>
    Penilai dan fase sub-komponen mengikuti aspek induknya secara otomatis.
</div>
