<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class KirimSertifikatMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $jadwal;
    public $konten;
    public $sertifikat;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nama, $jadwal, $konten, $sertifikat)
    {
        //
        $this->nama = $nama;
        $this->jadwal = $jadwal; 
        $this->konten = $konten;
        $this->sertifikat = $sertifikat;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(setting()->get('from_email'), setting()->get('from_name'))
                ->subject('Sertifikat ' . $this->jadwal->nama)
                ->view('emails.sertifikat_kirim');
    }
}
