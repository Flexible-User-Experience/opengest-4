<?php

namespace App\Enum;

/**
 * EnterpriseDocumentsEnum class.
 *
 * @category Enum
 */
class EnterpriseDocumentsEnum
{
    public const deedOfIncorporation = '0';
    public const taxIdentificationNumberCard = '1';
    public const tc1Receipt = '2';
    public const tc2Receipt = '3';
    public const ssRegistration = '4';
    public const ssPaymentCertificate = '5';
    public const rc1Insurance = '6';
    public const rc2Insurance = '7';
    public const rcReceipt = '8';
    public const preventionServiceContract = '9';
    public const preventionServiceInvoice = '10';
    public const preventionServiceReceipt = '11';
    public const occupationalAccidentsInsurance = '12';
    public const occupationalReceipt = '13';
    public const laborRiskAssessment = '14';
    public const securityPlan = '15';
    public const reaCertificate = '16';
    public const oilCertificate = '17';
    public const gencatPaymentCertificate = '18';
    public const deedsOfPowers = '19';
    public const iaeRegistration = '20';
    public const iaeReceipt = '21';
    public const mutualPartnership = '22';

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
            self::deedOfIncorporation => 'admin.with.enterprise.deed_of_incorporation',
            self::taxIdentificationNumberCard => 'admin.with.enterprise.tax_identification_number_card',
            self::tc1Receipt => 'admin.with.enterprise.tc1_receipt',
            self::tc2Receipt => 'admin.with.enterprise.tc2_receipt',
            self::ssRegistration => 'admin.with.enterprise.ss_registration',
            self::ssPaymentCertificate => 'admin.with.enterprise.ss_payment_certificate',
            self::rc1Insurance => 'admin.with.enterprise.rc1_insurance',
            self::rc2Insurance => 'admin.with.enterprise.rc2_insurance',
            self::rcReceipt => 'admin.with.enterprise.rc_receipt',
            self::preventionServiceContract => 'admin.with.enterprise.prevention_service_contract',
            self::preventionServiceInvoice => 'admin.with.enterprise.prevention_service_invoice',
            self::preventionServiceReceipt => 'admin.with.enterprise.prevention_service_receipt',
            self::occupationalAccidentsInsurance => 'admin.with.enterprise.occupational_accidents_insurance',
            self::occupationalReceipt => 'admin.with.enterprise.occupational_receipt',
            self::laborRiskAssessment => 'admin.with.enterprise.labor_risk_assessment',
            self::securityPlan => 'admin.with.enterprise.security_plan',
            self::reaCertificate => 'admin.with.enterprise.rea_certificate',
            self::oilCertificate => 'admin.with.enterprise.oil_certificate',
            self::gencatPaymentCertificate => 'admin.with.enterprise.gencat_payment_certificate',
            self::deedsOfPowers => 'admin.with.enterprise.deeds_of_powers',
            self::iaeRegistration => 'admin.with.enterprise.iae_registration',
            self::iaeReceipt => 'admin.with.enterprise.iae_receipt',
            self::mutualPartnership => 'admin.with.enterprise.mutual_partnership',
        ];
    }

    public static function getName(int $id)
    {
        $enum = [
            'deedOfIncorporation',
            'taxIdentificationNumberCard',
            'tc1Receipt',
            'tc2Receipt',
            'ssRegistration',
            'ssPaymentCertificate',
            'rc1Insurance',
            'rc2Insurance',
            'rcReceipt',
            'preventionServiceContract',
            'preventionServiceInvoice',
            'preventionServiceReceipt',
            'occupationalAccidentsInsurance',
            'occupationalReceipt',
            'laborRiskAssessment',
            'securityPlan',
            'reaCertificate',
            'oilCertificate',
            'gencatPaymentCertificate',
            'deedsOfPowers',
            'iaeRegistration',
            'iaeReceipt',
            'mutualPartnership',
        ];

        return $enum[$id];
    }
}
