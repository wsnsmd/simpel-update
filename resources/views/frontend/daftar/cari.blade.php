@extends('frontend.daftar._index')

@section('content-block')
    <div class="block-header block-header-default">
        <h3 class="block-title font-size-sm">Ambil Link Konfirmasi Kehadiran</h3>
    </div>
    <div class="block-content block-content-full">
        <form action="{{ route('jadwal.cek_status.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="font-size-sm">Pilih Kegiatan/Webinar</label>
                <select name="diklat_jadwal_id" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Jadwal --</option>
                    @foreach($jadwals as $j)
                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="font-size-sm">Alamat Email</label>
                <input type="email" name="email" class="form-control form-control-sm"
                    placeholder="Masukkan email saat mendaftar" required>
                <small class="text-muted font-size-xs italic">*Gunakan email yang sama saat Anda melakukan
                    registrasi.</small>
            </div>

            {{-- Bagian CAPTCHA --}}
            <div class="form-group">
                <label class="font-size-sm d-block">Kode Keamanan</label>
                <div class="captcha-container mb-2 d-flex align-items-center">
                    <span class="captcha-image">{!! captcha_img('flat') !!}</span>
                    <button type="button" class="btn btn-sm btn-light ml-2" id="reload">
                        <i class="fa fa-sync-alt"></i>
                    </button>
                </div>
                <input type="text" name="captcha"
                    class="form-control form-control-sm @error('captcha') is-invalid @enderror"
                    placeholder="Masukkan kode di atas" required>
                @error('captcha')
                    <div class="invalid-feedback font-size-xs">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-info border-0 font-size-sm py-2">
                <i class="fa fa-info-circle mr-1"></i>
                Sistem akan menampilkan link absensi Anda jika data ditemukan.
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-sm btn-primary btn-block shadow-sm">
                    <i class="fa fa-search mr-1"></i> Dapatkan Link Absensi
                </button>
                <a href="{{ url('/') }}" class="btn btn-sm btn-light btn-block">Kembali</a>
            </div>
        </form>
    </div>

    @section('js_sub')
        <script>
            $("#reload").click(function () {
                $.ajax({
                    type: "GET",
                    url: "{{ route('reload.captcha') }}",
                    success: function (data) {
                        $(".captcha-image").html(data.captcha);
                    }
                });
            });
        </script>
    @endsection
@endsection