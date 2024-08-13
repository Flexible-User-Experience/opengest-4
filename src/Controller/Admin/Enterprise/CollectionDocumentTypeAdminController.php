<?php

namespace App\Controller\Admin\Enterprise;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\CollectionDocumentType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CollectionDocumentTypeAdminController.
 */
class CollectionDocumentTypeAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): RedirectResponse|Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var CollectionDocumentType $collectionDocumentType */
        $collectionDocumentType = $this->admin->getObject($id);
        if (!$collectionDocumentType) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }
}
