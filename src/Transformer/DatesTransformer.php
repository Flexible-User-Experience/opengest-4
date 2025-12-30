<?php

namespace App\Transformer;

use DateTime;
use DateTimeInterface;
use Exception;

/**
 * Class DatesTransformer.
 *
 * @category Transformer
 *
 * @author   David Romaní <david@flux.cat>
 */
class DatesTransformer
{
    /**
     * @param string $dateCode
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public function convertStringWithDayAndMonthToDateTime(string $dateCode): DateTime
    {
        $date = explode('/', $dateCode);
        if (2 != count($date)) {
            throw new Exception('Expected DD/MM format');
        }
        if (!is_numeric($date[0])) {
            throw new Exception('Expected numeric day format');
        }
        if (!is_numeric($date[1])) {
            throw new Exception('Expected numeric month format');
        }
        $day = new DateTime();
        $day->setDate(0, intval($date[1]), intval($date[0]));
        $day->setTime(0, 0, 0);

        return $day;
    }
}
