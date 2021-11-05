<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class VehicleMaintenanceTask.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vehicle\VehicleMaintenanceTaskRepository")
 * @ORM\Table(name="vehicle_manteinance_task")
 * @UniqueEntity({"name"})
 */
class VehicleMaintenanceTask extends AbstractBase
{
    use NameTrait;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $hours = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $km = 0;

    /**
     * Methods.
     */
    public function getHours(): int
    {
        return $this->hours;
    }

    public function setHours(int $hours): VehicleMaintenanceTask
    {
        $this->hours = $hours;

        return $this;
    }

    public function getKm(): int
    {
        return $this->km;
    }

    public function setKm(int $km): VehicleMaintenanceTask
    {
        $this->km = $km;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
