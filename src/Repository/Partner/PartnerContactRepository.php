<?php

namespace App\Repository\Partner;

use App\Entity\Partner\PartnerContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class PartnerContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerContact::class);
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
        return  $this->getEnabledSortedByNameQB()->getQuery();
    }

    public function getEnabledSortedByName(): array
    {
        return $this->getEnabledSortedByNameQ()->getResult();
    }

    public function getFilteredByPartnerSortedByNameQB(int $partnerId): QueryBuilder
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('p.partner = :partner')
            ->setParameter('partner', $partnerId)
        ;
    }

    public function getFilteredByPartnerSortedByNameQ(int $partnerId): Query
    {
        return $this->getFilteredByPartnerSortedByNameQB($partnerId)->getQuery();
    }

    public function getFilteredByPartnerSortedByName(int $partnerId): array
    {
        return $this->getFilteredByPartnerSortedByNameQ($partnerId)->getResult();
    }
}
