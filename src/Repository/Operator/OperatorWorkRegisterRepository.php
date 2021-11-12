<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorWorkRegister;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class OperatorWorkRegisterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorWorkRegister::class);
    }

    public function getHoursFromOperatorWorkRegistersWithHoursFromDeliveryNotesAndDateQB(Collection $saleDeliveryNotes, DateTime $date): QueryBuilder
    {
        $saleDeliveryNoteIds = $saleDeliveryNotes->map(function ($obj) {return $obj->getId(); })->getValues();

        return $this->createQueryBuilder('owr')
            ->join('owr.saleDeliveryNote', 'sdn')
            ->join('owr.operatorWorkRegisterHeader', 'owrh')
            ->where('owrh.date >= :date')
            ->andWhere('sdn.id IN (:sdnIds)')
            ->andWhere('owr.start is not null')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('sdnIds', $saleDeliveryNoteIds)
            ->select('SUM(owr.units) as hours')
            ;
    }

    public function getHoursFromOperatorWorkRegistersWithHoursFromDeliveryNotesAndDateQ(Collection $saleDeliveryNotes, DateTime $date): Query
    {
        return $this->getHoursFromOperatorWorkRegistersWithHoursFromDeliveryNotesAndDateQB($saleDeliveryNotes, $date)->getQuery();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getHoursFromperatorWorkRegistersWithHoursFromDeliveryNotesAndDate(Collection $saleDeliveryNotes, DateTime $date)
    {
        return $this->getHoursFromOperatorWorkRegistersWithHoursFromDeliveryNotesAndDateQ($saleDeliveryNotes, $date)->getOneOrNullResult();
    }
}
