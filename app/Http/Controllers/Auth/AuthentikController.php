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

        $authorizationUrl = $provider->getAuthorizationUrl([
            'scope' => 'openid profile email simpel_profile',
        ]);

        $request->session()->put('authentik_oauth2state', $provider->getState());

        return redirect()->away($authorizationUrl);
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/login')->withErrors([
                'authentik' => 'Login via SSO gagal: ' . $request->get('error_description', $request->get('error')),
            ]);
        }

        $state = $request->get('state');
        $savedState = $request->session()->pull('authentik_oauth2state');

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
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $resourceOwner = $provider->getResourceOwner($accessToken);
            $data = $resourceOwner->toArray();

            $email = isset($data['email']) ? $data['email'] : null;
            $name = isset($data['name']) ? $data['name'] : ($email ?: 'User');
            $username = $data['preferred_username'];

            $nip = isset($data['nip']) ? $data['nip'] : null;

            $user = User::where('username', $username)->first();

            if (!$user) {
                $user = new User();
                $user->password = bcrypt(str_random(40));
                $user->username = $username;

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

            // Update data terbaru dari SSO setiap kali login (Sync)
            $user->name = $name;
            $user->email = $email;

            // Mapping custom attribute dari Authentik
            $user->superadmin = $data['superadmin'] ?? 0;
            $user->usergroup = $data['group'] ?? 'user';
            $user->instansi_id = $data['instansi_id'] ?? null;

            // Log akses
            $user->last_login = now();
            $user->last_login_ip = $request->ip();
            $user->last_login_browser = $request->header('User-Agent');
            $user->save();

            // Set Session Aplikasi SIMPel
            $this->setAppSessions($request, $user);

            Auth::login($user, true);

            return redirect()->intended('/backend/dashboard');

        } catch (\Exception $e) {
            dd($e);
            return redirect('/login')->withErrors([
                'authentik' => 'Gagal sinkronisasi data: ' . $e->getMessage(),
            ]);
        }
    }

    private function setAppSessions(Request $request, $user)
    {
        $request->session()->put('apps_tahun', setting()->get('app_tahun'));

        $tahun = DB::table('tahun')->where('aktif', true)->get();
        $request->session()->put('apps_tahuns', $tahun);

        if ($user->instansi_id) {
            $instansi = DB::table('instansi')->where('id', $user->instansi_id)->first();
            $request->session()->put('auth_instansi', $instansi->nama ?? 'BPSDM');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $baseUrl = rtrim(config('services.authentik.base_url'), '/');
        $returnTo = url('https://bpsdm.kaltimprov.go.id');

        $logoutUrl = "{$baseUrl}/application/o/simpel/end-session/?post_logout_redirect_uri=" . urlencode($returnTo);

        return redirect()->away($logoutUrl);
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
