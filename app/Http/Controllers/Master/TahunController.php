<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TahunController extends Controller
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
        $tahun = DB::table('tahun')->orderBy('tahun')->get();
        return view('backend.master.tahun.index', compact('tahun'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('backend.master.tahun.create');
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
            'tahun' => 'required|numeric',
            'aktif' => 'required',
        ]);

        try 
        {
            DB::table('tahun')->insert([
                'tahun' => $request->tahun,
                'aktif' => $request->aktif,
            ]);

            $notifikasi = 'Data tahun berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.master.tahun.index')->with('success', $notifikasi);
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data tahun gagal ditambahkan!';
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
        $tahun = DB::table('tahun')->where('tahun', $id)->first();
        return view('backend.master.tahun.edit', compact('tahun'));
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
        $validator = $request->validate([
            'tahun' => 'required|numeric',
            'aktif' => 'required',
        ]);

        try 
        {
            DB::table('tahun')->where('tahun', $id)->update([
                'tahun' => $request->tahun,
                'aktif' => $request->aktif,
            ]);

            $notifikasi = 'Data tahun berhasil diubah!';

                return redirect()->route('backend.master.tahun.index')->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data tahun gagal diubah!';
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
        $delete = DB::table('tahun')->where('tahun', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data tahun berhasil dihapus!';
            return redirect()->route('backend.master.tahun.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data tahun gagal dihapus!';
        return redirect()->route('backend.master.tahun.index')->with('error', $notifikasi);
    }
}
