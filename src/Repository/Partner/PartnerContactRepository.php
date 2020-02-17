<?php

namespace App\Repository\Partner;

use App\Entity\Partner\PartnerContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class PartnerContactRepository.
 *
 * @category    Repository
 *
 * @author Rubèn Hierro <info@rubenhierro.com>
 */
class PartnerContactRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PartnerContact::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getEnabledSortedByNameQB()
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('p.name', 'ASC')
        ;
    }

    /**
     * @return Query
     */
    public function getEnabledSortedByNameQ()
    {
        return  $this->getEnabledSortedByNameQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getEnabledSortedByName()
    {
        return $this->getEnabledSortedByNameQ()->getResult();
    }

    /**
     * @param int $partnerId
     *
     * @return QueryBuilder
     */
    public function getFilteredByPartnerSortedByNameQB(int $partnerId)
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('p.partner = :partner')
            ->setParameter('partner', $partnerId)
        ;
    }

    /**
     * @param int $partnerId
     *
     * @return Query
     */
    public function getFilteredByPartnerSortedByNameQ(int $partnerId)
    {
        return $this->getFilteredByPartnerSortedByNameQB($partnerId)->getQuery();
    }

    /**
     * @param int $partnerId
     *
     * @return array
     */
    public function getFilteredByPartnerSortedByName(int $partnerId)
    {
        return $this->getFilteredByPartnerSortedByNameQ($partnerId)->getResult();
    }
}
