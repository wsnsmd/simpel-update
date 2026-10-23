<html>

<head>
    <title>Surat Tugas - {{ $surtu->nomor }}</title>
    <meta name="author" content="SIMPel BPSDM Kaltim @ {!! date('Y') !!}">
    <style>
        @page {
            margin-top: 1em;
            margin-bottom: 2.5em;
            margin-left: 2.5em;
            margin-right: 2.5em;
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
            font-family: 'Helvetica';
        }

        table {
            border: 0px;
            border-collapse: collapse;
        }

        table.info td {
            font-size: 8pt;
        }

        table.rinci {
            border-collapse: collapse;
        }

        table.rinci th,
        table.rinci td {
            border: 1px solid black;
            padding: 4;
            font-size: 10pt;
        }

        h2 {
            font-weight: bold;
            font-size: 14pt;
            text-align: center;
            text-decoration: underline;
        }

        h3 {
            font-weight: bold;
            font-size: 14pt;
            text-align: center;
            text-decoration: underline;
            margin-top: 0px;
            margin-bottom: 40px;
        }

        h4 {
            font-weight: bold;
            font-size: 12pt;
            text-align: center;
            margin: 0px;
        }

        h4.total {
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
            margin: 2px 0;
        }

        div {
            margin: 0 15 0 15;
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

        hr.garis {
            border-top: 1px black;
            margin: 50px 30px 10px 30px;
        }

        hr.style2 {
            margin-top: 0.5em;
            margin-left: auto;
            margin-right: auto;
            border-top: 3px double;
        }

        p {
            margin: 0;
            text-align: justify;
        }

        ol {
            margin: 0;
            padding-left: 1.5rem;
        }

        p.text {
            margin-top: 1em;
            margin-bottom: 1em;
        }

        p.note {
            margin: 0;
            font-size: 10pt;
        }

        footer {
            position: fixed;
            bottom: 20px;
            left: 0px;
            right: 0px;
            height: 100px;
            text-align: right;
        }

        .qr-code {
            position: fixed;
            bottom: 120px;
            float: right
        }

        .text {
            font-size: 11.5pt;
        }

        table.paraf td, table.paraf th {
            font-size: 8pt;
            vertical-align: top;
            text-align: left;
        }

        .page_break {
            page-break-before: always;
        }

        #container {
            margin: 10px 20px;
        }

        #divparaf {
            padding-top: 0.5cm;
            padding-left: 0cm;
            width: 50%;
        }
    </style>
</head>

