<?php

namespace App\Enum;

/**
 * OperatorCheckingTypeCategoryEnum class.
 *
 * @category Enum
 */
enum OperatorCheckingTypeCategoryEnum: int
{
    public const CHECKING = 0;
    public const PPE = 1;
    public const TRAINING = 2;

    public static function getEnumArray(): array
    {
        return array_flip(self::getReversedEnumArray());
    }

    public static function getReversedEnumArray(): array
    {
        return [
            self::CHECKING => 'Revisión',
            self::PPE => 'EPI',
            self::TRAINING => 'Formación',
        ];
    }
}
