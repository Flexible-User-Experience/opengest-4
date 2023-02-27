<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\Column(type="float", scale=4, nullable=true)
     */
    private null|float $priceUnit = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\VehicleConsumption", mappedBy="vehicleFuel")
     */
    private Collection $vehicleConsumptions;

    /**
     * Methods.
     */
    public function getVehicleConsumptions(): Collection
    {
        return $this->vehicleConsumptions;
    }

    public function setVehicleConsumptions(Collection $vehicleConsumptions): VehicleFuel
    {
        $this->vehicleConsumptions = $vehicleConsumptions;

        return $this;
    }

    public function getPriceUnit(): null|float
    {
        return $this->priceUnit;
    }

    public function setPriceUnit(null|float $priceUnit): VehicleFuel
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
