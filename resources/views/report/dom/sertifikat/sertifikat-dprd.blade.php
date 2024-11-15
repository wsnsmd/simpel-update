@php
    $romawi = '';
    $pattern = '/(M{0,3}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})|I{1,3}|V|X{1,3}|L|C|D|M)$/';
    if(preg_match($pattern, $jadwal->nama, $matches)) {
        $romawi = $matches[0];
    }
@endphp

<html>

<head>
    <title>STTP - {!! $sertPeserta->nomor !!}</title>
    <meta name="author" content="IT BPSDM Prov. Kaltim @ {!! date('Y') !!}" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin-top: 0cm;
            margin-bottom: 0cm;
            margin-left: 0cm;
            margin-right: 0cm;
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
            font-family: 'Helvetica', sans-serif;
        }

        table.mdiklat td {
            font-family: 'Helvetica', sans-serif;
            font-size: 11pt;
            line-height: 1.2;
        }

        table.ttbox {
            border-collapse: collapse;
            width: 100%;
        }

        table.ttbox table, th, td {
            border: none;
            font-size: 9pt;
        }

        div {
            margin: 0 15 0 15;
        }

        p {
            font-family: 'Helvetica', sans-serif;
            font-size: 11pt;
            margin: 0;
            text-align: justify;
            line-height: 1.5;
        }

        .page_break {
            page-break-before: always;
        }

        .outer-border {
            border: 1px solid black; /* Menambahkan border pada tabel */
        }

        #container {
            margin: 0cm 2cm 0cm 2cm;
        }

        #container2 {
            margin: 0cm 2cm 0cm 2cm;
        }

        #tt1 {
            position: fixed;
            top: 14.2cm;
            left: 18cm;
            width: 450px;
        }

        #tt1-box {
            position: fixed;
            top: 16.3cm;
            left: 19cm;
            width: 420px;
        }

        #tt2 {
            position: fixed;
            top: 11.5cm;
            left: 21.6cm;
            width: 350px;
        }

        #tt2-box {
            position: fixed;
            top: 14.5cm;
            left: 19cm;
            width: 420px;
            font-family: 'Helvetica', sans-serif;
        }

        #foto {
            position: fixed;
            top: 7.8cm;
            left: 2cm;
            width: 134px;
            height: 180px;
            border: 1px solid;
        }

        .footer {
            font-family: 'Helvetica', sans-serif;
            font-size: 8pt;
            font-style: italic;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px; /* Tinggi footer */
            text-align: center;
            vertical-align: middle;
            line-height: 60px; /* Vertikal center text */
            border-top: 1px solid #000; /* Garis atas footer */
        }
    </style>
</head>

