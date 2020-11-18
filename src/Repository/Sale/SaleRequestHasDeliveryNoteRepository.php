<?php

namespace App\Repository\Sale;

use App\Entity\Sale\SaleRequestHasDeliveryNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SaleRequestHasDeliveryNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleRequestHasDeliveryNote::class);
    }
}
