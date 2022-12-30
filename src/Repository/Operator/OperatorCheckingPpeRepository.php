<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorCheckingPpe;
use Doctrine\Persistence\ManagerRegistry;

class OperatorCheckingPpeRepository extends OperatorCheckingBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorCheckingPpe::class);
    }
}
