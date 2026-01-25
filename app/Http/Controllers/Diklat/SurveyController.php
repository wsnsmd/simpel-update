<?php

namespace App\Http\Controllers\Diklat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\JadwalSurvey;
// use App\PesertaSurveyStatus; // opsional kalau nanti dipakai

use DB;

class SurveyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Load daftar survey per jadwal (untuk partial view / ajax)
     * Request: jadwal_id
     */
    public function load(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|integer',
        ]);

        $surveys = JadwalSurvey::where('jadwal_id', $request->jadwal_id)
            ->orderByDesc('id')
            ->get();

        return view('backend.diklat.jadwal.loadSurvey', compact('surveys'));
    }

    /**
     * Simpan survey ke jadwal
     * Request: jadwal_id, survey_code, survey_name, is_mandatory
     */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|integer',
            'survey_code' => 'required|string|max:255',
            'survey_name' => 'required|string|max:255',
            'is_mandatory' => 'required|in:0,1',
        ]);

        DB::beginTransaction();
        try {
            // opsional: cegah duplikat survey_code dalam jadwal yang sama
            $exists = JadwalSurvey::where('jadwal_id', $request->jadwal_id)
                ->where('survey_code', $request->survey_code)
                ->exists();

            if ($exists) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'pesan' => 'Survey ini sudah terdaftar pada jadwal tersebut.',
                ], 422);
            }

            JadwalSurvey::create([
                'jadwal_id' => $request->input('jadwal_id'),
                'survey_code' => $request->input('survey_code'),
                'survey_name' => $request->input('survey_name'),
                'params' => $request->input('params'),
                'is_mandatory' => (int) $request->input('is_mandatory'),
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'pesan' => 'Data survey berhasil disimpan!',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'pesan' => 'Data survey gagal disimpan dengan kesalahan : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil detail 1 data survey (untuk edit modal)
     */
    public function show($id)
    {
        $data = JadwalSurvey::find($id);
        return response()->json($data, 200);
    }

    public function edit($id)
    {
        //
    }

    /**
     * Update survey jadwal
     */
    public function update(Request $request, $id)
    {
        $survey = JadwalSurvey::findOrFail($id);

        $request->validate([
            'jadwal_id' => 'required|integer',
            'survey_code' => 'required|string|max:255',
            'survey_name' => 'required|string|max:255',
            'is_mandatory' => 'required|in:0,1',
        ]);

        DB::beginTransaction();
        try {
            // opsional: cegah duplikat saat update
            $exists = JadwalSurvey::where('jadwal_id', $request->jadwal_id)
                ->where('survey_code', $request->survey_code)
                ->where('id', '!=', $survey->id)
                ->exists();

            if ($exists) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'pesan' => 'Survey code tersebut sudah digunakan pada jadwal ini.',
                ], 422);
            }

            $survey->update([
                'jadwal_id' => $request->input('jadwal_id'),
                'survey_code' => $request->input('survey_code'),
                'survey_name' => $request->input('survey_name'),
                'params' => $request->input('params'),
                'is_mandatory' => (int) $request->input('is_mandatory'),
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'pesan' => 'Data survey berhasil disimpan!',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'pesan' => 'Data survey gagal disimpan dengan kesalahan : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus survey dari jadwal
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $survey = JadwalSurvey::findOrFail($id);

            // Jika nanti Anda pakai peserta_survey_status dan ingin ikut bersih-bersih:
            // PesertaSurveyStatus::where('survey_code', $survey->survey_code)->delete();

            $survey->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'pesan' => 'Data survey berhasil dihapus!',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'pesan' => 'Data survey gagal dihapus dengan kesalahan : ' . $e->getMessage(),
            ], 500);
        }
    }
}
