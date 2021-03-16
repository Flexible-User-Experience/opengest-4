<?php

namespace App\Repository\Vehicle;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\VehicleCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class VehicleCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleCategory::class);
    }

    public function getEnabledSortedByNameQBForAdmin(): QueryBuilder
    {
        return $this->createQueryBuilder('vc')
            ->where('vc.enabled = :value')
            ->setParameter('value', true)
            ->orderBy('vc.name', 'ASC')
        ;
    }

    public function findEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->getEnabledSortedByNameQBForAdmin()->join('vc.vehicles', 'v');
    }

    public function findEnabledSortedByNameQ(): Query
    {
        return $this->findEnabledSortedByNameQB()->getQuery();
    }

    public function findEnabledSortedByName(): array
    {
        return $this->findEnabledSortedByNameQ()->getResult();
    }

    public function findEnabledSortedByNameForWebQB(): QueryBuilder
    {
        return $this->findEnabledSortedByNameQB()
            ->join('v.enterprise', 'e')
            ->andWhere('e.taxIdentificationNumber = :tin')
            ->setParameter('tin', Enterprise::GRUAS_ROMANI_TIN)
        ;
    }

    public function findEnabledSortedByNameForWebQ(): Query
    {
        return $this->findEnabledSortedByNameForWebQB()->getQuery();
    }

    public function findEnabledSortedByNameForWeb(): array
    {
        return $this->findEnabledSortedByNameForWebQ()->getResult();
    }
}
