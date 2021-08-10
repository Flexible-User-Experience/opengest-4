<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorChecking;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Setting\TimeRange;
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
        $parameters = [];
        $date = DateTime::createFromFormat('d-m-Y', $request->query->get('custom_date'));
        if ($date) {
            if ($inputType === 'unit') {
                $operatorWorkRegister = new OperatorWorkRegister();
                $operatorWorkRegister->setOperator($operator);
                $operatorWorkRegister->setDate($date);
                $itemId = $request->query->get('custom_item');
                $item = OperatorWorkRegisterUnitEnum::getCodeFromId($itemId);
                $description = OperatorWorkRegisterUnitEnum::getReversedEnumArray()[$itemId];
                $price = $this->getPriceFromItem($operator, $item);
                $units = 1;
                $saleDeliveryNoteId = $request->query->get('custom_sale_delivery_note');
                if ($saleDeliveryNoteId != '') {
                    /** @var SaleDeliveryNote $saleDeliveryNote */
                    $saleDeliveryNote = $this->admin->getModelManager()->find(SaleDeliveryNote::class, $saleDeliveryNoteId);
                    $operatorWorkRegister->setSaleDeliveryNote($saleDeliveryNote);
                }
                $operatorWorkRegister->setUnits($units);
                $operatorWorkRegister->setPriceUnit($price);
                $operatorWorkRegister->setAmount($units*$price);
                $operatorWorkRegister->setDescription($description);
                $this->admin->getModelManager()->create($operatorWorkRegister);
            } elseif ($inputType === 'hour') {
                $customStart = $request->query->get('custom_start');
                $customFinish = $request->query->get('custom_finish');
                $start = DateTime::createFromFormat('!H:i:s', $customStart['hour'].':'.$customStart['minute'].':00');
                $finish = DateTime::createFromFormat('!H:i:s', $customFinish['hour'].':'.$customFinish['minute'].':00');
                $splitTimeRanges = $this->splitRangeInDefinedTimeRanges($start, $finish);
                foreach ($splitTimeRanges as $splitTimeRange) {
                    $operatorWorkRegister = new OperatorWorkRegister();
                    $operatorWorkRegister->setOperator($operator);
                    $operatorWorkRegister->setDate($date);
                    $itemId = $request->query->get('custom_description');
                    $description = OperatorWorkRegisterTimeEnum::getReversedEnumArray()[$itemId];
                    $operatorWorkRegister->setDescription($description);
                    $type = $splitTimeRange['type'];
                    $price = 0;
                    if ($type === 0) {
                        $price = $this->getPriceFromItem($operator, 'NORMAL_HOUR');
                    } elseif ($type === 1) {
                        $price = $this->getPriceFromItem($operator, 'EXTRA_NORMAL_HOUR');
                    } elseif ($type === 2) {
                        $price = $this->getPriceFromItem($operator, 'EXTRA_EXTRA_HOUR');
                    }
                    $operatorWorkRegister->setPriceUnit($price);
                    $units = ($splitTimeRange['finish']->getTimestamp() - $splitTimeRange['start']->getTimestamp())/3600;
                    $operatorWorkRegister->setUnits($units);
                    $operatorWorkRegister->setAmount($units*$price);
                    $operatorWorkRegister->setStart($splitTimeRange['start']);
                    $operatorWorkRegister->setFinish($splitTimeRange['finish']);
                    $saleDeliveryNoteId = $request->query->get('custom_sale_delivery_note');
                    if ($saleDeliveryNoteId != '') {
                        /** @var SaleDeliveryNote $saleDeliveryNote */
                        $saleDeliveryNote = $this->admin->getModelManager()->find(SaleDeliveryNote::class, $saleDeliveryNoteId);
                        $operatorWorkRegister->setSaleDeliveryNote($saleDeliveryNote);
                    }
                    $this->admin->getModelManager()->create($operatorWorkRegister);
                }
            }
            $parameters = array(
              'operator' => $operator->getId(),
              'date' => $date->format('d-m-Y'),
              'previousInputType' => $inputType
            );
        }

        return new RedirectResponse($this->generateUrl('admin_app_operator_operatorworkregister_create', $parameters));
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

        if ($bounty) {

            return call_user_func(array($bounty, $method->lower()->camel()->toString()));
        } else {

            return 0;
        }
    }

    private function splitRangeInDefinedTimeRanges($start, $finish): array
    {
        /** @var TimeRange[] $timeRanges */
        $timeRanges = $this->admin->getModelManager()->findBy(TimeRange::class);
        //Order time ranges by start time in case the retrieval is not properly sorted
        uasort($timeRanges, function ($tr1, $tr2) {
           if ($tr1->getStart() == $tr2->getStart()) {
               return 0;
           } else {
               return ($tr1->getStart() < $tr2->getStart()) ? -1 : 1;
           }
        });
        $splitTimeRanges = [];
        foreach ($timeRanges as $timeRange) {
//            dd('timeRenge', $timeRange->getFinish(), 'new start', $start);
            if ($timeRange->getFinish() <= $start) {
                continue;
            } else {
                if ($timeRange->getStart() <= $start) {
                    $newSplitTimeRange = array(
                        'start' => $start,
                        'type' => $timeRange->getType()
                    );
                    if($timeRange->getFinish() < $finish) {
                        $newSplitTimeRange['finish'] = $timeRange->getFinish();
                        $splitTimeRanges[] = $newSplitTimeRange;
                        $start = $timeRange->getFinish();
                    } else {
                        $newSplitTimeRange['finish'] = $finish;
                        $splitTimeRanges[] = $newSplitTimeRange;

                        break;
                    }
                }
            }
        }

        return $splitTimeRanges;
    }
}
