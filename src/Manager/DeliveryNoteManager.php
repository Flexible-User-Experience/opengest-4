<?php

namespace App\Manager;

use App\Entity\Enterprise\Enterprise;
use App\Repository\Sale\SaleDeliveryNoteRepository;

/**
 * Class DeliveryNoteManager.
 *
 * @category Manager
 **/
class DeliveryNoteManager
{
    private SaleDeliveryNoteRepository $saleDeliveryNoteRepository;

    /**
     * Methods.
     */
    public function __construct(SaleDeliveryNoteRepository $saleDeliveryNoteRepository)
    {
        $this->saleDeliveryNoteRepository = $saleDeliveryNoteRepository;
    }

    public function getLastDeliveryNoteByenterprise(Enterprise $enterprise): int
    {
        $lastDeliveryNote = $this->saleDeliveryNoteRepository->getLastDeliveryNoteByenterprise($enterprise);

        return $lastDeliveryNote ? $lastDeliveryNote->getId() + 1 : 1;
    }

    public function getAvailableIdsByEnterprise(Enterprise $enterprise)
    {
        $deliveryNoteIds = $this->saleDeliveryNoteRepository->getAllDeliveryNoteIdsByEnterprise($enterprise);
        $deliveryNoteIds = array_map(function ($number) {
            return $number['id'];
        }, $deliveryNoteIds);
        $new_arr = range($deliveryNoteIds[0], max($deliveryNoteIds));

        return array_diff($new_arr, $deliveryNoteIds);
    }
}
