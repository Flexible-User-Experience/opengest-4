<?php

namespace App\Manager;

use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorAbsence;
use App\Entity\Sale\SaleRequest;
use App\Entity\Vehicle\Vehicle;

class AvailabilityManager
{
    public static function isOperatorAvailable(Operator $operator, \DateTimeInterface $date): bool
    {
        return
            0 == $operator->getSaleRequests()->filter(function (SaleRequest $saleRequest) use ($date) {
                return $saleRequest->getServiceDate()->format('Y-m-d') === $date->format('Y-m-d');
            })->count()
            &&
            0 == $operator->getOperatorAbsences()->filter(function (OperatorAbsence $operatorAbsence) use ($date) {
                return $operatorAbsence->getBegin()->format('Y-m-d') <= $date->format('Y-m-d')
                    &&
                    $operatorAbsence->getEnd()->format('Y-m-d') >= $date->format('Y-m-d');
            })->count()
        ;
    }

    public static function isVehicleAvailable(Vehicle $vehicle, \DateTimeInterface $date): bool
    {
        return
            0 == $vehicle->getSaleRequests()->filter(function (SaleRequest $saleRequest) use ($date) {
                return $saleRequest->getServiceDate()->format('Y-m-d') === $date->format('Y-m-d');
            })->count()
        ;
    }
}
