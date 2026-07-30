@extends('layouts.frontend')

@section('content')
<div class="content content-full">
    <div class="row justify-content-center mt-5">
        <div class="col-sm-10 col-md-6 col-lg-4 text-center">
            <div class="block block-rounded">
                <div class="block-content block-content-full py-5">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-danger mb-3"
                         style="width:80px;height:80px">
                        <i class="fa fa-times fa-2x text-white"></i>
                    </div>
                    <h3 class="font-w700 text-danger">Link Tidak Valid</h3>
                    <p class="text-muted">{{ $pesan }}</p>

                    @if (isset($sesi))
                        <div class="bg-body-light rounded p-3 mb-4 text-left font-size-sm">
                            <div class="font-w600">{{ $sesi->nama_materi }}</div>
                            <div class="text-muted mt-1">
                                {{ \Carbon\Carbon::parse($sesi->tanggal)->format('d M Y') }}
                                {{ substr($sesi->jam_mulai,0,5) }}–{{ substr($sesi->jam_selesai,0,5) }}
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('peserta.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-home mr-1"></i> Ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
