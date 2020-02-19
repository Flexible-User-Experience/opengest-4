<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\OperatorChecking;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OperatorCheckingAdminController.
 */
class OperatorCheckingAdminController extends BaseAdminController
{
    /**
     * @param null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var OperatorChecking $operatorChecking */
        $operatorChecking = $this->admin->getObject($id);
        if (!$operatorChecking) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        /** @var GuardService $guardService */
        $guardService = $this->get('app.guard_service');
        if (!$guardService->isOwnOperatorCheking($operatorChecking)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($id);
    }
}
