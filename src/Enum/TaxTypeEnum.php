<?php

namespace App\Enum;

enum TaxTypeEnum: string
{
    case INTERNAL_OPERATIONS = '01'; // Internal operations
    case INTRACOMMUNITY_ACQUISITIONS = '02'; // Intra-community acquisitions
    case INTRACOMMUNITY_SUPPLIES = '03'; // Intra-community supplies
    case IMPORTS = '04'; // Imports
    case EXPORTS = '05'; // Exports
    case REVERSE_CHARGE = '06'; // Reverse charge (inversion of the taxable person)
    case INTRACOMMUNITY_SERVICE_PROVISION = '07'; // Intra-community service provision
    case INTRACOMMUNITY_SERVICE_ACQUISITION = '08'; // Intra-community service acquisition
    case VAT_NOT_SUBJECT = '09';

    public static function getEnumArray(): array
    {
        return array_flip(self::getReversedEnumArray());
    }

    public static function getReversedEnumArray(): array
    {
        return [
            self::INTERNAL_OPERATIONS->value => 'Operaciones Interiores',
            self::INTRACOMMUNITY_ACQUISITIONS->value => 'Adquisiones intracomunitarias',
            self::INTRACOMMUNITY_SUPPLIES->value => 'Entregas intracomunitarias',
            self::IMPORTS->value => 'Importaciones',
            self::EXPORTS->value => 'Exportaciones',
            self::REVERSE_CHARGE->value => 'ISP Inversion del Sujeto Pasivo',
            self::INTRACOMMUNITY_SERVICE_PROVISION->value => 'Prestación Servicios intracomunitarios',
            self::INTRACOMMUNITY_SERVICE_ACQUISITION->value => 'Adquision intracomunitaria de servicios',
            self::VAT_NOT_SUBJECT->value => 'Iva no sujeto',
        ];
    }
}
