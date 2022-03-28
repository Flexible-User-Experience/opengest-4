<?php

namespace App\Repository\Sale;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Partner\Partner;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Sale\SaleTariff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SaleTariffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleTariff::class);
    }

    public function getEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('st')
            ->where('st.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('st.enterprise', 'ASC')
            ->addOrderBy('st.year', 'DESC')
            ->addOrderBy('st.tonnage', 'DESC')
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

    public function getFilteredByEnterpriseEnabledSortedByNameQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('st.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
        ;
    }

    public function getFilteredByEnterpriseEnabledSortedByNameQ(Enterprise $enterprise): Query
    {
        return $this->getFilteredByEnterpriseEnabledSortedByNameQB($enterprise)->getQuery();
    }

    public function getFilteredByEnterpriseEnabledSortedByName(Enterprise $enterpise): array
    {
        return $this->getFilteredByEnterpriseEnabledSortedByNameQ($enterpise)->getResult();
    }

    public function getFilteredByPartnerAndSaleServiceTariffSortedByDate(Partner $partner, SaleServiceTariff $saleServiceTariff)
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('st.partner = :partner')
            ->andWhere('st.saleServiceTariff = :saleServiceTariff')
            ->setParameter('partner', $partner)
            ->setParameter('saleServiceTariff', $saleServiceTariff)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getFilteredBySaleServiceTariffWithoutPartnerSortedByDate(SaleServiceTariff $saleServiceTariff)
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('st.partner is NULL')
            ->andWhere('st.saleServiceTariff = :saleServiceTariff')
            ->setParameter('saleServiceTariff', $saleServiceTariff)
            ->getQuery()
            ->getResult()
            ;
    }
}
