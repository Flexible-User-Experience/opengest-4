<?php

namespace App\Controller\Admin\Partner;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Partner\Partner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PartnerBuildingSiteAdminController.
 */
class PartnerBuildingSiteAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var Partner $partner */
        $order = $this->admin->getObject($id);
        if (!$order) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }
}
