@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->    
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>

    <script>
        jQuery(function(){ 
            Dashmix.helpers(['datepicker', 'maxlength', 'select2']); 
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
                <h3 class="block-title">Fasilitator - Edit Data</h3>
            </div>
            <div class="block-content block-content-full">
                <form class="mb-2" action="{{ route('backend.diklat.fasilitator.update', $fasilitator->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="foto">Foto</label>
                        <div class="col-sm-10">
                            <p>
                                <a class="" href="{{ is_null($fasilitator->foto) ? asset('media/avatars/avatar8.jpg') : asset(Storage::url($fasilitator->foto)) }}" target="_blank">
                                    <img class="img-fluid" style="max-width:160px" title="Foto" src="{{ is_null($fasilitator->foto) ? asset('media/avatars/avatar8.jpg') : asset(Storage::url($fasilitator->foto)) }}">
                                    <input type="hidden" name="foto_lama" value="{{ $fasilitator->foto }}">
                                </a>
                            </p>
                            <p>
                                <input id="foto" name="foto" type="file" autofocus>
                                <small class="form-text text-muted">Hanya file (*.jpg, *.png, *.gif) dengan ukuran < 512 KB</small>
                                @if ($errors->has('foto'))
                                <div class="invalid-feedback d-block">{{ $errors->first('foto') }}</div>
                                @endif
                            </p>
                        </div>
                    </div>                            
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="nip">NIP</label>
                        <div class="col-sm-10">
                            <input type="text" class="js-maxlength form-control{{ $errors->has('nip') ? ' is-invalid' : '' }}" id="nip" name="nip" maxlength="18" placeholder="NIP..." value="{{ $fasilitator->nip }}" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary">
                            @if ($errors->has('nip'))
                            <div class="invalid-feedback">{{ $errors->first('nip') }}</div>
                            @endif
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="nama">Nama <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" id="nama" name="nama" placeholder="Nama..." value="{{ $fasilitator->nama }}">
                            @if ($errors->has('nama'))
                            <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="pangkat">Pangkat / TMT</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="pangkat" name="pangkat" style="width: 100%;" data-placeholder="-- Pilih Pangkat --">
                                <option selected>-- Pilih Pangkat --</option>
                                @foreach ($pangkat as $p)
                                <option value="{{ $p->id }}" {{ ($p->id == $fasilitator->pangkat_id ? 'selected' : '') }}>{{ $p->singkat }}</option>                                    
                                @endforeach
                            </select>
                            @if ($errors->has('pangkat'))
                            <div class="invalid-feedback">{{ $errors->first('pangkat') }}</div>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="js-datepicker form-control{{ $errors->has('tmt_pangkat') ? ' is-invalid' : '' }}" id="tmt_pangkat" name="tmt_pangkat" value="{{ $fasilitator->tmt_pangkat }}" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd">
                            @if ($errors->has('tmt_pangkat'))
                            <div class="invalid-feedback">{{ $errors->first('tmt_pangkat') }}</div>
                            @endif
                        </div>                        
                    </div>                       
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="jabatan">Jabatan / TMT <span class="text-danger">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control{{ $errors->has('jabatan') ? ' is-invalid' : '' }}" id="jabatan" name="jabatan" placeholder="Jabatan..." value="{{ $fasilitator->jabatan }}" autofocus>
                            @if ($errors->has('jabatan'))
                            <div class="invalid-feedback">{{ $errors->first('jabatan') }}</div>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="js-datepicker form-control{{ $errors->has('tmt_jabatan') ? ' is-invalid' : '' }}" id="tmt_jabatan" name="tmt_jabatan" value="{{ $fasilitator->tmt_jabatan }}" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd">
                            @if ($errors->has('pangkat'))
                            <div class="invalid-feedback">{{ $errors->first('pangkat') }}</div>
                            @endif
                        </div>     
                    </div>                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="instansi">Instansi</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('instansi') ? ' is-invalid' : '' }}" id="instansi" name="instansi" placeholder="Instansi..." value="{{ $fasilitator->instansi }}">
                            @if ($errors->has('instansi'))
                            <div class="invalid-feedback">{{ $errors->first('instansi') }}</div>
                            @endif
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="satker_nama">Satuan Kerja (SKPD/OPD)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('satker_nama') ? ' is-invalid' : '' }}" id="satker_nama" name="satker_nama" placeholder="Satuan Kerja..." value="{{ $fasilitator->satker_nama }}">
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
