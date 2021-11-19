<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class VehicleFuel.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vehicle\VehicleFuelRepository")
 * @ORM\Table(name="vehicle_fuel")
 */
class VehicleFuel extends AbstractBase
{
    use NameTrait;

    public function __toString(): string
    {
        return $this->getName();
    }
}
