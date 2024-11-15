@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->  
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-fileinput/css/fileinput.min.css') }}">

    <style>
        .kv-avatar .krajee-default.file-preview-frame,.kv-avatar .krajee-default.file-preview-frame:hover {
            margin: 0;
            padding: 0;
            border: none;
            box-shadow: none;
            text-align: center;
        }
        .kv-avatar {
            display: inline-block;
        }
        .kv-avatar .file-input {
            display: table-cell;
            width: 160px;
        }
        .kv-reqd {
            color: red;
            font-family: monospace;
            font-weight: normal;
        }
    </style>  --}}
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    {{-- <script src="{{ asset('js/plugins/bootstrap-fileinput/js/fileinput.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-fileinput/js/plugins/piexif.js') }}"></script> --}}


    <script>
        jQuery(function(){ 
            Dashmix.helpers(['datepicker', 'maxlength']); 
        });   

        {{--
        // $("#avatar-1").fileinput({
        //     overwriteInitial: true,
        //     showClose: false,
        //     showCaption: false,
        //     showUpload: false,
        //     showCancel: false,
        //     removeFromPreviewOnError: true,
        //     browseLabel: 'Cari...',
        //     removeLabel: 'Hapus',
        //     removeTitle: 'Hapus',
        //     browseIcon: '<i class="fa fa-folder-open"></i>',
        //     removeIcon: '<i class="fa fa-trash"></i>',
        //     browseClass: 'btn btn-sm btn-primary',
        //     removeClass: 'btn btn-sm btn-danger',
        //     dropZoneEnabled: false,
        //     showUploadedThumbs: false,
        //     showZoom: false,
        //     fileActionSettings: {
        //         showZoom: false,
        //     },
        //     defaultPreviewContent: '<img src="{{ asset('media/avatars/avatar0.jpg') }}" alt="Foto">',
        //     layoutTemplates: {main2: '{preview} ' + ' {remove} {browse}\n'},
        //     elErrorContainer: '#kv-avatar-errors-1',
        //     msgErrorClass: 'alert alert-block alert-danger invalid-feedback',
        // });
        --}}

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
    </script>
@endsection

@section('content')
    <!-- Hero -->
    @include('backend.diklat.fasilitator.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Fasilitator - Tambah Data</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <form class="mb-2" action="{{ route('backend.diklat.fasilitator.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="foto">Foto</label>
                        <div class="col-sm-10">
                            <input id="foto" name="foto" type="file" autofocus>
                            <small class="form-text text-muted">Hanya file (*.jpg, *.png, *.gif) dengan ukuran < 512 KB</small>
                            @if ($errors->has('foto'))
                            <div class="invalid-feedback d-block">{{ $errors->first('foto') }}</div>
                            @endif
                        </div>
                    </div>                            
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="nip">NIP</label>
                        <div class="col-sm-10">
                            <input type="text" class="js-maxlength form-control{{ $errors->has('nip') ? ' is-invalid' : '' }}" id="nip" name="nip" maxlength="18" placeholder="NIP..." value="{{ old('nip') }}" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary">
                            @if ($errors->has('nip'))
                            <div class="invalid-feedback">{{ $errors->first('nip') }}</div>
                            @endif
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="nama">Nama <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" id="nama" name="nama" placeholder="Nama..." value="{{ old('nama') }}">
                            @if ($errors->has('nama'))
                            <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="pangkat">Pangkat / TMT</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="pangkat" name="pangkat" style="width: 100%;" data-placeholder="-- Pilih Pangkat --">
                                <option value="" selected>-- Pilih Pangkat --</option>
                                @foreach ($pangkat as $p)
                                <option value="{{ $p->id }}">{{ $p->singkat }}</option>                                    
                                @endforeach
                            </select>
                            @if ($errors->has('pangkat'))
                            <div class="invalid-feedback">{{ $errors->first('pangkat') }}</div>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="js-datepicker form-control{{ $errors->has('tmt_pangkat') ? ' is-invalid' : '' }}" id="tmt_pangkat" name="tmt_pangkat" value="{{ old('tmt_pangkat') }}" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd">
                            @if ($errors->has('tmt_pangkat'))
                            <div class="invalid-feedback">{{ $errors->first('tmt_pangkat') }}</div>
                            @endif
                        </div>                        
                    </div>                       
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="jabatan">Jabatan / TMT <span class="text-danger">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control{{ $errors->has('jabatan') ? ' is-invalid' : '' }}" id="jabatan" name="jabatan" placeholder="Jabatan..." value="{{ old('jabatan') }}" autofocus>
                            @if ($errors->has('jabatan'))
                            <div class="invalid-feedback">{{ $errors->first('jabatan') }}</div>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="js-datepicker form-control{{ $errors->has('tmt_jabatan') ? ' is-invalid' : '' }}" id="tmt_jabatan" name="tmt_jabatan" value="{{ old('pangkat') }}" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd">
                            @if ($errors->has('pangkat'))
                            <div class="invalid-feedback">{{ $errors->first('pangkat') }}</div>
                            @endif
                        </div>     
                    </div>                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="instansi">Instansi</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('instansi') ? ' is-invalid' : '' }}" id="instansi" name="instansi" placeholder="Instansi..." value="{{ old('instansi') }}">
                            @if ($errors->has('instansi'))
                            <div class="invalid-feedback">{{ $errors->first('instansi') }}</div>
                            @endif
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="satker_nama">Satuan Kerja (SKPD/OPD)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('satker_nama') ? ' is-invalid' : '' }}" id="satker_nama" name="satker_nama" placeholder="Satuan Kerja..." value="{{ old('satker_nama') }}">
                            @if ($errors->has('satker_nama'))
                            <div class="invalid-feedback">{{ $errors->first('satker_nama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group mt-4 row">
                        <label class="col-sm-2 col-form-label text-right">&nbsp;</label>
                        <div class="col-sm-10">
                            <a href='{{URL::previous()}}' class="btn btn-sm btn-light"><i class="fa fa-chevron-circle-left"></i>
                                Kembali</a>
                            <input type="submit" name="more" value="Simpan &amp; Tambah Lagi" class="btn btn-sm btn-success">
                            <input type="submit" name="add" value="Simpan" class="btn btn-sm btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->
@endsection
