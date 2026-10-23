@extends('frontend.daftar._index')

@section('content-block')
    <div class="block-header block-header-default">
        <h3 class="block-title">4. <small>Konfirmasi Email</small></h3>
    </div>
    <div class="block-content block-content-full">
        <p>
            Hai <strong>{{ $peserta->nama_lengkap }}</strong>,
        </p>
        <p>
            Registrasi pelatihan tinggal selangkah lagi, silahkan cek email anda (<strong>{{ $peserta->email }}</strong>) 
            dan lakukan konfirmasi untuk menyelesaikan proses registrasi pelatihan. 
        </p>
        @if(!empty($jadwal->var_1))
        <p class="my-3">
            Pelatihan ini memiliki group Telegram/WhatsApp untuk berbagi serta mendapatkan informasi mengenai pelatihan ini, klik/salin tombol berikut untuk bergabung:
            <a href="{{ $jadwal->var_1 }}" class="btn btn-sm btn-primary rounded-0" target="_blank"><i class="far far fa-paper-plane me-1"></i> Gabung</a>
        </p>
        @endif
        <p>
            Terima Kasih.
        </p>
    </div>
@endsection    