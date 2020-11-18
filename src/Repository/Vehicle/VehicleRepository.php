<?php

namespace App\Repository\Vehicle;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public function findEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('v')
            ->where('v.enabled = :value')
            ->setParameter('value', true)
            ->orderBy('v.name', 'ASC');
    }

    public function findEnabledSortedByNameQ(): Query
    {
        return $this->findEnabledSortedByNameQB()->getQuery();
    }

    public function findEnabledSortedByName(): array
    {
        return $this->findEnabledSortedByNameQ()->getResult();
    }

    public function findEnabledSortedByNameFilterCategoryQB(VehicleCategory $category): QueryBuilder
    {
        return $this->findEnabledSortedByNameQB()
            ->join('v.category', 'vc')
            ->andWhere('v.category = :category')
            ->setParameter('category', $category);
    }

    public function findEnabledSortedByNameFilterCategoryQ(VehicleCategory $category): Query
    {
        return $this->findEnabledSortedByNameFilterCategoryQB($category)->getQuery();
    }

    public function findEnabledSortedByNameFilterCategory(VehicleCategory $category): array
    {
        return $this->findEnabledSortedByNameFilterCategoryQ($category)->getResult();
    }

    public function findEnabledSortedByPositionAndNameQB(VehicleCategory $category): QueryBuilder
    {
        return $this->createQueryBuilder('v')
            ->join('v.category', 'vc')
            ->where('v.enabled = :enabled')
            ->andWhere('v.category = :category')
            ->setParameter('enabled', true)
            ->setParameter('category', $category)
            ->orderBy('v.position', 'ASC')
            ->addOrderBy('v.name', 'ASC');
    }

    public function findEnabledSortedByPositionAndNameQ(VehicleCategory $category): Query
    {
        return $this->findEnabledSortedByPositionAndNameQB($category)->getQuery();
    }

    public function findEnabledSortedByPositionAndName(VehicleCategory $category): array
    {
        return $this->findEnabledSortedByPositionAndNameQ($category)->getResult();
    }

    public function findEnabledSortedByPositionAndNameForWebQB(VehicleCategory $category): QueryBuilder
    {
        return $this->findEnabledSortedByPositionAndNameQB($category)
            ->join('v.enterprise', 'e')
            ->andWhere('e.taxIdentificationNumber = :tin')
            ->setParameter('tin', Enterprise::GRUAS_ROMANI_TIN);
    }

    public function findEnabledSortedByPositionAndNameForWebQ(VehicleCategory $category): Query
    {
        return $this->findEnabledSortedByPositionAndNameForWebQB($category)->getQuery();
    }

    public function findEnabledSortedByPositionAndNameForWeb(VehicleCategory $category): array
    {
        return $this->findEnabledSortedByPositionAndNameForWebQ($category)->getResult();
    }

    public function getFilteredByEnterpriseEnabledSortedByNameQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->findEnabledSortedByNameQB()
            ->andWhere('v.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise);
    }

    public function getFilteredByEnterpriseEnabledSortedByNameQ(Enterprise $enterprise): Query
    {
        return $this->getFilteredByEnterpriseEnabledSortedByNameQB($enterprise)->getQuery();
    }

    public function getFilteredByEnterpriseEnabledSortedByName(Enterprise $enterprise): array
    {
        return $this->getFilteredByEnterpriseEnabledSortedByNameQ($enterprise)->getResult();
    }
}
