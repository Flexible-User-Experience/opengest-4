<?php

namespace App\Enum;

/**
 * TimeRangeTypeEnum class.
 *
 * @category Enum
 */
class TimeRangeTypeEnum
{
    const WORKING          = '0';
    const NORMAL           = '1';
    const EXTRA            = '2';

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
        ];
    }
}
