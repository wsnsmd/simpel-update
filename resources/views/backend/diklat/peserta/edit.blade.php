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
            $( '#batal' ).change(function() {
                var batal = $( '#batal' ).val();
                if(batal == '1') {
                    $('.batal-ket').show();
                    $('#batal_ket').prop("disabled", false);
                }
                else {
                    $('.batal-ket').hide();
                    $('#batal_ket').prop("disabled", true);
                }
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
                        // console.log(data);
                        $('#ktp').val(data.nik);
                        $('#nama_lengkap').val(data.nama_lengkap);
                        $('#nama_panggil').val(data.nama);
                        $('#hp').val(data.telp);
                        $('#email').val(data.email);
                        $('#tmp_lahir').val(data.tmp_lahir);
                        $('#tgl_lahir').val(data.tgl_lahir);
                        $('#jk').val(data.jk);
                        $('#jk').trigger('change');
                        $('#agama').val(data.agama);
                        $('#agama').trigger('change');
                        $('#marital').val(data.marital);
                        $('#marital').trigger('change');
                        $('#alamat').val(data.alamat);
                        $('#jabatan').val(data.jabatan);
                        $('#pangkat').val(data.pangkat);
                        $('#pangkat').trigger('change');
                        $('#instansi').val(data.instansi);
                        $('#instansi').trigger('change');
                        $('#satker_nama').val(data.satker_nama);
                        $('#satker_telp').val(data.satker_telp);
                        $('#satker_alamat').val(data.satker_alamat);
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
                        })
                        $('#nip').val('{{ $peserta->nip }}');
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
                <h3 class="block-title">Peserta - Edit</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <form class="mb-2" action="{{ route('backend.diklat.peserta.store', ['id' => $peserta->id ]) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    @method('PATCH')
                    <h2 class="content-heading pt-0">Data Peserta</h2>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="foto">Foto</label>
                        <div class="col-sm-9">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new img-thumbnail" style="width: 200px; height: 150px;">
                                    <img src="{{ is_null($peserta->foto) ? asset('media/avatars/foto.png') : asset(Storage::url($peserta->foto)) }}" alt="Foto">
                                    <input type="hidden" name="foto_lama" value="{{ $peserta->foto }}">
                                </div>
                                <div class="fileinput-preview fileinput-exists img-thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                    <div>
                                        <span class="btn btn-sm btn-outline-secondary btn-file"><span class="fileinput-new">Pilih Foto</span><span class="fileinput-exists">Ganti</span><input type="file" accept="image/x-png,image/gif,image/jpeg" id="foto" name="foto"></span>
                                        <a href="#" class="btn btn-sm btn-outline-secondary fileinput-exists" data-dismiss="fileinput">Hapus</a>
                                    </div>
                                <small class="form-text text-muted">Hanya file (*.jpg, *.png, *.gif) dengan ukuran < 512 KB</small>
                                @if ($errors->has('foto'))
                                <div class="invalid-feedback d-block">{{ $errors->first('foto') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nip">NIP</label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="js-maxlength form-control{{ $errors->has('nip') ? ' is-invalid' : '' }}" id="nip" name="nip" maxlength="18" placeholder="NIP..." value="{{ $peserta->nip }}" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary">
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
                        <label class="col-sm-3 col-form-label text-right" for="ktp">No KTP</label>
                        <div class="col-sm-9">
                            <input type="text" class="js-maxlength form-control{{ $errors->has('ktp') ? ' is-invalid' : '' }}" id="ktp" name="ktp" maxlength="16" placeholder="No KTP..." value="{{ $peserta->ktp }}" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary">
                            @if ($errors->has('ktp'))
                            <div class="invalid-feedback">{{ $errors->first('ktp') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="status_asn">Status ASN</label>
                        <div class="col-sm-9">
                            <select class="custom-select" id="status_asn" name="status_asn" required>
                                <option value="">-- Pilih Status ASN --</option>
                                <option value="1" {{ ($peserta->status_asn == 1 ? 'selected' : '') }}>PNS</option>
                                <option value="2" {{ ($peserta->status_asn == 2 ? 'selected' : '') }}>PPPK</option>
                                <option value="0" {{ ($peserta->status_asn == 0 ? 'selected' : '') }}>Non-ASN</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('nama_lengkap') ? ' is-invalid' : '' }}" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap..." value="{{ $peserta->nama_lengkap }}" required>
                            @if ($errors->has('nama_lengkap'))
                            <div class="invalid-feedback">{{ $errors->first('nama_lengkap') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="nama_panggil">Nama Panggilan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('nama_panggil') ? ' is-invalid' : '' }}" id="nama_panggil" name="nama_panggil" placeholder="Nama Panggilan..." value="{{ $peserta->nama_panggil }}">
                            @if ($errors->has('nama_panggil'))
                            <div class="invalid-feedback">{{ $errors->first('nama_panggil') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="alamat">Alamat</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat...">{{ $peserta->alamat }}</textarea>
                            @if ($errors->has('alamat'))
                            <div class="invalid-feedback">{{ $errors->first('alamat') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="jk">Jenis Kelamin <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control" id="jk" name="jk" style="width: 100%;" required>
                                <option value="" selected>-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ ($peserta->jk == 'L' ? 'selected' : '') }}>Laki-laki</option>
                                <option value="P" {{ ($peserta->jk == 'P' ? 'selected' : '') }}>Perempuan</option>
                            </select>
                            @if ($errors->has('jk'))
                            <div class="invalid-feedback">{{ $errors->first('jk') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="tmp_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('tmp_lahir') ? ' is-invalid' : '' }}" id="tmp_lahir" name="tmp_lahir" placeholder="Tempat Lahir..." value="{{ $peserta->tmp_lahir }}" required>
                            @if ($errors->has('tmp_lahir'))
                            <div class="invalid-feedback">{{ $errors->first('tmp_lahir') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="tgl_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="js-datepicker form-control{{ $errors->has('tgl_lahir') ? ' is-invalid' : '' }}" id="tgl_lahir" name="tgl_lahir" value="{{ $peserta->tgl_lahir }}" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="Tanggal Lahir... (yyyy-mm-dd)" required>
                            @if ($errors->has('tgl_lahir'))
                            <div class="invalid-feedback">{{ $errors->first('tgl_lahir') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="agama">Agama</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="agama" name="agama" style="width: 100%;">
                                <option value="" selected>-- Pilih Agama --</option>
                                @foreach ($agama as $a)
                                <option value="{{ $a->id }}" {{ ($a->id == $peserta->agama_id ? 'selected' : '') }}>{{ $a->nama }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('agama'))
                            <div class="invalid-feedback">{{ $errors->first('agama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="marital">Status Perkawinan</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="marital" name="marital" style="width: 100%;">
                                <option value="" selected>-- Pilih Status Perkawinan --</option>
                                <option value="1" {{ ($peserta->marital == 1 ? 'selected' : '') }}>Menikah</option>
                                <option value="2" {{ ($peserta->marital == 2 ? 'selected' : '') }}>Belum Menikah</option>
                                <option value="3" {{ ($peserta->marital == 3 ? 'selected' : '') }}>Duda</option>
                                <option value="4" {{ ($peserta->marital == 4 ? 'selected' : '') }}>Janda</option>
                            </select>
                            @if ($errors->has('marital'))
                            <div class="invalid-feedback">{{ $errors->first('marital') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="hp">Handphone <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('hp') ? ' is-invalid' : '' }}" id="hp" name="hp" placeholder="No. Handphone..." value="{{ $peserta->hp }}" required>
                            @if ($errors->has('hp'))
                            <div class="invalid-feedback">{{ $errors->first('hp') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="email">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" placeholder="Email..." value="{{ $peserta->email }}" required>
                                @if ($errors->has('email'))
                                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                        </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="pangkat">Pangkat</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="pangkat" name="pangkat" style="width: 100%;">
                                <option value="" selected>-- Pilih Pangkat --</option>
                                @foreach ($pangkat as $p)
                                <option value="{{ $p->id }}" {{ ($p->id == $peserta->pangkat_id ? 'selected' : '') }}>{{ $p->singkat }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('pangkat'))
                            <div class="invalid-feedback">{{ $errors->first('pangkat') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="jabatan">Jabatan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('jabatan') ? ' is-invalid' : '' }}" id="jabatan" name="jabatan" placeholder="Jabatan..." value="{{ $peserta->jabatan }}" required>
                            @if ($errors->has('jabatan'))
                            <div class="invalid-feedback">{{ $errors->first('jabatan') }}</div>
                            @endif
                        </div>
                    </div>
                    <h2 class="content-heading pt-0">Data Instansi</h2>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="instansi">Instansi <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" list="instansi_list" class="form-control{{ $errors->has('instansi') ? ' is-invalid' : '' }}" id="instansi" name="instansi" placeholder="Instansi..." value="{{ $peserta->instansi }}" required>
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
                        <label class="col-sm-3 col-form-label text-right" for="satker_nama">Satuan Kerja (SKPD/OPD) / Partai <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('satker_nama') ? ' is-invalid' : '' }}" id="satker_nama" name="satker_nama" placeholder="Satuan Kerja..." value="{{ $peserta->satker_nama }}" required>
                            @if ($errors->has('satker_nama'))
                            <div class="invalid-feedback">{{ $errors->first('satker_nama') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="satker_alamat">Alamat</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="satker_alamat" name="satker_alamat" placeholder="Alamat Satuan Kerja...">{{ $peserta->satker_alamat }}</textarea>
                            @if ($errors->has('satker_alamat'))
                            <div class="invalid-feedback">{{ $errors->first('satker_alamat') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="satker_telp">Telepon</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control{{ $errors->has('satker_telp') ? ' is-invalid' : '' }}" id="satker_telp" name="satker_telp" placeholder="No. Telepon Satuan Kerja..." value="{{ $peserta->satker_telp }}">
                            @if ($errors->has('satker_telp'))
                            <div class="invalid-feedback">{{ $errors->first('satker_telp') }}</div>
                            @endif
                        </div>
                    </div>
                    <h2 class="content-heading pt-0">Status Peserta</h2>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="verifikasi">Verifikasi <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control" id="verifikasi" name="verifikasi" style="width: 100%;" required>
                                <option value="" selected>-- Pilih Verifikasi --</option>
                                <option value="0" {{ ($peserta->verifikasi == '0' ? 'selected' : '') }}>Belum</option>
                                <option value="1" {{ ($peserta->verifikasi == '1' ? 'selected' : '') }}>Sudah</option>
                                <option value="2" {{ ($peserta->verifikasi == '2' ? 'selected' : '') }}>Tolak</option>
                            </select>
                            @if ($errors->has('verifikasi'))
                            <div class="invalid-feedback">{{ $errors->first('verifikasi') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label text-right" for="batal">Batal <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control" id="batal" name="batal" style="width: 100%;" required>
                                <option value="" selected>-- Pilih Batal --</option>
                                <option value="0" {{ ($peserta->batal == '0' ? 'selected' : '') }}>Tidak</option>
                                <option value="1" {{ ($peserta->batal == '1' ? 'selected' : '') }}>Ya</option>
                            </select>
                            @if ($errors->has('batal'))
                            <div class="invalid-feedback">{{ $errors->first('batal') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row batal-ket" style="{{ ($peserta->batal ? '' : 'display: none')}}">
                        <label class="col-sm-3 col-form-label text-right" for="batal_ket">Keterangan Batal <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="batal_ket" name="batal_ket" placeholder="Keterangan Batal..." {{ ($peserta->batal ? '' : 'disabled')}} required>{{ $peserta->batal_ket }}</textarea>
                            @if ($errors->has('batal_ket'))
                            <div class="invalid-feedback">{{ $errors->first('batal_ket') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group mt-4 row">
                        <label class="col-sm-3 col-form-label text-right">&nbsp;</label>
                        <div class="col-sm-9">
                            <a href='{{URL::previous()}}' class="btn btn-sm btn-light"><i class="fa fa-chevron-circle-left"></i>
                            {{-- <a href="{{route('backend.diklat.peserta.back', ['id' => $jadwal->id, 'slug' => str_slug($jadwal->nama)])}}" class="btn btn-sm btn-light"><i class="fa fa-chevron-circle-left"></i> --}}
                                Kembali</a>
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
