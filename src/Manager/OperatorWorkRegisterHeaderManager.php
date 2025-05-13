<?php

namespace App\Manager;

use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Operator\OperatorWorkRegisterHeader;

class OperatorWorkRegisterHeaderManager
{
    public function getTotalsFromDifferentWorkRegisterHeaders($workRegisterHeaders)
    {
        $detailedHours = [
            'normalHours' => 0,
            'extraHours' => 0,
            'negativeHours' => 0,
            'displacement' => 0,
            'waiting' => 0,
            'lunch' => 0,
            'lunchInt' => 0,
            'dinner' => 0,
            'dinnerInt' => 0,
            'diet' => 0,
            'dietInt' => 0,
            'overNight' => 0,
            'exitExtra' => 0,
        ];
        foreach ($workRegisterHeaders as $workRegisterHeader) {
            $totalHoursFromWorkRegister = $this->getTotalsFromWorkRegisterHeader($workRegisterHeader);
            foreach ($detailedHours as $key => $value) {
                $detailedHours[$key] += $totalHoursFromWorkRegister[$key];
            }
        }

        return $detailedHours;
    }

    public function getTotalsFromWorkRegisterHeader(OperatorWorkRegisterHeader $workRegisterHeader): array
    {
        $detailedHours = [
            'normalHours' => 0,
            'extraHours' => 0,
            'holidayHours' => 0,
            'negativeHours' => 0,
            'displacement' => 0,
            'waiting' => 0,
            'lunch' => 0,
            'lunchInt' => 0,
            'dinner' => 0,
            'dinnerInt' => 0,
            'diet' => 0,
            'dietInt' => 0,
            'overNight' => 0,
            'exitExtra' => 0,
        ];
        foreach ($workRegisterHeader->getOperatorWorkRegisters() as $workRegister) {
            $detailedHoursFromWorkRegister = $this->getDetailedHoursFromWorkRegister($workRegister);
            foreach ($detailedHours as $key => $value) {
                $detailedHours[$key] += $detailedHoursFromWorkRegister[$key];
            }
        }

        return $detailedHours;
    }

    /**
     * @return array|int[]
     */
    public function getPricesForOperator($operator): array
    {
        $bountyGroup = $operator->getEnterpriseGroupBounty();

        return [
            'normalHourPrice' => $bountyGroup ? $bountyGroup->getExtraNormalHour() : 0,
            'extraHourPrice' => $bountyGroup ? $bountyGroup->getExtraExtraHour() : 0,
            'holidayHourPrice' => $bountyGroup ? $bountyGroup->getHolidayHour() : 0,
            'negativeHourPrice' => $bountyGroup ? $bountyGroup->getNegativeHour() : 0,
            'lunchPrice' => $bountyGroup ? $bountyGroup->getLunch() : 0,
            'lunchIntPrice' => $bountyGroup ? $bountyGroup->getInternationalLunch() : 0,
            'dinnerPrice' => $bountyGroup ? $bountyGroup->getDinner() : 0,
            'dinnerIntPrice' => $bountyGroup ? $bountyGroup->getInternationalDinner() : 0,
            'dietPrice' => $bountyGroup ? $bountyGroup->getDiet() : 0,
            'dietIntPrice' => $bountyGroup ? $bountyGroup->getExtraNight() : 0,
            'overNightPrice' => $bountyGroup ? $bountyGroup->getOverNight() : 0,
            'exitExtraPrice' => $bountyGroup ? $bountyGroup->getCarOutput() : 0,
        ];
    }

    private function getDetailedHoursFromWorkRegister(OperatorWorkRegister $workRegister): array
    {
        $detailedHours = [
            'normalHours' => 0,
            'extraHours' => 0,
            'holidayHours' => 0,
            'negativeHours' => 0,
            'displacement' => 0,
            'waiting' => 0,
            'lunch' => 0,
            'lunchInt' => 0,
            'dinner' => 0,
            'dinnerInt' => 0,
            'diet' => 0,
            'dietInt' => 0,
            'overNight' => 0,
            'exitExtra' => 0,
        ];
        if (str_contains($workRegister->getDescription(), 'Hora negativa')) {
            $detailedHours['negativeHours'] = $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Desplazamiento')) {
            $detailedHours['displacement'] = $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Espera')) {
            $detailedHours['waiting'] = $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'PRIMA')) {
            $detailedHours['prima'] = $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Hora normal')) {
            $detailedHours['normalHours'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Hora nocturna')) {
            $detailedHours['extraHours'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Hora festiva')) {
            $detailedHours['holidayHours'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Comida') && !str_contains($workRegister->getDescription(), 'Comida internacional')) {
            $detailedHours['lunch'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Cena') && !str_contains($workRegister->getDescription(), 'Cena internacional')) {
            $detailedHours['dinner'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Comida internacional')) {
            $detailedHours['lunchInt'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Cena internacional')) {
            $detailedHours['dinnerInt'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Dieta') && !str_contains($workRegister->getDescription(), 'Dieta internacional')) {
            $detailedHours['diet'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Dieta internacional')) {
            $detailedHours['dietInt'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Pernoctación')) {
            $detailedHours['overNight'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Salida')) {
            $detailedHours['exitExtra'] = $workRegister->getUnits();
        }

        return $detailedHours;
    }
}
