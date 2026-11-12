<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class BearerTokenSimAsnCommand extends Command
{
    protected $signature = 'token:simasn';

    protected $description = 'Perbarui Bearer Token SIMASN jika sudah kedaluwarsa';

    public function handle(): int
    {
        $tokenData = ApiToken::where('app_name', 'SIMASN')->first();

        if ($tokenData?->token && $tokenData->expires_at && Carbon::parse($tokenData->expires_at)->isFuture()) {
            $this->info('Token SIMASN masih valid. Tidak ada tindakan.');
            return self::SUCCESS;
        }

        $loginUrl = config('services.simasn.login_url');
        $username = config('services.simasn.username');
        $password = config('services.simasn.password');

        if (! $loginUrl || ! $username || ! $password) {
            $this->error('Konfigurasi SIMASN_LOGIN, SIMASN_USERNAME, atau SIMASN_PASSWORD belum lengkap.');
            return self::FAILURE;
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(30)
                ->post($loginUrl, [
                    'username' => $username,
                    'password' => $password,
                ]);

            if (! $response->successful()) {
                $this->error("Login SIMASN gagal: HTTP {$response->status()}.");
                Log::error('SIMASN token refresh failed', [
                    'status' => $response->status(),
                    'body' => str($response->body())->limit(500)->toString(),
                ]);
                return self::FAILURE;
            }

            $payload = $response->json();
            $token = $payload['access_token'] ?? null;
            $expiresAt = $payload['expires_at'] ?? null;

            if (! $token || ! $expiresAt) {
                $this->error('Respons login SIMASN tidak berisi access_token atau expires_at.');
                Log::error('SIMASN token refresh returned incomplete payload', [
                    'keys' => array_keys($payload ?? []),
                ]);
                return self::FAILURE;
            }

            ApiToken::updateOrCreate(
                ['app_name' => 'SIMASN'],
                [
                    'token' => $token,
                    'type' => 0,
                    'expires_at' => Carbon::parse($expiresAt),
                ]
            );

            $this->info('Token SIMASN berhasil diperbarui.');
            return self::SUCCESS;
        } catch (Throwable $e) {
            Log::error('SIMASN token refresh exception', [
                'message' => $e->getMessage(),
            ]);
            $this->error('Gagal memperbarui token SIMASN: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
