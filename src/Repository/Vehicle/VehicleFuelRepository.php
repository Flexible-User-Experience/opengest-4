<?php

namespace App\Repository\Vehicle;

use App\Entity\Vehicle\VehicleFuel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class VehicleFuelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleFuel::class);
    }

    public function getVehicleFuelsSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.name');
    }

    public function getVehicleFuelsSortedByNameQ(): Query
    {
        return $this->getVehicleFuelsSortedByNameQB()->getQuery();
    }

    public function getVehicleFuelsSortedByName(): array
    {
        return $this->getVehicleFuelsSortedByNameQ()->getResult();
    }
}
