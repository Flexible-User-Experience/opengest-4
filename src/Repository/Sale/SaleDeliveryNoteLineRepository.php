<?php

namespace App\Repository\Sale;

use App\Entity\Sale\SaleDeliveryNoteLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SaleDeliveryNoteLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleDeliveryNoteLine::class);
    }
}
