<?php

namespace App\Manager;

use DateTimeImmutable;
use Exception;

/**
 * Class YearChoicesManager.
 *
 * @category Manager
 **/
class YearChoicesManager
{
    const INITIAL_YEAR = 1980;

    /**
     * Methods.
     */

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getYearRange(): array
    {
        $currentYear = new DateTimeImmutable();
        $years = array();
        for ($currentYear = intval($currentYear->format('Y')) + 1; $currentYear >= self::INITIAL_YEAR; --$currentYear) {
            $years[$currentYear] = $currentYear;
        }

        return $years;
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function getCurrentYear(): int
    {
        $currentYear = new DateTimeImmutable();
        $currentYear = intval($currentYear->format('Y'));

        return $currentYear;
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function getTodayString(): string
    {
        $today = new DateTimeImmutable();
        $today = $today->format('d/m/Y');

        return $today;
    }
}
