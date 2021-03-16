<?php

namespace App\Repository\Vehicle;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\VehicleChecking;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class VehicleCheckingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleChecking::class);
    }

    public function getItemsInvalidByEnabledVehicleQB(): QueryBuilder
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

    public function getItemsInvalidByEnabledVehicleQ(): Query
    {
        return $this->getItemsInvalidByEnabledVehicleQB()->getQuery();
    }

    public function getItemsInvalidByEnabledVehicle(): array
    {
        return $this->getItemsInvalidByEnabledVehicleQ()->getResult();
    }

    public function getItemsBeforeToBeInvalidByEnabledVehicleQB(): QueryBuilder
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

    public function getItemsBeforeToBeInvalidByEnabledVehicleQ(): Query
    {
        return $this->getItemsBeforeToBeInvalidByEnabledVehicleQB()->getQuery();
    }

    public function getItemsBeforeToBeInvalidByEnabledVehicle(): array
    {
        return $this->getItemsBeforeToBeInvalidByEnabledVehicleQ()->getResult();
    }

    public function getItemsInvalidSinceTodayByEnterpriseAmountQB(Enterprise $enterprise): QueryBuilder
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

    public function getItemsInvalidSinceTodayByEnterpriseAmountQ(Enterprise $enterprise): Query
    {
        return $this->getItemsInvalidSinceTodayByEnterpriseAmountQB($enterprise)->getQuery();
    }

    public function getItemsInvalidSinceTodayByEnterpriseAmount(Enterprise $enterprise): int
    {
        try {
            $result = $this->getItemsInvalidSinceTodayByEnterpriseAmountQ($enterprise)->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        } catch (NonUniqueResultException $e) {
            $result = 0;
        }

        return $result;
    }

    public function getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmountQB(Enterprise $enterprise): QueryBuilder
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

    public function getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmountQ(Enterprise $enterprise): Query
    {
        return $this->getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmountQB($enterprise)->getQuery();
    }

    public function getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmount(Enterprise $enterprise): int
    {
        try {
            $result = $this->getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmountQ($enterprise)->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        } catch (NonUniqueResultException $e) {
            $result = 0;
        }

        return $result;
    }
}
