<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScanRedirectController extends Controller
{
    /**
     * Redirect peserta ke halaman presensi setelah scan QR.
     * Jika belum login, simpan intended URL lalu redirect ke login.
     * Route: GET /presensi/{token}
     */
    public function handle($token)
    {
        if (!Auth::guard('peserta')->check()) {
            session(['url.intended' => url('/presensi/' . $token)]);
            return redirect()->route('peserta.login');
        }

        return app()->call(
            'App\Http\Controllers\Peserta\PresensiPesertaController@show',
            ['token' => $token]
        );
    }
}
