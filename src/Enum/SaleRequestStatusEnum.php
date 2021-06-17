<?php

namespace App\Enum;

/**
 * SaleRequestStatusEnum class.
 *
 * @category Enum
 */
class SaleRequestStatusEnum
{
    const PENDING          = '0';
    const IN_PROCESS       = '1';
    const FINISHED         = '2';
    const DISCARDED        = '3';
    const TO_BE_APPROVED   = '4';
    const TO_SETTLE_ON     = '5';

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
            self::PENDING => 'Pendent',
            self::IN_PROCESS => 'En procés',
            self::FINISHED => 'Finalitzada',
            self::DISCARDED => 'Descartada',
            self::TO_BE_APPROVED => 'Ofertada',
            self::TO_SETTLE_ON => 'Per concretar',
        ];
    }
}
