<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use App\Entity\Traits\DescriptionTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class VehicleMaintenance.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 */
#[ORM\Table(name: 'vehicle_manteinance')]
#[ORM\Entity(repositoryClass: \App\Repository\Vehicle\VehicleMaintenanceRepository::class)]
class VehicleMaintenance extends AbstractBase
{
    use DescriptionTrait;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Vehicle\Vehicle::class, inversedBy: 'vehicleMaintenances')]
    private Vehicle $vehicle;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Vehicle\VehicleMaintenanceTask::class, inversedBy: 'vehicleMaintenances')]
    private VehicleMaintenanceTask $vehicleMaintenanceTask;

    #[ORM\Column(type: 'datetime')]
    private DateTime $date;

    #[ORM\Column(type: 'integer', nullable: true)]
    private int $km = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $needsCheck = false;

    /**
     * Methods.
     */
    public function __construct()
    {
        $this->date = new DateTime();
    }

    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle $vehicle): VehicleMaintenance
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getVehicleMaintenanceTask(): VehicleMaintenanceTask
    {
        return $this->vehicleMaintenanceTask;
    }

    public function setVehicleMaintenanceTask(VehicleMaintenanceTask $vehicleMaintenanceTask): VehicleMaintenance
    {
        $this->vehicleMaintenanceTask = $vehicleMaintenanceTask;

        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): VehicleMaintenance
    {
        $this->date = $date;

        return $this;
    }

    public function getKm(): int
    {
        return $this->km;
    }

    public function setKm(int $km): VehicleMaintenance
    {
        $this->km = $km;

        return $this;
    }

    public function isNeedsCheck(): bool
    {
        return $this->needsCheck;
    }

    public function setNeedsCheck(bool $needsCheck): VehicleMaintenance
    {
        $this->needsCheck = $needsCheck;

        return $this;
    }

    public function __toString(): string
    {
        return ''; //TODO toString method
    }
}
