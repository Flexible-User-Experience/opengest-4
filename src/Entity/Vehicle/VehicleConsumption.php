<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class VehicleConsumption.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vehicle\VehicleConsumptionRepository")
 * @ORM\Table(name="vehicle_consumption")
 */
class VehicleConsumption extends AbstractBase
{
    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $supplyDate;

    /**
     * @var ?DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTime $supplyTime;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $supplyCode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle", inversedBy="vehicleConsumptions")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id", nullable=false)
     */
    private Vehicle $vehicle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\VehicleFuel", inversedBy="vehicleConsumptions")
     */
    private ?VehicleFuel $vehicleFuel;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $amount = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $quantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceUnit = 0;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $fuelType;

    /**
     * Methods.
     */
    public function getSupplyDate(): DateTime
    {
        return $this->supplyDate;
    }

    public function setSupplyDate(DateTime $supplyDate): VehicleConsumption
    {
        $this->supplyDate = $supplyDate;

        return $this;
    }

    public function getSupplyTime(): ?DateTime
    {
        return $this->supplyTime;
    }

    public function setSupplyTime(?DateTime $supplyTime): VehicleConsumption
    {
        $this->supplyTime = $supplyTime;

        return $this;
    }

    public function getSupplyCode(): ?string
    {
        return $this->supplyCode;
    }

    public function setSupplyCode(?string $supplyCode): VehicleConsumption
    {
        $this->supplyCode = $supplyCode;

        return $this;
    }

    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle $vehicle): VehicleConsumption
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): VehicleConsumption
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): VehicleConsumption
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceUnit(): float
    {
        return $this->priceUnit;
    }

    public function setPriceUnit(float $priceUnit): VehicleConsumption
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function setFuelType(?string $fuelType): VehicleConsumption
    {
        $this->fuelType = $fuelType;

        return $this;
    }

    /**
     * @return VehicleFuel
     */
    public function getVehicleFuel(): VehicleFuel
    {
        return $this->vehicleFuel;
    }

    public function setVehicleFuel(VehicleFuel $vehicleFuel): VehicleConsumption
    {
        $this->vehicleFuel = $vehicleFuel;

        return $this;
    }

    /**
     * Custom methods.
     */
    public function getSupplyDateFormatted(): string
    {
        return $this->getSupplyDate()->format('d/m/y');
    }

    public function __toString(): string
    {
        return $this->getSupplyDate()->format('d/m/y').' - '.$this->getVehicle()->getVehicleRegistrationNumber();
    }
}
