<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DaftarHadirMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $peserta;
    public $jadwal_tipe;
    public $jadwal_nama;
    public $jadwal_tanggal;
    public $konfirmasi_url;
    public $tahun;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nama, $jadwal, $url)
    {
        $this->peserta = $nama;
        $this->jadwal_tipe = $jadwal->tipe;
        $this->jadwal_nama = $jadwal->nama;
        $this->jadwal_tanggal = $jadwal->tgl_awal;
        $this->konfirmasi_url = $url;
        $this->tahun = $jadwal->tahun;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(setting()->get('from_email'), setting()->get('from_name'))
            ->subject('Daftar Hadir - ' . $this->jadwal_tipe . ' ' . $this->jadwal_nama)
            ->view('emails.daftar_hadir');
    }
}
