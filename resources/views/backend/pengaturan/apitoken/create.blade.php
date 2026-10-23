@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/jasny-bootstrap/css/jasny-bootstrap.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js') }}"></script>

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
    @include('backend.pengaturan.apitoken.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">API Token - Tambah Data</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <form class="mb-2" action="{{ route('backend.apitoken.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="nama">Nama Aplikasi <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" id="nama" name="nama" placeholder="Nama Aplikasi..." value="{{ old('nama') }}" required>
                            @if ($errors->has('nama'))
                            <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="ip_address">IP Address</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('ip_address') ? ' is-invalid' : '' }}" id="ip_address" name="ip_address" placeholder="IP Address..." value="{{ old('ip_address') }}">
                            @if ($errors->has('ip_address'))
                            <div class="invalid-feedback">{{ $errors->first('ip_address') }}</div>
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
