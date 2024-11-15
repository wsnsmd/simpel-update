@php
    $date_awal = date_create($jadwal->tgl_awal);
    $date_akhir = date_create($jadwal->tgl_akhir);
    $jum_hari = date_diff($date_awal, $date_akhir);
@endphp

<html>

<head>
    <title>Sertifikat - {!! $sertPeserta->nomor !!}</title>
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
            font-size: 11pt;
            text-align: justify;
        }

        table {
            border: 0px;
            border-collapse: collapse;
        }

        table.header th, td {
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
            line-height: 1;
        }

        .page_break {
            page-break-before: always;
        }

        #container {
            margin: 0cm 2cm 0cm 2cm;
        }

        #container2 {
            margin: 0cm 2cm 0cm 2cm;
        }

        #tt1 {
            padding-top: 0.5cm;
            padding-left: 14cm;
        }

        #tt3 {
            padding-top: 0.5cm;
            padding-left: 14cm;
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
                    <span style="font-weight: bold; font-size: 12pt; font-family: bookman; line-height: 1.0;">BADAN PENGEMBANGAN SUMBER DAYA MANUSIA PROVINSI KALIMANTAN TIMUR</span><br>
                    <span style="font-weight: bold; font-size: 20pt; font-family: bookman; line-height: 1.5;">S E R T I F I K A T</span><br>
                    <span style="font-size: 12pt; font-family: bookman; line-height: 1.0;">Nomor : {!! $sertPeserta->nomor !!}</span></td>
            </tr>
        </tbody>
    </table>

    <div id="container">
        <div style="text-align: center; margin-left: 0px; margin-right: 0px; margin-top: 20px">
			<p style="text-align: center;;">Diberikan kepada</p>
			<table width="100%" cellspacing="0" cellpadding="0" class="info" style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                <tbody>
                    <tr>
                        <td style="vertical-align: top">
                            <table width="100%" cellspacing="0" cellpadding="3" border="0"
                                style="padding: 0;" class="mdiklat">
                                <tbody>
                                    <tr>
                                        <td width="22%">Nama</td>
                                        <td width="2%">:</td>
                                        <td width="62%">{!! $sertPeserta->nama_lengkap !!}</td>
                                    </tr>
                                    <tr>
                                        <td>NIP/NRP/NI PPPK</td>
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
            @if($jum_hari->d == 0)
            <p style="line-height: 1.5;">Atas partisipasinya dalam {!! $jadwal->nama !!} Tahun {!! $jadwal->tahun !!} yang diselenggarakan oleh {!! $sertifikat->fasilitasi !!} bekerja sama dengan Badan Pengembangan Sumber Daya Manusia Provinsi Kalimantan Timur pada tanggal {!! formatTanggal($jadwal->tgl_awal) !!} bertempat di {!! $jadwal->lokasi !!} yang meliputi, {!! $jadwal->total_jp !!} JP (Jam Pelajaran).</p>
            @else
			<p style="line-height: 1.5;">Atas partisipasinya dalam {!! $jadwal->nama !!} Tahun {!! $jadwal->tahun !!} yang diselenggarakan oleh {!! $sertifikat->fasilitasi !!} bekerja sama dengan Badan Pengembangan Sumber Daya Manusia Provinsi Kalimantan Timur dari tanggal {!! formatTanggal($jadwal->tgl_awal) !!} sampai dengan {!! formatTanggal($jadwal->tgl_akhir) !!} bertempat di {!! $jadwal->lokasi !!} yang meliputi {!! $jadwal->total_jp !!} JP (Jam Pelajaran).</p>
            @endif
        </div>
        <div id="tt1">
            <table cellspacing="0" cellpadding="0" class="mdiklat" style="line-height: 1.0;">
                <tr>
                    <td>{!! $sertifikat->tempat !!}, {!! formatTanggal($sertifikat->tanggal) !!}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 100px">{!! $sertifikat->jabatan !!} Badan Pengembangan Sumber Daya Manusia</td>
                </tr>
                <tr>
                    <td>{!! $sertifikat->nama !!}</td>
                </tr>
                <tr>
                    <td>{!! $sertifikat->pangkat !!}</td>
                </tr>
                <tr>
                    <td>NIP. {!! formatNIP($sertifikat->nip) !!}</td>
                </tr>
            </table>
        </div>
        @if(!is_null($sertifikat->spesimen))
        <div id="tt2">
            <img src="{{ storage_path('app/' . $sertifikat->spesimen) }}" height="200" />
        </div>
        @endif
    </div>
    <div class="page_break"></div>
    <div id="container2">
        <table width="100%" cellspacing="0" cellpadding="0" class="header" style="margin-top: 2cm;">
            <tbody>
                <tr>
                    <td style="text-align: center">
                        <span style="font-weight: bold; font-size: 11pt; font-family: bookman; line-height: 1.0;">AGENDA KEGIATAN</span><br>
                    </td>
                </tr>
            </tbody>
        </table>
        <table width="90%" cellspacing="0" cellpadding="2" class="header" style="margin: 30px auto; border: 2px solid;">
            <thead>
                <tr>
                    <th width="5%" style="border-right: 2px solid; border-bottom: 2px solid; padding-top: 15px; padding-bottom: 15px; vertical-align: center; text-align: center"><span style="font-weight: bold;">No.</span></th>
                    <th style="border-bottom: 2px solid; padding-top: 15px; padding-bottom: 15px; vertical-align: center; text-align: center"><span style="font-weight: bold;">Materi</span></th>
                </tr>
            </thead>
            <tbody>
            @foreach($kurikulum as $k)
                <tr>
                    <td style="border-right: 2px solid; text-align: center">
                        <span style="font-weight: bold; line-height: 1.0;">{{ $loop->iteration }}.</span>
                    </td>
                    <td style="padding-left: 10px; padding-right: 10px">
                        {{ $k->nama }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div id="tt3">
            <table cellspacing="0" cellpadding="0" class="mdiklat">
                <tr>
                    <td style="">{!! $sertifikat->tempat !!}, {!! formatTanggal($sertifikat->tanggal) !!}</td>
                </tr>
                <tr>
                    <td style="">{!! $sertifikat->jabatan2 !!}</td>
                </tr>
                @if(!is_null($sertifikat->spesimen2))
                <tr>
                    <td  style="padding-left: -75px"><img style="" src="{{ storage_path('app/' . $sertifikat->spesimen2) }}" height="150" /></td>
                </tr>
                @else
                <tr>
                    <td  style="padding-bottom: 100pxp">&nbsp;</td>
                </tr>
                @endif
                <tr>
                    <td style="">{!! $sertifikat->nama2 !!}</td>
                </tr>
                <tr>
                    <td style="">{!! $sertifikat->pangkat2 !!}</td>
                </tr>
                <tr>
                    <td style="">NIP. {!! formatNIP($sertifikat->nip2) !!}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
