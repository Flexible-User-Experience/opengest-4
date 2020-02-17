<?php

namespace App\Repository\Enterprise;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class EnterpriseRepository.
 *
 * @category    Repository
 *
 * @author      Wils Iglesias <wiglesias83@gmail.com>
 */
class EnterpriseRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Enterprise::class);
    }

    /**
     * @param User|object|string $user
     *
     * @return QueryBuilder
     */
    public function getEnterprisesByUserQB($user)
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

    /**
     * @param User $user
     *
     * @return Query
     */
    public function getEnterprisesByUserQ(User $user)
    {
        return $this->getEnterprisesByUserQB($user)->getQuery();
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getEnterprisesByUser(User $user)
    {
        return $this->getEnterprisesByUserQ($user)->getResult();
    }
}
