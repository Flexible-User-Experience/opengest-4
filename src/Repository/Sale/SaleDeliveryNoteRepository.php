<?php

namespace App\Repository\Sale;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleDeliveryNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class SaleDeliveryNoteRepository.
 *
 * @category    Repository
 *
 * @author Rubèn Hierro <info@rubenhierro.com>
 */
class SaleDeliveryNoteRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SaleDeliveryNote::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getEnabledSortedByNameQB()
    {
        return $this->createQueryBuilder('s')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('s.date', 'DESC')
        ;
    }

    /**
     * @return Query
     */
    public function getEnabledSortedByNameQ()
    {
        return $this->getEnabledSortedByNameQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getEnabledSortedByName()
    {
        return $this->getEnabledSortedByNameQ()->getResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     */
    public function getFilteredByEnterpriseSortedByNameQB(Enterprise $enterprise)
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('s.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
        ;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Query
     */
    public function getFilteredByEnterpriseSortedByNameQ(Enterprise $enterprise)
    {
        return $this->getFilteredByEnterpriseSortedByNameQB($enterprise)->getQuery();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return array
     */
    public function getFilteredByEnterpriseSortedByName(Enterprise $enterprise)
    {
        return $this->getFilteredByEnterpriseSortedByNameQ($enterprise)->getResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     */
    public function getLastDeliveryNoteByEnterpriseQB(Enterprise $enterprise)
    {
        return $this->getFilteredByEnterpriseSortedByNameQB($enterprise)
             ->orderBy('s.deliveryNoteNumber', 'DESC')
             ->setMaxResults(1)
         ;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Query
     */
    public function getLastDeliveryNoteByEnterpriseQ(Enterprise $enterprise)
    {
        return $this->getLastDeliveryNoteByEnterpriseQB($enterprise)->getQuery();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return SaleDeliveryNote|null
     *
     * @throws NonUniqueResultException
     */
    public function getLastDeliveryNoteByenterprise(Enterprise $enterprise)
    {
        return $this->getLastDeliveryNoteByEnterpriseQ($enterprise)->getOneOrNullResult();
    }

    /**
     * @return QueryBuilder
     */
    public function getEmptyQueryBuilder()
    {
        return $this->createQueryBuilder('s')
            ->where('s.id = :novalue')
            ->setParameter('novalue', 0)
        ;
    }
}
