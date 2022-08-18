<?php

namespace App\Controller\Admin\Purchase;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CostAnalyticsAdminController extends CRUDController
{
    public function imputableCostsAction(Request $request): Response
    {
        return $this->renderWithExtraParams(
            'admin/analytics/imputable_costs.html.twig',
            []
        );
    }
}
