<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorCheckingType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class OperatorCheckingTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorCheckingType::class);
    }

    public function getEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('oct')
            ->where('oct.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('oct.name', 'ASC')
        ;
    }

    public function getEnabledSortedByNameQ(): Query
    {
        return $this->getEnabledSortedByNameQB()->getQuery();
    }

    public function getEnabledSortedByName(): array
    {
        return $this->getEnabledSortedByNameQ()->getResult();
    }

    public function getEnabledByTypeSortedByNameQB(int $operatorCheckingTypeCategory): QueryBuilder
    {
        return $this->createQueryBuilder('oct')
            ->where('oct.enabled = :enabled')
            ->andWhere('oct.category = :category')
            ->setParameter('enabled', true)
            ->setParameter('category', $operatorCheckingTypeCategory)
            ->orderBy('oct.name', 'ASC')
        ;
    }
}
