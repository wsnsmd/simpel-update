@extends('frontend.daftar._index')

@section('content-block')
    <div class="block-header block-header-default">
        <h3 class="block-title">3. <small>Konfirmasi</small></h3>
    </div>
    <!-- Form -->
    <form action="{{ route('jadwal.daftar.step3')}}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <input type="hidden" name="step" value="3">
        <div class="block-content block-content-full">
            <h2 class="content-heading pt-0">Data Peserta</h2>
            <div class="form-group">
				@if(session('foto_temp'))
                <label for="foto">Foto</label>
                <div class="js-gallery">
                    <a class="border img-link img-link-zoom-in img-thumb img-lightbox" href="{{ asset(Storage::url(session('foto_temp'))) }}">
                        <img class="img-fluid" style="max-width: 200px; max-height: 150px;" title="Foto" src="{{ asset(Storage::url(session('foto_temp'))) }}">
                        <input type="hidden" name="foto" value="{{ session('foto_temp') }}">
                    </a>
                </div>
				@endif
            </div>
            <div class="form-group form-row">
                <div class="col-6">
                    <label for="nip">NIP</label>
                    <input class="js-maxlength form-control" type="text" id="nip" name="nip" placeholder="NIP tanpa spasi..." minlength="18" maxlength="18" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary" value="{{ session('peserta')['nip'] }}" readonly required>
                </div>
                <div class="col-6">
                    <label for="nip">NIK (No. KTP)</label>
                    <input class="js-maxlength form-control" type="text" id="ktp" name="ktp" placeholder="NIK (No. KTP)..." minlength="16" maxlength="16" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary" value="{{ session('peserta')['ktp'] }}" readonly required>
                </div>                                    
            </div>
            <div class="form-group form-row">
                <div class="col-6">
                <label for="instansi">Nama Lengkap</label>
                    <input class="form-control" type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap..." value="{{ session('peserta')['nama_lengkap'] }}" readonly required>
                </div>
                <div class="col-6">
                        <label for="instansi">Nama Panggilan</label>
                    <input class="form-control" type="text" id="nama_panggil" name="nama_panggil" placeholder="Nama Panggilan..." value="{{ session('peserta')['nama_panggil'] }}" readonly required>
                </div>
            </div>
            <div class="form-group">
                <label for="instansi">Alamat</label>
                <textarea class="form-control" name="alamat" placeholder="Alamat..." readonly required>{{ session('peserta')['alamat'] }}</textarea>
            </div>                                
            <div class="form-group form-row">
                <div class="col-6">
                    <label for="instansi">Tempat Lahir</label>
                    <input class="form-control" type="text" id="tmp_lahir" name="tmp_lahir" placeholder="Tempat Lahir..." value="{{ session('peserta')['tmp_lahir'] }}" readonly required>
                </div>
                <div class="col-6">
                    <label for="instansi">Tanggal Lahir</label>
                    <input class="js-datepicker form-control" type="text" id="tgl_lahir" name="tgl_lahir" placeholder="Tanggal Lahir" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" value="{{ session('peserta')['tgl_lahir'] }}" readonly required>
                </div>
            </div>  
            <div class="form-group form-row">
                <div class="col-6">
                    <label for="instansi">Jenis Kelamin</label>
                    <select class="form-control" id="jk" name="jk" style="width: 100%;" readonly required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ session('peserta')['jk'] == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ session('peserta')['jk'] == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-6">
                    <label for="instansi">Agama</label>
                    <select class="form-control" id="agama" name="agama" style="width: 100%;" readonly required>
                        <option value="">-- Pilih Agama --</option>
                        @foreach ($agama as $a)
                        <option value="{{ $a->id }}" {{ session('peserta')['agama'] == $a->id ? 'selected' : '' }}>{{ $a->nama }}</option>                                    
                        @endforeach
                    </select>
                </div>
            </div>                                
            <div class="form-group form-row">
                <div class="col-6">
                    <label for="instansi">No. Handphone</label>
                    <input class="form-control" type="text" id="hp" name="hp" placeholder="No. Handphone..." value="{{ session('peserta')['hp'] }}" readonly required>
                </div>
                <div class="col-6">
                    <label for="instansi">Email</label>
                    <input class="form-control" type="email" id="email" name="email" placeholder="Email..." value="{{ session('peserta')['email'] }}" readonly required>
                </div>
            </div>              
            <div class="form-group form-row">
                <div class="col-6">
                    <label for="instansi">Status Perkawinan</label>
                    <select class="form-control" id="marital" name="marital" style="width: 100%;" readonly required>
                        <option value="">-- Pilih Status Perkawinan --</option>
                        <option value="1" {{ session('peserta')['marital'] == 1 ? 'selected' : '' }}>Menikah</option>
                        <option value="2" {{ session('peserta')['marital'] == 2 ? 'selected' : '' }}>Belum Menikah</option>
                        <option value="3" {{ session('peserta')['marital'] == 3 ? 'selected' : '' }}>Duda</option>
                        <option value="4" {{ session('peserta')['marital'] == 4 ? 'selected' : '' }}>Janda</option>
                    </select>
                </div>
                <div class="col-6">
                    <label for="instansi">Pangkat</label>
                    <select class="form-control" id="pangkat" name="pangkat" style="width: 100%;" readonly required>
                        <option value="">-- Pilih Pangkat --</option>
                        @foreach ($pangkat as $p)
                        <option value="{{ $p->id }}" {{ session('peserta')['pangkat'] == $p->id ? 'selected' : '' }}>{{ $p->singkat }}</option>                                    
                        @endforeach
                    </select>
                </div>                                    
            </div>                                                                                                             
            <div class="form-group">
                <label for="instansi">Jabatan</label>
                <input class="form-control" type="text" id="jabatan" name="jabatan" placeholder="Jabatan..." value="{{ session('peserta')['jabatan'] }}" readonly required>
            </div>
            <h2 class="content-heading pt-3 mb-3">Data Instansi & Unit Kerja</h2>
            <div class="form-group">
                <label for="instansi">Instansi</label>
                <input class="form-control" type="text" id="instansi" name="instansi" placeholder="Instansi..." value="{{ session('peserta')['instansi'] }}" readonly required>
            </div>
            <div class="form-group">
                <label for="instansi">Satuan Kerja (SKPD/OPD)</label>
                <input class="form-control" type="text" id="satker_nama" name="satker_nama" placeholder="Satuan Kerja..." value="{{ session('peserta')['satker_nama'] }}" readonly required>
            </div>
            <div class="form-group">
                <label for="instansi">Alamat</label>
                <textarea class="form-control" id="satker_alamat" name="satker_alamat" placeholder="Alamat Satuan Kerja..." readonly required>{{ session('peserta')['satker_alamat'] }}</textarea>
            </div>    
            <div class="form-group form-row">
                <div class="col-6">
                <label for="instansi">No. Telepon</label>
                <input class="form-control" type="text" id="satker_telp" name="satker_telp" placeholder="No. Telepon Satuan Kerja" value="{{ session('peserta')['satker_telp'] }}" readonly required>
                </div>
            </div>    
            <div class="form-group mt-4 mb-0">
                {{-- {!! NoCaptcha::display() !!}      --}}
            </div>                                                                                    
        </div>
        <div class="block-content block-content-sm block-content-full bg-body-light rounded-bottom">
            <div class="row">
                <div class="col-12">
                    <a href="{{URL::route('jadwal.daftar.step2')}}" class="btn btn-sm btn-primary"><i class="fa fa-angle-left"></i>
                        Kembali</a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        Daftar Pelatihan
                    </button>                                        
                </div>
            </div>
        </div>
    </form>
    <!-- END Form -->
@endsection    