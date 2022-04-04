<?php

namespace App\Repository\Sale;

use App\Entity\Sale\SaleServiceTariff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SaleServiceTariffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleServiceTariff::class);
    }

    public function getEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('sst')
            ->addSelect('sst.description + 0 as HIDDEN numeric')
            ->where('sst.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('numeric', 'ASC')
            ->addOrderBy('sst.description', 'ASC')
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
}
