<?php

namespace App\Services\Analytics;

class UaParser
{
    /**
     * @return array{device_type: string, browser: ?string, os: ?string}
     */
    public static function parse(string $userAgent): array
    {
        return [
            'device_type' => self::deviceType($userAgent),
            'browser' => self::browser($userAgent),
            'os' => self::os($userAgent),
        ];
    }

    private static function deviceType(string $ua): string
    {
        if (preg_match('/iPad|Tablet(?!.*Mobile)|Nexus (7|9|10)/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/Mobi|Android.*Mobile|iPhone|iPod/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    private static function browser(string $ua): ?string
    {
        return match (true) {
            (bool) preg_match('/Edg\//i', $ua) => 'Edge',
            (bool) preg_match('/OPR\/|Opera/i', $ua) => 'Opera',
            (bool) preg_match('/CriOS\/|Chrome\//i', $ua) => 'Chrome',
            (bool) preg_match('/FxiOS\/|Firefox\//i', $ua) => 'Firefox',
            (bool) preg_match('/Version\/.*Safari/i', $ua) => 'Safari',
            (bool) preg_match('/MSIE|Trident/i', $ua) => 'Internet Explorer',
            default => null,
        };
    }

    private static function os(string $ua): ?string
    {
        return match (true) {
            (bool) preg_match('/iPhone|iPad|iPod/i', $ua) => 'iOS',
            (bool) preg_match('/Android/i', $ua) => 'Android',
            (bool) preg_match('/Windows/i', $ua) => 'Windows',
            (bool) preg_match('/Mac OS X/i', $ua) => 'macOS',
            (bool) preg_match('/Linux/i', $ua) => 'Linux',
            default => null,
        };
    }

    private const BOT_SIGNATURES = [
        'bot', 'spider', 'crawl', 'slurp', 'facebookexternalhit', 'bingpreview',
        'googlebot', 'bingbot', 'yandex', 'duckduckbot', 'baiduspider', 'ahrefsbot',
        'semrushbot', 'mj12bot', 'petalbot', 'applebot', 'headlesschrome',
    ];

    public static function isBot(string $userAgent): bool
    {
        $ua = mb_strtolower($userAgent);

        foreach (self::BOT_SIGNATURES as $signature) {
            if (str_contains($ua, $signature)) {
                return true;
            }
        }

        return false;
    }
}
