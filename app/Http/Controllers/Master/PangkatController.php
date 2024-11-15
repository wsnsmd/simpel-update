<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PangkatController extends Controller
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
        $pangkat = DB::table('pangkat')->get();
        return view('backend.master.pangkat.index', compact('pangkat'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('backend.master.pangkat.create');
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
            'id' => 'required|unique:pangkat',
            'pangkat' => 'required',
            'golongan' => 'required',
            'singkat' => 'required',
        ]);

        try 
        {
            DB::table('pangkat')->insert([
                'id' => $request->id,
                'pangkat' => $request->pangkat,
                'golongan' => $request->golongan,
                'singkat' => $request->singkat,
            ]);

            $notifikasi = 'Data pangkat berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.master.pangkat.index')->with('success', $notifikasi);
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data pangkat gagal ditambahkan!';
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
        $pangkat = DB::table('pangkat')->where('id', $id)->first();
        return view('backend.master.pangkat.edit', compact('pangkat'));
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
            'id' => 'required' . ($request->id != $id ? '|unique:pangkat' : ''),
            'pangkat' => 'required',
            'golongan' => 'required',
            'singkat' => 'required',
        ]);

        try 
        {
            DB::table('pangkat')->where('id', $id)->update([
                'id' => $request->id,
                'pangkat' => $request->pangkat,
                'golongan' => $request->golongan,
                'singkat' => $request->singkat,
            ]);

            $notifikasi = 'Data pangkat berhasil diubah!';
            return redirect()->route('backend.master.pangkat.index')->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data pangkat gagal diubah!';
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
        $delete = DB::table('pangkat')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data pangkat berhasil dihapus!';
            return redirect()->route('backend.master.pangkat.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data pangkat gagal dihapus!';
        return redirect()->route('backend.master.pangkat.index')->with('error', $notifikasi);
    }
}
