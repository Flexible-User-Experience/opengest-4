<?php

namespace App\Repository\Partner;

use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerBuildingSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PartnerBuildingSiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerBuildingSite::class);
    }

    public function getEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('p.name', 'ASC')
        ;
    }

    public function getEnabledSortedByNameQ(): Query
    {
        return $this->getEnabledSortedByNameQB()->getQuery();
    }

    public function getEnabledSortedByName(): array
    {
        return $this->getEnabledSortedByNameQ()->getResult();
    }

    public function getEnabledSortedByNameWithPartnerJoinQB(): QueryBuilder
    {
        return $this->getEnabledSortedByNameQB()
            ->join('p.partner', 'pa')
        ;
    }

    public function getEnabledSortedByNameWithPartnerJoinQ(): Query
    {
        return $this->getEnabledSortedByNameWithPartnerJoinQB()->getQuery();
    }

    public function getEnabledSortedByNameWithPartnerJoin(): array
    {
        return $this->getEnabledSortedByNameWithPartnerJoinQ()->getResult();
    }

    public function getEnabledFilteredByPartnerSortedByNameQB(Partner $partner): QueryBuilder
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('p.partner = :partner')
            ->setParameter('partner', $partner)
            ;
    }

    public function getEnabledWithPendingInvoicesSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->join('App\Entity\Sale\SaleDeliveryNote', 'sdn', 'WITH', 'sdn.buildingSite = p.id')
            ->where('p.enabled = :enabled')
            ->andWhere('sdn.isInvoiced = :isInvoiced')
            ->setParameter('enabled', true)
            ->setParameter('isInvoiced', false)
            ->groupBy('p.id')
            ->orderBy('p.name', 'ASC')
        ;
    }

    public function getEnabledWithPendingInvoicesSortedByName(): array
    {
        return $this->getEnabledWithPendingInvoicesSortedByNameQB()->getQuery()->getResult();
    }
}
