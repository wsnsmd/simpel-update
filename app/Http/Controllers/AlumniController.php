<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumniController extends Controller
{
    public function index()
    {
        $jenis = DB::table('diklat_jenis')->orderBy('nama')->get();
        $tahun = DB::table('tahun')->orderby('tahun', 'desc')->get();
        $alumni = DB::table('v_front_alumni')->where('id', 0)->get();

        return view('frontend.alumni', compact('jenis', 'tahun', 'alumni'));
    }

    public function cari(Request $request) 
    {
        $sql = "SELECT * FROM v_front_alumni";
        $where = array();

        if(!is_null($request->nip))
            $where[] = " nip='" . $request->nip . "'";

        if($request->has('jenis'))
        {
            if($request->jenis > 0)
                $where[] = " diklat_jenis_id=" . $request->jenis;
        }

        if(!empty($request->tahun))
        {
            $where[] = " tahun=" . $request->tahun;
        }

        if(count($where) > 0)
            $sql .= " WHERE" . implode(" AND", $where);
        
        $alumni = DB::select($sql);

        return view('frontend.alumni_cari', compact('alumni'));
    }
}
