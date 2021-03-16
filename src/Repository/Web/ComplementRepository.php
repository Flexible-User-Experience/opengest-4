<?php

namespace App\Repository\Web;

use App\Entity\Web\Complement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class ComplementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Complement::class);
    }

    public function findEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->where('c.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('c.name', 'ASC');
    }

    public function findEnabledSortedByNameQ(): Query
    {
        return $this->findEnabledSortedByNameQB()->getQuery();
    }

    public function findEnabledSortedByName(): array
    {
        return $this->findEnabledSortedByNameQ()->getResult();
    }
}
