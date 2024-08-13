<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class VehicleCheckingType.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
#[ORM\Table(name: 'vehicle_checking_type')]
#[ORM\Entity(repositoryClass: \App\Repository\Vehicle\VehicleCheckingTypeRepository::class)]
class VehicleCheckingType extends AbstractBase
{
    use NameTrait;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
