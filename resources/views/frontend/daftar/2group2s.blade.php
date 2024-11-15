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
                    <input class="form-control" type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap..." value="{{ old('nama_lengkap') }}" required>
                </div>
            </div>
            <div class="form-group form-row">
                <div class="col-12">
                    <label for="jk">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-control" id="jk" name="jk" style="width: 100%;" required>
                        <option value="" {{ old('jk') == '' ? "selected" : "" }}>-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ old('jk') == 'L' ? "selected" : "" }}>Laki-laki</option>
                        <option value="P" {{ old('jk') == 'P' ? "selected" : "" }}>Perempuan</option>
                    </select>
                </div>
            </div>                                
            <div class="form-group form-row">
                <div class="col-12">
                    <label for="hp">No. Handphone <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="hp" name="hp" placeholder="No. Handphone..." value="{{ old('hp') }}" required>
                </div>
            </div>
            <div class="form-group form-row">
                <div class="col-12">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input class="form-control" type="email" id="email" name="email" placeholder="Email..." value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="form-group">
                <label for="instansi">Instansi <span class="text-danger">*</span></label>
                <select class="form-control" id="instansi" name="instansi" style="width: 100%;" required>
                    <option value="" {{ old('instansi') == '' ? "selected" : "" }}>-- Pilih Instansi --</option>
                    @foreach ($instansi as $i)
                    <option value="{{ $i->nama }}" {{ old('instansi') == $i->nama ? "selected" : "" }}>{{ $i->nama }}</option>                                    
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="instansi">Jabatan <span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="jabatan" name="jabatan" placeholder="Jabatan..." value="{{ old('jabatan') }}" required>
            </div>
        </div>
        <div class="block-content block-content-sm block-content-full bg-body-light rounded-bottom">
            <div class="row">
                <div class="col-12">
                    <a href="{{URL::route('jadwal.daftar.step1')}}" class="btn btn-sm btn-primary"><i class="fa fa-angle-left"></i>
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
@endsection    