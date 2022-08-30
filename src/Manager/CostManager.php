<?php

namespace App\Manager;

use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Payslip\Payslip;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Setting\CostCenter;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleConsumption;
use Doctrine\Common\Collections\ArrayCollection;

class CostManager
{
    private RepositoriesManager $repositoriesManager;

    public function __construct(RepositoriesManager $repositoriesManager)
    {
        $this->repositoriesManager = $repositoriesManager;
    }

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
        } else {
            $purchaseInvoiceLines = $this->repositoriesManager->getPurchaseInvoiceLineRepository()->getFilteredByYear($year);
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
        $priceHourOperators = $this->getOperatorPriceHoursFromDeliveryNotes($saleDeliveryNotes, $year);
        $saleDeliveryNotesMarginAnalysis = [];
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            $workingHours = $this->getWorkingHoursFromDeliveryNote($saleDeliveryNote);
            $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()] = [
                'income' => $saleDeliveryNote->getBaseAmount(),
                'workingHoursDirectCost' => $this->getWorkingHoursCostFromDeliveryNote($saleDeliveryNote),
                'purchaseInvoiceDirectCost' => $this->getPurchaseInvoiceCostFromDeliveryNote($saleDeliveryNote),
                'vehicleIndirectCost' => $workingHours * $priceHourVehicles[$saleDeliveryNote->getVehicle()->getId()]['priceHourIndirect'],
                'operatorPurchaseInvoiceIndirectCost' => $workingHours * $priceHourOperators[$saleDeliveryNote->getOperator()->getId()]['priceHourPurchaseInvoiceIndirect'],
                'operatorPayslipIndirectCost' => $workingHours * $priceHourOperators[$saleDeliveryNote->getOperator()->getId()]['priceHourPayslipIndirect'],
            ];
            $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['totalCost'] =
                $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['workingHoursDirectCost'] +
                $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['purchaseInvoiceDirectCost'] +
                $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['vehicleIndirectCost'] +
                $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['operatorPurchaseInvoiceIndirectCost'] +
                $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['operatorPayslipIndirectCost']
            ;
            $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['margin'] =
                $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['income'] -
                $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['totalCost']
            ;
            if ($saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['income'] > 0) {
                $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['marginPercentage'] =
                    $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['margin'] /
                    $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['income'] * 100
                ;
            } else {
                $saleDeliveryNotesMarginAnalysis[$saleDeliveryNote->getId()]['marginPercentage'] = 0;
            }
        }
        $saleDeliveryNotesMarginAnalysis['totalIncome'] = array_sum(array_column($saleDeliveryNotesMarginAnalysis, 'income'));
        $saleDeliveryNotesMarginAnalysis['totalWorkingHoursDirectCost'] = array_sum(array_column($saleDeliveryNotesMarginAnalysis, 'workingHoursDirectCost'));
        $saleDeliveryNotesMarginAnalysis['totalPurchaseInvoiceDirectCost'] = array_sum(array_column($saleDeliveryNotesMarginAnalysis, 'purchaseInvoiceDirectCost'));
        $saleDeliveryNotesMarginAnalysis['totalVehicleIndirectCost'] = array_sum(array_column($saleDeliveryNotesMarginAnalysis, 'vehicleIndirectCost'));
        $saleDeliveryNotesMarginAnalysis['totalOperatorPurchaseInvoiceIndirectCost'] = array_sum(array_column($saleDeliveryNotesMarginAnalysis, 'operatorPurchaseInvoiceIndirectCost'));
        $saleDeliveryNotesMarginAnalysis['totalOperatorPayslipIndirectCost'] = array_sum(array_column($saleDeliveryNotesMarginAnalysis, 'operatorPayslipIndirectCost'));
        $saleDeliveryNotesMarginAnalysis['totalCost'] =
            $saleDeliveryNotesMarginAnalysis['totalWorkingHoursDirectCost'] +
            $saleDeliveryNotesMarginAnalysis['totalPurchaseInvoiceDirectCost'] +
            $saleDeliveryNotesMarginAnalysis['totalVehicleIndirectCost'] +
            $saleDeliveryNotesMarginAnalysis['totalOperatorPurchaseInvoiceIndirectCost'] +
            $saleDeliveryNotesMarginAnalysis['totalOperatorPayslipIndirectCost']
        ;
        $saleDeliveryNotesMarginAnalysis['totalMargin'] = $saleDeliveryNotesMarginAnalysis['totalIncome'] - $saleDeliveryNotesMarginAnalysis['totalCost'];
        if ($saleDeliveryNotesMarginAnalysis['totalIncome'] > 0) {
            $saleDeliveryNotesMarginAnalysis['totalMarginPercentage'] = $saleDeliveryNotesMarginAnalysis['totalMargin'] / $saleDeliveryNotesMarginAnalysis['totalIncome'] * 100;
        } else {
            $saleDeliveryNotesMarginAnalysis['totalMarginPercentage'] = 0;
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
        $purchaseInvoiceLines = $this->getPurchaseInvoiceLinesFromYear($year);
        $vehicleConsumptions = $this->getVehicleConsumptionsFromYear($year);
        foreach ($priceHourVehicles as $priceHourVehicle) {
            /** @var Vehicle $vehicle */
            $vehicle = $priceHourVehicle['vehicle'];
            $purchaseInvoiceCost = $this->getTotalCostFromPurchaseInvoiceLines(array_filter($purchaseInvoiceLines, function (PurchaseInvoiceLine $purchaseInvoiceLine) use ($vehicle) {
                return ($purchaseInvoiceLine->getVehicle() ? $purchaseInvoiceLine->getVehicle()->getId() : null) === $vehicle->getId();
            }));
            $vehicleConsumptions = array_filter($vehicleConsumptions, function (VehicleConsumption $vehicleConsumption) use ($vehicle) {
                return $vehicleConsumption->getVehicle()->getId() === $vehicle->getId();
            });
            $vehicleConsumptionCost = $this->getTotalCostFromVehicleConsumptions($vehicleConsumptions);
            $priceHourVehicles[$vehicle->getId()]['cost'] = $purchaseInvoiceCost + $vehicleConsumptionCost;
            if ($priceHourVehicles[$vehicle->getId()]['hours'] > 0) {
                $priceHourVehicles[$vehicle->getId()]['priceHourIndirect'] = $priceHourVehicles[$vehicle->getId()]['cost'] / $priceHourVehicles[$vehicle->getId()]['hours'];
            } else {
                $priceHourVehicles[$vehicle->getId()]['priceHourIndirect'] = 0;
            }
        }

        return $priceHourVehicles;
    }

    private function getVehicleConsumptionsFromYear($year)
    {
        return $this->repositoriesManager->getVehicleConsumptionRepository()->getFilteredByYearAndVehicle($year);
    }

    private function getOperatorPriceHoursFromDeliveryNotes($saleDeliveryNotes, int $year)
    {
        $priceHourOperators = [];
        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            if (array_key_exists($saleDeliveryNote->getOperator()->getId(), $priceHourOperators)) {
                $priceHourOperators[$saleDeliveryNote->getOperator()->getId()]['hours'] += $this->getWorkingHoursFromDeliveryNote($saleDeliveryNote);
                $priceHourOperators[$saleDeliveryNote->getOperator()->getId()]['directCost'] += $this->getWorkingHoursCostFromDeliveryNote($saleDeliveryNote);
            } else {
                $priceHourOperators[$saleDeliveryNote->getOperator()->getId()]['hours'] = $this->getWorkingHoursFromDeliveryNote($saleDeliveryNote);
                $priceHourOperators[$saleDeliveryNote->getOperator()->getId()]['directCost'] = $this->getWorkingHoursCostFromDeliveryNote($saleDeliveryNote);
                $priceHourOperators[$saleDeliveryNote->getOperator()->getId()]['operator'] = $saleDeliveryNote->getOperator();
            }
        }
        $purchaseInvoiceLines = $this->getPurchaseInvoiceLinesFromYear($year);
        $payslips = $this->getPayslipsFromYear($year);
        foreach ($priceHourOperators as $priceHourOperator) {
            /** @var Operator $operator */
            $operator = $priceHourOperator['operator'];
            $purchaseInvoiceCost = $this->getTotalCostFromPurchaseInvoiceLines(array_filter($purchaseInvoiceLines, function (PurchaseInvoiceLine $purchaseInvoiceLine) use ($operator) {
                return ($purchaseInvoiceLine->getOperator() ? $purchaseInvoiceLine->getOperator()->getId() : null) === $operator->getId();
            }));
            $payslipsFromOperator = array_filter($payslips, function (Payslip $payslip) use ($operator) {
                return $payslip->getOperator()->getId() === $operator->getId();
            });
            $payslipTotalCost = $this->getTotalCostFromPayslips($payslipsFromOperator);
            $priceHourOperators[$operator->getId()]['indirectCost'] = $purchaseInvoiceCost + $payslipTotalCost - $priceHourOperators[$operator->getId()]['directCost'];
            if ($priceHourOperators[$operator->getId()]['hours'] > 0) {
                $priceHourOperators[$operator->getId()]['priceHourPayslipIndirect'] = ($payslipTotalCost - $priceHourOperators[$operator->getId()]['directCost']) / $priceHourOperators[$operator->getId()]['hours'];
                $priceHourOperators[$operator->getId()]['priceHourPurchaseInvoiceIndirect'] = $purchaseInvoiceCost / $priceHourOperators[$operator->getId()]['hours'];
                $priceHourOperators[$operator->getId()]['priceHourIndirect'] = $priceHourOperators[$operator->getId()]['indirectCost'] / $priceHourOperators[$operator->getId()]['hours'];
            } else {
                $priceHourOperators[$operator->getId()]['priceHourPayslipIndirect'] = 0;
                $priceHourOperators[$operator->getId()]['priceHourPurchaseInvoiceIndirect'] = 0;
                $priceHourOperators[$operator->getId()]['priceHourIndirect'] = 0;
            }
        }

        return $priceHourOperators;
    }

    private function getPayslipsFromYear(int $year)
    {
        return $this->repositoriesManager->getPayslipRepository()->getPayslipsSortedByNameQB()
            ->leftJoin('p.payslipLines', 'payslipLines')
            ->addSelect('payslipLines')
            ->where('YEAR(p.fromDate) = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult();
    }

    private function getTotalCostFromPayslips(array $payslipsFromOperator): float|int
    {
        $costFromPayslip = 0;
        /** @var Payslip $payslip */
        foreach ($payslipsFromOperator as $payslip) {
            $costFromPayslip += $payslip->getTotalAmount() + $payslip->getExpenses() + $payslip->getOtherCosts() + $payslip->getSocialSecurityCost();
        }

        return $costFromPayslip;
    }
}
