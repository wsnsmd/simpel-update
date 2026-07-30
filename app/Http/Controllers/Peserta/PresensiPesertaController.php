<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\PresensiSesi;
use App\PresensiPeserta;
use App\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiPesertaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:peserta');
    }

    // =========================================================================
    // Halaman konfirmasi presensi setelah scan QR
    // Akses: /presensi/{token}
    // =========================================================================

    public function show($token)
    {
        $sesi = PresensiSesi::where('token', $token)->first();

        if (!$sesi) {
            return view('peserta.presensi.invalid', [
                'pesan' => 'Link presensi tidak valid atau sudah tidak berlaku.',
            ]);
        }

        // Cek apakah sesi masih berlangsung
        if (!$sesi->isTokenValid()) {
            $now  = Carbon::now();
            $sls  = Carbon::parse($sesi->tanggal->format('Y-m-d') . ' ' . $sesi->jam_selesai);
            $pesan = $now->gt($sls)
                ? 'Sesi "' . $sesi->nama_materi . '" sudah berakhir pada pukul ' . substr($sesi->jam_selesai, 0, 5) . '.'
                : 'Sesi "' . $sesi->nama_materi . '" belum dimulai. Silakan scan ulang saat sesi berlangsung.';

            return view('peserta.presensi.invalid', compact('pesan', 'sesi'));
        }

        $akun = Auth::guard('peserta')->user();
        $nip  = session('peserta_nip', $akun->nip);

        // Cari peserta yang sesuai dengan jadwal sesi ini
        $peserta = DB::table('peserta')
            ->where('nip', $nip)
            ->where('diklat_jadwal_id', $sesi->diklat_jadwal_id)
            ->where('batal', false)
            ->where('verifikasi', true)
            ->select('id', 'nama_lengkap', 'nip', 'instansi', 'kode')
            ->first();

        if (!$peserta) {
            return view('peserta.presensi.invalid', [
                'pesan' => 'Anda tidak terdaftar sebagai peserta aktif pada pelatihan ini.',
                'sesi'  => $sesi,
            ]);
        }

        // Cek apakah sudah presensi
        $sudahPresensi = DB::table('presensi_peserta')
            ->where('presensi_sesi_id', $sesi->id)
            ->where('peserta_id', $peserta->id)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->first();

        if ($sudahPresensi) {
            return view('peserta.presensi.sudah', compact('sesi', 'peserta', 'sudahPresensi'));
        }

        return view('peserta.presensi.konfirmasi', compact('sesi', 'peserta', 'token'));
    }

    // =========================================================================
    // Proses konfirmasi presensi (POST)
    // =========================================================================

    public function konfirmasi(Request $request, $token)
    {
        $sesi = PresensiSesi::where('token', $token)->first();

        if (!$sesi || !$sesi->isTokenValid()) {
            return redirect()->route('peserta.dashboard')
                ->withErrors(['presensi' => 'Sesi presensi sudah tidak aktif.']);
        }

        $akun    = Auth::guard('peserta')->user();
        $nip     = session('peserta_nip', $akun->nip);
        $now     = Carbon::now();

        $peserta = DB::table('peserta')
            ->where('nip', $nip)
            ->where('diklat_jadwal_id', $sesi->diklat_jadwal_id)
            ->where('batal', false)
            ->where('verifikasi', true)
            ->select('id', 'nama_lengkap')
            ->first();

        if (!$peserta) {
            return redirect()->route('peserta.dashboard')
                ->withErrors(['presensi' => 'Anda tidak terdaftar di pelatihan ini.']);
        }

        // Tentukan status (hadir/terlambat)
        $status = $sesi->getStatusByScanTime($now);

        // Update atau insert record presensi
        DB::table('presensi_peserta')
            ->where('presensi_sesi_id', $sesi->id)
            ->where('peserta_id', $peserta->id)
            ->update([
                'scan_at'    => $now,
                'status'     => $status,
                'ip_address' => $request->ip(),
                'updated_at' => $now,
            ]);

        return view('peserta.presensi.sukses', compact('sesi', 'peserta', 'status', 'now'));
    }
}
