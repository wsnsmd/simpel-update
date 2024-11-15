<?php

namespace App\Policies;

use App\User;
use App\Jadwal;
use Illuminate\Auth\Access\HandlesAuthorization;

class JadwalPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, $jadwal)
    {
        return $user->usergroup === $jadwal->usergroup;
    }
}
