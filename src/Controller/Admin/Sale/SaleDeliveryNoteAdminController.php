<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Partner\PartnerOrder;
use App\Entity\Partner\PartnerType;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Setting\SaleInvoiceSeries;
use App\Form\Type\GenerateSaleInvoicesFormType;
use App\Manager\Pdf\SaleDeliveryNotePdfManager;
use App\Repository\Setting\SaleInvoiceSeriesRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\AdminBundle\Exception\ModelManagerThrowable;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
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
//        usort($saleDeliveryNotes, function(SaleDeliveryNote $a, SaleDeliveryNote $b){
//            return $a->getDateToString() > $b->getDateToString();
//        });
        $sdnforDates = $saleDeliveryNotes;
        $filterInfo = $this->admin->getFilterParameters();

        if (array_key_exists('date', $filterInfo)) {
            //get from to filter dates
            $from = $filterInfo['date']['value']['start'];
            $to = $filterInfo['date']['value']['end'];
        } else {
            $from = array_shift($sdnforDates)->getDateToString();
            if (!$sdnforDates) {
                $to = $from;
            } else {
                $to = array_pop($sdnforDates)->getDateToString();
            }
        }

        return new Response($this->sdnpm->outputDeliveryNotesByClient($saleDeliveryNotes, $from, $to), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionDeliveryNotesList(ProxyQueryInterface $selectedModelQuery): Response
    {
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();
        usort($saleDeliveryNotes, function (SaleDeliveryNote $a, SaleDeliveryNote $b) {
            return $a->getDateToString() > $b->getDateToString();
        });
        $sdnforDates = $saleDeliveryNotes;
        $filterInfo = $this->admin->getFilterParameters();

        if (array_key_exists('date', $filterInfo)) {
            //get from to filter dates
            $from = $filterInfo['date']['value']['start'];
            $to = $filterInfo['date']['value']['end'];
        } else {
            $from = array_shift($sdnforDates)->getDateToString();
            if (!$sdnforDates) {
                $to = $from;
            } else {
                $to = array_pop($sdnforDates)->getDateToString();
            }
        }

        return new Response($this->sdnpm->outputDeliveryNotesList($saleDeliveryNotes, $from, $to), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionGenerateSaleInvoiceFromDeliveryNotes(ProxyQueryInterface $selectedModelQuery, Request $request)
    {
        $this->admin->checkAccess('edit');
        $form = $this->createForm(GenerateSaleInvoicesFormType::class);
        $form->handleRequest($request);
        /** @var SaleDeliveryNote[] $saleDeliveryNotes */
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();
        $form->get('saleDeliveryNotes')->setData($saleDeliveryNotes);
        $partnerIds = array_unique(array_map(
            function (SaleDeliveryNote $saleDeliveryNote) {
                return $saleDeliveryNote->getPartner()->getId();
            },
            $saleDeliveryNotes
        ));
        $nonInvoiceableDeliveryNotes = array_filter($saleDeliveryNotes, function (SaleDeliveryNote $saleDeliveryNote) {
            return $saleDeliveryNote->isWontBeInvoiced();
        });
        if (count($nonInvoiceableDeliveryNotes)) {
            $nonInvoiceableDeliveryNotesIds = array_map(function (SaleDeliveryNote $saleDeliveryNote) {
                return $saleDeliveryNote->getId();
            }, $nonInvoiceableDeliveryNotes);
            $this->addFlash('warning', 'Los albaranes '.implode(',',$nonInvoiceableDeliveryNotesIds).' son no facturables');

            return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_to_invoice_custom_list'));
        }
        foreach ($partnerIds as $partnerId) {
            $orderCheck = false;
            $buildingSiteCheck = false;
            $deliveryAddressCheck = false;
            $first = true;
            foreach (array_filter($saleDeliveryNotes, function ($saleDeliveryNote) use ($partnerId) { return $saleDeliveryNote->getPartner()->getId() === $partnerId; })
                     as $saleDeliveryNote) {
                if ($first) {
                    $order = $saleDeliveryNote->getOrder();
                    $buildingSite = $saleDeliveryNote->getBuildingSite();
                    $deliveryAddress = $saleDeliveryNote->getDeliveryAddress();
                    $first = false;
                } else {
                    if ($order !== $saleDeliveryNote->getOrder()) {
                        $orderCheck = true;
                    }
                    if ($buildingSite !== $saleDeliveryNote->getBuildingSite()) {
                        $buildingSiteCheck = true;
                    }
                    if ($deliveryAddress !== $saleDeliveryNote->getDeliveryAddress()) {
                        $deliveryAddressCheck = true;
                    }
                }
            }
            if ($buildingSiteCheck || $orderCheck || $deliveryAddressCheck) {
                $this->addFlash('warning', 'Los albaranes seleccionados del cliente: '.$saleDeliveryNote->getPartner().' no tienen el mismo/a: '
                    .($orderCheck ? 'pedido' : '')
                    .($buildingSiteCheck ? ', obra' : '')
                    .($deliveryAddressCheck ? ', dirección de envio' : '')
                )
                ;
            }
        }

        $form->get('saleDeliveryNotes')->setData($saleDeliveryNotes);
        $enterprise = $this->em->getRepository(Enterprise::class)->find(1);
        $partnerType = $this->em->getRepository(PartnerType::class)->find(1);
        $partners = $this->em->getRepository(Partner::class)->getFilteredByEnterprisePartnerTypeEnabledSortedByName($enterprise, $partnerType);
        $orders = $this->em->getRepository(PartnerOrder::class)->getEnabledSortedByNumber();
        $buildingSites = $this->em->getRepository(PartnerBuildingSite::class)->getEnabledSortedByName();

        return $this->renderWithExtraParams(
            'admin/sale-delivery-note/invoiceGeneration.html.twig',
            [
                'generateInvoicesForm' => $form->createView(),
                'partners' => $partners,
                'orders' => $orders,
                'buildingSites' => $buildingSites,
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
        $em = $this->em;
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
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();
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
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            $saleDeliveryNote->setPrinted(true);
            $this->admin->getModelManager()->update($saleDeliveryNote);
        }

        return new Response($this->sdnpm->outputCollectionDriverPrint($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionGenerateStandardMail(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleDeliveryNote[] $saleDeliveryNotes */
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();

        return new Response($this->sdnpm->outputCollectionStandardMail($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionGenerateDriverMail(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleDeliveryNote[] $saleDeliveryNotes */
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();

        return new Response($this->sdnpm->outputCollectionDriverMail($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    public function getJsonDeliveryNotesByParametersAction(Request $request): JsonResponse
    {
        $partnerId = $request->get('partnerId');
        $fromDate = DateTime::createFromFormat('d/m/Y', $request->get('fromDate'));
        $toDate = DateTime::createFromFormat('d/m/Y', $request->get('toDate'));
        $deliveryNoteNumber = $request->get('deliveryNoteNumber');
        $buildingSiteId = $request->get('buildingSiteId');
        $orderId = $request->get('orderId');
        $deliveryNotes = $this->em->getRepository(SaleDeliveryNote::class)->getDeliveryNotesFilteredByParameters($partnerId, $fromDate, $toDate, $deliveryNoteNumber, $buildingSiteId, $orderId);
        $serializer = $this->container->get('serializer');
        $serializedDeliveryNotes = $serializer->serialize($deliveryNotes, 'json', ['groups' => ['api']]);
        $partners = array_unique(array_map(
            function (SaleDeliveryNote $saleDeliveryNote) {
                return $saleDeliveryNote->getPartner();
            },
            $deliveryNotes
        ));
        usort($partners, function(Partner $a, Partner $b) {
            return strcmp($a->getName(), $b->getName());
        });
        $serializedPartners = $serializer->serialize($partners, 'json', ['groups' => ['api']]);

        return new JsonResponse(['deliveryNotes' => $serializedDeliveryNotes, 'partners' => $serializedPartners]);
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
            $partnerDeliveryNotes = [];
            /** @var SaleDeliveryNote[] $partnerDeliveryNotes */
            $partnerDeliveryNotes = array_filter($deliveryNotes, function (SaleDeliveryNote $deliveryNote) use ($partnerId) {
                return $deliveryNote->getPartner()->getId() === $partnerId;
            });
            // Check if all deliveryNotes from partner have same collectionDocument and terms
            $first = true;
            foreach ($partnerDeliveryNotes as $partnerDeliveryNote) {
                if ($first) {
                    $collectionDocument = $partnerDeliveryNote->getCollectionDocument();
                    $collectionTerm1 = $partnerDeliveryNote->getCollectionTerm();
                    $collectionTerm2 = $partnerDeliveryNote->getCollectionTerm2();
                    $collectionTerm3 = $partnerDeliveryNote->getCollectionTerm3();
                    $first = false;
                }
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
        /** @var SaleDeliveryNote[] $deliveryNotes */
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
        $invoiceNumber = $this->im->getLastInvoiceNumberBySerieAndEnterprise($saleInvoiceSeries, $deliveryNotes->first()->getEnterprise());
        $saleInvoice->setInvoiceNumber($invoiceNumber);
        $saleInvoice->setDeliveryNotes($deliveryNotes);
        if ($saleInvoice->getPartner()->getCollectionDocumentType()) {
            $saleInvoice->setCollectionDocumentType($saleInvoice->getPartner()->getCollectionDocumentType());
        }
        if ($deliveryNotes->first()->getDeliveryAddress()) {
            $saleInvoice->setDeliveryAddress($deliveryNotes->first()->getDeliveryAddress());
        }
        $installmentCollectionDocumentType = null;
        if (3 === $saleInvoice->getSeries()->getId()) {
            $installmentCollectionDocumentType = $this->em->getRepository(CollectionDocumentType::class)->find(10);
            $saleInvoice->setCollectionDocumentType($installmentCollectionDocumentType);
        }
        $this->im->createDueDatesFromSaleInvoice($saleInvoice);
        try {
            $this->admin->getModelManager()->create($saleInvoice);
            foreach ($deliveryNotes as $deliveryNote) {
                $deliveryNote->setSaleInvoice($saleInvoice);
                $deliveryNote->setIsInvoiced(true);
                if ($installmentCollectionDocumentType) {
                    $deliveryNote->setCollectionDocument($installmentCollectionDocumentType);
                }
                $this->admin->getModelManager()->update($deliveryNote);
            }

            return $saleInvoice;
        } catch (ModelManagerException $e) {
            $this->addFlash('error', 'Error al facturar los albaranes: '.$e->getMessage().$e->getFile());

            return new RedirectResponse($this->generateUrl('admin_app_sale_saledeliverynote_list'));
        }
    }
}
