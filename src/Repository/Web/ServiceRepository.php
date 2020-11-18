<?php

namespace App\Repository\Web;

use App\Entity\Web\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    private function findEnabledQB(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', true);
    }

    public function findEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->findEnabledQB()->orderBy('s.name', 'ASC');
    }

    public function findEnabledSortedByNameQ(): Query
    {
        return $this->findEnabledSortedByNameQB()->getQuery();
    }

    public function findEnabledSortedByName(): array
    {
        return $this->findEnabledSortedByNameQ()->getResult();
    }

    public function findEnabledSortedByPositionAndNameQB(): QueryBuilder
    {
        return $this->findEnabledQB()
            ->orderBy('s.position', 'ASC')
            ->addOrderBy('s.name', 'ASC');
    }

    public function findEnabledSortedByPositionAndNameQ(): Query
    {
        return $this->findEnabledSortedByPositionAndNameQB()->getQuery();
    }

    public function findEnabledSortedByPositionAndName(): array
    {
        return $this->findEnabledSortedByPositionAndNameQ()->getResult();
    }

    public function findEnabledSortedByPositionQB(): QueryBuilder
    {
        return $this->findEnabledQB()->orderBy('s.position', 'ASC');
    }

    public function findEnabledSortedByPositionQ(): Query
    {
        return $this->findEnabledSortedByPositionQB()->getQuery();
    }

    public function findEnabledSortedByPosition(): array
    {
        return $this->findEnabledSortedByPositionQ()->getResult();
    }
}
