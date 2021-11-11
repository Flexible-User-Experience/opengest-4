<?php

namespace App\Repository\Vehicle;

use App\Entity\Vehicle\VehicleMaintenanceTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class VehicleMaintenanceTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleMaintenanceTask::class);
    }

    public function getVehicleMaintenanceTasksSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.name');
    }

    public function getVehicleMaintenanceTasksSortedByNameQ(): Query
    {
        return $this->getVehicleMaintenanceTasksSortedByNameQB()->getQuery();
    }

    public function getVehicleMaintenanceTasksSortedByName(): array
    {
        return $this->getVehicleMaintenanceTasksSortedByNameQ()->getResult();
    }
}
