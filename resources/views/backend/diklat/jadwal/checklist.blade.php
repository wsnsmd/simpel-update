<!-- Quick Menu -->
<div class="pt-4 px-4 bg-body-dark rounded push">
    <div class="row row-deck">
        <div class="col-6 col-md-4 col-xl-2">
            <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="showTambah()">
                <div class="block-content">
                    <p class="mb-2 d-sm-block">
                        <i class="fa fa-plus-circle text-success fa-2x"></i>
                    </p>
                    <p class="font-w600 font-size-sm text-uppercase">Tambah</p>
                </div>
            </a>
        </div>        
        <div class="col-6 col-md-4 col-xl-2">
            <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="showImport()">
                <div class="block-content">
                    <p class="mb-2 d-sm-block">
                        <i class="fa fa-download text-warning fa-2x"></i>
                    </p>
                    <p class="font-w600 font-size-sm text-uppercase">Impor</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="{{ asset('/download/checklist_temp.xlsx')}}">
                <div class="block-content">
                    <p class="mb-2 d-sm-block">
                        <i class="fa fa-upload text-info fa-2x"></i>
                    </p>
                    <p class="font-w600 font-size-sm text-uppercase">Template</p>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- END Quick Menu --> 

<!-- Checklist-->
<div class="block block-bordered block-themed">
    <div class="block-header">
        <h3 class="block-title">Daftar Checklist</h3>
    </div>
    <div class="block-content block-content-full">
        <div class="table-responsive" id='checklist-load'>
        </div>
    </div>
</div>
<!-- END Checklist -->

<!-- Modal Tambah -->
<div class="modal fade" id="mdl-tambah" tabindex="-1" role="dialog" aria-labelledby="mdl-tambah" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <form id="mdl-tambah-form" method="POST" action="" autocomplete="off">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
            <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" id="mdl-form-title">Tambah Checklist</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content" id="mdl-form-content">
                            <div class="form-group">
                                <label for="dokumen" class="control-label">Dokumen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="dokumen" name="dokumen" placeholder="Dokumen..." required>
                            </div>
                            <div class="form-group">
                                <label for="keterangan" class="control-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan..."></textarea>
                            </div>
                        </div>
                        <div class="block-content block-content-full text-right bg-light">
                            <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary btn-submit"></i> Simpan</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
<!-- END Modal Tambah -->

<!-- Modal Upload -->
<div class="modal fade" id="mdl-upload" tabindex="-1" role="dialog" aria-labelledby="mdl-upload" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <form id="mdl-upload-form" method="POST" action="" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <input type="hidden" id="checklist_id" name="checklist_id" value="">
            <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" id="mdl-form-title">Upload Dokumen</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content" id="mdl-form-content">
                            <div class="form-group">
                                <label for="file" class="control-label">File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="file" name="file" accept="application/pdf, application/msword, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                            </div>
                        </div>
                        <div class="block-content block-content-full text-right bg-light">
                            <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary btn-submit"></i> Simpan</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
<!-- END Modal Upload -->

<!-- Modal Import -->
<div class="modal fade" id="mdl-import" tabindex="-1" role="dialog" aria-labelledby="mdl-import" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <form id="mdl-import-form" method="POST" action="" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <input type="hidden" id="jadwal_id" name="jadwal_id" value="{{ $jadwal->id }}">
            <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" id="mdl-form-title">Import Checklist</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content" id="mdl-form-content">
                            <div class="form-group">
                                <label for="file_import" class="control-label">File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="file_import" name="file_import" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                            </div>
                        </div>
                        <div class="block-content block-content-full text-right bg-light">
                            <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary btn-submit"></i> Simpan</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
<!-- END Modal Import -->

