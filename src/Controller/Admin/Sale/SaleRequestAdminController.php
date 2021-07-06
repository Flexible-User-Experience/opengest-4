<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleRequest;
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
     * @param Request $request
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

        return new Response($rps->outputSingle($saleRequest), 200, array('Content-type' => 'application/pdf'));
    }

    /**
     * Clone sale request and go te edit view
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function cloneAction (Request $request, EntityManagerInterface $em) {
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
     * Generate delivery note from sale request and go to edit view
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function generateDeliveryNoteFromSaleRequestAction (Request $request, EntityManagerInterface $em) {
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
        $deliveryNote = new SaleDeliveryNote();
        $deliveryNote->setDate($saleRequest->getServiceDate());
        $deliveryNote->setPartner($saleRequest->getPartner());
        $deliveryNote->setBuildingSite($saleRequest->getBuildingSite());
        $deliveryNote->setDeliveryNoteNumber($saleRequest->getId());
        $deliveryNote->setEnterprise($saleRequest->getEnterprise());
        $em->persist($deliveryNote);
        $saleRequest->setStatus(1);
        $em->persist($saleRequest);
        $em->flush();

        return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_edit', [
            'id' => $deliveryNote->getId()
        ]));
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     *
     * @return Response|RedirectResponse
     */
    public function batchActionGeneratepdfs(ProxyQueryInterface $selectedModelQuery)
    {
        $this->admin->checkAccess('edit');
        $selectedModels = $selectedModelQuery->execute();

        /** @var SaleRequestPdfManager $rps */
        $rps = $this->container->get('app.sale_request_pdf_manager');

        return new Response($rps->outputCollection($selectedModels), 200, array('Content-type' => 'application/pdf'));
    }
}
