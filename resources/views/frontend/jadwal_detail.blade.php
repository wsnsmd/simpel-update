@extends('layouts.frontend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <style>
        .dataTables_processing {
            position: absolute; top: 50% !important; left: 50% !important;
            width: 200px !important; margin-left: -100px !important; margin-top: -30px !important;
            padding: 15px !important; text-align: center; color: #333;
            border: none !important; background-color: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important; border-radius: 4px !important;
            z-index: 1060 !important;
        }
        .blur-content { filter: blur(2px); opacity: 0.5; transition: all 0.2s ease; pointer-events: none; }
        #table-peserta-frontend { transition: all 0.2s ease; }
    </style>
@endsection


@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        jQuery(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            jQuery.extend(jQuery.fn.dataTable.ext.classes, {
                sWrapper: "dataTables_wrapper dt-bootstrap4",
                sFilterInput:  "form-control",
                sLengthSelect: "form-control"
            });

            jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br>Memuat...',
                    emptyTable: "Tidak ada data tersedia",
                    infoEmpty: "Halaman 0 dari 0",
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Cari...",
                    info: "Halaman <strong>_PAGE_</strong> dari <strong>_PAGES_</strong>",
                    paginate: {
                        first: '<i class="fa fa-angle-double-left"></i>',
                        previous: '<i class="fa fa-angle-left"></i>',
                        next: '<i class="fa fa-angle-right"></i>',
                        last: '<i class="fa fa-angle-double-right"></i>'
                    }
                }
            });

            var table = $('#table-peserta-frontend').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('jadwal.peserta.datatable', $jadwal->id) }}",
                    type: "POST"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'nip_masking', name: 'nip', searchable: false, class: 'text-center' },
                    { data: 'nama_lengkap', name: 'nama_lengkap' },
                    { data: 'instansi_render', name: 'instansi' }
                ],
                pageLength: 25,
                autoWidth: false,
                drawCallback: function() {
                    $('#table-peserta-frontend').removeClass('blur-content');
                }
            });

            table.on('processing.dt', function (e, settings, processing) {
                if (processing) {
                    $('#table-peserta-frontend').addClass('blur-content');
                } else {
                    $('#table-peserta-frontend').removeClass('blur-content');
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
                    <i class="fa fa-angle-right fa-fw text-primary"></i> Jadwal
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3 font-size-sm" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><i class="fa fa-home"></i></li>
                        <li class="breadcrumb-item">Jadwal</i></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $jadwal->nama }}</li>
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
                    <h1 class="h3 font-w700 mb-2">{{ $jadwal->nama }}</h1>
                    <h2 class="h5 font-w400 text-muted">{{ $jadwal->kelas }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white">
        <div class="content">
            <div class="row invisible" data-toggle="appear">
                <div class="col-md-9">
                    <div class="block block-rounded block-bordered" style="min-height: 50vh">
                        <ul class="nav nav-tabs nav-tabs-block bg-gray-lighter" data-toggle="tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#daftar-peserta"><i class="fa fa-users mr-2"></i>Daftar Peserta</a>
                            </li>
                            @if(!is_null($jadwal->deskripsi))
                            <li class="nav-item">
                                <a class="nav-link" href="#deskripsi"><i class="fa fa-align-justify mr-2"></i>Deskripsi</a>
                            </li>
                            @endif
                            @if(!is_null($jadwal->syarat))
                            <li class="nav-item">
                                <a class="nav-link" href="#syarat"><i class="fa fa-info-circle mr-2"></i>Syarat</a>
                            </li>
                            @endif
                            @if(!is_null($jadwal->lampiran))
                            <li class="nav-item">
                                <a class="nav-link" href="#lampiran"><i class="fa fa-file-download mr-2"></i>Lampiran</a>
                            </li>
                            @endif
                        </ul>
                        <div class="block-content tab-content overflow-hidden">
                            <div class="tab-pane fade active show" id="daftar-peserta" role="tabpanel">
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-hover table-vcenter table-sm" id="table-peserta-frontend" style="width:100%">
                                        <thead class="bg-gray-lighter">
                                            <tr>
                                                <th class="text-center" style="width: 50px;">No</th>
                                                <th style="width: 200px;">NIP</th>
                                                <th>Nama Lengkap</th>
                                                <th>Instansi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            @if(!is_null($jadwal->deskripsi))
                            <div class="tab-pane fade" id="deskripsi" role="tabpanel">
                                {!! $jadwal->deskripsi !!}
                            </div>
                            @endif
                            @if(!is_null($jadwal->syarat))
                            <div class="tab-pane fade" id="syarat" role="tabpanel">
                                {!! $jadwal->syarat !!}
                            </div>
                            @endif
                            @if(!is_null($jadwal->lampiran))
                            <div class="tab-pane fade" id="lampiran" role="tabpanel">
                                {{-- {!! $jadwal->lampiran !!} --}}
                                <a href="{{ asset(\Storage::url($jadwal->lampiran)) }}" class="btn btn-square btn-primary mb-3" target="_blank"><i class="fa fa-download mr-1"></i> Unduh Lampiran</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3 px-0">
                    <div class="block block-rounded block-bordered mb-3">
                        <div class="block-header block-header-default bg-gray-lighter" style="max-height: 50px">
                            <h3 class="block-title">Informasi</h3>
                        </div>
                        <div class="block-content">
                            <table class="table table-borderless table-sm font-size-sm">
                                <tbody>
                                    <tr>
                                        <td class="font-w700">Tanggal Mulai</td>
                                        <td>{{$jadwal->tgl_awal}}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Tanggal Akhir</td>
                                        <td>{{$jadwal->tgl_akhir}}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Durasi</td>
                                        <td>{{$jadwal->jum_hari+1 }} Hari</td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Total JP</td>
                                        <td>{{$jadwal->total_jp}} JP</td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Kuota</td>
                                        <td>
                                            @if($jadwal->kuota == 0)
                                                <span class="text-primary" title="Tidak Dibatasi">&infin;</span> 
                                            @else
                                                {{ $jadwal->kuota }} Peserta
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Registrasi</td>
                                        <td>
                                            @switch($jadwal->registrasi)
                                            @case(0)
                                                <span class="badge badge-warning">Internal</span>
                                                @break
                                            @case(1)
                                                <span class="badge badge-primary">Online</span>
                                                @break
                                            @default
                                                <span class="badge badge-warning">Internal</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-w700">Status</td>
                                        <td>
                                            @switch($jadwal->status_jadwal)
                                            @case(1)
                                                <span class="badge badge-success">Berjalan</span>
                                                @break
                                            @case(2)
                                                <span class="badge badge-primary">Akan Datang</span>
                                                @break
                                            @default
                                                <span class="badge badge-danger">Selesai</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @if($jadwal->status_registrasi == true && $jadwal->registrasi_lengkap == true && $jadwal->kuota > $peserta)
                            <form action="{{ route('jadwal.daftar') }}" method="POST">
                                @csrf
                                <input type="hidden" id="jadwal_id" name="jadwal_id" value="{{$jadwal->id}}">
                                <button type="submit" class="btn btn-square btn-block btn-primary mb-3"><i class="fa fa-check mr-1"></i> Daftar</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <div class="block block-rounded block-bordered">
                        <div class="block-header block-header-default bg-gray-lighter" style="min-height: 50px">
                            <h3 class="block-title">Kontak</h3>
                        </div>
                        <div class="block-content">
                            <p class="font-size-sm">
                                <i class="fa fa-fw fa-user mr-1"></i> {{$jadwal->panitia_nama}} <br>
                                <i class="fa fa-fw fa-phone mr-1"></i> {{$jadwal->panitia_telp}} <br>
                                <i class="fa fa-fw fa-envelope mr-1"></i> {{$jadwal->panitia_email}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
