<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

use App\Mail\DaftarHadirMailable;

use GuzzleHttp\Client;

class EmailDaftarHadirJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

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
        $client = new Client();
        try
        {
            // $headers = [
            //     'Authorization' => 'Bearer ' . env('MAIL_BEARER'),
            //     'Content-Type' => 'application/json'
            // ];
            // $body = [
            //     'from' => [[
            //         'email' => env('MAIL_FROM_ADDRESS'),
            //         'name' => env('MAIL_FROM_NAME')
            //     ]],
            //     'to' => [[
            //         'email' => $this->email,
            //         'name' => $this->nama
            //     ]],
            //     'template_uuid' => 'acf60173-9e96-449a-a31a-21c5c3276e7c',
            //     'template_vaiables' => [
            //     'peserta' => $this->nama,
            //     'jadwal_tipe' => $this->jadwal->tipe,
            //     'jadwal_nama' => $this->jadwal->nama,
            //     'jadwal_tanggal' => $this->jadwal->tgl_awal,
            //     'konfirmasi_url' => $this->url,
            //     'tahun' => $this->jadwal->tahun
            //     ],
            // ];
            // // print_r($body);
            // $request = new \GuzzleHttp\Psr7\Request('POST', 'https://send.api.mailtrap.io/api/send', $headers, json_encode($body));
            $response = $client->post('https://send.api.mailtrap.io/api/send', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('MAIL_BEARER'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'from' => [
                        'email' => env('MAIL_FROM_ADDRESS'),
                        'name' => env('MAIL_FROM_NAME')
                    ],
                    'to' => [[
                        'email' => $this->email,
                        'name' => $this->nama
                    ]],
                    'template_uuid' => 'acf60173-9e96-449a-a31a-21c5c3276e7c',
                    'template_vaiables' => [
                        'peserta' => $this->nama,
                        'jadwal_tipe' => $this->jadwal->tipe,
                        'jadwal_nama' => $this->jadwal->nama,
                        'jadwal_tanggal' => $this->jadwal->tgl_awal,
                        'konfirmasi_url' => $this->url,
                        'tahun' => $this->jadwal->tahun
                    ],
                ],
            ]);

            return response()->json([
                'status' => 'success',
                'data' => json_decode($response->getBody(), true)
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        // Mail::to($this->email)->send(new DaftarHadirMailable($this->nama, $this->jadwal, $this->url));
    }
}
