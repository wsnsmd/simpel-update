{{-- penguji/nilai/invalid.blade.php --}}
@extends('layouts.frontend')
@section('content')
<div class="content content-full">
    <div class="row justify-content-center mt-5">
        <div class="col-sm-8 col-md-5 text-center">
            <div class="block block-rounded">
                <div class="block-content py-5">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-danger mb-3"
                         style="width:72px;height:72px">
                        <i class="fa fa-times fa-2x text-white"></i>
                    </div>
                    <h3 class="font-w700 text-danger">Akses Tidak Valid</h3>
                    <p class="text-muted">{{ $pesan }}</p>
                    <p class="text-muted font-size-sm mt-3">
                        Silakan hubungi panitia BPSDM Kalimantan Timur<br>
                        untuk mendapatkan link yang valid.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
