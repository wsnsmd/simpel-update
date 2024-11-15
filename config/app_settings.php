<?php

return [

    // All the sections for the settings page
    'sections' => [
        'app' => [
            'title' => 'Umum',
            // 'descriptions' => 'Application general settings.', // (optional)
            'icon' => 'fa fa-cog', // (optional)

            'inputs' => [
                [
                    'name' => 'app_nama', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'Nama Aplikasi', // label for input
                    // optional properties
                    'placeholder' => 'Nama Aplikasi', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'required|min:2|max:20', // validation rules for this input
                    'value' => 'SIM Diklat', // any default value
                    // 'hint' => 'You can set the app name here' // help block text for input
                ],
                // [
                //     'name' => 'logo',
                //     'type' => 'image',
                //     'label' => 'Upload logo',
                //     'hint' => 'Must be an image and cropped in desired size',
                //     'rules' => 'image|max:500',
                //     'disk' => 'public', // which disk you want to upload
                //     'path' => 'app', // path on the disk,
                //     'preview_class' => 'thumbnail',
                //     'preview_style' => 'height:40px'
                // ]
                [
                    'name' => 'app_tahun', // unique key for setting
                    'type' => 'select', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'Tahun Aktif', // label for input
                    // optional properties
                    'placeholder' => 'Tahun Aktif', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'required', // validation rules for this input
                    'value' => '', // any default value
                    'hint' => 'Tahun Aktif pada Aplikasi', // help block text for input
                    'options' => function() {
                        $tahun = DB::table('tahun')->select('tahun as value', 'tahun as label')
                                    ->where('aktif', 1)
                                    ->get();
                        // $data = [];
                        // $data[] = array('value' => 'm/d/Y', 'label' => date('m/d/Y'));
                        // $array = json_encode($data);
                        // // dd($array);
                        return json_encode($tahun);
                    }
                ],
                [
                    'name' => 'app_tema', // unique key for setting
                    'type' => 'select', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'Tema', // label for input
                    // optional properties
                    'placeholder' => 'Tahun Aktif', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'required', // validation rules for this input
                    'value' => '', // any default value
                    'options' => function() {
                        $data = [];
                        $data[] = array('value' => 'xdream', 'label' => 'xdream');
                        $data[] = array('value' => 'xeco', 'label' => 'xeco');
                        $data[] = array('value' => 'xinspire', 'label' => 'xinspire');
                        $data[] = array('value' => 'xmodern', 'label' => 'xmodern');
                        $data[] = array('value' => 'xplay', 'label' => 'xplay   ');
                        $data[] = array('value' => 'xpro', 'label' => 'xpro');
                        $data[] = array('value' => 'xsmooth', 'label' => 'xsmooth');
                        $data[] = array('value' => 'xwork', 'label' => 'xwork');
                        return json_encode($data);
                    }
                ],
            ]
        ],
        'email' => [
            'title' => 'Email Settings',
            'descriptions' => 'How app email will be sent.',
            'icon' => 'fa fa-envelope',

            'inputs' => [
                [
                    'name' => 'from_email',
                    'type' => 'email',
                    'label' => 'From Email',
                    'placeholder' => 'Application from email',
                    'rules' => 'required|email',
                ],
                [
                    'name' => 'from_name',
                    'type' => 'text',
                    'label' => 'Email from Name',
                    'placeholder' => 'Email from Name',
                ]
            ]
        ]
    ],

    // Setting page url, will be used for get and post request
    'url' => 'backend/pangaturan',
    'route' => 'backend.pengaturan',

    // Any middleware you want to run on above route
    'middleware' => ['auth','can:isAdmin'],

    // View settings
    'setting_page_view' => 'backend.pengaturan',
    'flash_partial' => 'app_settings::_flash',

    // Setting section class setting
    'section_class' => 'block block-rounded block-bordered',
    'section_heading_class' => 'block-header block-header-default',
    'section_body_class' => 'block-content',

    // Input wrapper and group class setting
    'input_wrapper_class' => 'form-group',
    'input_class' => 'form-control',
    'input_error_class' => 'has-error',
    'input_invalid_class' => 'is-invalid',
    'input_hint_class' => 'form-text text-muted',
    'input_error_feedback_class' => 'text-danger',

    // Submit button
    'submit_btn_text' => 'Simpan',
    'submit_success_message' => 'Pengaturan berhasil disimpan.',

    // Remove any setting which declaration removed later from sections
    'remove_abandoned_settings' => false,

    // Controller to show and handle save setting
    'controller' => '\QCod\AppSettings\Controllers\AppSettingController',

    // settings group
    'setting_group' => function() {
        // return 'user_'.auth()->id();
        return 'default';
    },    
];
