<?php

namespace App\Repository\Operator;

use App\Entity\Operator\OperatorDigitalTachograph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class WorkRepository.
 *
 * @category Repository
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
class OperatorDigitalTachographRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OperatorDigitalTachograph::class);
    }
}
