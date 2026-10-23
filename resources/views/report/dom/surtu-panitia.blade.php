<html>

<head>
    <title>Surat Tugas - {{ $surtu->nomor }}</title>
    <meta name="author" content="SIMPel BPSDM Kaltim @ {!! date('Y') !!}">
    <style>
        @page {
            margin-top: 11rem;
            margin-bottom: {{ $bawah }}rem;
            margin-left: 2.5rem;
            margin-right: 2.5rem;
        }

        footer {
            width: 100%;
            position: fixed;
            bottom: 0px;
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

        .page-break + header {
            display: block;
        }

        .page_break {
            page-break-after: always;
        }

        #kop {
            width: 100%;
            position: fixed;
            top: -10rem;
            margin: 0px;
            display: block;
        }

        #container {
            margin-top: 0rem;
            margin-right: 20px;
            margin-bottom: 10px;
            margin-left: 20px;
        }

        #divparaf {
            padding-top: 0.5cm;
            padding-left: 0cm;
            width: 40%;
        }
    </style>
</head>

<body style="page-break-inside: avoid !important;">
    @php
        $page = 1;
        $counter = 1;
    @endphp
    @for($hal=1; $hal<=2; $hal++)
    <main>
        <div id="kop">
            <table width="100%" cellspacing="0" cellpadding="0">
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
        </div>
        <div id="container">
            <h4 style="text-decoration: underline;">SURAT TUGAS</h4>
            <p style="text-align: center; margin-top: 5px;">Nomor : {{ $surtu->nomor }}</p>
            <div style="text-align: center; margin-left: 10px; margin-top: 30px;">
                <table width="100%" cellspacing="0" cellpadding="4">
                    <tbody>
                        <tr>
                            <td width="15%" style="vertical-align: top">Dasar</td>
                            <td width="2%" style="vertical-align: top">:</td>
                            <td width="83%" style="vertical-align: top">{!! $surtu->dasar !!}</td>
                        </tr>
                    </tbody>
                </table>
                @foreach($pegawai as $p)
                <table width="100%" cellspacing="0" cellpadding="4">
                    <tbody>
                        <tr>
                            @if($loop->iteration == 1)
                            <td width="15%" style="vertical-align: top">Kepada</td>
                            <td width="2%" style="vertical-align: top">:</td>
                            @else
                            <td width="15%" style="vertical-align: top">&nbsp;</td>
                            <td width="2%" style="vertical-align: top">&nbsp;</td>
                            @endif
                            <td width="83%">
                                <ol start="{{ $loop->iteration }}">
                                    <li>
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="35%" style="vertical-align: top">Nama</td>
                                                <td width="3%" style="vertical-align: top">:</td>
                                                <td width="62%" style="vertical-align: top">{{ $p->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align: top">NIP</td>
                                                <td style="vertical-align: top">:</td>
                                                <td style="vertical-align: top">{{ formatNIP($p->nip) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align: top">Pangkat/Gol.</td>
                                                <td style="vertical-align: top">:</td>
                                                <td style="vertical-align: top">{{ $p->pangkat }}</td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align: top">Jabatan</td>
                                                <td style="vertical-align: top">:</td>
                                                <td style="vertical-align: top">{{ $p->jabatan }}</td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align: top">Jabatan dalam Kepanitian</td>
                                                <td style="vertical-align: top">:</td>
                                                <td style="vertical-align: top">{{ $p->keterangan }}</td>
                                            </tr>
                                        </table>
                                    </li>
                                </ol>
                            </td>
                        </tr>
                    </tbody>
                </table>
                @endforeach
                <table width="100%" cellspacing="0" cellpadding="4">
                    <tbody>
                        <tr>
                            <td width="15%" style="vertical-align: top">Untuk</td>
                            <td width="2%" style="vertical-align: top">:</td>
                            <td width="83%" style="vertical-align: top">{!! $surtu->untuk !!}</td>
                        </tr>
                    </tbody>
                </table>
                @if($template == 1)
                <table width="100%" border="0" cellspacing="0" cellpadding="3" class="text" style="page-break-before: auto;">
                @else
                <table width="100%" border="0" cellspacing="0" cellpadding="3" class="text" style="page-break-before: always;">
                @endif
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
                                @if($hal==1)
                                    <div>
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
    </main>

    @if($hal!=2)
    <div class="page_break"></div>
    @endif

    @endfor
</body>

</html>
