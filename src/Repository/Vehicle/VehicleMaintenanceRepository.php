<?php

namespace App\Repository\Vehicle;

use App\Entity\Vehicle\VehicleMaintenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class VehicleMaintenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleMaintenance::class);
    }

    public function getVehicleMaintenancesSortedByIdQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.id');
    }

    public function getVehicleMaintenancesSortedByIdQ(): Query
    {
        return $this->getVehicleMaintenancesSortedByIdQB()->getQuery();
    }

    public function getVehicleMaintenancesSortedById(): array
    {
        return $this->getVehicleMaintenancesSortedByIdQ()->getResult();
    }
}
