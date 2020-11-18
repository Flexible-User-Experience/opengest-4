<?php

namespace App\Repository\Enterprise;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class EnterpriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enterprise::class);
    }

    public function getEnterprisesByUserQB($user): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->join('e.users', 'u')
            ->where('e.enabled = :value')
            ->andWhere('u.id = :id')
            ->setParameter('value', true)
            ->setParameter('id', $user->getId())
            ->orderBy('e.name', 'ASC')
        ;
    }

    public function getEnterprisesByUserQ(User $user): Query
    {
        return $this->getEnterprisesByUserQB($user)->getQuery();
    }

    public function getEnterprisesByUser(User $user): array
    {
        return $this->getEnterprisesByUserQ($user)->getResult();
    }
}
