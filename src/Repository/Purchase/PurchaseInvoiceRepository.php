<?php

namespace App\Repository\Purchase;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Purchase\PurchaseInvoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class PurchaseInvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseInvoice::class);
    }

    public function getEnabledSortedByDateQB(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('s.date', 'DESC')
            ;
    }

    public function gettEnabledSortedByDateQ(): Query
    {
        return $this->getEnabledSortedByDateQB()->getQuery();
    }

    public function getEnabledSortedByDate(): array
    {
        return $this->gettEnabledSortedByDateQ()->getResult();
    }

    public function getFilteredByEnterpriseSortedByDateQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->getEnabledSortedByDateQB()
            ->join('s.partner', 'p')
            ->andWhere('p.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
            ;
    }
}
