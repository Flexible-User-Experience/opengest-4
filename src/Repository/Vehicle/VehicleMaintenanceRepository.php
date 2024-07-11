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
    public function getEnabledActiveVehicleMaintenancesSortedById(bool $needsCheck = true): array
    {
        return $this
            ->getVehicleMaintenancesSortedByIdQB()
            ->join('c.vehicle', 'v')
            ->andWhere('v.enabled = true')
            ->andWhere('c.enabled = :enabled')
            ->andWhere('c.needsCheck = :needsCheck')
            ->setParameter('enabled', true)
            ->setParameter('needsCheck', $needsCheck)
            ->getQuery()
            ->getResult()
        ;
    }
}
