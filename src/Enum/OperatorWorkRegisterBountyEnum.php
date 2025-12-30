<?php

namespace App\Enum;

use ReflectionClass;

/**
 * OperatorWorkRegisterUnitEnum class.
 *
 * @category Enum
 */
class OperatorWorkRegisterBountyEnum
{
    public const TRANSP = '0';
    public const CP40 = '1';
    public const CPPLUS40 = '2';
    public const CRANE40 = '3';
    public const CRANE50 = '4';
    public const CRANE60 = '5';
    public const CRANE80 = '6';
    public const CRANE100 = '7';
    public const CRANE120 = '8';
    public const CRANE200 = '9';
    public const CRANE250300 = '10';
    public const PLATFORM40 = '11';
    public const PLATFORM50 = '12';
    public const PLATFORM60 = '13';
    public const PLATFORM70 = '14';

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
            self::TRANSP => 'admin.label.bounty.transp',
            self::CP40 => 'admin.label.bounty.cp40',
            self::CPPLUS40 => 'admin.label.bounty.cpPlus40',
            self::CRANE40 => 'admin.label.bounty.crane40',
            self::CRANE50 => 'admin.label.bounty.crane50',
            self::CRANE60 => 'admin.label.bounty.crane60',
            self::CRANE80 => 'admin.label.bounty.crane80',
            self::CRANE100 => 'admin.label.bounty.crane100',
            self::CRANE120 => 'admin.label.bounty.crane120',
            self::CRANE200 => 'admin.label.bounty.crane200',
            self::CRANE250300 => 'admin.label.bounty.crane250300',
            self::PLATFORM40 => 'admin.label.bounty.platform40',
            self::PLATFORM50 => 'admin.label.bounty.platform50',
            self::PLATFORM60 => 'admin.label.bounty.platform60',
            self::PLATFORM70 => 'admin.label.bounty.platform70',
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
