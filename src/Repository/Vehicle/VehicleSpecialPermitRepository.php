<?php

namespace App\Repository\Vehicle;

use App\Entity\Vehicle\VehicleSpecialPermit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class VehicleSpecialPermitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleSpecialPermit::class);
    }

    public function getVehicleSpecialPermitsSortedByIdQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.id');
    }

    public function getVehicleSpecialPermitsSortedByIdQ(): Query
    {
        return $this->getVehicleSpecialPermitsSortedByIdQB()->getQuery();
    }

    public function getVehicleSpecialPermitsSortedById(): array
    {
        return $this->getVehicleSpecialPermitsSortedByIdQ()->getResult();
    }
}
