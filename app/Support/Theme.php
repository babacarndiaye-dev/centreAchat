<?php

namespace App\Support;

use App\Models\Setting;

class Theme
{
    protected const DEFAULTS = [
        'color_primary' => '#1D8A4E',
        'color_secondary' => '#E8604F',
        'color_accent' => '#F0A93B',
    ];

    protected const CSS_VARS = [
        'color_primary' => '--terroir-green',
        'color_secondary' => '--terroir-terracotta',
        'color_accent' => '--terroir-gold',
    ];

    public static function cssVariables(): string
    {
        $declarations = [];

        foreach (self::CSS_VARS as $settingKey => $cssVar) {
            $hex = Setting::get($settingKey, self::DEFAULTS[$settingKey]);
            $declarations[] = "{$cssVar}: ".self::hexToRgbTriplet($hex ?: self::DEFAULTS[$settingKey]).';';
        }

        return ':root{'.implode('', $declarations).'}';
    }

    public static function hexToRgbTriplet(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            $hex = '1E4A3D';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "{$r} {$g} {$b}";
    }
}
