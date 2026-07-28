<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthentikAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Peserta;
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

            // Penentu percabangan: cek field 'groups' dari JWT payload
            $groups = $data['groups'] ?? [];

            if (in_array('peserta', $groups)) {
                return $this->loginAsPeserta($request, $data);
            }

            return $this->loginAsAdmin($request, $data);

        } catch (\Exception $e) {
            \Log::error('SSO callback error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect('/login')->withErrors([
                'authentik' => 'Gagal memproses login. Silakan coba lagi atau hubungi admin.',
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Login sebagai Admin / Operator
    // -------------------------------------------------------------------------

    private function loginAsAdmin(Request $request, array $data)
    {
        $username = $data['preferred_username'];
        $email = $data['email'] ?? null;
        $name = $data['name'] ?? ($email ?: 'User');

        $user = User::where('username', $username)->first();

        if (!$user) {
            $user = new User();
            $user->username = $username;
            $user->password = bcrypt(str_random(40));
        }

        $user->name = $name;
        $user->email = $email;
        $user->superadmin = $data['superadmin'] ?? 0;
        $user->usergroup = $data['group'] ?? 'user';
        $user->instansi_id = $data['instansi_id'] ?? null;

        $user->last_login = now();
        $user->last_login_ip = $request->ip();
        $user->last_login_browser = $request->header('User-Agent');
        $user->save();

        $this->setAppSessions($request, $user);

        Auth::login($user, true);

        return redirect()->intended('/backend/dashboard');
    }

    // -------------------------------------------------------------------------
    // Login sebagai Peserta
    // -------------------------------------------------------------------------

    private function loginAsPeserta(Request $request, array $data)
    {
        // Untuk akun peserta, preferred_username diisi dengan NIP
        $nip = trim($data['preferred_username'] ?? '');

        if (!$nip) {
            \Log::warning('Login peserta gagal: preferred_username (NIP) kosong', ['data' => $data]);

            return redirect('/login')->withErrors([
                'authentik' => 'Data NIP tidak ditemukan pada akun SSO Anda. Hubungi admin BPSDM.',
            ]);
        }

        // Ambil satu baris peserta aktif terbaru sebagai representasi session login.
        // Dashboard nanti akan query ulang semua riwayat berdasarkan NIP yang sama.
        $peserta = Peserta::where('nip', $nip)
            ->where('batal', false)
            ->orderByDesc('created_at')
            ->first();

        if (!$peserta) {
            \Log::warning('Login peserta gagal: NIP tidak ditemukan di tabel peserta', [
                'nip' => $nip,
                'name' => $data['name'] ?? '-',
            ]);

            return redirect('/login')->withErrors([
                'authentik' => 'NIP Anda tidak ditemukan sebagai peserta terdaftar di SIMPel. Silakan hubungi admin BPSDM.',
            ]);
        }

        Auth::guard('peserta')->login($peserta);

        // Simpan NIP di session — dipakai DashboardController untuk query semua riwayat
        $request->session()->put('peserta_nip', $nip);
        $request->session()->put('peserta_name', $data['name'] ?? $peserta->nama_lengkap);

        return redirect()->route('peserta.dashboard');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

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

    public function logoutPeserta(Request $request)
    {
        Auth::guard('peserta')->logout();
        $request->session()->forget(['peserta_nip', 'peserta_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('peserta.login');
    }

    public function profileSSO(Request $request)
    {
        return redirect()->away('https://auth.bpsdmkaltim.net/if/user/#/settings;');
    }
}

/**
 * Helper cek kolom ada/tidak (kompatibel Laravel 5.6)
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
