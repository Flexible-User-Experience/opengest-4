<?php

namespace App\Controller\Admin\Partner;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Partner\PartnerContact;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PartnerContactAdminController.
 */
class PartnerContactAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var PartnerContact $contact */
        $contact = $this->admin->getObject($id);
        if (!$contact) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }
}
