<?php

namespace App\Enum;

/**
 * OperatorDocumentsEnum class.
 *
 * @category Enum
 */
class OperatorDocumentsEnum
{
    public const taxIdentificationNumber = '0';
    public const drivingLicense = '1';
    public const cranesOperatorLicense = '2';
    public const medicalCheck = '3';
    public const epis = '4';
    public const trainingDocument = '5';
    public const information = '6';
    public const useOfMachineryAuthorization = '7';
    public const dischargeSocialSecurity = '8';
    public const employmentContract = '9';

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
            self::taxIdentificationNumber => 'admin.with.operator.tax_identification_number',
            self::drivingLicense => 'admin.with.operator.driving_license',
            self::cranesOperatorLicense => 'admin.with.operator.cranes_operator_license',
            self::medicalCheck => 'admin.with.operator.medical_check',
            self::epis => 'admin.with.operator.epis',
            self::trainingDocument => 'admin.with.operator.training_document',
            self::information => 'admin.with.operator.information',
            self::useOfMachineryAuthorization => 'admin.with.operator.use_of_machinery_authorization',
            self::dischargeSocialSecurity => 'admin.with.operator.discharge_social_security',
            self::employmentContract => 'admin.with.operator.employment_contract',
        ];
    }

    public static function getName(int $id)
    {
        $enum = [
            'taxIdentificationNumber',
            'drivingLicense',
            'cranesOperatorLicense',
            'medicalCheck',
            'epis',
            'trainingDocument',
            'information',
            'useOfMachineryAuthorization',
            'dischargeSocialSecurity',
            'employmentContract',
        ];

        return $enum[$id];
    }
}
