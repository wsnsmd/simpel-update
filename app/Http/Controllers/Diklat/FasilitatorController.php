<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Gate;

class FasilitatorController extends Controller
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
                $fasilitator = DB::table('fasilitator')
                            ->leftjoin('pangkat', 'fasilitator.pangkat_id', '=', 'pangkat.id')
                            ->select('fasilitator.*', 'pangkat.singkat')
                            ->orderBy('nama')
                            ->get();
                break;

            case 'user':
                $fasilitator = DB::table('fasilitator')
                            ->leftjoin('pangkat', 'fasilitator.pangkat_id', '=', 'pangkat.id')
                            ->select('fasilitator.*', 'pangkat.singkat')
                            ->where('fasilitator.usergroup', $this->user->usergroup)
                            ->orderBy('nama')
                            ->get();
                break;
        }
        
        return view('backend.diklat.fasilitator.index', compact('fasilitator'));
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

        $pangkat = DB::table('pangkat')->get();
        return view('backend.diklat.fasilitator.create', compact('pangkat'));
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
            'jabatan' => 'required',
            'foto' => 'file|mimes:jpeg,jpg,png,gif|max:512',
        ]);
        
        try 
        {
            $created_at = date('Y-m-d H:i:s');
            $usergroup = Auth::user()->usergroup;
            $path = null;
            if(isset($request->foto))
            {
                $foto = $request->file('foto');
                $nama_file = time()."_".$foto->getClientOriginalName();    
                $path = $request->foto->storeAs('public/files/photo/fasilitator', $nama_file);
            }

            DB::table('fasilitator')->insert([
                'nip' => $request->nip,
                'nama' => $request->nama,
                'pangkat_id' => $request->pangkat,
                'tmt_pangkat' => $request->tmt_pangkat,
                'jabatan' => $request->jabatan,
                'tmt_jabatan' => $request->tmt_jabatan,
                'instansi' => $request->instansi,
                'satker_nama' => $request->satker_nama,
                'foto' => $path,
                'created_at' => $created_at,
                'usergroup' => $usergroup,
            ]);

            $notifikasi = 'Data fasilitator berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.diklat.fasilitator.index')->with('success', $notifikasi);
            
            return redirect()->back()->with('success', $notifikasi); 
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data fasilitator gagal ditambahkan!';
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

        $fasilitator = DB::table('fasilitator')->where('id', $id)->first();
        $pangkat = DB::table('pangkat')->get();
        return view('backend.diklat.fasilitator.edit', compact('fasilitator', 'pangkat'));
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
            'jabatan' => 'required',
            'foto' => 'file|mimes:jpeg,jpg,png,gif|max:512',
        ]);
        
        try 
        {
            $updated_at = date('Y-m-d H:i:s');
            $usergroup = Auth::user()->usergroup;
            $path = null;
            if(isset($request->foto))
            {
                $foto = $request->file('foto');
                $nama_file = time()."_".$foto->getClientOriginalName();    
                $path = $request->foto->storeAs('public/files/photo/fasilitator', $nama_file);                
            }
            else 
            {
                $path = $request->foto_lama;
            }

            DB::table('fasilitator')->where('id', $id)->update([
                'nip' => $request->nip,
                'nama' => $request->nama,
                'pangkat_id' => $request->pangkat,
                'tmt_pangkat' => $request->tmt_pangkat,
                'jabatan' => $request->jabatan,
                'tmt_jabatan' => $request->tmt_jabatan,
                'instansi' => $request->instansi,
                'satker_nama' => $request->satker_nama,
                'foto' => $path,
                'updated_at' => $updated_at,
                'usergroup' => $usergroup,
            ]);

            if(isset($request->foto))
                \Storage::delete($request->foto_lama);

            $notifikasi = 'Data fasilitator berhasil diubah!';

            return redirect()->route('backend.diklat.fasilitator.index')->with('success', $notifikasi);
            
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data fasilitator gagal diubah!';
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

        $fasilitator = DB::table('fasilitator')->where('id', $id)->first();

        $delete = DB::table('fasilitator')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data fasilitator berhasil dihapus!';
            \Storage::delete($fasilitator->foto);
            return redirect()->route('backend.diklat.fasilitator.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data fasilitator gagal dihapus!';
        return redirect()->route('backend.diklat.fasilitator.index')->with('error', $notifikasi);
    }

    public function checkAuth($id)
    {
        if($this->isAdmin())
            return true;

        $data = DB::table('fasilitator')->where('id', $id)
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
