<?php

namespace App\Repository\Web;

use App\Entity\Web\Work;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class WorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Work::class);
    }

    public function findEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('w')
            ->where('w.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('w.name', 'ASC');
    }

    public function findEnabledSortedByNameQ(): Query
    {
        return $this->findEnabledSortedByNameQB()->getQuery();
    }

    public function findEnabledSortedByName(): array
    {
        return $this->findEnabledSortedByNameQ()->getResult();
    }

    public function findEnabledSortedByDateQB(): QueryBuilder
    {
        return $this->createQueryBuilder('w')
            ->where('w.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('w.date', 'DESC');
    }

    public function findEnabledSortedByDateQ(): Query
    {
        return $this->findEnabledSortedByDateQB()->getQuery();
    }

    public function findEnabledSortedByDate(): array
    {
        return $this->findEnabledSortedByDateQ()->getResult();
    }
}
