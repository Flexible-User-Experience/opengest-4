<?php

namespace App\Repository\Web;

use App\Entity\Web\Work;
use App\Entity\Web\WorkImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class WorkImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkImage::class);
    }

    public function findEnabledSortedByPositionQB(Work $work): QueryBuilder
    {
        return $this->createQueryBuilder('wi')
            ->where('wi.work = :work')
            ->andWhere('wi.enabled = :enabled')
            ->setParameter('work', $work)
            ->setParameter('enabled', true)
            ->orderBy('wi.position', 'ASC');
    }

    public function findEnabledSortedByPositionQ(Work $work): Query
    {
        return $this->findEnabledSortedByPositionQB($work)->getQuery();
    }

    public function findEnabledSortedByPosition(Work $work): array
    {
        return $this->findEnabledSortedByPositionQ($work)->getResult();
    }
}
