<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AppSettings;

class BerandaController extends Controller
{
    public function __construct()
    {
        $this->tahun = AppSettings::get('app_tahun');
    }

    public function index()
    {
        //
        $jadwal = DB::table('v_front_beranda')->where('status', 2)->take(10)->get();
        $tahun = $this->tahun;

        return view('frontend.beranda', compact('jadwal', 'tahun'));
    }
}
