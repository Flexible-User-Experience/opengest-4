<?php

namespace App\Enum;

/**
 * OperatorWorkRegisterTimeEnum class.
 *
 * @category Enum
 */
class OperatorWorkRegisterTimeEnum
{
    const SERVICE          = '0';
    const JOURNEY           = '1';
    const WAIT            = '2';

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
            self::SERVICE => 'Servicio',
            self::JOURNEY => 'Desplazamiento',
            self::WAIT => 'Espera',
        ];
    }
}
