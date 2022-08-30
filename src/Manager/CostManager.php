<?php

namespace App\Manager;

use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Setting\CostCenter;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleConsumption;
use Doctrine\Common\Collections\ArrayCollection;

class CostManager
{
    public function getPurchaseInvoiceLinesFromYear(int $year, ?SaleDeliveryNote $saleDeliveryNote = null, ?Vehicle $vehicle = null, ?Operator $operator = null, ?CostCenter $costCenter = null)
    {
        $purchaseInvoiceLines = [];
        if ($saleDeliveryNote) {
            $purchaseInvoiceLines = $saleDeliveryNote->getPurchaseInvoiceLines();
        } elseif ($vehicle) {
            $purchaseInvoiceLines = $vehicle->getPurchaseInvoiceLines();
        } elseif ($operator) {
            $purchaseInvoiceLines = $operator->getPurchaseInvoiceLines();
        } elseif ($costCenter) {
            $purchaseInvoiceLines = $costCenter->getPurchaseInvoiceLines();
        }

        return $purchaseInvoiceLines;
    }

    /**
     * @param PurchaseInvoiceLine[] $purchaseInvoiceLines
     */
    public function getTotalCostFromPurchaseInvoiceLines($purchaseInvoiceLines): float
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
    public function getTotalCostFromVehicleConsumptions($vehicleConsumptions): float
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
    public function getTotalCostFromOperatorWorkRegisters($operatorWorkRegisters): float
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
    public function getTotalWorkingHoursFromOperatorWorkRegisters($operatorWorkRegisters): float
    {
        $totalWorkingHours = 0;
        foreach ($operatorWorkRegisters as $operatorWorkRegister) {
            $totalWorkingHours += $operatorWorkRegister->getUnits();
        }

        return $totalWorkingHours;
    }

    public function getTotalWorkingHoursFromVehicleInYear(Vehicle $vehicle, $year): float
    {
        /** @var OperatorWorkRegister[] $operatorWorkRegisters */
        $operatorWorkRegisters = new ArrayCollection();
        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach ($vehicle->getSaleDeliveryNotes() as $saleDeliveryNote) {
            if ($saleDeliveryNote->getDate()->format('Y') === $year) {
                $operatorWorkRegisters = new ArrayCollection(array_merge(
                    $operatorWorkRegisters->toArray(),
                    $saleDeliveryNote->getOperatorWorkRegisters()->filter(function (OperatorWorkRegister $operatorWorkRegister) {
                        return null !== $operatorWorkRegister->getStart();
                    })->toArray()
                ));
            }
        }

        return $this->getTotalWorkingHoursFromOperatorWorkRegisters($operatorWorkRegisters);
    }

    public function getSaleDeliveryNotesMarginAnalysis($saleDeliveryNotes, int $year): array
    {
        $priceHourVehicles = $this->getVehiclePriceHoursFromDeliveryNotes($saleDeliveryNotes, $year);
        $saleDeliveryNotesMarginAnalysis = [];
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()] = [
                'income' => $saleDeliveryNote->getBaseAmount(),
                'workingHoursDirectCost' => $this->getWorkingHoursCostFromDeliveryNote($saleDeliveryNote),
                'purchaseInvoiceDirectCost' => $this->getPurchaseInvoiceCostFromDeliveryNote($saleDeliveryNote),
                'vehicleIndirectCost' => $this->getWorkingHoursFromDeliveryNote($saleDeliveryNote) * $priceHourVehicles[$saleDeliveryNote->getVehicle()->getId()]['priceHour'],
            ];
        }

        return $saleDeliveryNotesMarginAnalysis;
    }

    private function getWorkingHoursCostFromDeliveryNote(SaleDeliveryNote $saleDeliveryNote)
    {
        $operatorWorkRegisters = $saleDeliveryNote->getOperatorWorkRegisters()->filter(function (OperatorWorkRegister $operatorWorkRegister) {
            return null !== $operatorWorkRegister->getStart();
        });
        $amount = 0;
        foreach ($operatorWorkRegisters as $operatorWorkRegister) {
            $amount += $operatorWorkRegister->getAmount();
        }

        return $amount;
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
            $vehicleConsumptions = $vehicle->getVehicleConsumptions()->filter(function (VehicleConsumption $vehicleConsumption) use ($year) {
                return $vehicleConsumption->getSupplyDate()->format('Y') === $year;
            });
            $vehicleConsumptionCost = $this->getTotalCostFromVehicleConsumptions($vehicleConsumptions);
            $totalCost = $purchaseInvoiceCost + $vehicleConsumptionCost;
            $priceHour = $totalCost / $hours;
        }

        return $priceHour;
    }

    private function getVehiclePriceHoursFromDeliveryNotes($saleDeliveryNotes, $year)
    {
        $priceHourVehicles = [];
        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            if (array_key_exists($saleDeliveryNote->getVehicle()->getId(), $priceHourVehicles)) {
                $priceHourVehicles[$saleDeliveryNote->getVehicle()->getId()]['hours'] += $this->getWorkingHoursFromDeliveryNote($saleDeliveryNote);
            } else {
                $priceHourVehicles[$saleDeliveryNote->getVehicle()->getId()]['hours'] = $this->getWorkingHoursFromDeliveryNote($saleDeliveryNote);
                $priceHourVehicles[$saleDeliveryNote->getVehicle()->getId()]['vehicle'] = $saleDeliveryNote->getVehicle();
            }
        }
        foreach ($priceHourVehicles as $priceHourVehicle) {
            /** @var Vehicle $vehicle */
            $vehicle = $priceHourVehicle['vehicle'];
            $purchaseInvoiceCost = $this->getTotalCostFromPurchaseInvoiceLines($this->getPurchaseInvoiceLinesFromYear($year, null, $vehicle));
            $vehicleConsumptions = $vehicle->getVehicleConsumptions()->filter(function (VehicleConsumption $vehicleConsumption) use ($year) {
                return $vehicleConsumption->getSupplyDate()->format('Y') == $year;
            });
            $vehicleConsumptionCost = $this->getTotalCostFromVehicleConsumptions($vehicleConsumptions);
            $priceHourVehicles[$vehicle->getId()]['cost'] = $purchaseInvoiceCost + $vehicleConsumptionCost;
            if ($priceHourVehicles[$vehicle->getId()]['hours'] > 0) {
                $priceHourVehicles[$vehicle->getId()]['priceHour'] = $priceHourVehicles[$vehicle->getId()]['cost'] / $priceHourVehicles[$vehicle->getId()]['hours'];
            } else {
                $priceHourVehicles[$vehicle->getId()]['priceHour'] = 0;
            }
        }

        return $priceHourVehicles;
    }
}
