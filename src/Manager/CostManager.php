<?php

namespace App\Manager;

use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleConsumption;

class CostManager
{
    private RepositoriesManager $repositoriesManager;

    public function __construct(RepositoriesManager $repositoriesManager)
    {
        $this->repositoriesManager = $repositoriesManager;
    }

    /**
     * @param PurchaseInvoiceLine[] $purchaseInvoiceLines
     */
    public function getTotalCostFromPurchaseInvoiceLines(array $purchaseInvoiceLines): float
    {
        $totalCost = 0;
        foreach ($purchaseInvoiceLines as $purchaseInvoiceLine) {
            $totalCost += $purchaseInvoiceLine->getPriceUnit() * $purchaseInvoiceLine->getUnits();
        }

        return $totalCost;
    }

    /**
     * @param VehicleConsumption[] $vehicleConsumptions
     */
    public function getTotalCostFromVehicleConsumptions(array $vehicleConsumptions): float
    {
        $totalCost = 0;
        foreach ($vehicleConsumptions as $vehicleConsumption) {
            $totalCost += $vehicleConsumption->getAmount();
        }

        return $totalCost;
    }

    /**
     * @param OperatorWorkRegister[] $operatorWorkRegisters
     */
    public function getTotalCostFromOperatorWorkRegisters(array $operatorWorkRegisters): float
    {
        $totalCost = 0;
        foreach ($operatorWorkRegisters as $operatorWorkRegister) {
            $totalCost += $operatorWorkRegister->getAmount();
        }

        return $totalCost;
    }

    /**
     * @param OperatorWorkRegister[] $operatorWorkRegisters
     */
    public function getTotalWorkingHoursFromOperatorWorkRegisters(array $operatorWorkRegisters): float
    {
        $totalWorkingHours = 0;
        foreach ($operatorWorkRegisters as $operatorWorkRegister) {
            $totalWorkingHours += $operatorWorkRegister->getUnits();
        }

        return $totalWorkingHours;
    }

    public function getTotalWorkingHoursFromVehicleInYear(Vehicle $vehicle, $year): float
    {
        $operatorWorkRegisters = $this->repositoriesManager->getOperatorWorkRegisterRepository()->getEnabledWithHoursSortedByIdQB()
            ->join('owr.saleDeliveryNote', 's')
            ->andWhere('year(s.date) = :year')
            ->andWhere('s.vehicle = :vehicle')
            ->setParameter('year', $year)
            ->setParameter('vehicle', $vehicle)
            ->getQuery()
            ->getResult()
        ;

        return $this->getTotalWorkingHoursFromOperatorWorkRegisters($operatorWorkRegisters);
    }
}
