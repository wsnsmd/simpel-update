@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->    
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
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
    @include('backend.user.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Profil</h3>
            </div>
            <div class="block-content block-content-full">
                <form class="mb-2" action="{{ route('backend.user.profil.update') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="foto">Foto</label>
                        <div class="col-sm-10">
                            <p>
                                <a class="" href="{{ is_null($user->photo) ? asset('media/avatars/avatar8.jpg') : asset(Storage::url($user->photo)) }}" target="_blank">
                                    <img class="img-fluid" style="max-width:160px" title="Foto" src="{{ is_null($user->photo) ? asset('media/avatars/avatar8.jpg') : asset(Storage::url($user->photo)) }}">
                                    <input type="hidden" name="foto_lama" value="{{ $user->photo }}">
                                </a>
                            </p>
                            <p>
                                <input id="foto" name="foto" type="file" accept="image/x-png,image/gif,image/jpeg" autofocus>
                                <small class="form-text text-muted">Hanya file (*.jpg, *.png, *.gif) dengan ukuran < 512 KB</small>
                                @if ($errors->has('foto'))
                                <div class="invalid-feedback d-block">{{ $errors->first('foto') }}</div>
                                @endif
                            </p>
                        </div>
                    </div>                       
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="nama">Nama <span class="text-danger">*</span></label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" id="nama" name="nama" placeholder="Nama..." value="{{ $user->name }}">
                            @if ($errors->has('nama'))
                            <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="email">Email <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" placeholder="Email..." value="{{ $user->email }}">
                            @if ($errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="password_lama">Password Lama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('password_lama') ? ' is-invalid' : '' }}" id="password_lama" name="password_lama" placeholder="Password Lama...">
                            @if ($errors->has('password_lama'))
                            <div class="invalid-feedback">{{ $errors->first('password_lama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="password_baru">Password Baru</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('password_baru') ? ' is-invalid' : '' }}" id="password_baru" name="password_baru" placeholder="Password Baru...">
                            @if ($errors->has('password_baru'))
                            <div class="invalid-feedback">{{ $errors->first('password_baru') }}</div>
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
