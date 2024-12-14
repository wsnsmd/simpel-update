@extends('layouts.frontend')

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
    @if (session('error') || $errors->has('captcha'))
            $.notify({
                icon: "fa fa-times mr-1",
                @if($errors->has('captcha'))
                message: "Captcha salah!"
                @else
                message: "{{ session('error') }}"
                @endif
            }, {
                allow_dismiss: false,
                type: 'danger',
                placement: {
                    from: "top",
                    align: "center"
                }
            });
    @endif
    $("#reload").click(function () {
        $.ajax({
            type: "GET",
            url: "{{ route('reload.captcha') }}",
            success: function (data) {
                console.log(data);
                $(".captcha span").html(data.captcha);
            }
        });
    });
    </script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light border-top border-bottom">
        <div class="content content-full py-1">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-sm text-uppercase font-w700 mt-2 mb-0 mb-sm-2">
                    <i class="fa fa-angle-right fa-fw text-primary"></i> Cek Sertifikat
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3 font-size-sm" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><i class="fa fa-home"></i></li>
                        <li class="breadcrumb-item active" aria-current="page">Cek Sertifikat</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content invisible" data-toggle="appear">
                <div class="text-center py-3">
                    <h1 class="h3 font-w700 mb-2">Cek Sertifikat</h1>
                    <h2 class="h5 font-w400 text-muted">Silahkan isi form dibawah untuk pengecekan sertifikat!</h2>
                </div>
                <div class="text-center">
                    <form id="form-cek" action="{{ route('sertifikat.cek') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <input type="text" class="form-control form-control-lg py-3 text-center" id="nomor" name="nomor" placeholder="Nomor Sertifikat..." required>
                        </div>
                        <div class="form-group row" style="display: flex; justify-content: center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg text-center" id="captcha" name="captcha" placeholder="Captcha..." required>
                                    <div class="input-group-append">
                                        <button type="reset" class="btn btn-dark" id="reload"><i class="fa fa-sync"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" style="display: flex; justify-content: center">
                            <div class="col-md-3 text-center captcha">
                                <span>
                                    {!! captcha_img('flat') !!}
                                </span>
                            </div>
                        </div>
                        <div class="form-group row items-push mb-0" style="display: flex; justify-content: center">
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-block btn-primary"><i class="fa fa-search mr-1"></i> Cek</button>
                            </div>
                            <div class="col-md-2">
                                <button type="reset" class="btn btn-block btn-dark">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if(!empty($peserta))
    <div class="bg-white">
        <div class="content">
            <div class="row invisible" data-toggle="appear">
                <div class="block block-rounded block-bordered" style="width: 100%">
                    <div class="block-content block-content-full">
                        <div class="table-responsive" id="div-data">
                            <table class="table table-hover table-bordered table-striped table-sm">
                                <tbody>
                                    <tr>
                                        <td>Nomor Sertifikat</td>
                                        <td>:</td>
                                        <td>{{ $peserta->nomor }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama</td>
                                        <td>:</td>
                                        <td>{{ $peserta->nama_lengkap }}</td>
                                    </tr>
                                    <tr>
                                        <td>NIK</td>
                                        <td>:</td>
                                        <td>
                                        @php
                                            $output = '';
                                            if(!is_null($peserta->ktp) && strlen($peserta->ktp) == 16)
                                            {
                                                $count = strlen($peserta->ktp) - 8;
                                                $output = substr_replace($peserta->ktp, str_repeat('*', $count), 2, $count);
                                            }
                                            else
                                            {
                                                $output = '-';
                                            }
                                            echo $output;
                                        @endphp
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>NIP</td>
                                        <td>:</td>
                                        <td>
                                        @php
                                            $output = '';
                                            if(!is_null($peserta->nip) && strlen($peserta->nip) == 18)
                                            {
                                                $count = strlen($peserta->nip) - 8;
                                                $output = substr_replace($peserta->nip, str_repeat('*', $count), 2, $count);
                                            }
                                            else
                                            {
                                                $output = '-';
                                            }
                                            echo $output;
                                        @endphp
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pangkat/Golongan/Ruang</td>
                                        <td>:</td>
                                        <td>
                                        @if(strlen($peserta->pangkat) == 0 || strlen($peserta->golongan) == 0)
                                        -
                                        @else
                                        {{ $peserta->pangkat }} ({{ $peserta->golongan }})
                                        @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jabatan</td>
                                        <td>:</td>
                                        <td>{{ $peserta->jabatan ?? '-' }}</td>
                                    </tr=>
                                    <tr>
                                        <td>Instansi</td>
                                        <td>:</td>
                                        <td>{{ $peserta->instansi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Pelatihan</td>
                                        <td>:</td>
                                        <td>{{ $jadwal->nama }}</td>
                                    </tr>
                                    <tr>
                                        @php
                                            $date_awal = date_create($jadwal->tgl_awal);
                                            $date_akhir = date_create($jadwal->tgl_akhir);
                                            $jum_hari = date_diff($date_awal, $date_akhir);
                                        @endphp
                                        <td>Tanggal</td>
                                        <td>:</td>
                                        <td>
                                        @if($jum_hari->d == 0)
                                        {{ formatTanggal($jadwal->tgl_awal) }}
                                        @else
                                        {{ formatTanggal($jadwal->tgl_awal) }} - {{ formatTanggal($jadwal->tgl_akhir) }}
                                        @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Tempat</td>
                                        <td>:</td>
                                        <td>{{ $jadwal->lokasi_kota }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kualifikasi</td>
                                        <td>:</td>
                                        <td>{{ $peserta->kualifikasi ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
