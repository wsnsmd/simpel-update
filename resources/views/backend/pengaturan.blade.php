@extends('layouts.backend')

@section('css_before')

@endsection

@section('js_after')

@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('{{ asset('media/various/bg_dashboard.jpg') }}');">
        <div class="bg-white-90">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Pengaturan</li>
                            <li class="breadcrumb-item active" aria-current="page">Aplikasi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <div class="content">
        <div class="block block-rounded block-bordered block-fx-shadow block-themed">
            <div class="block-header">
                <h3 class="block-title">Pengaturan</h3>
            </div>
            <div class="block-content">
                <div class="row">
                    <div class="col-md-12">                
                        @includeIf(config('app_settings.flash_partial'))                
                        <form method="post" action="{{ URL::current() }}" class="form-horizontal mb-3" enctype="multipart/form-data" role="form">
                            {!! csrf_field() !!}
            
                            @if( isset($settingsUI) && count($settingsUI) )
            
                                @foreach(Arr::get($settingsUI, 'sections', []) as $section => $fields)
                                    @component('app_settings::section', compact('fields'))
                                        <div class="{{ Arr::get($fields, 'section_body_class', config('app_settings.section_body_class', 'card-body')) }}">
                                            @foreach(Arr::get($fields, 'inputs', []) as $field)
                                                @if(!view()->exists('app_settings::fields.' . $field['type']))
                                                    <div style="background-color: #f7ecb5; box-shadow: inset 2px 2px 7px #e0c492; border-radius: 0.3rem; padding: 1rem; margin-bottom: 1rem">
                                                        Defined setting <strong>{{ $field['name'] }}</strong> with
                                                        type <code>{{ $field['type'] }}</code> field is not supported. <br>
                                                        You can create a <code>fields/{{ $field['type'] }}.balde.php</code> to render this input however you want.
                                                    </div>
                                                @endif
                                                @includeIf('app_settings::fields.' . $field['type'] )
                                            @endforeach
                                        </div>
                                    @endcomponent
                                @endforeach
                            @endif
            
                            <div class="row m-b-md">
                                <div class="col-md-12">
                                    <button class="btn-primary btn">
                                        {{ Arr::get($settingsUI, 'submit_btn_text', 'Save Settings') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>               
            </div>
        </div>
    </div>

@endsection
