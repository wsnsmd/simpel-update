<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

use App\Mail\KirimSertifikatMailable;

use GuzzleHttp\Client;

class KirimEmailSertifikatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public $email;
    public $nama;
    public $jadwal;
    public $konten;
    public $sertifikat;
    public $bcc;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $nama, $jadwal, $konten, $sertifikat, $bcc = null)
    {
        $this->email = $email;
        $this->nama = $nama;
        $this->jadwal = $jadwal;
        $this->konten = $konten;
        $this->sertifikat = $sertifikat;
        $this->bcc = $bcc;
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
                    'bcc' => [[
                        'email' => $this->bcc,
                    ]],
                    'template_uuid' => '9d6d9661-7baa-4581-b7fb-a76052ab7700',
                    'template_vaiables' => [
                        'jadwal_nama' => $this->jadwal->nama,
                        'konten' => $this->konten,
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
        // if(is_null($this->bcc))
        //     Mail::to($this->email)->send(new KirimSertifikatMailable($this->nama, $this->jadwal, $this->konten, $this->sertifikat));
        // else
        //     Mail::to($this->email)->bcc($this->bcc)->send(new KirimSertifikatMailable($this->nama, $this->jadwal, $this->konten, $this->sertifikat));
    }
}
