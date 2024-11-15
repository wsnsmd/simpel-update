@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/plugins/es6-promise/es6-promise.auto.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(function(){
            Dashmix.helpers(['core-bootstrap-tabs', 'datepicker', 'maxlength']);
            CKEDITOR.replace('dasar');
            CKEDITOR.replace('untuk');

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
        });

        const showNotifikasi = (msg, type='success') => {
            var icon = type === 'success' ? 'fa fa-check mr-1' : 'fa fa-times mr-1';

            $.notify({
                icon: icon,
                message: msg
            }, {
                allow_dismiss: false,
                type: type,
                placement: {
                    from: "top",
                    align: "center"
                }
            });
        }
    </script>
@endsection

@section('content')
    <!-- Hero -->
    @include('backend.diklat.jadwal.detail_hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

        <!-- Jadwal -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header">
                <h3 class="block-title">Edit Sertfikat - {{$jadwal->nama}}</h3>
            </div>
            <div class="block-content block-content-full">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissable" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <p class="mb-0">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </p>
                </div>
                @endif
                <div class="row mb-3 border-bottom">
                    <div class="col-lg-12">
                        <div class="block block-rounded block-bordered">
                            <form action="{{ route('backend.diklat.sertifikat.update', $sertifikat->id) }}" method="POST" enctype="multipart/form-data" autocomplete="on" id="form-sertifikat">
                            @csrf
                            @method('PATCH')
                            <ul class="nav nav-tabs-alt nav-tabs-block" data-toggle="tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#btabs-konten">Data Sertifikat</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#btabs-ttangan1">Penandatangan-1</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#btabs-ttangan2">Penandatangan-2</a>
                                </li>
                            </ul>
                            <div class="block-content tab-content overflow-hidden">
                                <div class="tab-pane fade active show" id="btabs-konten" role="tabpanel">
                                    <div class="form-group">
                                        <label for="is_generate" class="control-label">Generate Otomatis <span class="text-danger">*</span></label>
                                        <select class="form-control" id="is_generate" name="is_generate" style="width: 100%;" required>
                                        @foreach (['0' => 'Tidak', '1' => 'Ya'] as $value => $label)
                                            <option value="{{ $value }}" {{ $sertifikat->is_generate === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="is_upload" class="control-label">Upload <span class="text-danger">*</span></label>
                                        <select class="form-control" id="is_upload" name="is_upload" style="width: 100%;" required>
                                        @foreach (['0' => 'Tidak', '1' => 'Ya'] as $value => $label)
                                            <option value="{{ $value }}" {{ $sertifikat->is_upload === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="is_import" class="control-label">Import <span class="text-danger">*</span></label>
                                        <select class="form-control" id="is_import" name="is_import" style="width: 100%;" required>
                                        @foreach (['0' => 'Tidak', '1' => 'Ya'] as $value => $label)
                                            <option value="{{ $value }}" {{ $sertifikat->import === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="tempat" class="control-label">Tempat</label>
                                        <input type="text" class="form-control" id="tempat" name="tempat" placeholder="Cth. Samarinda..." value="{{ $sertifikat->tempat }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="tanggal" class="control-label">Tanggal</label>
                                        <input type="text" class="js-datepicker form-control" id="tanggal" name="tanggal" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" value="{{ $sertifikat->tanggal }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="tsid" class="control-label">Template <span class="text-danger">*</span></label>
                                        <select class="form-control" id="tsid" name="tsid" style="width: 100%;" required>
                                            <option value="" selected>-- Pilih Template --</option>
                                            @foreach ($template as $tp)
                                            <option value="{{ $tp->id }}" {{ $tp->id === $sertifikat->tsid ? 'selected' : '' }}>{{ $tp->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="format_nomor" class="control-label">Format Nomor</label>
                                        <input type="text" class="form-control" id="format_nomor" name="format_nomor" placeholder="Cth. {N}/{m}/{y}" value="{{ $sertifikat->format_nomor }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="is_barcode" class="control-label">Barcode <span class="text-danger">*</span></label>
                                        <select class="form-control" id="barcode" name="barcode" style="width: 100%;" required>
                                        @foreach (['0' => 'Tidak', '1' => 'Ya'] as $value => $label)
                                            <option value="{{ $value }}" {{ $sertifikat->barcode === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="is_kualifikasi" class="control-label">Kualifikasi <span class="text-danger">*</span></label>
                                        <select class="form-control" id="kualifikasi" name="kualifikasi" style="width: 100%;" required>
                                        @foreach (['0' => 'Tidak', '1' => 'Ya'] as $value => $label)
                                            <option value="{{ $value }}" {{ $sertifikat->kualifikasi === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="format_nomor" class="control-label">Fasilitasi</label>
                                        <input type="text" class="form-control" id="fasilitasi" name="fasilitasi" placeholder="Fasilitasi..." value="{{ $sertifikat->fasilitasi }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="is_final" class="control-label">Final Draft <span class="text-danger">*</span></label>
                                        <select class="form-control" id="is_finale" name="is_final" style="width: 100%;" required>
                                        @foreach (['0' => 'Tidak', '1' => 'Ya'] as $value => $label)
                                            <option value="{{ $value }}" {{ $sertifikat->is_final === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                        </select>
                                        <div class="form-text text-muted text-sm">
                                            1. Tidak - jika data sertifikat ingin diubah <br />
                                            2. Ya - jika sudah final dan ingin dicetak/dikirim
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="btabs-ttangan1" role="tabpanel">
                                    <div class="form-group">
                                        <label for="jabatan" class="control-label">Jabatan</label>
                                        <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Cth. Kepala, Plt. Kepala..." value="{{ $sertifikat->jabatan }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="nip" class="control-label">NIP</label>
                                        <input class="js-maxlength form-control" type="text" id="nip" name="nip" placeholder="NIP..." minlength="18" maxlength="18" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary" value="{{ $sertifikat->nip }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="nama" class="control-label">Nama</label>
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama..." value="{{ $sertifikat->nama }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="pangkat" class="control-label">Pangkat</label>
                                        <input type="text" class="form-control" id="pangkat" name="pangkat" placeholder="Pangkat..." value="{{ $sertifikat->pangkat }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="spesimen" class="control-label">Spesimen</label>
                                        <input type="file" class="form-control" id="spesimen" name="spesimen" placeholder="Spesimen">
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="btabs-ttangan2" role="tabpanel">
                                    <div class="form-group">
                                        <label for="jabatan2" class="control-label">Jabatan-2</label>
                                        <input type="text" class="form-control" id="jabatan2" name="jabatan2" placeholder="Cth. Kepala, Plt. Kepala..." value="{{ $sertifikat->jabatan2 }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="nip2" class="control-label">NIP-2</label>
                                        <input class="js-maxlength form-control" type="text" id="nip2" name="nip2" placeholder="NIP..." minlength="18" maxlength="18" data-always-show="true" data-warning-class="badge badge-primary" data-limit-reached-class="badge badge-primary" value="{{ $sertifikat->nip2 }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="nama2" class="control-label">Nama-2</label>
                                        <input type="text" class="form-control" id="nama2" name="nama2" placeholder="Nama..." value="{{ $sertifikat->nama2 }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="pangkat2" class="control-label">Pangkat-2</label>
                                        <input type="text" class="form-control" id="pangkat2" name="pangkat2" placeholder="Pangkat..." value="{{ $sertifikat->pangkat2 }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="spesimen2" class="control-label">Spesimen-2</label>
                                        <input type="file" class="form-control" id="spesimen2" name="spesimen2" placeholder="Spesimen">
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="float-left">
                            <button type="submit" value="simpan" class="btn btn-square btn-primary" form="form-sertifikat">
                                <i class="fas fa-save mr-2"></i>Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Jadwal -->
    </div>
    <!-- END Page Content -->
@endsection
