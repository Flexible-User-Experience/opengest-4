<?php

namespace App\Manager;

use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleConsumption;

class CostManager
{
    private RepositoriesManager $repositoriesManager;

    public function __construct(RepositoriesManager $repositoriesManager)
    {
        $this->repositoriesManager = $repositoriesManager;
    }

    public function getPurchaseInvoiceLinesFromYear(int $year, $saleDeliveryNote = null, $vehicle = null, $operator = null, $activityLine = null)
    {
        $purchaseInvoiceLinesQueryBuilder = $this->repositoriesManager->getPurchaseInvoiceLineRepository()->getEnabledFilteredByYearQB($year);
        if ($saleDeliveryNote) {
            $purchaseInvoiceLinesQueryBuilder = $purchaseInvoiceLinesQueryBuilder
                ->andWhere('pil.saleDeliveryNote = :saleDeliveryNote')
                ->setParameter('saleDeliveryNote', $saleDeliveryNote)
            ;
        } elseif ($vehicle) {
            $purchaseInvoiceLinesQueryBuilder = $purchaseInvoiceLinesQueryBuilder
                ->andWhere('pil.vehicle = :vehicle')
                ->setParameter('vehicle', $vehicle)
            ;
        } elseif ($operator) {
            $purchaseInvoiceLinesQueryBuilder = $purchaseInvoiceLinesQueryBuilder
                ->andWhere('pil.operator = :operator')
                ->setParameter('operator', $operator)
            ;
        } elseif ($activityLine) {
            $purchaseInvoiceLinesQueryBuilder = $purchaseInvoiceLinesQueryBuilder
                ->andWhere('pil.activityLine = :activityLine')
                ->setParameter('activityLine', $activityLine)
            ;
        }

        return $purchaseInvoiceLinesQueryBuilder->getQuery()->getResult();
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

    /**
     * @param SaleDeliveryNote[] $saleDeliveryNotes
     */
    public function getSaleDeliveryNotesMarginAnalysis(array $saleDeliveryNotes, int $year): array
    {
        $saleDeliveryNotesMarginAnalysis = [];
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()] = [
                'income' => $saleDeliveryNote->getBaseAmount(),
                'workingHoursDirectCost' => $this->getWorkingHoursCostFromDeliveryNote($saleDeliveryNote),
                'purchaseInvoiceDirectCost' => $this->getPurchaseInvoiceCostFromDeliveryNote($saleDeliveryNote),
                'vehicleIndirectCost' => $this->getWorkingHoursFromDeliveryNote($saleDeliveryNote) * $this->getPriceHourFromVehicleInYear($saleDeliveryNote->getVehicle(), $year),
            ];
        }

        return $saleDeliveryNotesMarginAnalysis;
    }

    private function getWorkingHoursCostFromDeliveryNote(SaleDeliveryNote $saleDeliveryNote)
    {
        $operatorWorkRegisterHours = $this->repositoriesManager->getOperatorWorkRegisterRepository()->getEnabledWithHoursSortedByIdQB()
            ->andWhere('owr.saleDeliveryNote = :saleDeliveryNote')
            ->andWhere('owr.start is not null')
            ->setParameter('saleDeliveryNote', $saleDeliveryNote)
            ->select('SUM(owr.amount) as amount')
            ->getQuery()
            ->getResult()
        ;

        return $operatorWorkRegisterHours[0]['amount'];
    }

    private function getWorkingHoursFromDeliveryNote(SaleDeliveryNote $saleDeliveryNote)
    {
        $operatorWorkRegisters = $saleDeliveryNote->getOperatorWorkRegisters()->filter(function (OperatorWorkRegister $operatorWorkRegister) {
            return null !== $operatorWorkRegister->getStart();
        });
        $hours = 0;
        foreach ($operatorWorkRegisters as $operatorWorkRegister) {
            $hours += $operatorWorkRegister->getUnits();
        }

        return $hours;
    }

    private function getPurchaseInvoiceCostFromDeliveryNote(SaleDeliveryNote $saleDeliveryNote): float
    {
        $cost = 0;
        $purchaseInvoiceLines = $saleDeliveryNote->getPurchaseInvoiceLines();
        foreach ($purchaseInvoiceLines as $purchaseInvoiceLine) {
            $cost += $purchaseInvoiceLine->getBaseTotal();
        }

        return $cost;
    }

    private function getPriceHourFromVehicleInYear(Vehicle $vehicle, $year): float
    {
        $hours = $this->getTotalWorkingHoursFromVehicleInYear($vehicle, $year);
        $priceHour = 0;
        if ($hours > 0) {
            $purchaseInvoiceCost = $this->getTotalCostFromPurchaseInvoiceLines($this->getPurchaseInvoiceLinesFromYear($year, null, $vehicle));
            $vehicleConsumptionCost = $this->getTotalCostFromVehicleConsumptions($this->repositoriesManager->getVehicleConsumptionRepository()->getFilteredByYearAndVehicle($year, $vehicle));
            $totalCost = $purchaseInvoiceCost + $vehicleConsumptionCost;
            $priceHour = $totalCost / $hours;
        }

        return $priceHour;
    }
}
