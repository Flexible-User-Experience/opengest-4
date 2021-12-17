<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleInvoice;
use App\Manager\Pdf\SaleInvoicePdfManager;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
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
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    public function invoiceListPdfAction(SaleInvoicePdfManager $saleInvoicePdfManager): Response
    {
        //TODO input client and dates and generate invoice list calling $saleInvoicePdfManager->outputSingle($saleInvoices)
        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }

    /**
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
        /* TODO @var SaleRequestPdfManager $rps /
        $rps = $this->container->get('app.sale_request_pdf_manager');
        return new Response($rps->outputSingle($saleRequest), 200, array('Content-type' => 'application/pdf'));*/

        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }

    /**
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
        // TODO
        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }

    /**
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
        // TODO
        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws ModelManagerException
     */
    public function setHasNotBeenCountedAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleInvoice $saleInvoice */
        $saleInvoice = $this->admin->getObject($id);
        if (!$saleInvoice) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        $saleInvoice->setHasBeenCounted(false);
        try {
            $this->admin->getModelManager()->update($saleInvoice);
            $this->addFlash('success', 'La factura se ha descontabilizado');
        } catch (\Exception $ex) {
            $this->addFlash('warning', 'No se pudo realizar la acción');
        }

        return $this->redirectToRoute('admin_app_sale_saleinvoice_edit', ['id' => $id]);
    }

    public function batchActionGeneratePdfs(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleInvoice[] $saleDeliveryNotes */
        $saleInvoices = $selectedModelQuery->execute();

        return new Response($this->sipm->outputCollection($saleInvoices), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * @param SaleInvoice $object
     *
     * @throws ModelManagerException
     */
    public function preDelete(Request $request, $object): ?Response
    {
        if ($object->isHasBeenCounted()) {
            $this->addFlash('warning', 'No se puede borrar una factura contablilizada');

            return $this->redirectToRoute('admin_app_sale_saleinvoice_list');
        } else {
            try {
                /** @var SaleDeliveryNote $deliveryNote */
                foreach ($object->getDeliveryNotes() as $deliveryNote) {
                    $deliveryNote->setSaleInvoice(null);
                    $deliveryNote->setIsInvoiced(false);
                    $this->admin->getModelManager()->update($deliveryNote);
                }
            } catch (ModelManagerException $exception) {
                $this->addFlash('error', 'Error al actualizar albaranes relacionados: '.$exception->getMessage());
                throw $exception;
            }
        }

        return null;
    }
}
