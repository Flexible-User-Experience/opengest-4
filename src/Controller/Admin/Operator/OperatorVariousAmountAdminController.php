<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\OperatorVariousAmount;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OperatorVariousAmountAdminController.
 */
class OperatorVariousAmountAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): RedirectResponse|Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var OperatorVariousAmount $operatorVariousAmount */
        $operatorVariousAmount = $this->admin->getObject($id);
        if (!$operatorVariousAmount) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }
}
