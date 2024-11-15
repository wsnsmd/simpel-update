@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">    
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/additional-methods.js') }}"></script>
    <script>
        jQuery(function(){
            // Override a few default classes
            jQuery.extend(jQuery.fn.dataTable.ext.classes, {
                sWrapper: "dataTables_wrapper dt-bootstrap4",
                sFilterInput:  "form-control",
                sLengthSelect: "form-control"
            });

            // Override a few defaults
            jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                language: {
                    emptyTable: "Tidak ada data tersedia",
                    infoEmpty: "Halaman 0 dari 0",
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Cari...",
                    info: "Halaman <strong>_PAGE_</strong> dari <strong>_PAGES_</strong>",
                    paginate: {
                        first: '<i class="fa fa-angle-double-left"></i>',
                        previous: '<i class="fa fa-angle-left"></i>',
                        next: '<i class="fa fa-angle-right"></i>',
                        last: '<i class="fa fa-angle-double-right"></i>'
                    }
                }
            });

            jQuery('.js-dataTable-full').dataTable({
                pageLength: 50,
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                autoWidth: false,
                scrollX: false,
            });

            $('[data-toggle="tooltip"]').tooltip();   
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(function(){
            Dashmix.helpers(['datepicker', 'validation']); 
        });

        @if (session('success'))
        $.notify({
            icon: "fa fa-check mr-1",
            message: "{{ session('success') }}"
        }, {
            allow_dismiss: false,
            type: 'success',
            placement: {
                from: "top",
                align: "center"
            }
        });
        @elseif (session('error'))
        $.notify({
            icon: "fa fa-times mr-1",
            message: "{{ session('error') }}"
        }, {
            allow_dismiss: false,
            type: 'danger',
            placement: {
                from: "top",
                align: "center"
            }
        });
        @endif

        jQuery(document).ready(function () { 
            jQuery.extend(jQuery.validator.messages, {
                required: "Wajib diisi.",
            });

            var form_tt = $('#mdl-tt-form').validate({
                errorElement: "em",
				errorPlacement: function ( error, element ) {
					// Add the `help-block` class to the error element
					error.addClass( "help-block" );

					if ( element.prop( "type" ) === "checkbox" ) {
						error.insertAfter( element.parent( "label" ) );
					} else {
						error.insertAfter( element );
					}
				},
                highlight: function ( element, errorClass, validClass ) {
					$( element ).addClass( "is-invalid" ).removeClass( "valid" );
				},
				unhighlight: function (element, errorClass, validClass) {
					$( element ).addClass( "valid" ).removeClass( "is-invalid" );
				}
            });

            $('#mdl-tt').on('hidden.bs.modal', function() {        
                form_tt.resetForm();
                $('#mdl-tt-form').trigger('reset');
                $('.is-invalid').removeClass('is-invalid');
            });
        })
        
        function showTT() {            
            $.ajax({
                type: "get",
                url: "{{ route('backend.diklat.mapel.tt.edit', ['jadwal' => $jadwal->id]) }}",
                success: function(data) {
                    $("#tempat").val(data.tempat);
                    $("#tanggal").val(data.tanggal);
                    $("#an").val(data.an);
                    $("#jabatan").val(data.jabatan);
                    $("#nip").val(data.nip);
                    $("#nama").val(data.nama);
                    $("#pangkat").val(data.pangkat);
                    $("#paraf1_nama").val(data.paraf1_nama);
                    $("#paraf1_jabatan").val(data.paraf1_jabatan);
                    $("#paraf2_nama").val(data.paraf2_nama);
                    $("#paraf2_jabatan").val(data.paraf2_jabatan);
                }
            });
            $('#mdl-tt').modal('show');
        }
    </script>
@endsection

