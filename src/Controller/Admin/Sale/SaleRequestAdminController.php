<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleRequestHasDeliveryNote;
use App\Manager\Pdf\SaleRequestPdfManager;
use App\Service\GuardService;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
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
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleRequest $saleRequest */
        $saleRequest = $this->admin->getObject($id);
        if (!$saleRequest) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleRequest->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($id);
    }

    /**
     * Generate PDF receipt action.
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function pdfAction(Request $request, SaleRequestPdfManager $rps)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleRequest $saleRequest */
        $saleRequest = $this->admin->getObject($id);
        if (!$saleRequest) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleRequest->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

//        /** @var SaleRequestPdfManager $rps */
//        $rps = $this->container->get('app.sale_request_pdf_manager');

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
    public function cloneAction(Request $request, EntityManagerInterface $em)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());
        /** @var SaleRequest $saleRequest */
        $saleRequest = $this->admin->getObject($id);
        if (!$saleRequest) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleRequest->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }
        $newSaleRequest = clone $saleRequest;
        $newSaleRequest->getServiceDate()->add(\DateInterval::createFromDateString('1 day'));
        $newSaleRequest->setStatus(0);
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
    public function generateDeliveryNoteFromSaleRequestAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());
        /** @var SaleRequest $saleRequest */
        $saleRequest = $this->admin->getObject($id);
        if (!$saleRequest) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleRequest->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }
        if ($saleRequest->getSaleRequestHasDeliveryNotes()->count() > 0) {
            $this->addFlash('warning', 'La petición con id '.$saleRequest->getId().' ya tiene un albarán asociado');

            return new RedirectResponse($request->headers->get('referer'));
        }
        $deliveryNote = $this->generateDeliveryNoteFromSaleRequest($saleRequest);

        return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_edit', [
            'id' => $deliveryNote->getId(),
        ]));
    }

    /**
     * @return Response|RedirectResponse
     */
    public function batchActionGeneratepdfs(ProxyQueryInterface $selectedModelQuery)
    {
        $this->admin->checkAccess('edit');
        $selectedModels = $selectedModelQuery->execute();

        /** @var SaleRequestPdfManager $rps */
        $rps = $this->container->get('app.sale_request_pdf_manager');

        return new Response($rps->outputCollection($selectedModels), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * @return Response|RedirectResponse
     */
    public function batchActionGeneratedeliverynotefromsalerequests(ProxyQueryInterface $selectedModelQuery)
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
            foreach ($selectedModels as $saleRequest) {
                $this->generateDeliveryNoteFromSaleRequest($saleRequest);
            }
        }

        return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_list'));
    }

    /**
     * @return SaleDeliveryNote
     *
     * @throws \Sonata\AdminBundle\Exception\ModelManagerException
     */
    private function generateDeliveryNoteFromSaleRequest(SaleRequest $saleRequest)
    {
        $deliveryNote = new SaleDeliveryNote();
        $deliveryNote->setDate($saleRequest->getServiceDate());
        $deliveryNote->setPartner($saleRequest->getInvoiceTo());
        $deliveryNote->setBuildingSite($saleRequest->getBuildingSite());
        $deliveryNote->setDeliveryNoteReference('P-'.$saleRequest->getId());
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
