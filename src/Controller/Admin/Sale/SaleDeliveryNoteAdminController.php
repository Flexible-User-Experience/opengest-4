<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Partner\Partner;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Setting\SaleInvoiceSeries;
use App\Form\Type\GenerateSaleInvoicesFormType;
use App\Manager\Pdf\SaleDeliveryNotePdfManager;
use App\Repository\Sale\SaleInvoiceRepository;
use App\Repository\Setting\SaleInvoiceSeriesRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\AdminBundle\Exception\ModelManagerThrowable;
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
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        /** @var SaleDeliveryNote $saleDeliveryNote */
        $saleDeliveryNote = $this->admin->getObject($id);
        if (!$saleDeliveryNote) {
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
    public function pdfAction(Request $request, SaleDeliveryNotePdfManager $sdnps)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var SaleDeliveryNote $saleDeliveryNote */
        $saleDeliveryNote = $this->admin->getObject($id);
        if (!$saleDeliveryNote) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return new Response($sdnps->outputSingle($saleDeliveryNote), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionDeliveryNotesByClient(ProxyQueryInterface $selectedModelQuery): Response
    {
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();
        $sdnforDates = $saleDeliveryNotes;

        //get from to dates
        $from = array_shift($sdnforDates)->getDateToString();

        if (!$sdnforDates) {
            $to = $from;
        } else {
            $to = array_pop($sdnforDates)->getDateToString();
        }

        return new Response($this->sdnpm->outputDeliveryNotesByClient($saleDeliveryNotes, $from, $to), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionDeliveryNotesList(ProxyQueryInterface $selectedModelQuery): Response
    {
        //TODO get delivery notes by date interval calling $deliveryNotePdfManager->outputDeliveryNotesList($deliveryNotes)
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();

        return new Response($this->sdnpm->outputDeliveryNotesList($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionGenerateSaleInvoiceFromDeliveryNotes(ProxyQueryInterface $selectedModelQuery, Request $request)
    {
        $this->admin->checkAccess('edit');
        $form = $this->createForm(GenerateSaleInvoicesFormType::class);
        $form->handleRequest($request);
        /** @var SaleDeliveryNote[] $operators */
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();
        $form->get('saleDeliveryNotes')->setData($saleDeliveryNotes);

        return $this->renderWithExtraParams(
            'admin/sale-delivery-note/invoiceGeneration.html.twig',
            [
                'generateInvoicesForm' => $form->createView(),
            ]
        );
    }

    /**
     * @return Response|RedirectResponse
     */
    public function generateInvoicesAction(Request $request)
    {
//        $this->admin->checkAccess('edit');
//        $selectedModels = $selectedModelQuery->execute()->getQuery()->getResult();
        $formData = $request->request->get('app_generate_sale_invoices');
        /** @var SaleDeliveryNote $operators */
        $selectedModels = $formData['saleDeliveryNotes'];
        $date = DateTime::createFromFormat('d/m/Y', $formData['date']);
        /** @var SaleInvoiceSeriesRepository $saleInvoiceSeriesRepository */
        $saleInvoiceSeriesRepository = $this->container->get('doctrine')->getRepository(SaleInvoiceSeries::class);
        $saleInvoiceSeries = $saleInvoiceSeriesRepository->find($formData['series']);
        $saleDeliveryNotes = [];
        $em = $this->getDoctrine()->getManager();
        foreach ($selectedModels as $saleDeliveryNote) {
            $saleDeliveryNotes[] = $em->getRepository(SaleDeliveryNote::class)->find($saleDeliveryNote);
        }
        $saleDeliveryNotesWithSaleInvoice = [];
        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            if ($saleDeliveryNote->getSaleInvoice()) {
                $saleDeliveryNotesWithSaleInvoice[] = $saleDeliveryNote->getId();
            }
        }
        if (count($saleDeliveryNotesWithSaleInvoice) > 0) {
            $this->addFlash('warning', 'El/los albarán/nes con id '.implode(', ', $saleDeliveryNotesWithSaleInvoice).' ya están facturados');

            return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_list'));
        } else {
            $return = $this->generateSaleInvoiceFromSaleDeliveryNotes($saleDeliveryNotes, $date, $saleInvoiceSeries);
        }

        return $return;
    }

    /**
     * @throws ModelManagerThrowable
     */
    public function batchActionGenerateStandardPrint(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleDeliveryNote[] $saleDeliveryNotes */
        $saleDeliveryNotes = $selectedModelQuery->execute();
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            $saleDeliveryNote->setPrinted(true);
            $this->admin->getModelManager()->update($saleDeliveryNote);
        }

        return new Response($this->sdnpm->outputCollection($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionGenerateDriverPrint(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleDeliveryNote[] $saleDeliveryNotes */
        $saleDeliveryNotes = $selectedModelQuery->execute();

        return new Response($this->sdnpm->outputCollectionDriverPrint($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionGenerateStandardMail(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleDeliveryNote[] $saleDeliveryNotes */
        $saleDeliveryNotes = $selectedModelQuery->execute();

        return new Response($this->sdnpm->outputCollectionStandardMail($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionGenerateDriverMail(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleDeliveryNote[] $saleDeliveryNotes */
        $saleDeliveryNotes = $selectedModelQuery->execute();

        return new Response($this->sdnpm->outputCollectionDriverMail($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    private function generateSaleInvoiceFromSaleDeliveryNotes($deliveryNotes, $date, SaleInvoiceSeries $saleInvoiceSeries)
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
            /** @var SaleDeliveryNote[] $partnerDeliveryNotes */
            $partnerDeliveryNotes = array_filter($deliveryNotes, function (SaleDeliveryNote $deliveryNote) use ($partnerId) {
                return $deliveryNote->getPartner()->getId() === $partnerId;
            });
            // Check if all deliveryNotes from partner have same collectionDocument and terms
            $collectionDocument = $partnerDeliveryNotes[0]->getCollectionDocument();
            $collectionTerm1 = $partnerDeliveryNotes[0]->getCollectionTerm();
            $collectionTerm2 = $partnerDeliveryNotes[0]->getCollectionTerm2();
            $collectionTerm3 = $partnerDeliveryNotes[0]->getCollectionTerm3();
            foreach ($partnerDeliveryNotes as $partnerDeliveryNote) {
                if (
                    ($partnerDeliveryNote->getCollectionDocument() !== $collectionDocument)
                    ||
                    ($partnerDeliveryNote->getCollectionTerm() !== $collectionTerm1)
                    ||
                    ($partnerDeliveryNote->getCollectionTerm2() !== $collectionTerm2)
                    ||
                    ($partnerDeliveryNote->getCollectionTerm3() !== $collectionTerm3)
                ) {
                    $this->addFlash('warning', 'Los albaranes del cliente '.$partnerDeliveryNote->getPartner().' tienen que tener la misma forma y plazos de pago');

                    return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_list'));
                }
            }
            $saleInvoice = $this->generateSaleInvoiceFromPartnerSaleDeliveryNotes($partnerDeliveryNotes, $date, $saleInvoiceSeries);
            $saleInvoiceIds[] = $saleInvoice->getInvoiceNumber();
        }
        $this->addFlash('success', 'Factura/s con numero '.implode(', ', $saleInvoiceIds).' creada/s.');

        return new RedirectResponse($this->generateUrl('admin_app_sale_saleinvoice_list'));
    }

    private function generateSaleInvoiceFromPartnerSaleDeliveryNotes($deliveryNotes, $date, SaleInvoiceSeries $saleInvoiceSeries)
    {
        $saleInvoice = new SaleInvoice();
        $deliveryNotes = new ArrayCollection($deliveryNotes);
        /** @var Partner $partner */
        $partner = $deliveryNotes->first()->getPartner();
        $saleInvoice->setPartner($partner);
        $saleInvoice->setPartnerName($partner->getName());
        $saleInvoice->setPartnerCifNif($partner->getCifNif());
        $saleInvoice->setPartnerMainAddress($partner->getMainAddress());
        $saleInvoice->setPartnerMainCity($partner->getMainCity());
        if ($partner->getIban()) {
            $saleInvoice->setPartnerIban($partner->getIban());
        }
        if ($partner->getSwift()) {
            $saleInvoice->setPartnerSwift($partner->getSwift());
        }
        $saleInvoice->setDate($date);
        $saleInvoice->setType(1);
        $saleInvoice->setDeliveryNotes($deliveryNotes);
        $saleInvoice->setSeries($saleInvoiceSeries);
        $this->im->calculateInvoiceImportsFromDeliveryNotes($saleInvoice, $deliveryNotes);
        /** @var SaleInvoiceRepository $saleInvoiceRepository */
        $saleInvoiceRepository = $this->container->get('doctrine')->getRepository(SaleInvoice::class);
        $lastSaleInvoice = $saleInvoiceRepository->getLastInvoiceBySerieAndEnterprise($saleInvoiceSeries, $deliveryNotes->first()->getEnterprise());
        $saleInvoice->setInvoiceNumber($lastSaleInvoice->getInvoiceNumber() + 1);
        $saleInvoice->setDeliveryNotes($deliveryNotes);
        if ($saleInvoice->getPartner()->getCollectionDocumentType()) {
            $saleInvoice->setCollectionDocumentType($saleInvoice->getPartner()->getCollectionDocumentType());
        }
        if ($saleInvoice->getPartner()->getPartnerDeliveryAddresses()->first()) {
            $saleInvoice->setDeliveryAddress($saleInvoice->getPartner()->getPartnerDeliveryAddresses()->first());
        }
        $this->im->createDueDatesFromSaleInvoice($saleInvoice);
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
