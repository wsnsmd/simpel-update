<div class="form-group">
    <label for="cetak_tgl" class="control-label">Tanggal <span class="text-danger">*</span></label>
    <input type="text" class="js-datepicker form-control" id="cetak_tgl" name="cetak_tgl" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd..." placeholder="yyyy-mm-dd" required>
</div>
<div class="form-group">
    <label for="cetak_tmpt" class="control-label">Mata Pelatihan / Materi <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="cetak_mapel" name="cetak_mapel" placeholder="Mata Pelatihan..." required>
</div>
<div class="form-group">
    <label for="cetak_tmpt" class="control-label">Fasilitator / Narasumber <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="cetak_fator" name="cetak_fator" placeholder="Fasilitator..." required>
</div>

<style>
    .autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
    .autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; cursor: pointer; }
    .autocomplete-selected { background: #F0F0F0; }
    .autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
    .autocomplete-group { padding: 2px 5px; }
    .autocomplete-group strong { display: block; border-bottom: 1px solid #000; }
</style>

<script>

    Dashmix.helpers(['datepicker']);

    $('#cetak_mapel').autocomplete({
        source: function( request, response ) {
        $.ajax({
            url: "{{ route('ajax.carimapel') }}",
            type: 'post',
            dataType: "json",
            data: {
                kurikulum_id: kurikulum_id,
                search: request.term
            },
            success: function( data ) {
                response($.map(data, function (item) {
                    return {
                        label: item.nama,
                        value: item.nama
                    };
                }));
            }
        })},
        minLength: 3
    });

    $('#cetak_fator').autocomplete({
        source: function( request, response ) {
        $.ajax({
            url: "{{ route('ajax.carifasilitator') }}",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response($.map(data, function (item) {
                    return {
                        label: item.nama,
                        value: item.nama
                    };
                }));
            }
        })},
        minLength: 3
    });
</script>