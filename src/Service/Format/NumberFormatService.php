<?php

namespace App\Service\Format;

class NumberFormatService
{
    public static function formatNumber(float $number): string
    {
        if (0 != $number - round($number)) {
            return number_format($number, 2, ',', '');
        } else {
            return number_format($number, 0, ',', '');
        }
    }
}
