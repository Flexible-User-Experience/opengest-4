<?php

namespace App\Repository\Operator;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\OperatorAbsence;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class OperatorAbsenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorAbsence::class);
    }

    public function getItemsAbsenceTodayByEnterpriseAmountQB(Enterprise $enterprise): QueryBuilder
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

    public function getItemsAbsenceTodayByEnterpriseAmountQ(Enterprise $enterprise): Query
    {
        return $this->getItemsAbsenceTodayByEnterpriseAmountQB($enterprise)->getQuery();
    }

    public function getItemsAbsenceTodayByEnterpriseAmount(Enterprise $enterprise): int
    {
        try {
            $result = $this->getItemsAbsenceTodayByEnterpriseAmountQ($enterprise)->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        } catch (NonUniqueResultException $e) {
            $result = 0;
        }

        return $result;
    }

    public function getItemsToBeAbsenceTomorrowByEnterpriseAmountQB(Enterprise $enterprise): QueryBuilder
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

    public function getItemsToBeAbsenceTomorrowByEnterpriseAmountQ(Enterprise $enterprise): Query
    {
        return $this->getItemsToBeAbsenceTomorrowByEnterpriseAmountQB($enterprise)->getQuery();
    }

    public function getItemsToBeAbsenceTomorrowByEnterpriseAmount(Enterprise $enterprise): int
    {
        try {
            $result = $this->getItemsToBeAbsenceTomorrowByEnterpriseAmountQ($enterprise)->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        } catch (NonUniqueResultException $e) {
            $result = 0;
        }

        return $result;
    }
}
