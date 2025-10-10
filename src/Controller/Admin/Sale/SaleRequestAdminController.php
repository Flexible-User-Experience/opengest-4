<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorAbsence;
use App\Entity\Partner\Partner;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleRequestHasDeliveryNote;
use App\Entity\Vehicle\Vehicle;
use App\Enum\OperatorTypeEnum;
use App\Manager\AvailabilityManager;
use App\Manager\Pdf\SaleRequestPdfManager;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SaleRequestAdminController.
 */
class SaleRequestAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): RedirectResponse|Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleRequest $saleRequest */
        $saleRequest = $this->admin->getObject($id);
        if (!$saleRequest) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    /**
     * Generate PDF receipt action.
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function pdfAction(Request $request, SaleRequestPdfManager $rps): Response
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleRequest $saleRequest */
        $saleRequest = $this->admin->getObject($id);
        if (!$saleRequest) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return new Response($rps->outputSingle($saleRequest), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * Clone sale request and go te edit view.
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function cloneAction(Request $request, EntityManagerInterface $em): Response
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());
        /** @var SaleRequest $saleRequest */
        $saleRequest = $this->admin->getObject($id);
        if (!$saleRequest) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        $newSaleRequest = clone $saleRequest;
        $newSaleRequest->getServiceDate()->add(\DateInterval::createFromDateString('1 day'));
        $newSaleRequest->setStatus(0);
        $newSaleRequest->setSaleRequestHasDeliveryNotes([]);
        $em->clear(SaleRequest::class);
        $em->persist($newSaleRequest);
        $em->flush();

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * Generate delivery note from sale request and go to edit view.
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function generateDeliveryNoteFromSaleRequestAction(Request $request): Response
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());
        /** @var SaleRequest $saleRequest */
        $saleRequest = $this->admin->getObject($id);
        if (!$saleRequest) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        if ($saleRequest->getSaleRequestHasDeliveryNotes()->count() > 0) {
            $this->addFlash('warning', 'La petición con id '.$saleRequest->getId().' ya tiene un albarán asociado');

            return new RedirectResponse($request->headers->get('referer'));
        }
        if (!$saleRequest->getOperator() || !$saleRequest->getVehicle()) {
            $this->addFlash('warning', 'La petición con id '.$saleRequest->getId().' tiene que tener vehiculo y operario asignado para generar el albarán.');

            return new RedirectResponse($request->headers->get('referer'));
        }
        $deliveryNote = $this->generateDeliveryNoteFromSaleRequest($saleRequest);

        return new RedirectResponse($request->headers->get('referer'));
    }

    public function calendarAction(Request $request): Response
    {
        $date = new DateTimeImmutable();
        $date = $date->sub(new DateInterval('P2M'));
        $saleRequests = $this->em->getRepository(SaleRequest::class)
            ->getFilteredByEnterpriseEnabledSortedByRequestDateQB($this->getUser()->getDefaultEnterprise())
            ->andWhere('DATE(s.serviceDate) > DATE(:moment)')
            ->setParameter('moment', $date)
            ->getQuery()
            ->getResult()
        ;
        $operatorAbsences = $this->em->getRepository(OperatorAbsence::class)->getFilteredByEnterpriseSortedByStartDateQB($this->getUser()->getDefaultEnterprise())
            ->andWhere('DATE(oa.begin) > DATE(:moment)')
            ->setParameter('moment', $date)
            ->getQuery()
            ->getResult()
        ;
        $operators = $this->em->getRepository(Operator::class)->getFilteredByEnterpriseEnabledSortedByName($this->getUser()->getDefaultEnterprise());
        $vehicles = $this->em->getRepository(Vehicle::class)->getFilteredByEnterpriseEnabledSortedByName($this->getUser()->getDefaultEnterprise());
        $partners = $this->em->getRepository(Partner::class)->getFilteredByEnterpriseEnabledSortedByName($this->getUser()->getDefaultEnterprise());
        $availabilities = [];
        $dateForAvailabilities = new DateTimeImmutable();
        $endDate = $dateForAvailabilities->add(new DateInterval('P15D'));
        while ($dateForAvailabilities->getTimestamp() <= $endDate->getTimestamp()) {
            /** @var Vehicle $vehicle */
            foreach ($vehicles as $vehicle) {
                if (AvailabilityManager::isVehicleAvailable($vehicle, $dateForAvailabilities)) {
                    $availabilities[] = [
                        'date' => $dateForAvailabilities->format('Y-m-d'),
                        'type' => 'Vehículo',
                        'id' => $vehicle->getId(),
                        'name' => $vehicle,
                    ];
                }
            }
            /** @var Operator $operator */
            foreach ($operators as $operator) {
                if (OperatorTypeEnum::OPERATOR === $operator->getType()) {
                    if (AvailabilityManager::isOperatorAvailable($operator, $dateForAvailabilities)) {
                        $availabilities[] = [
                            'date' => $dateForAvailabilities->format('Y-m-d'),
                            'type' => 'Operario',
                            'id' => $operator->getId(),
                            'name' => $operator,
                        ];
                    }
                }
            }
            $dateForAvailabilities = $dateForAvailabilities->add(new DateInterval('P1D'));
        }

        return $this->renderWithExtraParams(
            'admin/sale-request/calendar.html.twig',
            [
                'saleRequests' => $saleRequests,
                'operatorAbsences' => $operatorAbsences,
                'operators' => $operators,
                'vehicles' => $vehicles,
                'partners' => $partners,
                'availabilities' => $availabilities,
        ]
        );
    }

    public function batchActionGeneratepdfs(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        $selectedModels = $selectedModelQuery->execute()->getQuery()->getResult();

        /** @var SaleRequestPdfManager $rps */
        $rps = $this->container->get('app.sale_request_pdf_manager');

        return new Response($rps->outputCollection($selectedModels), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * @return Response|RedirectResponse
     */
    public function batchActionGeneratedeliverynotefromsalerequests(ProxyQueryInterface $selectedModelQuery): Response|RedirectResponse
    {
        $this->admin->checkAccess('edit');
        $selectedModels = $selectedModelQuery->execute();
        $saleRequestWithDeliveryNotes = [];
        foreach ($selectedModels as $saleRequest) {
            if ($saleRequest->getSaleRequestHasDeliveryNotes()->count() > 0) {
                $saleRequestWithDeliveryNotes[] = $saleRequest->getId();
            }
        }
        if (count($saleRequestWithDeliveryNotes) > 0) {
            $this->addFlash('warning', 'La/s petición/es con id '.implode(', ', $saleRequestWithDeliveryNotes).' ya tiene/n albarán asociado');

            return new RedirectResponse($this->generateUrl('admin_app_sale_salerequest_list'));
        } else {
            /** @var SaleRequest[] $saleRequests */
            $saleRequests = $selectedModels->getQuery()->getResult();
            usort($saleRequests, function (SaleRequest $a, SaleRequest $b) {
                if ($a->getServiceDate()->getTimestamp() === $b->getServiceDate()->getTimestamp()) {
                    return $a->getId() > $b->getId();
                }

                return $a->getServiceDate()->getTimestamp() > $b->getServiceDate()->getTimestamp();
            });
            foreach ($saleRequests as $saleRequest) {
                if (!$saleRequest->getOperator() || !$saleRequest->getVehicle()) {
                    $this->addFlash('warning', 'La petición con id '.$saleRequest->getId().' tiene que tener vehiculo y operario asignado para generar el albarán.');
                } else {
                    $this->generateDeliveryNoteFromSaleRequest($saleRequest);
                }
            }
        }

        return new RedirectResponse($this->generateUrl('admin_app_sale_salerequest_list'));
    }

    /**
     * @return SaleDeliveryNote
     *
     * @throws ModelManagerException
     */
    private function generateDeliveryNoteFromSaleRequest(SaleRequest $saleRequest): SaleDeliveryNote
    {
        $deliveryNote = new SaleDeliveryNote();
        $availableIds = $this->deliveryNoteManager->getAvailableIdsByEnterprise($saleRequest->getPartner()->getEnterprise());
        if (count($availableIds) > 0) {
            $metadata = $this->em->getManagerForClass(SaleDeliveryNote::class)->getClassMetadata(SaleDeliveryNote::class);
            $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
            $deliveryNote->setId(array_values($availableIds)[0]);
        }
        $deliveryNote->setDate($saleRequest->getServiceDate());
        $deliveryNote->setPartner($saleRequest->getPartner());
        $deliveryNote->setBuildingSite($saleRequest->getBuildingSite());
//        $deliveryNote->setDeliveryNoteReference('P-'.$saleRequest->getId());
        $partner = $saleRequest->getPartner();
        if ($partner) {
            $deliveryNote->setCollectionTerm($partner->getCollectionTerm1());
            $deliveryNote->setCollectionDocument($partner->getCollectionDocumentType());
        }
        $deliveryNote->setEnterprise($saleRequest->getEnterprise());
        $deliveryNote->setActivityLine($saleRequest->getService()->getActivityLine());
        $deliveryNote->setSaleServiceTariff($saleRequest->getService());
        $deliveryNote->setServiceDescription($saleRequest->getServiceDescription());
        $deliveryNote->setPlace($saleRequest->getPlace());
        $deliveryNote->setObservations($saleRequest->getObservations());
        if ($saleRequest->getVehicle()) {
            $deliveryNote->setVehicle($saleRequest->getVehicle());
        }
        if ($saleRequest->getSecondaryVehicle()) {
            $deliveryNote->setSecondaryVehicle($saleRequest->getSecondaryVehicle());
        }
        if ($saleRequest->getOperator()) {
            $deliveryNote->setOperator($saleRequest->getOperator());
        }
        if ($saleRequest->getPartner()->getPartnerDeliveryAddresses()->first()) {
            $deliveryNote->setDeliveryAddress($saleRequest->getPartner()->getPartnerDeliveryAddresses()->first());
        }
        $deliveryNote->setCollectionDocument($partner->getCollectionDocumentType());
        $deliveryNote->setCollectionTerm($partner->getCollectionTerm1());
        $deliveryNote->setCollectionTerm2($partner->getCollectionTerm2());
        $deliveryNote->setCollectionTerm3($partner->getCollectionTerm3());
        $this->admin->getModelManager()->create($deliveryNote);
        $saleRequestHasDeliveryNote = new SaleRequestHasDeliveryNote();
        $saleRequestHasDeliveryNote->setSaleRequest($saleRequest);
        $saleRequestHasDeliveryNote->setSaleDeliveryNote($deliveryNote);
        $this->admin->getModelManager()->create($saleRequestHasDeliveryNote);
        $saleRequest->setStatus(1);
        $this->admin->getModelManager()->update($saleRequest);

        return $deliveryNote;
    }
}
