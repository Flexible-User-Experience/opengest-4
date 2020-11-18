<?php

namespace App\Repository\Sale;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Setting\SaleInvoiceSeries;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class SaleInvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleInvoice::class);
    }

    public function getEnabledSortedByDateQB(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('s.date', 'DESC')
        ;
    }

    public function gettEnabledSortedByDateQ(): Query
    {
        return $this->getEnabledSortedByDateQB()->getQuery();
    }

    public function getEnabledSortedByDate(): array
    {
        return $this->gettEnabledSortedByDateQ()->getResult();
    }

    public function getFilteredByEnterpriseSortedByDateQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->getEnabledSortedByDateQB()
            ->join('s.partner', 'p')
            ->andWhere('p.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
        ;
    }

    public function getLastInvoiceBySerieAndEnterpriseQB(SaleInvoiceSeries $saleInvoiceSeries, Enterprise $enterprise): QueryBuilder
    {
        return $this->getFilteredByEnterpriseSortedByDateQB($enterprise)
            ->andWhere('s.series = :serie')
            ->setParameter('serie', $saleInvoiceSeries)
            ->orderBy('s.invoiceNumber', 'DESC')
            ->setMaxResults(1)
        ;
    }

    public function getLastInvoiceBySerieAndEnterpriseQ(SaleInvoiceSeries $saleInvoiceSeries, Enterprise $enterprise): Query
    {
        return $this->getLastInvoiceBySerieAndEnterpriseQB($saleInvoiceSeries, $enterprise)->getQuery();
    }

    public function getLastInvoiceBySerieAndEnterprise(SaleInvoiceSeries $saleInvoiceSeries, Enterprise $enterprise): ?SaleInvoice
    {
        try {
            $result = $this->getLastInvoiceBySerieAndEnterpriseQ($saleInvoiceSeries, $enterprise)->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $result = null;
        }

        return $result;
    }
}
