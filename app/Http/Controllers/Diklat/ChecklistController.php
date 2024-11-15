<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Excel;

use App\Checklist;
use App\Imports\ChecklistImport;

class ChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {   
        $jadwal = DB::table('v_jadwal_detail')->where('id', $request->jadwal_id)->first();     
        return view('backend.diklat.jadwal.checklist', compact('jadwal'));
    }

    public function store(Request $request) 
    {               
        $validator = $request->validate([            
            'jadwal_id' => 'required',
            'dokumen' => 'required',
        ]);

        $input = $request->all();

        DB::beginTransaction();

        try 
        {
            $created_at = date('Y-m-d H:i:s');

            DB::table('checklist')->insert([
                'diklat_jadwal_id' => $input['jadwal_id'],
                'dokumen' => $input['dokumen'],
                'keterangan' => $input['keterangan'],
                'created_at' => $created_at
            ]);
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return response()->json(['status' => 'success', 'pesan' => 'Data checklist berhasil disimpan!'], 200);
    }

    public function show($id)
    {
        $data = DB::table('checklist')->where('id', $id)->first();
        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'dokumen' => 'required',
        ]);

        $input = $request->all();

        DB::beginTransaction();

        try 
        {
            $updated_at = date('Y-m-d H:i:s');

            DB::table('checklist')->where('id', $id)->update([
                'dokumen' => $input['dokumen'],
                'keterangan' => $input['keterangan'],
                'updated_at' => $updated_at
            ]);
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return response()->json(['status' => 'success', 'pesan' => 'Data checklist berhasil disimpan!'], 200);
    }

    public function destroy($id)
    {
        $checklist = DB::table('checklist')->where('id', $id)->first();
        $delete = DB::table('checklist')->where('id', $id)->delete();
        $data = [];

        if($delete)
        {
            $data['status'] = 'success';
            $data['pesan'] = 'Data checklist berhasil dihapus';
            \Storage::delete($checklist->path);
            return response()->json($data, 200);            
        }

        $data['status'] = 'error';
        $data['pesan'] = 'Data checklist gagal dihapus';
        return response()->json($data, 200);
    }

    public function loadChecklist(Request $request)
    {
        $checklist = DB::table('checklist')->where('diklat_jadwal_id', $request->jadwal_id)->get();
        return view('backend.diklat.jadwal.loadChecklist', compact('checklist'));
    }

    public function upload(Request $request) 
    {
        $validator = $request->validate([            
            'checklist_id' => 'required',
            'file' => 'required|mimes:doc,docx,pdf,xls,xlsx|max:102400',
        ]);

        $input = $request->all();
        $pathold = null;

        DB::beginTransaction();

        try 
        {
            $checklist = Checklist::where('id', $input['checklist_id'])->first();
            $path = null;

            if(!is_null($checklist->path)) 
                $pathold = $checklist->path;

            $file = $input['file'];
            $nama_file = time()."_".$file->getClientOriginalName(); 
            $path = $input['file']->storeAs('public/files/checklist', $nama_file);

            $checklist->path = $path;
            $checklist->save();
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        if(!is_null($pathold))
            \Storage::delete($pathold);

        return response()->json(['status' => 'success', 'pesan' => 'Upload file dokumen checklist berhasil!'], 200);
    }

    public function import(Request $request)
    {
        $validator = $request->validate([            
            'jadwal_id' => 'required',
            'file' => 'required|mimes:xls,xlsx|max:1024',
        ]);

        $input = $request->all();

        DB::beginTransaction();

        try
        {
            $import = new ChecklistImport();
            $import->jadwal_id = $input['jadwal_id'];
            $data = Excel::import($import, $input['file']);
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return response()->json(['status' => 'success', 'pesan' => 'Import data checklist berhasil!'], 200);
    }
}
