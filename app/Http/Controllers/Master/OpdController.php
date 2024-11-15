<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:isAdmin');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $opd = DB::table('opd')->orderBy('nama')->get();
        return view('backend.master.opd.index', compact('opd'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('backend.master.opd.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = $request->validate([
            'nama' => 'required',
            'singkat' => 'required|unique:opd'
        ]);

        try 
        {
            DB::table('opd')->insert([
                'nama' => $request->nama,
                'singkat' => $request->singkat,
            ]);

            $notifikasi = 'Data OPD / SKPD berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.master.opd.index')->with('success', $notifikasi);
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data OPD / SKPD gagal ditambahkan!';
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
        //
        $opd = DB::table('opd')->where('id', $id)->first();
        return view('backend.master.opd.edit', compact('opd'));
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
        //
        $opd = DB::table('opd')->where('id', $id)->first();

        $validator = $request->validate([
            'nama' => 'required',
            'singkat' => 'required' . ($request->singkat != $opd->singkat ? '|unique:opd' : ''),
        ]);

        try 
        {
            DB::table('opd')->where('id', $id)->update([
                'nama' => $request->nama,
                'singkat' => $request->singkat,
            ]);

            $notifikasi = 'Data OPD / SKPD berhasil diubah!';
            return redirect()->route('backend.master.opd.index')->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data OPD / SKPD gagal diubah!';
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
        //
        $delete = DB::table('opd')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data OPD / SKPD berhasil dihapus!';
            return redirect()->route('backend.master.opd.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data OPD / SKPD gagal dihapus!';
        return redirect()->route('backend.master.opd.index')->with('error', $notifikasi);
    }
}
