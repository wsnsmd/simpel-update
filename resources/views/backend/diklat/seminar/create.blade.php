@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->  
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        jQuery(document).ready(function () {
            $('.widyaiswara').select2({
                placeholder: "-- Pilih Widyaiswara --",
                // minimumInputLength: 3,
                ajax: {
                    url: '{{ route("ajax.widyaiswara") }}',
                    dataType: 'json',
                    delay: 50,
                    data: function (params) {                
                        return { search: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id
                                }
                            })
                        };
                    },        
                    cache: true        
                }
            });
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
    @include('backend.diklat.jadwal.detail_hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Kelompok Seminar - {{ $edit ? 'Edit' : 'Tambah' }} Data</h3>
            </div>
            <div class="block-content block-content-full border-top">
                @if($edit)
                <form class="mb-2" action="{{ route('backend.diklat.seminar.kelompok.update', ['jadwal'=> $jadwal->id, 'id' => $seminar->id]) }}" method="POST" autocomplete="off">
                    @method('PATCH') 
                @else
                <form class="mb-2" action="{{ route('backend.diklat.seminar.kelompok.store', ['jadwal' => $jadwal->id]) }}" method="POST" autocomplete="off">
                @endif
                    @csrf                                       
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="kelompok">Nama Kelompok <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('kelompok') ? ' is-invalid' : '' }}" id="kelompok" name="kelompok" placeholder="Nama Kelompok..." value="{{ $edit ? $seminar->kelompok : old('kelompok') }}">
                            @if ($errors->has('kelompok'))
                            <div class="invalid-feedback">{{ $errors->first('kelompok') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="cid">Coach <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control widyaiswara" id="cid" name="cid" style="width: 100%;">
                                @if($edit)
                                <option value="{{$seminar->cid}}" selected="selected">{{$seminar->coach}}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="pid">Penguji <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control widyaiswara" id="pid" name="pid" style="width: 100%;">
                                @if($edit)
                                <option value="{{$seminar->pid}}" selected="selected">{{$seminar->penguji}}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-4 row">
                        <label class="col-sm-3 col-form-label text-right">&nbsp;</label>
                        <div class="col-sm-9">
                            <a href='{{URL::previous()}}' class="btn btn-sm btn-light"><i class="fa fa-chevron-circle-left"></i>
                                Kembali</a>
                            @if($edit)
                            <input type="submit" name="update" value="Simpan" class="btn btn-sm btn-success">
                            @else
                            <input type="submit" name="more" value="Simpan &amp; Tambah Lagi" class="btn btn-sm btn-success">
                            <input type="submit" name="add" value="Simpan" class="btn btn-sm btn-success">
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->
@endsection
