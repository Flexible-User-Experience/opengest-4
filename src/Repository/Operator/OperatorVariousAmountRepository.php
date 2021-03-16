<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorVariousAmount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OperatorVariousAmountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorVariousAmount::class);
    }
}
