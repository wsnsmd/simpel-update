<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\UploadSimpegJob;
use DB;
use PhpParser\Node\Stmt\Catch_;

class SurveyCallbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|integer',
            'peserta_id' => 'required|integer',
            'survey_code' => 'required|string|max:255',

            // response boleh array atau string JSON
            'response' => 'nullable',

            // meta optional
            'meta' => 'nullable',

            // optional timestamp dari app survei
            'completed_at' => 'nullable|date',
        ]);

        $jadwalId = (int) $request->jadwal_id;
        $pesertaId = (int) $request->peserta_id;
        $surveyCode = (string) $request->survey_code;

        // Pastikan peserta ada
        $pesertaExists = DB::table('peserta')->where('id', $pesertaId)->exists();
        if (!$pesertaExists) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak ditemukan',
            ], 404);
        }

        // (Opsional) Pastikan jadwal ada - sesuaikan tabel jadwal kamu
        $jadwalExists = DB::table('diklat_jadwal')->where('id', $jadwalId)->exists();
        if (!$jadwalExists) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan',
            ], 404);
        }

        // Normalisasi response_json
        $responseJson = null;
        if ($request->filled('response')) {
            if (is_array($request->response)) {
                $responseJson = json_encode($request->response);
            } else {
                $responseJson = (string) $request->response;

                json_decode($responseJson);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format response harus JSON valid atau array.',
                    ], 422);
                }
            }
        }

        // Normalisasi meta_json (gabungkan meta dari request + info request)
        $meta = [
            'ip' => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
        ];

        if ($request->filled('meta')) {
            if (is_array($request->meta)) {
                $meta = array_merge($meta, $request->meta);
            } else {
                $metaStr = (string) $request->meta;
                $decoded = json_decode($metaStr, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format meta harus JSON valid atau array.',
                    ], 422);
                }
                if (is_array($decoded)) {
                    $meta = array_merge($meta, $decoded);
                }
            }
        }

        $completedAt = $request->filled('completed_at') ? $request->completed_at : now();

        try {
            DB::beginTransaction();

            // Kunci unik yang disarankan: (jadwal_id, peserta_id, survey_code)
            $existing = DB::table('peserta_survey_status')
                ->where('jadwal_id', $jadwalId)
                ->where('peserta_id', $pesertaId)
                ->where('survey_code', $surveyCode)
                ->first();

            $payload = [
                'is_completed' => 1,
                'completed_at' => $completedAt,
                'response_json' => $responseJson,
                'meta_json' => json_encode($meta),
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('peserta_survey_status')
                    ->where('id', $existing->id)
                    ->update($payload);
            } else {
                DB::table('peserta_survey_status')->insert(array_merge($payload, [
                    'jadwal_id' => $jadwalId,
                    'peserta_id' => $pesertaId,
                    'survey_code' => $surveyCode,
                    'created_at' => now(),
                ]));
            }

            DB::commit();

            $this->checkAndDispatchSimasnIfSurveyComplete((int) $request->peserta_id, (int) $request->jadwal_id);

            return response()->json([
                'success' => true,
                'message' => 'Status survei berhasil disimpan',
                'data' => [
                    'jadwal_id' => $jadwalId,
                    'peserta_id' => $pesertaId,
                    'survey_code' => $surveyCode,
                    'is_completed' => true,
                    'completed_at' => $completedAt,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan status survei: ' . $e->getMessage(),
            ], 500);
        }
    }
    private function checkAndDispatchSimasnIfSurveyComplete($pesertaId, $jadwalId)
    {
        try {
            $mandatoryCodes = DB::table('jadwal_surveys')
                ->where('jadwal_id', $jadwalId)
                ->where('is_mandatory', 1)
                ->pluck('survey_code')
                ->toArray();

            \Log::info('[SIMASN] mandatoryCodes', [
                'count' => count($mandatoryCodes),
                'codes' => $mandatoryCodes,
            ]);

            if (empty($mandatoryCodes)) {
                \Log::info('[SIMASN] stop: no mandatory surveys');
                return;
            }

            $doneCodes = DB::table('peserta_survey_status')
                ->where('peserta_id', $pesertaId)
                ->where('jadwal_id', $jadwalId)
                ->where('is_completed', 1)
                ->whereIn('survey_code', $mandatoryCodes)
                ->pluck('survey_code')
                ->toArray();

            $missing = array_diff($mandatoryCodes, $doneCodes);
            if (!empty($missing)) {
                \Log::info('[SIMASN] doneCodes', ['done' => $doneCodes]);
                \Log::info('[SIMASN] missing', ['missing' => array_values($missing)]);
                return;
            }

            $pes = DB::table('v_sertifikat')
                ->where('id', $pesertaId)
                ->where('diklat_jadwal_id', $jadwalId)
                ->first();

            if (!$pes) {
                \Log::info('[SIMASN] stop: no $pes');
                return;
            }

            $sertifikat = DB::table('sertifikat')
                ->where('diklat_jadwal_id', $jadwalId)
                ->first();

            if (!$sertifikat) {
                \Log::info('[SIMASN] stop: no $sertifikat');
                return;
            }

            if ((int) $sertifikat->is_upload === 1 && empty($pes->upload)) {
                \Log::info('[SIMASN] stop: $sertifikat->is_upload === 1 && empty($pes->upload)');
                return;
            }

            DB::table('sertifikat_peserta')
                ->where('id', $pes->spid)
                ->update([
                    'updated_at' => now(),
                    'is_published' => true
                ]);

            \Log::info('[SERTIFIKAT: ' . $pes->spid . '] peserta_id ' . $pesertaId . ' jadwal_id ' . $jadwalId . ' sertifikat published karena survey lengkap');

            // if ((int) $pes->status_asn === 0) {
            //     \Log::info('[SIMASN] stop: $pes->status_asn === 0');
            //     return;
            // }

            // $targetInstansi = 'Pemerintah Provinsi Kalimantan Timur';
            // if (strtolower(trim((string) $pes->instansi)) !== strtolower($targetInstansi)) {
            //     \Log::info('[SIMASN] stop: $targetInstansi');
            //     return;
            // }

            // if (!is_null($pes->simpeg_at)) {
            //     return;
            // }

            // $simasn = DB::table('sertifikat_simasn')
            //     ->where('sertifikat_id', $sertifikat->id)
            //     ->first();

            // if (!$simasn) {
            //     // kalau belum diset operator/admin, jangan auto kirim
            //     \Log::info('[SIMASN] stop: $simasn');
            //     return;
            // }

            // $jenis = $simasn->jenis;
            // $kategori = $simasn->kategori;
            // $sub = $simasn->sub_kategori;

            // $updated = DB::table('sertifikat_peserta')
            //     ->where('id', $pes->spid)
            //     ->whereNull('simpeg_at')
            //     ->whereNull('simpeg_queued_at')
            //     ->update([
            //         'simpeg_queued_at' => now(),
            //         'simpeg_failed_at' => null, // reset gagal jika sebelumnya pernah gagal
            //         'updated_at' => now(),
            //     ]);

            // Jika 0 artinya:
            // - sudah queued oleh proses lain, ATAU
            // - sudah simpeg_at terisi (sudah sukses)
            // // => JANGAN dispatch lagi
            // if ((int) $updated !== 1) {
            //     \Log::info('[SIMASN] stop: $updated');
            //     return;
            // }

            // $url_sertifikat = route('sertifikat.show', [
            //     'peserta' => $pes->id,
            //     'jadwal' => $pes->diklat_jadwal_id,
            //     'sertifikat' => $pes->spid,
            //     'email' => str_slug($pes->email),
            // ]);

            // $jadwal = DB::table('v_jadwal_detail')->where('id', $jadwalId)->first();

            // $job = new UploadSimpegJob($pes, $jadwal, $sertifikat, $jenis, $kategori, $sub, $url_sertifikat);
            // $job->delay(now()->addSeconds(5));
            // $this->dispatch($job);
        } catch (\Exception $e) {
            \Log::info('[SIMASN] stop: exception: ' . $e->getMessage());
        }
    }
}
