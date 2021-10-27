<?php

namespace App\Repository\Vehicle;

use App\Entity\Vehicle\VehicleConsumption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class VehicleConsumptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleConsumption::class);
    }

    public function getVehicleConsumptionsSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.supplyCode');
    }

    public function getVehicleConsumptionsSortedByNameQ(): Query
    {
        return $this->getVehicleConsumptionsSortedByNameQB()->getQuery();
    }

    public function getVehicleConsumptionsSortedByName(): array
    {
        return $this->getVehicleConsumptionsSortedByNameQ()->getResult();
    }
}
