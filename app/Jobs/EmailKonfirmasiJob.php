<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

use App\Mail\KonfirmasiMailable;

use GuzzleHttp\Client;

class EmailKonfirmasiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 10;
    public $timeout = 180;

    public $nama;
    public $email;
    public $jadwal;
    public $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nama, $email, $jadwal, $url)
    {
        $this->nama = $nama;
        $this->email = $email;
        $this->jadwal = $jadwal;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json'
        ];
        $body = [
            'api_key' => env('MAIL_API_SEND'),
            'sender' => 'SIMPel BPSDM Kaltim <no-reply@bpsdmkaltim.net>',
            'to' => [
                '<' . $this->email . '>'
            ],
            'template_id' => '9474095',
            'template_data' => [
              'peserta' => $this->nama,
              'jadwal_tipe' => $this->jadwal->tipe,
              'jadwal_nama' => $this->jadwal->nama,
              'konfirmasi_url' => $this->url,
              'tahun' => $this->jadwal->tahun
            ],
            'custom_headers' => array([
              'header' => 'Reply-To',
              'value' => $this->jadwal->panitia_nama . ' <' . $this->jadwal->panitia_email . '>'
            ])
        ];
        // print_r($body);
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://api.smtp2go.com/v3/email/send', $headers, json_encode($body));
        $res = $client->sendAsync($request)->wait();
        */
        Mail::to($this->email)->send(new KonfirmasiMailable($this->nama, $this->jadwal, $this->url));
    }
}