<html>

<head>
    <title>Surat Penugasan Widyaiswara</title>
    <meta name="author" content="SP Widyaiswara @ {!! date('Y') !!}">
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
        }

        table {
            border: 0px;
            border-collapse: collapse;
        }

        table.header td {
            font-family: 'Times New Roman', Times, serif;
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
        <h4>SURAT TUGAS</h4>
        <h4>MELAKSANAKAN KEGIATAN WIDYAISWARA</h4>
        <p style="text-align: center; margin-top: 5px">Nomor : {!! $nomor !!}</p>
        <div style="text-align: center; margin-left: 10px; margin-top: 30px">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                        <td>Yang bertanda tangan di bawah ini :</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">
                            <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-left: 30px; padding: 4px 0">
                                <tbody>
                                    <tr>
                                        <td width="25%">Nama</td>
                                        <td width="2%">:</td>
                                        <td width="73%">
                                            <strong>{!! $tandatangan->nama !!}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>NIP</td>
                                        <td>:</td>
                                        <td>
                                            {!! formatNIP($tandatangan->nip) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pangkat/Gol. Ruang</td>
                                        <td>:</td>
                                        <td>
                                            {!! $tandatangan->pangkat !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jabatan</td>
                                        <td>:</td>
                                        <td>
                                            {!! $tandatangan->jabatan !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Unit Kerja</td>
                                        <td>:</td>
                                        <td>BPSDM Provinsi Kalimantan Timur</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>Menugaskan :</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-left: 30px; padding: 4px 0">
                                <tbody>
                                    <tr>
                                        <td width="25%">Nama</td>
                                        <td width="2%">:</td>
                                        <td width="73%"><strong>{!! $fasilitator->nama !!}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>NIP</td>
                                        <td>:</td>
                                        <td>{!! is_null($fasilitator->nip) ? '-' : formatNIP($fasilitator->nip) !!}</td>
                                    </tr>
                                    <tr>
                                        <td>Pangkat/Gol. Ruang</td>
                                        <td>:</td>
                                        <td>{!! is_null($fasilitator->pangkat) ? '-' : $fasilitator->pangkat !!}</td>
                                    </tr>
                                    <tr>
                                        <td>Jabatan</td>
                                        <td>:</td>
                                        <td>{!! $fasilitator->jabatan !!}</td>
                                    </tr>
                                    <tr>
                                        <td>Unit Kerja</td>
                                        <td>:</td>
                                        <td>{!! is_null($fasilitator->instansi) ? 'BPSDM Provinsi Kalimantan Timur' : $fasilitator->instansi !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>Untuk melaksanakan kegiatan dengan rincian sebagai berikut :</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table width="100%" class="rinci">
                <thead>
                    <tr>
                        <th width="2%" style="text-align: center;">No</th>
                        <th width="27%" style="text-align: center">Uraian Kegiatan</th>
                        <th width="5%" style="text-align: center">Kode Butir Kegiatan</th>
                        <th width="11%" style="text-align: center">Tempat / Instansi</th>
                        <th width="13%" style="text-align: center">Tanggal, Bulan, Tahun</th>
                        <th width="15%" style="text-align: center">Jam</th>
                        <th width="5%" style="text-align: center">Jumlah Vol Kegiatan</th>
                        <th width="22%" style="text-align: center">Ket.</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; font-size: 8pt; padding: 1px; margin: 0px">1</td>
                        <td style="text-align: center; font-size: 8pt; padding: 1px; margin: 0px">2</td>
                        <td style="text-align: center; font-size: 8pt; padding: 1px; margin: 0px">3</td>
                        <td style="text-align: center; font-size: 8pt; padding: 1px; margin: 0px">4</td>
                        <td style="text-align: center; font-size: 8pt; padding: 1px; margin: 0px">5</td>
                        <td style="text-align: center; font-size: 8pt; padding: 1px; margin: 0px">6</td>
                        <td style="text-align: center; font-size: 8pt; padding: 1px; margin: 0px">7</td>
                        <td style="text-align: center; font-size: 8pt; padding: 1px; margin: 0px">8</td>
                    </tr>
                    <?php $total_jp = 0; ?>
                    @foreach ($jadwal as $j)
                    <tr>
                        <td style="text-align: center; vertical-align: top">{!! $loop->iteration !!}.</td>
                        @if($loop->first)
                        <td style="text-align: left; vertical-align: top">{!! $j->nama !!}</td>
                        @else
                        <td style="text-align: left; vertical-align: top">Lanjutan</td>
                        @endif
                        <td style="text-align: center; vertical-align: top">{!! $fasilitator->butir !!}</td>
                        <td style="text-align: center; vertical-align: top">{!! $lokasi->lokasi !!}</td>
                        <td style="text-align: center; vertical-align: top">{!! formatTgl($j->tanggal) !!}</td>
                        <td style="text-align: center; vertical-align: top">{!! formatJam($j->jam_mulai) . ' - ' . formatJam($j->jam_akhir) !!}</td>
                        <td style="text-align: center; vertical-align: top">{!! $j->jp !!} JP</td>
                        @if($loop->first)
                        <td style="text-align: left; vertical-align: top" rowspan="{!! $loop->count !!}">{!!
                            $lokasi->nama !!}</td>
                        @endif
                    </tr>
                    <?php $total_jp += $j->jp; ?>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <h4 class="total">Total Vol Kegiatan (JP)</h4>
                        </td>
                        <td style="text-align: center">{!! $total_jp !!} JP</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table width="100%" cellspacing="0" cellpadding="2">
                <tbody>
                    <tr>
                        <td width="100%">Demikian surat tugas ini dibuat, untuk dapat dipergunakan sebagaimana
                            mestinya.</td>
                    </tr>
                </tbody>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
                <tbody>
                    <tr>
                        <td width="50%">&nbsp;</td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td style="text-align: center">
                            Samarinda, {!! formatTanggal($tandatangan->tanggal) !!} <br><br>
                            @if( $tandatangan->an)
                            a.n. Kepala<br>                                                        
                            @endif
                            {!! $tandatangan->jabatan !!},
                        </td>
                    </tr>
                    <tr>
                        <td height="50"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
						@if($hal==1)
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
									<td>{!! $tandatangan->paraf1_nama !!}</td>
									<td>{!! $tandatangan->paraf1_jabatan !!}</td>
									<td>&nbsp;</td>
								</tr>
								@if(!is_null($tandatangan->paraf2_nama))
								<tr>
									<td style="text-align:center">2</td>
									<td>{!! $tandatangan->paraf2_nama !!}</td>
									<td>{!! $tandatangan->paraf2_jabatan !!}</td>
									<td>&nbsp;</td>
								</tr>
								@endif
							</tbody>
						</table>
						@endif
						</td>
                        <td style="text-align: center"><span style="text-decoration: underline">
                            <strong>{!! $tandatangan->nama !!}</strong></span><br>
                            {!! $tandatangan->pangkat !!}<br>
                            NIP. {!! formatNIP($tandatangan->nip) !!}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- <footer>
        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(120)->backgroundColor(255,255,255)->generate($qrcode)) !!} ">
    </footer> --}}

    @if($hal!=2)
    <div class="page_break"></div>
    @endif
    
    @endfor
</body>

</html>
