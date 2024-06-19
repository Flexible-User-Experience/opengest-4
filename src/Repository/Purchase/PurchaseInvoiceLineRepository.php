<?php

namespace App\Repository\Purchase;

use App\Entity\Operator\Operator;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Vehicle\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseInvoiceLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseInvoiceLine::class);
    }

    public function getEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('pil')
            ->where('pil.enabled = :enabled')
            ->setParameter('enabled', true)
        ;
    }

    public function getEnabledSortedByNameB(): Query
    {
        return $this->getEnabledSortedByNameQB()->getQuery();
    }

    public function getEnabledSortedByName(): array
    {
        return $this->getEnabledSortedByNameB()->getResult();
    }

    public function getEnabledFilteredByYearQB(int $year): QueryBuilder
    {
        return $this->getEnabledSortedByNameQB()
            ->join('pil.purchaseInvoice', 'pi')
            ->andWhere('year(pi.date) = :year')
            ->setParameter('year', $year)
        ;
    }

    public function getFilteredByYear(int $year)
    {
        return $this->getEnabledFilteredByYearQB($year)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getCostCenters(SaleDeliveryNote $saleDeliveryNote = null, Operator $operator = null, Vehicle $vehicle = null)
    {
        $queryBuilder = $this->createQueryBuilder('pil')
            ->select('costCenter.id, costCenter.code, costCenter.name, costCenter.showInLogBook, costCenter.orderInLogBook')
            ->innerJoin('pil.costCenter', 'costCenter')
            ->orderBy('costCenter.orderInLogBook', 'ASC')
            ->groupBy('pil.costCenter')
        ;

        if ($saleDeliveryNote) {
            $queryBuilder
                ->where('pil.saleDeliveryNote = :saleDeliveryNote')
                ->setParameter('saleDeliveryNote', $saleDeliveryNote)
            ;
        }
        if ($operator) {
            $queryBuilder
                ->where('pil.operator = :operator')
                ->setParameter('operator', $operator)
            ;
        }
        if ($vehicle) {
            $queryBuilder
                ->where('pil.vehicle = :vehicle')
                ->setParameter('vehicle', $vehicle)
            ;
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }
}
