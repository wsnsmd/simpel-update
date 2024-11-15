<!-- Cetak -->
<div class="block block-bordered block-themed">
    <div class="block-header">
        <h3 class="block-title">Daftar Cetak</h3>
    </div>
    <div class="block-content block-content-full">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th class="font-w700 text-center" style="width: 30px;">#</th>
                        <th class="font-w700" style="">Nama</th>                 
                        <th class="font-w700 text-center" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($umum as $u)
                        <tr>
                            <td class="font-w600 text-center">{{$loop->iteration}}.</td>
                            <td class="font-w600">{{$u->nama}}</td>
                            <td class="font-w600 text-center">
                                @if($u->modal_id == 1)
                                <form action="{{ route('backend.cetak.cetak', $u->id) }}" method="POST" target="_cetak">
                                @csrf
                                <input type="hidden" name="jadwal_id" value="{{$jadwal->id}}">
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-print mr-1"></i> Cetak
                                </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="return showDialog({{ $u->id }}, {{ $u->modal_id }}, '{{ $u->nama }}')">
                                    <i class="fa fa-print mr-1"></i> Cetak
                                </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- END Cetak -->

<!-- Block Modal -->
<div class="modal fade" id="mdl-cetak" tabindex="-1" role="dialog" aria-labelledby="mdl-cetak" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <form id="mdl-cetak-form" method="POST" action="" autocomplete="off" target="_cetak">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{$jadwal->id}}">
            <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" id="mdl-cetak-title">Cetak</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content" id="mdl-form-content">
                        </div>
                        <div class="block-content block-content-full text-right bg-light">
                            <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-print mr-1"></i> Cetak</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>

<script>
    function showDialog(id, modal, title) {
        var url_form = "{{ route('backend.cetak.cetak', ':id') }}";
        var url_modal = "{{ route('backend.cetak.modal', ':id') }}";
        url_form = url_form.replace(':id', id);
        url_modal = url_modal.replace(':id', modal);

        $('#mdl-cetak-form').attr("action", url_form);
        $('#mdl-cetak-title').html('Cetak - ' + title);

        $.ajax({
            type: "POST",
            url: url_modal,
            success: function(data) {
                $('#mdl-form-content').html(data);
                $('#mdl-cetak').modal('show');
                console.log(kurikulum_id);
            }
        });

        // $("#mdl-cetak").modal('show');
    }
    $('#mdl-cetak-form').submit(function(e) {
        $('#mdl-cetak').modal('hide'); //or  $('#IDModal').modal('hide');
    });
</script>