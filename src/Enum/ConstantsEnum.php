<?php

namespace App\Enum;

/**
 * ConstantsEnum class.
 *
 * @category Enum
 */
class ConstantsEnum
{
    /**
     * Business.
     */
    const IVA = 21;
    const IRPF = 0;
    const DEFAULT_COUNTRY_CODE_SPAIN = 'ES';
    const DEFAULT_COUNTRY_SPAIN = 'ESPAÑA';

    /**
     * Frontend.
     */
    const FRONTEND_ITEMS_PER_PAGE_LIMIT = 10;

    /**
     * PDF.
     */
    const PDF_DEFAULT_FONT = 'Helvetica';
    const PDF_OPENGEST_V1_FONT = 'FreeSerif';
    const PDF_PORTRAIT_PAGE_ORIENTATION = 'P';
    const PDF_LANDSCAPE_PAGE_ORIENTATION = 'L';
    const PDF_PAGE_UNITS = 'mm';
    const PDF_PAGE_A4 = 'A4';
    const PDF_PAGE_A5 = 'A5';
    const PDF_PAGE_A5_MARGIN_LEFT = 10;
    const PDF_CELL_HEIGHT = 6;

    /**
     * Http.
     */
    const HTTP_PROTOCOL = 'https://';
    const PHP_SERVER_API_CLI_CONTEXT = 'cli';
    const SYMFONY_DEV_ENVIRONMENT = 'dev';
}
