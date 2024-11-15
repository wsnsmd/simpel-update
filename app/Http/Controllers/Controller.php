<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $tahun;
    protected $user;

    public function isAdmin()
    {
        if($this->user->superadmin)
            return true;

        return false;
    }

    public function isViewer()
    {
        return $this->user->viewer;
    }

    public function checkLevel()
    {
        if($this->user->superadmin)
            return 'admin';

        $group = $this->user->usergroup;

        switch($group)
        {
            case 'skpk':
            case 'pkt':
            case 'pkmf':
                $level = 'user';
                break;

            case 'kontribusi';
                $level = 'kontribusi';
                break;

            default:
                $level = 'guest';
                break;
        }

        return $level;
    }
}
