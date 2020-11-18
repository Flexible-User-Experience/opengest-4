<?php

namespace App\Repository\Setting;

use App\Entity\Setting\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function getCitiesSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.name');
    }

    public function getCitiesSortedByNameQ(): Query
    {
        return $this->getCitiesSortedByNameQB()->getQuery();
    }

    public function getCitiesSortedByName(): array
    {
        return $this->getCitiesSortedByNameQ()->getResult();
    }
}
