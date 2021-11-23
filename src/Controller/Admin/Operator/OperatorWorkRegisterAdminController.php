<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Setting\TimeRange;
use App\Enum\OperatorWorkRegisterTimeEnum;
use App\Enum\OperatorWorkRegisterUnitEnum;
use DateTime;
use Sonata\AdminBundle\Exception\ModelManagerException;
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
     * @return RedirectResponse|Response
     *
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
            if ('unit' === $inputType) {
                $itemId = $request->query->get('custom_item');
                $item = OperatorWorkRegisterUnitEnum::getCodeFromId($itemId);
                $description = OperatorWorkRegisterUnitEnum::getReversedEnumArray()[$itemId];
                $price = $this->getPriceFromItem($operator, $item);
                $units = 1;
                $saleDeliveryNoteId = $request->query->get('custom_sale_delivery_note');
                if ('' != $saleDeliveryNoteId) {
                    /** @var SaleDeliveryNote $saleDeliveryNote */
                    $saleDeliveryNote = $this->admin->getModelManager()->find(SaleDeliveryNote::class, $saleDeliveryNoteId);
                } else {
                    $saleDeliveryNote = null;
                }
                $operatorWorkRegister = $this->createOperatorWorkRegister($operator, $date, $description, $units, $price, $saleDeliveryNote);
                $this->admin->getModelManager()->create($operatorWorkRegister);
                $this->addFlash('success', 'Parte de trabajo con id '.$operatorWorkRegister->getId().' creado');
            } elseif ('other' === $inputType) {
                $description = $request->query->get('custom_text_description');
                $price = $request->query->get('amount') * 1;
                $units = 1;
                $saleDeliveryNoteId = $request->query->get('custom_sale_delivery_note');
                if ('' != $saleDeliveryNoteId) {
                    /** @var SaleDeliveryNote $saleDeliveryNote */
                    $saleDeliveryNote = $this->admin->getModelManager()->find(SaleDeliveryNote::class, $saleDeliveryNoteId);
                } else {
                    $saleDeliveryNote = null;
                }
                $operatorWorkRegister = $this->createOperatorWorkRegister($operator, $date, $description, $units, $price, $saleDeliveryNote);
                $this->admin->getModelManager()->create($operatorWorkRegister);
                $this->addFlash('success', 'Parte de trabajo con id '.$operatorWorkRegister->getId().' creado');
            } elseif ('hour' === $inputType) {
                $customStart = $request->query->get('custom_start');
                $customFinish = $request->query->get('custom_finish');
                $start = DateTime::createFromFormat('!H:i:s', $customStart.':00');
                $finish = DateTime::createFromFormat('!H:i:s', $customFinish.':00');
                $splitTimeRanges = $this->splitRangeInDefinedTimeRanges($start, $finish);
                $operatorWorkRegisterIds = [];
                foreach ($splitTimeRanges as $splitTimeRange) {
                    $itemId = $request->query->get('custom_description');
                    if ('' !== $itemId) {
                        $description = OperatorWorkRegisterTimeEnum::getReversedEnumArray()[$itemId];
                    } else {
                        $description = '';
                    }
                    // Check if hour is negative (itemId ==3)
                    if ($itemId < 3) {
                        $type = $splitTimeRange['type'];
                        $price = 0;
                        if (0 === $type) {
                            $price = $this->getPriceFromItem($operator, 'NORMAL_HOUR');
                        } elseif (1 === $type) {
                            $price = $this->getPriceFromItem($operator, 'EXTRA_NORMAL_HOUR');
                        } elseif (2 === $type) {
                            $price = $this->getPriceFromItem($operator, 'EXTRA_EXTRA_HOUR');
                        }
                    } else {
                        $price = $this->getPriceFromItem($operator, 'NEGATIVE_HOUR');
                        $units = ($splitTimeRange['finish']->getTimestamp() - $splitTimeRange['start']->getTimestamp()) / 3600;
                        $units = $units * (-1);
                    }
                    $saleDeliveryNoteId = $request->query->get('custom_sale_delivery_note');
                    /** @var SaleDeliveryNote $saleDeliveryNote */
                    $saleDeliveryNote = $this->admin->getModelManager()->find(SaleDeliveryNote::class, $saleDeliveryNoteId);
                    $operatorWorkRegister = $this->createOperatorWorkRegister($operator, $date, $description, $units, $price, $saleDeliveryNote, $splitTimeRange['start'], $splitTimeRange['finish']);
                    $this->admin->getModelManager()->create($operatorWorkRegister);
                    $operatorWorkRegisterIds[] = $operatorWorkRegister->getId();
                }
                $this->addFlash('success', count($operatorWorkRegisterIds).' parte/s de trabajo con id '.implode(', ', $operatorWorkRegisterIds).' creado/s.');
            }
            $parameters = [
              'operator' => $operator->getId(),
              'date' => $date->format('d-m-Y'),
              'previousInputType' => $inputType,
            ];
        }

        return new RedirectResponse($this->generateUrl('admin_app_operator_operatorworkregisterheader_create', $parameters));
    }

    /**
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
        /** @var OperatorWorkRegisterHeader $operatorWorkRegisterHeader */
        $operatorWorkRegisterHeader = $this->admin->getModelManager()->findOneBy(OperatorWorkRegisterHeader::class, [
            'operator' => $operator,
            'date' => $date,
        ]);
        if (!$operatorWorkRegisterHeader) {
            $operatorWorkRegisters = [];
        } else {
            $operatorWorkRegisters = $operatorWorkRegisterHeader->getOperatorWorkRegisters();
        }

        $serializer = $this->container->get('serializer');
        $serializedOperatorWorkRegisters = $serializer->serialize($operatorWorkRegisters, 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedOperatorWorkRegisters);
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function customDeleteAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $operatorWorkRegisterId = $request->query->get('id');
        /** @var OperatorWorkRegister $operatorWorkRegister */
        $operatorWorkRegister = $this->admin->getModelManager()->find(OperatorWorkRegister::class, $operatorWorkRegisterId);
        if (!$operatorWorkRegister) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $operatorWorkRegisterId));
        }
        $parameters = [
            'operator' => $operatorWorkRegister->getOperatorWorkRegisterHeader()->getOperator()->getId(),
            'date' => $operatorWorkRegister->getOperatorWorkRegisterHeader()->getDate()->format('d-m-Y'),
            'previousInputType' => 'hour',
        ];
        $this->admin->getModelManager()->delete($operatorWorkRegister);
        $this->addFlash('success', 'Parte de trabajo con id '.$operatorWorkRegisterId.' eliminado');

        return new RedirectResponse($this->generateUrl('admin_app_operator_operatorworkregisterheader_create', $parameters));
    }

    private function getPriceFromItem(Operator $operator, $item)
    {
        $bounty = $operator->getEnterpriseGroupBounty();
        $method = new UnicodeString('GET_'.$item);

        if ($bounty) {
            return call_user_func([$bounty, $method->lower()->camel()->toString()]);
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
                    $newSplitTimeRange = [
                        'start' => $start,
                        'type' => $timeRange->getType(),
                    ];
                    if ($timeRange->getFinish() < $finish) {
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

    private function createOperatorWorkRegister(Operator $operator, DateTime $date, $description, $units, $price, ?SaleDeliveryNote $saleDeliveryNote = null, $start = null, $finish = null)
    {
        /** @var OperatorWorkRegisterHeader $operatorWorkRegisterHeader */
        $operatorWorkRegisterHeader = $this->admin->getModelManager()->findOneBy(OperatorWorkRegisterHeader::class, [
            'operator' => $operator,
            'date' => $date,
        ]);
        try {
            if (!$operatorWorkRegisterHeader) {
                $operatorWorkRegisterHeader = new OperatorWorkRegisterHeader();
                $operatorWorkRegisterHeader->setDate($date);
                $operatorWorkRegisterHeader->setOperator($operator);
                $this->admin->getModelManager()->create($operatorWorkRegisterHeader);
            }
        } catch (ModelManagerException $e) {
            $this->addFlash('warning', 'No se ha podido crear la cabecera del parte de trabajo. Error: '.$e->getMessage());
        }
        $operatorWorkRegister = new OperatorWorkRegister();
        $operatorWorkRegister->setOperatorWorkRegisterHeader($operatorWorkRegisterHeader);
        $operatorWorkRegister->setDescription($description);
        $operatorWorkRegister->setUnits($units);
        $operatorWorkRegister->setPriceUnit($price);
        $operatorWorkRegister->setAmount($units * $price);
        if ($saleDeliveryNote) {
            $operatorWorkRegister->setSaleDeliveryNote($saleDeliveryNote);
        }
        if ($start) {
            $operatorWorkRegister->setStart($start);
        }
        if ($finish) {
            $operatorWorkRegister->setFinish($finish);
        }

        return $operatorWorkRegister;
    }
}
