<?php

namespace App\Repository\Partner;

use App\Entity\Partner\PartnerOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class PartnerOrderRepository.
 *
 * @category    Repository
 *
 * @author Rubèn Hierro <info@rubenhierro.com>
 */
class PartnerOrderRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PartnerOrder::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getEnabledSortedByNumberQB()
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('p.number', 'DESC')
        ;
    }

    /**
     * @return Query
     */
    public function getEnabledSortedByNumberQ()
    {
        return  $this->getEnabledSortedByNumberQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getEnabledSortedByNumber()
    {
        return $this->getEnabledSortedByNumberQ()->getResult();
    }
}
