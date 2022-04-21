<?php

namespace App\Repository\Operator;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorAbsence;
use App\Manager\EnterpriseHolidayManager;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class OperatorAbsenceRepository extends ServiceEntityRepository
{
    private EnterpriseHolidayManager $enterpriseHolidayManager;

    public function __construct(ManagerRegistry $registry, EnterpriseHolidayManager $enterpriseHolidayManager)
    {
        parent::__construct($registry, OperatorAbsence::class);
        $this->enterpriseHolidayManager = $enterpriseHolidayManager;
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

    public function getAbsencesFilteredByOperator(Operator $operator)
    {
        $operatorAbsences = $this->createQueryBuilder('oa')
            ->where('oa.operator = :operator')
            ->andWhere('oa.enabled = :enabled')
            ->setParameter('enabled', true)
            ->setParameter('operator', $operator)
            ->getQuery()
            ->getResult();
        $date = new DateTime();
        $currentYear = $date->format('Y') * 1;
        $operatorAbsencesGrouped = [];
        /** @var OperatorAbsence $operatorAbsence */
        foreach ($operatorAbsences as $operatorAbsence) {
            $numberOfDays = ($operatorAbsence->getEnd()->getTimestamp() - $operatorAbsence->getBegin()->getTimestamp()) / (60 * 60 * 24) + 1;
            $numberOfHolidays = 0;
            $date = $operatorAbsence->getBegin();
            /** @var DateTime $date */
            while ($date->getTimestamp() <= $operatorAbsence->getEnd()->getTimestamp()) {
                if (($date->format('N') >= 6) || ($this->enterpriseHolidayManager->checkIfDayIsEnterpriseHoliday($date))) {
                    ++$numberOfHolidays;
                }
                $date->modify('+1 day');
            }
            $numberOfDays = $numberOfDays - $numberOfHolidays;
            if (
                ($operatorAbsence->getBegin()->format('Y') == $currentYear)
                &&
                (!$operatorAbsence->isToPreviousYearCount())
            ) {
                if (isset($operatorAbsencesGrouped[$operatorAbsence->getType()->getName()]['currentYear'])) {
                    $operatorAbsencesGrouped[$operatorAbsence->getType()->getName()]['currentYear'] += $numberOfDays;
                } else {
                    $operatorAbsencesGrouped[$operatorAbsence->getType()->getName()]['currentYear'] = $numberOfDays;
                }
            } elseif (
                (($operatorAbsence->getBegin()->format('Y') == $currentYear - 1))
                    &&
                    (!$operatorAbsence->isToPreviousYearCount())
                ||
                (($operatorAbsence->getBegin()->format('Y') == $currentYear)
                    &&
                    ($operatorAbsence->isToPreviousYearCount()))
            ) {
                if (isset($operatorAbsencesGrouped[$operatorAbsence->getType()->getName()]['lastYear'])) {
                    $operatorAbsencesGrouped[$operatorAbsence->getType()->getName()]['lastYear'] += $numberOfDays;
                } else {
                    $operatorAbsencesGrouped[$operatorAbsence->getType()->getName()]['lastYear'] = $numberOfDays;
                }
            }
        }

        return $operatorAbsencesGrouped;
    }
}
