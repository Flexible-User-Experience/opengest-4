<?php

namespace App\Service\Format;

class NumberFormatService
{
    public static function formatNumber(?float $number, $decimalsIfZeroDecimals = false): string
    {
        if (!$number) {
            $number = 0;
        }
        if ((0 != $number - round($number)) || ($decimalsIfZeroDecimals)) {
            return number_format($number, 2, ',', '.');
        } else {
            return number_format($number, 0, ',', '.');
        }
    }
}
