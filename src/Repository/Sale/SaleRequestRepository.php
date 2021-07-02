<?php

namespace App\Repository\Sale;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleRequest;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class SaleRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleRequest::class);
    }

    public function getEnabledSortedByRequestDateQB(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('s.requestDate', 'DESC')
        ;
    }

    public function getEnabledSortedByRequestDateQ(): Query
    {
        return $this->getEnabledSortedByRequestDateQB()->getQuery();
    }

    public function getEnabledSortedByRequestDate(): array
    {
        return $this->getEnabledSortedByRequestDateQ()->getResult();
    }

    public function getFilteredByEnterpriseEnabledSortedByRequestDateQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->getEnabledSortedByRequestDateQB()
            ->andWhere('s.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
        ;
    }

    public function getFilteredByEnterpriseEnabledSortedByRequestDateQ(Enterprise $enterprise): Query
    {
        return $this->getFilteredByEnterpriseEnabledSortedByRequestDateQB($enterprise)->getQuery();
    }

    public function getFilteredByEnterpriseEnabledSortedByRequestDate(Enterprise $enterprise): array
    {
        return $this->getFilteredByEnterpriseEnabledSortedByRequestDateQ($enterprise)->getResult();
    }

    public function getTodayFilteredByEnterpriseEnabledSortedByServiceDateQB(Enterprise $enterprise): QueryBuilder
    {
        $moment = new DateTimeImmutable();

        return $this->commonGetTimeFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise, $moment);
    }

    public function getTodayFilteredByEnterpriseEnabledSortedByServiceDateQ(Enterprise $enterprise): Query
    {
        return $this->getTodayFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise)->getQuery();
    }

    public function getTodayFilteredByEnterpriseEnabledSortedByServiceDate(Enterprise $enterprise): array
    {
        return $this->getTodayFilteredByEnterpriseEnabledSortedByServiceDateQ($enterprise)->getResult();
    }

    public function getTomorrowFilteredByEnterpriseEnabledSortedByServiceDateQB(Enterprise $enterprise): QueryBuilder
    {
        $moment = new DateTimeImmutable('tomorrow');

        return $this->commonGetTimeFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise, $moment);
    }

    public function getTomorrowFilteredByEnterpriseEnabledSortedByServiceDateQ(Enterprise $enterprise): Query
    {
        return $this->getTomorrowFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise)->getQuery();
    }

    public function getTomorrowFilteredByEnterpriseEnabledSortedByServiceDate(Enterprise $enterprise): array
    {
        return $this->getTomorrowFilteredByEnterpriseEnabledSortedByServiceDateQ($enterprise)->getResult();
    }

    public function getNextFilteredByEnterpriseEnabledSortedByServiceDateQB(Enterprise $enterprise): QueryBuilder
    {
        $moment = new DateTimeImmutable('tomorrow');

        return $this->getFilteredByEnterpriseEnabledSortedByRequestDateQB($enterprise)
            ->andWhere('DATE(s.serviceDate) > DATE(:moment)')
            ->setParameter('moment', $moment)
            ->addOrderBy('s.serviceDate', 'ASC')
            ->addOrderBy('s.serviceTime', 'ASC')
        ;
    }

    public function getNextFilteredByEnterpriseEnabledSortedByServiceDateQ(Enterprise $enterprise): Query
    {
        return $this->getNextFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise)->getQuery();
    }

    public function getNextFilteredByEnterpriseEnabledSortedByServiceDate(Enterprise $enterprise): array
    {
        return $this->getNextFilteredByEnterpriseEnabledSortedByServiceDateQ($enterprise)->getResult();
    }

    private function commonGetTimeFilteredByEnterpriseEnabledSortedByServiceDateQB(Enterprise $enterprise, DateTimeInterface $moment): QueryBuilder
    {
        return $this->getFilteredByEnterpriseEnabledSortedByRequestDateQB($enterprise)
            ->andWhere('DATE(s.serviceDate) = DATE(:moment)')
            ->setParameter('moment', $moment)
            ->addOrderBy('s.serviceTime', 'ASC')
        ;
    }

    public function getFilteredByStatusFilteredByEnterpriseEnabledSortedByServiceDateQB(Enterprise $enterprise, $status): QueryBuilder
    {
        return $this->getFilteredByEnterpriseEnabledSortedByRequestDateQB($enterprise)
            ->andWhere('s.status = :status')
            ->setParameter('status', $status)
        ;
    }

    public function getFilteredByStatusFilteredByEnterpriseEnabledSortedByServiceDateQ(Enterprise $enterprise, $status): Query
    {
        return $this->getFilteredByStatusFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise, $status)->getQuery();
    }

    public function getFilteredByStatusFilteredByEnterpriseEnabledSortedByServiceDate(Enterprise $enterprise, $status): array
    {
        return $this->getFilteredByStatusFilteredByEnterpriseEnabledSortedByServiceDateQ($enterprise, $status)->getResult();
    }
}
