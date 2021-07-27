<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorChecking;
use App\Entity\Operator\OperatorWorkRegister;
use App\Service\GuardService;
use DateTime;
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
     * @throws \Exception
     */
    public function createCustomWorkRegisterAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $inputType = $request->query->get('select_input_type');
        $operatorId = $request->query->get('custom_operator');
        /** @var Operator $operator */
        $operator = $this->admin->getModelManager()->find(Operator::class, $operatorId);
        if (!$operator) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $operatorId));
        }
        $date = new DateTime($request->query->get('custom_date'));
        if ($date) {
            $operatorWorkRegister = new OperatorWorkRegister();
            $operatorWorkRegister->setOperator($operator);
            $operatorWorkRegister->setDate($date);
            if ($inputType === 'unit') {
                $item = $request->query->get('custom_item');
            } elseif ($inputType === 'hour') {

            }
        }

        return new RedirectResponse($this->generateUrl('admin_app_operator_operatorworkregister_list'));
    }
}
