<?php

namespace App\Controller\Admin\Enterprise;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnterpriseTransferAccountAdminController.
 */
class EnterpriseTransferAccountAdminController extends BaseAdminController
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

        /** @var EnterpriseTransferAccount $enterpriseTransferAccount */
        $enterpriseTransferAccount = $this->admin->getObject($id);
        if (!$enterpriseTransferAccount) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($enterpriseTransferAccount->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($id);
    }
}
