<?php

namespace App\Repository\Operator;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class OperatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operator::class);
    }

    public function getEnabledSortedByNameBQ(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->where('o.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('o.surname1', 'ASC')
            ->addOrderBy('o.surname2', 'ASC')
            ->addOrderBy('o.name', 'ASC')
        ;
    }

    public function getEnabledSortedByNameB(): Query
    {
        return $this->getEnabledSortedByNameBQ()->getQuery();
    }

    public function getEnabledSortedByName(): array
    {
        return $this->getEnabledSortedByNameB()->getResult();
    }

    public function getFilteredByEnterpriseEnabledSortedByNameQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->getEnabledSortedByNameBQ()
            ->andWhere('o.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
        ;
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
