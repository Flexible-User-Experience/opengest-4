<?php

namespace App\Enum;

use ReflectionClass;

/**
 * OperatorWorkRegisterUnitEnum class.
 *
 * @category Enum
 */
class OperatorWorkRegisterUnitEnum
{
    const LUNCH                 = '0';
    const DINNER                = '1';
    const OVER_NIGHT            = '2';
    const EXTRA_NIGHT           = '3';
    const DIET                  = '4';
    const INTERNATIONAL_LUNCH   = '5';
    const INTERNATIONAL_DINNER  = '6';
    const TRUCK_OUTPUT          = '7';
    const CAR_OUTPUT            = '8';

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
            self::LUNCH                => 'Comida',
            self::DINNER               => 'Cena',
            self::OVER_NIGHT           => 'Pernoctación',
            self::EXTRA_NIGHT          => 'Pernoctación extra',
            self::DIET                 => 'Dieta',
            self::INTERNATIONAL_LUNCH  => 'Comida internacional',
            self::INTERNATIONAL_DINNER => 'Cena internacional',
            self::TRUCK_OUTPUT         => 'Salida camión',
            self::CAR_OUTPUT           => 'Salida coche',
        ];
    }

    public static function getCodeFromId($id) {
        $owruClass = new ReflectionClass ( self::class );
        $constants = $owruClass->getConstants();
        $constName = null;
        foreach ( $constants as $name => $value )
        {
            if ( $value == $id )
            {
                $constName = $name;
                break;
            }
        }

        return $constName;
    }
}
