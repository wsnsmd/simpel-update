@php $isEdit = isset($itemSesi); @endphp

<div class="form-group">
    <label class="font-w600">Nama Materi / Sesi <span class="text-danger">*</span></label>
    <input type="text" name="nama_materi" class="form-control"
           placeholder="cth: Manajemen Kinerja ASN, Pembukaan & Orientasi"
           value="{{ $isEdit ? $itemSesi->nama_materi : old('nama_materi') }}"
           maxlength="200" required>
</div>

<div class="form-group">
    <label class="font-w600">Widyaiswara / Fasilitator</label>
    <input type="text" name="widyaiswara" class="form-control"
           placeholder="Nama fasilitator (opsional)"
           value="{{ $isEdit ? $itemSesi->widyaiswara : old('widyaiswara') }}"
           maxlength="100">
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600">Jumlah JP <span class="text-danger">*</span></label>
            <input type="number" name="jp" class="form-control"
                   min="1" max="20"
                   value="{{ $isEdit ? $itemSesi->jp : old('jp', 2) }}"
                   required>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label class="font-w600">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" class="form-control"
                   value="{{ $isEdit ? $itemSesi->tanggal->format('Y-m-d') : old('tanggal') }}"
                   required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600">Jam Mulai <span class="text-danger">*</span></label>
            <input type="time" name="jam_mulai" class="form-control"
                   value="{{ $isEdit ? substr($itemSesi->jam_mulai, 0, 5) : old('jam_mulai', '08:00') }}"
                   required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600">Jam Selesai <span class="text-danger">*</span></label>
            <input type="time" name="jam_selesai" class="form-control"
                   value="{{ $isEdit ? substr($itemSesi->jam_selesai, 0, 5) : old('jam_selesai', '10:00') }}"
                   required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="font-w600">Batas Terlambat</label>
            <div class="input-group">
                <input type="number" name="batas_terlambat" class="form-control"
                       min="0" max="120"
                       value="{{ $isEdit ? $itemSesi->batas_terlambat : old('batas_terlambat', 30) }}">
                <div class="input-group-append">
                    <span class="input-group-text">menit</span>
                </div>
            </div>
        </div>
    </div>
</div>
