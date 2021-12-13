<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleDeliveryNoteLine;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SaleDeliveryNoteLineAdminController.
 */
class SaleDeliveryNoteLineAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        /** @var SaleDeliveryNoteLine $saleDeliveryNoteLine */
        $saleDeliveryNoteLine = $this->admin->getObject($id);
        if (!$saleDeliveryNoteLine) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleDeliveryNoteLine->getDeliveryNote()->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($request);
    }
}
