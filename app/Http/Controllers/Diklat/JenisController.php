<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Gate;

class JenisController extends Controller
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
        //
        $jenis = DB::table('diklat_jenis')->get();
        return view('backend.diklat.jenis.index', compact('jenis'));
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

        return view('backend.diklat.jenis.create');
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
            'id' => 'required|unique:diklat_jenis',
            'nama' => 'required',
        ]);

        try 
        {
            $usergroup = Auth::user()->usergroup;

            DB::table('diklat_jenis')->insert([
                'id' => $request->id,
                'nama' => $request->nama,
                'usergroup' => $usergroup,
            ]);

            $notifikasi = 'Data jenis diklat berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.diklat.jenis.index')->with('success', $notifikasi);
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data jenis diklat gagal ditambahkan!';
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

        $jenis = DB::table('diklat_jenis')->where('id', $id)->first();
        return view('backend.diklat.jenis.edit', compact('jenis'));
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
            'id' => 'required' . ($request->id != $id ? '|unique:diklat_jenis' : ''),
            'nama' => 'required',
        ]);

        try 
        {
            $usergroup = Auth::user()->usergroup;

            DB::table('diklat_jenis')->where('id', $id)->update([
                'id' => $request->id,
                'nama' => $request->nama,
                'usergroup' => $usergroup,
            ]);

            $notifikasi = 'Data jenis diklat berhasil diubah!';
            return redirect()->route('backend.diklat.jenis.index')->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data jenis diklat gagal diubah!';
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

        $delete = DB::table('diklat_jenis')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data jenis diklat berhasil dihapus!';
            return redirect()->route('backend.diklat.jenis.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data jenis diklat gagal dihapus!';
        return redirect()->route('backend.diklat.jenis.index')->with('error', $notifikasi);
    }

    public function checkAuth($id)
    {
        if($this->isAdmin())
            return true;

        $data = DB::table('diklat_jenis')->where('id', $id)
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
