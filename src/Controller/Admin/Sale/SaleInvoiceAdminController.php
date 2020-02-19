<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleInvoice;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SaleInvoiceAdminController.
 */
class SaleInvoiceAdminController extends BaseAdminController
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

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleInvoice->getPartner()->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($id);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function pdfAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleInvoice->getPartner()->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        /* TODO @var SaleRequestPdfManager $rps /
        $rps = $this->container->get('app.sale_request_pdf_manager');
        return new Response($rps->outputSingle($saleRequest), 200, array('Content-type' => 'application/pdf'));*/

        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function pdfWithBackgroundAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleInvoice->getPartner()->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        // TODO
        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function countAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleInvoice->getPartner()->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        // TODO
        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }
}
