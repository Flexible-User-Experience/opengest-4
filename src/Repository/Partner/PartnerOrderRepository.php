<?php

namespace App\Repository\Partner;

use App\Entity\Partner\PartnerOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class PartnerOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerOrder::class);
    }

    public function getEnabledSortedByNumberQB(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('p.number', 'DESC')
        ;
    }

    public function getEnabledSortedByNumberQ(): Query
    {
        return  $this->getEnabledSortedByNumberQB()->getQuery();
    }

    public function getEnabledSortedByNumber(): array
    {
        return $this->getEnabledSortedByNumberQ()->getResult();
    }
}