<body>
    @for($hal=1; $hal<=2; $hal++)
    <table width="100%" cellspacing="0" cellpadding="0" class="header">
        <tbody>
            <tr>
                <td width="15%" style="text-align: center"><img src="{{ imageToBase64(public_path('/media/images/logo_pemprov_bw.jpg')) }}"
                        height="150" width="150"></td>
                <td width="85%" style="text-align: center">
                    <span style="font-weight: bold; font-size: 16pt">PEMERINTAH PROVINSI KALIMANTAN TIMUR</span><br>
                    <span style="font-weight: bold; font-size: 18pt">BADAN PENGEMBANGAN SUMBER DAYA MANUSIA</span><br>
                    <span style="font-size: 11.5pt">Jalan H.A.M.M Rifaddin No. 88 Samarinda Seberang</span><br>
                    <span style="font-size: 11.5pt">Telp: 0541 - 7270207 Fax : 0541 7270208, email : <span style="text-decoration: underline">bpsdm@kaltimprov.go.id</span>
                        /</span><br>
                    <span style="font-size: 11.5pt"><span style="text-decoration: underline">bpsdm.kaltimprov@gmail.com</span>
                        website : http://bpsdm.kaltimprov.go.id</span><br>
                </td>
            </tr>
        </tbody>
    </table>

    <hr class="style2">

    <div id="container">
        <h4 style="text-decoration: underline;">SURAT TUGAS</h4>
        <p style="text-align: center; margin-top: 5px;">Nomor : {{ $surtu->nomor }}</p>
        <div style="text-align: center; margin-left: 10px; margin-top: 30px">
            <table width="100%" cellspacing="0" cellpadding="4">
                <tbody>
                    <tr>
                        <td width="15%" style="vertical-align: top">Dasar</td>
                        <td width="2%" style="vertical-align: top">:</td>
                        <td width="83%" style="vertical-align: top">{!! $surtu->dasar !!}</td>
                    </tr>
                    <tr>
                        <td width="15%" style="vertical-align: top">Kepada</td>
                        <td width="2%" style="vertical-align: top">:</td>
                        <td width="83%">
                            @if(count($pegawai) <= 1)
                                @foreach($pegawai as $p)
                                <table width="100%" cellspacing="0" cellpadding="0" style="margin-left: 16px">
                                    <tr>
                                        <td width="20%">Nama</td>
                                        <td width="3%">:</td>
                                        <td width="77%">{{ $p->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>NIP</td>
                                        <td>:</td>
                                        <td>{{ formatNIP($p->nip) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pangkat/Gol.</td>
                                        <td>:</td>
                                        <td>{{ $p->pangkat }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jabatan</td>
                                        <td>:</td>
                                        <td>{{ $p->jabatan }}</td>
                                    </tr>
                                </table>
                                @endforeach
                            @else
                                <ol>
                                @foreach($pegawai as $p)
                                <li>
                                    <table width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="20%">Nama</td>
                                            <td width="3%">:</td>
                                            <td width="77%">{{ $p->nama }}</td>
                                        </tr>
                                        <tr>
                                            <td>NIP</td>
                                            <td>:</td>
                                            <td>{{ formatNIP($p->nip) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Pangkat/Gol.</td>
                                            <td>:</td>
                                            <td>{{ $p->pangkat }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jabatan</td>
                                            <td>:</td>
                                            <td>{{ $p->jabatan }}</td>
                                        </tr>
                                    </table>
                                </li>
                                @endforeach
                                </ol>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="15%" style="vertical-align: top">Untuk</td>
                        <td width="2%" style="vertical-align: top">:</td>
                        <td width="83%" style="vertical-align: top">{!! $surtu->untuk !!}</td>
                    </tr>
                </tbody>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
                <tbody>
                    <tr>
                        <td width="60%">&nbsp;</td>
                        <td width="40%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td style="text-align: left">
                            Ditetapkan di {{ $surtu->tempat }} <br>
                            pada tanggal {!! formatTanggal($surtu->tanggal) !!} <br><br>
                            @if( $surtu->an)
                            a.n. Kepala<br>
                            @endif
                            {!! $surtu->jabatan !!},
                        </td>
                    </tr>
                    <tr>
                        <td height="50"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
						</td>
                        <td style="text-align: left"><span style="text-decoration: underline">
                            <strong>{!! $surtu->nama !!}</strong></span><br>
                            {!! $surtu->pangkat !!}<br>
                            NIP. {!! formatNIP($surtu->nip) !!}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if($hal==1)
    <div id="divparaf">
        <table width="100%" border="1" cellspacing="0" cellpadding="3" class="paraf">
            <thead>
                <tr>
                    <th width="6%" style="text-align:center">No</th>
                    <th width="34%" style="text-align:center">Nama</th>
                    <th width="42%" style="text-align:center">Jabatan</th>
                    <th width="13%" style="text-align:center">Paraf</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center">1</td>
                    <td>{!! $surtu->paraf1_nama !!}</td>
                    <td>{!! $surtu->paraf1_jabatan !!}</td>
                    <td>&nbsp;</td>
                </tr>
                @if(!is_null($surtu->paraf2_nama))
                <tr>
                    <td style="text-align:center">2</td>
                    <td>{!! $surtu->paraf2_nama !!}</td>
                    <td>{!! $surtu->paraf2_jabatan !!}</td>
                    <td>&nbsp;</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    @if($hal!=2)
    <div class="page_break"></div>
    @endif

    @endfor
</body>

</html>
