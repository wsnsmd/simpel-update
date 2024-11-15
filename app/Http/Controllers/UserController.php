<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\User;
use App\ActivityLog;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = DB::table('users')->get();
        return view('backend.pengaturan.pengguna.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $instansi = DB::table('instansi')->orderby('sort')->get();

        return view('backend.pengaturan.pengguna.create', compact('instansi'));
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
        // dd($request->all());
        $validator = $request->validate([
            'nama' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required',
            'group' => 'required',
            'instansi' => 'required',
            'foto' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:512',
        ]);

        try
        {

            $created_at = date('Y-m-d H:i:s');
            $path = null;
            if(isset($request->foto))
            {
                $foto = $request->file('foto');
                $nama_file = time()."_".$foto->getClientOriginalName();
                $path = $request->foto->storeAs('public/files/photo/user', $nama_file);
            }


            DB::table('users')->insert([
                'name' => $request->nama,
                'username' => $request->username,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
                'usergroup' => $request->group,
                'photo' => $path,
                'instansi_id' => $request->instansi,
                'created_at' => $created_at,
            ]);

            $notifikasi = 'Data pengguna berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.pengguna.index')->with('success', $notifikasi);

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data pengguna gagal ditambahkan!';
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
        $user = DB::table('users')->where('id', $id)->first();
        $instansi = DB::table('instansi')->orderby('sort')->get();
        return view('backend.pengaturan.pengguna.edit', compact('user', 'instansi'));

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
        $user = User::find($id);

        $validator = $request->validate([
            'nama' => 'required',
            'username' => 'required' . ($request->username != $user->username ? '|unique:users' : ''),
            'email' => 'required' . ($request->email != $user->email ? '|unique:users' : ''),
            'group' => 'required',
            'foto' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:512',
            'instansi' => 'required',
        ]);

        try
        {
            $updated_at = date('Y-m-d H:i:s');
            $path = null;
            if(isset($request->foto))
            {
                $foto = $request->file('foto');
                $nama_file = time()."_".$foto->getClientOriginalName();
                $path = $request->foto->storeAs('public/files/photo/user', $nama_file);
            }
            else
            {
                $path = $request->foto_lama;
            }

            $user->name = $request->nama;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->usergroup = $request->group;
            $user->photo = $path;
            $user->instansi_id = $request->instansi;
            $user->updated_at = $updated_at;

            if(isset($request->password))
                $user->password = \Hash::make($request->password);

            $user->save();

            if(isset($request->foto))
                \Storage::delete($request->foto_lama);

            $notifikasi = 'Data pengguna berhasil diubah!';

            return redirect()->route('backend.pengguna.index')->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data pengguna gagal diubah!';
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
        $user = DB::table('users')->where('id', $id)->first();

        $delete = DB::table('users')->where('id', $id)->delete();

        if($delete)
        {
            $notifikasi = 'Data pengguna berhasil dihapus!';
            \Storage::delete($user->photo);
            return redirect()->route('backend.pengguna.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data pengguna gagal dihapus!';
        return redirect()->route('backend.pengguna.index')->with('error', $notifikasi);
    }

    public function profil()
    {
        if(Auth::check())
        {
            $user = User::where('id', Auth::user()->id)->firstOrFail();
            return view('backend.user.profil', compact('user'));
        }
        abort(403);
    }

    public function profilupdate(Request $request)
    {
        $validator = $request->validate([
            'nama' => 'required',
            'email' => 'required' . ($request->email != Auth::user()->email ? '|unique:users' : ''),
            'foto' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:512',
            'password_lama' => ['nullable', 'required_with:password_baru', function ($attribute, $value, $fail) {
                if (!\Hash::check($value, Auth::user()->password)) {
                    return $fail(__('password anda salah'));
                }
            }],
            'password_baru' => 'required_with:password_lama'
        ]);

        try
        {
            $updated_at = date('Y-m-d H:i:s');
            $path = null;
            if(isset($request->foto))
            {
                $foto = $request->file('foto');
                $nama_file = time()."_".$foto->getClientOriginalName();
                $path = $request->foto->storeAs('public/files/photo/user', $nama_file);
            }
            else
            {
                $path = $request->foto_lama;
            }

            $user = User::find(Auth::user()->id);
            $user->name = $request->nama;
            $user->email = $request->email;
            $user->photo = $path;
            $user->updated_at = $updated_at;

            if(\Hash::check($request->password_lama, Auth::user()->password))
                $user->password = \Hash::make($request->password_baru);

            $user->save();

            if(isset($request->foto))
                \Storage::delete($request->foto_lama);

            $notifikasi = 'Data profil berhasil diubah!';

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data profil gagal diubah!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function aktifitas(Request $request)
    {
        $logs = ActivityLog::orderBy('created_at', 'desc')->take(1000)->get();

        return view('backend.pengaturan.pengguna.aktifitas', compact('logs'));
    }
}
