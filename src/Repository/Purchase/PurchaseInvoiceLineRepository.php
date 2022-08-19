<?php

namespace App\Repository\Purchase;

use App\Entity\Purchase\PurchaseInvoiceLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseInvoiceLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseInvoiceLine::class);
    }

    public function getEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('pil')
            ->where('pil.enabled = :enabled')
            ->setParameter('enabled', true)
        ;
    }

    public function getEnabledSortedByNameB(): Query
    {
        return $this->getEnabledSortedByNameQB()->getQuery();
    }

    public function getEnabledSortedByName(): array
    {
        return $this->getEnabledSortedByNameB()->getResult();
    }

    public function getFilteredByYear(int $year)
    {
        return $this->getEnabledSortedByNameQB()
            ->join('pil.purchaseInvoice', 'pi')
            ->andWhere('year(pi.date) = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult()
        ;
    }
}
