<?php

namespace App\Repository\Operator;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\OperatorChecking;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

abstract class OperatorCheckingBaseRepository extends ServiceEntityRepository
{
    public function getItemsBeforeToBeInvalidByEnabledOperatorQB(): QueryBuilder
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

    public function getItemsBeforeToBeInvalidByEnabledOperatorQ(): Query
    {
        return $this->getItemsBeforeToBeInvalidByEnabledOperatorQB()->getQuery();
    }

    public function getItemsBeforeToBeInvalidByEnabledOperator(): array
    {
        return $this->getItemsBeforeToBeInvalidByEnabledOperatorQ()->getResult();
    }

    public function getItemsBeforeToBeInvalidSinceTodayByEnterpriseAmountQB(Enterprise $enterprise): QueryBuilder
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

    public function getItemsInvalidByEnabledOperatorQB(): QueryBuilder
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

    public function getItemsInvalidByEnabledOperatorQ(): Query
    {
        return $this->getItemsInvalidByEnabledOperatorQB()->getQuery();
    }

    public function getItemsInvalidByEnabledOperator(): array
    {
        return $this->getItemsInvalidByEnabledOperatorQ()->getResult();
    }

    public function getItemsInvalidSinceTodayByEnterpriseAmountQB(Enterprise $enterprise): QueryBuilder
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
}
