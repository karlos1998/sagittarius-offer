<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class TurnstileService
{
    public const SITEVERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function enabled(): bool
    {
        return (bool) config('services.turnstile.enabled');
    }

    public function siteKey(): ?string
    {
        return $this->filledConfigValue('services.turnstile.site_key');
    }

    /**
     * @return array{enabled: bool, site_key: string|null}
     */
    public function configurationForFrontend(): array
    {
        return [
            'enabled' => $this->enabled(),
            'site_key' => $this->siteKey(),
        ];
    }

    public function verify(string $token, ?string $ipAddress = null): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        $secretKey = $this->secretKey();

        if ($secretKey === null || trim($token) === '') {
            return false;
        }

        try {
            $payload = [
                'secret' => $secretKey,
                'response' => $token,
            ];

            if ($ipAddress !== null && $ipAddress !== '') {
                $payload['remoteip'] = $ipAddress;
            }

            $response = Http::asForm()
                ->timeout(5)
                ->post(self::SITEVERIFY_URL, $payload);
        } catch (Throwable) {
            return false;
        }

        return $response->ok() && $response->json('success') === true;
    }

    private function secretKey(): ?string
    {
        return $this->filledConfigValue('services.turnstile.secret_key');
    }

    private function filledConfigValue(string $key): ?string
    {
        $value = config($key);

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
