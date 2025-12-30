<?php

namespace App\Enum;

/**
 * IvaEnum class.
 *
 * @category Enum
 */
class IvaEnum
{
    public const IVA_21 = 21;
    public const IVA_10 = 10;
    public const IVA_4 = 4;
    public const IVA_0 = 0;

    /**
     * @return array
     */
    public static function getEnumArray(): array
    {
        return array_flip(self::getReversedEnumArray());
    }

    /**
     * @return array
     */
    public static function getReversedEnumArray(): array
    {
        return [
            self::IVA_21 => 21,
            self::IVA_10 => 10,
            self::IVA_4 => 4,
            self::IVA_0 => 0,
        ];
    }
}
