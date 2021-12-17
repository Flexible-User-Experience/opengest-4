<?php

namespace App\Repository\Partner;

use App\Entity\Partner\PartnerDeliveryAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PartnerDeliveryAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerDeliveryAddress::class);
    }

    public function getEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('p.address', 'ASC')
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
