<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleRequestHasDeliveryNote;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SaleRequestHasDeliveryNoteAdminController.
 */
class SaleRequestHasDeliveryNoteAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id = null): Response
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleRequestHasDeliveryNote $saleRequestHasDeliveryNote */
        $saleRequestHasDeliveryNote = $this->admin->getObject($id);
        if (!$saleRequestHasDeliveryNote) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleRequestHasDeliveryNote->getSaleRequest()->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($id);
    }
}
