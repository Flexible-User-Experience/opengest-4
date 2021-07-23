<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\OperatorChecking;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OperatorWorkRegisterAdminController.
 */
class OperatorWorkRegisterAdminController extends BaseAdminController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createCustomWorkRegisterAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        dd($request->query->get('custom_date'));

        return new RedirectResponse($this->generateUrl('admin_app_operator_operatorworkregister_list'));
    }
}
