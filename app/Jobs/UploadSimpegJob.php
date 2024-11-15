<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use GuzzleHttp\Client;

use DB;

class UploadSimpegJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public $peserta;
    public $jadwal;
    public $sertifikat;
    public $jenis;
    public $kategori;
    public $sub;

    public $url_sertifikat;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($peserta, $jadwal, $sertifikat, $jenis, $kategori, $sub, $url_sertifikat)
    {
        $this->peserta = $peserta;
        $this->jadwal = $jadwal;
        $this->sertifikat = $sertifikat;
        $this->jenis = $jenis;
        $this->kategori = $kategori;
        $this->sub = $sub;
        $this->url_sertifikat = $url_sertifikat;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $headers = [
            'Authorization' => 'Bearer ' . env('SIMASN_BEARER')
        ];
        $url = env('SIMASN_KIRIM_DIKLAT');

        $client = new \GuzzleHttp\Client();
        $res = $client->get($this->url_sertifikat);
        $content = $res->getBody()->getContents();
        // $content = (string) $res->getBody();
        $microtime = microtime(true);
        $microtimeString = str_replace('.', '_', (string)$microtime);

        $client = new Client(['verify' => false]);
        $options = [
            'multipart' => [
                [
                    'name' => 'nip',
                    'contents' => $this->peserta->nip
                ],
                [
                    'name' => 'jenis_diklat',
                    'contents' => $this->jenis
                ],
                [
                    'name' => 'kategori_diklat',
                    'contents' => $this->kategori
                ],
                [
                    'name' => 'sub_kategori_diklat',
                    'contents' => $this->sub
                ],
                [
                    'name' => 'nama_diklat',
                    'contents' => $this->jadwal->nama
                ],
                [
                    'name' => 'nomor_sertifikat',
                    'contents' => $this->peserta->nomor
                ],
                [
                    'name' => 'tgl_awal_diklat',
                    'contents' => $this->jadwal->tgl_awal
                ],
                [
                    'name' => 'tgl_akhir_diklat',
                    'contents' => $this->jadwal->tgl_akhir
                ],
                [
                    'name' => 'instansi_diklat',
                    'contents' => $this->jadwal->lokasi
                ],
                [
                    'name' => 'jumlah_jam',
                    'contents' => $this->jadwal->total_jp
                ],
                [
                    'name' => 'sertifikat',
                    'contents' => $content,
                    'filename' => '/sertifikat-'. $microtimeString . '.pdf',
                    'headers'  => [
                      'Content-Type' => '<Content-type header>'
                    ]
                ],
            ]
        ];
        $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers);
        $res = $client->sendAsync($request, $options)->wait();

        $body = json_decode($res->getBody());

        if($body->success)
        {
            $at = date('Y-m-d H:i:s');
            DB::table('sertifikat_peserta')->where('id', $this->peserta->spid)->update([
                'simpeg_at' => $at
            ]);
        }
    }
}
