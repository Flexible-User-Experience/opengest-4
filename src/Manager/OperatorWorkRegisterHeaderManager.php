<?php

namespace App\Manager;

use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Operator\OperatorWorkRegisterHeader;

class OperatorWorkRegisterHeaderManager
{
    public function getTotalsFromWorkRegisterHeader(OperatorWorkRegisterHeader  $workRegisterHeader): array
    {
        $detailedHours = [
            'normalHours' => 0,
            'extraHours' => 0,
            'negativeHours' => 0,
            'lunch' => 0,
            'lunchInt' => 0,
            'dinner' => 0,
            'dinnerInt' => 0,
            'diet' => 0,
            'dietInt' => 0,
            'overNight' => 0,
            'exitExtra' => 0,
        ];
        foreach($workRegisterHeader->getOperatorWorkRegisters() as $workRegister)
        {
            $detailedHoursFromWorkRegister = $this->getDetailedHoursFromWorkRegister($workRegister);
            foreach($detailedHours as $key => $value)
            {
                $detailedHours[$key] += $detailedHoursFromWorkRegister[$key];
            }
        }

        return $detailedHours;
    }

    private function getDetailedHoursFromWorkRegister(OperatorWorkRegister $workRegister): array
    {
        $detailedHours = [
            'normalHours' => 0,
            'extraHours' => 0,
            'negativeHours' => 0,
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
        if (str_contains($workRegister->getDescription(), 'Hora normal')) {
            $detailedHours['normalHours'] = $workRegister->getUnits();
        } elseif (str_contains($workRegister->getDescription(), 'Hora extra')) {
            $detailedHours['extraHours'] = $workRegister->getUnits();
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