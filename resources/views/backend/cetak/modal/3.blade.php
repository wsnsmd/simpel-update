<div class="form-group">
    <label for="cetak_tmpt" class="control-label">Tempat <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="cetak_tmpt" name="cetak_tmpt" placeholder="Tempat..." required>
</div>
<div class="form-group">
    <label for="cetak_tgl" class="control-label">Tanggal <span class="text-danger">*</span></label>
    <input type="text" class="js-datepicker form-control" id="cetak_tgl" name="cetak_tgl" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
</div>

<script>
    Dashmix.helpers(['datepicker']);
</script>