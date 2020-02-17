<?php

namespace App\Repository\Web;

use App\Entity\Web\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ServiceRepository.
 *
 * @category Repository
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class ServiceRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Service::class);
    }

    /**
     * @return QueryBuilder
     */
    private function findEnabledQB()
    {
        return $this->createQueryBuilder('s')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', true);
    }

    /**
     * @return QueryBuilder
     */
    public function findEnabledSortedByNameQB()
    {
        return $this->findEnabledQB()->orderBy('s.name', 'ASC');
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
    public function findEnabledSortedByPositionAndNameQB()
    {
        return $this->findEnabledQB()
            ->orderBy('s.position', 'ASC')
            ->addOrderBy('s.name', 'ASC');
    }

    /**
     * @return Query
     */
    public function findEnabledSortedByPositionAndNameQ()
    {
        return $this->findEnabledSortedByPositionAndNameQB()->getQuery();
    }

    /**
     * @return array
     */
    public function findEnabledSortedByPositionAndName()
    {
        return $this->findEnabledSortedByPositionAndNameQ()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function findEnabledSortedByPositionQB()
    {
        return $this->findEnabledQB()->orderBy('s.position', 'ASC');
    }

    /**
     * @return Query
     */
    public function findEnabledSortedByPositionQ()
    {
        return $this->findEnabledSortedByPositionQB()->getQuery();
    }

    /**
     * @return array
     */
    public function findEnabledSortedByPosition()
    {
        return $this->findEnabledSortedByPositionQ()->getResult();
    }
}
