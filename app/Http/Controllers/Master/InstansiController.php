<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InstansiController extends Controller
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
        $instansi = DB::table('instansi')->orderBy('sort')->get();
        return view('backend.master.instansi.index', compact('instansi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('backend.master.instansi.create');
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
            $sort = DB::table('instansi')->max('sort');
            
            DB::table('instansi')->insert([
                'nama' => $request->nama,
                'ket' => $request->ket,
                'sort' => ++$sort
            ]);

            $notifikasi = 'Data instansi berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.master.instansi.index')->with('success', $notifikasi);
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data instansi gagal ditambahkan!';
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
        $instansi = DB::table('instansi')->where('id', $id)->first();
        return view('backend.master.instansi.edit', compact('instansi'));
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
            DB::table('instansi')->where('id', $id)->update([
                'nama' => $request->nama,
                'ket' => $request->ket,
            ]);

            $notifikasi = 'Data instansi berhasil diubah!';
            return redirect()->route('backend.master.instansi.index')->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data instansi gagal diubah!';
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
        $delete = DB::table('instansi')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data instansi berhasil dihapus!';
            return redirect()->route('backend.master.instansi.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data instansi gagal dihapus!';
        return redirect()->route('backend.master.instansi.index')->with('error', $notifikasi);
    }

    public function sort(Request $request)
    {        
        $post = json_decode($request->instansi, true);
        $i = 1;
        foreach ($post as $ro) {
            $pid = $ro['id'];            
            DB::table('instansi')->where('id', $pid)->update(['sort' => $i]);
            $i++;
        }

        return response()->json('success', 200);
    }
}
