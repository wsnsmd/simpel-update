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
            Daftar hadir pada kegiatan ini sedang kami tutup. <br />
            Tunggu pada saat kegiatan berlangsung dan informasi daftar hadir dibuka oleh Admin.
        </p>
        <p>
            Terima Kasih.
        </p>
    </div>
@endsection
