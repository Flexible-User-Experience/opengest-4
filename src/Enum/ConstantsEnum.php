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
    public const IVA = 21;
    public const IRPF = 0;
    public const DEFAULT_COUNTRY_CODE_SPAIN = 'ES';
    public const DEFAULT_COUNTRY_SPAIN = 'ESPAÑA';

    /**
     * Frontend.
     */
    public const FRONTEND_ITEMS_PER_PAGE_LIMIT = 10;

    /**
     * PDF.
     */
    public const PDF_DEFAULT_FONT = 'Helvetica';
    public const PDF_OPENGEST_V1_FONT = 'FreeSerif';
    public const PDF_PORTRAIT_PAGE_ORIENTATION = 'P';
    public const PDF_LANDSCAPE_PAGE_ORIENTATION = 'L';
    public const PDF_PAGE_UNITS = 'mm';
    public const PDF_PAGE_A4 = 'A4';
    public const PDF_PAGE_A5 = 'A5';
    public const PDF_PAGE_A5_MARGIN_LEFT = 10;
    public const PDF_PAGE_A4_MARGIN_LEFT = 15;
    public const PDF_PAGE_A4_MARGIN_RIGHT = 10;
    public const PDF_PAGE_A4_MARGIN_TOP = 5;
    public const PDF_PAGE_A4_WIDTH_LANDSCAPE = 266;
    public const PDF_PAGE_A4_HEIGHT_LANDSCAPE = 175;
    public const PDF_CELL_HEIGHT = 6;

    /**
     * Http.
     */
    public const HTTP_PROTOCOL = 'https://';
    public const PHP_SERVER_API_CLI_CONTEXT = 'cli';
    public const SYMFONY_DEV_ENVIRONMENT = 'dev';

    /**
     *  Time pickers.
     */
    public const TIME_PICKER_HOURS = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
    public const TIME_PICKER_MINUTES = ['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55'];
}
