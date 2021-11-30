<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Setting\SaleInvoiceSeries;
use App\Manager\Pdf\SaleDeliveryNotePdfManager;
use App\Repository\Sale\SaleInvoiceRepository;
use App\Service\GuardService;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SaleDeliveryNoteAdminController.
 */
class SaleDeliveryNoteAdminController extends BaseAdminController
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
        /** @var SaleDeliveryNote $saleDeliveryNote */
        $saleDeliveryNote = $this->admin->getObject($id);
        if (!$saleDeliveryNote) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleDeliveryNote->getEnterprise())) {
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
    public function pdfAction(Request $request, SaleDeliveryNotePdfManager $sdnps)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleDeliveryNote $saleDeliveryNote */
        $saleDeliveryNote = $this->admin->getObject($id);
        if (!$saleDeliveryNote) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($saleDeliveryNote->getEnterprise())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        return new Response($sdnps->outputSingle($saleDeliveryNote), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * @return Response|RedirectResponse
     */
    public function batchActionGenerateSaleInvoiceFromDeliveryNotes(ProxyQueryInterface $selectedModelQuery)
    {
        $this->admin->checkAccess('edit');
        $selectedModels = $selectedModelQuery->execute();
        $saleDeliveryNotesWithSaleInvoice = [];
        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach ($selectedModels as $saleDeliveryNote) {
            if ($saleDeliveryNote->getSaleInvoice()) {
                $saleDeliveryNotesWithSaleInvoice[] = $saleDeliveryNote->getId();
            }
        }
        if (count($saleDeliveryNotesWithSaleInvoice) > 0) {
            $this->addFlash('warning', 'El/los albarán/nes con id '.implode(', ', $saleDeliveryNotesWithSaleInvoice).' ya están facturados');

            return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_list'));
        } else {
            $return = $this->generateSaleInvoiceFromSaleDeliveryNotes($selectedModels);
        }

        return $return;
    }

    /**
     * @param SaleDeliveryNotePdfManager $sdnps
     */
    public function batchActionGenerateStandardPrintTemplate(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleDeliveryNote[] $saleDeliveryNotes */
        $saleDeliveryNotes = $selectedModelQuery->execute();

        return new Response($this->sdnpm->outputCollection($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    private function generateSaleInvoiceFromSaleDeliveryNotes($deliveryNotes)
    {
        $partnerIds = [];
        /** @var SaleDeliveryNote $deliveryNote */
        foreach ($deliveryNotes as $deliveryNote) {
            if (null === $deliveryNote->getPartner()) {
                $this->addFlash('warning', 'El albarán con referencia: '.$deliveryNote->getDeliveryNoteReference().' no tiene cliente asignado.');

                return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_list'));
            }
            if (!in_array($deliveryNote->getPartner()->getId(), $partnerIds)) {
                $partnerIds[] = $deliveryNote->getPartner()->getId();
            }
        }
        $saleInvoiceIds = [];
        foreach ($partnerIds as $partnerId) {
            $partnerDeliveryNotes = array_filter($deliveryNotes, function (SaleDeliveryNote $deliveryNote) use ($partnerId) {
                return $deliveryNote->getPartner()->getId() === $partnerId;
            });
            $saleInvoice = $this->generateSaleInvoiceFromPartnerSaleDeliveryNotes($partnerDeliveryNotes);
            $saleInvoiceIds[] = $saleInvoice->getInvoiceNumber();
        }
        $this->addFlash('success', 'Factura/s con numero '.implode(', ', $saleInvoiceIds).' creada/s.');

        return new RedirectResponse($this->generateUrl('admin_app_sale_saleinvoice_list'));
    }

    private function generateSaleInvoiceFromPartnerSaleDeliveryNotes($deliveryNotes)
    {
        $saleInvoice = new SaleInvoice();
        $deliveryNotes = new ArrayCollection($deliveryNotes);
        $saleInvoice->setPartner($deliveryNotes->first()->getPartner());
        $date = new \DateTime();
        $saleInvoice->setDate($date);
        $saleInvoice->setType(1);
        $saleInvoice->setDeliveryNotes($deliveryNotes);
        /** @var SaleInvoiceSeries $saleInvoiceSeries */
        $saleInvoiceSeries = $this->admin->getModelManager()->findOneBy(SaleInvoiceSeries::class, ['id' => 1]);
        $saleInvoice->setSeries($saleInvoiceSeries);
        $this->im->calculateInvoiceImportsFromDeliveryNotes($saleInvoice, $deliveryNotes);
        /** @var SaleInvoiceRepository $saleInvoiceRepository */
        $saleInvoiceRepository = $this->container->get('doctrine')->getRepository(SaleInvoice::class);
        $lastSaleInvoice = $saleInvoiceRepository->getLastInvoiceBySerieAndEnterprise($saleInvoiceSeries, $deliveryNotes->first()->getEnterprise());
        $saleInvoice->setInvoiceNumber($lastSaleInvoice->getInvoiceNumber() + 1);
        $saleInvoice->setDeliveryNotes($deliveryNotes);
        try {
            $this->admin->getModelManager()->create($saleInvoice);
            /** @var SaleDeliveryNote $deliveryNote */
            foreach ($deliveryNotes as $deliveryNote) {
                $deliveryNote->setSaleInvoice($saleInvoice);
                $deliveryNote->setIsInvoiced(true);
                $this->admin->getModelManager()->update($deliveryNote);
            }

            return $saleInvoice;
        } catch (ModelManagerException $e) {
            $this->addFlash('error', 'Error al facturar los albaranes: '.$e->getMessage().$e->getFile());

            return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_list'));
        }
    }
}
