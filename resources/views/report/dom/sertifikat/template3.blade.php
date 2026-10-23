@php
$date_awal = date_create($jadwal->tgl_awal);
$date_akhir = date_create($jadwal->tgl_akhir);
$jum_hari = date_diff($date_awal, $date_akhir);
@endphp

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
            font-size: 11pt;
        }

        div {
            margin: 0 15 0 15;
        }

        p {
            font-family: bookman;
            font-size: 11pt;
            margin: 0;
            text-align: justify;
        }

        hr.style1 {
            border-top: 1px solid;
            border: none;
            height: 1px;
            /* Set the hr color */
            color: #000;
            /* old IE */
            background-color: #000;
            /* Modern Browsers */
        }

        hr.style2 {
            margin: 2px 200px 0px 200px;
            border-bottom: 2px solid;
            color: #00923F;
        }

        .page_break {
            page-break-before: always;
        }

        #container {
            margin: 0cm 2cm 0cm 2cm;
        }

        #tt1 {
            padding-top: 0.5cm;
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

<body style='background-image: url("/media/images/template3.jpg"); background-size: contain; background-repeat: no-repeat;'>
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
                    <span style="font-weight: bold; font-size: 14pt; font-family: bookman">BADAN PENGEMBANGAN SUMBER DAYA MANUSIA</span><br>
                    <span style="font-weight: bold; font-size: 14pt; font-family: bookman">PROVINSI KALIMANTAN TIMUR</span><br>
                    <span style="font-weight: bold; font-size: 22pt; font-family: bookman">SERTIFIKAT</span><br>
                    <span style="font-size: 11pt; font-family: bookman">Nomor : {!! $sertPeserta->nomor !!}</span>
                </td>
            </tr>
        </tbody>
    </table>

    <div id="container">
        <div style="text-align: center; margin-left: 0px; margin-right: 0px; margin-top: 20px">
			<table width="100%" cellspacing="0" cellpadding="0" class="info" style="margin: 10px 0; font-weight: bold; font-size: 12pt;">
                <tbody>
                    <tr>
                        <td style="vertical-align: top">
                            <table width="100%" cellspacing="0" cellpadding="3"
                                style="padding: 2px 0 2px 0px;" class="mdiklat">
                                <tbody>
                                    <tr>
                                        <td style="text-align: center"><span style="font-weight: bold; font-size: 14pt; font-family: bookman">DIBERIKAN KEPADA</span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; padding-top: 20px;">
                                            <span style="font-weight: bold; font-size: 16pt; font-family: bookman">
                                                {!! $sertPeserta->nama_lengkap !!} <br />
                                            </span>
                                            <hr class="style2" />
                                            @if(!empty($sertPeserta->sebagai))
                                            <span style="font-weight: bold; font-size: 14pt; font-family: bookman">Sebagai {!! $sertPeserta->sebagai !!}</span>
                                            @else
                                            <span style="font-weight: bold; font-size: 14pt; font-family: bookman">Sebagai Peserta</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            @if($jum_hari->d == 0)
			<p>Dalam {!! $jadwal->nama !!} Tahun {!! $jadwal->tahun !!} yang dilaksanakan oleh Badan Pengembangan Sumber Daya Manusia Provinsi Kalimantan Timur pada tanggal {!! formatTanggal($jadwal->tgl_awal) !!} sebanyak {!! $jadwal->total_jp !!} Jam Pelajaran (JP).</p>
            @else
            <p>Dalam {!! $jadwal->nama !!} Tahun {!! $jadwal->tahun !!} yang dilaksanakan oleh Badan Pengembangan Sumber Daya Manusia Provinsi Kalimantan Timur pada tanggal {!! formatTanggal($jadwal->tgl_awal) !!} sampai dengan {!! formatTanggal($jadwal->tgl_akhir) !!} sebanyak {!! $jadwal->total_jp !!} Jam Pelajaran (JP).</p>
            @endif
        </div>
        <div id="tt1">
            <table cellspacing="0" cellpadding="0" class="mdiklat" width="100%">
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
                    <td style="text-align: center">{!! $sertifikat->pangkat !!}</td>
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
