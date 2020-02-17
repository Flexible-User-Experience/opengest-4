<?php

namespace App\Repository\Vehicle;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\VehicleCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class VehicleCategoryRepository.
 *
 * @category Repository
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class VehicleCategoryRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VehicleCategory::class);
    }

    /**
     * @return QueryBuilder
     */
    public function findEnabledSortedByNameQB()
    {
        return $this->createQueryBuilder('vc')
            ->join('vc.vehicles', 'v')
            ->where('vc.enabled = :value')
            ->setParameter('value', true)
            ->orderBy('vc.name', 'ASC')
        ;
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
     * @return QueryBuilder
     */
    public function findEnabledSortedByNameForWebQB()
    {
        return $this->findEnabledSortedByNameQB()
            ->join('v.enterprise', 'e')
            ->andWhere('e.taxIdentificationNumber = :tin')
            ->setParameter('tin', Enterprise::GRUAS_ROMANI_TIN)
        ;
    }

    /**
     * @return Query
     */
    public function findEnabledSortedByNameForWebQ()
    {
        return $this->findEnabledSortedByNameForWebQB()->getQuery();
    }

    /**
     * @return array
     */
    public function findEnabledSortedByNameForWeb()
    {
        return $this->findEnabledSortedByNameForWebQ()->getResult();
    }
}
