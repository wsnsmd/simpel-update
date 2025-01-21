<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ApiToken;

use Auth;
use DB;

class ApiTokenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $apitoken = ApiToken::all();
        return view('backend.pengaturan.apitoken.index', compact('apitoken'));
    }

    public function create()
    {
        return view('backend.pengaturan.apitoken.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:api_tokens,app_name',
            'ip_address' => 'nullable|ip'
        ]);

        $token = $this->generateToken();

        try
        {
            $apiToken = ApiToken::create([
                'app_name' => $request->input('nama'),
                'token' => $token,
                'ip_address' => $request->input('ip_address')
            ]);

            $notifikasi = 'Data API Token berhasil ditambahkan!';

            if(isset($request->add))
                return redirect()->route('backend.apitoken.index')->with('success', $notifikasi);

            return redirect()->back()->with('success', $notifikasi);
        }
        catch(\Exception $e)
        {
            $notifikasi = 'Data API Token gagal ditambahkan!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function edit($id)
    {
        $apiToken = ApiToken::where('id', $id)->where('type', true)->firstOrFail();

        return view('backend.pengaturan.apitoken.edit', compact('apiToken'));
    }

    public function update(Request $request, $id)
    {
        $apiToken = ApiToken::findOrFail($id);
        $request->validate([
            'nama' => 'required|unique:api_tokens,app_name',
            'ip_address' => 'nullable|ip'
        ]);

        $token = $this->generateToken();

        try
        {
            $apiToken->update([
                'app_name' => $request->input('nama'),
                'token' => $token,
                'ip_address' => $request->input('ip_address')
            ]);

            $notifikasi = 'Data API Token berhasil diubah!';

            return redirect()->route('backend.apitoken.index')->with('success', $notifikasi);
        }

        catch(\Exception $e)
        {
            $notifikasi = 'Data API Token gagal diubah!';
            return redirect()->back()->with('error', $notifikasi);
        }
    }

    public function destroy($id)
    {
        $apiToken = ApiToken::findOrFail($id);

        if($apiToken)
        {
            $apiToken->delete();
            $notifikasi = 'Data API Token berhasil dihapus!';
            return redirect()->route('backend.apitoken.index')->with('success', $notifikasi);
        }

        $notifikasi = 'Data API Token gagal dihapus!';
        return redirect()->route('backend.apitoken.index')->with('error', $notifikasi);
    }

    public function generateToken()
    {
        $token = hash('sha256', uniqid() . str_random(60));

        return $token;
    }
}
