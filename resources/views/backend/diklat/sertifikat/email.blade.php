@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/summernote/summernote-bs4.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        jQuery(function(){
            Dashmix.helpers(['summernote']);
        });
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Jadwal</li>
                        <li class="breadcrumb-item">Detail</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $jadwal->nama }}</li>
                    </ol>
                </nav>
            </div>
       </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Sertifikat Email Template</h3>
            </div>
            <div class="block-content block-content-full">
            @if(count($errors) > 0 )
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <ul class="p-0 m-0" style="list-style: none;">
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif
                <form action="{{ route('backend.diklat.sertifikat.email.template.simpan', ['jadwal' => $jadwal->id]) }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="alert alert-info alert-dismissable" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h3 class="alert-heading font-size-h4 my-2">Helper</h3>
                        <p class="mb-0"><strong>{nama}</strong> : Nama Peserta</p>
                        <p class="mb-0"><strong>{sertifikat}</strong> : URL/Link Sertifikat</p>
                    </div>
                    <div class="block-content block-content-full">
                        <label for="bcc" class="control-label">BCC Email</label>
                        @if(is_null($email))
                        <input type="email" class="form-control" id="bcc" name="bcc" value="" placeholder="BCC Email">
                        @else
                        <input type="email" class="form-control" id="bcc" name="bcc" value="{!! $email->bcc !!}" placeholder="BCC Email">
                        @endif
                    </div>
                    <div class="block-content block-content-full">
                        <textarea class="js-summernote" name="konten">
                            @if(!is_null($email))
                            {!! $email->konten !!}
                            @endif
                        </textarea>
                    </div>
                    <div class="block-content block-content-sm block-content-full bg-body-light rounded-bottom">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-block btn-primary">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Dynamic Table Full -->


    </div>
    <!-- END Page Content -->
@endsection
