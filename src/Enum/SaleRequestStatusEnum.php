<?php

namespace App\Enum;

/**
 * SaleRequestStatusEnum class.
 *
 * @category Enum
 */
class SaleRequestStatusEnum
{
    const TO_BE_APPROVED   = 'TO_BE_APPROVED';
    const TO_SETTLE_ON     = 'TO_SETTLE_ON';
    const PENDING          = 'PENDING';
    const IN_PROCESS       = 'IN_PROCESS';
    const FINISHED         = 'FINISHED';
    const DISCARDED        = 'DISCARDED';

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
            self::TO_BE_APPROVED => 'Ofertada',
            self::TO_SETTLE_ON => 'Per concretar',
            self::PENDING => 'Pendent',
            self::IN_PROCESS => 'En procés',
            self::FINISHED => 'Finalitzada',
            self::DISCARDED => 'Descartada',
        ];
    }
}
