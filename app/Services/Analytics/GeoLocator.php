<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocator
{
    /**
     * Best-effort, non-blocking-caller IP lookup. Returns null on any failure
     * or for local/private IPs. The IP itself is never persisted — callers
     * only keep the returned country/city.
     *
     * @return array{country: ?string, city: ?string}|null
     */
    public static function lookup(string $ip): ?array
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        try {
            $response = Http::timeout(1.5)
                ->connectTimeout(1.5)
                ->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,countryCode,city']);

            if (! $response->ok() || $response->json('status') !== 'success') {
                return null;
            }

            return [
                'country' => $response->json('countryCode'),
                'city' => $response->json('city'),
            ];
        } catch (\Throwable $e) {
            Log::debug('GeoLocator lookup failed', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
