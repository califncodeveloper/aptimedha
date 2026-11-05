<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Service for verifying Cloudflare Turnstile tokens via Cloudflare's Siteverify API.
 */
class TurnstileService
{
    public const SITEVERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Verify the Turnstile response token with Cloudflare's Siteverify API.
     *
     * @param string|null $token The cf-turnstile-response token submitted by client.
     * @param string|null $remoteIp The user's IP address.
     * @param string|null $expectedAction The expected action name (e.g., 'register', 'contact').
     * @return bool
     */
    public function verify(?string $token, ?string $remoteIp = null, ?string $expectedAction = null): bool
    {
        if (empty($token) || !is_string($token) || trim($token) === '') {
            Log::info('Turnstile verification failed: missing or empty token.');
            return false;
        }

        $token = trim($token);
        $secretKey = config('services.turnstile.secret_key');

        if (empty($secretKey)) {
            Log::warning('Turnstile secret key is not configured in services.turnstile.secret_key.');
            return false;
        }

        // Prevent token reuse (replay attacks). Cloudflare tokens expire in ~300 seconds.
        $cacheKey = 'turnstile_token_used:' . hash('sha256', $token);
        if (!Cache::add($cacheKey, true, 300)) {
            Log::warning('Turnstile verification rejected: token reuse detected.', [
                'token_prefix' => substr($token, 0, 8),
            ]);
            return false;
        }

        try {
            $payload = [
                'secret' => $secretKey,
                'response' => $token,
            ];

            if (!empty($remoteIp)) {
                $payload['remoteip'] = $remoteIp;
            }

            $response = Http::asForm()
                ->timeout(5)
                ->connectTimeout(3)
                ->post(self::SITEVERIFY_URL, $payload);

            if (!$response->successful()) {
                Log::warning('Cloudflare Turnstile siteverify returned HTTP error.', [
                    'status' => $response->status(),
                ]);
                return false;
            }

            $result = $response->json();

            if (!is_array($result) || empty($result['success'])) {
                Log::info('Cloudflare Turnstile verification unsuccessful.', [
                    'error_codes' => $result['error-codes'] ?? [],
                ]);
                return false;
            }

            // Verify action name if provided
            if ($expectedAction !== null && !empty($result['action'])) {
                if ($result['action'] !== $expectedAction) {
                    Log::warning('Turnstile action mismatch.', [
                        'expected' => $expectedAction,
                        'received' => $result['action'],
                    ]);
                    return false;
                }
            }

            // Verify hostname if present and running in production
            if (!empty($result['hostname']) && app()->environment('production')) {
                $appHost = parse_url(config('app.url'), PHP_URL_HOST);
                if ($appHost && strcasecmp($result['hostname'], $appHost) !== 0) {
                    Log::warning('Turnstile hostname mismatch in production.', [
                        'expected' => $appHost,
                        'received' => $result['hostname'],
                    ]);
                    return false;
                }
            }

            return true;
        } catch (Throwable $e) {
            // Fail closed: never silently bypass CAPTCHA on errors or timeouts
            Log::warning('Cloudflare Turnstile verification failed due to exception: ' . $e->getMessage());
            return false;
        }
    }
}

