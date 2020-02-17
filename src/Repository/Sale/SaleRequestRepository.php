<?php

namespace App\Repository\Sale;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleRequest;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Class SaleRequestRepository.
 *
 * @category    Repository
 */
class SaleRequestRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SaleRequest::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getEnabledSortedByRequestDateQB()
    {
        return $this->createQueryBuilder('s')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('s.requestDate', 'DESC')
        ;
    }

    /**
     * @return Query
     */
    public function getEnabledSortedByRequestDateQ()
    {
        return $this->getEnabledSortedByRequestDateQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getEnabledSortedByRequestDate()
    {
        return $this->getEnabledSortedByRequestDateQ()->getResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     */
    public function getFilteredByEnterpriseEnabledSortedByRequestDateQB(Enterprise $enterprise)
    {
        return $this->getEnabledSortedByRequestDateQB()
            ->andWhere('s.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
        ;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Query
     */
    public function getFilteredByEnterpriseEnabledSortedByRequestDateQ(Enterprise $enterprise)
    {
        return $this->getFilteredByEnterpriseEnabledSortedByRequestDateQB($enterprise)->getQuery();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return array
     */
    public function getFilteredByEnterpriseEnabledSortedByRequestDate(Enterprise $enterprise)
    {
        return $this->getFilteredByEnterpriseEnabledSortedByRequestDateQ($enterprise)->getResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getTodayFilteredByEnterpriseEnabledSortedByServiceDateQB(Enterprise $enterprise)
    {
        $moment = new DateTimeImmutable();

        return $this->commonGetTimeFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise, $moment);
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Query
     *
     * @throws Exception
     */
    public function getTodayFilteredByEnterpriseEnabledSortedByServiceDateQ(Enterprise $enterprise)
    {
        return $this->getTodayFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise)->getQuery();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return array
     *
     * @throws Exception
     */
    public function getTodayFilteredByEnterpriseEnabledSortedByServiceDate(Enterprise $enterprise)
    {
        return $this->getTodayFilteredByEnterpriseEnabledSortedByServiceDateQ($enterprise)->getResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getTomorrowFilteredByEnterpriseEnabledSortedByServiceDateQB(Enterprise $enterprise)
    {
        $moment = new DateTimeImmutable('tomorrow');

        return $this->commonGetTimeFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise, $moment);
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Query
     *
     * @throws Exception
     */
    public function getTomorrowFilteredByEnterpriseEnabledSortedByServiceDateQ(Enterprise $enterprise)
    {
        return $this->getTomorrowFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise)->getQuery();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return array
     *
     * @throws Exception
     */
    public function getTomorrowFilteredByEnterpriseEnabledSortedByServiceDate(Enterprise $enterprise)
    {
        return $this->getTomorrowFilteredByEnterpriseEnabledSortedByServiceDateQ($enterprise)->getResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getNextFilteredByEnterpriseEnabledSortedByServiceDateQB(Enterprise $enterprise)
    {
        $moment = new DateTimeImmutable('tomorrow');

        return $this->getFilteredByEnterpriseEnabledSortedByRequestDateQB($enterprise)
            ->andWhere('DATE(s.serviceDate) > DATE(:moment)')
            ->setParameter('moment', $moment)
            ->addOrderBy('s.serviceDate', 'ASC')
            ->addOrderBy('s.serviceTime', 'ASC')
        ;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Query
     *
     * @throws Exception
     */
    public function getNextFilteredByEnterpriseEnabledSortedByServiceDateQ(Enterprise $enterprise)
    {
        return $this->getNextFilteredByEnterpriseEnabledSortedByServiceDateQB($enterprise)->getQuery();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return array
     *
     * @throws Exception
     */
    public function getNextFilteredByEnterpriseEnabledSortedByServiceDate(Enterprise $enterprise)
    {
        return $this->getNextFilteredByEnterpriseEnabledSortedByServiceDateQ($enterprise)->getResult();
    }

    /**
     * @param Enterprise        $enterprise
     * @param DateTimeInterface $moment
     *
     * @return QueryBuilder
     */
    private function commonGetTimeFilteredByEnterpriseEnabledSortedByServiceDateQB(Enterprise $enterprise, DateTimeInterface $moment)
    {
        return $this->getFilteredByEnterpriseEnabledSortedByRequestDateQB($enterprise)
            ->andWhere('DATE(s.serviceDate) = DATE(:moment)')
            ->setParameter('moment', $moment)
            ->addOrderBy('s.serviceTime', 'ASC')
        ;
    }
}
