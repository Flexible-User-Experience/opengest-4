<?php

namespace App\Repository\Vehicle;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class VehicleRepository.
 *
 * @category Repository
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class VehicleRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /**
     * @return QueryBuilder
     */
    public function findEnabledSortedByNameQB()
    {
        return $this->createQueryBuilder('v')
            ->where('v.enabled = :value')
            ->setParameter('value', true)
            ->orderBy('v.name', 'ASC');
    }

    /**
     * @return Query
     */
    public function findEnabledSortedByNameQ()
    {
        return $this->findEnabledSortedByNameQB()->getQuery();
    }

    /**
     * @return array
     */
    public function findEnabledSortedByName()
    {
        return $this->findEnabledSortedByNameQ()->getResult();
    }

    /**
     * @param VehicleCategory $category
     *
     * @return QueryBuilder
     */
    public function findEnabledSortedByNameFilterCategoryQB(VehicleCategory $category)
    {
        return $this->findEnabledSortedByNameQB()
            ->join('v.category', 'vc')
            ->andWhere('v.category = :category')
            ->setParameter('category', $category);
    }

    /**
     * @param VehicleCategory $category
     *
     * @return Query
     */
    public function findEnabledSortedByNameFilterCategoryQ(VehicleCategory $category)
    {
        return $this->findEnabledSortedByNameFilterCategoryQB($category)->getQuery();
    }

    /**
     * @param VehicleCategory $category
     *
     * @return array
     */
    public function findEnabledSortedByNameFilterCategory(VehicleCategory $category)
    {
        return $this->findEnabledSortedByNameFilterCategoryQ($category)->getResult();
    }

    /**
     * @param VehicleCategory $category
     *
     * @return QueryBuilder
     */
    public function findEnabledSortedByPositionAndNameQB(VehicleCategory $category)
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

    /**
     * @param VehicleCategory $category
     *
     * @return Query
     */
    public function findEnabledSortedByPositionAndNameQ(VehicleCategory $category)
    {
        return $this->findEnabledSortedByPositionAndNameQB($category)->getQuery();
    }

    /**
     * @param VehicleCategory $category
     *
     * @return array
     */
    public function findEnabledSortedByPositionAndName(VehicleCategory $category)
    {
        return $this->findEnabledSortedByPositionAndNameQ($category)->getResult();
    }

    /**
     * @param VehicleCategory $category
     *
     * @return QueryBuilder
     */
    public function findEnabledSortedByPositionAndNameForWebQB(VehicleCategory $category)
    {
        return $this->findEnabledSortedByPositionAndNameQB($category)
            ->join('v.enterprise', 'e')
            ->andWhere('e.taxIdentificationNumber = :tin')
            ->setParameter('tin', Enterprise::GRUAS_ROMANI_TIN);
    }

    /**
     * @param VehicleCategory $category
     *
     * @return Query
     */
    public function findEnabledSortedByPositionAndNameForWebQ(VehicleCategory $category)
    {
        return $this->findEnabledSortedByPositionAndNameForWebQB($category)->getQuery();
    }

    /**
     * @param VehicleCategory $category
     *
     * @return array
     */
    public function findEnabledSortedByPositionAndNameForWeb(VehicleCategory $category)
    {
        return $this->findEnabledSortedByPositionAndNameForWebQ($category)->getResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     */
    public function getFilteredByEnterpriseEnabledSortedByNameQB(Enterprise $enterprise)
    {
        return $this->findEnabledSortedByNameQB()
            ->andWhere('v.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise);
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Query
     */
    public function getFilteredByEnterpriseEnabledSortedByNameQ(Enterprise $enterprise)
    {
        return $this->getFilteredByEnterpriseEnabledSortedByNameQB($enterprise)->getQuery();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return array
     */
    public function getFilteredByEnterpriseEnabledSortedByName(Enterprise $enterprise)
    {
        return $this->getFilteredByEnterpriseEnabledSortedByNameQ($enterprise)->getResult();
    }
}
