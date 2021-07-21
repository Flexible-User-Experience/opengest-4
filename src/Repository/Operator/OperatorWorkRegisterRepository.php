<?php

namespace App\Repository\Operator;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\OperatorAbsence;
use App\Entity\Operator\OperatorWorkRegister;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class OperatorWorkRegisterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperatorWorkRegister::class);
    }
}
