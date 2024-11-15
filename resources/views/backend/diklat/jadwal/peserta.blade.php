<!-- Quick Menu -->
@if(Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))
<div class="pt-4 px-4 bg-body-dark rounded push">
    <div class="row row-deck">        
        <div class="col-6 col-md-4 col-xl-2">
            <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="{{ route('backend.diklat.peserta.create', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]) }}">
                <div class="block-content">
                    <p class="mb-2 d-sm-block">
                        <i class="fa fa-plus-circle text-success fa-2x"></i>
                    </p>
                    <p class="font-w600 font-size-sm text-uppercase">Tambah Peserta</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('export-form1').submit();">
                <div class="block-content">
                    <p class="mb-2 d-sm-block">
                        <i class="fa fa-upload text-info fa-2x"></i>
                    </p>
                    <p class="font-w600 font-size-sm text-uppercase">Ekspor Peserta</p>
                </div>
            </a>
            <form id="export-form1" action="{{ route('backend.diklat.peserta.export') }}" method="post" style="display: none;" target="_export">
                @csrf
                <input type="hidden" name="export" value="1">
                <input type="hidden" name="jadwal_id" value="{{ $jadwal->id}}">
            </form>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('import-form').submit();">
                <div class="block-content">
                    <p class="mb-2 d-sm-block">
                        <i class="fa fa-download text-warning fa-2x"></i>
                    </p>
                    <p class="font-w600 font-size-sm text-uppercase">Impor Peserta</p>
                </div>
            </a>
            <form id="import-form" action="{{ route('backend.diklat.peserta.import', ['jadwal' => $jadwal->id]) }}" method="post" style="display: none;">
                @csrf                
            </form>
        </div>        
    </div>
</div>
@endif
<!-- END Quick Menu --> 

<!-- Peserta Verifikasi -->
<div class="block block-bordered block-themed">
    <div class="block-header bg-success">
        <h3 class="block-title">Sudah di Verifikasi</h3>
    </div>
    <div class="block-content block-content-full">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 30px;">#</th>
                        <th style="width: 60px;">Foto</th>
                        <th style="width: 12%;">NIP</th>   
                        <th>Nama</th>                         
                        <th>Satker</th>
                        <th>Instansi</th>
                        @if(Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))                
                        <th style="width: 5%;">Aksi</th>
                        <th style="width: 5%;">Batal</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pes_verif as $pv)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <img src="{{ is_null($pv->foto) ? asset('media/avatars/avatar8.jpg') :  asset(\Storage::url($pv->foto)) }}" class="img-avatar img-avatar-thumb img-avatar-rounded" style="height: auto;">
                        </td>
                        <td class="font-w600">
                            {{ $pv->nip }}
                        </td>
                        <td class="font-w600">
                            {{ $pv->nama_lengkap }}
                        </td>             
                        <td class="font-w600">
                            {{ $pv->satker_nama }}
                        </td>          
                        <td class="font-w600">
                            {{ $pv->instansi }}
                        </td>               
                        @if(Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))                                                                              
                        <td class="text-center">
                            <form action="{{ route('backend.diklat.peserta.destroy', $pv->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="btn-group">   
                                    <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pv->id]) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>
                                    <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="far fa-trash-alt"></i>
                                    </a>                               
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('backend.diklat.peserta.batal', $pv->id) }}" method="POST">
                                @csrf
                                <div class="btn-group">   
                                    <a href="javascript:;" onclick="return showBatal($(this).closest('form'));" class="btn btn-sm btn-danger" title="Batal"><i class="fa fa-times"></i></a>
                                </div>
                            </form>
                        </td>  
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- END Peserta Verifikasi -->

<!-- Peserta Belum Verifikasi -->
<div class="block block-bordered block-themed">
    <div class="block-header bg-warning">
        <h3 class="block-title">Belum di Verifikasi</h3>
    </div>
    <div class="block-content block-content-full border-top">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 30px;">#</th>
                        <th style="width: 60px;">Foto</th>
                        <th style="width: 12%;">NIP</th>   
                        <th>Nama</th>                         
                        <th>Satker</th>
                        <th>Instansi</th>
                        @if(Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))
                        <th style="width: 5%;">Verifikasi</th>
                        <th style="width: 5%;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pes_noverif as $pn)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <img src="{{ is_null($pn->foto) ? asset('media/avatars/avatar8.jpg') :  asset(\Storage::url($pn->foto)) }}" class="img-avatar img-avatar-thumb img-avatar-rounded" style="height: auto;">
                        </td>
                        <td class="font-w600">
                            {{ $pn->nip }}
                        </td>
                        <td class="font-w600">
                            {{ $pn->nama_lengkap }}
                        </td>             
                        <td class="font-w600">
                            {{ $pn->satker_nama }}
                        </td>          
                        <td class="font-w600">
                            {{ $pn->instansi }}
                        </td>
                        @if(Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))
                        <td class="text-center">
                            <form action="{{ route('backend.diklat.peserta.verifikasi', $pn->id) }}" method="POST">
                                @csrf
                                <div class="btn-group">   
                                    <a href="javascript:;" onclick="return showVerifikasi($(this).closest('form'), 1);" class="btn btn-sm btn-success" title="Setuju"><i class="fa fa-check"></i></a>
                                    <a href="javascript:;" onclick="return showVerifikasi($(this).closest('form'), 2);" class="btn btn-sm btn-danger" title="Tolak"><i class="fa fa-times"></i></a>
                                </div>
                            </form>
                        </td>                                
                        <td class="text-center">
                            <form action="{{ route('backend.diklat.peserta.destroy', $pn->id) }}" method="POST">
                                @csrf
                                @method('DELETE')                                        
                                <div class="btn-group">   
                                    <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pn->id]) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>                                            
                                    <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="far fa-trash-alt"></i>
                                    </a>                               
                                </div>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- END Peserta Belum Verifikasi -->
