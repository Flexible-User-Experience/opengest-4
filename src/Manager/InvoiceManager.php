<?php

namespace App\Manager;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\SaleInvoiceSeries;
use App\Repository\Sale\SaleInvoiceRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class InvoiceManager.
 *
 * @category Manager
 **/
class InvoiceManager
{
    /**
     * @var SaleInvoiceRepository
     */
    private SaleInvoiceRepository $saleInvoiceRepository;

    /**
     * Methods.
     */

    /**
     * @param SaleInvoiceRepository $saleInvoiceRepository
     */
    public function __construct(SaleInvoiceRepository $saleInvoiceRepository)
    {
        $this->saleInvoiceRepository = $saleInvoiceRepository;
    }

    /**
     * @param SaleInvoiceSeries $serie
     * @param Enterprise        $enterprise
     *
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getLastInvoiceNumberBySerieAndEnterprise(SaleInvoiceSeries $serie, Enterprise $enterprise)
    {
        $lastSaleInvoice = $this->saleInvoiceRepository->getLastInvoiceBySerieAndEnterprise($serie, $enterprise);

        return $lastSaleInvoice ? $lastSaleInvoice->getInvoiceNumber() + 1 : 1;
    }
}
