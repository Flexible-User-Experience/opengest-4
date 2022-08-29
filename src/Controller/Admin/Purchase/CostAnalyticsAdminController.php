<?php

namespace App\Controller\Admin\Purchase;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Setting\CostCenter;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleConsumption;
use App\Repository\Sale\SaleDeliveryNoteRepository;
use App\Service\Format\NumberFormatService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CostAnalyticsAdminController extends BaseAdminController
{
    public function imputableCostsAction(Request $request): Response
    {
        $this->admin->checkAccess('edit');
        $saleDeliveryNoteRepository = $this->em->getRepository(SaleDeliveryNote::class);
        $year = $request->get('year') ?? date('Y');
        $saleDeliveryNoteId = $request->get('sale_delivery_note');
        $vehicleId = $request->get('vehicle');
        $operatorId = $request->get('operator');
        $costCenterId = $request->get('costCenter');
        $download = $request->get('download');
        $operatorWorkRegisters = [];
        $vehicleConsumptions = [];
        $totalWorkingHours = 0;
        $totalWorkingHoursCost = 0;
        $totalVehicleConsumptionCost = 0;
        if ($saleDeliveryNoteId) {
            $saleDeliveryNote = $saleDeliveryNoteRepository->find($saleDeliveryNoteId);
            $purchaseInvoiceLines = $this->costManager->getPurchaseInvoiceLinesFromYear($year, $saleDeliveryNote);
        } elseif ($vehicleId) {
            $vehicle = $this->em->getRepository(Vehicle::class)->find($vehicleId);
            $purchaseInvoiceLines = $this->costManager->getPurchaseInvoiceLinesFromYear($year, null, $vehicle);
            $vehicleConsumptions = $this->em->getRepository(VehicleConsumption::class)->getFilteredByYearAndVehicle($year, $vehicle);
            $totalWorkingHours = $this->costManager->getTotalWorkingHoursFromVehicleInYear($vehicle, $year);
        } elseif ($operatorId) {
            $operator = $this->em->getRepository(Operator::class)->find($operatorId);
            $purchaseInvoiceLines = $this->costManager->getPurchaseInvoiceLinesFromYear($year, null, null, $operator);
            $operatorWorkRegisters = $this->em->getRepository(OperatorWorkRegister::class)->getFilteredByYearAndOperator($year, $operator);
        } elseif ($costCenterId) {
            $costCenter = $this->em->getRepository(CostCenter::class)->find($costCenterId);
            $purchaseInvoiceLines = $this->costManager->getPurchaseInvoiceLinesFromYear($year, null, null, null, $costCenter);
        } else {
            $purchaseInvoiceLines = $this->em->getRepository(PurchaseInvoiceLine::class)->getFilteredByYear($year);
            $operatorWorkRegisters = $this->em->getRepository(OperatorWorkRegister::class)->getFilteredByYearAndOperator($year);
            $vehicleConsumptions = $this->em->getRepository(VehicleConsumption::class)->getFilteredByYearAndVehicle($year);
        }
        $totalInvoiceCost = $this->costManager->getTotalCostFromPurchaseInvoiceLines($purchaseInvoiceLines);
        if ($vehicleConsumptions) {
            $totalVehicleConsumptionCost = $this->costManager->getTotalCostFromVehicleConsumptions($vehicleConsumptions);
        }
        if ($operatorWorkRegisters) {
            $totalWorkingHours = $this->costManager->getTotalWorkingHoursFromOperatorWorkRegisters($operatorWorkRegisters);
            $totalWorkingHoursCost = $this->costManager->getTotalCostFromOperatorWorkRegisters($operatorWorkRegisters);
        }
        $totalCost = $totalInvoiceCost + $totalVehicleConsumptionCost + $totalWorkingHoursCost;
        $saleDeliveryNoteRepository = $this->em->getRepository(SaleDeliveryNote::class);
        $vehicles = $this->em->getRepository(Vehicle::class)->findEnabledSortedByName();
        $operators = $this->em->getRepository(Operator::class)->getFilteredByEnterpriseEnabledSortedByName($this->getUser()->getDefaultEnterprise());
        $costCenters = $this->em->getRepository(CostCenter::class)->getEnabledSortedByName();
        $saleDeliveryNotes = $saleDeliveryNoteRepository->getFilteredByEnterpriseSortedById($this->getUser()->getDefaultEnterprise());
        $oldestYear = $saleDeliveryNoteRepository->getOldestYearFilteredByEnterprise($this->getUser()->getDefaultEnterprise());
        $currentYear = date('Y');
        $oldestYear = $oldestYear['year'] ?? $currentYear;
        $parameters = [
            'saleDeliveryNotes' => $saleDeliveryNotes,
            'years' => range($currentYear, $oldestYear),
            'vehicles' => $vehicles,
            'operators' => $operators,
            'costCenters' => $costCenters,
            'selectedYear' => $year,
            'selectedSaleDeliveryNoteId' => $saleDeliveryNoteId,
            'selectedVehicleId' => $vehicleId,
            'selectedOperatorId' => $operatorId,
            'selectedCostCenterId' => $costCenterId,
            'totalWorkingHours' => NumberFormatService::formatNumber($totalWorkingHours),
            'totalWorkingHoursCost' => NumberFormatService::formatNumber($totalWorkingHoursCost),
            'totalInvoiceCost' => NumberFormatService::formatNumber($totalInvoiceCost),
            'totalVehicleConsumptionCost' => NumberFormatService::formatNumber($totalVehicleConsumptionCost),
            'totalCost' => NumberFormatService::formatNumber($totalCost),
            'purchaseInvoiceLines' => $purchaseInvoiceLines,
            'operatorWorkRegisters' => $operatorWorkRegisters,
            'vehicleConsumptions' => $vehicleConsumptions,
        ]
        ;

        if ($download) {
            $headers = [
                'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="costesImputables.xlsx"',
            ];

            return new Response($this->imputableCostXlsManager->outputXls($parameters), 200, $headers);
        }

        return $this->renderWithExtraParams(
            'admin/analytics/imputable_costs.html.twig',
            $parameters
        );
    }

    public function marginAnalysisAction(Request $request): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleDeliveryNoteRepository $saleDeliveryNoteRepository */
        $saleDeliveryNoteRepository = $this->em->getRepository(SaleDeliveryNote::class);
        $year = $request->get('year') ?? date('Y');
        $saleDeliveryNotes = $saleDeliveryNoteRepository->getFilteredByEnterpriseSortedByNameQB($this->getUser()->getDefaultEnterprise())
            ->andWhere('YEAR(s.date) = :year')
            ->setParameter('year', $year)
            ->orderBy('s.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        $saleDeliveryNotesMarginAnalysis = $this->costManager->getSaleDeliveryNotesMarginAnalysis($saleDeliveryNotes);

        return $this->renderWithExtraParams(
            'admin/analytics/margin_analysis.html.twig',
            [
                'saleDeliveryNotes' => $saleDeliveryNotes,
                'saleDeliveryNotesMarginAnalysis' => $saleDeliveryNotesMarginAnalysis,
                'years' => range(date('Y'), date('Y') - 10),
                'selectedYear' => $year,
            ]
        );
    }
}