<!-- Peserta Batal -->
<div class="block block-bordered block-themed">
    <div class="block-header bg-danger">
        <h3 class="block-title">Batal</h3>
    </div>
    <div class="block-content block-content-full border-top">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 30px;">#</th>
                        <th style="width: 60px;">Foto</th>
                        <th style="width: 12%;">NIP</th>   
                        <th>Nama</th>                         
                        <th>Satker</th>
                        <th>Instansi</th>
                        @if(Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))
                        <th style="width: 8%;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pes_batal as $pb)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <img src="{{ is_null($pb->foto) ? asset('media/avatars/avatar8.jpg') :  asset(\Storage::url($pb->foto)) }}" class="img-avatar img-avatar-thumb img-avatar-rounded" style="height: auto;">
                        </td>
                        <td class="font-w600">
                            {{ $pb->nip }}
                        </td>
                        <td class="font-w600">
                            {{ $pb->nama_lengkap }}
                        </td>             
                        <td class="font-w600">
                            {{ $pb->satker_nama }}
                        </td>          
                        <td class="font-w600">
                            {{ $pb->instansi }}
                        </td>   
                        @if(Gate::check('isUser') || (Gate::check('isKontribusi') && $jadwal->status_jadwal < 3))                                                          
                        <td class="text-center">
                            <form action="{{ route('backend.diklat.peserta.destroy', $pb->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="btn-group">   
                                    <a href="{{ route('backend.diklat.peserta.edit', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $pb->id]) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>
                                    <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="far fa-trash-alt"></i>
                                    </a>                               
                                </div>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- END Peserta Batal -->

<script>
    jQuery(function(){
        jQuery('.js-dataTable-full').dataTable({
            pageLength: 50,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            autoWidth: false,
        });
    });

    // function exportPeserta(id) {
    //     var form = $('#export-form');
    //     $('#export='+id).remove();
    //     form.append('<input type="hidden" id="export-'+id+'" name="export" value="'+id+'" /> ');
    //     form.submit();
    //     // var url = "{{ route('backend.diklat.peserta.export') }}";

    //     // $.post(url, { name: "John", time: "2pm" })
    //     // .done(function( data ) {
    //     //     alert( "Data Loaded: " + data );
    //     // });
    // }

    function showVerifikasi(form, id) {
        var e = Swal.mixin({
                    buttonsStyling: !1,
                    customClass: {
                        confirmButton: "btn btn-success m-1",
                        cancelButton: "btn btn-danger m-1",
                        input: "form-control"
                    }
                });

        if(id == 1) {
            e.fire({   
                title: 'Apakah anda yakin',   
                text: 'Melakukan verifikasi dan menyetujui peserta?',   
                type: 'warning',   
                showCancelButton: true,
                confirmButtonText: 'Setuju',  
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: "btn btn-success m-1",
                    cancelButton: "btn btn-secondary m-1"
                },
                html: !1
            }).then((result) => {
                if(result.value) {
                    form.append('<input type="hidden" name="setuju" value="1" /> ');
                    form.submit();
                }
            });
        }
        else if(id == 2) {
            e.fire({   
                title: 'Apakah anda yakin',   
                text: 'Melakukan verifikasi dan menolak peserta?',   
                type: 'warning',   
                showCancelButton: true,
                confirmButtonText: 'Tolak',  
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: "btn btn-danger m-1",
                    cancelButton: "btn btn-secondary m-1"
                },
                html: !1
            }).then((result) => {
                if(result.value) {
                    form.append('<input type="hidden" name="setuju" value="2" /> ');
                    form.submit();
                }
            });
        }
    }

    async function showBatal(form) {
        var e = Swal.mixin({
                    buttonsStyling: !1,
                    customClass: {
                        confirmButton: "btn btn-danger m-1",
                        cancelButton: "btn btn-secondary m-1",
                        input: "form-control",
                    }
                });

        const { value: text } = await e.fire({   
            title: 'Apakah anda yakin',   
            text: 'Melakukan pembatalan pada peserta?',   
            type: 'warning',   
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
            input: 'textarea',
            inputPlaceholder: 'Keterangan Batal...',
            inputAttributes: {
                'aria-label': 'Keterangan Batal'
            },
            html: !1,
            inputValidator: (value) => {
                return !value && 'Tidak boleh kosong!'
            }
        });

        if(text) {
            form.append('<input type="hidden" name="batal_ket" value="' + text + '" /> ');
            form.submit();
        }
    }   
</script>