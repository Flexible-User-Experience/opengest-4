<?php

namespace App\Enum;

/**
 * TimeRangeTypeEnum class.
 *
 * @category Enum
 */
class TimeRangeTypeEnum
{
    public const WORKING = '0';
    public const NORMAL = '1';
    public const EXTRA = '2';
    public const HOLIDAY = '3';

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
            self::WORKING => 'Laboral',
            self::NORMAL => 'Normal',
            self::EXTRA => 'Extra',
            self::HOLIDAY => 'Festiva',
        ];
    }
}
