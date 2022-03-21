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
    public const LUNCH = '0';
    public const DINNER = '1';
    public const OVER_NIGHT = '2';
    public const EXTRA_NIGHT = '3';
    public const DIET = '4';
    public const INTERNATIONAL_LUNCH = '5';
    public const INTERNATIONAL_DINNER = '6';
    public const ROAD_NORMAL_HOUR = '7';
    public const CAR_OUTPUT = '8';

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
            self::LUNCH => 'Comida',
            self::DINNER => 'Cena',
            self::OVER_NIGHT => 'Pernoctación',
            self::EXTRA_NIGHT => 'Dieta internacional',
            self::DIET => 'Dieta',
            self::INTERNATIONAL_LUNCH => 'Comida internacional',
            self::INTERNATIONAL_DINNER => 'Cena internacional',
//            self::ROAD_NORMAL_HOUR => 'Plus carretera',
            self::CAR_OUTPUT => 'Salida',
        ];
    }

    public static function getCodeFromId($id)
    {
        $owruClass = new ReflectionClass(self::class);
        $constants = $owruClass->getConstants();
        $constName = null;
        foreach ($constants as $name => $value) {
            if ($value == $id) {
                $constName = $name;
                break;
            }
        }

        return $constName;
    }
}
