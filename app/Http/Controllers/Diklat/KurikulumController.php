<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Gate;

class KurikulumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:isUser');

        $this->user = Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $level = $this->checkLevel();

        $kurikulum = [];

        switch($level)
        {
            case 'admin':
                $kurikulum = DB::table('kurikulum')
                                ->join('diklat_jenis', 'kurikulum.diklat_jenis_id', '=', 'diklat_jenis.id')
                                ->select('kurikulum.*', 'diklat_jenis.nama as jenis_diklat')
                                ->orderBy('kurikulum.nama')
                                ->get();
                break;
            case 'user':
                $kurikulum = DB::table('kurikulum')
                                ->join('diklat_jenis', 'kurikulum.diklat_jenis_id', '=', 'diklat_jenis.id')
                                ->select('kurikulum.*', 'diklat_jenis.nama as jenis_diklat')
                                ->where('kurikulum.usergroup', $this->user->usergroup)
                                ->orderBy('kurikulum.nama')
                                ->get();
                break;
        }

        return view('backend.diklat.kurikulum.index', compact('kurikulum'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('isUser'))
        {
            abort(403);
        }

        $jdiklat = DB::table('diklat_jenis')->orderBy('nama')->get();

        return view('backend.diklat.kurikulum.create', compact('jdiklat'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('isUser'))
        {
            abort(403);
        }

        $validator = $request->validate([
            'nama' => 'required',
            'j_diklat' => 'required',
            'j_belajar' => 'required',
            'jp' => 'required|numeric',
        ]);

        try
        {
            $usergroup = Auth::user()->usergroup;

            DB::table('kurikulum')->insert([
                'diklat_jenis_id' => $request->j_diklat,
                'nama' => $request->nama,
                'jenis_belajar' => $request->j_belajar,
                'total_jp' => $request->jp,
                'usergroup' => $usergroup,
            ]);

            $notifikasi = 'Data kurikulum berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.diklat.kurikulum.index')->with('success', $notifikasi);

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data kurikulum gagal ditambahkan!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->checkAuth($id);

        $kurikulum = DB::table('kurikulum')->where('id', $id)->first();
        $jdiklat = DB::table('diklat_jenis')->orderBy('nama')->get();

        return view('backend.diklat.kurikulum.edit', compact('jdiklat', 'kurikulum'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->checkAuth($id);

        $validator = $request->validate([
            'nama' => 'required',
            'j_diklat' => 'required',
            'j_belajar' => 'required',
            'jp' => 'required|numeric',
        ]);

        try
        {
            $usergroup = Auth::user()->usergroup;

            DB::table('kurikulum')->where('id', $id)->update([
                'diklat_jenis_id' => $request->j_diklat,
                'nama' => $request->nama,
                'jenis_belajar' => $request->j_belajar,
                'total_jp' => $request->jp,
                'usergroup' => $usergroup,
            ]);

            $notifikasi = 'Data kurikulum berhasil diubah!';

            if(isset($request->add))
                return redirect()->route('backend.diklat.kurikulum.index')->with('success', $notifikasi);

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data kurikulum gagal diubah!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->checkAuth($id);

        $delete = DB::table('kurikulum')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data kurikulum berhasil dihapus!';
            return redirect()->route('backend.diklat.kurikulum.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data kurikulum gagal dihapus!';
        return redirect()->route('backend.diklat.kurikulum.index')->with('error', $notifikasi);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id, $slug)
    {
        $this->checkAuth($id);

        $kurikulum = DB::table('kurikulum')->where('id', $id)->first();
        $mapel = DB::table('mapel')->where('kurikulum_id', $id)->orderBy('id')->get();

        return view('backend.diklat.kurikulum.detail', compact('kurikulum', 'mapel'));
    }

    public function getKurikulum($jdiklat)
    {
        $data = DB::table('kurikulum')
                    ->select('id', 'nama')
                    ->where('diklat_jenis_id', $jdiklat)
                    ->get();

        return response()->json($data, 200);
    }

    public function checkAuth($id)
    {
        if($this->isAdmin())
            return true;

        $data = DB::table('kurikulum')->where('id', $id)
                    ->first();

        if(empty($data))
        {
            abort(404);
        }

        if(Gate::allows('isCreator', $data) && Auth::user()->instansi_id == 1)
        {
            return true;
        }

        abort(403);
    }
}
