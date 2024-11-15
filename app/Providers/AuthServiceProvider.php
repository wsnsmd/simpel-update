<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Jadwal;
use DB;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function ($user) {
            return $user->superadmin;
        });

        Gate::define('isUser', function ($user) {
            return ($user->superadmin || $user->usergroup === 'skpk' || $user->usergroup === 'pkt' || $user->usergroup === 'pkmf');
        });

        Gate::define('isPKMF', function ($user) {
            return ($user->superadmin || $user->usergroup === 'pkmf');
        });

        Gate::define('isCreator', function ($user, $data) {
            if($user->superadmin)
                return true;
                
            return ($user->usergroup === $data->usergroup && $user->instansi_id == 1);
        });

        Gate::define('isKontribusi', function ($user) {
            if($user->superadmin)
                return true;
                
            return ($user->usergroup === 'kontribusi');
        });

        Gate::define('isKelasKontribusi', function($user, $jadwal) {
            if($user->superadmin)
                return true;

            if($user->usergroup === 'kontribusi')
            {
                $instansi = session('auth_instansi');
                return ($instansi === $jadwal->kelas);
            }

            return false;
        });
    }
}
