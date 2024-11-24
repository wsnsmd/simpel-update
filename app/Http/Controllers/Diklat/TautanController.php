<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Tautan;

use DB;

class TautanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function load(Request $request)
    {
        $tautan = Tautan::where('jadwal_id', $request->jadwal_id)->get();
        return view('backend.diklat.jadwal.loadTautan', compact('tautan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required',
            'title' => 'required',
            'url' => 'required',
            'is_active' => 'required'
        ]);

        try
        {
            Tautan::create([
                'jadwal_id' => $request->input('jadwal_id'),
                'title' => $request->input('title'),
                'url' => $request->input('url'),
                'is_active' => $request->input('is_active')
            ]);

            return response()->json(['status' => 'success', 'pesan' => 'Data tautan berhasil disimpan!'], 200);
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => 'error', 'pesan' => 'Data tautan gagal disimpan dengan kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $data = Tautan::find($id);
        return response()->json($data, 200);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $tautan = Tautan::findOrFail($id);
        $request->validate([
            'jadwal_id' => 'required',
            'title' => 'required',
            'url' => 'required',
            'is_active' => 'required'
        ]);

        try
        {
            $tautan->update([
                'title' => $request->input('title'),
                'url' => $request->input('url'),
                'is_active' => $request->input('is_active')
            ]);

            return response()->json(['status' => 'success', 'pesan' => 'Data tautan berhasil disimpan!'], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['status' => 'error', 'pesan' => 'Data tautan gagal disimpan dengan kesalahan : ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try
        {
            $tautan = Tautan::findOrFail($id);
            $tautan->delete();
            return response()->json(['status' => 'success', 'pesan' => 'Data tautan berhasil dihapus!'], 200);
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error', 'pesan' => 'Data tautan gagal dihapus dengan kesalahan : ' . $e->getMessage()], 500);
        }
    }
}
