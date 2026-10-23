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
    @include('backend.master.pangkat.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Pangkat - Edit Data</h3>
            </div>
            <div class="block-content block-content-full">
                <form class="mb-2" action="{{ route('backend.master.pangkat.update', $pangkat->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="id">ID <span class="text-danger">*</span></label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control{{ $errors->has('id') ? ' is-invalid' : '' }}" id="id" name="id" placeholder="ID..." value="{{ $pangkat->id }}">
                            @if ($errors->has('id'))
                            <div class="invalid-feedback">{{ $errors->first('id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="pangkat">Pangkat <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('pangkat') ? ' is-invalid' : '' }}" id="pangkat" name="pangkat" placeholder="Pangkat..." value="{{ $pangkat->pangkat }}">
                            @if ($errors->has('pangkat'))
                            <div class="invalid-feedback">{{ $errors->first('pangkat') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="golongan">Golongan <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('golongan') ? ' is-invalid' : '' }}" id="golongan" name="golongan" placeholder="Golongan..." value="{{ $pangkat->golongan }}">
                            @if ($errors->has('golongan'))
                            <div class="invalid-feedback">{{ $errors->first('golongan') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right" for="singkat">Singkat <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control{{ $errors->has('singkat') ? ' is-invalid' : '' }}" id="singkat" name="singkat" placeholder="Pangkat..." value="{{ $pangkat->singkat }}">
                            @if ($errors->has('singkat'))
                            <div class="invalid-feedback">{{ $errors->first('singkat') }}</div>
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
