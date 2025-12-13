<?php

namespace App\Manager;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerUnableDays;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleDeliveryNoteLine;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Sale\SaleInvoiceDueDate;
use App\Entity\Setting\SaleInvoiceSeries;
use App\Repository\Sale\SaleInvoiceRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Mirmit\EFacturaBundle\Service\EFacturaService;

/**
 * Class InvoiceManager.
 *
 * @category Manager
 **/
class InvoiceManager
{
    private SaleInvoiceRepository $saleInvoiceRepository;

    /**
     * Methods.
     */
    public function __construct(
        SaleInvoiceRepository $saleInvoiceRepository,
        private readonly EFacturaService $eFacturaService
    )
    {
        $this->saleInvoiceRepository = $saleInvoiceRepository;
    }

    /**
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getLastInvoiceNumberBySerieAndEnterprise(SaleInvoiceSeries $serie, Enterprise $enterprise): int
    {
        $lastSaleInvoice = $this->saleInvoiceRepository->getLastInvoiceBySerieAndEnterprise($serie, $enterprise);
        $firstInvoiceNumber = $this->getFirstInvoiceNumberBySerieAndEnterprise($serie);

        return $lastSaleInvoice ? $lastSaleInvoice->getInvoiceNumber() + 1 : $firstInvoiceNumber;
    }

    public function getFirstInvoiceNumberBySerieAndEnterprise(SaleInvoiceSeries $serie): int
    {
        $firstInvoiceNumber = 1;
        if (1 === $serie->getId()) {
            $firstInvoiceNumber = 16681;
        } elseif (2 === $serie->getId()) {
            $firstInvoiceNumber = 191;
        } elseif (3 === $serie->getId()) {
            $firstInvoiceNumber = 516;
        } elseif (4 === $serie->getId()) {
            $firstInvoiceNumber = 59;
        }

        return $firstInvoiceNumber;
    }

    public function calculateInvoiceImportsFromDeliveryNotes(SaleInvoice $saleInvoice, Collection $deliveryNotes)
    {
        $baseAmount = 0;
        $finalTotal = 0;
        $iva = 0;
        $iva21 = 0;
        $iva10 = 0;
        $iva4 = 0;
        $iva0 = 0;
        $irpf = 0;
        /** @var SaleDeliveryNote $deliveryNote */
        foreach ($deliveryNotes as $deliveryNote) {
            /** @var SaleDeliveryNoteLine $deliveryNoteLine */
            foreach ($deliveryNote->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
                $baseLineAmount = $deliveryNoteLine->getTotal() * (1 - $deliveryNote->getDiscount() / 100) * (1 - $saleInvoice->getDiscount() / 100);
                $lineIvaPercent = $deliveryNoteLine->getIva();
                $lineIva = $baseLineAmount * $lineIvaPercent / 100;
                $lineIrpf = $baseLineAmount * $deliveryNoteLine->getIrpf() / 100;
                $finalLineAmount = $baseLineAmount + $lineIva - $lineIrpf;
                $baseAmount += $baseLineAmount;
                $finalTotal += $finalLineAmount;
                if (21 == $lineIvaPercent) {
                    $iva21 += $lineIva;
                } elseif (10 == $lineIvaPercent) {
                    $iva10 += $lineIva;
                } elseif (4 == $lineIvaPercent) {
                    $iva4 += $lineIva;
                } elseif (0 == $lineIvaPercent) {
                    $iva0 += $lineIva;
                }
                $iva += $lineIva;
                $irpf += $lineIrpf;
            }
        }
        $saleInvoice->setBaseTotal(round($baseAmount, 2));
        $saleInvoice->setIva(round($iva, 2));
        $saleInvoice->setIva21(round($iva21, 2));
        $saleInvoice->setIva10(round($iva10, 2));
        $saleInvoice->setIva4(round($iva4, 2));
        $saleInvoice->setIva0(0 == $iva0 ? round($iva0, 2) : null);
        $saleInvoice->setIrpf(round($irpf, 2));
        $saleInvoice->setTotal(round($saleInvoice->getBaseTotal() + $saleInvoice->getIva() - $saleInvoice->getIrpf(), 2));
    }

    /**
     * @throws NonUniqueResultException
     */
    public function checkIfNumberIsAllowedBySerieAndEnterprise(SaleInvoiceSeries $serie, Enterprise $enterprise, $invoiceNumber, ?int $invoiceId = null): bool
    {
        // Busca facturas con el mismo número Y la misma serie
        $invoicesWithNumber = $this->saleInvoiceRepository->findBy(['invoiceNumber' => $invoiceNumber, 'series' => $serie]);

        // Si estamos editando una factura, la excluimos de la verificación
        if ($invoiceId !== null && count($invoicesWithNumber) > 0) {
            $invoicesWithNumber = array_filter($invoicesWithNumber, function($invoice) use ($invoiceId) {
                return $invoice->getId() !== $invoiceId;
            });
        }

        if (0 == count($invoicesWithNumber)) {
            $firstInvoiceNumber = $this->getFirstInvoiceNumberBySerieAndEnterprise($serie);
            $lastInvoiceNumber = $this->getLastInvoiceNumberBySerieAndEnterprise($serie, $enterprise);
            if ($firstInvoiceNumber <= $invoiceNumber && $lastInvoiceNumber >= $invoiceNumber) {
                return true;
            }
        }

        return false;
    }

    public function getAvailableNumbersBySerieAndEnterprise(SaleInvoiceSeries $serie, Enterprise $enterprise)
    {
        $invoiceNumbers = $this->saleInvoiceRepository->getAllInvoiceNumbersByEnterpriseAndSeries($enterprise, $serie);
        $invoiceNumbers = array_map(function ($number) {
            return $number['invoiceNumber'];
        }, $invoiceNumbers);
        $new_arr = range($invoiceNumbers[0], max($invoiceNumbers));

        return array_diff($new_arr, $invoiceNumbers);
    }

    public function createDueDatesFromSaleInvoice(SaleInvoice $saleInvoice)
    {
        $partner = $saleInvoice->getPartner();
        /** @var SaleDeliveryNote $deliveryNote */
        $deliveryNote = $saleInvoice->getDeliveryNotes()->first();
        $invoiceDate = $saleInvoice->getDate();
        $numberOfCollectionTerms = 1;
        $collectionTerm3 = $deliveryNote->getCollectionTerm3();
        $collectionTerm2 = $deliveryNote->getCollectionTerm2();
        if ( $collectionTerm3 && $deliveryNote->getCollectionTerm3() > 0) {
            $numberOfCollectionTerms = 3;
        } elseif ($collectionTerm2 && $deliveryNote->getCollectionTerm2() > 0) {
            $numberOfCollectionTerms = 2;
        }
        $amountSplit = $saleInvoice->getTotal() / $numberOfCollectionTerms;
        $payDay1 = $partner->getPayDay1() ? $partner->getPayDay1() : 0;
        $payDay2 = $partner->getPayDay2() ? $partner->getPayDay2() : 1;
        $payDay3 = $partner->getPayDay3() ? $partner->getPayDay3() : 1;
        $collectionTerm1 = $deliveryNote->getCollectionTerm() ? $deliveryNote->getCollectionTerm() : 0;
        $saleInvoiceDueDate1 = $this->generateDueDateWithAmountPayDayCollectionTerm($invoiceDate, $amountSplit, $payDay1, $payDay2, $payDay3, $collectionTerm1, $partner);
        $saleInvoice->addSaleInvoiceDueDate($saleInvoiceDueDate1);
        $collectionTerm2 = $deliveryNote->getCollectionTerm2();
        if ($collectionTerm2) {
            $saleInvoiceDueDate2 = $this->generateDueDateWithAmountPayDayCollectionTerm($invoiceDate, $amountSplit, $payDay1, $payDay2, $payDay3, $collectionTerm2, $partner);
            $saleInvoice->addSaleInvoiceDueDate($saleInvoiceDueDate2);
            $collectionTerm3 = $deliveryNote->getCollectionTerm3();
            if ($collectionTerm3) {
                $saleInvoiceDueDate3 = $this->generateDueDateWithAmountPayDayCollectionTerm($invoiceDate, $amountSplit, $payDay1, $payDay2, $payDay3, $collectionTerm3, $partner);
                $saleInvoice->addSaleInvoiceDueDate($saleInvoiceDueDate3);
            }
        }
    }

    public function createEInvoice(SaleInvoice $saleInvoice): int|string
    {
        $xml = $this->eFacturaService->createEFactura(
            $saleInvoice,
            billingPeriodStart: $saleInvoice->getDate(),
            billingPeriodEnd: $saleInvoice->getDate());
        $accountingAccount = $saleInvoice->getPartner()?->getAccountingAccount();
        if ($accountingAccount) {
            $shortAccountedAccount = substr($accountingAccount, 3) * 1;
            $buyerPartyIdentification = '<PartyIdentification>'.$shortAccountedAccount.'</PartyIdentification>';
            $xml = str_replace('</BuyerParty>', $buyerPartyIdentification.'</BuyerParty>',$xml);
        }
        $taxType = $saleInvoice->getPartner()?->getTaxType();
        if ($taxType) {
            $xml = str_replace('<TaxTypeCode>01</TaxTypeCode>', '<TaxTypeCode>'.$taxType->value.'</TaxTypeCode>', $xml);
        }

        return $xml;
    }

    private function generateDueDateWithAmountPayDayCollectionTerm(DateTime $invoiceDate, float $amount, int $payDay1, int $payDay2, int $payDay3, int $collectionTerm, Partner $partner): SaleInvoiceDueDate
    {
        $initialDueDate = new DateTime();
        $initialDueDate = $initialDueDate->setTimestamp(strtotime($invoiceDate->format('y-m-d').' + '.$collectionTerm.' days'));
        $dueDate = $initialDueDate;
        $this->setDueDate($initialDueDate, $payDay1, $dueDate, $payDay2, $payDay3);
        while ($this->checkIfDateIsInPartnerUnableDates($dueDate, $partner)) {
            $this->setDueDate($dueDate, $payDay1, $dueDate, $payDay2, $payDay3);
            if ($this->checkIfDateIsInPartnerUnableDates($dueDate, $partner)) {
                $dueDate->modify('+1 day');
            }
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
        $yearDate = ($date->format('m').$date->format('d')) * 1;
        $unableDays = $partner->getPartnerUnableDays();
        /** @var PartnerUnableDays $unableDay */
        foreach ($unableDays as $unableDay) {
            $yearDateBegin = ($unableDay->getBegin()->format('m').$unableDay->getBegin()->format('d')) * 1;
            $yearDateEnd = ($unableDay->getEnd()->format('m').$unableDay->getEnd()->format('d')) * 1;
            if ($yearDate >= $yearDateBegin) {
                if ($yearDate <= $yearDateEnd) {
                    $isInUnableDays = true;
                    break;
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
