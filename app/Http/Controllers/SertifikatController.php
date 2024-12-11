<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App;
use DB;
use Storage;

class SertifikatController extends Controller
{
    //
    public function show($peserta, $jadwal, $sertifikat, $email)
    {
        $cekSertifikat = DB::table('sertifikat_peserta')->where('id', $sertifikat)->first();

        if(is_null($cekSertifikat))
            abort(404);

        $cek = DB::table('v_sertifikat')
                    ->where('id', $peserta)
                    ->where('sertifikat_id', $cekSertifikat->sertifikat_id)
                    ->first();

        if($cek->id == $peserta && $cek->diklat_jadwal_id == $jadwal && $cek->spid == $sertifikat && str_slug($cek->email) == $email)
        {
            $sertPeserta = DB::table('v_sertifikat')
                        //->select('nip', 'nama_lengkap', 'tmp_lahir', 'tgl_lahir', 'jabatan', 'foto', 'instansi', 'satker_nama', 'diklat_jadwal_id', 'pangkat', 'golongan', 'nomor', 'sertifikat_id', 'spesimen_kiri', 'spesimen_bawah', 'upload')
                        ->select('nip', 'nama_lengkap', 'tmp_lahir', 'tgl_lahir', 'jabatan', 'foto', 'instansi', 'satker_nama', 'sebagai', 'diklat_jadwal_id', 'pangkat', 'golongan', 'nomor', 'kualifikasi', 'status', 'sertifikat_id', 'spesimen_kiri', 'spesimen_bawah', 'spesimen2_kiri', 'spesimen2_bawah', 'upload')
                        ->where('spid', $sertifikat)
                        ->first();

            $sertifikat = DB::table('sertifikat')
                        //->select('tempat', 'tanggal', 'jabatan', 'nama', 'pangkat', 'nip', 'diklat_jadwal_id', 'spesimen', 'tsid', 'is_upload', 'fasilitasi')
                        ->select('tempat', 'tanggal', 'jabatan', 'nama', 'pangkat', 'nip', 'jabatan2', 'nama2', 'pangkat2', 'nip2', 'diklat_jadwal_id', 'spesimen', 'spesimen2', 'tsid', 'is_upload', 'fasilitasi', 'barcode')
                        ->where('id', $sertPeserta->sertifikat_id)
                        ->first();

            $jadwal = DB::table('v_jadwal_detail')
                        //->select('nama', 'tahun', 'tipe', 'tgl_awal', 'tgl_akhir', 'kelas', 'total_jp', 'lokasi', 'lokasi_kota')
                        ->select('nama', 'tahun', 'tipe', 'tgl_awal', 'tgl_akhir', 'kelas', 'total_jp', 'lokasi', 'lokasi_kota', 'kurikulum_id')
                        ->where('id', $sertifikat->diklat_jadwal_id)
                        ->first();

            $kurikulum = DB::table('mapel')
                        ->select('nama', 'jpk', 'jpe')
                        ->where('kurikulum_id', $jadwal->kurikulum_id)
                        ->get();

            if($sertifikat->is_upload)
            {
                return Storage::response($sertPeserta->upload);
            }

            $template = DB::table('sertifikat_template')->where('id', $sertifikat->tsid)->first();

            if(is_null($sertPeserta->foto))
            {
                $sertPeserta->foto = 'media/avatars/avatar8.jpg';
            }

            ini_set('memory_limit', '1024M');
            set_time_limit(30);

            $papersize = 'a4';
            $paperorientation = 'landscape';
            //$filename = 'Surat Tugas - ' . $fasilitator->nama . '-' . time();
            $filename = 'Sertifikat - ' . $sertPeserta->nomor;

            $view = view('report.dom.sertifikat.' . $template->file, compact('sertPeserta', 'sertifikat', 'jadwal', 'kurikulum'));
            $pdf = App::make('dompdf.wrapper');
            $pdf->setOptions(['dpi' => '120', 'isRemoteEnabled' => true, 'chroot' => realpath(base_path()) ]);
            $pdf->loadHTML($view);
            $pdf->setPaper($papersize, $paperorientation);

            return $pdf->stream($filename.'.pdf');
        }

        abort(404);
    }

    public function cek(Request $request)
    {
        if ($request->has('hash'))
        {
            $hash = $request->hash;
            $sertifikat_peserta = DB::table('sertifikat_peserta')
                        ->whereRaw('SHA1(nomor) = ?', [$hash])
                        ->first();
            if(is_null($sertifikat_peserta))
                return redirect()->route('sertifikat.cek')->with('error', 'Data sertifikat tidak ditemukan!');

            $peserta = DB::table('v_sertifikat')
                        ->where('spid', $sertifikat_peserta->id)
                        ->first();
            $sertifikat = DB::table('sertifikat')
                        ->where('id', $sertifikat_peserta->sertifikat_id)
                        ->first();
            $jadwal = DB::table('v_jadwal_detail')
                        ->select('nama', 'tahun', 'tipe', 'tgl_awal', 'tgl_akhir', 'kelas', 'total_jp', 'lokasi', 'lokasi_kota', 'kurikulum_id')
                        ->where('id', $sertifikat->diklat_jadwal_id)
                        ->first();
            return view('frontend.sertifikat-cek', compact('peserta', 'sertifikat_peserta', 'jadwal'));
        }

        return view('frontend.sertifikat-cek');
    }

    public function postCek(Request $request)
    {
        $request->validate([
            'nomor' => 'required',
            'captcha' => 'required|captcha',
        ]);
        $nomor = str_replace(' ', '', $request->nomor);
        $sertifikat_peserta = DB::table('sertifikat_peserta')
                        ->where(DB::raw("REPLACE(nomor, ' ', '')"), $nomor)
                        ->first();

        if(is_null($sertifikat_peserta))
            return redirect()->back()->with('error', 'Data sertifikat tidak ditemukan!');

        $peserta = DB::table('v_sertifikat')
                        ->where('spid', $sertifikat_peserta->id)
                        ->first();
        $sertifikat = DB::table('sertifikat')
                        ->where('id', $sertifikat_peserta->sertifikat_id)
                        ->first();
        $jadwal = DB::table('v_jadwal_detail')
                        ->select('nama', 'tahun', 'tipe', 'tgl_awal', 'tgl_akhir', 'kelas', 'total_jp', 'lokasi', 'lokasi_kota', 'kurikulum_id')
                        ->where('id', $sertifikat->diklat_jadwal_id)
                        ->first();
        return view('frontend.sertifikat-cek', compact('peserta', 'sertifikat_peserta', 'jadwal'));
    }
}
