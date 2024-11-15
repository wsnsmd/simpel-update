<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LokasiController extends Controller
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
        $lokasi = DB::table('lokasi')->get();
        return view('backend.master.lokasi.index', compact('lokasi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('backend.master.lokasi.create');
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
        ]);

        try 
        {
            DB::table('lokasi')->insert([
                'nama' => $request->nama,
                'ket' => $request->ket,
            ]);

            $notifikasi = 'Data lokasi berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.master.lokasi.index')->with('success', $notifikasi);
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data lokasi gagal ditambahkan!';
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
        $lokasi = DB::table('lokasi')->where('id', $id)->first();
        return view('backend.master.lokasi.edit', compact('lokasi'));
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
            'nama' => 'required',
        ]);

        try 
        {
            DB::table('lokasi')->where('id', $id)->update([
                'nama' => $request->nama,
                'ket' => $request->ket,
            ]);

            $notifikasi = 'Data lokasi berhasil diubah!';
            return redirect()->route('backend.master.lokasi.index')->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data lokasi gagal diubah!';
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
        $delete = DB::table('lokasi')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data lokasi berhasil dihapus!';
            return redirect()->route('backend.master.lokasi.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data lokasi gagal dihapus!';
        return redirect()->route('backend.master.lokasi.index')->with('error', $notifikasi);
    }
}
