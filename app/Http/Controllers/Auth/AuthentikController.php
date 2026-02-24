<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthentikAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use DB;

class AuthentikController extends Controller
{
    public function loginRedirect()
    {
        return redirect()->route('authentik.redirect');
    }

    public function redirect(Request $request)
    {
        $provider = AuthentikAuth::makeProvider();

        // scope OIDC: minimal openid, profile, email
        $authorizationUrl = $provider->getAuthorizationUrl([
            'scope' => 'openid profile email simpel_profile',
        ]);

        // simpan state ke session untuk proteksi CSRF
        $request->session()->put('authentik_oauth2state', $provider->getState());

        return redirect()->away($authorizationUrl);
    }

    public function callback(Request $request)
    {
        // cek error dari Authentik
        if ($request->has('error')) {
            return redirect('/login')->withErrors([
                'authentik' => 'Login via SSO gagal: ' . $request->get('error_description', $request->get('error')),
            ]);
        }

        // cek state
        $state = $request->get('state');
        $savedState = $request->session()->pull('authentik_oauth2state'); // hapus setelah diambil

        if (!$state || !$savedState || $state !== $savedState) {
            return redirect('/login')->withErrors([
                'authentik' => 'State tidak valid, silakan coba lagi.',
            ]);
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect('/login')->withErrors([
                'authentik' => 'Kode otorisasi tidak ditemukan.',
            ]);
        }

        $provider = AuthentikAuth::makeProvider();

        try {
            // tukar code dengan access token
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            // ambil data user dari /userinfo
            $resourceOwner = $provider->getResourceOwner($accessToken);
            $data = $resourceOwner->toArray();

            // dd($data); // pakai sekali untuk lihat struktur data dari Authentik

            $email = isset($data['email']) ? $data['email'] : null;
            $name = isset($data['name']) ? $data['name'] : ($email ?: 'User');
            $username = $data['preferred_username'];

            // Kalau kamu pakai NIP, cek claim-nya (contoh: 'nip' atau klaim custom)
            $nip = isset($data['nip']) ? $data['nip'] : null;

            // Cari atau buat user lokal
            $user = User::where('username', $username)->first();

            if (!$user) {
                $user = new User();
                $user->password = bcrypt(str_random(40));

                // // kalau di tabel users ada kolom 'nip'
                // if (schema_has_column('users', 'nip') && $nip) {
                //     $user->nip = $nip;
                // }

                // // kalau ada kolom 'password', isi dummy random (tidak dipakai untuk login SSO)
                // if (schema_has_column('users', 'password')) {
                //     $user->password = bcrypt(str_random(40));
                // }

                // $user->save();
            }

            // Login user di Laravel
            $user->name = $name;
            $user->email = $email;
            $user->username = $username;
            $user->superadmin = $data['superadmin'];
            $user->usergroup = $data['group'];
            $user->instansi_id = $data['instansi_id'];
            $user->last_login = now();  // Atur waktu terakhir login ke waktu sekarang
            $user->last_login_ip = $request->ip();  // Ambil alamat IP
            $user->last_login_browser = $request->header('User-Agent');  // Ambil informasi browser
            $user->save();  // Simpan perubahan ke database

            $request->session()->put('apps_tahun', setting()->get('app_tahun'));
            $instansi = DB::table('instansi')->where('id', $user->instansi_id)->first();
            $tahun = DB::table('tahun')->where('aktif', true)->get();
            $request->session()->put('apps_tahuns', $tahun);
            $request->session()->put('auth_instansi', $instansi->nama);

            Auth::login($user, true);

            return redirect()->intended('/backend/dashboard'); // dashboard

        } catch (\Exception $e) {
            return redirect('/login')->withErrors([
                'authentik' => 'Terjadi error saat proses SSO: ' . $e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        // 1. Logout dari sistem Laravel
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Redirect kembali ke halaman login aplikasi (setelah logout dari Authentik)
        $redirectAfterLogout = url('https://bpsdm.kaltimprov.go.id');

        // 3. URL logout di Authentik
        $authentikLogoutUrl = rtrim(env('AUTHENTIK_BASE_URL'), '/') .
            '/application/o/simpel/end-session/?post_logout_redirect_uri=';

        // 4. Redirect ke Authentik logout endpoint
        return redirect()->away($authentikLogoutUrl);
    }

    public function profileSSO(Request $request)
    {
        return redirect()->away('https://auth.bpsdmkaltim.net/if/user/#/settings;');
    }
}

/**
 * Helper sederhana untuk cek kolom ada/tidak (karena di L5.4 tidak ada Schema::hasColumn di mana-mana)
 */
if (!function_exists('schema_has_column')) {
    function schema_has_column($table, $column)
    {
        try {
            return \Schema::hasColumn($table, $column);
        } catch (\Exception $e) {
            return false;
        }
    }
}
