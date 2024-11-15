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
            $('#nip').keypress(function(event){	
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if(keycode == '13'){
                    cariNIP();
                }
                event.stopPropagation();
            });
        });   

        @if (session('success'))
        $.notify({
            icon: "fa fa-check mr-1",
            message: "{{ session('success') }}"
        }, {
            allow_dismiss: false,
            type: 'success',
            placement: {
                from: "top",
                align: "center"
            }
        });
        @elseif (session('error'))
        $.notify({
            icon: "fa fa-times mr-1",
            message: "{{ session('error') }}"
        }, {
            allow_dismiss: false,
            type: 'danger',
            placement: {
                from: "top",
                align: "center"
            }
        });
        @endif

        function cariNIP() {
            var nip = $('#nip').val();
            console.log(nip);
            if (nip != '' && nip.length == 18) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('ajax.caripegawai') }}',
                    data: {
                        nip: nip,
                    },
                    success: function(data) {
                        $('#nama_lengkap').val(data.nama_lengkap);
                        $('#status_asn').val(1);
                        $('#status_asn').trigger('change');
                        $('#hp').val(data.telp);
                        $('#email').val(data.email);
                        $('#jk').val(data.jk);
                        $('#jk').trigger('change');
                        $('#jabatan').val(data.jabatan);
                        $('#instansi').val(data.instansi);
                        $('#instansi').trigger('change');
                        $('#satker_nama').val(data.satker_nama);				
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        var err = JSON.parse(xhr.responseText);
                        var e = Swal.mixin({
                            buttonsStyling: !1,
                            customClass: {
                                confirmButton: "btn btn-danger m-1",
                                cancelButton: "btn btn-danger m-1",
                                input: "form-control"
                            }
                        });
                        e.fire({
                            type: 'error',
                            title: err.status,
                            text: err.message,
                        });
                        $('#form-peserta').trigger('reset');
                    }
                });
            }          
        }
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
                <h3 class="block-title">Peserta - Tambah</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <form id="form-peserta" class="mb-2" action="{{ route('backend.diklat.peserta.store.simple', ['id' => $jadwal->id ]) }}" method="POST" autocomplete="off">
                    @csrf
                    <h2 class="content-heading pt-0">Data Peserta</h2>                           
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nip">NIP</label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="js-maxlength form-control{{ $errors->has('nip') ? ' is-invalid' : '' }}" id="nip" name="nip" maxlength="18" placeholder="NIP..." value="{{ old('nip') }}" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" onclick="cariNIP()">
                                            <i class="fa fa-search mr-1"></i> 
                                        </button>
                                    </div>
                                </div>
                            </div>                            
                            @if ($errors->has('nip'))
                            <div class="invalid-feedback">{{ $errors->first('nip') }}</div>
                            @endif
                        </div>
                    </div>             
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="status_asn">Status ASN <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="custom-select" id="status_asn" name="status_asn" required>
                                <option value="">-- Pilih Status ASN --</option>
                                <option value="1">PNS</option>
                                <option value="2">PPPK</option>
                                <option value="0">Non-ASN</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('nama_lengkap') ? ' is-invalid' : '' }}" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap..." value="{{ old('nama_lengkap') }}" required>
                            @if ($errors->has('nama_lengkap'))
                            <div class="invalid-feedback">{{ $errors->first('nama_lengkap') }}</div>
                            @endif
                        </div>
                    </div>                
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="jk">Jenis Kelamin <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control" id="jk" name="jk" style="width: 100%;" required>
                                <option value="" selected>-- Pilih Jenis Kelamin --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @if ($errors->has('jk'))
                            <div class="invalid-feedback">{{ $errors->first('jk') }}</div>
                            @endif
                        </div>                        
                    </div>              
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="hp">Handphone <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('hp') ? ' is-invalid' : '' }}" id="hp" name="hp" placeholder="No. Handphone..." value="{{ old('hp') }}" required>
                            @if ($errors->has('hp'))
                            <div class="invalid-feedback">{{ $errors->first('hp') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="email">Email <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" placeholder="Email..." value="{{ old('email') }}" required>
                            @if ($errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                    </div>                                                                                                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="instansi">Instansi <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" list="instansi_list" class="form-control{{ $errors->has('instansi') ? ' is-invalid' : '' }}" id="instansi" name="instansi" placeholder="Instansi..." value="{{ old('instansi') }}" required>
                            <datalist id="instansi_list">
                                @foreach ($instansi as $i)
                                <option value="{{ $i->nama }}">
                                @endforeach
                            </datalist>                            
                            @if ($errors->has('instansi'))
                            <div class="invalid-feedback">{{ $errors->first('instansi') }}</div>
                            @endif
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="jabatan">Jabatan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('jabatan') ? ' is-invalid' : '' }}" id="jabatan" name="jabatan" placeholder="Jabatan..." value="{{ old('jabatan') }}" required>
                            @if ($errors->has('jabatan'))
                            <div class="invalid-feedback">{{ $errors->first('jabatan') }}</div>
                            @endif
                        </div>  
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="sebagai">Sebagai <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="custom-select" id="sebagai" name="sebagai" required>
                                <option value="">-- Pilih Sebagai --</option>
                                <option value="Moderator">Moderator</option>
                                <option value="Narasumber">Narasumber</option>
                                <option value="Panitia">Panitia</option>
                                <option value="Peserta">Peserta</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-4 row">
                        <label class="col-sm-3 col-form-label text-right">&nbsp;</label>
                        <div class="col-sm-9">
                            <a href='{{URL::previous()}}' class="btn btn-sm btn-light"><i class="fa fa-chevron-circle-left"></i>
                            {{-- <a href="{{route('backend.diklat.peserta.back', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama)])}}" class="btn btn-sm btn-light"><i class="fa fa-chevron-circle-left"></i> --}}
                                Kembali</a>
                            <input type="submit" name="more" value="Simpan &amp; Tambah Lagi" class="btn btn-sm btn-success">
                            <input type="submit" name="add" value="Simpan" class="btn btn-sm btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->
@endsection
