<?php

namespace App\Controller\Admin\Purchase;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleDeliveryNote;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CostAnalyticsAdminController extends BaseAdminController
{
    public function imputableCostsAction(Request $request): Response
    {
        $saleDeliveryNoteRepository = $this->em->getRepository(SaleDeliveryNote::class);
        $saleDeliveryNotes = $saleDeliveryNoteRepository->getFilteredByEnterpriseSortedById($this->getUser()->getDefaultEnterprise());
        $oldestYear = $saleDeliveryNoteRepository->getOldestYearFilteredByEnterprise($this->getUser()->getDefaultEnterprise());
        $currentYear = date('Y');
        $oldestYear = $oldestYear['year'] ?? $currentYear;

        return $this->renderWithExtraParams(
            'admin/analytics/imputable_costs.html.twig',
            [
                'saleDeliveryNotes' => $saleDeliveryNotes,
                'years' => range($currentYear, $oldestYear),
            ]
        );
    }
}