@section('content')
    <!-- Hero -->
    @include('backend.diklat.jadwal.detail_hero')
    <!-- END Hero -->

    <!-- Quick Menu -->
    @if(Gate::check('isUser'))
    <div class="pt-4 px-4 bg-body-dark rounded push">
        <div class="row row-deck">
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="showTT()">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-cog fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Tanda Tangan</p>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-rounded block-link-pop text-center d-flex align-items-center" href="javascript:;" onclick="event.preventDefault(); document.getElementById('rekap-form').submit();">
                    <div class="block-content">
                        <p class="mb-2 d-sm-block">
                            <i class="fa fa-file-alt text-info fa-2x"></i>
                        </p>
                        <p class="font-w600 font-size-sm text-uppercase">Rekap. Surat Tugas</p>
                    </div>
                </a>
                <form id="rekap-form" action="{{ route('backend.diklat.mapel.wi.rekap', ['jadwal' => $jadwal->id]) }}" method="post" style="display: none;" target="_cetak">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    @endif
    <!-- END Quick Menu --> 

    <!-- Page Content -->
    <div class="content">
        <!-- Mata Pelatihan -->        
        <div class="block block-bordered block-themed">
            <div class="block-header">
                <h3 class="block-title">Daftar Mata Pelatihan</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="font-w700 text-center" style="width: 30px;">#</th>
                                <th>Nama</th>
                                @if ($kurikulum->jenis_belajar == 1 || $kurikulum->jenis_belajar == 2)
                                <th>Klasikal (JP)</th>
                                @endif
                                @if ($kurikulum->jenis_belajar == 2 || $kurikulum->jenis_belajar == 3)
                                <th>E-Learning (JP)</th>    
                                @endif
                                <th>Jumlah (JP)</th>
                                <th style="width: 1%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mapel as $m)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-w600">
                                    {{ $m->nama }}
                                </td>
                                @if ($kurikulum->jenis_belajar == 1 || $kurikulum->jenis_belajar == 2)
                                <td class="font-w600">
                                    {{ $m->jpk }}
                                </td>
                                @endif
                                @if ($kurikulum->jenis_belajar == 2 || $kurikulum->jenis_belajar == 3)
                                <td class="font-w600">
                                    {{ $m->jpe }}
                                </td>
                                @endif
                                <td class="font-w600">
                                    {{ $m->jpk + $m->jpe }}
                                </td>                                                             
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('backend.diklat.mapel.jadwal', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama), 'id' => $m->id]) }}" class="btn btn-sm btn-outline-primary" title="Jadwal">
                                            <i class="fa fa-calendar-alt"></i>
                                        </a>                                        
                                    </div>
                                </td>
                            </tr>                            
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Mata Pelatihan -->

        <!-- Modal Tanda Tangan -->
        <div class="modal fade" id="mdl-tt" tabindex="-1" role="dialog" aria-labelledby="mdl-tt" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-popin" role="document">
                <form id="mdl-tt-form" method="POST" action="{{ route('backend.diklat.mapel.tt.update', ['jadwal' => $jadwal->id]) }}" autocomplete="off">
                    @csrf
                    <div class="modal-content">
                            <div class="block block-themed block-transparent mb-0">
                                <div class="block-header bg-primary-dark">
                                    <h3 class="block-title">Tanda Tangan</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content" id="mdl-form-content">                                    
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="tempat" class="control-label">Tempat <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="tempat" name="tempat" placeholder="Cth. Samarinda..." required>
                                        </div>
                                        <div class="col-6">
                                            <label for="tanggal" class="control-label">Tanggal<span class="text-danger">*</span></label>
                                            <input type="text" class="js-datepicker form-control" id="tanggal" name="tanggal" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required>
                                        </div>
                                    </div>
                                    <h2 class="content-heading pt-0 mb-3">Pejabat Penandatangan</h2>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="import" class="control-label">a.n. Kepala <span class="text-danger">*</span></label>
                                            <select class="form-control" id="an" name="an" style="width: 100%;" required>
                                                <option value="" selected>-- Pilih a.n. Kepala --</option>
                                                <option value="0">Tidak</option>
                                                <option value="1">Ya</option>
                                            </select>
                                        </div>
                                    </div>  
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="jabatan" class="control-label">Jabatan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Cth. Kepala, Plt. Kepala..." required>
                                        </div>
                                        <div class="col-6">
                                            <label for="nip" class="control-label">NIP <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nip" name="nip" placeholder="NIP..." min="18" maxlength="18" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="nama" class="control-label">Nama <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama..." required>
                                        </div>
                                        <div class="col-6">
                                            <label for="pangkat" class="control-label">Pangkat / Gol. Ruang <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="pangkat" name="pangkat" placeholder="Pangkat..." required>
                                        </div>
                                    </div>  
                                    <h2 class="content-heading pt-0 mb-3">Paraf</h2>                                     
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="jabatan" class="control-label">Paraf-1 Nama </label>
                                            <input type="text" class="form-control" id="paraf1_nama" name="paraf1_nama" placeholder="Nama..." >
                                        </div>
                                        <div class="col-6">
                                            <label for="nip" class="control-label">Paraf-1 Jabatan</label>
                                            <input type="text" class="form-control" id="paraf1_jabatan" name="paraf1_jabatan" placeholder="Jabatan...">
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <div class="col-6">
                                            <label for="nama" class="control-label">Paraf-2 Nama</label>
                                            <input type="text" class="form-control" id="paraf2_nama" name="paraf2_nama" placeholder="Nama...">
                                        </div>
                                        <div class="col-6">
                                            <label for="pangkat" class="control-label">Paraf-2 Jabatan</label>
                                            <input type="text" class="form-control" id="paraf2_jabatan" name="paraf2_jabatan" placeholder="Jabatan...">
                                        </div>
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
        <!-- END Tanda Tangan -->
    </div>
    <!-- END Page Content -->     
@endsection
