<?php

namespace App\Repository\Vehicle;

use App\Entity\Vehicle\VehicleDigitalTachograph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VehicleDigitalTachographRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleDigitalTachograph::class);
    }
}
