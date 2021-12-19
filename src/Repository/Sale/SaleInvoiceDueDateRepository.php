<?php

namespace App\Repository\Sale;

use App\Entity\Sale\SaleInvoiceDueDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SaleInvoiceDueDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleInvoiceDueDate::class);
    }
}
