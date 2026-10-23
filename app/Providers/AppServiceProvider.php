<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('isAdmin', function ($user) {
            return $user->superadmin;
        });

        Gate::define('isViewer', function ($user) {
            return $user->viewer;
        });

        Gate::define('isUser', function ($user) {
            return ($user->superadmin || $user->usergroup === 'skpk' || $user->usergroup === 'pkt' || $user->usergroup === 'pkmf');
        });

        Gate::define('isPKMF', function ($user) {
            return ($user->superadmin || $user->usergroup === 'pkmf');
        });

        Gate::define('isCreator', function ($user, $data) {
            if ($user->superadmin) {
                return true;
            }

            return ($user->usergroup === $data->usergroup && $user->instansi_id == 1);
        });

        Gate::define('isKontribusi', function ($user) {
            if ($user->superadmin) {
                return true;
            }

            return ($user->usergroup === 'kontribusi');
        });

        Gate::define('isKelasKontribusi', function ($user, $jadwal) {
            if ($user->superadmin) {
                return true;
            }

            if ($user->usergroup === 'kontribusi') {
                $instansi = session('auth_instansi');
                return ($instansi === $jadwal->kelas);
            }

            return false;
        });
    }
}
