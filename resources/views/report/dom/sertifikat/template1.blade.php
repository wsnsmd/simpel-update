<html>

<head>
    <title>Sertifikat Pelatihan - {!! $sertPeserta->nomor !!}</title>
    <meta name="author" content="IT BPSDM Prov. Kaltim @ {!! date('Y') !!}" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin-top: 0cm;
            margin-bottom: 0cm;
            margin-left: 0cm;
            margin-right: 0cm;
        }

        @font-face {
            font-family: 'bookman';
            src: url({{ storage_path('fonts/bookos.ttf') }}) format('truetype');
        }
        @font-face {
            font-family: 'bookman';
            src: url({{ storage_path('fonts/bookosb.ttf') }}) format('truetype');
            font-weight: bold;
        }

        html,
        body,
        form,
        fieldset,
        table,
        tr,
        td,
        img {
            font-size: 10pt;
            text-align: justify;
        }

        table {
            border: 0px;
            border-collapse: collapse;
        }

        table.header td {
            font-family: 'Bookman';
        }

        table.mdiklat td {
            font-family: bookman;
            font-size: 10pt;
        }

        div {
            margin: 0 15 0 15;
        }

        p {
            font-family: bookman;
            font-size: 10pt;
            margin: 0;
            text-align: justify;
        }

        .page_break {
            page-break-before: always;
        }

        #container {
            margin: 0cm 2cm 0cm 2cm;
        }

        #tt1 {
            padding-top: 0.5cm;
            padding-left: 15cm;
        }

        @if(!is_null($sertifikat->spesimen))
        #tt2 {
            position: fixed;
            bottom: {{ $sertPeserta->spesimen_bawah  }}cm;
            left: {{ $sertPeserta->spesimen_kiri }}cm;
        }
        @endif
    </style>
</head>

<body>
    <table width="100%" cellspacing="0" cellpadding="0" class="header" style="padding-top: 0.5cm;">
        <tbody>
            <tr>
                <td width="15%" style="text-align: center"><img src="{{ imageToBase64(public_path('/media/images/pemprov.png')) }}"
                        height="120" width="120"></td>
            </tr>
        </tbody>
    </table>
    <table width="100%" cellspacing="0" cellpadding="0" class="header" style="margin-top: 0.2cm;">
        <tbody>
            <tr>
                <td style="text-align: center">
                    <span style="font-weight: bold; font-size: 14pt; font-family: bookman">SURAT TANDA TAMAT PELATIHAN</span><br>
                    <span style="font-size: 10pt; font-family: bookman">Nomor : {!! $sertPeserta->nomor !!}</span></td>
            </tr>
        </tbody>
    </table>

    <div id="container">
        <div style="text-align: center; margin-left: 0px; margin-right: 0px; margin-top: 20px">
			<p>Badan Pengembangan Sumber Daya Manusia Provinsi Kalimantan Timur berdasarkan Peraturan Pemerintah Republik Indonesia Nomor 11 Tahun 2017 tentang Manajemen Pegawai Negeri Sipil serta ketentuan pelaksanaannya, menyatakan bahwa:</p>
			<table width="100%" cellspacing="0" cellpadding="0" class="info" style="margin: 10px 0; font-weight: bold; font-size: 12pt;">
                <tbody>
                    <tr>
                        <td style="vertical-align: top">
                            <table width="100%" cellspacing="0" cellpadding="3"
                                style="padding: 2px 0 2px 0px;" class="mdiklat">
                                <tbody>
                                    <tr>
                                        <td rowspan="6" width="18%" style="vertical-align: middle; text-align: center;"><img style="border: 2px solid;" src="{{ $sertPeserta->foto }}" height="160px" /></td>
                                        <td width="20%">Nama</td>
                                        <td width="2%">:</td>
                                        <td width="60%">{!! $sertPeserta->nama_lengkap !!}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Tempat dan Tanggal Lahir</td>
                                        <td style="vertical-align: top">:</td>
                                        <td style="vertical-align: top">{!! $sertPeserta->tmp_lahir !!}, {!! formatTanggal($sertPeserta->tgl_lahir) !!}</td>
                                    </tr>
                                    <tr>
                                        <td>NIP</td>
                                        <td>:</td>
                                        <td>
                                            @if(is_null($sertPeserta->nip))
                                                -
                                            @else
                                            {!! formatNIP($sertPeserta->nip) !!}
                                            @endif
                                        </td>
                                    </tr>
									<tr>
                                        <td>Pangkat / Golongan</td>
                                        <td>:</td>
                                        <td>
                                            @if(is_null($sertPeserta->pangkat))
                                                -
                                            @else
                                            {!! $sertPeserta->pangkat !!}, {!! $sertPeserta->golongan !!}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Jabatan</td>
                                        <td style="vertical-align: top">:</td>
                                        <td style="vertical-align: top">{!! $sertPeserta->jabatan !!}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">Instansi</td>
                                        <td style="vertical-align: top">:</td>
                                        <td style="vertical-align: top; text-transform: uppercase">{!! $sertPeserta->instansi !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
			<p>pada Pelatihan {!! $jadwal->nama !!} Tahun {!! $jadwal->tahun !!} yang diselenggarakan oleh Badan Pengembangan Sumber Daya Manusia Provinsi Kalimantan Timur dari tanggal {!! formatTanggal($jadwal->tgl_awal) !!} sampai dengan {!! formatTanggal($jadwal->tgl_akhir) !!} di {!! $jadwal->lokasi_kota !!} yang meliputi {!! $jadwal->total_jp !!} jam pelajaran.</p>
        </div>
        <div id="tt1">
            <table cellspacing="0" cellpadding="0" class="mdiklat">
                <tr>
                    <td style="text-align: center; text-transform: uppercase; padding-bottom: 30px">{!! $sertifikat->tempat !!}, {!! formatTanggal($sertifikat->tanggal) !!}</td>
                </tr>
                <tr>
                    <td style="text-align: center; text-transform: uppercase">{!! $sertifikat->jabatan !!}</td>
                </tr>
                <tr>
                    <td style="text-align: center">BADAN PENGEMBANGAN SUMBER DAYA MANUSIA</td>
                </tr>
                <tr>
                    <td style="text-align: center; padding-bottom: 100px">PROVINSI KALIMANTAN TIMUR</td>
                </tr>
                <tr>
                    <td style="text-align: center; font-weight: bold; text-decoration: underline;">{!! $sertifikat->nama !!}</td>
                </tr>
                <tr>
                    <td style="text-align: center">NIP. {!! formatNIP($sertifikat->nip) !!}</td>
                </tr>
            </table>
        </div>
        @if(!is_null($sertifikat->spesimen))
        <div id="tt2">
            <img src="{{ storage_path('app/' . $sertifikat->spesimen) }}" height="200" />
        </div>
        @endif
    </div>
</body>

</html>
