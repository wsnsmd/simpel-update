<?php

return [

    'sections' => [
        'app' => [
            'title' => 'Umum',
            'icon' => 'fa fa-cog',

            'inputs' => [
                [
                    'name' => 'app_nama',
                    'type' => 'text',
                    'label' => 'Nama Aplikasi',
                    'placeholder' => 'Nama Aplikasi',
                    'class' => 'form-control',
                    'rules' => 'required|min:2|max:20',
                    'value' => 'SIM Diklat',
                ],
                [
                    'name' => 'app_tahun',
                    'type' => 'text', // Diubah dari select ke text
                    'label' => 'Tahun Aktif',
                    'placeholder' => 'Contoh: 2026',
                    'class' => 'form-control',
                    'rules' => 'required|numeric|digits:4', // Tambahkan validasi angka 4 digit
                    'value' => date('Y'), // Default tahun saat ini
                    'hint' => 'Masukkan tahun aktif anggaran/kegiatan (format: YYYY)',
                ],
                [
                    'name' => 'app_tema',
                    'type' => 'select',
                    'label' => 'Tema',
                    'placeholder' => 'Tahun Aktif',
                    'class' => 'form-control',
                    'rules' => 'required',
                    'value' => '',
                    // SOLUSI: Mengubah Closure menjadi Array Statis
                    'options' => [
                        'xdream' => 'xdream',
                        'xeco' => 'xeco',
                        'xinspire' => 'xinspire',
                        'xmodern' => 'xmodern',
                        'xplay' => 'xplay',
                        'xpro' => 'xpro',
                        'xsmooth' => 'xsmooth',
                        'xwork' => 'xwork',
                    ]
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

    'url' => 'backend/pangaturan',
    'route' => 'backend.pengaturan',
    'middleware' => ['auth', 'can:isAdmin'],
    'setting_page_view' => 'backend.pengaturan',
    'flash_partial' => 'app_settings::_flash',
    'section_class' => 'block block-rounded block-bordered',
    'section_heading_class' => 'block-header block-header-default',
    'section_body_class' => 'block-content',
    'input_wrapper_class' => 'form-group',
    'input_class' => 'form-control',
    'input_error_class' => 'has-error',
    'input_invalid_class' => 'is-invalid',
    'input_hint_class' => 'form-text text-muted',
    'input_error_feedback_class' => 'text-danger',
    'submit_btn_text' => 'Simpan',
    'submit_success_message' => 'Pengaturan berhasil disimpan.',
    'remove_abandoned_settings' => false,
    'controller' => '\QCod\AppSettings\Controllers\AppSettingController',

    // SOLUSI: Mengubah Closure menjadi String Statis
    'setting_group' => 'default',
];