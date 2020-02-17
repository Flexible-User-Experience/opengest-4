<?php

namespace App\Repository\Setting;

use App\Entity\Setting\Province;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class ProvinceRepository.
 *
 * @category Repository
 *
 * @author Wils Iglesias <wiglesias83@gmail.com>
 */
class ProvinceRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Province::class);
    }
}
