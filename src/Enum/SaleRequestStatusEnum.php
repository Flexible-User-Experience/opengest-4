<?php

namespace App\Enum;

/**
 * SaleRequestStatusEnum class.
 *
 * @category Enum
 */
class SaleRequestStatusEnum
{
    public const PENDING = '0';
    public const IN_PROCESS = '1';
    public const FINISHED = '2';
    public const DISCARDED = '3';
    public const TO_BE_APPROVED = '4';
    public const TO_SETTLE_ON = '5';

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array_flip(self::getReversedEnumArray());
    }

    /**
     * @return array
     */
    public static function getReversedEnumArray()
    {
        return [
            self::PENDING => 'Pendiente',
            self::IN_PROCESS => 'En proceso',
            self::FINISHED => 'Finalizada',
            self::DISCARDED => 'Descartada',
            self::TO_BE_APPROVED => 'Ofertada',
            self::TO_SETTLE_ON => 'Por concretar',
        ];
    }
}
