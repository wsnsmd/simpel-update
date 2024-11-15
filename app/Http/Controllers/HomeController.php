<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DaftarMailable;
use Illuminate\Support\HtmlString;
use Soundasleep\Html2Text;
use GuzzleHttp\Client;
use DB;

use function GuzzleHttp\json_encode;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function mail()
    {
        // $jadwal = DB::table('v_jadwal_detail')->find(1);
        // $url = route('jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]);
        // $name = 'Wawan Setiawan';
        // Mail::to('wsnsmd@gmail.com')->send(new DaftarMailable($name, $url));

        // return 'Email was sent';
        $jadwal = DB::table('v_jadwal_detail')->find(1);
        $url = route('jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]);

        // $text = view('emails.verifikasi_wait_text', ['name' => 'Wawan Setiawan'], compact('jadwal'))->render();
        // $html = view('emails.verifikasi_wait', ['name' => 'Wawan Setiawan'], compact('jadwal'))->render();

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json'
        ];
        // $body = '{
        //     "api_key": "api-EFC7EA4860E811ED8F3CF23C91C88F4E",
        //     "html_body": ' . $html . ',
        //     "sender": "SIMPel BPSDM Kaltim <no-reply@bpsdmkaltim.net>",
        //     "subject": "Status Verifikasi",
        //     "text_body": '.$text.',
        //     "to": [
        //         "Test Person <wsnsmd@gmail.com>"
        //     ]
        // }';
        $body = [
            'api_key' => 'api-EFC7EA4860E811ED8F3CF23C91C88F4E',
            'sender' => 'SIMPel BPSDM Kaltim <no-reply@bpsdmkaltim.net>',
            'to' => [
                'Test Person <wsnsmd@gmail.com>'
            ],
            'template_id' => '9474095',
            'template_data' => [
              'peserta' => 'XXX',
              'jadwal_nama' => 'YYY',
              'konfirmasi_url' => 'https://xyz.com',
              'tahun' => '2020'
            ],
            'custom_headers' => array([
              'header' => 'Reply-To',
              'value' => 'Actual Person <test3@example.com>'
            ])
        ];
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://api.smtp2go.com/v3/email/send', $headers, json_encode($body));
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();
    }
}
