<?php

namespace App\Repository\Operator;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\OperatorChecking;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Class OperatorCheckingRepository.
 *
 * @category Repository
 *
 * @author   Wils Iglesias
 */
class OperatorCheckingRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OperatorChecking::class);
    }

    /**
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getItemsBeforeToBeInvalidByEnabledOperatorQB()
    {
        $thresholdDay = new DateTime();
        $thresholdDay->add(new DateInterval('P30D'));

        return $this->createQueryBuilder('oc')
            ->join('oc.operator', 'o')
            ->where('oc.end = :thresholdDay')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('thresholdDay', $thresholdDay->format('Y-m-d'))
            ->setParameter('enabled', true)
        ;
    }

    /**
     * @return Query
     *
     * @throws Exception
     */
    public function getItemsBeforeToBeInvalidByEnabledOperatorQ()
    {
        return $this->getItemsBeforeToBeInvalidByEnabledOperatorQB()->getQuery();
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getItemsBeforeToBeInvalidByEnabledOperator()
    {
        return $this->getItemsBeforeToBeInvalidByEnabledOperatorQ()->getResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmountQB(Enterprise $enterprise)
    {
        $thresholdDay = new DateTime();
        $thresholdDay->add(new DateInterval('P30D'));
        $today = new DateTimeImmutable();

        return $this->createQueryBuilder('oc')
            ->join('oc.operator', 'o')
            ->select('COUNT(oc.id)')
            ->where('oc.end <= :thresholdDay')
            ->andWhere('oc.end > :today')
            ->andWhere('o.enterprise = :enterprise')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('thresholdDay', $thresholdDay->format('Y-m-d'))
            ->setParameter('today', $today->format('Y-m-d'))
            ->setParameter('enterprise', $enterprise)
            ->setParameter('enabled', true)
        ;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Query
     *
     * @throws Exception
     */
    public function getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmountQ(Enterprise $enterprise)
    {
        return $this->getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmountQB($enterprise)->getQuery();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmount(Enterprise $enterprise)
    {
        return $this->getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmountQ($enterprise)->getSingleScalarResult();
    }

    /**
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getItemsInvalidByEnabledOperatorQB()
    {
        $today = new DateTimeImmutable();

        return $this->createQueryBuilder('oc')
            ->join('oc.operator', 'o')
            ->where('oc.end = :today')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('today', $today->format('Y-m-d'))
            ->setParameter('enabled', true)
        ;
    }

    /**
     * @return Query
     *
     * @throws Exception
     */
    public function getItemsInvalidByEnabledOperatorQ()
    {
        return $this->getItemsInvalidByEnabledOperatorQB()->getQuery();
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getItemsInvalidByEnabledOperator()
    {
        return $this->getItemsInvalidByEnabledOperatorQ()->getResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getItemsInvalidSinceTodayByEnterpriseAmountQB(Enterprise $enterprise)
    {
        $today = new DateTimeImmutable();

        return $this->createQueryBuilder('oc')
            ->join('oc.operator', 'o')
            ->select('COUNT(oc.id)')
            ->where('oc.end <= :today')
            ->andWhere('o.enterprise = :enterprise')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('today', $today->format('Y-m-d'))
            ->setParameter('enterprise', $enterprise)
            ->setParameter('enabled', true)
        ;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Query
     *
     * @throws Exception
     */
    public function getItemsInvalidSinceTodayByEnterpriseAmountQ(Enterprise $enterprise)
    {
        return $this->getItemsInvalidSinceTodayByEnterpriseAmountQB($enterprise)->getQuery();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getItemsInvalidSinceTodayByEnterpriseAmount(Enterprise $enterprise)
    {
        return $this->getItemsInvalidSinceTodayByEnterpriseAmountQ($enterprise)->getSingleScalarResult();
    }
}
