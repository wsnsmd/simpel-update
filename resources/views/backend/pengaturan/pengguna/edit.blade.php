@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->  
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/jasny-bootstrap/css/jasny-bootstrap.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js') }}"></script>

    <script>
        jQuery(function(){ 
            Dashmix.helpers(['magnific-popup', 'select2']); 
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
    @include('backend.pengaturan.pengguna.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Pengguna - Edit Data</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <form class="mb-2" action="{{ route('backend.pengguna.update', $user->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    @method('PATCH')
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="foto">Foto</label>
                        <div class="col-sm-10">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new img-thumbnail" style="width: 200px; height: 150px;">
                                    <img src="{{ is_null($user->photo) ? asset('media/avatars/foto.png') : asset(Storage::url($user->photo)) }}" alt="Foto">
                                    <input type="hidden" name="foto_lama" value="{{ $user->photo }}">
                                </div>
                                <div class="fileinput-preview fileinput-exists img-thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                    <div>
                                        <span class="btn btn-sm btn-outline-secondary btn-file"><span class="fileinput-new">Pilih Foto</span><span class="fileinput-exists">Ganti</span><input type="file" accept="image/x-png,image/gif,image/jpeg" id="foto" name="foto"></span>
                                        <a href="#" class="btn btn-sm btn-outline-secondary fileinput-exists" data-dismiss="fileinput">Hapus</a>
                                    </div>
                                <small class="form-text text-muted">Hanya file (*.jpg, *.png, *.gif) dengan ukuran < 512 KB</small>
                                @if ($errors->has('foto'))
                                <div class="invalid-feedback d-block">{{ $errors->first('foto') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>                                               
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="nama">Nama <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" id="nama" name="nama" placeholder="Nama..." value="{{ $user->name }}" required>
                            @if ($errors->has('nama'))
                            <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                            @endif
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="email">Email <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" placeholder="Email..." value="{{ $user->email }}" required>
                            @if ($errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                    </div>                
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="username">Username <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" id="username" name="username" placeholder="Username..." value="{{ $user->username }}" required>
                            @if ($errors->has('username'))
                            <div class="invalid-feedback">{{ $errors->first('username') }}</div>
                            @endif
                        </div>
                    </div>       
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="password">Password</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" name="password" placeholder="Password..." value="">
                            <small class="form-text text-muted">
                                Kosongkan jika tidak ingin mengubah password.
                            </small>
                            @if ($errors->has('password'))
                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                            @endif
                        </div>
                    </div>                                  
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="group">Group <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="js-select2-min form-control{{ $errors->has('group') ? ' is-invalid' : '' }}" id="group" name="group" style="width: 100%" required>
                                <option value="">-- Pilih group --</option>
                                <option value="pkmf" {{ $user->usergroup == 'pkmf' ? 'selected' : ''}}>PKMF</option>
                                <option value="pkt" {{ $user->usergroup == 'pkt' ? 'selected' : ''}}>PKT</option>
                                <option value="skpk" {{ $user->usergroup == 'skpk' ? 'selected' : ''}}>SKPK</option>
                                <option value="kontribusi" {{ $user->usergroup == 'kontribusi' ? 'selected' : ''}}>Kontribusi</option>
                            </select>
                            @if ($errors->has('group'))
                            <div class="invalid-feedback">{{ $errors->first('group') }}</div>
                            @endif
                        </div>                     
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="instansi">Instansi <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="js-select2 form-control{{ $errors->has('instansi') ? ' is-invalid' : '' }}" id="instansi" name="instansi" style="width: 100%" required>
                                <option value="" selected>-- Pilih Instansi --</option>
                                @foreach ($instansi as $i)
                                <option value="{{ $i->id }}" {{ $user->instansi_id == $i->id ? "selected":"" }}>{{ $i->nama }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('instansi'))
                            <div class="invalid-feedback">{{ $errors->first('instansi') }}</div>
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
