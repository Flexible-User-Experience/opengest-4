<?php

namespace App\Repository\Setting;

use App\Entity\Setting\TimeRange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class TimeRangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeRange::class);
    }

    public function getTimeRangesSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.name');
    }

    public function getTimeRangesSortedByNameQ(): Query
    {
        return $this->getTimeRangesSortedByNameQB()->getQuery();
    }

    public function getTimeRangesSortedByName(): array
    {
        return $this->getTimeRangesSortedByNameQ()->getResult();
    }
}
