<?php

namespace App\Controller\Admin\Enterprise;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\EnterpriseGroupBounty;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnterpriseGroupBountyAdminController.
 */
class EnterpriseGroupBountyAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var EnterpriseGroupBounty $enterpriseGroupBounty */
        $enterpriseGroupBounty = $this->admin->getObject($id);
        if (!$enterpriseGroupBounty) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($enterpriseGroupBounty->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($id);
    }
}
