<?php

namespace App\Repository\Enterprise;

use App\Entity\Enterprise\EnterpriseHolidays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EnterpriseHolidaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnterpriseHolidays::class);
    }
}
