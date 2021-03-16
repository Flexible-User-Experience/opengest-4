<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorDigitalTachograph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OperatorDigitalTachographRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorDigitalTachograph::class);
    }
}
