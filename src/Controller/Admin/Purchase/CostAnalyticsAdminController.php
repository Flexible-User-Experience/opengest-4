<?php

namespace App\Controller\Admin\Purchase;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Setting\CostCenter;
use App\Entity\Vehicle\Vehicle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CostAnalyticsAdminController extends BaseAdminController
{
    public function imputableCostsAction(Request $request): Response
    {
        $year = $request->get('year');
        $saleDeliveryNoteId = $request->get('sale_delivery_note');
        $vehicleId = $request->get('vehicle');
        $operatorId = $request->get('operator');
        $costCenterId = $request->get('costCenter');
        $saleDeliveryNoteRepository = $this->em->getRepository(SaleDeliveryNote::class);
        $vehicles = $this->em->getRepository(Vehicle::class)->findEnabledSortedByName();
        $operators = $this->em->getRepository(Operator::class)->getFilteredByEnterpriseEnabledSortedByName($this->getUser()->getDefaultEnterprise());
        $costCenters = $this->em->getRepository(CostCenter::class)->getEnabledSortedByName();
        $saleDeliveryNotes = $saleDeliveryNoteRepository->getFilteredByEnterpriseSortedById($this->getUser()->getDefaultEnterprise());
        $oldestYear = $saleDeliveryNoteRepository->getOldestYearFilteredByEnterprise($this->getUser()->getDefaultEnterprise());
        $currentYear = date('Y');
        $oldestYear = $oldestYear['year'] ?? $currentYear;

        return $this->renderWithExtraParams(
            'admin/analytics/imputable_costs.html.twig',
            [
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
            ]
        );
    }
}
