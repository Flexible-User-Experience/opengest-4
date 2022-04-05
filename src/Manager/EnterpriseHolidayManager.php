<?php

namespace App\Manager;

use App\Repository\Enterprise\EnterpriseHolidaysRepository;
use DateTime;

class EnterpriseHolidayManager
{
    private EnterpriseHolidaysRepository $ehr;

    public function __construct(EnterpriseHolidaysRepository $enterpriseHolidaysRepository)
    {
        $this->ehr = $enterpriseHolidaysRepository;
    }

    public function checkIfDayIsEnterpriseHoliday(DateTime $date)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d'.' 00:00:00'));
        $holiday = false;
        if ($date->format('N') >= 6) {
            $holiday = true;
        } elseif ($this->ehr->findOneBy(['day' => $date])) {
            $holiday = true;
        }

        return $holiday;
    }
}
