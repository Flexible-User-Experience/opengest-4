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
            $baseAmount += $deliveryNote->getBaseAmount();
            /** @var SaleDeliveryNoteLine $deliveryNoteLine */
            foreach ($deliveryNote->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
                $baseLineAmount = $deliveryNoteLine->getTotal() * (1 - $deliveryNote->getDiscount() / 100);
                $lineIva = $baseLineAmount * $deliveryNoteLine->getIva() / 100;
                $lineIrpf = $baseLineAmount * $deliveryNoteLine->getIrpf() / 100;
                $finalLineAmount = $baseLineAmount + $lineIva - $lineIrpf;
                $finalTotal += $finalLineAmount;
                $iva += $lineIva;
                $irpf += $lineIrpf;
            }
        }
        $saleInvoice->setBaseTotal($baseAmount);
        $saleInvoice->setIva($iva);
        $saleInvoice->setIrpf($irpf);
        $saleInvoice->setTotal($finalTotal);
    }
}
