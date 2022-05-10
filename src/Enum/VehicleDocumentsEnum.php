<?php

namespace App\Enum;

/**
 * VehicleDocumentsEnum class.
 *
 * @category Enum
 */
class VehicleDocumentsEnum
{
    public const chassisImage= '0';
    public const technicalDatasheet1= '1';
    public const technicalDatasheet2= '2';
    public const loadTable= '3';
    public const reachDiagram= '4';
    public const trafficCertificate= '5';
    public const dimensions= '6';
    public const transportCard= '7';
    public const trafficInsurance= '8';
    public const itv= '9';
    public const itc= '10';
    public const CEDeclaration= '11';

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
            self::chassisImage => 'admin.with.vehicle.chassis_image',
            self::technicalDatasheet1 => 'admin.with.vehicle.technical_datasheet_1',
            self::technicalDatasheet2 => 'admin.with.vehicle.technical_datasheet_2',
            self::loadTable => 'admin.with.vehicle.load_table',
            self::reachDiagram => 'admin.with.vehicle.reach_diagram',
            self::trafficCertificate => 'admin.with.vehicle.traffic_certificate',
            self::dimensions => 'admin.with.vehicle.dimensions',
            self::transportCard => 'admin.with.vehicle.transport_card',
            self::trafficInsurance => 'admin.with.vehicle.traffic_insurance',
            self::itv => 'admin.with.vehicle.itv',
            self::itc => 'admin.with.vehicle.itc',
            self::CEDeclaration => 'admin.with.vehicle.c_e_declaration'
        ];
    }

    public static function getName(int $id)
    {
        $enum = [
            'chassisImage',
            'technicalDatasheet1',
            'technicalDatasheet2',
            'loadTable',
            'reachDiagram',
            'trafficCertificate',
            'dimensions',
            'transportCard',
            'trafficInsurance',
            'itv',
            'itc',
            'CEDeclaration',
        ];

        return $enum[$id];
    }
}
