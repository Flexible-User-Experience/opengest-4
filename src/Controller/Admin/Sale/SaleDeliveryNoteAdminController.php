<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerUnableDays;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Sale\SaleInvoiceDueDate;
use App\Entity\Setting\SaleInvoiceSeries;
use App\Manager\Pdf\SaleDeliveryNotePdfManager;
use App\Repository\Sale\SaleInvoiceRepository;
use DateTime;
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
        //TODO get delivery notes by client and date interval calling $deliveryNotePdfManager->outputDeliveryNotesByClient($deliveryNotes)
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();

        return new Response($this->sdnpm->outputDeliveryNotesByClient($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionDeliveryNotesList(ProxyQueryInterface $selectedModelQuery): Response
    {
        //TODO get delivery notes by date interval calling $deliveryNotePdfManager->outputDeliveryNotesList($deliveryNotes)
        $saleDeliveryNotes = $selectedModelQuery->execute()->getQuery()->getResult();

        return new Response($this->sdnpm->outputDeliveryNotesList($saleDeliveryNotes), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * @return Response|RedirectResponse
     */
    public function batchActionGenerateSaleInvoiceFromDeliveryNotes(ProxyQueryInterface $selectedModelQuery)
    {
        $this->admin->checkAccess('edit');
        $selectedModels = $selectedModelQuery->execute()->getQuery()->getResult();
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

    public function batchActionGenerateStandardPrint(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        /** @var SaleDeliveryNote[] $saleDeliveryNotes */
        $saleDeliveryNotes = $selectedModelQuery->execute();

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
        $date = new DateTime();
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
        if ($saleInvoice->getPartner()->getCollectionDocumentType()) {
            $saleInvoice->setCollectionDocumentType($saleInvoice->getPartner()->getCollectionDocumentType());
        }
        if ($saleInvoice->getPartner()->getPartnerDeliveryAddresses()->first()) {
            $saleInvoice->setDeliveryAddress($saleInvoice->getPartner()->getPartnerDeliveryAddresses()->first());
        }
        $this->createDueDatesFromSaleInvoice($saleInvoice);
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

    private function createDueDatesFromSaleInvoice(SaleInvoice $saleInvoice)
    {
        $partner = $saleInvoice->getPartner();
        $numberOfCollectionTerms = 1;
        if ($partner->getCollectionTerm3() > 0) {
            $numberOfCollectionTerms = 3;
        } elseif ($partner->getCollectionTerm2() > 0) {
            $numberOfCollectionTerms = 2;
        }
        $amountSplit = $saleInvoice->getTotal() / $numberOfCollectionTerms;
        $today = new DateTime();
        $payDay1 = $partner->getPayDay1() ? $partner->getPayDay1() : 0;
        $payDay2 = $partner->getPayDay2() ? $partner->getPayDay2() : 1;
        $payDay3 = $partner->getPayDay3() ? $partner->getPayDay3() : 1;
        $collectionTerm1 = $partner->getCollectionTerm1() ? $partner->getCollectionTerm1() : 0;
        $saleInvoiceDueDate1 = $this->generateDueDateWithAmountPayDayCollectionTerm($amountSplit, $payDay1, $payDay2, $payDay3, $collectionTerm1, $partner);
        $saleInvoice->addSaleInvoiceDueDate($saleInvoiceDueDate1);
        $collectionTerm2 = $partner->getCollectionTerm2();
        if ($collectionTerm2) {
            $saleInvoiceDueDate2 = $this->generateDueDateWithAmountPayDayCollectionTerm($amountSplit, $payDay1, $payDay2, $payDay3, $collectionTerm2, $partner);
            $saleInvoice->addSaleInvoiceDueDate($saleInvoiceDueDate2);
            $collectionTerm3 = $partner->getCollectionTerm3();
            if ($collectionTerm3) {
                $saleInvoiceDueDate3 = $this->generateDueDateWithAmountPayDayCollectionTerm($amountSplit, $payDay1, $payDay2, $payDay3, $collectionTerm3, $partner);
                $saleInvoice->addSaleInvoiceDueDate($saleInvoiceDueDate3);
            }
        }
    }

    private function generateDueDateWithAmountPayDayCollectionTerm(float $amount, int $payDay1, int $payDay2, int $payDay3, int $collectionTerm, Partner $partner): SaleInvoiceDueDate
    {
        $initialDueDate = new DateTime();
        $dueDate = new DateTime();
        $initialDueDate = $initialDueDate->setTimestamp(strtotime('+ '.$collectionTerm.' days'));
        $this->setDueDate($initialDueDate, $payDay1, $dueDate, $payDay2, $payDay3);
        while ($this->checkIfDateIsInPartnerUnableDates($dueDate, $partner)) {
            $this->setDueDate($dueDate, $payDay1, $dueDate, $payDay2, $payDay3);
        }
        $saleInvoiceDueDate = new SaleInvoiceDueDate();

        return $saleInvoiceDueDate
                    ->setDate($dueDate)
                    ->setAmount($amount)
                ;
    }

    private function checkIfDateIsInPartnerUnableDates(DateTime $date, Partner $partner): bool
    {
        $isInUnableDays = false;
        $dateFormatted = new DateTime();
        $dateFormatted->setDate('0000', $date->format('m'), $date->format('d'));
        $unableDays = $partner->getPartnerUnableDays();
        /** @var PartnerUnableDays $unableDay */
        foreach ($unableDays as $unableDay) {
            if ($dateFormatted->getTimestamp() >= $unableDay->getBegin()->getTimestamp()) {
                if ($dateFormatted->getTimestamp() <= $unableDay->getEnd()->getTimestamp()) {
                    $isInUnableDays = true;
                }
            }
        }

        return $isInUnableDays;
    }

    private function setDueDate(DateTime $initialDueDate, int $payDay1, DateTime $dueDate, int $payDay2, int $payDay3): void
    {
        if (0 === $payDay1) {
            $dueDate->setDate($initialDueDate->format('Y'), $initialDueDate->format('m'), $initialDueDate->format('d'));
        } elseif ($initialDueDate->format('d') * 1 <= $payDay1) {
            $dueDate->setDate($initialDueDate->format('Y'), $initialDueDate->format('m'), $payDay1);
        } elseif ($initialDueDate->format('d') * 1 <= $payDay2) {
            $dueDate->setDate($initialDueDate->format('Y'), $initialDueDate->format('m'), $payDay2);
        } elseif ($initialDueDate->format('d') * 1 <= $payDay3) {
            $dueDate->setDate($initialDueDate->format('Y'), $initialDueDate->format('m'), $payDay3);
        } else {
            $dueDate->setDate($initialDueDate->format('Y'), $initialDueDate->format('m') * 1 + 1, $payDay1);
        }
    }
}
