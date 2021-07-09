<?php

namespace App\Enum;

/**
 * SaleItemTypeEnum class.
 *
 * @category Enum
 */
class SaleItemTypeEnum
{
    const SERVICE          = '0';
    const SALE             = '1';
    const SERVICE_AND_SALE = '2';

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
            self::SERVICE => 'Servei',
            self::SALE => 'Venta',
            self::SERVICE_AND_SALE => 'Servei i venta',
        ];
    }
}