<body>
    <table width="100%" cellspacing="0" cellpadding="0" class="header" style="padding-top: 0.5cm;">
        <tbody>
            <tr>
                <td width="15%" style="text-align: center"><img src="{{ imageToBase64(public_path('/media/images/garuda.png')) }}"
                        height="120" width="120"></td>
            </tr>
        </tbody>
    </table>
    <table width="100%" cellspacing="0" cellpadding="0" class="header" style="margin-top: 0.2cm;">
        <tbody>
            <tr>
                <td style="text-align: center">
                    <span style="font-size: 11pt; font-family: 'Helvetica', sans-serif; line-height: 1.5;">PEMERINTAH PROVINSI KALIMANTAN TIMUR</span><br>

                    <span style="font-weight: bold; font-size: 14pt; font-family: 'Helvetica', sans-serif; line-height: 1.5;">S E R T I F I K A T</span><br>
                    <span style="font-size: 11pt; font-family: 'Helvetica', sans-serif; line-height: 1.5;">Nomor : {!! $sertPeserta->nomor !!}</span></td>
            </tr>
        </tbody>
    </table>

    <div id="container">
        <div style="text-align: center; margin-left: 0px; margin-right: 0px; margin-top: 20px">
			<p>Gubernur Kalimantan Timur berdasarkan Peraturan Pemerintah Nomor 12 Tahun 2017 tentang Pembinaan dan Pengawasan Penyelenggaraan Pemerintahan Daerah dan Peraturan Menteri Dalam Negeri Nomor 6 Tahun 2024 tentang Orientasi dan Pendalaman Tugas Anggota DPRD Provinsi dan Anggota DPRD Kabupaten/Kota serta ketentuan-ketentuan pelaksanaannya, menyatakan bahwa :</p>
			<table width="100%" cellspacing="0" cellpadding="0" class="info" style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                <tbody>
                    <tr>
                        <td style="vertical-align: top">
                            <table width="100%" cellspacing="0"
                                style="padding: 0;" class="mdiklat">
                                <tbody>
                                    <tr>
                                        <td rowspan="3" width="14%" style="vertical-align: top; text-align: center; height: 100px">&nbsp;</td>
                                        <td width="22%" style="">Nama</td>
                                        <td width="2%">:</td>
                                        <td width="62%">{!! $sertPeserta->nama_lengkap !!}</td>
                                    </tr>
                                    <tr>
                                        <td style="">Partai Politik</td>
                                        <td style="">:</td>
                                        <td style="">{!! $sertPeserta->satker_nama !!} Tes</td>
                                    </tr>
                                    <tr>
                                        <td style="">Instansi</td>
                                        <td style="">:</td>
                                        <td style="">{!! $sertPeserta->instansi !!}</td>
                                    </tr>
                                    <tr style="border: 1px solid">
                                        <td colspan="4" style="font-weight: bold; font-size: 14pt; padding-top: 30px; padding-bottom: 20px; vertical-align: middle; text-align: center; text-transform: uppercase">TELAH MENGIKUTI</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
			<p>Orientasi bagi Anggota Dewan Perwakilan Rakyat Daerah Kabupaten/Kota Angkatan {!! $romawi !!} Tahun {!! $jadwal->tahun !!} yang diselenggarakan oleh Badan Pengembangan Sumber Daya Manusia Provinsi Kalimantan Timur, dari tanggal {!! formatTanggal($jadwal->tgl_awal) !!} sampai dengan {!! formatTanggal($jadwal->tgl_akhir) !!} di {!! $jadwal->lokasi_kota !!}.</p>
        </div>
        <div id="foto">
            <img src="{{ storage_path('app/' . $sertPeserta->foto) }}" height="180px" width="134" />
        </div>
        <div id="tt1">
            <table cellspacing="0" cellpadding="0" class="mdiklat" border="0">
                <tr>
                    <td style="">&nbsp;</td>
                    <td style="">Ditetapkan di : {!! $sertifikat->tempat !!}, {!! formatTanggal($sertifikat->tanggal) !!}</td>
                </tr>
                <tr>
                    <td style="">&nbsp;</td>
                    <td style="">&nbsp;</td>
                </tr>
                <tr>
                    <td width="45px">a.n.</td>
                    <td style="">Gubernur Kalimantan Timur</td>
                </tr>
                <tr>
                    <td style="">&nbsp;</td>
                    <td style="padding-bottom: 20px">Sekretaris Daerah,</td>
                </tr>
            </table>
        </div>
        <div id="tt1-box">
            <table border="1" cellpadding="5px" class="ttbox" style="border: 1px solid black;">
                <tr>
                    <td width="60px" style="vertical-align: middle; text-align: center;"><img src="{{ imageToBase64(public_path('/media/images/pemprov-border.png')) }}" height="80"></td>
                    <td>
                        <span>Ditandatangani secara elektronik oleh: <br /></span>
                        <span>{!! $sertifikat->jabatan !!}<br /></span>
                        <span style="font-weight: bold">{!! $sertifikat->nama !!}<br /></span>
                        <span>{!! $sertifikat->pangkat !!}<br /></span>
                        <span>{!! $sertifikat->nip !!}<br /></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="footer">
        Dokumen ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan oleh BSrE. Sesuai UU
        ITE pasal 11, tandatangan elektronik memliki kekuatan hukum dan akibat hukum yang sah.
        </div>
    </div>
    <div class="page_break"></div>
    <div id="container2">
        <table width="100%" cellspacing="0" cellpadding="0" class="header" style="margin-top: 2cm;">
            <tbody>
                <tr>
                    <td style="text-align: center">
                        <span style="font-weight: bold; font-size: 14pt; font-family: 'Helvetica', sans-serif; line-height: 1.0;">DAFTAR MATERI PEMBELAJARAN</span><br>
                    </td>
                </tr>
            </tbody>
        </table>
        <table width="90%" cellspacing="0" cellpadding="6" class="header" style="margin: 30px auto; border: 1px solid black;">
            <thead>
                <tr>
                    <th width="8%" style="border: 1px solid black; padding-top: 15px; padding-bottom: 15px; vertical-align: center; text-align: center"><span style="font-weight: bold; font-size: 11pt">NOMOR</span></th>
                    <th style="border: 1px solid black; padding-top: 15px; padding-bottom: 15px; vertical-align: center; text-align: center"><span style="font-weight: bold; font-size: 11pt">MATERI PEMBELAJARAN</span></th>
                    <th width="15%" style="border: 1px solid black; padding-top: 15px; padding-bottom: 15px; vertical-align: center; text-align: center"><span style="font-weight: bold; font-size: 11pt">JAM PELAJARAN</span></th>
                </tr>
            </thead>
            <tbody>
            @php
                $jp = 0;
            @endphp
            @foreach($kurikulum as $k)
                <tr>
                    <td style="border: 1px solid black; text-align: center">
                        <span style="font-size: 11pt">{{ $loop->iteration }}.</span>
                    </td>
                    <td style="border: 1px solid black; padding-left: 10px; padding-right: 10px; font-size: 11pt">
                        {{ $k->nama }}
                    </td>
                    <td style="border: 1px solid black; text-align: center; font-size: 11pt">
                        {{ $k->jpk + $k->jpe }}
                    </td>
                </tr>
                @php
                    $jp += $k->jpk + $k->jpe
                @endphp
            @endforeach
                <tr>
                    <td style="border: 1px solid black; text-align: center" colspan="2">
                        <span style="font-size: 11pt; font-weight: bold">JUMLAH</span>
                    </td>
                    <td style="border: 1px solid black; text-align: center; font-size: 11pt; font-weight: bold">
                        {{ $jp }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="tt2-box">
            <span>{!! $sertifikat->tempat !!}, {!! formatTanggal($sertifikat->tanggal) !!}</span>
            <table border="1" cellpadding="5px" class="ttbox" style="border: 1px solid black; margin-top: 12px">
                <tr>
                    <td width="60px" style="vertical-align: middle; text-align: center;"><img src="{{ imageToBase64(public_path('/media/images/pemprov-border.png')) }}" height="80"></td>
                    <td>
                        <span>Ditandatangani secara elektronik oleh: <br /></span>
                        <span>{!! $sertifikat->jabatan2 !!}<br /></span>
                        <span style="font-weight: bold">{!! $sertifikat->nama2 !!}<br /></span>
                        <span>{!! $sertifikat->pangkat2 !!}<br /></span>
                        <span>{!! $sertifikat->nip2 !!}<br /></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
