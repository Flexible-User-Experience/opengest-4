<?php

namespace App\Repository\Setting;

use App\Entity\Setting\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class     CityRepository.
 *
 * @category Repository
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class CityRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getCitiesSortedByNameQB()
    {
        return $this->createQueryBuilder('c')->orderBy('c.name');
    }

    /**
     * @return Query
     */
    public function getCitiesSortedByNameQ()
    {
        return $this->getCitiesSortedByNameQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getCitiesSortedByName()
    {
        return $this->getCitiesSortedByNameQ()->getResult();
    }
}
