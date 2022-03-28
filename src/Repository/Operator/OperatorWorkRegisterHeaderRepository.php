<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorWorkRegisterHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OperatorWorkRegisterHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorWorkRegisterHeader::class);
    }

    public function getHoursFromOperatorWorkRegistersWithHoursFromDeliveryNotesAndDateQB(OperatorWorkRegisterHeader $operatorWorkRegisterHeader): array
    {
        return $this->createQueryBuilder('owrh')
            ->join('owrh.operatorWorkRegisters', 'owr')
            ->andWhere('owrh.id = :id')
            ->setParameter('id', $operatorWorkRegisterHeader->getId())
            ->select('SUM(owr.units) as hours, owr.description as description')
            ->groupBy('owr.description')
            ->getQuery()
            ->getResult();
    }
}
