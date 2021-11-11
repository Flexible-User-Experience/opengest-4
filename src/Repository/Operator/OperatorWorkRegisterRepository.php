<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorWorkRegister;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class OperatorWorkRegisterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorWorkRegister::class);
    }

    public function getOperatorWorkRegistersFromDeliveryNotesAndDateQB(Collection $saleDeliveryNotes, DateTime $date): QueryBuilder
    {
        $saleDeliveryNoteIds = $saleDeliveryNotes->map(function ($obj) {return $obj->getId(); })->getValues();
        dd($saleDeliveryNoteIds);

        return $this->createQueryBuilder('owr')
            ->join('owr.saleDeliveryNote', 'sdn')
            ->join('owr.operatorWorkRegisterHeader', 'owrh')
            ->where('owrh.date >= :date')
            ->andWhere('sdn.id IN (:sdnIds)')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('sdnIds', $saleDeliveryNoteIds)
            ;
    }
}
