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
    @include('backend.diklat.kurikulum.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Kurikulum - Tambah Data</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <form class="mb-2" action="{{ route('backend.diklat.kurikulum.store') }}" method="POST" autocomplete="off">
                    @csrf                                       
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nama">Nama Kurikulum <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" id="nama" name="nama" placeholder="Nama Kurikulum..." value="{{ old('nama') }}">
                            @if ($errors->has('nama'))
                            <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="j_diklat">Jenis Diklat <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control{{ $errors->has('j_diklat') ? ' is-invalid' : '' }}" id="j_diklat" name="j_diklat">
                                <option value="" selected>-- Pilih Jenis Diklat --</option>
                                @foreach ($jdiklat as $jd)
                                <option value="{{ $jd->id }}" {{ old('j_diklat') == $jd->id ? "selected":"" }}>{{ $jd->nama }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('j_diklat'))
                            <div class="invalid-feedback">{{ $errors->first('j_diklat') }}</div>
                            @endif
                        </div>                     
                    </div>                       
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="j_belajar">Jenis Pembelajaran <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control{{ $errors->has('j_belajar') ? ' is-invalid' : '' }}" id="j_belajar" name="j_belajar">
                                <option value="" selected>-- Pilih Jenis Pembelajaran --</option>
                                <option value="1" {{ old('j_belajar') == 1 ? "selected":"" }}>Klasikal Penuh</option>
                                <option value="2" {{ old('j_belajar') == 2 ? "selected":"" }}>Blended Learning</option>
                                <option value="3" {{ old('j_belajar') == 3 ? "selected":"" }}>E-Learning</option>
                            </select>
                            @if ($errors->has('j_belajar'))
                            <div class="invalid-feedback">{{ $errors->first('j_belajar') }}</div>
                            @endif
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="jp">Total JP <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('jp') ? ' is-invalid' : '' }}" id="jp" name="jp" placeholder="Total JP..." value="{{ old('jp') }}">
                            @if ($errors->has('jp'))
                            <div class="invalid-feedback">{{ $errors->first('jp') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group mt-4 row">
                        <label class="col-sm-3 col-form-label text-right">&nbsp;</label>
                        <div class="col-sm-9">
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
