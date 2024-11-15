@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        jQuery(function(){
            // Override a few default classes
            jQuery.extend(jQuery.fn.dataTable.ext.classes, {
                sWrapper: "dataTables_wrapper dt-bootstrap4",
                sFilterInput:  "form-control",
                sLengthSelect: "form-control"
            });

            // Override a few defaults
            jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                language: {
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

            jQuery('.js-dataTable-full').dataTable({
                pageLength: 50,
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                autoWidth: false,
                ordering: false,
            });

            $('[data-toggle="tooltip"]').tooltip();
        });

    </script>
@endsection

@section('content')
    <!-- Hero -->
    @include('backend.pengaturan.pengguna.hero')
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

        <!-- Dynamic Table Full -->
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header">
                <h3 class="block-title">Aktifitas</h3>
            </div>
            <div class="block-content block-content-full border-top">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full" style="width:100%">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Method</th>
                                <th>URL</th>
                                <th>IP Address</th>
                                <th>Agent</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                            @php
                                // Decode JSON data
                                $logData = json_decode($log->log_data, true);
                            @endphp
                            <tr>
                                <td class="font-w600">{{ $log->user_id }}</td>
                                <td class="font-w600">{{ $log->username }}</td>
                                <td class="font-w600">{{ $logData['method'] }}</td>
                                <td class="font-w600">{{ $logData['url'] }}</td>
                                <td class="font-w600">{{ $logData['ip_address'] }}</td>
                                <td class="font-w600">{{ $logData['agent'] }}</td>
                                <td class="font-w600">{{ \Carbon\Carbon::parse($logData['time'])->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    </div>
    <!-- END Page Content -->
@endsection
