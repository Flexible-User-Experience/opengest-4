<?php

namespace App\Repository\Vehicle;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\VehicleChecking;
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
 * Class VehicleCheckingRepository.
 *
 * @category Repository
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class VehicleCheckingRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VehicleChecking::class);
    }

    /**
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getItemsInvalidByEnabledVehicleQB()
    {
        $today = new DateTimeImmutable();

        return $this->createQueryBuilder('vc')
            ->join('vc.vehicle', 'v')
            ->where('vc.end = :today')
            ->andWhere('v.enabled = :enabled')
            ->setParameter('today', $today->format('Y-m-d'))
            ->setParameter('enabled', true)
        ;
    }

    /**
     * @return Query
     *
     * @throws Exception
     */
    public function getItemsInvalidByEnabledVehicleQ()
    {
        return $this->getItemsInvalidByEnabledVehicleQB()->getQuery();
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getItemsInvalidByEnabledVehicle()
    {
        return $this->getItemsInvalidByEnabledVehicleQ()->getResult();
    }

    /**
     * @return QueryBuilder
     *
     * @throws Exception
     */
    public function getItemsBeforeToBeInvalidByEnabledVehicleQB()
    {
        $thresholdDay = new DateTime();
        $thresholdDay->add(new DateInterval('P30D'));

        return $this->createQueryBuilder('vc')
            ->join('vc.vehicle', 'v')
            ->where('vc.end = :thresholdDay')
            ->andWhere('v.enabled = :enabled')
            ->setParameter('thresholdDay', $thresholdDay->format('Y-m-d'))
            ->setParameter('enabled', true)
        ;
    }

    /**
     * @return Query
     *
     * @throws Exception
     */
    public function getItemsBeforeToBeInvalidByEnabledVehicleQ()
    {
        return $this->getItemsBeforeToBeInvalidByEnabledVehicleQB()->getQuery();
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getItemsBeforeToBeInvalidByEnabledVehicle()
    {
        return $this->getItemsBeforeToBeInvalidByEnabledVehicleQ()->getResult();
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

        return $this->createQueryBuilder('vc')
            ->join('vc.vehicle', 'v')
            ->select('COUNT(vc.id)')
            ->where('vc.end <= :today')
            ->andWhere('v.enterprise = :enterprise')
            ->andWhere('v.enabled = :enabled')
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

        return $this->createQueryBuilder('vc')
            ->join('vc.vehicle', 'v')
            ->select('COUNT(vc.id)')
            ->where('vc.end <= :thresholdDay')
            ->andWhere('vc.end > :today')
            ->andWhere('v.enterprise = :enterprise')
            ->andWhere('v.enabled = :enabled')
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
}
