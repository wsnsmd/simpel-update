<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Konfirmasi Peserta</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;600;700&amp;display=swap" rel="stylesheet">
  <style>
    body,
    .template-email-body {
      margin: 0;
      font-family: 'Ubuntu', Helvetica, Arial, sans-serif;
      background-color: #fafbfc;
      width: 100%;
    }

    body {
      padding: 36px 0;
    }

    .template-email-body {
      padding: 0;
    }

    .template-email-content-box {
      margin: 8px 0 0;
    }

    .template-email-content-container {
      background-color: #ffffff;
      width: 478px;
      padding: 36px 36px 20px;
      border-radius: 4px;
      box-shadow: 0 1px 2px 0 #aeb8c1ac, 0 11px 22px 0 #00000021;
      vertical-align: top;
    }

    h1,
    .template-email-title {
      margin: 0 0 18px;
      font-size: 16px;
      font-weight: bold;
      font-stretch: normal;
      font-style: normal;
      line-height: 1.15;
      letter-spacing: -0.6px;
      color: #596569;
      text-align: left;
    }

    p,
    .template-email-text {
      font-size: 16px;
      font-weight: normal;
      font-stretch: normal;
      font-style: normal;
      line-height: 1.56;
      letter-spacing: -0.12px;
      color: #596569;
      margin: 1em 0;
      text-align: left;
    }
    
    div.logo {
      height: 120px; 
      background-image: url('https://bpsdm.kaltimprov.go.id/img/bpsdm-text.png');
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
      margin-bottom: 10px;
    }

    p,
    .template-email-text.no-margin {
      margin: 1em 0 0;
    }
    
    td.template-email-button table {
      margin: 0 74px;
    }

    a.template-email-blue-action-button {
      display: block;
      width: 100%;
      text-align: center;
      border-radius: 3px;
      background-color: #32a0e8;
      font-size: 16px;
      font-weight: 600;
      font-stretch: normal;
      font-style: normal;
      line-height: 1.31;
      letter-spacing: -0.22px;
      text-decoration: none;
      color: #ffffff;
      transition: all .15s;
    }

    a.template-email-blue-action-button:hover {
      background-color: #258cd0;
    }

    strong.template-email-text-accent,
    .template-email-text-accent {
      display: block;
      font-size: 16px;
      font-weight: 600;
      font-stretch: normal;
      font-style: normal;
      line-height: 2;
      letter-spacing: -0.14px;
      color: #000000;
    }

    p.template-email-footer-text,
    .template-email-footer-text {
      font-size: 13px;
      font-weight: normal;
      font-stretch: normal;
      font-style: normal;
      line-height: 1.69;
      letter-spacing: -0.1px;
      text-align: center;
      color: #596569;
      margin-bottom: 2px;
      margin-top: 0;
    }

    a,
    .template-email-footer-text a {
      color: #596569;
      text-decoration: underline;
    }

    a:hover,
    .template-email-footer-text a:hover {
      text-decoration: none;
    }
    
    h4 {
      font-size: 14px;
      font-weight: bold;
      font-style: normal;
      letter-spacing: -0.1px;
      text-align: center;
      color: #596569;
      margin-bottom: 0;
      margin-top: 0;
    }

    @media screen and (max-width: 596px) {
      .template-email-content-container {
        max-width: 100%;
      }

      .template-email-blue-action-button {
        padding-top: 4px;
        padding-bottom: 4px;
      }

      td.template-email-button table {
        margin: 0 auto;
      }

      td.template-button-container {
        width: 100%;
        height: auto;
      }
    }

    @media screen and (max-width: 380px) {
      .template-email-content-container {
        padding: 18px 18px 2px;
      }
    }
  </style>
  <!--[if mso]><style>
    td.template-email-button table {
      margin: 0 auto;
    }
  </style><![endif]-->
</head>

<body>
  <table class="template-email-body">
    <tr>
      <td class="template-email-float-center" align="center" valign="top">
        <div class="logo"></div>
        <table class="template-email-content-box">
          <tr>
            <td class="template-email-content-container">
              <table>
                <tr>
                  <td class="template-email-content">

                    <h1 class="template-email-title">
                      <!--[if mso]><span style="font-family: sans-serif;"><![endif]-->
                      Hi {{ $peserta }},
                      <!--[if mso]></span><![endif]-->
                    </h1>
                    <p class="template-email-text" style="mso-line-height: exactly; line-height: 25px;">
                      <!--[if mso]><span style="font-family: sans-serif;"><![endif]-->
                      Proses registrasi anda hampir selesai, klik tombol Konfirmasi dibawah ini untuk menyelesaikan proses registrasi.
                      <!--[if mso]></span><![endif]-->
                    </p>
                  </td>
                </tr>
                <tr>
                  <td class="template-email-button">
                    <table>
                      <tr>
                        <td cellpadding="0" style="padding: 0;">
                          <div>
                            <!--[if mso]>
                          <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="" style="height:36px;v-text-anchor:middle;width:174px;" arcsize="10%" stroke="f" fillcolor="#32a0e8">
                            <w:anchorlock/>
                            <center style="color:#ffffff;font-family:sans-serif;font-size:16px;font-weight:bold;">
                              Get started
                            </center>
                          </v:roundrect>
                          <![endif]-->
                            <!--[if !mso]><!-->
                            <table cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center" width="174" height="36" bgcolor="#32a0e8"
                                  style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: #ffffff; display: block;box-shadow: 0px 0px 0px 1px #258cd0;"
                                  class="template-button-container">
                                  <a href="{{ $konfirmasi_url }}" class="template-email-blue-action-button"
                                    style="font-size:16px; font-weight: 600; font-family: 'Open Sans', Helvetica, Arial, sans-serif; text-decoration: none; line-height:36px; width:100%; letter-spacing: -0.22px;display:inline-block">
                                    <span style="color: #ffffff; padding-left: 10px; padding-right: 10px;">
                                      Konfirmasi
                                    </span>
                                  </a>
                                </td>
                              </tr>
                            </table>
                            <!--[endif]><!-->
                          </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <!--[if !mso]>-->
                <tr>
                  <td>
                    <hr style="margin-top: 26px; background: #dfebf6; border: 1px solid #dfebf6;">
                  </td>
                </tr>
                <!--<![endif]-->
                <tr>
                  <td>
                    <p class="template-email-text no-margin" style="mso-line-height: exactly; line-height: 25px;">
                      <!--[if mso]><span style="font-family: sans-serif;"><![endif]-->
                      <small>Jika anda memiliki masalah dengan tombol diatas, salin dan tempel tautan/link dibawah pada
                        peramban web anda.
                        <br>
                        {{ $konfirmasi_url }}
                      </small>
                      <!--[if mso]></span><![endif]-->
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <table class="template-email-footer">
          <tr>
            <td style="padding-top: 24px;">
              <center>
                <p class="template-email-footer-text">
                  <!--[if mso]><span style="font-family: sans-serif;"><![endif]-->
                  Hak Cipta © {{ $tahun }}. BPSDM Prov. Kaltim
                  <!--[if mso]></span><![endif]-->
                </p>
              </center>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>