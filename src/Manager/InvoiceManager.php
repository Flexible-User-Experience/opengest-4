<?php

namespace App\Manager;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleDeliveryNoteLine;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Setting\SaleInvoiceSeries;
use App\Repository\Sale\SaleInvoiceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;

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
    public function __construct(SaleInvoiceRepository $saleInvoiceRepository)
    {
        $this->saleInvoiceRepository = $saleInvoiceRepository;
    }

    /**
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getLastInvoiceNumberBySerieAndEnterprise(SaleInvoiceSeries $serie, Enterprise $enterprise)
    {
        $lastSaleInvoice = $this->saleInvoiceRepository->getLastInvoiceBySerieAndEnterprise($serie, $enterprise);

        return $lastSaleInvoice ? $lastSaleInvoice->getInvoiceNumber() + 1 : 1;
    }

    public function calculateInvoiceImportsFromDeliveryNotes(SaleInvoice $saleInvoice, Collection $deliveryNotes)
    {
        $baseAmount = 0;
        $finalTotal = 0;
        $iva = 0;
        $irpf = 0;
        /** @var SaleDeliveryNote $deliveryNote */
        foreach ($deliveryNotes as $deliveryNote) {
            /** @var SaleDeliveryNoteLine $deliveryNoteLine */
            foreach ($deliveryNote->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
                $baseLineAmount = $deliveryNoteLine->getTotal() * (1 - $deliveryNote->getDiscount() / 100) * (1 - $saleInvoice->getDiscount() / 100);
                $lineIva = $baseLineAmount * $deliveryNoteLine->getIva() / 100;
                $lineIrpf = $baseLineAmount * $deliveryNoteLine->getIrpf() / 100;
                $finalLineAmount = $baseLineAmount + $lineIva - $lineIrpf;
                $baseAmount += $baseLineAmount;
                $finalTotal += $finalLineAmount;
                $iva += $lineIva;
                $irpf += $lineIrpf;
            }
        }
        $saleInvoice->setBaseTotal(round($baseAmount, 2));
        $saleInvoice->setIva(round($iva, 2));
        $saleInvoice->setIrpf(round($irpf, 2));
        $saleInvoice->setTotal(round($saleInvoice->getBaseTotal() + $saleInvoice->getIva() - $saleInvoice->getIrpf(), 2));
    }

    /**
     * @throws NonUniqueResultException
     */
    public function checkIfNumberIsAllowedBySerieAndEnterprise(SaleInvoiceSeries $serie, Enterprise $enterprise, $invoiceNumber): bool
    {
        $return = false;
        if ($this->getLastInvoiceNumberBySerieAndEnterprise($serie, $enterprise) == $invoiceNumber) {
            $return = true;
        } else {
            if (count($this->saleInvoiceRepository->findBy(['invoiceNumber' => $invoiceNumber])) > 1) {
                $return = false;
            } elseif ($this->getLastInvoiceNumberBySerieAndEnterprise($serie, $enterprise) > $invoiceNumber) {
                $return = true;
            }
        }

        return $return;
    }
}
