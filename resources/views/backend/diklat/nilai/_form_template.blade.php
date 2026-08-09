@php $isEdit = isset($item); @endphp

<div class="form-group">
    <label class="font-w600 font-size-sm">
        Nama Template <span class="text-danger">*</span>
    </label>
    <input type="text" name="nama" class="form-control"
           placeholder="cth: PKA 2023 Klasikal, Latsar 2026 Blended"
           value="{{ $isEdit ? $item->nama : old('nama') }}"
           maxlength="200" required>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600 font-size-sm">Tahun Peraturan</label>
            <input type="number" name="tahun" class="form-control"
                   placeholder="2024"
                   value="{{ $isEdit ? $item->tahun : old('tahun') }}"
                   min="2000" max="2100">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600 font-size-sm">Mode</label>
            <select name="mode" class="form-control">
                @foreach (['klasikal' => 'Klasikal', 'blended' => 'Blended', 'distance' => 'Distance'] as $val => $lbl)
                <option value="{{ $val }}"
                    {{ ($isEdit ? $item->mode : old('mode','klasikal')) === $val ? 'selected' : '' }}>
                    {{ $lbl }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600 font-size-sm">PG per Aspek <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" name="passing_grade_aspek" class="form-control"
                       step="0.01" min="0" max="100"
                       value="{{ $isEdit ? $item->passing_grade_aspek : old('passing_grade_aspek', '70.01') }}"
                       required>
                <div class="input-group-append">
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="font-w600 font-size-sm">Jenis Diklat</label>
    <select name="jenis_diklat_id" class="form-control">
        <option value="">-- Semua Jenis --</option>
        @foreach ($jenisDiklat as $j)
        <option value="{{ $j->id }}"
            {{ ($isEdit && $item->jenis_diklat_id == $j->id) ? 'selected' : '' }}>
            {{ $j->nama }}
        </option>
        @endforeach
    </select>
    <small class="text-muted">Opsional — untuk pengelompokan referensi saja</small>
</div>

<div class="form-group mb-0">
    <label class="font-w600 font-size-sm">Deskripsi</label>
    <textarea name="deskripsi" class="form-control" rows="2"
              placeholder="Keterangan singkat template ini...">{{ $isEdit ? $item->deskripsi : old('deskripsi') }}</textarea>
</div>
