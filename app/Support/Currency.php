<?php

namespace App\Support;

class Currency
{
    /**
     * The franc CFA (XOF) is pegged to the euro at a fixed treaty rate (BCEAO) —
     * this is a real, stable conversion, not a fluctuating market rate.
     */
    protected const XOF_PER_EUR = 655.957;

    public static function eurFromFcfa(float $fcfa): float
    {
        return $fcfa / self::XOF_PER_EUR;
    }

    public static function formatEur(float $fcfa): string
    {
        return number_format(self::eurFromFcfa($fcfa), 2, ',', ' ').' €';
    }
}
