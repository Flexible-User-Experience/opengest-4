<?php

namespace App\Repository\Sale;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleDeliveryNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class SaleDeliveryNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleDeliveryNote::class);
    }

    public function getEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('s.date', 'DESC')
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

    public function getFilteredByEnterpriseSortedByNameQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('s.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
        ;
    }

    public function getFilteredByEnterpriseSortedByNameQ(Enterprise $enterprise): Query
    {
        return $this->getFilteredByEnterpriseSortedByNameQB($enterprise)->getQuery();
    }

    public function getFilteredByEnterpriseSortedByName(Enterprise $enterprise): array
    {
        return $this->getFilteredByEnterpriseSortedByNameQ($enterprise)->getResult();
    }

    public function getLastDeliveryNoteByEnterpriseQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->getFilteredByEnterpriseSortedByNameQB($enterprise)
             ->orderBy('s.deliveryNoteReference', 'DESC')
             ->setMaxResults(1)
         ;
    }

    public function getLastDeliveryNoteByEnterpriseQ(Enterprise $enterprise): Query
    {
        return $this->getLastDeliveryNoteByEnterpriseQB($enterprise)->getQuery();
    }

    public function getLastDeliveryNoteByenterprise(Enterprise $enterprise): ?SaleDeliveryNote
    {
        try {
            $result = $this->getLastDeliveryNoteByEnterpriseQ($enterprise)->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $result = null;
        }

        return $result;
    }

    public function getEmptyQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->where('s.id = :novalue')
            ->setParameter('novalue', 0)
        ;
    }
}
