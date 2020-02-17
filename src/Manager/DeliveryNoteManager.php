<?php

namespace App\Manager;

use App\Entity\Enterprise\Enterprise;
use App\Repository\Sale\SaleDeliveryNoteRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class DeliveryNoteManager.
 *
 * @category Manager
 **/
class DeliveryNoteManager
{
    /**
     * @var SaleDeliveryNoteRepository
     */
    private SaleDeliveryNoteRepository $saleDeliveryNoteRepository;

    /**
     * Methods.
     */

    /**
     * @param SaleDeliveryNoteRepository $saleDeliveryNoteRepository
     */
    public function __construct(SaleDeliveryNoteRepository $saleDeliveryNoteRepository)
    {
        $this->saleDeliveryNoteRepository = $saleDeliveryNoteRepository;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getLastDeliveryNoteByenterprise(Enterprise $enterprise)
    {
        $lastDeliveryNote = $this->saleDeliveryNoteRepository->getLastDeliveryNoteByenterprise($enterprise);

        return $lastDeliveryNote ? $lastDeliveryNote->getDeliveryNoteNumber() + 1 : 1;
    }
}
