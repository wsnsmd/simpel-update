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

    public function dashboard()
    {
        $today = now()->toDateString();
        $user  = auth()->user();

        // ── Statistik utama ───────────────────────────────────────────────
        $statDiklatAktif = DB::table('diklat_jadwal')
            ->where('tgl_awal', '<=', $today)
            ->where('tgl_akhir', '>=', $today)
            ->where('status', 1)
            ->count();

        $statTotalPeserta = DB::table('peserta')
            ->where('batal', false)
            ->whereYear('created_at', now()->year)
            ->count();

        $statDokumenMenunggu = DB::table('peserta_dokumen')
            ->whereNull('verified_at')
            ->count();

        $statPresensiHariIni = DB::table('presensi_peserta')
            ->join('presensi_sesi', 'presensi_sesi.id', '=', 'presensi_peserta.presensi_sesi_id')
            ->whereDate('presensi_sesi.tanggal', $today)
            ->whereIn('presensi_peserta.status', ['hadir', 'terlambat'])
            ->count();

        // ── Diklat sedang berjalan ────────────────────────────────────────
        $diklatBerjalan = DB::table('diklat_jadwal')
            ->where('tgl_awal', '<=', $today)
            ->where('tgl_akhir', '>=', $today)
            ->where('status', 1)
            ->select('id', 'nama', 'tgl_awal', 'tgl_akhir', 'kuota')
            ->orderBy('tgl_awal')
            ->get()
            ->map(function($j) {
                $j->jumlah_peserta = DB::table('peserta')
                    ->where('diklat_jadwal_id', $j->id)
                    ->where('batal', false)
                    ->where('verifikasi', true)
                    ->count();
                return $j;
            });

        // ── Pendaftaran terbaru ───────────────────────────────────────────
        $pendaftaranTerbaru = DB::table('peserta')
            ->join('diklat_jadwal', 'diklat_jadwal.id', '=', 'peserta.diklat_jadwal_id')
            ->select(
                'peserta.id',
                'peserta.nama_lengkap',
                'peserta.instansi',
                'peserta.verifikasi',
                'peserta.created_at',
                'diklat_jadwal.nama as diklat_nama'
            )
            ->orderByDesc('peserta.created_at')
            ->limit(6)
            ->get();

        // ── Dokumen menunggu verifikasi per jenis ────────────────────────
        $dokumenMenunggu = DB::table('peserta_dokumen')
            ->join('jadwal_dokumen_syarat', 'jadwal_dokumen_syarat.id', '=', 'peserta_dokumen.dokumen_syarat_id')
            ->whereNull('peserta_dokumen.verified_at')
            ->select(
                'jadwal_dokumen_syarat.id as syarat_id',
                'jadwal_dokumen_syarat.nama',
                DB::raw('COUNT(*) as jumlah')
            )
            ->groupBy('jadwal_dokumen_syarat.id', 'jadwal_dokumen_syarat.nama')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        // ── Presensi sesi hari ini ────────────────────────────────────────
        $sesiHariIni = DB::table('presensi_sesi')
            ->join('diklat_jadwal', 'diklat_jadwal.id', '=', 'presensi_sesi.diklat_jadwal_id')
            ->whereDate('presensi_sesi.tanggal', $today)
            ->select(
                'presensi_sesi.id',
                'presensi_sesi.nama_materi',
                'presensi_sesi.jam_mulai',
                'presensi_sesi.jam_selesai',
                'diklat_jadwal.nama as diklat_nama',
                'diklat_jadwal.id   as jadwal_id'
            )
            ->orderBy('presensi_sesi.jam_mulai')
            ->get()
            ->map(function($s) {
                $s->hadir = DB::table('presensi_peserta')
                    ->where('presensi_sesi_id', $s->id)
                    ->whereIn('status', ['hadir', 'terlambat'])
                    ->count();
                $s->total = DB::table('presensi_peserta')
                    ->where('presensi_sesi_id', $s->id)
                    ->count();
                $s->is_aktif = \App\PresensiSesi::find($s->id)->isTokenValid();
                return $s;
            });

        return view('dashboard', compact(
            'statDiklatAktif',
            'statTotalPeserta',
            'statDokumenMenunggu',
            'statPresensiHariIni',
            'diklatBerjalan',
            'pendaftaranTerbaru',
            'dokumenMenunggu',
            'sesiHariIni'
        ));
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
            'custom_headers' => array(
                [
                    'header' => 'Reply-To',
                    'value' => 'Actual Person <test3@example.com>'
                ]
            )
        ];
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://api.smtp2go.com/v3/email/send', $headers, json_encode($body));
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();
    }
}
