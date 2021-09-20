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
}
