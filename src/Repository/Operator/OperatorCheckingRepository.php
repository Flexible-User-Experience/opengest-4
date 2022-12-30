<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorChecking;
use Doctrine\Persistence\ManagerRegistry;

class OperatorCheckingRepository extends OperatorCheckingBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorChecking::class);
    }
}
