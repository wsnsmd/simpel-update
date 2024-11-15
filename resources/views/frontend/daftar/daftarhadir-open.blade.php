@extends('frontend.daftar._index')

@section('content-block')
    <div class="block-header block-header-default">
        <h3 class="block-title">4. <small>Daftar Hadir</small></h3>
    </div>
    <div class="block-content block-content-full">
        <p>
            Hai <strong>{{ $peserta->nama_lengkap }}</strong>,
        </p>
        <p>
            Terima kasih telah mengikuti kegiatan ini. <br />
            Sertifikat dan materi akan kami kirimkan ke email anda (<strong>{{ $peserta->email }}</strong>)
        </p>
        <p>
            BPSDM Prov. Kaltim
        </p>
    </div>
@endsection
