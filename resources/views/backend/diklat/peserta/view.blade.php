@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/jasny-bootstrap/css/jasny-bootstrap.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(function(){
            Dashmix.helpers(['datepicker', 'maxlength']);
        });

    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Jadwal</li>
                        <li class="breadcrumb-item">Detail</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $jadwal->nama }}</li>
                    </ol>
                </nav>
            </div>
       </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Peserta - Lihat</h3>
            </div>
            <div class="block-content block-content-full border-top">
                    <h2 class="content-heading pt-0">Data Peserta</h2>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="foto">Foto</label>
                        <div class="col-sm-9">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new img-thumbnail" style="width: 200px; height: 150px;">
                                    <img src="{{ is_null($peserta->foto) ? asset('media/avatars/foto.png') : asset(Storage::url($peserta->foto)) }}" alt="Foto">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nip">NIP</label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="js-maxlength form-control{{ $errors->has('nip') ? ' is-invalid' : '' }}" id="nip" name="nip" maxlength="18" placeholder="NIP..." value="{{ $peserta->nip }}" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="ktp">No KTP</label>
                        <div class="col-sm-9">
                            <input type="text" class="js-maxlength form-control{{ $errors->has('ktp') ? ' is-invalid' : '' }}" id="ktp" name="ktp" maxlength="16" placeholder="No KTP..." value="{{ $peserta->ktp }}" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="status_asn">Status ASN</label>
                        <div class="col-sm-9">
                            <select class="custom-select" id="status_asn" name="status_asn" disabled>
                                <option value="">-- Pilih Status ASN --</option>
                                <option value="1" {{ ($peserta->status_asn == 1 ? 'selected' : '') }}>PNS</option>
                                <option value="2" {{ ($peserta->status_asn == 2 ? 'selected' : '') }}>PPPK</option>
                                <option value="0" {{ ($peserta->status_asn == 0 ? 'selected' : '') }}>Non-ASN</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nama_lengkap">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('nama_lengkap') ? ' is-invalid' : '' }}" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap..." value="{{ $peserta->nama_lengkap }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nama_panggil">Nama Panggilan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('nama_panggil') ? ' is-invalid' : '' }}" id="nama_panggil" name="nama_panggil" placeholder="Nama Panggilan..." value="{{ $peserta->nama_panggil }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="alamat">Alamat</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat..." readonly>{{ $peserta->alamat }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="jk">Jenis Kelamin</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="jk" name="jk" style="width: 100%;" disabled>
                                <option value="" selected>-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ ($peserta->jk == 'L' ? 'selected' : '') }}>Laki-laki</option>
                                <option value="P" {{ ($peserta->jk == 'P' ? 'selected' : '') }}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="tmp_lahir">Tempat Lahir</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('tmp_lahir') ? ' is-invalid' : '' }}" id="tmp_lahir" name="tmp_lahir" placeholder="Tempat Lahir..." value="{{ $peserta->tmp_lahir }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="tgl_lahir">Tanggal Lahir</label>
                        <div class="col-sm-9">
                            <input type="text" class="js-datepicker form-control{{ $errors->has('tgl_lahir') ? ' is-invalid' : '' }}" id="tgl_lahir" name="tgl_lahir" value="{{ $peserta->tgl_lahir }}" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="Tanggal Lahir... (yyyy-mm-dd)" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="agama">Agama</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="agama" name="agama" style="width: 100%;" disabled>
                                <option value="" selected>-- Pilih Agama --</option>
                                @foreach ($agama as $a)
                                <option value="{{ $a->id }}" {{ ($a->id == $peserta->agama_id ? 'selected' : '') }}>{{ $a->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="marital">Status Perkawinan</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="marital" name="marital" style="width: 100%;" disabled>
                                <option value="" selected>-- Pilih Status Perkawinan --</option>
                                <option value="1" {{ ($peserta->marital == 1 ? 'selected' : '') }}>Menikah</option>
                                <option value="2" {{ ($peserta->marital == 2 ? 'selected' : '') }}>Belum Menikah</option>
                                <option value="3" {{ ($peserta->marital == 3 ? 'selected' : '') }}>Duda</option>
                                <option value="4" {{ ($peserta->marital == 4 ? 'selected' : '') }}>Janda</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="hp">Handphone</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('hp') ? ' is-invalid' : '' }}" id="hp" name="hp" placeholder="No. Handphone..." value="{{ $peserta->hp }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="email">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" placeholder="Email..." value="{{ $peserta->email }}" readonly>
                            </div>
                        </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="pangkat">Pangkat</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="pangkat" name="pangkat" style="width: 100%;" disabled>
                                <option value="" selected>-- Pilih Pangkat --</option>
                                @foreach ($pangkat as $p)
                                <option value="{{ $p->id }}" {{ ($p->id == $peserta->pangkat_id ? 'selected' : '') }}>{{ $p->singkat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="jabatan">Jabatan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('jabatan') ? ' is-invalid' : '' }}" id="jabatan" name="jabatan" placeholder="Jabatan..." value="{{ $peserta->jabatan }}" readonly>
                        </div>
                    </div>
                    <h2 class="content-heading pt-0">Data Instansi</h2>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="instansi">Instansi</label>
                        <div class="col-sm-9">
                            <input type="text" list="instansi_list" class="form-control{{ $errors->has('instansi') ? ' is-invalid' : '' }}" id="instansi" name="instansi" placeholder="Instansi..." value="{{ $peserta->instansi }}" readonly>
                            <datalist id="instansi_list">
                                @foreach ($instansi as $i)
                                <option value="{{ $i->nama }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="satker_nama">Satuan Kerja (SKPD/OPD) / Partai</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('satker_nama') ? ' is-invalid' : '' }}" id="satker_nama" name="satker_nama" placeholder="Satuan Kerja..." value="{{ $peserta->satker_nama }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="satker_alamat">Alamat</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="satker_alamat" name="satker_alamat" placeholder="Alamat Satuan Kerja..." readonly>{{ $peserta->satker_alamat }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="satker_telp">Telepon</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('satker_telp') ? ' is-invalid' : '' }}" id="satker_telp" name="satker_telp" placeholder="No. Telepon Satuan Kerja..." value="{{ $peserta->satker_telp }}" readonly>
                        </div>
                    </div>
                    <h2 class="content-heading pt-0">Status Peserta</h2>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="verifikasi">Verifikasi</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="verifikasi" name="verifikasi" style="width: 100%;" readonly>
                                <option value="" selected>-- Pilih Verifikasi --</option>
                                <option value="0" {{ ($peserta->verifikasi == '0' ? 'selected' : '') }}>Belum</option>
                                <option value="1" {{ ($peserta->verifikasi == '1' ? 'selected' : '') }}>Sudah</option>
                                <option value="2" {{ ($peserta->verifikasi == '2' ? 'selected' : '') }}>Tolak</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="batal">Batal</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="batal" name="batal" style="width: 100%;" readonly>
                                <option value="" selected>-- Pilih Batal --</option>
                                <option value="0" {{ ($peserta->batal == '0' ? 'selected' : '') }}>Tidak</option>
                                <option value="1" {{ ($peserta->batal == '1' ? 'selected' : '') }}>Ya</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row batal-ket" style="{{ ($peserta->batal ? '' : 'display: none')}}">
                        <label class="col-sm-3 col-form-label text-right" for="batal_ket">Keterangan Batal</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="batal_ket" name="batal_ket" placeholder="Keterangan Batal..." {{ ($peserta->batal ? '' : 'disabled')}} readonly>{{ $peserta->batal_ket }}</textarea>
                        </div>
                    </div>
                    <div class="form-group mt-4 row">
                        <label class="col-sm-3 col-form-label text-right">&nbsp;</label>
                        <div class="col-sm-9">
                            <a href='{{URL::previous()}}' class="btn btn-sm btn-light"><i class="fa fa-chevron-circle-left mr-1"></i>
                                Kembali</a>
                        </div>
                    </div>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->
@endsection
