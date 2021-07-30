<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorChecking;
use App\Entity\Operator\OperatorWorkRegister;
use App\Enum\OperatorWorkRegisterTimeEnum;
use App\Enum\OperatorWorkRegisterUnitEnum;
use App\Service\GuardService;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\UnicodeString;

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
        $date = DateTime::createFromFormat('d-m-Y', $request->query->get('custom_date'));
        if ($date) {
            $operatorWorkRegister = new OperatorWorkRegister();
            $operatorWorkRegister->setOperator($operator);
            $operatorWorkRegister->setDate($date);
            if ($inputType === 'unit') {
                $itemId = $request->query->get('custom_item');
                $item = OperatorWorkRegisterUnitEnum::getCodeFromId($itemId);
                $description = OperatorWorkRegisterUnitEnum::getReversedEnumArray()[$itemId];
                $price = $this->getPriceFromItem($operator, $item);
                $units = 1;
            } elseif ($inputType === 'hour') {
                //Implement when is time type
            }
            $operatorWorkRegister->setUnits($units);
            $operatorWorkRegister->setPriceUnit($price);
            $operatorWorkRegister->setAmount($units*$price);
            $operatorWorkRegister->setDescription($description);
            $this->admin->getModelManager()->create($operatorWorkRegister);
        }

        return new RedirectResponse($this->generateUrl('admin_app_operator_operatorworkregister_create'));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getJsonOperatorWorkRegistersByDataAndOperatorIdAction(Request $request): JsonResponse
    {
        $operatorId = $request->get('operatorId');
        $date = DateTime::createFromFormat('d-m-Y', $request->get('date'));
        /** @var Operator $operator */
        $operator = $this->admin->getModelManager()->find(Operator::class, $operatorId);
        if (!$operator) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $operatorId));
        }
        $operatorWorkRegisters = $this->admin->getModelManager()->findBy(OperatorWorkRegister::class, array(
            'operator' => $operator,
            'date' => $date
        ));

        $serializer = $this->container->get('serializer');
        $serializedOperatorWorkRegisters = $serializer->serialize($operatorWorkRegisters, 'json', array('groups' => array('api')));

        return new JsonResponse($serializedOperatorWorkRegisters);
    }

    private function getPriceFromItem(Operator $operator, $item)
    {
        $bounty = $operator->getEnterpriseGroupBounty();
        $method = new UnicodeString('GET_'.$item);

        return call_user_func(array($bounty, $method->lower()->camel()->toString()));
    }
}
