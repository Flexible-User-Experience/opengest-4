<?php

namespace App\Repository\Operator;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getFilteredByEnterpriseEnabledSortedByNameQB(Enterprise $enterprise, ?Operator $operator = null): QueryBuilder
    {
        $queryBuilder = $this->getEnabledSortedByNameBQ()
            ->andWhere('o.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
        ;
        if ($operator) {
            $queryBuilder
                ->orWhere('o.id = :operatorId')
                ->setParameter('operatorId', $operator->getId())
            ;
        }

        return $queryBuilder;
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
