<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Socialite;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use DB;

class KeycloakAuthController extends Controller
{
    /**
     * Redirect ke Keycloak
     */
    public function redirect()
    {
        return Socialite::driver('keycloak')->scopes(['openid'])->redirect();
    }

    /**
     * Callback dari Keycloak
     */
    public function callback(Request $request)
    {
        try {
            $kcUser = Socialite::driver('keycloak')->user();
        } catch (\Exception $e) {
            return redirect('/login')
                ->withErrors('Gagal menghubungi Keycloak: ' . $e->getMessage());
        }

        // Ambil data penting dari Keycloak
        $email = $kcUser->getEmail();
        $name  = $kcUser->getName();
        $username = $kcUser->getNickName();

        // Email dan username wajib ada
        if (!$email && !$username) {
            return redirect('/login')
                ->withErrors('Email tidak ditemukan dari akun Keycloak. Hubungi admin.');
        }

        // === PERUBAHAN UTAMA ===
        // Cek apakah user dengan email ini SUDAH ADA di database lokal
        $user = User::where('username', $username)->first();

        if (!$user) {
            // User tidak terdaftar di aplikasi → login DITOLAK
            \Log::warning('Login SSO gagal: email tidak terdaftar', ['email' => $email]);

            return redirect('/login')
                ->withErrors('Anda tidak memiliki akses ke aplikasi ini. Silakan hubungi administrator untuk mendaftarkan akun Anda.');
        }

        // Optional: sinkronkan nama terbaru dari Keycloak (bisa di-comment jika tidak perlu)
        // if ($user->name !== $name) {
        //     $user->name = $name;
        //     $user->save();
        // }

        // Simpan id_token untuk Single Logout (jika kamu pakai)
        if (isset($kcUser->accessTokenResponseBody['id_token'])) {
            $request->session()->put('keycloak_id_token', $kcUser->accessTokenResponseBody['id_token']);
        }

        $user->superadmin = $kcUser->user['simpel-superadmin'];
        $user->usergroup = $kcUser->user['simpel-group'];
        $user->instansi_id = $kcUser->user['simpel-instansi'];
        $user->last_login = now();  // Atur waktu terakhir login ke waktu sekarang
        $user->last_login_ip = $request->ip();  // Ambil alamat IP
        $user->last_login_browser = $request->header('User-Agent');  // Ambil informasi browser
        $user->save();  // Simpan perubahan ke database

        $request->session()->put('apps_tahun', setting()->get('app_tahun'));
        $instansi = DB::table('instansi')->where('id', $user->instansi_id)->first();
        $tahun = DB::table('tahun')->where('aktif', true)->get();
        $request->session()->put('apps_tahuns', $tahun);
        $request->session()->put('auth_instansi', $instansi->nama);

        // Login berhasil karena user sudah terdaftar
        Auth::login($user, true); // true = remember me

        return redirect()->intended('/backend/dashboard');
    }

    /**
     * Logout dari Laravel + (opsional) Keycloak
     */
    public function logout(Request $request)
    {
        $idToken = $request->session()->pull('keycloak_id_token');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($idToken) {
            $base  = rtrim(env('KEYCLOAK_BASE_URL'), '/');
            $realm = env('KEYCLOAK_REALM');
            $clientId = env('KEYCLOAK_CLIENT_ID');
            $redirect = url('/'); // setelah logout, ke halaman depan

            $query = http_build_query([
                'post_logout_redirect_uri' => $redirect,
                'id_token_hint'            => $idToken,
                'client_id'                => $clientId,
            ]);

            $logoutUrl = "{$base}/realms/{$realm}/protocol/openid-connect/logout?{$query}";

            return redirect()->away($logoutUrl);
        }

        return redirect('/');
    }
}