<script type="text/javascript">
    var action = '';
    var url_update = '';

    var loadchecklist = () => {
        var data = {
            jadwal_id: {{ $jadwal->id }},
        };
        var url = "{{ route('backend.diklat.checklist.load') }}";
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function (res) {
                $('#checklist-load').html(res);
            }
        });
    }

    jQuery(document).ready(function () {        
        loadchecklist();
    })

    var form_tambah = $('#mdl-tambah-form').validate({
        messages: {
            dokumen: {
                required: 'Dokumen tidak boleh kosong!'
            }
        },
        submitHandler: function(form) {
            if(action === 'add') {
                request = $.ajax({
                    type: 'POST',
                    cache: false,
                    url: '{{ route('backend.diklat.checklist.store') }}',
                    data: $(form).serialize(),
                    timeout: 3000
                });
            } else {
                request = $.ajax({
                    type: 'PATCH',
                    cache: false,
                    url: url_update,
                    data: $(form).serialize(),
                    timeout: 3000
                });
            }
            // Called on success.
            request.done(function(msg) {
                $('#mdl-tambah').modal('toggle');
                showNotifikasi(msg.pesan);
                loadchecklist();
            });
            // Called on failure.
            request.fail(function (jqXHR, textStatus, errorThrown) {
                $('#mdl-tambah').modal('toggle');
                showNotifikasi('Checklist gagal ditambah/disimpan!', 'danger');
                // log the error to the console
                console.error(
                    "The following error occurred: " + textStatus, errorThrown
                );
            });
            return false;
        } 
    })

    var form_upload = $('#mdl-upload-form').validate({
        messages: {
            file: {
                required: 'File tidak boleh kosong!'
            }
        },
        submitHandler: function(form) {
            var file_data = $('#file').prop('files')[0];
            var formData = new FormData();

            formData.append('file', file_data);
            formData.append('checklist_id', $('#checklist_id').val());            

            request = $.ajax({
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                url: '{{ route('backend.diklat.checklist.upload') }}',
                data: formData,
                //timeout: 3000
            });
            // Called on success.
            request.done(function(msg) {
                $('#mdl-upload').modal('toggle');
                showNotifikasi(msg.pesan);
                loadchecklist();
            });
            // Called on failure.
            request.fail(function (jqXHR, textStatus, errorThrown) {
                $('#mdl-upload').modal('toggle');
                showNotifikasi('Gagal upload file checklist!', 'danger');
                // log the error to the console
                console.error(
                    "The following error occurred: " + textStatus, errorThrown
                );
            });
            return false;
        } 
    })

    var form_import = $('#mdl-import-form').validate({
        messages: {
            file: {
                required: 'File tidak boleh kosong!'
            }
        },
        submitHandler: function(form) {
            var file_data = $('#file_import').prop('files')[0];
            var formData = new FormData();

            formData.append('file', file_data);
            formData.append('jadwal_id', $('#jadwal_id').val());            

            request = $.ajax({
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                url: '{{ route('backend.diklat.checklist.import') }}',
                data: formData,
                //timeout: 3000
            });
            // Called on success.
            request.done(function(msg) {
                $('#mdl-import').modal('toggle');
                showNotifikasi(msg.pesan);
                loadchecklist();
            });
            // Called on failure.
            request.fail(function (jqXHR, textStatus, errorThrown) {
                $('#mdl-import').modal('toggle');
                showNotifikasi('Gagal import checklist!', 'danger');
                // log the error to the console
                console.error(
                    "The following error occurred: " + textStatus, errorThrown
                );
            });
            return false;
        } 
    })

    $('#mdl-tambah').on('hidden.bs.modal', function() {        
        form_tambah.resetForm();
        $('input[name=dokumen]').val('');
        $('textarea[name=keterangan]').val('');
        $('.is-invalid').removeClass('is-invalid');
    });

    $('#mdl-upload').on('hidden.bs.modal', function() {        
        $('input[name=file]').val('');
        form_upload.resetForm();
        $('.is-invalid').removeClass('is-invalid');
    });

    $('#mdl-import').on('hidden.bs.modal', function() {        
        $('input[name=file_import]').val('');
        form_import.resetForm();
        $('.is-invalid').removeClass('is-invalid');
    });

    function showTambah() {
        action = 'add';
        $("#mdl-form-title").html("Tambah Checklist");
        $('#mdl-tambah').modal('show');        
    }

    function showEdit(id) {
        action = 'edit';

        var url = "{{ route('backend.diklat.checklist.show', ':id') }}";
        url_update = "{{ route('backend.diklat.checklist.update', ':id') }}";
        url = url.replace(':id', id);
        url_update = url_update.replace(':id', id);

        $('#mdl-form-title').html('Edit Checklist');

        $.ajax({
            type: 'GET',
            url: url,
            success: function(data) {
                $('#dokumen').val(data.dokumen);
                $('#keterangan').val(data.keterangan);
                $('#mdl-tambah').modal('show');
            }
        });
    }

    function showHapus(id) {
        var e = Swal.mixin({
                    buttonsStyling: !1,
                    customClass: {
                        confirmButton: "btn btn-success m-1",
                        cancelButton: "btn btn-danger m-1",
                        input: "form-control"
                    }
                });

        e.fire({   
            title: 'Apakah anda yakin',   
            text: 'Anda tidak akan dapat mengembalikan data anda',   
            type: 'warning',   
            showCancelButton: true,
            confirmButtonText: 'Ya',  
            cancelButtonText: 'Tidak',
            customClass: {
                confirmButton: "btn btn-danger m-1",
                cancelButton: "btn btn-secondary m-1"
            },
            html: !1
        }).then((result) => {
            if(result.value) {
                console.log('data ' + id + ' dihapus ');
                var data = {
                    id: id,
                };
                var url = "{{ route('backend.diklat.checklist.destroy', ':id') }}";
                url = url.replace(':id', id);
                console.log(url);
                var request = $.ajax({
                    type: 'DELETE',
                    url: url,
                    data: data,
                });
                request.done(function(msg) {
                    if(msg.status === 'success') {
                        showNotifikasi(msg.pesan);
                        loadchecklist();
                    } else {
                        showNotifikasi(msg.pesan, 'danger');
                    }
                });
                request.fail(function (jqXHR, textStatus, errorThrown){
                    showNotifikasi('Checklist gagal dihapus!', 'danger');
                    console.error(
                        "The following error occurred: " + textStatus, errorThrown
                    );
                });
            }
        });
    }

    function showUpload($id) {
        $('input[name=checklist_id]').val($id);
        $('input[name=file]').val('');
        $('#mdl-upload').modal('show');  
    }

    function showImport() {
        $('#mdl-import').modal('show');  
    }

    function showNotifikasi(msg, type='success') {
        var icon = type === 'success' ? 'fa fa-check mr-1' : 'fa fa-times mr-1';

        $.notify({
            icon: icon,
            message: msg
        }, {
            allow_dismiss: false,
            type: type,
            placement: {
                from: "top",
                align: "center"
            }
        });
    }
</script>