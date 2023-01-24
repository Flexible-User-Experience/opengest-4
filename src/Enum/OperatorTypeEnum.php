<?php

namespace App\Enum;

/**
 * OperatorTypeEnum class.
 *
 * @category Enum
 */
class OperatorTypeEnum
{
    public const OPERATOR = '0';
    public const OFFICE = '1';
    public const GARAGE = '2';

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
            self::OPERATOR => 'Operario',
            self::OFFICE => 'Oficina',
            self::GARAGE => 'Taller',
        ];
    }
}
