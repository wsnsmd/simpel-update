<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Jenis Layanan Diklat
    |--------------------------------------------------------------------------
    */
    'jenis_layanan' => [
        'Pelatihan Manajerial',
        'Pelatihan Dasar',
        'Orientasi dan Pendalaman Tugas Anggota DPRD Kabupaten/Kota',
        'Orientasi PPPK',
        'Pelatihan Sosiokultural',
        'Pelatihan Jabatan Fungsional',
        'Workshop / Bimbingan Teknis / Webinar',
        'Pelatihan Teknis',
        'Pelatihan Bendaharawan',
        'Pelatihan dan Uji Kompetensi Pengadaan Barang/Jasa Level 1',
        'Pelatihan Okupasi PPK Tipe C',
        'Uji Kompetensi Pemerintahan',
        'Uji Kompetensi Jabatan Fungsional Lingkup Kementerian Dalam Negeri',
        'Kerjasama Antar Lembaga',
        'Fasilitasi sarana dan prasarana Pelatihan dan Uji Kompetensi',
    ],

    'pendidikan' => [
        'SD / sederajat',
        'SMP / sederajat',
        'SMA / SMK / sederajat',
        'Diploma (D1–D4)',
        'Sarjana (S1)',
        'Magister (S2)',
        'Doktor (S3)',
    ],

    'survey_app' => [
        'base_url' => env('SURVEY_APP_URL', 'https://survei.test'),
        'secret_key' => env('SURVEY_SECRET_KEY', 'kunci-rahasia-anda'),
    ],

];
