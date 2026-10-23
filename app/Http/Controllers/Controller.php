<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $tahun;

    protected function getUser()
    {
        return $this->user ?: Auth::user();
    }

    public function __get($key)
    {
        if ($key === 'user') {
            return $this->getUser();
        }

        return null;
    }

    public function isAdmin()
    {
        $user = $this->getUser();
        if ($user && $user->superadmin) {
            return true;
        }

        return false;
    }

    public function isViewer()
    {
        $user = $this->getUser();
        return $user ? (bool) $user->viewer : false;
    }

    public function checkLevel()
    {
        $user = $this->getUser();
        if (!$user) {
            return 'guest';
        }

        if ($user->superadmin) {
            return 'admin';
        }

        $group = $user->usergroup;

        switch ($group) {
            case 'skpk':
            case 'pkt':
            case 'pkmf':
                $level = 'user';
                break;

            case 'kontribusi':
                $level = 'kontribusi';
                break;

            default:
                $level = 'guest';
                break;
        }

        return $level;
    }
}
