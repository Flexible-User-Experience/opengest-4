<?php

namespace App\Controller\Admin\Enterprise;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\EnterpriseGroupBounty;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var EnterpriseGroupBounty $enterpriseGroupBounty */
        $enterpriseGroupBounty = $this->admin->getObject($id);
        if (!$enterpriseGroupBounty) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }
}
