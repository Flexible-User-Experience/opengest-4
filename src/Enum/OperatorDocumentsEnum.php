<?php

namespace App\Enum;

/**
 * OperatorDocumentsEnum class.
 *
 * @category Enum
 */
class OperatorDocumentsEnum
{
    public const taxIdentificationNumberImage = '0';
    public const drivingLicenseImage = '1';
    public const cranesOperatorLicenseImage = '2';
    public const medicalCheckImage = '3';
    public const episImage = '4';
    public const trainingDocumentImage = '5';
    public const informationImage = '6';
    public const useOfMachineryAuthorizationImage = '7';
    public const dischargeSocialSecurityImage = '8';
    public const employmentContractImage = '9';

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
            self::taxIdentificationNumberImage => 'DNI/NIE',
            self::drivingLicenseImage => 'Carnet de conducir',
            self::cranesOperatorLicenseImage => 'Autorización maquinaria',
            self::medicalCheckImage => 'Revisión médica',
            self::episImage => 'EPI\'s',
            self::trainingDocumentImage => 'Título de formación',
            self::informationImage => 'Laboral',
            self::useOfMachineryAuthorizationImage => 'Autorización maquinaria',
            self::dischargeSocialSecurityImage => 'Baja seguridad social',
            self::employmentContractImage => 'Contrato de trabajo',
        ];
    }
}
