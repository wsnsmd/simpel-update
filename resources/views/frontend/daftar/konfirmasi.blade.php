@extends('frontend.daftar._index')

@section('content-block')
    <div class="block-header block-header-default">
        <h3 class="block-title">5. <small>Selesai</small></h3>
    </div>
    <div class="block-content block-content-full">
        <p>
            Hai <strong>{{ $peserta->nama_lengkap }}</strong>,
        </p>
        <p>
            Konfirmasi email berhasil, selanjutnya data peserta akan di verifikasi oleh <strong>BPSDM Prov. Kaltim</strong><br><br>

            Terima Kasih.
        </p>                                                            
    </div>
@endsection    