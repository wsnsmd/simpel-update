@extends('layouts.frontend')

@section('content')
    <!-- Hero -->
    <div class="bg-body-light border-top border-bottom">
        <div class="content content-full py-1">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-sm text-uppercase font-w700 mt-2 mb-0 mb-sm-2">
                    <i class="fa fa-angle-right fa-fw text-primary"></i> Informasi
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3 font-size-sm" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><i class="fa fa-home"></i></li>
                        <li class="breadcrumb-item active" aria-current="page">Informasi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <div class="bg-white">
        <div class="content">
            <div class="row invisible" data-toggle="appear">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15958.497562991774!2d117.1088263!3d-0.5649173!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x8427efc18bd6a486!2sBadan+Pendidikan+Dan+Pelatihan!5e0!3m2!1sid!2sid!4v1542172729899"
                width="100%" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
            <div class="row items-push font-size-sm py-4 invisible" data-toggle="appear">
                <div class="col-md-12">
                    <h3 class="h5 font-w700 border-bottom pb-2">Jam Kerja</h3>
                    <div>
                        <span class="font-w700">Senin - Kamis</span> <br>
                        07.30 - 16.30 WITA (12.00 - 13.00 Istirahat) <br>
                        <span class="font-w700">Jumat</span> <br>
                        07.30 - 12.00 WITA <br>
                        <span class="font-w700">Libur</span> <br>
                        Sabtu, Minggu dan hari libur Nasional
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
