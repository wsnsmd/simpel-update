<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AgamaController extends Controller
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
        $agama = DB::table('agama')->get();

        return view('backend.master.agama.index', compact('agama'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('backend.master.agama.create');
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
            'id' => 'required|unique:agama',
            'nama' => 'required',
        ]);

        // $created_at = date('Y-m-d H:i:s');

        try 
        {
            DB::table('agama')->insert([
                'id' => $request->id,
                'nama' => $request->nama,
            ]);

            $notifikasi = 'Data agama berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.master.agama.index')->with('success', $notifikasi);
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data agama gagal ditambahkan!';
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
        $agama = DB::table('agama')->where('id', $id)->first();
        return view('backend.master.agama.edit', compact('agama'));
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
            'id' => 'required' . ($request->id != $id ? '|unique:agama' : ''),
            'nama' => 'required',
        ]);

        try 
        {
            DB::table('agama')->where('id', $id)->update([
                'id' => $request->id,
                'nama' => $request->nama,
            ]);

            $notifikasi = 'Data agama berhasil diubah!';
            return redirect()->route('backend.master.agama.index')->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data agama gagal diubah!';
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
        $delete = DB::table('agama')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data agama berhasil dihapus!';
            return redirect()->route('backend.master.agama.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data agama gagal dihapus!';
        return redirect()->route('backend.master.agama.index')->with('error', $notifikasi);
    }
}
