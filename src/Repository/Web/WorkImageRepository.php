<?php

namespace App\Repository\Web;

use App\Entity\Web\Work;
use App\Entity\Web\WorkImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Class WorkImageRepository.
 *
 * @category Repository
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class WorkImageRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorkImage::class);
    }

    /**
     * @param Work $work
     *
     * @return QueryBuilder
     */
    public function findEnabledSortedByPositionQB(Work $work)
    {
        return $this->createQueryBuilder('wi')
            ->where('wi.work = :work')
            ->andWhere('wi.enabled = :enabled')
            ->setParameter('work', $work)
            ->setParameter('value', true)
            ->orderBy('wi.position', 'ASC');
    }
}
