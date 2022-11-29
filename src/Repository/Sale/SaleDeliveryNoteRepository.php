<?php

namespace App\Repository\Sale;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Partner\Partner;
use App\Entity\Sale\SaleDeliveryNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getFilteredByEnterpriseSortedByIdQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('s.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
            ->orderBy('s.id', 'ASC')
        ;
    }

    public function getFilteredByEnterpriseSortedByIdQ(Enterprise $enterprise): Query
    {
        return $this->getFilteredByEnterpriseSortedByIdQB($enterprise)->getQuery();
    }

    public function getFilteredByEnterpriseSortedById(Enterprise $enterprise): array
    {
        return $this->getFilteredByEnterpriseSortedByIdQ($enterprise)->getResult();
    }

    public function getFilteredByEnterpriseNotInvoicedSortedByIdQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', true)
            ->andWhere('s.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
            ->andWhere('s.isInvoiced = :isInvoiced')
            ->setParameter('isInvoiced', false)
            ->orderBy('s.id', 'ASC')
        ;
    }

    public function getFilteredByEnterpriseNotInvoicedSortedByIdQ(Enterprise $enterprise): Query
    {
        return $this->getFilteredByEnterpriseNotInvoicedSortedByIdQB($enterprise)->getQuery();
    }

    public function getFilteredByEnterpriseNotInvoicedSortedById(Enterprise $enterprise): array
    {
        return $this->getFilteredByEnterpriseNotInvoicedSortedByIdQ($enterprise)->getResult();
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

    public function getFilteredByEnterpriseAndPartnerSortedByNameQB(Enterprise $enterprise, Partner $partner): QueryBuilder
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('s.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
            ->andWhere('s.partner = :partner')
            ->setParameter('partner', $partner)
        ;
    }

    public function getFilteredByEnterpriseAndPartnerSortedByNameQ(Enterprise $enterprise, Partner $partner): Query
    {
        return $this->getFilteredByEnterpriseAndPartnerSortedByNameQB($enterprise, $partner)->getQuery();
    }

    public function getFilteredByEnterpriseAndPartnerSortedByName(Enterprise $enterprise, Partner $partner): array
    {
        return $this->getFilteredByEnterpriseAndPartnerSortedByNameQ($enterprise, $partner)->getResult();
    }

    public function getAllDeliveryNoteIdsByEnterprise(Enterprise $enterprise): array
    {
        return $this->getEnabledSortedByNameQB()
            ->join('s.partner', 'p')
            ->andWhere('p.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
            ->select('s.id')
            ->distinct()
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getDeliveryNotesFilteredByParameters($partnerId = null, $fromDate = null, $toDate = null, $deliveryNoteNumber = null, $buildingSiteId = null, $orderId = null)
    {
        $queryBuilder = $this->getEnabledSortedByNameQB()
            ->andWhere('s.isInvoiced = false')
            ->andWhere('s.wontBeInvoiced = false')
            ->andWhere('s.saleInvoice IS NULL')
        ;
        if ($partnerId) {
            $queryBuilder
                ->join('s.partner', 'p')
                ->andWhere('p.id = :partnerId')
                ->setParameter('partnerId', $partnerId)
            ;
        }
        if ($fromDate) {
            $queryBuilder
                ->andWhere('s.date >= :fromDate')
                ->setParameter('fromDate', $fromDate->format('Y-m-d'))
            ;
        }
        if ($toDate) {
            $queryBuilder
                ->andWhere('s.date <= :toDate')
                ->setParameter('toDate', $toDate->format('Y-m-d'))
            ;
        }
        if ($deliveryNoteNumber) {
            $queryBuilder
                ->andWhere('s.id = :deliveryNoteNumber')
                ->setParameter('deliveryNoteNumber', $deliveryNoteNumber)
            ;
        }
        if ($buildingSiteId) {
            $queryBuilder
                ->join('s.buildingSite', 'bs')
                ->andWhere('bs.id = :buildingSiteId')
                ->setParameter('buildingSiteId', $buildingSiteId)
            ;
        }
        if ($orderId) {
            $queryBuilder
                ->join('s.order', 'po')
                ->andWhere('po.id = :orderId')
                ->setParameter('orderId', $orderId)
            ;
        }

        return $queryBuilder
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getOldestYearFilteredByEnterprise(Enterprise $enterprise)
    {
        return $this->getFilteredByEnterpriseSortedByNameQB($enterprise)
            ->select('min(year(s.date)), year(s.date) as year')
            ->groupBy('year')
            ->orderBy('year')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
