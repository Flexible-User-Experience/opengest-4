<?php

namespace App\Repository\Vehicle;

use App\Entity\Vehicle\VehicleDigitalTachograph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class WorkRepository.
 *
 * @category Repository
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
class VehicleDigitalTachographRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VehicleDigitalTachograph::class);
    }
}
