<div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
    <div class="bg-white-90">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h3 font-w400 mt-2 mb-0 mb-sm-2">{{$detail}}</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Jadwal</li>
                        <li class="breadcrumb-item">Detail</li>
                        <li class="breadcrumb-item active" aria-current="page">{{$jadwal->nama}}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>