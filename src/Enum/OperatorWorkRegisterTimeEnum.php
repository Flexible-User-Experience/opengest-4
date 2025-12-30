<?php

namespace App\Enum;

/**
 * OperatorWorkRegisterTimeEnum class.
 *
 * @category Enum
 */
class OperatorWorkRegisterTimeEnum
{
    public const SERVICE = '0';
    public const JOURNEY = '1';
    public const WAIT = '2';
    public const NEGATIVE_HOUR = '3';

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
            self::SERVICE => 'Servicio',
            self::JOURNEY => 'Desplazamiento',
            self::WAIT => 'Espera',
            self::NEGATIVE_HOUR => 'Hora negativa',
        ];
    }
}
