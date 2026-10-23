@extends('frontend.daftar._index')

@section('content-block')
    <div class="block-header block-header-default">
        <h3 class="block-title">2. <small>Registrasi</small></h3>
    </div>
    <!-- Form -->
    <form action="{{ route('jadwal.daftar.step2.simple')}}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <input type="hidden" name="step" value="2">
        <div class="block-content block-content-full">
            @if(count($errors) > 0)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <ul class="px-3 m-0">
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-group form-row">
                <div class="col-12">
                    <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="nama_lengkap" name="nama_lengkap"
                        placeholder="Nama Lengkap..." value="{{ $pegawai['nama_lengkap'] }}" readonly required>
                    <small class="form-text text-muted">Harap diisi tanpa menyertakan gelar (cth: Budi Suharja, bukan Dr.
                        Budi Suharja, SE).</small>
                </div>
            </div>
            <div class="form-group form-row">
                <div class="col-12">
                    <label for="pendidikan">Pendidikan <span class="text-danger">*</span></label>
                    <select class="form-control{{ $errors->has('pendidikan') ? ' is-invalid' : '' }}" id="pendidikan"
                        name="pendidikan" required>
                        <option value="">-- Pilih Pendidikan --</option>
                        <option value="SD / sederajat" {{ old('pendidikan') == 'SD / sederajat' ? 'selected' : '' }}>SD /
                            sederajat
                        </option>
                        <option value="SMP / sederajat" {{ old('pendidikan') == 'SMP / sederajat' ? 'selected' : '' }}>SMP /
                            sederajat
                        </option>
                        <option value="SMA / SMK / sederajat" {{ old('pendidikan') == 'SMA / SMK / sederajat' ? 'selected' : '' }}>SMA
                            / SMK / sederajat</option>
                        <option value="Diploma (D1–D4)" {{ old('pendidikan') == 'Diploma (D1–D4)' ? 'selected' : '' }}>Diploma
                            (D1–D4)
                        </option>
                        <option value="Sarjana (S1)" {{ old('pendidikan') == 'Sarjana (S1)' ? 'selected' : '' }}>Sarjana (S1)
                        </option>
                        <option value="Magister (S2)" {{ old('pendidikan') == 'Magister (S2)' ? 'selected' : '' }}>Magister
                            (S2)
                        </option>
                        <option value="Doktor (S3)" {{ old('pendidikan') == 'Doktor (S3)' ? 'selected' : '' }}>Doktor (S3)
                        </option>
                    </select>

                    @if ($errors->has('pendidikan'))
                        <div class="invalid-feedback">{{ $errors->first('pendidikan') }}</div>
                    @endif
                </div>
            </div>
            <div class="form-group form-row">
                <div class="col-12">
                    <label for="jk">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-control" id="jk" name="jk" style="width: 100%;" readonly required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ $pegawai['jk'] == 'L' ? 'selected' : ''}}>Laki-laki</option>
                        <option value="P" {{ $pegawai['jk'] == 'P' ? 'selected' : ''}}>Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="form-group form-row">
                <div class="col-12">
                    <label for="hp">No. Handphone <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="hp" name="hp" placeholder="No. Handphone..."
                        value="{{ $pegawai['telp'] }}" required>
                </div>
            </div>
            <div class="form-group form-row">
                <div class="col-12">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input class="form-control" type="email" id="email" name="email" placeholder="Email..."
                        value="{{ $pegawai['email'] }}" required>
                    <small class="form-text text-muted">Harap gunakan email aktif untuk verifikasi.</small>
                </div>
            </div>
            <div class="form-group">
                <label for="instansi">Instansi <span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="instansi" name="instansi" placeholder="Instansi..."
                    value="{{ $pegawai['instansi'] }}" readonly required>
            </div>
            <div class="form-group">
                <label for="instansi">Satuan Kerja (SKPD/OPD)</label>
                <input class="form-control" type="text" id="satker_nama" name="satker_nama"
                    value="{{ $pegawai['satker_nama'] }}" placeholder="Satuan Kerja..." readonly required>
            </div>
            <div class="form-group">
                <label for="instansi">Jabatan <span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="jabatan" name="jabatan" placeholder="Jabatan..."
                    value="{{ $pegawai['jabatan'] }}" readonly required>
            </div>
        </div>
        <div class="block-content block-content-sm block-content-full bg-body-light rounded-bottom">
            <div class="row">
                <div class="col-12">
                    <a href="{{URL::route('jadwal.daftar.step1')}}" class="btn btn-sm btn-primary"><i
                            class="fa fa-angle-left"></i>
                        Kembali</a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        Lanjut <i class="fa fa-angle-right ml-1"></i>
                    </button>
                    <button type="reset" class="btn btn-sm btn-light">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </form>
    <!-- END Form -->
@section('js_sub')
@include('frontend.daftar._foto_upload_handler')
@endsection
@endsection