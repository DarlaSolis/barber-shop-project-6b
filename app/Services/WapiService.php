<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class WapiService
{
    protected string $baseUrl;
    protected string $token;
    protected string $sessionId;

    public function __construct()
    {
        $this->baseUrl   = rtrim(env('WAPI2_BASE_URL'), '/');
        $this->token     = env('WAPI2_TOKEN');
        $this->sessionId = env('WAPI2_SESSION_ID');
    }

    public function sendMessage(string $to, string $message): void
    {
        // session_id va como query param, no en el body
        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/chat/{$this->formatPhone($to)}/message?session_id={$this->sessionId}", [
                'message' => $message,
            ]);

        if (!$response->successful() || ($response->json('status') ?? null) === 'error') {
            throw new RuntimeException('Wapi2 error: ' . $response->body());
        }
    }

    private function formatPhone(string $to): string
    {
        $digits = preg_replace('/\D/', '', $to);

        // Número local mexicano a 10 dígitos: anteponer 521 (código país + prefijo móvil)
        if (strlen($digits) === 10) {
            $digits = '521' . $digits;
        }

        return $digits;
    }
}
