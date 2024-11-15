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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(function(){ 
            Dashmix.helpers(['select2']); 
        });   

        $('#form-import').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), 
                success: function(data)
                {
                    $('#div-data').html(data);
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
                <h3 class="block-title">Peserta - Import</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <form id="form-import" action="{{ route('backend.diklat.peserta.import.preview', ['jadwal' => $jadwal->id]) }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="form-row">
                        <div class="col-8">
                            <label for="jadwal_from">Dari Pelatihan</label>
                            <select class="js-select2 form-control" id="jadwal_from" name="jadwal_from" style="width: 100%;" required>
                                <option value="">-- Pilih Jadwal --</option>
                                @foreach ($from as $f)
                                <option value="{{ $f->id }}">{{ $f->nama }}</option>    
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-block btn-primary" value="preview" style="min-width: 120px; max-height: 38px"><i class="fa fa-search mr-1"></i> Pratinjau</button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="div-data" class="block-content block-content-full">
            </div>
        </div>
        <!-- END Dynamic Table Full -->
        
        
    </div>
    <!-- END Page Content -->
@endsection
