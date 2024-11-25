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
                    <label class="col-sm-3 col-form-label text-right" for="jabatan">Jabatan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control{{ $errors->has('jabatan') ? ' is-invalid' : '' }}" id="jabatan" name="jabatan" placeholder="Jabatan..." value="{{ $peserta->jabatan }}" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right" for="sebagai">Sebagai</label>
                    <div class="col-sm-9">
                        <select class="custom-select" id="sebagai" name="sebagai" disabled>
                            <option value="">-- Pilih Sebagai --</option>
                            <option value="Moderator" {{ ($peserta->sebagai == 'Moderator' ? 'selected' : '') }}>Moderator</option>
                            <option value="Narasumber" {{ ($peserta->sebagai == 'Narasumber' ? 'selected' : '') }}>Narasumber</option>
                            <option value="Panitia" {{ ($peserta->sebagai == 'Panitia' ? 'selected' : '') }}>Panitia</option>
                            <option value="Peserta" {{ ($peserta->sebagai == 'Peserta' ? 'selected' : '') }}>Peserta</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mt-4 row">
                    <label class="col-sm-3 col-form-label text-right">&nbsp;</label>
                    <div class="col-sm-9">
                        <a href='{{URL::previous()}}' class="btn btn-sm btn-light"><i class="fa fa-chevron-circle-left"></i>
                            Kembali</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->
@endsection
