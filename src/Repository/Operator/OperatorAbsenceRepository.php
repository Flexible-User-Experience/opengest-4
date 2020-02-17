<?php

namespace App\Repository\Operator;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\OperatorAbsence;
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
 * Class OperatorAbsenceRepository.
 *
 * @category Repository
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class OperatorAbsenceRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OperatorAbsence::class);
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getItemsAbsenceTodayByEnterpriseAmountQB(Enterprise $enterprise)
    {
        $today = new DateTimeImmutable();

        return $this->createQueryBuilder('oa')
            ->join('oa.operator', 'o')
            ->select('COUNT(oa.id)')
            ->where('oa.begin <= :today')
            ->andWhere('oa.end >= :today')
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
    public function getItemsAbsenceTodayByEnterpriseAmountQ(Enterprise $enterprise)
    {
        return $this->getItemsAbsenceTodayByEnterpriseAmountQB($enterprise)->getQuery();
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
    public function getItemsAbsenceTodayByEnterpriseAmount(Enterprise $enterprise)
    {
        return $this->getItemsAbsenceTodayByEnterpriseAmountQ($enterprise)->getSingleScalarResult();
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getItemsToBeAbsenceTomorrowByEnterpriseAmountQB(Enterprise $enterprise)
    {
        $tomorrow = new DateTime();
        $tomorrow->add(new DateInterval('P1D'));

        return $this->createQueryBuilder('oa')
            ->join('oa.operator', 'o')
            ->select('COUNT(oa.id)')
            ->where('oa.begin = :tomorrow')
            ->andWhere('o.enterprise = :enterprise')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('tomorrow', $tomorrow->format('Y-m-d'))
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
    public function getItemsToBeAbsenceTomorrowByEnterpriseAmountQ(Enterprise $enterprise)
    {
        return $this->getItemsToBeAbsenceTomorrowByEnterpriseAmountQB($enterprise)->getQuery();
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
    public function getItemsToBeAbsenceTomorrowByEnterpriseAmount(Enterprise $enterprise)
    {
        return $this->getItemsToBeAbsenceTomorrowByEnterpriseAmountQ($enterprise)->getSingleScalarResult();
    }
}
