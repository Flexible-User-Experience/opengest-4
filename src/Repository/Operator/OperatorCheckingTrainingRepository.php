<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorCheckingTraining;
use Doctrine\Persistence\ManagerRegistry;

class OperatorCheckingTrainingRepository extends OperatorCheckingBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorCheckingTraining::class);
    }
}
