<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

use App\Mail\KirimSertifikatMailable;

class KirimEmailSertifikatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 10;
    public $timeout = 180;

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
        if(is_null($this->bcc))
            Mail::to($this->email)->send(new KirimSertifikatMailable($this->nama, $this->jadwal, $this->konten, $this->sertifikat));
        else
            Mail::to($this->email)->bcc($this->bcc)->send(new KirimSertifikatMailable($this->nama, $this->jadwal, $this->konten, $this->sertifikat));
    }
}
