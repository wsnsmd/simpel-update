@extends('layouts.backend')

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/fullcalendar/fullcalendar.min.css') }}">
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/fullcalendar/locale/id.js') }}"></script>
    <script>
        $('#cal-bulan').fullCalendar({
            themeSystem: "bootstrap4",
            firstDay: 1,
            editable: false,
            locale: "id",
            weekNumbers: true,
            // eventLimit: true,
            events: "{{ route('ajax.kalendar') }}",
        });
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">Beranda</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">&nbsp;</li>
                    </ol>
                </nav>
            </div>
       </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="block block-rounded block-bordered">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Kalendar Kegiatan</h3>
                    </div>
                    <div class="block-content">
                        <!-- Calendar Container -->
                        <div id="cal-bulan" class="p-xl-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection
